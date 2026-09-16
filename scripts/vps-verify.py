"""Read-only HTTP checks of the private deployment, including linked assets."""
import base64
import pathlib
import urllib.request
import urllib.error
from html.parser import HTMLParser
from urllib.parse import urljoin, urlparse

base = 'https://feret-peinture.fr'
credentials = pathlib.Path('/srv/apps/feret-peinture/private/curl.conf').read_text().split('"')[1]
auth = 'Basic ' + base64.b64encode(credentials.encode()).decode()

def fetch(url, authenticated=False):
    headers = {'Authorization': auth} if authenticated else {}
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
    status, headers, body, url = fetch(base + path)
    assert status == 401, (path, 'anonymous', status)
    assert 'noindex' in headers.get('X-Robots-Tag', ''), path
    print(f'Anonymous {path}: blocked (401)')
    if path in ['/robots.txt', '/wp-sitemap.xml', '/wp-json/']:
        continue
    status, headers, body, url = fetch(base + path, True)
    assert status == 200, (path, 'authenticated', status)
    if path == '/devis/':
        assert url == base + '/contact/', ('legacy contact redirect', url)
    assert b'http://localhost' not in body, path
    assert 'noindex' in headers.get('X-Robots-Tag', ''), path
    assets.feed(body.decode('utf-8'))
    print(f'Authenticated {path}: OK (200)')
for url in sorted(assets.urls):
    if '/wp-content/' not in url and '/wp-includes/' not in url:
        continue
    assert fetch(url)[0] == 401, ('public asset', url)
    assert fetch(url, True)[0] == 200, ('missing asset', url)
print('Linked images, styles, fonts/scripts: authenticated access only.')
assert fetch(base + '/?fp_captcha=1')[0] == 401
status, headers, body, url = fetch(base + '/?fp_captcha=1', True)
assert status == 200 and 'application/json' in headers.get('Content-Type', ''), 'CAPTCHA endpoint'
for origin in ['http://feret-peinture.fr', 'http://www.feret-peinture.fr', 'https://www.feret-peinture.fr']:
    status, headers, body, url = fetch(origin + '/')
    assert status == 401 and url == base + '/', (origin, status, url)
    print(f'{origin}: redirects to protected HTTPS canonical domain')
print('All private deployment checks passed.')
