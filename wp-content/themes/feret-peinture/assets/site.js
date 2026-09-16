(() => {
  'use strict';
  document.documentElement.classList.add('js');
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#navigation-principale');
  if (toggle && nav) {
    const close = () => { toggle.setAttribute('aria-expanded', 'false'); nav.classList.remove('is-open'); };
    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(open));
      nav.classList.toggle('is-open', open);
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') { close(); toggle.focus(); }
    });
    document.addEventListener('click', (event) => {
      if (!nav.contains(event.target) && !toggle.contains(event.target)) close();
    });
    window.matchMedia('(min-width: 1001px)').addEventListener('change', close);
  }
  // Local events only. No SDK, network call, persisted identifier or form value.
  // Analytics can subscribe to fp:analytics after a separate, explicit decision.
  const emit = (name) => window.dispatchEvent(new CustomEvent('fp:analytics', { detail: { name } }));
  const knownEvents = new Set(['click_phone', 'click_quote']);
  document.addEventListener('click', (event) => {
    const link = event.target.closest('[data-fp-event], [data-event]');
    if (link) { const name = link.dataset.fpEvent || link.dataset.event; if (knownEvents.has(name)) emit(name); }
  });
  const form = document.querySelector('.fp-form, [data-quote-form]');
  if (form) {
    let started = false;
    form.addEventListener('input', () => { if (!started) { started = true; emit('form_start'); } }, { passive: true });
    const submit = form.querySelector('button[type="submit"]');
    const status = form.querySelector('[data-submit-status]');
    if (submit && status) {
      let sending = false;
      const label = submit.textContent;
      const reset = () => {
        sending = false;
        submit.disabled = false;
        submit.removeAttribute('aria-busy');
        submit.textContent = label;
        status.textContent = '';
      };
      // Native validation runs before submit. Keep the regular server POST.
      form.addEventListener('submit', (event) => {
        if (event.defaultPrevented) return;
        if (sending) { event.preventDefault(); return; }
        sending = true;
        submit.disabled = true;
        submit.setAttribute('aria-busy', 'true');
        submit.textContent = 'Envoi en cours…';
      });
      // Restore the button when returning through the browser back/forward cache.
      window.addEventListener('pageshow', reset);
    }
    // Server determines outcomes; client-side validation never emits a success.
    if (document.querySelector('.form-error-summary, [data-has-errors="1"]')) {
      emit('form_error');
      const summary = document.querySelector('.form-error-summary');
      const target = summary || form.querySelector('[aria-invalid="true"]');
      if (target) target.focus();
    }
  }
  if (document.querySelector('[data-form-success]')) emit('form_success');
})();
