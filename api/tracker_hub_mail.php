<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';
require_once __DIR__ . '/../includes/tracker_mail.php';
require_once __DIR__ . '/../includes/tracker_store.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$pdo = get_db();
tracker_require_api_key($pdo);
tracker_flasher_ensure_schema($pdo);

$input = tracker_json_body();
$to = strtolower(trim((string) ($input['to'] ?? '')));
$subject = trim((string) ($input['subject'] ?? ''));
$body = (string) ($input['body'] ?? '');

if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Valid recipient email is required'], 422);
}
if ($subject === '' || trim($body) === '') {
    json_response(['success' => false, 'message' => 'Subject and body are required'], 422);
}

$sent = tracker_send_mail($pdo, $to, $subject, $body);
$smtpHost = trim((string) (tracker_smtp_config($pdo)['host'] ?? ''));

json_response([
    'success' => true,
    'mail_sent' => $sent,
    'debug' => !$sent && $smtpHost === '',
    'message' => $sent
        ? 'Email accepted for delivery'
        : 'Email not delivered — configure SMTP on Elysium Tracker settings',
]);
