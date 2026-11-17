<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Shugia Safaris — Contact Us';
$pageDesc  = 'Request a personalized quote — we reply within 2 hours.';
$pageKey   = 'contact';

include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
  <div class="container">
    <a href="/home" data-i18n="nav_home">Home</a> <span>/</span>
    <strong data-i18n="nav_contact">Contact</strong>
  </div>
</div>

<main>
  <section class="page-section">
    <div class="container">
      <h2 class="section-title" data-i18n="contact_title">Get in Touch</h2>
      <p class="section-subtitle" data-i18n="contact_sub">Request a personalized quote — we reply within 2 hours.</p>

      <div class="contact-grid">
        <div class="contact-card">
          <i class="fas fa-phone"></i>
          <h4 data-i18n="contact_phone_label">Phone</h4>
          <p>+254 702 243 495</p>
        </div>
        <div class="contact-card">
          <i class="fas fa-envelope"></i>
          <h4 data-i18n="contact_email_label">Email</h4>
          <a href="mailto:tours@shugiasafaris.com">tours@shugiasafaris.com</a>
        </div>
        <div class="contact-card">
          <i class="fab fa-whatsapp"></i>
          <h4 data-i18n="contact_wa_label">WhatsApp</h4>
          <a href="https://wa.me/254702243495" target="_blank" rel="noopener">+254 702 243 495</a>
        </div>
        <div class="contact-card">
          <i class="fas fa-map-marker-alt"></i>
          <h4 data-i18n="contact_office_label">Office</h4>
          <p>Gede Watamu Road, Watamu, Kenya</p>
        </div>
      </div>
    </div>
  </section>

  <section class="page-section page-section--alt" id="quote">
    <div class="container">
      <h2 class="section-title" data-i18n="quote_title">Request a Quote</h2>
      <p class="section-subtitle" data-i18n="contact_sub">Request a personalized quote — we reply within 2 hours.</p>

      <form class="quote-form" id="quoteForm" novalidate>
        <input type="text" name="website" tabindex="-1" style="position:absolute;left:-9999px" aria-hidden="true">
        <div class="form-row">
          <div class="form-group">
            <label for="quoteName"><span data-i18n="quote_name">Full Name</span> <span class="required">*</span></label>
            <input type="text" id="quoteName" name="name" data-i18n-placeholder="quote_placeholder_name" required>
          </div>
          <div class="form-group">
            <label for="quoteEmail"><span data-i18n="quote_email">Email</span> <span class="required">*</span></label>
            <input type="email" id="quoteEmail" name="email" data-i18n-placeholder="quote_placeholder_email" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="quotePhone" data-i18n="quote_phone">Phone (optional)</label>
            <input type="tel" id="quotePhone" name="phone" data-i18n-placeholder="quote_placeholder_phone">
          </div>
          <div class="form-group">
            <label for="quoteDate" data-i18n="quote_date">Travel Date</label>
            <input type="text" id="quoteDate" name="travelDate" data-datepicker data-i18n-placeholder="quote_placeholder_date">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group form-group--full">
            <label for="quoteDest" data-i18n="quote_dest">Destination</label>
            <select id="quoteDest" name="destination">
              <option value="Maasai Mara" data-i18n="quote_dest_mara">Maasai Mara</option>
              <option value="Amboseli" data-i18n="quote_dest_amboseli">Amboseli</option>
              <option value="Mount Kenya" data-i18n="quote_dest_kenya">Mount Kenya</option>
              <option value="Tsavo" data-i18n="quote_dest_tsavo">Tsavo East & West</option>
              <option value="Lake Nakuru" data-i18n="quote_dest_nakuru">Lake Nakuru</option>
              <option value="Diani Beach" data-i18n="quote_dest_diani">Diani Beach</option>
              <option value="Custom" data-i18n="quote_dest_custom">Custom / Multiple</option>
            </select>
          </div>
        </div>

        <div class="form-group form-group--full">
          <label for="quoteMessage"><span data-i18n="quote_msg">Message</span> <span class="required">*</span></label>
          <textarea id="quoteMessage" name="message" rows="4" data-i18n-placeholder="quote_placeholder_msg" required></textarea>
        </div>

        <button type="submit" class="btn btn-accent" data-i18n="quote_submit">Send Request</button>
        <p class="form-feedback" role="status"></p>
      </form>
    </div>
  </section>

  <section class="page-section">
    <div class="container">
      <h2 class="section-title" data-i18n="contact_form_title">Send Us a Message</h2>
      <p class="section-subtitle" data-i18n="contact_sub">Request a personalized quote — we reply within 2 hours.</p>

      <form class="quote-form" id="contactForm" novalidate>
        <input type="text" name="website" tabindex="-1" style="position:absolute;left:-9999px" aria-hidden="true">
        <div class="form-row">
          <div class="form-group">
            <label for="contactName"><span data-i18n="contact_form_name">Full Name</span> <span class="required">*</span></label>
            <input type="text" id="contactName" name="name" data-i18n-placeholder="quote_placeholder_name" required>
          </div>
          <div class="form-group">
            <label for="contactEmail"><span data-i18n="contact_form_email">Email</span> <span class="required">*</span></label>
            <input type="email" id="contactEmail" name="email" data-i18n-placeholder="quote_placeholder_email" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group form-group--full">
            <label for="contactSubject" data-i18n="contact_form_subject">Subject</label>
            <input type="text" id="contactSubject" name="subject">
          </div>
        </div>
        <div class="form-group form-group--full">
          <label for="contactMessage"><span data-i18n="contact_form_msg">Message</span> <span class="required">*</span></label>
          <textarea id="contactMessage" name="message" rows="4" data-i18n-placeholder="quote_placeholder_msg" required></textarea>
        </div>
        <button type="submit" class="btn btn-accent" data-i18n="contact_form_submit">Send Message</button>
        <p class="form-feedback" role="status"></p>
      </form>
    </div>
  </section>

  <section class="page-section page-section--alt">
    <div class="container">
      <h2 class="section-title" data-i18n="contact_map_title">Visit Our Office</h2>
      <p class="section-subtitle" data-i18n="contact_map_sub">Gede Watamu Road, Watamu — on Kenya's north coast.</p>
      <div class="map-embed">
        <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=39.98%2C-3.40%2C40.05%2C-3.33&amp;layer=mapnik&amp;marker=-3.3667%2C40.0167"
                loading="lazy" title="Shugia Safaris office"></iframe>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>