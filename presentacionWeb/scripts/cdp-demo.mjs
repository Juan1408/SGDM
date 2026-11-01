#!/usr/bin/env node
/**
 * Recorrido E2E real de ASCEND y captura de pantallas para la presentación web.
 *
 * Requisitos:
 *   - Stack Docker arriba (sgdm_web en http://127.0.0.1:8080, sgdm_db healthy).
 *   - Chrome instalado en la ruta estándar de Windows.
 *   - DEMO_PASSWORD con la contraseña de demostración documentada.
 *
 * El script crea dos jugadores y un equipo de demostración, los inscribe en el
 * torneo 1217 y aprueba la inscripción con la cuenta organizadora 1000.
 * Las capturas quedan en presentacionWeb/capturas. No imprime credenciales,
 * tokens ni códigos de invitación.
 */

import { spawn, spawnSync } from 'node:child_process';
import { mkdirSync, writeFileSync, readFileSync, existsSync, rmSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..', '..');
const OUT = resolve(ROOT, 'presentacionWeb', 'capturas');
const SCRATCH = resolve(ROOT, 'scratch');
const STATE_FILE = resolve(SCRATCH, 'demo-state.json');
const LOG_FILE = resolve(SCRATCH, 'demo-run.log');
const PROFILE = resolve(SCRATCH, 'chrome-demo-profile');
const BASE = process.env.DEMO_BASE || 'http://127.0.0.1:8080/';
const CHROME = process.env.CHROME_BIN || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const PORT = Number(process.env.CDP_PORT || 9333);
const PASSWORD = process.env.DEMO_PASSWORD || '';
const TORNEO_ID = 1217;
const ORGANIZADOR_ID = 1000;
const ADMIN_ID = 1;

if (!PASSWORD) {
  console.error('Falta DEMO_PASSWORD. Exportala antes de ejecutar (no se imprime).');
  process.exit(2);
}

mkdirSync(OUT, { recursive: true });
mkdirSync(SCRATCH, { recursive: true });

const logLines = [];
function log(step, extra = '') {
  const line = `[${new Date().toISOString()}] ${step}${extra ? ' — ' + extra : ''}`;
  console.log(line);
  logLines.push(line);
  writeFileSync(LOG_FILE, logLines.join('\n') + '\n');
}

function decode(s) {
  return String(s)
    .replace(/&amp;/g, '&')
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'")
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/\u00a0/g, ' ');
}

function sleep(ms) { return new Promise((r) => setTimeout(r, ms)); }

async function waitPort() {
  for (let i = 0; i < 80; i++) {
    try {
      const r = await fetch(`http://127.0.0.1:${PORT}/json/version`);
      if (r.ok) return await r.json();
    } catch { /* reintentar */ }
    await sleep(250);
  }
  throw new Error('Chrome no abrió el puerto de depuración.');
}

class Cdp {
  constructor(wsUrl) {
    this.wsUrl = wsUrl;
    this.id = 0;
    this.pending = new Map();
    this.events = new Map();
  }
  async connect() {
    this.ws = new WebSocket(this.wsUrl);
    await new Promise((res, rej) => {
      this.ws.onopen = res;
      this.ws.onerror = () => rej(new Error('No se pudo conectar al CDP.'));
    });
    this.ws.onmessage = (ev) => {
      const msg = JSON.parse(ev.data);
      if (msg.id && this.pending.has(msg.id)) {
        const { resolve, reject } = this.pending.get(msg.id);
        this.pending.delete(msg.id);
        if (msg.error) reject(new Error(`${msg.error.message} (${msg.error.code})`));
        else resolve(msg.result);
        return;
      }
      if (msg.method && this.events.has(msg.method)) {
        for (const fn of this.events.get(msg.method)) fn(msg.params);
      }
    };
  }
  send(method, params = {}) {
    const id = ++this.id;
    return new Promise((resolve, reject) => {
      this.pending.set(id, { resolve, reject });
      this.ws.send(JSON.stringify({ id, method, params }));
    });
  }
  once(method, timeout = 15000) {
    return new Promise((resolve, reject) => {
      const timer = setTimeout(() => reject(new Error(`Timeout esperando ${method}`)), timeout);
      const fn = (params) => {
        clearTimeout(timer);
        this.events.get(method).delete(fn);
        resolve(params);
      };
      if (!this.events.has(method)) this.events.set(method, new Set());
      this.events.get(method).add(fn);
    });
  }
  close() { try { this.ws.close(); } catch { /* noop */ } }
}

