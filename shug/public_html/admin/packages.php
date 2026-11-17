<?php
require_once __DIR__ . '/auth.php';
require_admin();

$packages = read_json('packages', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
  if (!csrf_verify()) { csrf_fail(false); }

  $id   = $_POST['id'] ?? '';
  $days = [];
  if (!empty($_POST['days']) && is_array($_POST['days'])) {
    foreach ($_POST['days'] as $d) {
      if (!empty($d['label']) || !empty($d['text'])) {
        $days[] = ['d' => clean_text($d['label'] ?? ''), 't' => clean_text($d['text'] ?? '')];
      }
    }
  }
  $pkg = [
    'id'        => $id ?: uniqid('pkg_'),
    'title'     => clean_text($_POST['title'] ?? '', 120),
    'category'  => clean_text($_POST['category'] ?? 'big5', 30),
    'duration'  => max(1, (int)($_POST['duration'] ?? 1)),
    'nights'    => max(0, (int)($_POST['nights'] ?? 0)),
    'priceUsd'  => max(0, (int)($_POST['priceUsd'] ?? 0)),
    'image'     => clean_text($_POST['image'] ?? '', 200),
    'badge'     => clean_text($_POST['badge'] ?? '', 40),
    'summary'   => clean_text($_POST['summary'] ?? '', 400),
    'days'      => $days,
    'updatedAt' => date('c'),
  ];
  if ($id) { foreach ($packages as &$p) if ($p['id'] === $id) { $p = $pkg; break; } unset($p); }
  else { $packages[] = $pkg; }
  write_json('packages', $packages);
  redirect('packages.php?saved=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_id'])) {
  if (!csrf_verify()) { csrf_fail(false); }
  $packages = array_values(array_filter($packages, fn($p) => $p['id'] !== $_POST['delete_id']));
  write_json('packages', $packages);
  redirect('packages.php?deleted=1');
}

$editing = null;
if (!empty($_GET['edit'])) {
  foreach ($packages as $p) if ($p['id'] === $_GET['edit']) { $editing = $p; break; }
}

$days        = $editing['days'] ?? [['d'=>'Day 1','t'=>''],['d'=>'Day 2','t'=>''],['d'=>'Day 3','t'=>'']];
$dayIndex    = count($days);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Packages — Admin</title>
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body{background:var(--light-bg);padding:32px;margin:0;font-family:var(--font-body);}
    .wrap{max-width:1100px;margin:0 auto;}
    .panel{background:var(--white);padding:24px;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:32px;}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;}
    .panel label{display:block;font-size:0.75rem;text-transform:uppercase;color:var(--gray-text);font-weight:600;margin-bottom:4px;}
    .panel input,.panel textarea,.panel select{width:100%;padding:10px;border:1px solid var(--border);border-radius:4px;box-sizing:border-box;font-size:0.9rem;}
    .list{display:grid;gap:16px;}
    .item{background:var(--white);border-radius:var(--radius);padding:16px;border:1px solid var(--border);display:flex;gap:16px;align-items:center;}
    .item img{width:80px;height:60px;object-fit:cover;border-radius:4px;background:var(--primary);}
    .item-body{flex:1;}
    .item-body strong{display:block;color:var(--dark-text);}
    .item-body span{font-size:0.75rem;color:var(--gray-text);}
    .flash{background:var(--success-bg);color:var(--success);padding:12px 16px;border-radius:6px;margin-bottom:16px;}
    .inline-form{display:inline;}
  </style>
</head>
<body>
  <div class="wrap">
    <p><a href="index.php">← Dashboard</a></p>
    <h1>Packages</h1>
    <?php if (isset($_GET['saved'])):   ?><div class="flash">Saved.</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="flash">Deleted.</div><?php endif; ?>

    <form method="post" class="panel">
      <?= csrf_field() ?>
      <h2><?= $editing ? 'Edit Package' : 'Add New Package' ?></h2>
      <input type="hidden" name="id" value="<?= esc($editing['id'] ?? '') ?>">

      <div class="row">
        <div><label>Title</label><input name="title" required value="<?= esc($editing['title'] ?? '') ?>"></div>
        <div><label>Category</label>
          <select name="category">
            <?php foreach (['big5','combo','marine','birding','beach','adventure','luxury'] as $c): ?>
              <option value="<?= $c ?>" <?= ($editing['category'] ?? '') === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="row">
        <div><label>Duration (days)</label><input type="number" name="duration" min="1" value="<?= (int)($editing['duration'] ?? 3) ?>"></div>
        <div><label>Nights</label><input type="number" name="nights" min="0" value="<?= (int)($editing['nights'] ?? 2) ?>"></div>
      </div>

      <div class="row">
        <div><label>Price (USD)</label><input type="number" name="priceUsd" min="0" value="<?= (int)($editing['priceUsd'] ?? 1000) ?>"></div>
        <div><label>Badge</label><input name="badge" value="<?= esc($editing['badge'] ?? 'Big Five') ?>"></div>
      </div>

      <div class="row">
        <div><label>Image path</label><input name="image" placeholder="images/hero/amboseli.jpg" value="<?= esc($editing['image'] ?? '') ?>"></div>
      </div>

      <div style="margin-bottom:16px;"><label>Summary</label><textarea name="summary" rows="2"><?= esc($editing['summary'] ?? '') ?></textarea></div>

      <h3>Itinerary</h3>
      <div id="daysBlock">
        <?php foreach ($days as $i => $d): ?>
        <div class="row">
          <input name="days[<?= $i ?>][label]" value="<?= esc($d['d']) ?>" placeholder="Day 1">
          <input name="days[<?= $i ?>][text]" value="<?= esc($d['t']) ?>" placeholder="Activity">
        </div>
        <?php endforeach; ?>
      </div>
      <button type="button" onclick="addDay()" class="btn btn-ghost btn-sm" style="margin-bottom:16px;">+ Add Day</button>

      <div style="display:flex;gap:8px;">
        <button type="submit" name="save" class="btn btn-accent"><?= $editing ? 'Update' : 'Create' ?></button>
        <?php if ($editing): ?><a href="packages.php" class="btn btn-ghost">Cancel</a><?php endif; ?>
      </div>
    </form>

    <h2>All Packages (<?= count($packages) ?>)</h2>
    <div class="list">
      <?php foreach ($packages as $p): ?>
        <div class="item">
          <img src="/<?= esc($p['image']) ?>" alt="" onerror="this.removeAttribute('src')">
          <div class="item-body">
            <strong><?= esc($p['title']) ?></strong>
            <span><?= ucfirst($p['category']) ?> · <?= (int)$p['duration'] ?> days · $<?= (int)$p['priceUsd'] ?></span>
          </div>
          <a href="?edit=<?= urlencode($p['id']) ?>" class="btn btn-ghost btn-sm">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete?')">
            <?= csrf_field() ?>
            <input type="hidden" name="delete_id" value="<?= esc($p['id']) ?>">
            <button type="submit" class="btn btn-ghost btn-sm">Delete</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <script>
    let dayIndex = <?= (int)$dayIndex ?>;
    function addDay() {
      const row = document.createElement('div');
      row.className = 'row';
      row.innerHTML = `<input name="days[${dayIndex}][label]" placeholder="Day ${dayIndex+1}"><input name="days[${dayIndex}][text]" placeholder="Activity">`;
      document.getElementById('daysBlock').appendChild(row);
      dayIndex++;
    }
  </script>
</body>
</html>