<?php
require_once __DIR__ . '/auth.php';

$sent  = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify()) {
    $error = 'Security token expired. Please try again.';
  } else {
    $email = trim($_POST['email'] ?? '');
    if (!valid_email($email)) {
      $error = 'Please enter a valid email address.';
    } elseif (strtolower($email) !== strtolower(admin_email())) {
      $sent = true; // vague on purpose
    } else {
      $token = create_reset_token();
      send_reset_email($token);
      $sent = true;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password — Shugia Safaris Admin</title>
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body { display: grid; place-items: center; min-height: 100vh; background: var(--light-bg); margin: 0; font-family: var(--font-body); }
    .card { background: var(--white); padding: 40px; border-radius: var(--radius); box-shadow: var(--shadow-lg); max-width: 440px; width: 100%; box-sizing: border-box; }
    .card h1 { font-size: 1.5rem; margin-bottom: 8px; color: var(--dark-text); }
    .card .sub { font-size: 0.9rem; color: var(--gray-text); margin-bottom: 24px; }
    .card input { width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 6px; margin-bottom: 16px; font-size: 0.95rem; box-sizing: border-box; }
    .card button { width: 100%; padding: 14px; border-radius: var(--radius-full); background: var(--accent); border: none; font-weight: 700; cursor: pointer; font-size: 0.95rem; }
    .card button:hover { background: var(--accent-hover); }
    .card a { color: var(--brand-blue); font-size: 0.9rem; display: inline-block; margin-top: 20px; text-decoration: none; }
    .msg { padding: 12px 16px; border-radius: 6px; font-size: 0.9rem; margin-bottom: 16px; }
    .msg.ok { background: var(--success-bg); color: var(--success); }
    .msg.err { background: var(--error-bg); color: var(--error); }
  </style>
</head>
<body>
  <div class="card">
    <h1><i class="fas fa-key" style="color:var(--accent)"></i> Reset Password</h1>
    <p class="sub">Enter the admin email address to receive a reset link.</p>

    <?php if ($sent): ?>
      <div class="msg ok">If that email matches our records, a reset link has been sent. Check your inbox (and spam folder). The link expires in 1 hour.</div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="msg err"><?= esc($error) ?></div>
    <?php endif; ?>

    <?php if (!$sent): ?>
      <form method="post">
        <?= csrf_field() ?>
        <input type="email" name="email" placeholder="Admin email address" required autofocus>
        <button type="submit">Send Reset Link</button>
      </form>
    <?php endif; ?>

    <a href="index.php">← Back to login</a>
  </div>
</body>
</html>