async function launchChrome() {
  if (!existsSync(CHROME)) throw new Error(`No se encontró Chrome en ${CHROME}`);
  const args = [
    '--headless=new',
    '--disable-gpu',
    '--hide-scrollbars',
    '--no-first-run',
    '--no-default-browser-check',
    '--disable-background-networking',
    '--remote-allow-origins=*',
    `--remote-debugging-port=${PORT}`,
    `--user-data-dir=${PROFILE}`,
    '--window-size=1440,900',
    'about:blank',
  ];
  const child = spawn(CHROME, args, { stdio: 'ignore', detached: false });
  await waitPort();
  return child;
}

async function openTarget() {
  const r = await fetch(`http://127.0.0.1:${PORT}/json/new?about:blank`, { method: 'PUT' });
  if (!r.ok) throw new Error(`No se pudo crear la pestaña CDP (${r.status}).`);
  return await r.json();
}

class Page {
  constructor(cdp) {
    this.cdp = cdp;
    this.width = 1440;
    this.height = 900;
  }
  async init() {
    await this.cdp.send('Page.enable');
    await this.cdp.send('Runtime.enable');
    await this.cdp.send('Network.enable');
    await this.setViewport(this.height);
  }
  async setViewport(height) {
    this.height = height;
    await this.cdp.send('Emulation.setDeviceMetricsOverride', {
      width: this.width,
      height,
      deviceScaleFactor: 1,
      mobile: false,
      screenWidth: this.width,
      screenHeight: height,
    });
  }
  async eval(expression) {
    const out = await this.cdp.send('Runtime.evaluate', {
      expression: `(() => { ${expression} })()`,
      awaitPromise: true,
      returnByValue: true,
      userGesture: true,
    });
    if (out.exceptionDetails) {
      const desc = out.exceptionDetails.exception?.description || out.exceptionDetails.text;
      throw new Error(`JS: ${desc}`);
    }
    return out.result?.value;
  }
  async nav(url, wait = 900) {
    const loaded = this.cdp.once('Page.loadEventFired', 20000).catch(() => null);
    await this.cdp.send('Page.navigate', { url });
    await loaded;
    await this.waitReady();
    await sleep(wait);
  }
  async waitReady() {
    for (let i = 0; i < 80; i++) {
      try {
        const state = await this.eval('return document.readyState;');
        if (state === 'complete') return;
      } catch { /* navegando */ }
      await sleep(150);
    }
  }
  async shot(name, { fullPage = true, maxHeight = 4200 } = {}) {
    await this.waitReady();
    await sleep(450);
    let height = 900;
    if (fullPage) {
      const metrics = await this.cdp.send('Page.getLayoutMetrics');
      height = Math.min(Math.max(900, Math.ceil(metrics.contentSize.height)), maxHeight);
      await this.setViewport(height);
      await sleep(350);
    } else {
      await this.setViewport(900);
      await sleep(200);
    }
    const shot = await this.cdp.send('Page.captureScreenshot', {
      format: 'png',
      captureBeyondViewport: true,
      fromSurface: true,
    });
    const file = resolve(OUT, name.endsWith('.png') ? name : name + '.png');
    writeFileSync(file, Buffer.from(shot.data, 'base64'));
    await this.setViewport(900);
    log('Captura', `${name} (${this.width}x${height})`);
    return file;
  }
  async fill(selector, value) {
    const ok = await this.eval(`
      const el = document.querySelector(${JSON.stringify(selector)});
      if (!el) return false;
      el.focus();
      if (el.type === 'checkbox' || el.type === 'radio') {
        el.checked = ${JSON.stringify(Boolean(value))};
      } else {
        el.value = ${JSON.stringify(String(value))};
      }
      el.dispatchEvent(new Event('input', { bubbles: true }));
      el.dispatchEvent(new Event('change', { bubbles: true }));
      return true;
    `);
    if (!ok) throw new Error(`No se encontró el campo ${selector}`);
  }
  async select(selector, value) {
    const ok = await this.eval(`
      const el = document.querySelector(${JSON.stringify(selector)});
      if (!el) return false;
      el.value = ${JSON.stringify(String(value))};
      el.dispatchEvent(new Event('change', { bubbles: true }));
      return true;
    `);
    if (!ok) throw new Error(`No se encontró el select ${selector}`);
  }
  async submit(selector) {
    const loaded = this.cdp.once('Page.loadEventFired', 20000).catch(() => null);
    const ok = await this.eval(`
      const el = document.querySelector(${JSON.stringify(selector)});
      if (!el) return false;
      const form = el.tagName === 'FORM' ? el : el.closest('form');
      if (!form) return false;
      form.requestSubmit();
      return true;
    `);
    if (!ok) throw new Error(`No se pudo enviar ${selector}`);
    await loaded;
    await this.waitReady();
    await sleep(900);
  }
  async clickJs(expression) {
    const loaded = this.cdp.once('Page.loadEventFired', 20000).catch(() => null);
    const ok = await this.eval(expression);
    if (!ok) throw new Error('No se pudo ejecutar el clic.');
    await loaded;
    await this.waitReady();
    await sleep(900);
  }
  async url() { return await this.eval('return location.href;'); }
  async text(selector) {
    return await this.eval(`const el=document.querySelector(${JSON.stringify(selector)}); return el ? el.textContent.trim() : null;`);
  }
}

