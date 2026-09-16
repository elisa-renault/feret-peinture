#!/usr/bin/env python3
"""Native WordPress HTTP editor checks, restricted to a disposable loopback site.

Export CHRISTOPHE_PASSWORD or pass --env-file .env. No credential/cookie is logged.
No browser rendering is claimed: this tests native HTTP login/forms/capabilities.
"""
import argparse
import http.cookiejar
import json
import os
import re
import struct
import sys
import urllib.error
import urllib.parse
import urllib.request
import uuid
import zlib
from html.parser import HTMLParser


class EditorHTML(HTMLParser):
    """Read native inputs and Pods' server-rendered JSON field state, without JS."""
    def __init__(self, source):
        super().__init__(convert_charrefs=True)
        self.values = {}
        self.links = []
        self.action = "post.php"
        self.in_post = False
        self.textarea = None
        self.select = None
        self.option = None
        self.pods_json = None
        self.feed(source)

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == "a" and "href" in attrs:
            self.links.append(attrs["href"])
        if tag == "form":
            self.in_post = attrs.get("id") == "post"
            if self.in_post:
                self.action = attrs.get("action", "post.php")
        if not self.in_post:
            return
        if tag == "script" and "pods-dfv-field-data" in attrs.get("class", ""):
            self.pods_json = ""
        name = attrs.get("name")
        if tag == "input" and name and "disabled" not in attrs:
            kind = attrs.get("type", "text")
            if kind in ("button", "submit", "reset", "file"):
                return
            if kind in ("checkbox", "radio") and "checked" not in attrs:
                return
            self.values[name] = attrs.get("value", "")
        elif tag == "textarea" and name:
            self.textarea = name
            self.values[name] = ""
        elif tag == "select" and name:
            self.select = name
            self.values.setdefault(name, "")
        elif tag == "option" and self.select:
            value = attrs.get("value", "")
            if "selected" in attrs:
                self.values[self.select] = value
            elif not self.values[self.select] and value.isdigit() and int(value) > 0:
                self.values[self.select] = value

    def handle_endtag(self, tag):
        if tag == "script" and self.pods_json is not None:
            data = json.loads(self.pods_json)
            name = data.get("htmlAttr", {}).get("name")
            if name and name.startswith("pods_meta_"):
                value = data.get("fieldValue")
                if isinstance(value, list):
                    ids = [str(item.get("ID", item.get("id", ""))) if isinstance(item, dict) else str(item) for item in value]
                    self.values[name] = ",".join(value for value in ids if value)
                elif isinstance(value, dict):
                    self.values[name] = str(value.get("ID", value.get("id", "")))
                else:
                    self.values[name] = "" if value is None else str(int(value) if isinstance(value, bool) else value)
            self.pods_json = None
        if tag == "form":
            self.in_post = False
        elif tag == "textarea":
            self.textarea = None
        elif tag == "select":
            self.select = None

    def handle_data(self, data):
        if self.pods_json is not None:
            self.pods_json += data
        if self.textarea:
            self.values[self.textarea] += data


def private_env(path):
    values = {}
    if path:
        with open(path, encoding="utf-8") as source:
            for line in source:
                line = line.strip()
                if not line or line.startswith("#") or "=" not in line:
                    continue
                key, value = line.split("=", 1)
                values[key] = value.strip("\"'")
    return values


def png_swatch():
    def chunk(kind, data):
        return struct.pack("!I", len(data)) + kind + data + struct.pack("!I", zlib.crc32(kind + data))
    rows = b"".join(b"\0" + bytes((35, 66, 62)) * 24 for _ in range(24))
    return b"\x89PNG\r\n\x1a\n" + chunk(b"IHDR", struct.pack("!2I5B", 24, 24, 8, 2, 0, 0, 0)) + chunk(b"IDAT", zlib.compress(rows)) + chunk(b"IEND", b"")


