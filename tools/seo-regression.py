#!/usr/bin/env python3
"""Read-only SEO baseline/check: sitemap URLs, head metadata, JSON-LD, H1 and links.

Usage: python tools/seo-regression.py snapshot BASE OUTPUT
       python tools/seo-regression.py compare BEFORE AFTER
Only GET requests. No form submission, credentials or visitor data are captured.
"""
import concurrent.futures
import json
import sys
import urllib.error
import urllib.parse
import urllib.request
import xml.etree.ElementTree as ET
from html.parser import HTMLParser
from pathlib import Path


class Page(HTMLParser):
    def __init__(self):
        super().__init__()
        self.meta, self.links, self.canonical, self.schemas = {}, set(), [], []
        self.title, self.h1, self.capture, self.buf = [], [], None, []

    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if tag == 'meta' and ('name' in a or 'property' in a):
            key = a.get('name', a.get('property'))
            self.meta.setdefault(key, []).append(a.get('content', ''))
        if tag == 'link' and a.get('rel') == 'canonical':
            self.canonical.append(a.get('href', ''))
        if tag == 'a' and a.get('href'):
            self.links.add(a['href'])
        if tag in ('title', 'h1') or (tag == 'script' and a.get('type') == 'application/ld+json'):
            self.capture, self.buf = tag, []

    def handle_data(self, data):
        if self.capture:
            self.buf.append(data)

    def handle_endtag(self, tag):
        if tag != self.capture:
            return
        value = ''.join(self.buf).strip()
        if tag == 'script':
            self.schemas.append(json.loads(value))
        else:
            getattr(self, tag).append(' '.join(value.split()))
        self.capture, self.buf = None, []


def get(url):
    request = urllib.request.Request(url, headers={'User-Agent': 'MaterialesReadOnlyReview/1.0'})
    try:
        with urllib.request.urlopen(request, timeout=25) as response:
            return response.status, response.geturl(), response.read().decode('utf-8', errors='replace')
    except urllib.error.HTTPError as error:
        return error.code, error.geturl(), error.read().decode('utf-8', errors='replace')


def snapshot(base, output):
    base = base.rstrip('/')
    status, _, sitemap = get(base + '/sitemap.xml')
    assert status == 200, f'sitemap status: {status}'
    root = ET.fromstring(sitemap)
    locations = [e.text for e in root.findall('.//{*}loc')]
    paths = [urllib.parse.urlparse(url).path for url in locations]
    # Conversion/404 are intentionally outside sitemap but must keep their SEO contracts.
    paths = sorted(set(paths + ['/gracias/', '/__review-missing-route__/']))

    def read(path):
        code, final, html = get(base + path)
        page = Page()
        page.feed(html)
        assert page.h1 and page.title, f'{path}: missing title/H1 (status {code})'
        return path, dict(status=code, final_path=urllib.parse.urlparse(final).path,
                          title=page.title, h1=page.h1, meta=page.meta,
                          canonical=page.canonical, schema=page.schemas,
                          links=sorted(page.links))

    with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:
        pages = dict(pool.map(read, paths))
    robots_status, _, robots = get(base + '/robots.txt')
    result = dict(base=base, sitemap=sitemap, urls=locations, robots=robots,
                  robots_status=robots_status, pages=pages)
    Path(output).write_text(json.dumps(result, ensure_ascii=False, indent=2), encoding='utf-8')
    print(f'SNAPSHOT OK: {len(locations)} sitemap URLs, {len(pages)} pages -> {output}')


def compare(before, after):
    old, new = [json.loads(Path(p).read_text(encoding='utf-8')) for p in (before, after)]
    failures, added = [], 0
    for key in ('sitemap', 'urls', 'robots', 'robots_status'):
        # Git on Windows uses CRLF for static robots; directives/content must stay identical.
        before_value, after_value = old[key], new[key]
        if key == 'robots':
            before_value, after_value = before_value.replace('\r\n', '\n'), after_value.replace('\r\n', '\n')
        if before_value != after_value:
            failures.append(f'{key} changed')
    for path, page in old['pages'].items():
        if path not in new['pages']:
            failures.append(f'{path}: missing page')
            continue
        current = new['pages'][path]
        for key in ('status', 'final_path', 'title', 'h1', 'meta', 'canonical', 'schema'):
            if page[key] != current[key]:
                failures.append(f'{path}: {key} changed')
        missing = set(page['links']) - set(current['links'])
        added += len(set(current['links']) - set(page['links']))
        if missing:
            failures.append(f'{path}: removed links: {sorted(missing)}')
    if failures:
        print('\n'.join(failures))
        print(f'SEO FAIL: {len(failures)} differences')
        return 1
    print(f'SEO PASS: {len(old["urls"])} sitemap URLs; {len(old["pages"])} pages; '
          f'all metadata, H1, canonical, JSON-LD and existing links preserved; {added} new links')
    return 0


if __name__ == '__main__':
    if len(sys.argv) != 4 or sys.argv[1] not in ('snapshot', 'compare'):
        sys.exit(__doc__)
    if sys.argv[1] == 'snapshot':
        snapshot(sys.argv[2], sys.argv[3])
    else:
        sys.exit(compare(sys.argv[2], sys.argv[3]))
