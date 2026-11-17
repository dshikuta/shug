<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Shugia Safaris — Trip Calculator';
$pageDesc  = 'Build and price your custom Kenyan safari instantly.';
$pageKey   = 'calculator';
$rates = read_json('rates', []);
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
  <div class="container">
    <a href="/home" data-i18n="nav_home">Home</a> <span>/</span>
    <strong data-i18n="nav_calc">Trip Calculator</strong>
  </div>
</div>

<main>
  <section class="page-section">
    <div class="container">
      <h2 class="section-title" data-i18n="calc_title">Trip Calculator</h2>
      <p class="section-subtitle" data-i18n="calc_sub">Choose your dates, travelers, and package — we'll estimate your total.</p>

      <div class="calculator-box">
        <div class="calc-row">
          <div class="calc-group">
            <label for="calcStartDate">Start Date</label>
            <input type="text" id="calcStartDate" data-datepicker required>
          </div>
          <div class="calc-group">
            <label for="calcEndDate">End Date</label>
            <input type="text" id="calcEndDate" data-datepicker required>
          </div>
        </div>
        <div class="calc-row">
          <div class="calc-group">
            <label for="calcPeople">Travelers</label>
            <div class="number-stepper">
              <button type="button" id="calcPeopleMinus"><i class="fas fa-minus"></i></button>
              <input type="number" id="calcPeople" value="2" min="1" max="20">
              <button type="button" id="calcPeoplePlus"><i class="fas fa-plus"></i></button>
            </div>
          </div>
          <div class="calc-group">
            <label for="calcPackage">Package</label>
            <select id="calcPackage">
              <?php foreach (($rates['packages'] ?? []) as $key => $r): ?>
                <option value="<?= (int)$r['usd'] ?>"><?= esc($r['label']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="calc-addons">
          <?php foreach (($rates['addons'] ?? []) as $key => $r): ?>
            <label>
              <input type="checkbox" name="addon" value="<?= esc($key) ?>" data-price="<?= (int)$r['usd'] ?>">
              <span><?= esc($r['label']) ?></span>
              <small>(+$<?= number_format((int)$r['usd']) ?> pp)</small>
            </label>
          <?php endforeach; ?>
        </div>

        <div class="calc-result">
          <span class="calc-label"><span>Estimated total</span><span id="calcDays"></span></span>
          <span id="calcResult">$ 0</span>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:20px;">
          <button type="button" class="btn btn-accent" id="calcBtn">Update Estimate</button>
          <a href="/contact#quote" class="btn">Request Exact Quote</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="/js/calculator.js"></script>
</body>
</html>