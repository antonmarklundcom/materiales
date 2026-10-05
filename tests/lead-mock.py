#!/usr/bin/env python3
"""Exercise existing buyer/supplier -> CRM adapter with an in-memory loopback receiver.
Never uses production config, never contacts a supplier, and never writes lead data.
Usage: PHP_BINARY=/path/to/php python tests/lead-mock.py
"""
import json
import os
import subprocess
import tempfile
import threading
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path

received = []
statuses = [201, 200, 422, 500]


class Receiver(BaseHTTPRequestHandler):
    def log_message(self, *_):
        pass

    def do_POST(self):
        assert self.path == '/api/v1/leads'
        payload = json.loads(self.rfile.read(int(self.headers['Content-Length'])))
        received.append(payload)
        code = statuses[len(received) - 1]
        self.send_response(code)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps({'duplicate': code == 200, 'test': True}).encode())


php_source = '''<?php
require $argv[1] . '/partials/init.php';
require PUBLIC_ROOT . '/partials/lead.php';
$config = ['url' => $argv[2], 'api_key' => 'loopback-fixture-only', 'timeout' => 2];
$resolved = lead_resolve_slug('cemento');
$buyer = lead_build_payload($resolved + [
    'phone_raw' => '0990 000 000', 'nombre' => 'Prueba local',
    'cantidad' => '16 bolsas de 50 kg', 'ciudad' => 'Zona de prueba',
    'mensaje' => "16 bolsas de cemento\\n1,1 m³ de arena", 'page_url' => url('/cotizar/'),
    'idempotency_key' => 'synthetic-buyer', 'consent_at' => '2026-10-05T12:00:00Z',
]);
$supplier = lead_build_supplier_payload([
    'phone_raw' => '0990 000 001', 'empresa' => 'Fixture local',
    'rubros' => ['hierro', 'aridos'], 'nombre' => 'Prueba local', 'ciudad' => 'Zona de prueba',
    'idempotency_key' => 'synthetic-supplier', 'consent_at' => '2026-10-05T12:00:00Z',
]);
$outcomes = [];
foreach ([$buyer, $buyer, $supplier, $supplier] as $payload) {
    $result = lead_send($payload, $config);
    $outcomes[] = ['status' => $result['status'], 'ok' => lead_send_ok($result)];
}
echo json_encode($outcomes);
'''

server = ThreadingHTTPServer(('127.0.0.1', 0), Receiver)
thread = threading.Thread(target=server.serve_forever, daemon=True)
thread.start()
try:
    with tempfile.TemporaryDirectory(prefix='materiales-mock-') as temp:
        script = Path(temp) / 'adapter.php'
        script.write_text(php_source, encoding='utf-8')
        result = subprocess.run([os.environ.get('PHP_BINARY', 'php'), str(script), str(Path.cwd()),
                                 f'http://127.0.0.1:{server.server_port}'],
                                text=True, capture_output=True, timeout=15, check=True)
        assert result.stderr == '', result.stderr
        outcomes = json.loads(result.stdout)
    assert outcomes == [{'status': 201, 'ok': True}, {'status': 200, 'ok': True},
                        {'status': 422, 'ok': False}, {'status': 500, 'ok': False}], outcomes
    assert len(received) == 4
    buyer, repeated, supplier, failed = received
    assert buyer == repeated, 'same payload/idempotency for repeat'
    assert buyer['source'] == supplier['source'] == 'site:materiales'
    assert 'tipo' not in buyer['fields']
    assert buyer['fields']['material'] == 'cemento'
    assert buyer['fields']['cantidad'] == '16 bolsas de 50 kg'
    assert '\n' in buyer['message'], 'list newlines preserved'
    assert buyer['fields']['consent'].startswith('proveedores-v1 @ ')
    assert supplier['fields']['tipo'] == 'proveedor'
    assert supplier['fields']['rubros'] == 'hierro,aridos'
    assert supplier['fields']['consent'].startswith('proveedor-v1 @ ')
    assert not any(key in buyer for key in ('pipeline', 'stage', 'owner', 'tag'))
    print('LEAD MOCK PASS: buyer/list, supplier, identical retry; 201/200 success, 422/500 failure; loopback only')
finally:
    server.shutdown()
    server.server_close()
