<?php
require_once __DIR__ . '/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify()) {
    $error = 'Security token expired. Please try again.';
  } elseif (attempt_login($_POST['password'] ?? '')) {
    redirect('index.php');
  } else {
    $error = 'Incorrect password.';
  }
}

if (!is_admin_logged_in()):
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login — Shugia Safaris</title>
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body { display:grid;place-items:center;min-height:100vh;background:var(--light-bg);margin:0;font-family:var(--font-body); }
    .login-card { background:var(--white);padding:40px;border-radius:var(--radius);box-shadow:var(--shadow-lg);max-width:400px;width:100%; }
    .login-card h1 { font-size:1.5rem;margin-bottom:24px; }
    .login-card input { width:100%;padding:12px;border:1.5px solid var(--border);border-radius:6px;margin-bottom:16px;font-size:1rem;box-sizing:border-box; }
    .login-card button { width:100%;padding:14px;border-radius:9999px;background:var(--accent);border:none;font-weight:700;cursor:pointer;font-size:1rem; }
    .login-card .error { color:var(--error);font-size:0.9rem;margin-bottom:16px; }
    .login-card a { display:block;text-align:center;font-size:0.85rem;color:var(--brand-blue);margin-top:16px; }
  </style>
</head>
<body>
  <div class="login-card">
    <h1><i class="fas fa-lock" style="color:var(--accent)"></i> Admin Access</h1>
    <?php if ($error): ?><p class="error"><?= esc($error) ?></p><?php endif; ?>
    <form method="post">
      <?= csrf_field() ?>
      <input type="password" name="password" placeholder="Password" required autofocus>
      <button type="submit">Sign In</button>
    </form>
    <a href="forgot.php">Forgot password?</a>
  </div>
</body>
</html>
<?php
exit;
endif;

// Dashboard
$reviews      = read_json('reviews', []);
$packages     = read_json('packages', []);
$destinations = read_json('destinations', []);
$pending      = array_filter($reviews, fn($r) => empty($r['approved']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard — Shugia Admin</title>
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body{background:var(--light-bg);padding:32px;margin:0;font-family:var(--font-body);}
    .wrap{max-width:1200px;margin:0 auto;}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;flex-wrap:wrap;gap:16px;}
    .nav{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:32px;}
    .nav a{padding:8px 16px;border-radius:9999px;background:var(--white);border:1px solid var(--border);color:var(--dark-text);font-size:0.9rem;text-decoration:none;transition:all 0.15s;}
    .nav a:hover,.nav a.is-active{background:var(--primary);color:#fff;border-color:var(--primary);}
    .stats{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;}
    .stat{background:var(--white);border-radius:var(--radius);padding:24px;border:1px solid var(--border);}
    .stat h3{font-size:0.75rem;text-transform:uppercase;color:var(--gray-text);margin:0 0 8px;letter-spacing:0.08em;}
    .stat strong{font-size:2rem;color:var(--primary);font-family:var(--font-heading);}
    .stat a{font-size:0.75rem;display:inline-block;margin-top:8px;color:var(--brand-blue);}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <h1 style="margin:0;">Admin Dashboard</h1>
      <a href="logout.php" class="btn btn-ghost btn-sm">Log out</a>
    </div>
    <nav class="nav">
      <a href="index.php" class="is-active">Dashboard</a>
      <a href="reviews.php">Reviews (<?= count($pending) ?> pending)</a>
      <a href="packages.php">Packages (<?= count($packages) ?>)</a>
      <a href="destinations.php">Destinations (<?= count($destinations) ?>)</a>
      <a href="rates.php">Calculator Rates</a>
      <a href="security.php">Change Password</a>
      <a href="backup.php">Backups</a>
    </nav>
    <div class="stats">
      <div class="stat"><h3>Approved Reviews</h3><strong><?= count($reviews) - count($pending) ?></strong><a href="reviews.php">Manage →</a></div>
      <div class="stat"><h3>Pending Reviews</h3><strong><?= count($pending) ?></strong><a href="reviews.php">Moderate →</a></div>
      <div class="stat"><h3>Packages</h3><strong><?= count($packages) ?></strong><a href="packages.php">Manage →</a></div>
      <div class="stat"><h3>Destinations</h3><strong><?= count($destinations) ?></strong><a href="destinations.php">Manage →</a></div>
    </div>
  </div>
</body>
</html>