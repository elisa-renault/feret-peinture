const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { join } = require('node:path');
const { runInNewContext } = require('node:vm');

// Exercise the actual script's submit lifecycle without an HTTP request.
function setup() {
  const formEvents = {};
  const windowEvents = {};
  const attributes = {};
  const submit = {
    textContent: 'Envoyer ma demande', disabled: false,
    setAttribute: (key, value) => { attributes[key] = value; },
    removeAttribute: (key) => { delete attributes[key]; },
  };
  const status = { textContent: '' };
  const form = {
    querySelector: (selector) => selector === 'button[type="submit"]' ? submit : status,
    addEventListener: (name, callback) => { formEvents[name] = callback; },
  };
  const document = {
    documentElement: { classList: { add() {} } },
    querySelector: (selector) => selector === '.fp-form, [data-quote-form]' ? form : null,
    addEventListener() {},
  };
  const window = {
    addEventListener: (name, callback) => { windowEvents[name] = callback; },
    dispatchEvent() {},
  };
  runInNewContext(readFileSync(join(__dirname, '../wp-content/themes/feret-peinture/assets/site.js'), 'utf8'), { document, window });
  return { formEvents, windowEvents, submit, status, attributes };
}

test('submission gives feedback and prevents repeated submission', () => {
  const ui = setup();
  assert.equal(ui.submit.disabled, false);
  let prevented = 0;
  ui.formEvents.submit({ preventDefault() { prevented++; } });
  assert.equal(prevented, 0, 'first POST remains native');
  assert.equal(ui.submit.disabled, true);
  assert.equal(ui.submit.textContent, 'Envoi en cours…');
  assert.equal(ui.attributes['aria-busy'], 'true');
  assert.equal(ui.status.textContent, '');
  ui.formEvents.submit({ preventDefault() { prevented++; } });
  assert.equal(prevented, 1, 'second submit is blocked');
});

test('browser history restoration allows another attempt', () => {
  const ui = setup();
  ui.formEvents.submit({ preventDefault() {} });
  ui.windowEvents.pageshow();
  assert.equal(ui.submit.disabled, false);
  assert.equal(ui.submit.textContent, 'Envoyer ma demande');
  assert.equal(ui.attributes['aria-busy'], undefined);
  assert.equal(ui.status.textContent, '');
  let prevented = false;
  ui.formEvents.submit({ preventDefault() { prevented = true; } });
  assert.equal(prevented, false);
});
