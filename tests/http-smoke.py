#!/usr/bin/env python3
"""Real HTTP acceptance checks against disposable local WordPress + Mailpit. No external traffic."""
import json
import base64
import hashlib
import re
import os
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from html.parser import HTMLParser
from pathlib import Path

BASE = os.environ.get("FP_TEST_URL", "http://localhost:8080").rstrip("/")
if urllib.parse.urlsplit(BASE).hostname not in ("localhost", "127.0.0.1"):
    raise SystemExit("Tests require a localhost WordPress instance.")

class Page(HTMLParser):
    def __init__(self, body):
        super().__init__()
        self.h1 = 0
        self.title = ""
        self.in_title = False
        self.links = []
        self.fields = {}
        self.canonical = []
        self.description = []
        self.robots = []
        self.scripts = []
        self.feed(body)
    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if tag == "h1": self.h1 += 1
        if tag == "title": self.in_title = True
        if tag == "a" and a.get("href"): self.links.append(a["href"])
        if tag == "input" and a.get("name"): self.fields[a["name"]] = a.get("value", "")
        if tag == "link" and a.get("rel") == "canonical": self.canonical.append(a.get("href", ""))
        if tag == "meta" and a.get("name") == "description": self.description.append(a.get("content", ""))
        if tag == "meta" and a.get("name") == "robots": self.robots.append(a.get("content", ""))
        if tag == "script" and a.get("src"): self.scripts.append(a["src"])
    def handle_endtag(self, tag):
        if tag == "title": self.in_title = False
    def handle_data(self, data):
        if self.in_title: self.title += data

checks = 0
def check(value, message):
    global checks
    if not value: raise AssertionError(message)
    checks += 1
    print("PASS:", message)

def fetch(path, data=None):
    req = urllib.request.Request(urllib.parse.urljoin(BASE + "/", path), data=urllib.parse.urlencode(data).encode() if data is not None else None)
    try:
        with urllib.request.urlopen(req, timeout=25) as response:
            return response.status, response.read().decode("utf-8"), dict(response.headers), response.url
    except urllib.error.HTTPError as response:
        return response.code, response.read().decode("utf-8"), dict(response.headers), response.url

paths = ["/", "/prestations/", "/prestations/peinture-interieure/", "/prestations/peinture-exterieure/", "/prestations/revetements-muraux/", "/prestations/revetements-sols/", "/entreprise/", "/zone-intervention/", "/contact/", "/mentions-legales/", "/confidentialite/", "/merci/"]
pages = {}
for path in paths:
    status, body, headers, url = fetch(path)
    check(status == 200, f"{path} returns 200")
    page = Page(body)
    pages[path] = page
    check(page.h1 == 1, f"{path} has exactly one H1")
    check(bool(page.title.strip()), f"{path} has a title")
    check(len(page.description) == 1 and bool(page.description[0]), f"{path} has one nonempty description")
    check(len(page.canonical) == 1 and page.canonical[0] == BASE + path, f"{path} has correct local canonical")
    check(any("noindex" in item for item in page.robots), f"{path} is noindex in local preview")
    check("PHP Fatal" not in body and "Warning:" not in body, f"{path} exposes no PHP diagnostic")
    check(all(urllib.parse.urlsplit(urllib.parse.urljoin(BASE, src)).netloc == urllib.parse.urlsplit(BASE).netloc for src in page.scripts), f"{path} loads no third-party JavaScript")
check(len({p.title for p in pages.values()}) == len(paths), "Page titles are distinct")
check(len({p.description[0] for p in pages.values()}) == len(paths), "Page descriptions are distinct")
status, body, _, _ = fetch("/page-inexistante-recette/")
check(status == 404 and Page(body).h1 == 1, "Useful 404 page returns 404 with H1")
status, body, _, _ = fetch("/merci/?sent=1")
check("data-form-success" not in body, "A manual query string cannot simulate successful submission")
check("Prendre rendez-vous" in fetch("/")[1], "Appointment contact is the primary action")

seen = set(paths)
for page in pages.values():
    for link in page.links:
        url = urllib.parse.urlsplit(urllib.parse.urljoin(BASE + "/", link))
        if url.scheme not in ("http", "https") or url.netloc != urllib.parse.urlsplit(BASE).netloc or url.path.startswith("/wp-"): continue
        if url.path in seen: continue
        seen.add(url.path)
        check(fetch(url.path)[0] == 200, f"Internal link {url.path} resolves")

def payload(fields):
    return dict(fields, fp_name="Recette HTTP", fp_town="Ã‰couen", fp_type="a-preciser", fp_description="Message fictif du parcours de recette HTTP.", fp_phone="", fp_email="http@example.test", fp_period="", fp_website="")

def captcha_proof():
    status, body, _, _ = fetch('/?fp_captcha=1')
    check(status == 200, 'Local CAPTCHA challenge is available')
    challenge = json.loads(body)
    params = challenge['parameters']
    check(params['algorithm'] == 'PBKDF2/SHA-256' and params['cost'] == 1000, 'Expected local CAPTCHA algorithm')
    nonce, salt = bytes.fromhex(params['nonce']), bytes.fromhex(params['salt'])
    prefix = bytes.fromhex(params['keyPrefix'])
    for counter in range(100000):
        key = hashlib.pbkdf2_hmac('sha256', nonce + counter.to_bytes(4, 'big'), salt, params['cost'], params['keyLength'])
        if key.startswith(prefix):
            value = {'challenge': challenge, 'solution': {'counter': counter, 'derivedKey': key.hex()}}
            return base64.b64encode(json.dumps(value).encode()).decode()
    raise AssertionError('Local CAPTCHA challenge could not be solved')
status, body, _, _ = fetch("/contact/")
fields = Page(body).fields
check("fp_nonce" in fields and "fp_token" in fields, "Actual quote page provides server-generated nonce and token")
time.sleep(2.1)
status, body, _, _ = fetch("/contact/", dict(payload(fields), fp_email="", fp_phone=""))
if status != 422 or "form-error-summary" not in body:
    print("Invalid submission diagnostic: HTTP", status, "error summary:", "form-error-summary" in body)
    if os.environ.get("FP_TEST_DEBUG_DIR"):
        Path(os.environ["FP_TEST_DEBUG_DIR"]).joinpath("invalid-quote.html").write_text(body, encoding="utf-8")
check(status == 422 and "form-error-summary" in body, "Invalid actual POST returns a useful server error")
check("Message fictif du parcours de recette HTTP." in body, "Invalid HTTP submission retains description")
fields = Page(body).fields
time.sleep(2.1)
status, body, _, _ = fetch("/contact/", dict(payload(fields), fp_website="spam"))
check(status == 422, "Honeypot actual POST is rejected")
fields = Page(body).fields
time.sleep(2.1)
status, body, _, url = fetch("/contact/", dict(payload(fields), fp_captcha=captcha_proof()))
check(status == 200 and "/merci/?sent=" in url and "data-form-success" in body, "Valid HTTP request redirects to verified thank-you page")
check("Message fictif" not in url and "example.test" not in url, "No submitted personal data in redirect URL")
print(json.dumps({"checks": checks, "status": "passed", "base": BASE}, ensure_ascii=False))
