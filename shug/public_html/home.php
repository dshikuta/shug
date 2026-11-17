<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Shugia Safaris — Authentic African Adventures';
$pageDesc  = 'Tailor-made African safaris across Kenya since 2010.';
$pageKey   = 'home';

$approved = get_reviews(true);
$stats    = get_reviews_stats();
$packages = read_json('packages', []);

include __DIR__ . '/includes/header.php';
?>

<section class="hero" aria-label="Hero">
  <div class="slider">
    <div class="slide active" style="background-image:url('/images/hero/wildbeast-migration.jpg');">
      <div class="slide-content">
        <h1 data-i18n="hero1_title">Explore Kenya's Wild Heart</h1>
        <p data-i18n="hero1_text">Unforgettable safaris, majestic landscapes, and authentic cultural encounters.</p>
        <a href="/contact#quote" class="btn btn-accent" data-i18n="plan_btn">Plan My Tour</a>
      </div>
    </div>
    <div class="slide" style="background-image:url('/images/hero/Mara-cheetahs.jpg');">
      <div class="slide-content">
        <h1 data-i18n="hero2_title">Serengeti & Maasai Mara</h1>
        <p data-i18n="hero2_text">Witness the Great Migration and luxury tented camps.</p>
        <a href="/contact#quote" class="btn btn-accent" data-i18n="plan_btn">Plan My Tour</a>
      </div>
    </div>
    <div class="slide" style="background-image:url('/images/hero/marine.jpg');">
      <div class="slide-content">
        <h1 data-i18n="hero3_title">Beach Safaris and Marine Life</h1>
        <p data-i18n="hero3_text">Discover the fascinating Marine Life.</p>
        <a href="/contact#quote" class="btn btn-accent" data-i18n="plan_btn">Plan My Tour</a>
      </div>
    </div>
    <div class="slide" style="background-image:url('/images/hero/zebra-sunset.jpg');">
      <div class="slide-content">
        <h1 data-i18n="hero4_title">Bird Watching, Nature Walks and Sundowners</h1>
        <p data-i18n="hero4_text">Slow down and enjoy the breathtaking beauty of nature.</p>
        <a href="/contact#quote" class="btn btn-accent" data-i18n="plan_btn">Plan My Tour</a>
      </div>
    </div>
    <div class="slide" style="background-image:url('/images/hero/fortjesus.jpg');">
      <div class="slide-content">
        <h1 data-i18n="hero5_title">Historical and Cultural Tours</h1>
        <p data-i18n="hero5_text">Uncover the stories behind every destination.</p>
        <a href="/contact#quote" class="btn btn-accent" data-i18n="plan_btn">Plan My Tour</a>
      </div>
    </div>
  </div>
  <button class="slider-btn prev" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
  <button class="slider-btn next" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
  <div class="slider-dots">
    <button class="slider-dot active" aria-label="Slide 1"></button>
    <button class="slider-dot" aria-label="Slide 2"></button>
    <button class="slider-dot" aria-label="Slide 3"></button>
    <button class="slider-dot" aria-label="Slide 4"></button>
    <button class="slider-dot" aria-label="Slide 5"></button>
  </div>
</section>

