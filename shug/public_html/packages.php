<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Shugia Safaris — Safari Packages';
$pageDesc  = "Browse Shugia Safaris' signature tour packages.";
$pageKey   = 'packages';
$packages  = read_json('packages', []);
$initial   = $_GET['filter'] ?? 'all';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
  <div class="container">
    <a href="/home" data-i18n="nav_home">Home</a> <span>/</span>
    <strong data-i18n="nav_packages">Packages</strong>
  </div>
</div>

<main>
  <section class="page-section">
    <div class="container">
      <h2 class="section-title" data-i18n="pack_title">Popular Packages</h2>
      <p class="section-subtitle" data-i18n="pack_sub">All-inclusive safaris designed for every budget.</p>

      <div class="pkg-controls">
        <div class="pkg-tabs" id="categoryTabs">
          <?php
          $tabs = [
            'all'=>'All Packages','big5'=>'Big 5 Safari','birding'=>'Bird Watching',
            'marine'=>'Marine Parks','combo'=>'Bush & Beach','beach'=>'Beach Escapes',
            'adventure'=>'Safari Walks','luxury'=>'Luxury'
          ];
          foreach ($tabs as $key=>$label):
            $active = ($initial === $key) ? ' is-active' : '';
          ?>
            <button type="button" class="pkg-tab-btn<?= $active ?>" data-filter="<?= $key ?>"><?= esc($label) ?></button>
          <?php endforeach; ?>
        </div>
        <div class="pkg-search">
          <i class="fas fa-magnifying-glass"></i>
          <input type="search" id="pkgSearch" placeholder="Search packages or parks..." aria-label="Search packages">
        </div>
      </div>

      <div class="grid grid-3 packages-grid" id="packagesGrid">
        <?php foreach ($packages as $pkg): ?>
          <article class="package-card"
                   data-id="<?= esc($pkg['id']) ?>"
                   data-category="<?= esc($pkg['category']) ?>"
                   data-search="<?= esc(strtolower($pkg['title'].' '.$pkg['summary'].' '.$pkg['badge'])) ?>">
            <div class="package-card__img">
              <img src="/<?= esc($pkg['image']) ?>" alt="<?= esc($pkg['title']) ?>" loading="lazy">
              <span class="package-badge"><?= esc($pkg['badge']) ?></span>
            </div>
            <div class="package-card__body">
              <div class="package-card__meta">
                <span><i class="fas fa-clock"></i> <?= (int)$pkg['duration'] ?> Days</span>
                <span><i class="fas fa-moon"></i> <?= (int)$pkg['nights'] ?> Nights</span>
              </div>
              <h3><?= esc($pkg['title']) ?></h3>
              <p><?= esc($pkg['summary']) ?></p>
              <div class="package-card__footer">
                <span class="price" data-price-usd="<?= (int)$pkg['priceUsd'] ?>">$<?= number_format((int)$pkg['priceUsd']) ?></span>
                <button type="button" class="btn btn-accent btn-sm" data-view-package="<?= esc($pkg['id']) ?>">View Package</button>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<!-- Itinerary modal -->
<div id="itineraryModal" class="itinerary-modal" hidden aria-hidden="true" role="dialog">
  <div class="itinerary-modal__backdrop" data-close-modal></div>
  <div class="itinerary-modal__panel">
    <div class="itinerary-modal__head">
      <div>
        <span id="modalBadge" class="package-badge">Package</span>
        <h3 id="modalTitle"></h3>
        <p id="modalDuration" class="itinerary-modal__duration"></p>
      </div>
      <button type="button" class="itinerary-modal__close" data-close-modal aria-label="Close"><i class="fas fa-xmark"></i></button>
    </div>
    <div class="itinerary-modal__body">
      <div class="itinerary-modal__img">
        <img id="modalImg" src="" alt="">
        <div class="itinerary-modal__price">
          <span>From</span>
          <strong id="modalPrice" data-price-usd="0">$0</strong>
          <span>/ guest</span>
        </div>
      </div>
      <div><h4 class="itinerary-modal__label">Tour Overview</h4><p id="modalDesc"></p></div>
      <div><h4 class="itinerary-modal__label">Day-by-Day Itinerary</h4><div id="modalDaysList" class="itinerary-modal__days"></div></div>
    </div>
    <div class="itinerary-modal__foot">
      <span class="itinerary-modal__note"><i class="fas fa-shield-halved"></i> Guaranteed Best Rate</span>
      <button type="button" id="modalBookBtn" class="btn btn-accent">Inquire About This Tour</button>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
window.SHUGIA_PACKAGES = <?= json_encode($packages, JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="/js/package.js"></script>
</body>
</html>