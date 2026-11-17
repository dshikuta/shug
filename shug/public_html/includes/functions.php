<?php
/**
 * Shugia Safaris — shared helpers
 * JSON storage, auth, sanitization, review CRUD, password reset, CSRF.
 */

if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'lifetime' => 0, 'path' => '/',
    'secure'   => !empty($_SERVER['HTTPS']),
    'httponly' => true, 'samesite' => 'Lax'
  ]);
  session_start();
}

define('DATA_DIR',   __DIR__ . '/../data');
define('BACKUP_DIR', DATA_DIR . '/backups');

/* ---------- JSON storage ---------- */
function data_path($name) { return DATA_DIR . '/' . basename($name) . '.json'; }

function read_json($name, $default = []) {
  $file = data_path($name);
  if (!is_file($file)) return $default;
  $raw = @file_get_contents($file);
  if ($raw === false) return $default;
  $data = json_decode($raw, true);
  return is_array($data) ? $data : $default;
}

function write_json($name, $data, $backup = true) {
  $file = data_path($name);
  if (!is_dir(DATA_DIR)) @mkdir(DATA_DIR, 0755, true);
  if ($backup && is_file($file)) {
    if (!is_dir(BACKUP_DIR)) @mkdir(BACKUP_DIR, 0755, true);
    $stamp = date('Y-m-d');
    $bak = BACKUP_DIR . '/' . basename($name) . '-' . $stamp . '.json';
    if (!is_file($bak)) @copy($file, $bak);
  }
  $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  $tmp = $file . '.tmp';
  if (@file_put_contents($tmp, $json, LOCK_EX) === false) return false;
  return @rename($tmp, $file);
}

/* ---------- Sanitize ---------- */
function clean_text($str, $max = 2000) {
  $str = trim((string)$str);
  $str = preg_replace('/\s+/u', ' ', $str);
  return mb_substr($str, 0, $max);
}
function clean_multiline($str, $max = 5000) {
  $str = trim((string)$str);
  $str = str_replace(["\r\n", "\r"], "\n", $str);
  $str = preg_replace('/\n{3,}/', "\n\n", $str);
  return mb_substr($str, 0, $max);
}
function valid_email($email) { return filter_var($email, FILTER_VALIDATE_EMAIL) !== false; }
function esc($str) { return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect($url, $code = 302) { header('Location: ' . $url, true, $code); exit; }

/* ---------- CSRF ---------- */
/**
 * Return the current CSRF token for this session, generating it once.
 */
function csrf_token() {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

/**
 * Hidden <input> for HTML forms.
 * Usage: <?= csrf_field() ?>
 */
function csrf_field() {
  return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
}

/**
 * Validate a CSRF token. Accepts the token from:
 *   - explicit $token argument (preferred in AJAX handlers)
 *   - HTTP header 'X-CSRF-Token' (AJAX)
 *   - $_POST['csrf_token']        (classic form)
 *
 * Does NOT read php://input itself — the caller must supply the token
 * explicitly for AJAX endpoints, otherwise it is read from the header
 * or from $_POST. This avoids the "read php://input twice" trap.
 */
function csrf_verify($token = null) {
  $expected = $_SESSION['csrf_token'] ?? '';

  if ($token === null) {
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
  }
  if ($token === null && isset($_POST['csrf_token'])) {
    $token = $_POST['csrf_token'];
  }

  if (!is_string($expected) || $expected === '' || !is_string($token) || $token === '') {
    return false;
  }
  return hash_equals($expected, $token);
}

/**
 * Hard-stop with 403.
 */
function csrf_fail($json = true) {
  http_response_code(403);
  if ($json) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Invalid security token. Please refresh the page and try again.']);
  } else {
    echo 'Invalid security token. Please refresh the page and try again.';
  }
  exit;
}

/* ---------- Admin auth ---------- */
function get_admin() { return read_json('admin', ['passwordHash' => null, 'email' => null]); }

function admin_password_fallback() { return 'shugia2025'; }

function verify_admin_password($password) {
  $admin = get_admin();
  if (!empty($admin['passwordHash'])) return password_verify($password, $admin['passwordHash']);
  return hash_equals(admin_password_fallback(), (string)$password);
}

function set_admin_password($password) {
  $admin = get_admin();
  $admin['passwordHash'] = password_hash($password, PASSWORD_DEFAULT);
  $admin['updatedAt']    = date('c');
  return write_json('admin', $admin);
}

function is_admin_logged_in() {
  return !empty($_SESSION['shugia_admin']);
}

function attempt_login($password) {
  if (verify_admin_password($password)) {
    $_SESSION['shugia_admin'] = true;
    session_regenerate_id(true);
    // Regenerate CSRF token on privilege escalation
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return true;
  }
  return false;
}

function admin_logout() {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
  }
  session_destroy();
}

