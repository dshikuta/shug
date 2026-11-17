<?php
require_once __DIR__ . '/auth.php';
require_admin();

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify()) {
    $err = 'Security token expired. Please try again.';
  } else {
    $current = $_POST['current'] ?? '';
    $new     = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!verify_admin_password($current)) {
      $err = 'Current password is incorrect.';
    } elseif (strlen($new) < 8) {
      $err = 'New password must be at least 8 characters.';
    } elseif ($new !== $confirm) {
      $err = 'New passwords do not match.';
    } else {
      if (set_admin_password($new)) {
        $msg = 'Password updated successfully.';
        // Regenerate CSRF token on password change
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
      } else {
        $err = 'Could not save the new password. Check that data/ is writable.';
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password — Admin</title>
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body { background: var(--light-bg); padding: 32px; margin: 0; font-family: var(--font-body); }
    .wrap { max-width: 480px; margin: 0 auto; }
    .card { background: var(--white); padding: 32px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
    .card label { display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-text); margin-bottom: 6px; font-weight: 600; }
    .card input { width: 100%; padding: 12px; border: 1px solid var(--border-strong); border-radius: 6px; margin-bottom: 16px; box-sizing: border-box; font-size: 0.95rem; }
    .card button { padding: 12px 24px; border-radius: var(--radius-full); background: var(--accent); border: none; font-weight: 700; cursor: pointer; font-size: 0.95rem; }
    .card button:hover { background: var(--accent-hover); }
    .msg { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem; }
    .msg.ok { background: var(--success-bg); color: var(--success); }
    .msg.err { background: var(--error-bg); color: var(--error); }
    .hint { font-size: 0.8rem; color: var(--gray-text); margin-top: -8px; margin-bottom: 16px; }
  </style>
</head>
<body>
  <div class="wrap">
    <p><a href="index.php">← Dashboard</a></p>
    <h1>Change Password</h1>

    <?php if ($msg): ?><div class="msg ok"><?= esc($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="msg err"><?= esc($err) ?></div><?php endif; ?>

    <form method="post" class="card">
      <?= csrf_field() ?>
      <label>Current Password</label>
      <input type="password" name="current" required autocomplete="current-password">

      <label>New Password</label>
      <input type="password" name="new" required minlength="8" autocomplete="new-password">
      <p class="hint">Minimum 8 characters. Use a mix of letters, numbers, and symbols.</p>

      <label>Confirm New Password</label>
      <input type="password" name="confirm" required minlength="8" autocomplete="new-password">

      <button type="submit">Update Password</button>
    </form>
  </div>
</body>
</html>