#!/usr/bin/env python3
"""GET-only audit of snapshot links/assets, fragments, redirects and private-path protection."""
import concurrent.futures
import importlib.util
import json
import sys
import urllib.parse
import urllib.request
from html.parser import HTMLParser
from pathlib import Path

loader = importlib.util.spec_from_file_location('seo', Path(__file__).with_name('seo-regression.py'))
seo = importlib.util.module_from_spec(loader)
loader.loader.exec_module(seo)


class Resources(HTMLParser):
    def __init__(self):
        super().__init__()
        self.urls, self.ids = set(), set()

    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if a.get('id'):
            self.ids.add(a['id'])
        for key in ('src', 'srcset'):
            if a.get(key):
                for value in a[key].split(',') if key == 'srcset' else [a[key]]:
                    self.urls.add(value.strip().split()[0])
        if tag == 'link' and a.get('href'):
            self.urls.add(a['href'])


base, snapshot_path, output = sys.argv[1:]
base = base.rstrip('/')
snapshot = json.loads(Path(snapshot_path).read_text(encoding='utf-8'))
host = urllib.parse.urlparse(base).hostname
hosts = {host, 'materiales.com.py', 'www.materiales.com.py'}
targets, fragments = set(), set()
failures = []


def add(url, page):
    parsed = urllib.parse.urlparse(urllib.parse.urljoin(base + page, url))
    if parsed.scheme not in ('http', 'https') or parsed.hostname not in hosts:
        return
    path = parsed.path or '/'
    path += ('?' + parsed.query) if parsed.query else ''
    targets.add(path)
    if parsed.fragment:
        fragments.add((path, urllib.parse.unquote(parsed.fragment)))


def resources(path):
    code, _, html = seo.get(base + path)
    parser = Resources()
    parser.feed(html)
    return path, parser


with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:
    parsed_pages = dict(pool.map(resources, snapshot['pages']))
for path, page in snapshot['pages'].items():
    for link in page['links']:
        add(link, path)
    for resource in parsed_pages[path].urls:
        add(resource, path)


def inspect(path):
    code, final, body = seo.get(base + path)
    return path, code, final, body


with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:
    responses = list(pool.map(inspect, sorted(targets)))
for path, code, _, body in responses:
    expected = 404 if path == '/__review-missing-route__/' else 200
    if code != expected:
        failures.append(f'public {path}: {code}')
    if path not in parsed_pages and '<html' in body.lower():
        parser = Resources()
        parser.feed(body)
        parsed_pages[path] = parser
for path, fragment in fragments:
    if path not in parsed_pages or fragment not in parsed_pages[path].ids:
        failures.append(f'missing anchor {path}#{fragment}')

private = ['/data/site.php', '/content/home/intro.php', '/config/vendercrm.php',
           '/storage/leads.log', '/tools/smoke.php', '/tests/lead-mock.py',
           '/partials/init.php', '/materiales/_index.php', '/docs/review-2026-10-05/seo-live.json',
           '/.git/config', '/DEPLOY.md', '/config.sample.php', '/backup.sql', '/site.bak']
private_status = {}
for path in private:
    code, _, _ = seo.get(base + path)
    private_status[path] = code
    if code != 403:
        failures.append(f'private {path}: {code}, expected 403')

class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *_):
        return None

opener = urllib.request.build_opener(NoRedirect)
redirects = []
for path in ['/materiales/cemento', '/guias/como-elegir-un-corralon', '/calculadoras/hormigon-por-m3']:
    try:
        response = opener.open(base + path)
        code, location = response.status, response.headers.get('Location', '')
    except seo.urllib.error.HTTPError as error:
        code, location = error.code, error.headers.get('Location', '')
    redirects.append(dict(path=path, status=code, location=location))
    if code != 301 or location != path + '/':
        failures.append(f'redirect {path}: {code} -> {location}')

result = dict(base=base, public_targets=len(targets), fragments=len(fragments),
              private=private_status, redirects=redirects, failures=failures)
Path(output).write_text(json.dumps(result, ensure_ascii=False, indent=2), encoding='utf-8')
print(f'PUBLIC AUDIT {"FAIL" if failures else "PASS"}: {len(targets)} targets, {len(fragments)} anchors, '
      f'{len(private)} blocked paths, {len(redirects)} redirects; {len(failures)} failures')
for failure in failures:
    print(failure)
sys.exit(bool(failures))
