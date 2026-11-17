<?php
require_once __DIR__ . '/auth.php';
require_admin();

$destinations = read_json('destinations', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
  if (!csrf_verify()) { csrf_fail(false); }
  $id = $_POST['id'] ?? '';
  $dest = [
    'id'        => $id ?: uniqid('dest_'),
    'title'     => clean_text($_POST['title'] ?? '', 120),
    'tag'       => clean_text($_POST['tag'] ?? '', 40),
    'text'      => clean_text($_POST['text'] ?? '', 400),
    'priceUsd'  => max(0, (int)($_POST['priceUsd'] ?? 0)),
    'image'     => clean_text($_POST['image'] ?? '', 200),
    'link'      => clean_text($_POST['link'] ?? 'packages', 40),
    'category'  => clean_text($_POST['category'] ?? 'wildlife', 40),
    'updatedAt' => date('c'),
  ];
  if ($id) { foreach ($destinations as &$d) if ($d['id'] === $id) { $d = $dest; break; } unset($d); }
  else { $destinations[] = $dest; }
  write_json('destinations', $destinations);
  redirect('destinations.php?saved=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_id'])) {
  if (!csrf_verify()) { csrf_fail(false); }
  $destinations = array_values(array_filter($destinations, fn($d) => $d['id'] !== $_POST['delete_id']));
  write_json('destinations', $destinations);
  redirect('destinations.php?deleted=1');
}

$editing = null;
if (!empty($_GET['edit'])) {
  foreach ($destinations as $d) if ($d['id'] === $_GET['edit']) { $editing = $d; break; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Destinations — Admin</title>
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
    <h1>Destinations</h1>
    <?php if (isset($_GET['saved'])):   ?><div class="flash">Saved.</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="flash">Deleted.</div><?php endif; ?>

    <form method="post" class="panel">
      <?= csrf_field() ?>
      <h2><?= $editing ? 'Edit Destination' : 'Add New Destination' ?></h2>
      <input type="hidden" name="id" value="<?= esc($editing['id'] ?? '') ?>">

      <div class="row">
        <div><label>Title</label><input name="title" required value="<?= esc($editing['title'] ?? '') ?>"></div>
        <div><label>Tag</label><input name="tag" value="<?= esc($editing['tag'] ?? '') ?>"></div>
      </div>

      <div class="row">
        <div><label>Price (USD)</label><input type="number" name="priceUsd" min="0" value="<?= (int)($editing['priceUsd'] ?? 0) ?>"></div>
        <div><label>Category</label>
          <select name="category">
            <?php foreach (['wildlife','beach','marine','birding','adventure','cultural'] as $c): ?>
              <option value="<?= $c ?>" <?= ($editing['category'] ?? '') === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="row">
        <div><label>Image path</label><input name="image" placeholder="images/hero/amboseli.jpg" value="<?= esc($editing['image'] ?? '') ?>"></div>
        <div><label>Link</label>
          <select name="link">
            <?php foreach (['packages','destinations','contact','calculator'] as $l): ?>
              <option value="<?= $l ?>" <?= ($editing['link'] ?? 'packages') === $l ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div style="margin-bottom:16px;"><label>Description</label><textarea name="text" rows="3"><?= esc($editing['text'] ?? '') ?></textarea></div>

      <div style="display:flex;gap:8px;">
        <button type="submit" name="save" class="btn btn-accent"><?= $editing ? 'Update' : 'Create' ?></button>
        <?php if ($editing): ?><a href="destinations.php" class="btn btn-ghost">Cancel</a><?php endif; ?>
      </div>
    </form>

    <h2>All Destinations (<?= count($destinations) ?>)</h2>
    <div class="list">
      <?php foreach ($destinations as $d): ?>
        <div class="item">
          <img src="/<?= esc($d['image']) ?>" alt="" onerror="this.removeAttribute('src')">
          <div class="item-body">
            <strong><?= esc($d['title']) ?></strong>
            <span><?= esc($d['tag']) ?> · $<?= (int)$d['priceUsd'] ?></span>
          </div>
          <a href="?edit=<?= urlencode($d['id']) ?>" class="btn btn-ghost btn-sm">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete?')">
            <?= csrf_field() ?>
            <input type="hidden" name="delete_id" value="<?= esc($d['id']) ?>">
            <button type="submit" class="btn btn-ghost btn-sm">Delete</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>