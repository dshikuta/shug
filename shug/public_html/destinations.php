<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Shugia Safaris — Destinations';
$pageDesc  = 'Explore iconic Kenyan destinations.';
$pageKey   = 'destinations';
$destinations = read_json('destinations', []);
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
  <div class="container">
    <a href="/home" data-i18n="nav_home">Home</a> <span>/</span>
    <strong data-i18n="nav_dest">Destinations</strong>
  </div>
</div>

<main>
  <section class="page-section">
    <div class="container">
      <h2 class="section-title" data-i18n="dest_title">Top Destinations</h2>
      <p class="section-subtitle" data-i18n="dest_sub">Explore Kenya's most iconic parks and reserves.</p>
      <div class="grid grid-3">
        <?php if (empty($destinations)): ?>
          <div class="pkg-empty" style="grid-column:1/-1;">
            <i class="fas fa-map-location-dot"></i>
            <h3>Destinations coming soon</h3>
            <p>Check back shortly — we're adding new parks.</p>
          </div>
        <?php else: ?>
          <?php foreach ($destinations as $d): ?>
            <a href="/<?= esc($d['link'] ?? 'packages') ?>" class="dest-card" id="<?= esc($d['id']) ?>">
              <img src="/<?= esc($d['image']) ?>" alt="<?= esc($d['title']) ?>" loading="lazy">
              <div class="dest-info">
                <span class="dest-card__tag"><?= esc($d['tag']) ?></span>
                <h3><?= esc($d['title']) ?></h3>
                <p><?= esc($d['text']) ?></p>
                <span class="price" data-price-usd="<?= (int)$d['priceUsd'] ?>">$<?= number_format((int)$d['priceUsd']) ?></span>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>