"""Read-only HTTP checks of the private or public deployment, including assets."""
import base64
import os
import pathlib
import urllib.request
import urllib.error
from html.parser import HTMLParser
from urllib.parse import urljoin, urlparse

base = 'https://feret-peinture.fr'
mode = os.environ.get('FP_VPS_MODE', 'public')
if mode not in {'private', 'public'}:
    raise SystemExit('FP_VPS_MODE must be private or public.')
auth = None
if mode == 'private':
    credentials = pathlib.Path('/srv/apps/feret-peinture/private/curl.conf').read_text().split('"')[1]
    auth = 'Basic ' + base64.b64encode(credentials.encode()).decode()

def fetch(url, authenticated=False):
    headers = {'Authorization': auth} if authenticated and auth else {}
    try:
        response = urllib.request.urlopen(urllib.request.Request(url, headers=headers), timeout=30)
    except urllib.error.HTTPError as error:
        response = error
    return response.status, response.headers, response.read(), response.url

class Links(HTMLParser):
    def __init__(self):
        super().__init__()
        self.urls = set()
    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        for key in ('href', 'src'):
            value = attrs.get(key, '')
            resolved = urljoin(base + '/', value)
            if value and urlparse(resolved).netloc == 'feret-peinture.fr':
                self.urls.add(resolved.split('#')[0])

pages = ['/', '/contact/', '/devis/', '/entreprise/', '/prestations/', '/realisations/',
         '/zone-intervention/', '/wp-login.php', '/wp-json/', '/robots.txt', '/wp-sitemap.xml']
assets = Links()
for path in pages:
    if mode == 'private':
        status, headers, body, url = fetch(base + path)
        assert status == 401, (path, 'anonymous', status)
        assert 'noindex' in headers.get('X-Robots-Tag', ''), path
        print(f'Anonymous {path}: blocked (401)')
    status, headers, body, url = fetch(base + path, mode == 'private')
    # The public REST API is intentionally unavailable and the projects archive
    # is intentionally absent until a real, authorised project exists.
    if mode == 'public' and path == '/wp-json/':
        assert status == 401, (path, 'public REST restriction', status)
        print('Public REST API: intentionally restricted (401)')
        continue
    if mode == 'public' and path == '/realisations/' and status == 404:
        print('Projects archive: intentionally absent without published projects (404)')
        continue
    assert status == 200, (path, 'authenticated', status)
    if path == '/devis/':
        assert url == base + '/contact/', ('legacy contact redirect', url)
    assert b'http://localhost' not in body, path
    if mode == 'private':
        assert 'noindex' in headers.get('X-Robots-Tag', ''), path
    else:
        assert 'noindex' not in headers.get('X-Robots-Tag', ''), path
        assert headers.get('X-Content-Type-Options') == 'nosniff', path
        assert "frame-ancestors 'self'" in headers.get('Content-Security-Policy', ''), path
    assets.feed(body.decode('utf-8'))
    print(f'Authenticated {path}: OK (200)')
for url in sorted(assets.urls):
    if '/wp-content/' not in url and '/wp-includes/' not in url:
        continue
    status, headers, body, _ = fetch(url, mode == 'private')
    assert status == 200, ('missing asset', url)
    if mode == 'private':
        assert fetch(url)[0] == 401, ('public asset', url)
    elif '/wp-content/themes/feret-peinture/' in url or '/wp-content/plugins/feret-peinture-core/' in url:
        assert 'immutable' in headers.get('Cache-Control', ''), ('asset cache policy', url)
print('Linked images, styles, fonts and scripts are reachable with the expected policy.')
if mode == 'private':
    assert fetch(base + '/?fp_captcha=1')[0] == 401
status, headers, body, url = fetch(base + '/?fp_captcha=1', mode == 'private')
assert status == 200 and 'application/json' in headers.get('Content-Type', ''), 'CAPTCHA endpoint'
for origin in ['http://feret-peinture.fr', 'http://www.feret-peinture.fr', 'https://www.feret-peinture.fr']:
    status, headers, body, url = fetch(origin + '/')
    if mode == 'private':
        assert status == 401 and url == base + '/', (origin, status, url)
    else:
        assert status == 200 and url == base + '/', (origin, status, url)
    print(f'{origin}: redirects to the expected HTTPS canonical domain')
print(f'All {mode} deployment checks passed.')
