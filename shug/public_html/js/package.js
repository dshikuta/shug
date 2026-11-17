/* ==========================================================================
   SHUGIA SAFARIS — Package modal + filters + search
   Reads window.SHUGIA_PACKAGES injected by packages.php.
   ========================================================================== */
(function () {
  'use strict';

  const grid = document.getElementById('packagesGrid');
  if (!grid) return;

  const PACKAGES = window.SHUGIA_PACKAGES || [];

  const FALLBACK_IMG = 'data:image/svg+xml;utf8,' + encodeURIComponent(
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600">' +
    '<rect width="800" height="600" fill="#1e4d2b"/>' +
    '<text x="400" y="310" text-anchor="middle" fill="#c8a448" font-family="Playfair Display,serif" font-size="42" font-weight="700">Shugia Safaris</text>' +
    '</svg>'
  );

  function formatPrice(usd) {
    if (window.Currency && window.Currency.convert) {
      return window.Currency.convert(usd, window.Currency.getSelected());
    }
    return '$' + Math.round(usd).toLocaleString();
  }

  /* ---------- Filter + Search ---------- */
  let activeFilter = 'all';
  let activeSearch = '';

  function applyFilters() {
    const cards = grid.querySelectorAll('.package-card');
    let visible = 0;
    cards.forEach(card => {
      const cat    = card.dataset.category || '';
      const search = card.dataset.search || '';
      const matchCat = activeFilter === 'all' || cat === activeFilter;
      const matchSearch = !activeSearch || search.indexOf(activeSearch) !== -1;
      const show = matchCat && matchSearch;
      card.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    const existing = grid.querySelector('.pkg-empty');
    if (visible === 0 && !existing) {
      const empty = document.createElement('div');
      empty.className = 'pkg-empty';
      empty.innerHTML = '<i class="fas fa-binoculars"></i><h3>No matching packages</h3><p>Try a different category or search term.</p>';
      grid.appendChild(empty);
    } else if (visible > 0 && existing) {
      existing.remove();
    }
  }

  function initFilters() {
    const tabs = document.querySelectorAll('#categoryTabs .pkg-tab-btn');
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('is-active'));
        tab.classList.add('is-active');
        activeFilter = tab.dataset.filter || 'all';
        applyFilters();
      });
    });

    const searchInput = document.getElementById('pkgSearch');
    if (searchInput) {
      let timer = null;
      searchInput.addEventListener('input', e => {
        clearTimeout(timer);
        timer = setTimeout(() => {
          activeSearch = (e.target.value || '').trim().toLowerCase();
          applyFilters();
        }, 150);
      });
    }
  }

  /* ---------- Modal ---------- */
  let lastFocused = null;

  function openModal(id) {
    const pkg = PACKAGES.find(p => p.id === id);
    if (!pkg) return;
    const modal = document.getElementById('itineraryModal');
    if (!modal) return;

    lastFocused = document.activeElement;

    document.getElementById('modalBadge').textContent    = pkg.badge || 'Package';
    document.getElementById('modalTitle').textContent    = pkg.title || '';
    document.getElementById('modalDuration').textContent = (pkg.duration || 0) + ' Days / ' + (pkg.nights || 0) + ' Nights';
    document.getElementById('modalDesc').textContent     = pkg.summary || '';

    const img = document.getElementById('modalImg');
    img.onerror = null;
    img.src = '/' + (pkg.image || '');
    img.alt = pkg.title || '';
    img.onerror = function () { img.onerror = null; img.src = FALLBACK_IMG; };

    const priceEl = document.getElementById('modalPrice');
    priceEl.setAttribute('data-price-usd', pkg.priceUsd || 0);
    priceEl.textContent = formatPrice(parseFloat(pkg.priceUsd) || 0);

    const daysList = document.getElementById('modalDaysList');
    daysList.innerHTML = '';
    (pkg.days || []).forEach(d => {
      const row = document.createElement('div');
      row.className = 'itinerary-modal__day';
      const num  = document.createElement('div');
      num.className = 'itinerary-modal__day-num';
      num.textContent = d.d || '';
      const text = document.createElement('div');
      text.className = 'itinerary-modal__day-text';
      text.textContent = d.t || '';
      row.appendChild(num);
      row.appendChild(text);
      daysList.appendChild(row);
    });

    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    const closeBtn = modal.querySelector('.itinerary-modal__close');
    if (closeBtn) closeBtn.focus();

    const bookBtn = document.getElementById('modalBookBtn');
    if (bookBtn) {
      bookBtn.onclick = () => {
        window.location.href = '/contact#quote?destination=' + encodeURIComponent(pkg.title || '');
      };
    }
  }

  function closeModal() {
    const modal = document.getElementById('itineraryModal');
    if (!modal) return;
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lastFocused && lastFocused.focus) lastFocused.focus();
  }

  function initModal() {
    const modal = document.getElementById('itineraryModal');
    if (!modal) return;

    modal.querySelectorAll('[data-close-modal]').forEach(el => {
      el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && !modal.hidden) closeModal();
    });

    grid.addEventListener('click', e => {
      const viewBtn = e.target.closest('[data-view-package]');
      if (viewBtn) {
        e.preventDefault();
        openModal(viewBtn.dataset.viewPackage);
        return;
      }
      const card = e.target.closest('.package-card');
      if (card && !e.target.closest('button, a')) {
        openModal(card.dataset.id);
      }
    });
  }

  /* ---------- Currency change ---------- */
  document.addEventListener('currency:changed', () => {
    grid.querySelectorAll('[data-price-usd]').forEach(el => {
      const usd = parseFloat(el.dataset.priceUsd) || 0;
      el.textContent = formatPrice(usd);
    });
    const modalPrice = document.getElementById('modalPrice');
    if (modalPrice && !modalPrice.closest('[hidden]')) {
      const usd = parseFloat(modalPrice.dataset.priceUsd) || 0;
      modalPrice.textContent = formatPrice(usd);
    }
  });

  /* ---------- Boot ---------- */
  initFilters();
  initModal();
  applyFilters();
})();