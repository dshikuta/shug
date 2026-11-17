<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Shugia Safaris — Why Travel With Us';
$pageDesc  = 'Discover the Shugia Safaris advantage — local expertise, custom 4x4s, certified guides and 12+ years of crafting authentic Kenyan safaris.';
$pageKey   = 'why-us';

include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
  <div class="container">
    <a href="/home" data-i18n="nav_home">Home</a> <span>/</span>
    <strong data-i18n="nav_why">Why Us</strong>
  </div>
</div>

<main>
  <section class="page-section">
    <div class="container">
      <h2 class="section-title" data-i18n="why_title">Why Travel With Us</h2>
      <p class="section-subtitle" data-i18n="why_sub">We go the extra mile to make your safari memorable.</p>

      <div class="grid grid-4">
        <article class="card">
          <i class="fas fa-shield-alt"></i>
          <h3 data-i18n="why1_title">Safety First</h3>
          <p data-i18n="why1_text">Professional guides and well-maintained vehicles.</p>
        </article>
        <article class="card">
          <i class="fas fa-hand-holding-usd"></i>
          <h3 data-i18n="why2_title">Best Value</h3>
          <p data-i18n="why2_text">Transparent pricing with no hidden fees.</p>
        </article>
        <article class="card">
          <i class="fas fa-star"></i>
          <h3 data-i18n="why3_title">5-Star Reviews</h3>
          <p data-i18n="why3_text">Over 800 happy travelers from 40+ countries.</p>
        </article>
        <article class="card">
          <i class="fas fa-users"></i>
          <h3 data-i18n="why4_title">Small Groups</h3>
          <p data-i18n="why4_text">Personalized attention, never crowded.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="page-section page-section--alt" id="advantage">
    <div class="container">
      <h2 class="section-title">The Shugia Advantage</h2>
      <p class="section-subtitle">We go above and beyond to make every moment seamless, safe, and deeply rewarding.</p>

      <div class="grid grid-3">
        <article class="advantage-card" style="background:var(--white);border-radius:var(--radius);padding:32px 24px;border:1px solid var(--border);box-shadow:var(--shadow-sm);">
          <div style="width:56px;height:56px;border-radius:10px;background:var(--primary);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:20px;">
            <i class="fas fa-route"></i>
          </div>
          <h3 style="font-size:1.25rem;margin-bottom:8px;">Tailor-Made Itineraries</h3>
          <p style="color:var(--gray-text);font-size:0.95rem;">Every trip is customized to your group size, budget, and dreams — from honeymoon romance to family bush safaris.</p>
        </article>

        <article class="advantage-card" style="background:var(--white);border-radius:var(--radius);padding:32px 24px;border:1px solid var(--border);box-shadow:var(--shadow-sm);">
          <div style="width:56px;height:56px;border-radius:10px;background:var(--primary);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:20px;">
            <i class="fas fa-truck-monster"></i>
          </div>
          <h3 style="font-size:1.25rem;margin-bottom:8px;">Custom 4x4 Land Cruisers</h3>
          <p style="color:var(--gray-text);font-size:0.95rem;">Navigate the bush comfortably with pop-up viewing roofs, charging points, and onboard cooler boxes.</p>
        </article>

        <article class="advantage-card" style="background:var(--white);border-radius:var(--radius);padding:32px 24px;border:1px solid var(--border);box-shadow:var(--shadow-sm);">
          <div style="width:56px;height:56px;border-radius:10px;background:var(--primary);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:20px;">
            <i class="fas fa-user-tie"></i>
          </div>
          <h3 style="font-size:1.25rem;margin-bottom:8px;">Certified Safari Guides</h3>
          <p style="color:var(--gray-text);font-size:0.95rem;">Our guides possess deep knowledge of animal tracking and environmental conservation.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="page-section" id="stats">
    <div class="container">
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;background:var(--primary);border-radius:var(--radius);padding:40px 24px;color:#fff;text-align:center;">
        <div>
          <div style="font-family:var(--font-heading);font-size:clamp(1.75rem,3vw,2.5rem);color:var(--accent);font-weight:700;line-height:1;">12+</div>
          <div style="font-size:0.9rem;opacity:0.8;margin-top:6px;">Years Experience</div>
        </div>
        <div>
          <div style="font-family:var(--font-heading);font-size:clamp(1.75rem,3vw,2.5rem);color:var(--accent);font-weight:700;line-height:1;">4,800+</div>
          <div style="font-size:0.9rem;opacity:0.8;margin-top:6px;">Satisfied Guests</div>
        </div>
        <div>
          <div style="font-family:var(--font-heading);font-size:clamp(1.75rem,3vw,2.5rem);color:var(--accent);font-weight:700;line-height:1;">100%</div>
          <div style="font-size:0.9rem;opacity:0.8;margin-top:6px;">Local Kenyan Guides</div>
        </div>
        <div>
          <div style="font-family:var(--font-heading);font-size:clamp(1.75rem,3vw,2.5rem);color:var(--accent);font-weight:700;line-height:1;">4.9/5</div>
          <div style="font-size:0.9rem;opacity:0.8;margin-top:6px;">Guest Review Rating</div>
        </div>
      </div>
    </div>
  </section>

  <section class="page-section page-section--alt">
    <div class="container">
      <div class="center-action" style="margin-top:0;">
        <a href="/contact#quote" class="btn btn-accent">Plan My Safari</a>
        <a href="/packages" class="btn btn-ghost" style="margin-left:12px;">Browse Packages</a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>