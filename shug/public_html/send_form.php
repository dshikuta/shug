<?php
/**
 * Shugia Safaris — Free PHP mail() form handler with CSRF.
 * Handles quote + contact AJAX submissions.
 * Admin recipient: tours@shugiasafaris.com
 */
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// Read body ONCE.
$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = $_POST;
}

// CSRF: prefer header, then body field, then $_POST field.
$token = $_SERVER['HTTP_X_CSRF_TOKEN']
      ?? ($input['csrf_token'] ?? null)
      ?? ($_POST['csrf_token'] ?? null);

if (!csrf_verify($token)) {
    csrf_fail(true);
}

// Honeypot
if (!empty($input['website'])) {
    echo json_encode(['ok' => true, 'message' => 'Thank you!']);
    exit;
}

$type    = clean_text($input['type'] ?? 'contact', 20);
$name    = clean_text($input['name'] ?? '', 80);
$email   = clean_text($input['email'] ?? '', 120);
$phone   = clean_text($input['phone'] ?? '', 30);
$subject = clean_text($input['subject'] ?? '', 150);
$message = clean_multiline($input['message'] ?? '', 3000);
$dest    = clean_text($input['destination'] ?? '', 80);
$date    = clean_text($input['travelDate'] ?? '', 20);

if (strlen($name) < 2)     { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Please enter your name.']); exit; }
if (!valid_email($email))  { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Please enter a valid email address.']); exit; }
if (strlen($message) < 10) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Please write at least 10 characters.']); exit; }

$ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
$store  = read_json('submissions', []);
$cutoff = time() - 3600;
$recent = 0;
foreach ($store as $s) {
    if (($s['ipHash'] ?? '') === $ipHash && strtotime($s['createdAt'] ?? '') > $cutoff) $recent++;
}
if ($recent >= 3) {
    http_response_code(429);
    echo json_encode(['ok'=>false,'error'=>'Too many submissions. Please try again later or WhatsApp us.']);
    exit;
}

$ref = 'REF-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

$to = 'tours@shugiasafaris.com';
$mailSubject = ($type === 'quote' ? '[Quote Request] ' : '[Contact Message] ') . $name . ' — ' . $ref;

$body  = "New submission from Shugia Safaris website\r\n";
$body .= "Reference: $ref\r\n";
$body .= "Type: $type\r\n";
$body .= str_repeat('-', 50) . "\r\n";
$body .= "Name: $name\r\n";
$body .= "Email: $email\r\n";
if ($phone)   $body .= "Phone: $phone\r\n";
if ($subject) $body .= "Subject: $subject\r\n";
if ($dest)    $body .= "Destination: $dest\r\n";
if ($date)    $body .= "Travel date: $date\r\n";
$body .= str_repeat('-', 50) . "\r\n";
$body .= "Message:\r\n$message\r\n";

// FIXED: hardcoded From — must be a real mailbox created in cPanel.
$fromAddress = 'forms@shugiasafaris.com';
$headers  = "From: Shugia Safaris <$fromAddress>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$sent = @mail($to, $mailSubject, $body, $headers);

$store[] = [
    'ref' => $ref, 'type' => $type, 'name' => $name, 'email' => $email,
    'phone' => $phone, 'subject' => $subject, 'destination' => $dest,
    'travelDate' => $date, 'message' => $message,
    'mailSent' => $sent, 'createdAt' => date('c'), 'ipHash' => $ipHash,
];
write_json('submissions', $store);

echo json_encode([
    'ok'      => true,
    'message' => 'Thank you! Reference: ' . $ref . '. We reply within 2 hours.',
    'ref'     => $ref
]);