def main():
    cli = argparse.ArgumentParser(description=__doc__)
    cli.add_argument("--base-url", default=os.getenv("WP_BASE_URL", "http://localhost:8080"))
    cli.add_argument("--env-file")
    args = cli.parse_args()
    base = args.base_url.rstrip("/")
    if urllib.parse.urlparse(base).hostname not in ("localhost", "127.0.0.1", "::1"):
        raise SystemExit("This fixture-writing test is restricted to a disposable loopback site.")
    config = private_env(args.env_file)
    password = os.getenv("CHRISTOPHE_PASSWORD") or config.get("CHRISTOPHE_PASSWORD")
    if not password:
        raise SystemExit("Provide CHRISTOPHE_PASSWORD privately or --env-file; it will not be logged.")
    jar = http.cookiejar.CookieJar()
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
    failures = []
    count = 0
    passed = 0
    project_id = None
    attachment = None
    information_restore = None

    def check(ok, label):
        nonlocal count, passed
        count += 1
        passed += int(bool(ok))
        print(("PASS " if ok else "FAIL ") + label)
        if not ok:
            failures.append(label)

    def request(path, fields=None, content=None, headers=None):
        target = urllib.parse.urljoin(base + "/", path)
        body = urllib.parse.urlencode(fields).encode() if fields is not None else content
        req = urllib.request.Request(target, data=body, headers=headers or {})
        try:
            with opener.open(req, timeout=30) as response:
                return response.status, response.read().decode("utf-8", "replace"), response.url
        except urllib.error.HTTPError as response:
            return response.code, response.read().decode("utf-8", "replace"), response.url

    def save_editor(source, changes):
        form = EditorHTML(source)
        if not form.values.get("_wpnonce") or not form.values.get("post_ID"):
            raise RuntimeError("Native editor nonce or post ID absent; no write attempted.")
        form.values.update(changes)
        return request("/wp-admin/" + form.action.lstrip("/"), fields=form.values)

    def action_link(source, action, ident):
        for link in EditorHTML(source).links:
            query = urllib.parse.parse_qs(urllib.parse.urlparse(link).query)
            if query.get("action") == [action] and query.get("post") == [str(ident)]:
                return link
        return None

    try:
        request("/wp-login.php")
        status, source, url = request("/wp-login.php", fields={"log": "christophe", "pwd": password, "wp-submit": "Se connecter", "redirect_to": base + "/wp-admin/", "testcookie": "1"})
        logged_in = any(cookie.name.startswith("wordpress_logged_in_") for cookie in jar)
        login_ok = status == 200 and logged_in and urllib.parse.urlparse(url).path.startswith("/wp-admin/")
        check(login_ok, "Christophe authenticates through native wp-login and receives a session cookie")
        if not login_ok:
            raise RuntimeError("Native login failed; stopping authenticated write checks.")
        for path in ("options-general.php", "plugins.php", "users.php", "themes.php", "edit.php", "edit.php?post_type=page", "post-new.php?post_type=fp_service", "post-new.php?post_type=fp_information", "admin.php?page=pods"):
            status, _, _ = request("/wp-admin/" + path)
            check(status == 403, "Direct restricted admin URL returns 403: " + path)
        for post_type in ("fp_project", "fp_service", "fp_information"):
            status, page, _ = request("/wp-admin/edit.php?post_type=" + post_type)
            check(status == 200 and 'id="adminmenumain"' in page, "Native métier screen is accessible: " + post_type)
            if status == 403:
                error = re.search(r'<div class="wp-die-message">(.*?)</div>', page, re.S)
                if error:
                    print("HTTP DENIAL " + re.sub(r"<[^>]*>", "", error.group(1)).strip()[:350])
            if post_type == "fp_information" and status == 200:
                info_values = EditorHTML(page).values
                information_restore = (info_values.get("post_ID"), info_values.get("pods_meta_phone_mobile", ""))
                status, edited_info, _ = save_editor(page, {"pods_meta_phone_mobile": "06 00 00 00 02", "save": "Mettre à jour"})
                check(status == 200 and EditorHTML(edited_info).values.get("pods_meta_phone_mobile") == "06 00 00 00 02", "Christophe edits the shared phone through the native information form")
                _, public_home, _ = request("/")
                check("tel:+33600000002" in public_home, "Native contact edit propagates to the public phone link")

        status, source, _ = request("/wp-admin/post-new.php?post_type=fp_project")
        form = EditorHTML(source)
        project_id = form.values.get("post_ID")
        check(status == 200 and bool(project_id), "Native Add project page creates an editable draft with a nonce")
        for name in ("pods_meta_town", "pods_meta_short_description", "pods_meta_publication_authorized"):
            check(name in source, "Real Pods editor field is present: " + name)
        if not project_id:
            raise RuntimeError("No native project editor was returned.")
        if os.getenv("FP_QA_DIAGNOSTIC"):
            print("FORM_SHAPE " + json.dumps({"pods_names": [key for key in form.values if "pods" in key], "action": form.values.get("action"), "post_type": form.values.get("post_type")}))

        # Upload a generated raster through WordPress' authenticated media handler.
        match = re.search(r"var _wpPluploadSettings\s*=\s*(\{[^\n]+\});", source)
        media_nonce = None
        if match:
            media_nonce = json.loads(match.group(1)).get("defaults", {}).get("multipart_params", {}).get("_wpnonce")
        check(bool(media_nonce), "Native media uploader exposes its scoped upload nonce")
        if media_nonce:
            boundary = "FPQA" + uuid.uuid4().hex
            parts = []
            for name, value in {"action": "upload-attachment", "_wpnonce": media_nonce, "post_id": project_id}.items():
                parts.append((f'--{boundary}\r\nContent-Disposition: form-data; name="{name}"\r\n\r\n{value}\r\n').encode())
            parts.append((f'--{boundary}\r\nContent-Disposition: form-data; name="async-upload"; filename="qa-http-swatch.png"\r\nContent-Type: image/png\r\n\r\n').encode() + png_swatch() + b"\r\n")
            parts.append((f"--{boundary}--\r\n").encode())
            upload_status, upload_body, _ = request("/wp-admin/async-upload.php", content=b"".join(parts), headers={"Content-Type": "multipart/form-data; boundary=" + boundary})
            result = json.loads(upload_body)
            if result.get("success"):
                attachment = result["data"]
            check(upload_status == 200 and bool(attachment and attachment.get("id")), "Christophe uploads a real PNG through native authenticated media processing")

        changes = {"post_title": "QA HTTP — chantier temporaire", "content": "Travaux de vérification locale uniquement.", "post_status": "draft", "save": "Enregistrer le brouillon", "pods_meta_town": "QA Écouen", "pods_meta_short_description": "Description HTTP A", "pods_meta_publication_authorized": "0"}
        if attachment:
            changes["_thumbnail_id"] = str(attachment["id"])
            changes["pods_meta_gallery[]"] = str(attachment["id"])
        status, source, _ = save_editor(source, changes)
        saved = EditorHTML(source).values
        if os.getenv("FP_QA_DIAGNOSTIC"):
            print("SAVE_DRAFT " + json.dumps({"http_status": status, "status": saved.get("original_post_status"), "town": saved.get("pods_meta_town"), "description": saved.get("pods_meta_short_description"), "image": saved.get("_thumbnail_id")}, ensure_ascii=False))
        check(status == 200 and saved.get("post_title") == changes["post_title"] and saved.get("original_post_status") == "draft", "Native editor saves a draft project")
        check(saved.get("pods_meta_town") == "QA Écouen" and saved.get("pods_meta_short_description") == "Description HTTP A", "Native form persists its real Pods text fields")

        status, source, _ = save_editor(source, {"post_title": "QA HTTP — chantier publié", "pods_meta_short_description": "Description HTTP B", "pods_meta_publication_authorized": "1", "post_status": "publish", "publish": "Publier"})
        saved = EditorHTML(source).values
        if os.getenv("FP_QA_DIAGNOSTIC"):
            print("SAVE_PUBLISH " + json.dumps({"http_status": status, "status": saved.get("original_post_status"), "town": saved.get("pods_meta_town"), "description": saved.get("pods_meta_short_description"), "image": saved.get("_thumbnail_id")}, ensure_ascii=False))
        check(status == 200 and saved.get("original_post_status") == "publish", "Christophe publishes his project through the native form")
        check(saved.get("pods_meta_short_description") == "Description HTTP B", "Native edit updates a published project's Pods description")
        if attachment:
            check(saved.get("_thumbnail_id") == str(attachment["id"]), "Native editor saves the uploaded featured image")

        trash = action_link(source, "trash", project_id)
        check(bool(trash), "Native editor provides an authorized Trash action")
        if trash:
            status, source, _ = request(trash)
            status, source, _ = request("/wp-admin/edit.php?post_type=fp_project&post_status=trash")
            restore = action_link(source, "untrash", project_id)
            check(status == 200 and bool(restore), "Trashed project appears with a native Restore action")
            if restore:
                request(restore)
                status, source, _ = request(f"/wp-admin/post.php?post={project_id}&action=edit")
                check(status == 200 and EditorHTML(source).values.get("pods_meta_short_description") == "Description HTTP B", "Native restore recovers the project and its Pods fields")
    except Exception as error:
        # Do not output server responses, request fields, cookies or credentials.
        failures.append(type(error).__name__ + ": " + str(error))
        print("FAIL " + failures[-1])
    finally:
        if information_restore and information_restore[0]:
            try:
                _, info_source, _ = request(f"/wp-admin/post.php?post={information_restore[0]}&action=edit")
                status, restored_info, _ = save_editor(info_source, {"pods_meta_phone_mobile": information_restore[1], "save": "Mettre à jour"})
                check(status == 200 and EditorHTML(restored_info).values.get("pods_meta_phone_mobile") == information_restore[1], "Original shared phone is restored through the native form")
            except Exception:
                check(False, "Original information phone restoration requires inspection")
        if project_id:
            try:
                _, source, _ = request(f"/wp-admin/post.php?post={project_id}&action=edit")
                trash = action_link(source, "trash", project_id)
                if trash:
                    request(trash)
                _, source, _ = request("/wp-admin/edit.php?post_type=fp_project&post_status=trash")
                delete = action_link(source, "delete", project_id)
                if delete:
                    request(delete)
                check(bool(delete), "Temporary project is removed through the native Trash UI")
            except Exception:
                check(False, "Temporary project cleanup requires inspection")
        if attachment:
            nonce = attachment.get("nonces", {}).get("delete")
            status, body, _ = request("/wp-admin/admin-ajax.php", fields={"action": "delete-post", "id": attachment["id"], "_ajax_nonce": nonce or ""})
            check(status == 200 and body.strip() == "1", "Temporary uploaded media is removed through its authorized native action")
    print(f"{passed}/{count} native HTTP admin checks passed; {len(failures)} failures.")
    return 1 if failures else 0


if __name__ == "__main__":
    sys.exit(main())
