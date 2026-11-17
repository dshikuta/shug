/* ==========================================================================
   SHUGIA SAFARIS — Main JS
   Currency, date picker, mobile nav, submenu toggle, AJAX forms (with CSRF),
   newsletter, sticky header, smooth scroll.
   ========================================================================== */
(function () {
  'use strict';

  /* ----- CSRF helper: read meta tag, return header object ----- */
  function csrfHeaders() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? { 'X-CSRF-Token': meta.getAttribute('content') } : {};
  }
  window.csrfHeaders = csrfHeaders;

  /* =========================================================
     1. CURRENCY
     ========================================================= */
  const RATES   = { USD: 1, EUR: 0.92, KES: 130, GBP: 0.79, JPY: 150 };
  const SYMBOLS = { USD: '$', EUR: '€', KES: 'KSh ', GBP: '£', JPY: '¥' };
  const currencySelect = document.getElementById('currencySelect');

  function getCurrency() {
    return currencySelect ? currencySelect.value : 'USD';
  }
  function convertUSD(usd, currency) {
    const code = (currency || getCurrency()).toUpperCase();
    const rate = RATES[code] || 1;
    const symbol = SYMBOLS[code] || '$';
    return symbol + Math.round(usd * rate).toLocaleString();
  }
  function updatePrices() {
    const code = getCurrency();
    document.querySelectorAll('[data-price-usd]').forEach(el => {
      const usd = parseFloat(el.dataset.priceUsd);
      if (!isNaN(usd)) el.textContent = convertUSD(usd, code);
    });
    document.dispatchEvent(new CustomEvent('currency:changed', { detail: { currency: code } }));
  }

  if (currencySelect) {
    try {
      const saved = localStorage.getItem('shugia_currency');
      if (saved && RATES[saved]) currencySelect.value = saved;
    } catch (e) { /* noop */ }
    currencySelect.addEventListener('change', () => {
      try { localStorage.setItem('shugia_currency', currencySelect.value); } catch (e) { /* noop */ }
      updatePrices();
    });
  }
  window.Currency = { convert: convertUSD, getSelected: getCurrency, updatePrices };

  updatePrices();

  /* =========================================================
     2. DATE PICKER
     ========================================================= */
  (function initDatePickers() {
    const MONTHS   = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const WEEKDAYS = ['S','M','T','W','T','F','S'];
    const pad = n => String(n).padStart(2, '0');
    const fmt = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    const parse = s => { if (!s) return null; const d = new Date(s + 'T00:00:00'); return isNaN(d) ? null : d; };
    const same = (a, b) => a && b && a.toDateString() === b.toDateString();

    function build(input) {
      if (input.dataset.dpInit === 'true') return;
      input.dataset.dpInit = 'true';
      input.readOnly = true;

      const wrap = document.createElement('div');
      wrap.className = 'datepicker';
      input.parentNode.insertBefore(wrap, input);
      wrap.appendChild(input);

      const popup = document.createElement('div');
      popup.className = 'datepicker-popup';
      popup.innerHTML =
        '<div class="datepicker-header">' +
          '<button type="button" class="dp-prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>' +
          '<span class="datepicker-month"></span>' +
          '<button type="button" class="dp-next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>' +
        '</div>' +
        '<div class="datepicker-grid datepicker-weekdays"></div>' +
        '<div class="datepicker-grid datepicker-days"></div>' +
        '<div class="datepicker-footer">' +
          '<span>Pick a date</span>' +
          '<button type="button" class="datepicker-clear">Clear</button>' +
        '</div>';
      wrap.appendChild(popup);

      const monthEl    = popup.querySelector('.datepicker-month');
      const weekdaysEl = popup.querySelector('.datepicker-weekdays');
      const daysEl     = popup.querySelector('.datepicker-days');
      weekdaysEl.innerHTML = WEEKDAYS.map(d => '<div class="datepicker-weekday">' + d + '</div>').join('');

      const today = new Date(); today.setHours(0,0,0,0);
      let view     = parse(input.value) || new Date(today);
      let selected = parse(input.value);

      function render() {
        monthEl.textContent = MONTHS[view.getMonth()] + ' ' + view.getFullYear();
        const y = view.getFullYear(), m = view.getMonth();
        const firstDay = new Date(y, m, 1).getDay();
        const daysIn   = new Date(y, m + 1, 0).getDate();
        const prevDays = new Date(y, m, 0).getDate();
        let html = '';
        for (let i = firstDay - 1; i >= 0; i--) html += '<button type="button" class="datepicker-day other-month" disabled>' + (prevDays - i) + '</button>';
        for (let d = 1; d <= daysIn; d++) {
          const date = new Date(y, m, d);
          const disabled = date < today;
          const isSel = same(date, selected);
          const isToday = same(date, today);
          const cls = ['datepicker-day', disabled?'disabled':'', isSel?'selected':'', isToday?'today':''].filter(Boolean).join(' ');
          html += '<button type="button" class="' + cls + '" data-date="' + fmt(date) + '"' + (disabled?' disabled':'') + '>' + d + '</button>';
        }
        const remaining = (7 - ((firstDay + daysIn) % 7)) % 7;
        for (let i = 1; i <= remaining; i++) html += '<button type="button" class="datepicker-day other-month" disabled>' + i + '</button>';
        daysEl.innerHTML = html;
      }
      function open() {
        document.querySelectorAll('.datepicker-popup.open').forEach(p => { if (p !== popup) p.classList.remove('open'); });
        popup.classList.add('open'); render();
      }
      function close() { popup.classList.remove('open'); }

      input.addEventListener('click', open);
      popup.querySelector('.dp-prev').addEventListener('click', e => { e.stopPropagation(); view.setMonth(view.getMonth()-1); render(); });
      popup.querySelector('.dp-next').addEventListener('click', e => { e.stopPropagation(); view.setMonth(view.getMonth()+1); render(); });
      daysEl.addEventListener('click', e => {
        const btn = e.target.closest('button[data-date]');
        if (!btn || btn.disabled) return;
        const parts = btn.dataset.date.split('-').map(Number);
        selected = new Date(parts[0], parts[1]-1, parts[2]);
        input.value = fmt(selected);
        input.dispatchEvent(new Event('change', { bubbles: true }));
        input.dispatchEvent(new Event('input', { bubbles: true }));
        close();
      });
      popup.querySelector('.datepicker-clear').addEventListener('click', e => {
        e.stopPropagation(); selected = null; input.value = '';
        input.dispatchEvent(new Event('change', { bubbles: true }));
        input.dispatchEvent(new Event('input', { bubbles: true }));
        close();
      });
      document.addEventListener('click', e => { if (!wrap.contains(e.target)) close(); });
      document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
    }
    function initAll() { document.querySelectorAll('[data-datepicker]').forEach(build); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initAll);
    else initAll();
    window.DatePicker = { init: build, initAll };
  })();

  /* =========================================================
     3. MOBILE MENU
     ========================================================= */
  const menuToggle = document.getElementById('menuToggle');
  const navLinks   = document.getElementById('navLinks');
  const HAMBURGER_BP = 1023;
  function setMenuIcon(isOpen) {
    if (!menuToggle) return;
    menuToggle.innerHTML = isOpen ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    menuToggle.setAttribute('aria-expanded', String(isOpen));
  }
  function openMenu() {
    if (!navLinks) return;
    navLinks.classList.add('is-open'); document.body.classList.add('menu-open'); setMenuIcon(true);
  }
  function closeMenu() {
    if (!navLinks) return;
    navLinks.classList.remove('is-open'); document.body.classList.remove('menu-open'); setMenuIcon(false);
    document.querySelectorAll('.nav-item.is-submenu-open').forEach(el => {
      el.classList.remove('is-submenu-open');
      const a = el.querySelector(':scope > a'); if (a) a.setAttribute('aria-expanded', 'false');
    });
  }
  if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', () => {
      navLinks.classList.contains('is-open') ? closeMenu() : openMenu();
    });
    navLinks.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', e => {
        if (window.innerWidth > HAMBURGER_BP) return;
        const parentItem = a.closest('.nav-item.has-submenu');
        const isParent = parentItem && a === parentItem.querySelector(':scope > a');
        if (isParent) return;
        closeMenu();
      });
    });
    document.addEventListener('click', e => {
      if (!navLinks.classList.contains('is-open')) return;
      if (navLinks.contains(e.target) || menuToggle.contains(e.target)) return;
      closeMenu();
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && navLinks.classList.contains('is-open')) closeMenu();
    });
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        if (window.innerWidth > HAMBURGER_BP && navLinks.classList.contains('is-open')) closeMenu();
      }, 150);
    });
  }
  document.querySelectorAll('.nav-item.has-submenu').forEach(item => {
    const trigger = item.querySelector(':scope > a');
    const submenu = item.querySelector(':scope > .submenu');
    if (!trigger || !submenu) return;
    trigger.setAttribute('aria-haspopup', 'true');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.addEventListener('click', e => {
      if (window.innerWidth > HAMBURGER_BP) return;
      e.preventDefault(); e.stopPropagation();
      const isOpen = item.classList.contains('is-submenu-open');
      document.querySelectorAll('.nav-item.is-submenu-open').forEach(el => {
        if (el !== item) {
          el.classList.remove('is-submenu-open');
          const t = el.querySelector(':scope > a'); if (t) t.setAttribute('aria-expanded', 'false');
        }
      });
      item.classList.toggle('is-submenu-open', !isOpen);
      trigger.setAttribute('aria-expanded', String(!isOpen));
    });
  });

  /* =========================================================
     4. STICKY HEADER SHADOW
     ========================================================= */
  const header = document.querySelector('.site-header');
  if (header) {
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* =========================================================
     5. AJAX FORMS (quote + contact) → POST to /send_form.php
        Sends X-CSRF-Token from <meta name="csrf-token">.
     ========================================================= */
  function attachForm(formId, formType) {
    const form = document.getElementById(formId);
    if (!form) return;
    const submitBtn = form.querySelector('button[type="submit"]');
    const feedback  = form.querySelector('.form-feedback');

    const fields = {
      name:    form.querySelector('[name="name"]'),
      email:   form.querySelector('[name="email"]'),
      phone:   form.querySelector('[name="phone"]'),
      subject: form.querySelector('[name="subject"]'),
      message: form.querySelector('[name="message"]')
    };
    const rules = {
      name:    { validate: v => v && v.trim().length >= 2, key: 'Please enter your full name.' },
      email:   { validate: v => v && /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()), key: 'Please enter a valid email.' },
      phone:   { validate: v => !v || /^[+\d\s()-]{7,20}$/.test(v.trim()), key: 'Please enter a valid phone number.' },
      message: { validate: v => v && v.trim().length >= 10, key: 'Please write at least 10 characters.' }
    };
    function showFieldError(field, message) {
      field.classList.add('error');
      let err = field.parentElement.querySelector('.field-error');
      if (!err) { err = document.createElement('span'); err.className = 'field-error'; field.parentElement.appendChild(err); }
      err.textContent = message;
    }
    function clearFieldError(field) {
      field.classList.remove('error');
      const err = field.parentElement.querySelector('.field-error'); if (err) err.remove();
    }
    function validateField(key) {
      const f = fields[key]; if (!f || !rules[key]) return true;
      const ok = rules[key].validate(f.value);
      if (ok) clearFieldError(f); else showFieldError(f, rules[key].key);
      return ok;
    }
    Object.keys(rules).forEach(k => {
      if (!fields[k]) return;
      fields[k].addEventListener('blur', () => validateField(k));
      fields[k].addEventListener('input', () => { if (fields[k].classList.contains('error')) validateField(k); });
    });
    function showFeedback(msg, type) {
      if (!feedback) return;
      feedback.textContent = msg;
      feedback.className = 'form-feedback ' + type + ' visible';
      if (type === 'success') setTimeout(() => { feedback.className = 'form-feedback'; }, 8000);
    }

    form.addEventListener('submit', async e => {
      e.preventDefault();
      const valid = Object.keys(rules).every(validateField);
      if (!valid) { showFeedback('Please fix the errors above and try again.', 'error'); return; }

      submitBtn.classList.add('btn-loading');
      submitBtn.disabled = true;

      try {
        const payload = {
          type:        formType,
          name:        fields.name ? fields.name.value.trim() : '',
          email:       fields.email ? fields.email.value.trim() : '',
          phone:       fields.phone ? fields.phone.value.trim() : '',
          subject:     fields.subject ? fields.subject.value.trim() : '',
          message:     fields.message ? fields.message.value.trim() : '',
          destination: form.querySelector('[name="destination"]') ? form.querySelector('[name="destination"]').value : '',
          travelDate:  form.querySelector('[name="travelDate"]') ? form.querySelector('[name="travelDate"]').value : '',
          website:     form.querySelector('[name="website"]') ? form.querySelector('[name="website"]').value : ''
        };

        const res = await fetch('/send_form.php', {
          method: 'POST',
          headers: Object.assign(
            { 'Content-Type': 'application/json' },
            csrfHeaders()
          ),
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (res.ok && data.ok) {
          showFeedback('✅ ' + (data.message || 'Sent successfully!'), 'success');
          form.reset();
          Object.values(fields).forEach(f => f && clearFieldError(f));
        } else {
          showFeedback(data.error || 'Could not send. Please WhatsApp us at +254 702 243 495.', 'error');
        }
      } catch (err) {
        showFeedback('Network error. Please try again or WhatsApp us at +254 702 243 495.', 'error');
      } finally {
        submitBtn.classList.remove('btn-loading');
        submitBtn.disabled = false;
      }
    });
  }
  attachForm('quoteForm',   'quote');
  attachForm('contactForm', 'contact');

  /* =========================================================
     6. NEWSLETTER (local-only, no CSRF needed)
     ========================================================= */
  const newsletterForm = document.getElementById('newsletterForm');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', e => {
      e.preventDefault();
      const input = newsletterForm.querySelector('input[type="email"]');
      const email = (input.value || '').trim();
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
        input.style.borderColor = '#e74c3c'; input.focus(); return;
      }
      input.style.borderColor = '';
      try {
        const list = JSON.parse(localStorage.getItem('shugia_newsletter') || '[]');
        if (!list.some(s => s.email === email)) {
          list.push({ email: email, subscribedAt: new Date().toISOString() });
          localStorage.setItem('shugia_newsletter', JSON.stringify(list));
        }
      } catch (err) { /* noop */ }
      input.value = '';
      const ph = input.placeholder;
      input.placeholder = '✓ Subscribed! Thank you.';
      setTimeout(() => { input.placeholder = ph; }, 3000);
    });
  }

  /* =========================================================
     7. FOOTER YEAR
     ========================================================= */
  const yearEl = document.getElementById('footerYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  /* =========================================================
     8. SMOOTH SCROLL
     ========================================================= */
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', e => {
      const href = link.getAttribute('href');
      if (href === '#' || href === '!') return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
})();