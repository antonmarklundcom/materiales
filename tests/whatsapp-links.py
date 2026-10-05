#!/usr/bin/env python3
"""Audit every rendered WhatsApp button, without opening WhatsApp or sending messages.

Usage: python tests/whatsapp-links.py [http://127.0.0.1:8087]
With no URL, starts an isolated PHP preview with temporary storage for CI.
"""
import concurrent.futures
import json
import os
import socket
import subprocess
import sys
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request
import xml.etree.ElementTree as ET
from html.parser import HTMLParser
from pathlib import Path


class Links(HTMLParser):
    def __init__(self):
        super().__init__()
        self.buttons, self.canonical, self.h1 = [], '', ''
        self.in_h1 = False

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == 'a':
            href = attrs.get('href', '')
            if attrs.get('data-ev') == 'whatsapp_click' or 'wa.me/' in href:
                self.buttons.append(href)
        if tag == 'link' and attrs.get('rel') == 'canonical':
            self.canonical = attrs.get('href', '')
        if tag == 'h1':
            self.in_h1 = True

    def handle_endtag(self, tag):
        if tag == 'h1':
            self.in_h1 = False

    def handle_data(self, data):
        if self.in_h1:
            self.h1 += data


def fetch(url):
    try:
        with urllib.request.urlopen(url, timeout=20) as response:
            return response.read().decode('utf-8')
    except urllib.error.HTTPError as error:
        if error.code != 404:
            raise
        return error.read().decode('utf-8')


def audit(base):
    locations = [e.text for e in ET.fromstring(fetch(base + '/sitemap.xml')).findall('.//{*}loc')]
    routes = [urllib.parse.urlparse(url).path for url in locations]
    routes += ['/gracias/', '/__review-missing-route__/', '/cotizar/?m=cemento',
               '/cotizar/?lista=1', '/gracias/?m=cemento&k=invalid-private-token']

    def inspect(route):
        page = Links()
        page.feed(fetch(base + route))
        assert page.buttons, f'{route}: no WhatsApp buttons rendered'
        messages = []
        for href in page.buttons:
            parsed = urllib.parse.urlparse(href)
            assert (parsed.scheme, parsed.netloc, parsed.path) == ('https', 'wa.me', '/595992279599'), href
            text = urllib.parse.parse_qs(parsed.query).get('text', [''])[0]
            assert text.startswith('Hola, vengo de Materiales.com.py.'), (route, text)
            expected_url = page.canonical or 'https://materiales.com.py/'
            assert '\nPágina: ' in text and '\nEnlace: ' + expected_url in text, (route, text)
            assert 'invalid-private-token' not in text and '?k=' not in text, 'private query leaked'
            if route.startswith('/materiales/') and route != '/materiales/':
                assert page.h1.strip() in text, (route, 'material/category label missing')
            if ((route.startswith('/calculadoras/') and route != '/calculadoras/')
                or (route.startswith('/guias/') and route != '/guias/')):
                assert page.h1.strip() in text, (route, 'calculator/guide label missing')
            if route.startswith('/proveedores/'):
                assert 'sumarme como proveedor' in text and 'Quiero cotizar' not in text, (route, text)
            if '?m=cemento' in route:
                assert 'cemento' in text.lower(), (route, 'selected material missing')
            messages.append(text)
        return dict(route=route, buttons=len(page.buttons), example=messages[0])

    with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:
        results = list(pool.map(inspect, routes))
    print(f'WHATSAPP PASS: {len(results)} routes, {sum(p["buttons"] for p in results)} buttons; '
          'confirmed number, brand, public page URL, material/service context; no messages sent')
    return results


if __name__ == '__main__':
    process = None
    with tempfile.TemporaryDirectory(prefix='materiales-whatsapp-test-') as temp:
        try:
            if len(sys.argv) > 1:
                base = sys.argv[1].rstrip('/')
            else:
                with socket.socket() as listener:
                    listener.bind(('127.0.0.1', 0))
                    port = listener.getsockname()[1]
                base = f'http://127.0.0.1:{port}'
                process = subprocess.Popen([os.environ.get('PHP_BINARY', 'php'), '-S',
                                            f'127.0.0.1:{port}', '-t', '.', 'tools/router-cli.php'],
                                           env=dict(os.environ, MATERIALES_STORAGE_DIR=temp),
                                           stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
                for _ in range(100):
                    try:
                        fetch(base + '/robots.txt')
                        break
                    except urllib.error.URLError:
                        if process.poll() is not None:
                            raise RuntimeError('isolated PHP preview failed to start')
                        time.sleep(.05)
                else:
                    raise RuntimeError('isolated PHP preview did not become ready')
            results = audit(base)
            if len(sys.argv) > 2:
                Path(sys.argv[2]).write_text(json.dumps(results, ensure_ascii=False, indent=2), encoding='utf-8')
        finally:
            if process is not None:
                process.terminate()
                process.wait(timeout=5)
