#!/usr/bin/env node
/**
 * tests/mobile-overflow.mjs — chequeo local de desborde horizontal (plan §11.6).
 *
 * NO corre en CI (costo de runner-minutes, plan §10 Backlog): es una herramienta de QA local
 * para correr a mano antes de mergear un cambio de layout. Levanta el servidor embebido de
 * PHP con tools/router-cli.php (el mismo que usa tools/render-check.sh), visita un set de
 * rutas representativas en varios anchos con un viewport REAL de Playwright — nunca
 * `chromium --screenshot`, que arma la ventana con --window-size y mide un DOM más ancho de
 * lo que en realidad se ve (KNOWN-ISSUES #23) — y falla si `scrollWidth !== clientWidth` en
 * cualquier combinación de ruta y ancho.
 *
 * Requiere Playwright con Chromium ya instalado (PLAYWRIGHT_BROWSERS_PATH, como en el resto
 * del repo) — si el `require`/`import` de 'playwright' falla, instalalo (`npm i -D
 * playwright`) o corré con `NODE_PATH=$(npm root -g) node tests/mobile-overflow.mjs` si ya
 * está instalado globalmente.
 *
 * Uso: node tests/mobile-overflow.mjs [--port=8134]
 */

import { spawn } from 'node:child_process';
import { mkdir } from 'node:fs/promises';
import { createRequire } from 'node:module';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const require = createRequire(import.meta.url);
const ROOT = path.dirname(path.dirname(fileURLToPath(import.meta.url)));
const PORT = (process.argv.find((a) => a.startsWith('--port=')) || '--port=8134').split('=')[1];
const BASE = `http://127.0.0.1:${PORT}`;
const SCREENSHOT_DIR = path.join(ROOT, 'docs', 'screenshots');

const ROUTES = [
  '/',
  '/materiales/hierro/',
  '/materiales/cemento/',
  '/guias/',
  '/calculadoras/bolsas-de-cemento-por-m2/',
  '/proveedores/',
];
const WIDTHS = [320, 360, 390, 1280];

function waitForServer(url, attempts = 40) {
  return new Promise((resolve, reject) => {
    const tryOnce = (n) => {
      fetch(url).then(() => resolve()).catch((err) => {
        if (n <= 0) return reject(err);
        setTimeout(() => tryOnce(n - 1), 150);
      });
    };
    tryOnce(attempts);
  });
}

async function main() {
  let playwright;
  try {
    playwright = require('playwright');
  } catch (err) {
    console.error('No se pudo cargar "playwright". Instalalo (npm i -D playwright) o corré con');
    console.error('NODE_PATH=$(npm root -g) node tests/mobile-overflow.mjs si ya está global.');
    process.exit(2);
  }

  await mkdir(SCREENSHOT_DIR, { recursive: true });

  const server = spawn('php', ['-S', `127.0.0.1:${PORT}`, '-t', ROOT, path.join(ROOT, 'tools', 'router-cli.php')], {
    cwd: ROOT,
    stdio: 'ignore',
  });

  let failures = 0;

  try {
    await waitForServer(BASE + '/');

    const executablePath = process.env.PLAYWRIGHT_CHROMIUM_PATH || '/opt/pw-browsers/chromium';
    const browser = await playwright.chromium.launch({ executablePath }).catch(() => playwright.chromium.launch());

    for (const routePath of ROUTES) {
      for (const width of WIDTHS) {
        const page = await browser.newPage({ viewport: { width, height: 800 } });
        const response = await page.goto(BASE + routePath, { waitUntil: 'networkidle' });
        const status = response ? response.status() : 0;
        if (status !== 200) {
          console.error(`FAIL  ${routePath} @ ${width}px → HTTP ${status}`);
          failures++;
          await page.close();
          continue;
        }

        const { scrollWidth, clientWidth } = await page.evaluate(() => ({
          scrollWidth: document.documentElement.scrollWidth,
          clientWidth: document.documentElement.clientWidth,
        }));

        if (scrollWidth !== clientWidth) {
          console.error(`FAIL  ${routePath} @ ${width}px → scrollWidth ${scrollWidth} !== clientWidth ${clientWidth}`);
          failures++;
          const shotName = `${routePath.replace(/\W+/g, '-') || 'home'}-${width}.png`;
          await page.screenshot({ path: path.join(SCREENSHOT_DIR, shotName), fullPage: true }).catch(() => {});
        } else {
          console.log(`ok    ${routePath} @ ${width}px`);
        }

        await page.close();
      }
    }

    await browser.close();
  } finally {
    server.kill();
  }

  if (failures > 0) {
    console.error(`\nmobile-overflow: ${failures} desbordes encontrados.`);
    process.exit(1);
  }
  console.log('\nmobile-overflow: OK, sin desborde horizontal.');
}

main().catch((err) => {
  console.error(err);
  process.exit(2);
});