function dbQuery(sql) {
  const res = spawnSync('docker', ['exec', '-i', 'sgdm_db', 'sh', '-c', 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -N -B ascend'], {
    input: sql,
    encoding: 'utf8',
  });
  if (res.status !== 0) throw new Error('Consulta de apoyo falló: ' + (res.stderr || '').trim());
  return res.stdout.trim();
}

function q(v) { return "'" + String(v).replace(/\\/g, '\\\\').replace(/'/g, "''") + "'"; }

async function login(page, email, password, expectedController) {
  await page.nav(BASE + 'index.php?c=auth&a=mostrarLogin');
  await page.fill('#login-email', email);
  await page.fill('#login-password', password);
  await page.submit('form[action*="procesarLogin"]');
  const url = await page.url();
  if (expectedController && !url.includes(`c=${expectedController}`)) {
    const err = await page.text('.mensaje-error, .alerta-error, .error');
    throw new Error(`Login no redirigió a ${expectedController}. URL=${url}. ${err || ''}`);
  }
  log('Login', `${email} → ${url}`);
}

async function logout(page) {
  await page.nav(BASE + 'index.php?c=auth&a=logout');
}

async function registrar(page, { nombre, email, telefono, password }) {
  await page.nav(BASE + 'index.php?c=auth&a=mostrarRegistro');
  await page.fill('#registro-nombre', nombre);
  await page.fill('#registro-email', email);
  await page.fill('#registro-telefono', telefono);
  await page.fill('#registro-password', password);
  await page.fill('#registro-password-confirmar', password);
  await page.eval(`
    const r = document.querySelector('#rol-jugador'); if (r) { r.checked = true; r.dispatchEvent(new Event('change', {bubbles:true})); }
    const t = document.querySelector('#aceptar_terminos'); if (t) { t.checked = true; t.dispatchEvent(new Event('change', {bubbles:true})); }
    return true;
  `);
  await page.submit('form[action*="procesarRegistro"]');
  const url = await page.url();
  if (!url.includes('procesarRegistro')) {
    const err = await page.text('.mensaje-error, .alerta-error, .error');
    if (err) throw new Error(`Registro rechazado: ${err}`);
  }
  log('Registro', email);
}

async function verificarEmail(page) {
  const link = await page.eval(`
    const a = [...document.querySelectorAll('a')].find(x => (x.href || '').includes('verificarEmail'));
    return a ? a.href : null;
  `);
  if (!link) throw new Error('No apareció el enlace de verificación.');
  await page.nav(link);
  const ok = await page.eval(`
    const txt = document.body.innerText;
    return /verificad|activad/i.test(txt) && !/Token inválido|no válido|expirado/i.test(txt);
  `);
  if (!ok) throw new Error('La verificación de correo no se confirmó.');
  log('Verificación de correo', 'OK');
}

async function main() {
  log('Inicio', BASE);
  const resume = (process.env.DEMO_RESUME || '').toLowerCase();
  const existing = existsSync(STATE_FILE) ? JSON.parse(readFileSync(STATE_FILE, 'utf8')) : {};
  if (resume === 'admin' && !existing.playerA) {
    throw new Error('DEMO_RESUME=admin requiere un scratch/demo-state.json de una corrida previa.');
  }
  const stamp = existing.stamp || Date.now();
  const state = {
    stamp,
    playerA: existing.playerA || `charrua.demo.${stamp}.a@ascend.test`,
    playerB: existing.playerB || `charrua.demo.${stamp}.b@ascend.test`,
    teamName: existing.teamName || `CharruaCode Demo ${String(stamp).slice(-5)}`,
  };
  if (!existsSync(PROFILE)) mkdirSync(PROFILE, { recursive: true });

  const chrome = await launchChrome();
  const target = await openTarget();
  const cdp = new Cdp(target.webSocketDebuggerUrl);
  await cdp.connect();
  const page = new Page(cdp);
  await page.init();

  log('Modo', resume === 'admin' ? 'retomar solo bloque administrador' : 'recorrido completo');

  try {
    if (resume !== 'admin') {
    // ---------- 1. Público ----------
    await logout(page);
    await page.nav(BASE + 'index.php?c=inicio&a=index');
    await page.shot('01-home-publica');
    await page.nav(BASE + 'index.php?c=torneo&a=catalogoPublico');
    await page.shot('02-catalogo-torneos');
    await page.nav(BASE + `index.php?c=torneo&a=detalle&id=${TORNEO_ID}`);
    await page.shot('03-detalle-torneo');
    await page.nav(BASE + 'index.php?c=organizador&a=index');
    await page.shot('04-organizadores');
    await page.nav(BASE + `index.php?c=perfil&a=organizador&id=${ORGANIZADOR_ID}`);
    await page.shot('05-perfil-organizador');
    await page.nav(BASE + 'index.php?c=inicio&a=contacto');
    await page.shot('06-contacto');
    await page.nav(BASE + 'index.php?c=inicio&a=formatos');
    await page.shot('07-formatos');

    // ---------- 2. Registro y jugador A ----------
    await page.nav(BASE + 'index.php?c=auth&a=mostrarRegistro');
    await page.shot('08-formulario-registro');
    await registrar(page, {
      nombre: 'Mateo Charrua',
      email: state.playerA,
      telefono: '+5989901234',
      password: PASSWORD,
    });
    await page.shot('09-cuenta-creada-verificacion');
    await verificarEmail(page);
    await page.shot('10-correo-verificado');

    await login(page, state.playerA, PASSWORD, 'panelJugador');
    await page.shot('11-panel-jugador', { fullPage: false });
    await page.nav(BASE + 'index.php?c=equipo&a=crear');
    await page.fill('[name="nombre_equipo"]', state.teamName);
    await page.select('[name="juego_id"]', '1');
    await page.fill('[name="ubicacion"]', 'Montevideo - Uruguay');
    await page.fill('[name="anio_fundacion"]', '2026');
    await page.fill('[name="descripcion"]', 'Equipo de demostración creado durante el recorrido E2E de ASCEND.');
    await page.shot('12-crear-equipo');
    await page.submit('form[action*="procesarCreacion"]');
    const teamUrl = await page.url();
    state.teamUrl = teamUrl;
    state.teamId = Number((teamUrl.match(/[?&]id=(\d+)/) || [])[1] || 0);
    if (!state.teamId) throw new Error(`No se pudo obtener el ID del equipo. URL=${teamUrl}`);
    log('Equipo creado', `ID ${state.teamId}`);
    await page.shot('13-equipo-creado');

    await page.nav(BASE + `index.php?c=equipo&a=gestionar&id=${state.teamId}`);
    state.codigo = await page.text('#codigo-invitacion-texto');
    if (!state.codigo) throw new Error('No se pudo leer el código de invitación.');
    await page.shot('14-codigo-invitacion');

    await logout(page);

    // ---------- 3. Jugador B se une por código ----------
    await page.nav(BASE + 'index.php?c=auth&a=mostrarRegistro');
    await registrar(page, {
      nombre: 'Sofía Charrua',
      email: state.playerB,
      telefono: '+5989905678',
      password: PASSWORD,
    });
    await page.shot('15-segundo-registro');
    await verificarEmail(page);
    await login(page, state.playerB, PASSWORD, 'panelJugador');
    await page.nav(BASE + 'index.php?c=equipo&a=solicitudes');
    await page.fill('[name="codigo"]', state.codigo);
    await page.shot('16-unirse-por-codigo');
    await page.submit('form[action*="unirsePorCodigo"]');
    const unionUrl = await page.url();
    if (!unionUrl.includes('union=ok') && !unionUrl.includes('union=ya')) {
      throw new Error(`El ingreso por código no se confirmó. URL=${unionUrl}`);
    }
    await page.shot('17-equipo-con-dos-miembros');
    await logout(page);

    // ---------- 4. Jugador A inscribe el equipo ----------
    await login(page, state.playerA, PASSWORD, 'panelJugador');
    await page.nav(BASE + `index.php?c=torneo&a=detalle&id=${TORNEO_ID}`);
    await page.shot('18-inscripcion-equipo');
    await page.eval(`
      const sel = document.querySelector('[name="equipo_id"]');
      if (sel) { sel.value = ${JSON.stringify(String(state.teamId))}; sel.dispatchEvent(new Event('change', {bubbles:true})); }
      return true;
    `);
    await page.submit('form[action*="inscribirse"]');
    await page.shot('19-inscripcion-pendiente');
    const pendiente = await page.eval('return document.body.innerText;');
    if (!/pendiente|solicitad/i.test(pendiente)) {
      log('Aviso', 'No se detectó el texto de inscripción pendiente.');
    }
    await logout(page);

    // ---------- 5. Organizador ----------
    await login(page, 'lucia.fernandez@ascend.test', PASSWORD, 'panelOrganizador');
    await page.shot('20-panel-organizador', { fullPage: false });
    await page.nav(BASE + `index.php?c=panelOrganizador&a=administrar&seccion=solicitudes&id=${TORNEO_ID}`);
    await page.shot('21-solicitudes-inscripcion');
    const aprobado = await page.eval(`
      const team = ${JSON.stringify(state.teamName)};
      const articles = [...document.querySelectorAll('article')];
      for (const art of articles) {
        if (!art.textContent.includes(team)) continue;
        const form = [...art.querySelectorAll('form')].find(f => (f.querySelector('[name="respuesta"]')?.value === 'confirmado'));
        if (form) { form.requestSubmit(); return true; }
      }
      return false;
    `);
    if (!aprobado) throw new Error('No se encontró la solicitud del equipo para aprobar.');
    await page.waitReady();
    await sleep(900);
    await page.shot('22-inscripcion-aprobada');
    await page.nav(BASE + `index.php?c=panelOrganizador&a=administrar&seccion=participantes&id=${TORNEO_ID}`);
    await page.shot('23-participantes-confirmados');
    await page.nav(BASE + 'index.php?c=panelOrganizador&a=misTorneos');
    await page.shot('24-mis-torneos');
    await page.nav(BASE + 'index.php?c=panelOrganizador&a=crear');
    await page.shot('25-asistente-crear-torneo');
    await page.nav(BASE + 'index.php?c=panelOrganizador&a=calendario');
    await page.shot('26-calendario-organizador');
    await logout(page);
    } // fin del recorrido público, jugador y organizador

    // ---------- 6. Administrador ----------
    if (resume === 'admin') await logout(page);
    await login(page, 'admin@ascend.com', PASSWORD, 'admin');
    await page.shot('27-panel-admin', { fullPage: false });

    state.playerAId = Number(dbQuery(`SELECT id FROM usuarios WHERE email=${q(state.playerA)} LIMIT 1;`));
    state.playerBId = Number(dbQuery(`SELECT id FROM usuarios WHERE email=${q(state.playerB)} LIMIT 1;`));

    await page.nav(BASE + 'index.php?c=usuario&a=index');
    await page.shot('28-usuarios');
    if (state.playerAId) {
      await page.nav(BASE + `index.php?c=usuario&a=ver&id=${state.playerAId}`);
      await page.shot('29-ficha-usuario');
      await page.nav(BASE + `index.php?c=usuario&a=editarUsuario&id=${state.playerAId}`);
      await page.shot('30-editar-rol');
    }
    await page.nav(BASE + 'index.php?c=torneoAdmin&a=index');
    await page.shot('31-torneos-admin');
    await page.nav(BASE + `index.php?c=torneoAdmin&a=ver&id=${TORNEO_ID}`);
    await page.shot('32-ficha-torneo-admin');
    await page.nav(BASE + 'index.php?c=auditoria&a=index&fecha=' + encodeURIComponent(dbQuery('SELECT CURDATE();')));
    await page.shot('33-auditoria-dia');
    await page.nav(BASE + 'index.php?c=errorTracking&a=dashboard');
    await page.shot('34-errores-sistema');
    await page.nav(BASE + 'index.php?c=admin&a=dashboard');
    await page.shot('35-admin-dashboard-final', { fullPage: false });

    state.auditCount = dbQuery(`SELECT COUNT(*) FROM logs_actividad WHERE DATE(fecha_hora)=CURDATE();`);
    state.finishedAt = new Date().toISOString();
    writeFileSync(STATE_FILE, JSON.stringify(state, null, 2));
    log('Fin', `capturas en ${OUT}`);
  } catch (err) {
    log('ERROR', err.message);
    try { await page.shot('99-error'); } catch { /* sin captura */ }
    state.error = err.message;
    writeFileSync(STATE_FILE, JSON.stringify(state, null, 2));
    throw err;
  } finally {
    cdp.close();
    try { chrome.kill(); } catch { /* noop */ }
  }
}

main().catch((e) => {
  console.error(e.message);
  process.exit(1);
});