<main>
  <section class="page-section">
    <div class="container">
      <h2 class="section-title" data-i18n="home_title">Welcome to Shugia Safaris</h2>
      <p class="section-subtitle" data-i18n="home_sub">Your gateway to authentic African adventures. Tailor-made journeys since 2010.</p>
      <div class="grid grid-3">
        <article class="card">
          <i class="fas fa-map-marked-alt"></i>
          <h3 data-i18n="home_card1_title">Expert Local Guides</h3>
          <p data-i18n="home_card1_text">Born and raised in the wild — we know every hidden gem.</p>
        </article>
        <article class="card">
          <i class="fas fa-leaf"></i>
          <h3 data-i18n="home_card2_title">Eco-Friendly Travel</h3>
          <p data-i18n="home_card2_text">We support conservation and community projects.</p>
        </article>
        <article class="card">
          <i class="fas fa-headset"></i>
          <h3 data-i18n="home_card3_title">24/7 Support</h3>
          <p data-i18n="home_card3_text">Always here for you before, during and after your trip.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- Packages preview -->
  <section class="page-section page-section--alt">
    <div class="container">
      <h2 class="section-title" data-i18n="pack_title">Popular Packages</h2>
      <p class="section-subtitle" data-i18n="pack_sub">All-inclusive safaris designed for every budget.</p>
      <div class="grid grid-3">
        <?php foreach (array_slice($packages, 0, 3) as $pkg): ?>
          <article class="package-card">
            <div class="package-card__img">
              <img src="/<?= esc($pkg['image']) ?>" alt="<?= esc($pkg['title']) ?>" loading="lazy">
              <span class="package-badge"><?= esc($pkg['badge']) ?></span>
            </div>
            <div class="package-card__body">
              <h3><?= esc($pkg['title']) ?></h3>
              <p><?= esc($pkg['summary']) ?></p>
              <div class="package-card__footer">
                <span class="price" data-price-usd="<?= (int)$pkg['priceUsd'] ?>">$<?= number_format((int)$pkg['priceUsd']) ?></span>
                <a href="/packages" class="btn btn-accent btn-sm">View Package</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="center-action">
        <a href="/packages" class="btn" data-i18n="see_all_dest">See All Packages</a>
      </div>
    </div>
  </section>

  <!-- Reviews -->
  <section class="page-section" id="reviews">
    <div class="container">

      <?php if ($stats['approved'] > 0): ?>
        <div class="reviews-hero">
          <div class="reviews-hero__score">
            <strong><?= number_format($stats['average'], 1) ?></strong>
            <div class="reviews-hero__stars">
              <?php $full = (int)round($stats['average']); for ($i=1;$i<=5;$i++) echo $i<=$full?'★':'☆'; ?>
            </div>
            <div class="reviews-hero__count">
              <?= $stats['approved'] ?> review<?= $stats['approved']===1?'':'s' ?>
            </div>
          </div>
          <div class="reviews-hero__copy">
            <h3 data-i18n="testimonials_title">What Our Travelers Say</h3>
            <p data-i18n="testimonials_sub">Real stories from guests who travelled with Shugia Safaris.</p>
            <a href="#reviewForm" class="reviews-hero__cta">
              <i class="fas fa-pen-fancy"></i> Share your experience
            </a>
          </div>
        </div>
      <?php else: ?>
        <h2 class="section-title" data-i18n="testimonials_title">What Our Travelers Say</h2>
        <p class="section-subtitle" data-i18n="testimonials_sub">Real stories from guests who travelled with Shugia Safaris.</p>
      <?php endif; ?>

      <?php if (empty($approved)): ?>
        <div class="reviews-empty">
          <i class="fas fa-quote-left"></i>
          <h3>No reviews yet</h3>
          <p>Be the first to share your experience.</p>
        </div>
      <?php else: ?>
        <div class="review-grid">
          <?php foreach ($approved as $r): ?>
            <article class="review-card">
              <span class="review-card__verified"><i class="fas fa-circle-check"></i> Verified</span>
              <div class="review-card__head">
                <div class="testimonial-avatar"><?= esc(strtoupper(mb_substr($r['name'], 0, 2))) ?></div>
                <div>
                  <strong><?= esc($r['name']) ?></strong>
                  <?php if (!empty($r['location'])): ?>
                    <span class="review-card__loc"><?= esc($r['location']) ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="review-card__stars" aria-label="<?= (int)$r['rating'] ?> stars">
                <?= str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5-(int)$r['rating']) ?>
              </div>
              <p class="review-card__text"><?= nl2br(esc($r['text'])) ?></p>
              <div class="review-card__meta">
                <?php if (!empty($r['tour'])): ?>
                  <span class="review-card__tour"><i class="fas fa-suitcase"></i> <?= esc($r['tour']) ?></span>
                <?php endif; ?>
                <span class="review-card__date"><?= date('M j, Y', strtotime($r['createdAt'] ?? 'now')) ?></span>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="review-form-wrap" id="reviewForm">
        <h3 class="review-form__title"><i class="fas fa-pen-fancy"></i> Share your experience</h3>
        <p class="review-form__sub">Your review appears after a quick moderation check.</p>
        <form id="reviewFormEl" class="review-form" novalidate>
          <input type="text" name="website" tabindex="-1" style="position:absolute;left:-9999px" aria-hidden="true">
          <div class="form-row">
            <div class="form-group">
              <label for="revName">Your name <span class="required">*</span></label>
              <input type="text" id="revName" name="name" maxlength="80" required>
            </div>
            <div class="form-group">
              <label for="revEmail">Email (never published) <span class="required">*</span></label>
              <input type="email" id="revEmail" name="email" maxlength="120" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="revLocation">Location</label>
              <input type="text" id="revLocation" name="location" maxlength="80">
            </div>
            <div class="form-group">
              <label for="revTour">Which safari did you take?</label>
              <select id="revTour" name="tour">
                <option value="">— Select a package —</option>
                <?php foreach ($packages as $pkg): ?>
                  <option value="<?= esc($pkg['title']) ?>"><?= esc($pkg['title']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group form-group--full">
            <label>Your rating <span class="required">*</span></label>
            <div class="star-rating">
              <?php for ($i=1;$i<=5;$i++): ?>
                <button type="button" class="star-rating__star" data-value="<?= $i ?>"><i class="fas fa-star"></i></button>
              <?php endfor; ?>
              <input type="hidden" name="rating" id="revRating" value="0">
            </div>
          </div>
          <div class="form-group form-group--full">
            <label for="revText">Your review <span class="required">*</span></label>
            <textarea id="revText" name="text" rows="5" maxlength="800" required></textarea>
            <small class="review-form__count"><span id="revCount">0</span> / 800</small>
          </div>
          <div class="quote-form__actions">
            <button type="submit" id="revSubmit" class="btn btn-accent">
              <i class="fas fa-paper-plane"></i> <span>Post Review</span>
            </button>
          </div>
          <p class="form-feedback" role="status" aria-live="polite"></p>
        </form>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="/js/slider.js"></script>
<script src="/js/reviews.js"></script>
</body>
</html>