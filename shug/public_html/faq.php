<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Shugia Safaris — Travel FAQ';
$pageDesc  = 'Travel guidelines, booking, safety, visas and packing FAQ for Shugia Safaris.';
$pageKey   = 'faq';

include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
  <div class="container">
    <a href="/home" data-i18n="nav_home">Home</a> <span>/</span>
    <strong data-i18n="nav_faq">FAQ</strong>
  </div>
</div>

<main>
  <section class="page-section">
    <div class="container container-narrow">
      <h2 class="section-title" data-i18n="faq_title">Frequently Asked Questions</h2>
      <p class="section-subtitle" data-i18n="faq_sub">Quick answers to the questions we hear most.</p>

      <div class="faq-list">
        <details class="faq-item" open>
          <summary data-i18n="faq1_q">How far in advance should I book?</summary>
          <div class="faq-item__body" data-i18n="faq1_a">For peak season (July–October), we recommend 4–6 months. For other months, 4–8 weeks is usually enough.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq2_q">Do you arrange flights and visas?</summary>
          <div class="faq-item__body" data-i18n="faq2_a">We arrange internal flights and can recommend partners for international flights. Visa advice is provided free.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq3_q">Is a safari safe for children?</summary>
          <div class="faq-item__body" data-i18n="faq3_a">Yes — we design family-friendly itineraries with shorter drives, family tents, and kid-friendly guides. Children 5+ are welcome.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq4_q">What about vaccinations?</summary>
          <div class="faq-item__body" data-i18n="faq4_a">Yellow fever vaccination is required if arriving from an endemic country. Malaria prophylaxis is recommended.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq5_q">Can I customize a package?</summary>
          <div class="faq-item__body" data-i18n="faq5_a">Absolutely — every itinerary on our site is a starting point. Tell us your dates, budget, and interests and we will tailor it.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq6_q">What is your cancellation policy?</summary>
          <div class="faq-item__body" data-i18n="faq6_a">Free cancellation up to 30 days before departure. 50% refund up to 14 days before. No refund within 14 days.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq7_q">Can you combine a safari with a beach holiday?</summary>
          <div class="faq-item__body" data-i18n="faq7_a">Absolutely — a typical itinerary pairs 4 to 7 days on safari in the Maasai Mara or Amboseli with 3 to 5 days on the beach in Malindi, Watamu or Diani. We handle all logistics including flights and airport transfers.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq8_q">Are your safaris private or group?</summary>
          <div class="faq-item__body" data-i18n="faq8_a">Every itinerary we build is private and tailor-made for your needs. Your own vehicle, driver-guide, and schedule.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq9_q">How do I make changes to my booking?</summary>
          <div class="faq-item__body" data-i18n="faq9_a">Get in touch with the trip consultant who planned your safari to confirm the possibilities.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq10_q">When is the peak time to watch the Wildebeest Migration?</summary>
          <div class="faq-item__body" data-i18n="faq10_a">The Great Migration river crossings in Maasai Mara take place between late July and October. However, Maasai Mara offers exceptional wildlife viewing year-round!</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq11_q">What is included in the full safari package price?</summary>
          <div class="faq-item__body" data-i18n="faq11_a">Private 4x4 Land Cruisers, full board accommodation, park fees, certified guide, airport transfers, and unlimited drinking water.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq12_q">How do we travel from Maasai Mara to the coast?</summary>
          <div class="faq-item__body" data-i18n="faq12_a">A direct bush flight from the Mara airstrip to Malindi or Ukunda takes around 2 hours. Alternatively, fly back to Nairobi and take a domestic flight or the SGR train.</div>
        </details>
        <details class="faq-item">
          <summary data-i18n="faq13_q">What items should I pack for my East African Safari?</summary>
          <div class="faq-item__body" data-i18n="faq13_a">Neutral-colored clothing (khaki, olive, beige), a light jacket for early mornings, comfortable walking shoes, sun protection (hat, sunglasses, sunscreen), a camera with a zoom lens, and a soft duffel bag.</div>
        </details>
      </div>

      <div class="center-action" style="margin-top:48px;">
        <a href="/contact" class="btn btn-accent">Still have questions? Contact us</a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>