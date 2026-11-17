<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
  $input = $_POST;
}

$token = $_SERVER['HTTP_X_CSRF_TOKEN']
      ?? ($input['csrf_token'] ?? null)
      ?? ($_POST['csrf_token'] ?? null);

if (!csrf_verify($token)) {
  csrf_fail(true);
}

if (!empty($input['website'])) {
  echo json_encode(['ok' => true, 'message' => 'Thank you!']);
  exit;
}

$result = add_review([
  'name'     => $input['name']     ?? '',
  'email'    => $input['email']    ?? '',
  'location' => $input['location'] ?? '',
  'rating'   => $input['rating']   ?? 0,
  'text'     => $input['text']     ?? '',
  'tour'     => $input['tour']     ?? '',
]);

if (!$result['ok']) http_response_code(400);
echo json_encode($result);