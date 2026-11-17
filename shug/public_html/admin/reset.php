<?php
require_once __DIR__ . '/auth.php';

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$valid = verify_reset_token($token);
$error = '';
$done  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify()) {
    $error = 'Security token expired. Please try again.';
  } elseif (!verify_reset_token($token)) {
    $error = 'This reset link is invalid or has expired.';
  } else {
    $p1 = $_POST['password']  ?? '';
    $p2 = $_POST['password2'] ?? '';

    if (strlen($p1) < 8)         $error = 'Password must be at least 8 characters.';
    elseif ($p1 !== $p2)         $error = 'Passwords do not match.';
    elseif (!set_admin_password($p1)) $error = 'Could not save the new password. Check data/ permissions.';
    else {
      consume_reset_token();
      $done = true;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password — Shugia Safaris Admin</title>
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body { display: grid; place-items: center; min-height: 100vh; background: var(--light-bg); margin: 0; font-family: var(--font-body); }
    .card { background: var(--white); padding: 40px; border-radius: var(--radius); box-shadow: var(--shadow-lg); max-width: 440px; width: 100%; box-sizing: border-box; }
    .card h1 { font-size: 1.5rem; margin-bottom: 24px; color: var(--dark-text); }
    .card input { width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 6px; margin-bottom: 16px; font-size: 0.95rem; box-sizing: border-box; }
    .card button { width: 100%; padding: 14px; border-radius: var(--radius-full); background: var(--accent); border: none; font-weight: 700; cursor: pointer; font-size: 0.95rem; }
    .card button:hover { background: var(--accent-hover); }
    .card a { color: var(--brand-blue); font-size: 0.9rem; display: inline-block; margin-top: 20px; text-decoration: none; }
    .msg { padding: 12px 16px; border-radius: 6px; font-size: 0.9rem; margin-bottom: 16px; }
    .msg.ok { background: var(--success-bg); color: var(--success); }
    .msg.err { background: var(--error-bg); color: var(--error); }
    .hint { font-size: 0.8rem; color: var(--gray-text); margin-top: -8px; margin-bottom: 16px; }
  </style>
</head>
<body>
  <div class="card">
    <h1><i class="fas fa-lock" style="color:var(--accent)"></i> Set New Password</h1>

    <?php if ($done): ?>
      <div class="msg ok">Password updated. You can now <a href="index.php">sign in</a>.</div>
    <?php elseif (!$valid): ?>
      <div class="msg err">This reset link is invalid or has expired.</div>
      <a href="forgot.php">Request a new link →</a>
    <?php else: ?>
      <?php if ($error): ?><div class="msg err"><?= esc($error) ?></div><?php endif; ?>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token) ?>">
        <input type="password" name="password" placeholder="New password" required minlength="8" autofocus autocomplete="new-password">
        <p class="hint">Minimum 8 characters.</p>
        <input type="password" name="password2" placeholder="Confirm new password" required minlength="8" autocomplete="new-password">
        <button type="submit">Update Password</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>