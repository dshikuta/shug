<?php
require_once __DIR__ . '/auth.php';
require_admin();

$rates = read_json('rates', []);
if (empty($rates['packages'])) $rates['packages'] = [];
if (empty($rates['addons']))   $rates['addons']   = [];

$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify()) { csrf_fail(false); }
  foreach (['packages','addons'] as $group) {
    foreach ($rates[$group] as $key => $entry) {
      $rates[$group][$key]['usd'] = max(0, (int)($_POST[$group][$key] ?? $entry['usd']));
      if (!empty($_POST[$group . '_label'][$key])) {
        $rates[$group][$key]['label'] = clean_text($_POST[$group . '_label'][$key], 120);
      }
    }
  }
  write_json('rates', $rates);
  $saved = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Calculator Rates — Admin</title>
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
  <link rel="stylesheet" href="/css/style.css">
  <style>
    body{background:var(--light-bg);padding:32px;margin:0;font-family:var(--font-body);}
    .wrap{max-width:800px;margin:0 auto;}
    .row{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:12px;background:var(--white);padding:12px 16px;border-radius:6px;border:1px solid var(--border);align-items:center;}
    .row input{padding:8px;border:1px solid var(--border);border-radius:4px;font-size:0.9rem;box-sizing:border-box;width:100%;}
    h2{margin:32px 0 16px;font-size:1.25rem;}
    .flash{background:var(--success-bg);color:var(--success);padding:12px 16px;border-radius:6px;margin-bottom:16px;}
  </style>
</head>
<body>
  <div class="wrap">
    <p><a href="index.php">← Dashboard</a></p>
    <h1>Calculator Rates</h1>
    <?php if ($saved): ?><div class="flash">Saved.</div><?php endif; ?>
    <form method="post">
      <?= csrf_field() ?>
      <h2>Package Rates (USD per person / day)</h2>
      <?php foreach ($rates['packages'] as $key => $r): ?>
        <div class="row">
          <input name="packages_label[<?= esc($key) ?>]" value="<?= esc($r['label']) ?>">
          <input type="number" name="packages[<?= esc($key) ?>]" value="<?= (int)$r['usd'] ?>" min="0">
        </div>
      <?php endforeach; ?>

      <h2>Add-ons (USD per person)</h2>
      <?php foreach ($rates['addons'] as $key => $r): ?>
        <div class="row">
          <input name="addons_label[<?= esc($key) ?>]" value="<?= esc($r['label']) ?>">
          <input type="number" name="addons[<?= esc($key) ?>]" value="<?= (int)$r['usd'] ?>" min="0">
        </div>
      <?php endforeach; ?>

      <button type="submit" class="btn btn-accent" style="margin-top:16px;">Save Rates</button>
    </form>
  </div>
</body>
</html>