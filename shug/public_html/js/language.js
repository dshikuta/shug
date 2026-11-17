/* ==========================================================================
   SHUGIA SAFARIS — Language Engine
   Per-key English fallback + auto-translate for dynamically-added nodes.
   Requires window.TRANSLATIONS to exist (from translations.js).
   ========================================================================== */
(function () {
  'use strict';

  const STORAGE_KEY = 'shugia_language';
  const select = document.getElementById('languageSelect');
  const RTL_LANGS = ['ar'];

  function getDict(lang) {
    const all = window.TRANSLATIONS || {};
    const en  = all.en || {};
    const base = all[lang] || {};
    return Object.assign({}, en, base);
  }

  function currentLang() {
    return select ? select.value : 'en';
  }

  function t(key, replacements) {
    const dict = getDict(currentLang());
    let str = dict[key] != null ? dict[key] : key;
    if (replacements) {
      Object.keys(replacements).forEach(k => {
        str = str.replace(new RegExp('\\{' + k + '\\}', 'g'), replacements[k]);
      });
    }
    return str;
  }

  function translateNode(node) {
    if (!node || node.nodeType !== 1) return;

    const i18nKey = node.getAttribute && node.getAttribute('data-i18n');
    if (i18nKey) {
      const val = t(i18nKey);
      if (val !== i18nKey) node.textContent = val;
    }

    const phKey = node.getAttribute && node.getAttribute('data-i18n-placeholder');
    if (phKey) {
      const val = t(phKey);
      if (val !== phKey) node.placeholder = val;
    }

    const ariaKey = node.getAttribute && node.getAttribute('data-i18n-aria');
    if (ariaKey) {
      const val = t(ariaKey);
      if (val !== ariaKey) node.setAttribute('aria-label', val);
    }

    const titleKey = node.getAttribute && node.getAttribute('data-i18n-title');
    if (titleKey) {
      const val = t(titleKey);
      if (val !== titleKey) node.setAttribute('title', val);
    }

    if (node.children && node.children.length) {
      Array.prototype.forEach.call(node.children, translateNode);
    }
  }

  function translateWholePage() {
    const lang = currentLang();
    const dict = getDict(lang);

    document.querySelectorAll('[data-i18n], [data-i18n-placeholder], [data-i18n-aria], [data-i18n-title]')
      .forEach(translateNode);

    document.documentElement.lang = lang;
    document.documentElement.dir  = RTL_LANGS.indexOf(lang) !== -1 ? 'rtl' : 'ltr';

    const pageKey = document.body.dataset.page
      ? 'meta_title_' + document.body.dataset.page
      : 'meta_title_home';
    if (dict[pageKey]) document.title = dict[pageKey];

    const metaDesc = document.querySelector('meta[name="description"]');
    if (metaDesc && dict.meta_desc) metaDesc.setAttribute('content', dict.meta_desc);

    document.dispatchEvent(new CustomEvent('lang:changed', { detail: { lang: lang } }));
  }

  let observer = null;

  function startObserver() {
    if (observer || !window.MutationObserver) return;
    observer = new MutationObserver(mutations => {
      let needs = false;
      for (let i = 0; i < mutations.length; i++) {
        const m = mutations[i];
        if (m.type === 'childList' && m.addedNodes.length) {
          for (let j = 0; j < m.addedNodes.length; j++) {
            const n = m.addedNodes[j];
            if (n.nodeType === 1 && (
              n.hasAttribute('data-i18n') ||
              n.hasAttribute('data-i18n-placeholder') ||
              n.hasAttribute('data-i18n-aria') ||
              n.hasAttribute('data-i18n-title') ||
              (n.querySelector && n.querySelector('[data-i18n],[data-i18n-placeholder],[data-i18n-aria],[data-i18n-title]'))
            )) { needs = true; break; }
          }
        }
        if (m.type === 'attributes' && m.attributeName === 'data-i18n') needs = true;
      }
      if (needs) {
        cancelAnimationFrame(observer._raf);
        observer._raf = requestAnimationFrame(translateWholePage);
      }
    });
    observer.observe(document.body, {
      childList: true, subtree: true, attributes: true,
      attributeFilter: ['data-i18n', 'data-i18n-placeholder', 'data-i18n-aria', 'data-i18n-title']
    });
  }

  if (select) {
    select.addEventListener('change', function (e) {
      try { localStorage.setItem(STORAGE_KEY, e.target.value); } catch (err) { /* noop */ }
      translateWholePage();
    });
  }

  function init() {
    let saved = 'en';
    try { saved = localStorage.getItem(STORAGE_KEY) || 'en'; } catch (err) { /* noop */ }
    const available = Object.keys(window.TRANSLATIONS || { en: 1 });
    if (available.indexOf(saved) === -1) saved = 'en';
    if (select) select.value = saved;
    translateWholePage();
    startObserver();
  }

  if (window.TRANSLATIONS) {
    init();
  } else {
    let tries = 0;
    const wait = setInterval(function () {
      if (window.TRANSLATIONS || tries++ > 20) {
        clearInterval(wait);
        init();
      }
    }, 50);
  }

  window.Language = {
    translate: translateWholePage,
    current: currentLang,
    t: t
  };
})();