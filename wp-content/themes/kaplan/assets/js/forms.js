(function () {
  'use strict';

  const forms = document.querySelectorAll('form.kpl-form');
  if (!forms.length || typeof KPL_FORMS === 'undefined') return;

  forms.forEach(setupForm);

  function setupForm(form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const status = form.querySelector('.kpl-form__status');
      const button = form.querySelector('button[type="submit"]');
      const orig   = button ? button.innerHTML : '';

      // Disable + loading state
      if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> ' + KPL_FORMS.i18n.sending;
      }
      if (status) {
        status.textContent = '';
        status.className = 'kpl-form__status';
      }

      // Build form data
      const fd = new FormData(form);
      fd.append('action', 'kpl_submit');
      fd.append('_kpl_nonce', KPL_FORMS.nonce);

      try {
        const res = await fetch(KPL_FORMS.ajaxurl, {
          method: 'POST',
          credentials: 'same-origin',
          body: fd,
        });
        const json = await res.json().catch(() => ({ ok: false, message: KPL_FORMS.i18n.error }));

        if (json.ok) {
          showStatus(status, 'success', json.message);
          form.reset();
          if (json.redirect) {
            // Başarı mesajı kısa süre görünsün, sonra yönlendir.
            setTimeout(function () { window.location.href = json.redirect; }, 1200);
          }
        } else {
          showStatus(status, 'error', json.message || KPL_FORMS.i18n.error);
        }
      } catch (err) {
        showStatus(status, 'error', KPL_FORMS.i18n.error);
      } finally {
        if (button) {
          button.disabled = false;
          button.innerHTML = orig;
        }
      }
    });
  }

  function showStatus(el, type, msg) {
    if (!el) return;
    el.className = 'kpl-form__status kpl-form__status--' + type;
    el.textContent = msg;
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
})();
