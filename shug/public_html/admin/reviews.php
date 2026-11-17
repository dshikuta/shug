<?php
require_once __DIR__ . '/auth.php';
require_admin();

$reviews = read_json('reviews', []);

// Delete via POST (was GET before — CSRF-safe now)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_id'])) {
  if (!csrf_verify()) { csrf_fail(false); }
  $reviews = array_values(array_filter($reviews, fn($r) => $r['id'] !== $_POST['delete_id']));
  write_json('reviews', $reviews);
  redirect('reviews.php?deleted=1');
}

// Approve / unpublish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['approve_id'])) {
  if (!csrf_verify()) { csrf_fail(false); }
  foreach ($reviews as &$r) {
    if ($r['id'] === $_POST['approve_id']) $r['approved'] = !empty($_POST['approved']);
  }
  unset($r);
  write_json('reviews', $reviews);
  redirect('reviews.php?saved=1');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reviews — Admin</title>
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body{background:var(--light-bg);padding:32px;margin:0;font-family:var(--font-body);}
    .wrap{max-width:1100px;margin:0 auto;}
    .row{background:var(--white);border-radius:var(--radius);padding:20px;margin-bottom:16px;border:1px solid var(--border);}
    .row.is-pending{border-left:4px solid var(--accent);}
    .row header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:12px;flex-wrap:wrap;}
    .row strong{color:var(--dark-text);}
    .row .stars{color:var(--accent);}
    .row p{font-size:0.9rem;color:var(--dark-text);margin:8px 0 16px;line-height:1.6;}
    .row .actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
    .flash{background:var(--success-bg);color:var(--success);padding:12px 16px;border-radius:6px;margin-bottom:16px;}
    .btn-sm{padding:6px 14px;font-size:0.78rem;}
  </style>
</head>
<body>
  <div class="wrap">
    <p><a href="index.php">← Dashboard</a></p>
    <h1>Reviews (<?= count($reviews) ?>)</h1>
    <?php if (isset($_GET['saved'])):   ?><div class="flash">Saved.</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="flash">Deleted.</div><?php endif; ?>

    <?php if (empty($reviews)): ?>
      <p>No reviews yet.</p>
    <?php else: foreach ($reviews as $r): ?>
      <div class="row <?= empty($r['approved']) ? 'is-pending' : '' ?>">
        <header>
          <div>
            <strong><?= esc($r['name']) ?></strong>
            — <span class="stars"><?= str_repeat('★', (int)$r['rating']) ?></span>
            <?php if (!empty($r['location'])): ?> · <span style="color:var(--gray-text)"><?= esc($r['location']) ?></span><?php endif; ?>
          </div>
          <span style="font-size:0.75rem;color:var(--gray-text)">
            <?= date('M j, Y', strtotime($r['createdAt'] ?? 'now')) ?> · <?= empty($r['approved']) ? 'Pending' : 'Approved' ?>
          </span>
        </header>
        <p><?= nl2br(esc($r['text'])) ?></p>
        <div class="actions">
          <form method="post" style="display:inline;">
            <?= csrf_field() ?>
            <input type="hidden" name="approve_id" value="<?= esc($r['id']) ?>">
            <input type="hidden" name="approved" value="<?= empty($r['approved']) ? '1' : '0' ?>">
            <button type="submit" class="btn btn-accent btn-sm">
              <?= empty($r['approved']) ? '✓ Approve' : '✗ Unpublish' ?>
            </button>
          </form>
          <form method="post" style="display:inline;" onsubmit="return confirm('Delete permanently?')">
            <?= csrf_field() ?>
            <input type="hidden" name="delete_id" value="<?= esc($r['id']) ?>">
            <button type="submit" class="btn btn-ghost btn-sm">Delete</button>
          </form>
          <?php if (!empty($r['tour'])): ?>
            <span style="font-size:0.75rem;color:var(--gray-text);"><i class="fas fa-suitcase"></i> <?= esc($r['tour']) ?></span>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</body>
</html>