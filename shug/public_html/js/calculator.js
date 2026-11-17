/* ==========================================================================
   SHUGIA SAFARIS — Trip Calculator
   Works on /calculator and /home. Reads DOM element IDs:
   #calcStartDate #calcEndDate #calcPeople #calcPeopleMinus #calcPeoplePlus
   #calcPackage #calcResult #calcDays #calcBtn
   Uses window.Currency.convert for currency conversion.
   ========================================================================== */
(function () {
  'use strict';

  const root = document.querySelector('.calculator-box');
  if (!root) return;

  const startInput    = document.getElementById('calcStartDate');
  const endInput      = document.getElementById('calcEndDate');
  const peopleInput   = document.getElementById('calcPeople');
  const peopleMinus   = document.getElementById('calcPeopleMinus');
  const peoplePlus    = document.getElementById('calcPeoplePlus');
  const packageSelect = document.getElementById('calcPackage');
  const resultEl      = document.getElementById('calcResult');
  const daysEl        = document.getElementById('calcDays');
  const updateBtn     = document.getElementById('calcBtn');
  const addonInputs   = root.querySelectorAll('input[name="addon"]');

  if (!startInput || !endInput || !peopleInput || !packageSelect || !resultEl) {
    return;
  }

  const pad = n => String(n).padStart(2, '0');
  const fmt = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
  const clamp = (n, a, b) => Math.min(b, Math.max(a, n));

  function daysBetween(a, b) {
    if (!a || !b) return 1;
    const s = new Date(a + 'T00:00:00');
    const e = new Date(b + 'T00:00:00');
    if (isNaN(s) || isNaN(e)) return 1;
    return Math.max(1, Math.round((e - s) / 86400000));
  }

  function setPeople(val) {
    const n = clamp(parseInt(val, 10) || 1, 1, 20);
    peopleInput.value = n;
    if (peopleMinus) peopleMinus.disabled = n <= 1;
    if (peoplePlus)  peoplePlus.disabled  = n >= 20;
    calculate();
  }

  if (peopleMinus) peopleMinus.addEventListener('click', () =>
    setPeople((parseInt(peopleInput.value, 10) || 1) - 1));
  if (peoplePlus)  peoplePlus.addEventListener('click', () =>
    setPeople((parseInt(peopleInput.value, 10) || 1) + 1));

  peopleInput.addEventListener('input', () => setPeople(peopleInput.value));
  peopleInput.addEventListener('blur',  () => setPeople(peopleInput.value));

  startInput.addEventListener('change', () => {
    endInput.min = startInput.value;
    if (endInput.value && endInput.value < startInput.value) {
      endInput.value = startInput.value;
      endInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
    calculate();
  });
  endInput.addEventListener('change', calculate);
  packageSelect.addEventListener('change', calculate);
  addonInputs.forEach(a => a.addEventListener('change', calculate));

  function calculate() {
    const days   = daysBetween(startInput.value, endInput.value);
    const people = clamp(parseInt(peopleInput.value, 10) || 1, 1, 20);
    const price  = parseFloat(packageSelect.value) || 450;

    let totalUSD = price * people * days;

    addonInputs.forEach(a => {
      if (a.checked) totalUSD += parseFloat(a.dataset.price || 0) * people;
    });

    if (daysEl) {
      daysEl.textContent = '(' + days + ' day' + (days === 1 ? '' : 's') + ')';
    }

    resultEl.textContent = window.Currency
      ? window.Currency.convert(totalUSD, window.Currency.getSelected())
      : '$' + Math.round(totalUSD).toLocaleString();

    try {
      localStorage.setItem('shugia_calc', JSON.stringify({
        start: startInput.value,
        end: endInput.value,
        people,
        packagePrice: packageSelect.value,
        addons: Array.from(addonInputs).filter(a => a.checked).map(a => a.value)
      }));
    } catch (e) { /* noop */ }
  }

  if (updateBtn) {
    updateBtn.addEventListener('click', e => {
      e.preventDefault();
      calculate();
      updateBtn.classList.add('btn-loading');
      setTimeout(() => updateBtn.classList.remove('btn-loading'), 400);
    });
  }

  function restore() {
    try {
      const raw = localStorage.getItem('shugia_calc');
      if (!raw) return;
      const s = JSON.parse(raw);
      if (s.start) startInput.value = s.start;
      if (s.end)   endInput.value   = s.end;
      if (s.people) setPeople(s.people);
      if (s.packagePrice) packageSelect.value = s.packagePrice;
      if (Array.isArray(s.addons)) addonInputs.forEach(a => a.checked = s.addons.includes(a.value));
    } catch (e) { /* noop */ }
  }

  // Defaults
  if (!startInput.value) {
    const d = new Date();
    d.setDate(d.getDate() + 7);
    startInput.value = fmt(d);
    d.setDate(d.getDate() + 3);
    endInput.value = fmt(d);
  }
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  startInput.min = fmt(today);
  if (!endInput.min) endInput.min = startInput.value;

  setPeople(peopleInput.value);
  restore();
  calculate();

  document.addEventListener('currency:changed', calculate);
})();