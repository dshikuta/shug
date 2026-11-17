<?php
require_once __DIR__ . '/auth.php';
require_admin();

$backups = [];
if (is_dir(BACKUP_DIR)) {
  foreach (glob(BACKUP_DIR . '/*.json') as $f) {
    $backups[] = ['file' => basename($f), 'size' => filesize($f), 'time' => filemtime($f)];
  }
  usort($backups, fn($a, $b) => $b['time'] - $a['time']);
}

// Manual backup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['backup'])) {
  if (!csrf_verify()) { csrf_fail(false); }
  if (!is_dir(BACKUP_DIR)) @mkdir(BACKUP_DIR, 0755, true);
  $stamp = date('Y-m-d-H-i-s');
  foreach (['packages','destinations','rates','reviews','settings','admin'] as $name) {
    $src = data_path($name);
    if (is_file($src)) @copy($src, BACKUP_DIR . '/' . $name . '-' . $stamp . '.json');
  }
  redirect('backup.php?done=1');
}

// Restore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['restore'])) {
  if (!csrf_verify()) { csrf_fail(false); }
  $file = basename($_POST['restore']);
  $src  = BACKUP_DIR . '/' . $file;
  if (is_file($src)) {
    $name = preg_replace('/-\d{4}-\d{2}-\d{2}.*\.json$/', '', $file);
    @copy($src, data_path($name));
    redirect('backup.php?restored=' . urlencode($name));
  }
  redirect('backup.php?error=1');
}

// Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete'])) {
  if (!csrf_verify()) { csrf_fail(false); }
  $file = basename($_POST['delete']);
  $src  = BACKUP_DIR . '/' . $file;
  if (is_file($src) && strpos($file, '..') === false) @unlink($src);
  redirect('backup.php?deleted=1');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Backups — Admin</title>
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body { background: var(--light-bg); padding: 32px; margin: 0; font-family: var(--font-body); }
    .wrap { max-width: 950px; margin: 0 auto; }
    table { width: 100%; background: var(--white); border-radius: var(--radius); border-collapse: collapse; overflow: hidden; box-shadow: var(--shadow-sm); }
    th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.88rem; }
    th { background: #f7f4ed; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-text); font-weight: 700; }
    tr:last-child td { border-bottom: none; }
    .flash { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
    .flash.ok { background: var(--success-bg); color: var(--success); }
    .flash.err { background: var(--error-bg); color: var(--error); }
    .btn-sm { padding: 6px 12px; font-size: 0.75rem; }
    .actions { display: flex; gap: 6px; flex-wrap: wrap; }
    .note { font-size: 0.85rem; color: var(--gray-text); margin: 16px 0 32px; line-height: 1.6; }
    .empty { background: var(--white); padding: 40px; border-radius: var(--radius); text-align: center; color: var(--gray-text); border: 1px dashed var(--border-strong); }
    h2 { margin: 32px 0 16px; font-size: 1.25rem; }
    form button { padding: 12px 24px; border-radius: var(--radius-full); background: var(--accent); border: none; font-weight: 700; cursor: pointer; font-size: 0.9rem; }
    form button:hover { background: var(--accent-hover); }
    .inline-form { display:inline; }
  </style>
</head>
<body>
  <div class="wrap">
    <p><a href="index.php">← Dashboard</a></p>
    <h1>Backups</h1>

    <?php if (isset($_GET['done'])):     ?><div class="flash ok">Manual backup created at <?= date('H:i') ?>.</div><?php endif; ?>
    <?php if (isset($_GET['restored'])): ?><div class="flash ok">Restored <strong><?= esc($_GET['restored']) ?>.json</strong> from backup.</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])):  ?><div class="flash ok">Backup file deleted.</div><?php endif; ?>
    <?php if (isset($_GET['error'])):    ?><div class="flash err">Restore failed — the backup file could not be found.</div><?php endif; ?>

    <form method="post">
      <?= csrf_field() ?>
      <button type="submit" name="backup"><i class="fas fa-download"></i> Create Manual Backup Now</button>
    </form>

    <p class="note">
      <strong>Automatic backups:</strong> Every time an admin saves a change on Packages, Destinations, Rates, or Reviews, a daily snapshot is created automatically in <code>data/backups/</code>. Only one snapshot per day per file.
    </p>

    <h2>Existing Backups (<?= count($backups) ?>)</h2>

    <?php if (empty($backups)): ?>
      <div class="empty">
        <i class="fas fa-archive" style="font-size:2rem;color:var(--border-strong);display:block;margin-bottom:12px;"></i>
        No backups yet. A daily backup is created automatically whenever you save a change.
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>File</th><th>Size</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($backups as $b): ?>
            <tr>
              <td><code><?= esc($b['file']) ?></code></td>
              <td><?= round($b['size'] / 1024, 1) ?> KB</td>
              <td><?= date('M j, Y H:i', $b['time']) ?></td>
              <td>
                <div class="actions">
                  <form method="post" class="inline-form" onsubmit="return confirm('Restore this backup? Current data will be overwritten.')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="restore" value="<?= esc($b['file']) ?>">
                    <button type="submit" class="btn btn-accent btn-sm">Restore</button>
                  </form>
                  <form method="post" class="inline-form" onsubmit="return confirm('Delete this backup file permanently?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="delete" value="<?= esc($b['file']) ?>">
                    <button type="submit" class="btn btn-ghost btn-sm">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>