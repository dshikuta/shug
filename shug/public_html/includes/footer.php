<footer class="site-footer">
  <div class="footer-newsletter">
    <div class="container footer-newsletter__inner">
      <div class="footer-newsletter__text">
        <h4 data-i18n="footer_newsletter_title">Join our safari newsletter</h4>
        <p data-i18n="footer_newsletter_text">Get first access to new packages and travel tips.</p>
      </div>
      <form class="footer-newsletter__form" id="newsletterForm" novalidate>
        <input type="email" name="email" placeholder="Your email address" data-i18n-placeholder="footer_newsletter_placeholder" aria-label="Email" required>
        <button type="submit" data-i18n="footer_newsletter_btn">Subscribe</button>
      </form>
    </div>
  </div>

  <div class="footer-main">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="/home" class="footer-brand__logo">
            <img src="/images/logo-compact.png" alt="Shugia Safaris" class="footer-logo-img" loading="lazy">
          </a>
          <p class="footer-brand__tagline" data-i18n="footer_about">Crafting authentic African safaris since 2010.</p>
          <div class="footer-brand__contact">
            <a href="tel:+254702243495" class="footer-brand__contact-item"><i class="fas fa-phone"></i><span>+254 702 243 495</span></a>
            <a href="mailto:tours@shugiasafaris.com" class="footer-brand__contact-item"><i class="fas fa-envelope"></i><span>tours@shugiasafaris.com</span></a>
            <span class="footer-brand__contact-item"><i class="fas fa-map-marker-alt"></i><span>Gede Watamu Road, Watamu, Kenya</span></span>
          </div>
          <div class="footer-social">
            <span class="footer-social__label" data-i18n="footer_social_label">Follow Us</span>
            <div class="footer-social__links">
              <a href="https://facebook.com/shugiasafaris" target="_blank" rel="noopener" class="footer-social__link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="https://instagram.com/shugiasafaris" target="_blank" rel="noopener" class="footer-social__link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
              <a href="https://twitter.com/shugiasafaris" target="_blank" rel="noopener" class="footer-social__link" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
              <a href="https://wa.me/254702243495" target="_blank" rel="noopener" class="footer-social__link" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
          </div>
        </div>
        <div class="footer-col">
          <h4 data-i18n="footer_links">Quick Links</h4>
          <a href="/home" data-i18n="nav_home">Home</a>
          <a href="/destinations" data-i18n="nav_dest">Destinations</a>
          <a href="/packages" data-i18n="nav_packages">Packages</a>
          <a href="/calculator" data-i18n="nav_calc">Trip Calculator</a>
          <a href="/contact" data-i18n="nav_contact">Contact</a>
        </div>
        <div class="footer-col">
          <h4 data-i18n="footer_company">Company</h4>
          <a href="/why-us" data-i18n="nav_why">Why Us</a>
          <a href="/faq" data-i18n="nav_faq">FAQ</a>
          <a href="/privacy" data-i18n="footer_privacy">Privacy Policy</a>
          <a href="/terms" data-i18n="footer_terms">Terms of Service</a>
        </div>
        <div class="footer-col">
          <h4 data-i18n="footer_contact">Contact</h4>
          <p>Mon–Fri: 8:00 – 18:00 EAT</p>
          <p>Sat: 9:00 – 14:00 EAT</p>
          <a href="https://wa.me/254702243495" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
          <a href="/contact#quote"><i class="fas fa-plane-departure"></i> Plan Trip</a>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container footer-bottom__inner">
      <span>© <span id="footerYear"><?= date('Y') ?></span> Shugia Safaris. <span data-i18n="footer_copy_suffix">All rights reserved.</span></span>
      <div class="footer-bottom__links">
        <a href="/privacy" data-i18n="footer_privacy">Privacy Policy</a>
        <a href="/terms" data-i18n="footer_terms">Terms of Service</a>
        <a href="/sitemap.xml" target="_blank">Sitemap</a>
      </div>
    </div>
  </div>
</footer>

<a href="https://wa.me/254702243495?text=Hello%20Shugia%20Safaris" class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
  <i class="fab fa-whatsapp"></i>
</a>

<!-- Auto-translation (translate.js) — replaces old language.js/translations.js -->
<script src="https://res.zvo.cn/translate/translate.js"></script>
<script>
  translate.language.setLocal('english');
  translate.service.use('client.edge');
  translate.listener.start();
  translate.selectLanguageTag.show = false;
  translate.execute();

  (function () {
    var sel = document.getElementById('languageSelect');
    if (!sel) return;
    var map = {
      en: 'english', it: 'italian', de: 'german', fr: 'french',
      es: 'spanish', pt: 'portuguese', sw: 'swahili', ar: 'arabic',
      ja: 'japanese', zh: 'chinese_simplified'
    };
    sel.addEventListener('change', function () {
      var target = map[this.value] || 'english';
      translate.changeLanguage(target);
      try { localStorage.setItem('shugia_language', this.value); } catch (e) {}
    });
    try {
      var saved = localStorage.getItem('shugia_language');
      if (saved && map[saved]) sel.value = saved;
    } catch (e) {}
  })();
</script>
<script src="/js/main.js"></script>