function require_admin() {
  if (!is_admin_logged_in()) redirect('/admin/index.php');
}

/* ---------- Password reset ---------- */
function admin_email() {
  $admin = get_admin();
  return $admin['email'] ?: (read_json('settings', [])['adminEmail'] ?? 'tours@shugiasafaris.com');
}

function create_reset_token() {
  $token = bin2hex(random_bytes(32));
  write_json('reset', [
    'hash'   => hash('sha256', $token),
    'expiry' => time() + 3600,
    'used'   => false
  ]);
  return $token;
}

function verify_reset_token($token) {
  $r = read_json('reset', []);
  if (empty($r['hash']) || !empty($r['used']) || $r['expiry'] < time()) return false;
  return hash_equals($r['hash'], hash('sha256', $token));
}

function consume_reset_token() {
  $r = read_json('reset', []);
  $r['used'] = true;
  write_json('reset', $r);
}

function send_reset_email($token) {
  $to = admin_email();
  $url = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'shugiasafaris.com') . '/admin/reset.php?token=' . urlencode($token);
  $subject = 'Shugia Safaris — Password Reset';
  $headers = "From: Shugia Safaris <forms@shugiasafaris.com>\r\n" .
             "Reply-To: $to\r\n" .
             "Content-Type: text/html; charset=UTF-8\r\n";
  $body = "<p>Hello,</p><p>Someone requested a password reset for the Shugia Safaris admin panel.</p>" .
          "<p><a href=\"$url\">Click here to reset your password</a> — expires in 1 hour.</p>" .
          "<p>If you did not request this, ignore this email.</p><p>— Shugia Safaris</p>";
  return @mail($to, $subject, $body, $headers);
}

/* ---------- Reviews ---------- */
function get_reviews($approvedOnly = true) {
  $reviews = read_json('reviews', []);
  usort($reviews, fn($a, $b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));
  if (!$approvedOnly) return $reviews;
  return array_values(array_filter($reviews, fn($r) => !empty($r['approved'])));
}

function get_reviews_stats() {
  $all = read_json('reviews', []);
  $ok  = array_filter($all, fn($r) => !empty($r['approved']));
  $count = count($ok);
  $sum = 0;
  foreach ($ok as $r) $sum += (int)($r['rating'] ?? 0);
  return [
    'total'    => count($all),
    'approved' => $count,
    'pending'  => count($all) - $count,
    'average'  => $count ? round($sum / $count, 1) : 0,
  ];
}

function add_review($data) {
  $reviews = read_json('reviews', []);

  // Rate limit: 1 review per IP per hour
  $ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
  $cutoff = time() - 3600;
  foreach ($reviews as $r) {
    if (($r['ipHash'] ?? '') === $ipHash && strtotime($r['createdAt'] ?? '') > $cutoff) {
      return ['ok' => false, 'error' => 'You have already submitted a review recently.'];
    }
  }

  $review = [
    'id'        => uniqid('rev_', true),
    'name'      => clean_text($data['name'] ?? '', 80),
    'email'     => clean_text($data['email'] ?? '', 120),
    'location'  => clean_text($data['location'] ?? '', 80),
    'rating'    => max(1, min(5, (int)($data['rating'] ?? 0))),
    'text'      => clean_multiline($data['text'] ?? '', 800),
    'tour'      => clean_text($data['tour'] ?? '', 100),
    'approved'  => false,
    'createdAt' => date('c'),
    'ipHash'    => $ipHash,
  ];

  if (strlen($review['name']) < 2)    return ['ok' => false, 'error' => 'Please enter your name.'];
  if (!valid_email($review['email'])) return ['ok' => false, 'error' => 'Please enter a valid email.'];
  if (strlen($review['text']) < 20)   return ['ok' => false, 'error' => 'Please write at least 20 characters.'];

  $reviews[] = $review;
  if (write_json('reviews', $reviews)) {
    return ['ok' => true, 'message' => 'Thank you! Your review will appear after moderation.'];
  }
  return ['ok' => false, 'error' => 'Could not save your review.'];
}