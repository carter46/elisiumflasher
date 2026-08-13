<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/mobile_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    mobile_json_err('Method not allowed', 405);
}

$pdo = get_db();

try {
    $session = mobile_require_session($pdo);
    $input = json_decode(file_get_contents('php://input') ?: '{}', true);
    if (!is_array($input)) {
        mobile_json_err('Invalid JSON body', 400);
    }

    $fcmToken = trim((string) ($input['fcm_token'] ?? ''));
    if ($fcmToken === '') {
        mobile_json_err('fcm_token is required', 422);
    }
    if (str_starts_with($fcmToken, 'ExponentPushToken[') || str_starts_with($fcmToken, 'ExpoPushToken[')) {
        mobile_json_err('Expo push tokens are not allowed. Use native FCM device token.', 422);
    }
    if (strlen($fcmToken) < 80) {
        mobile_json_err('Device push token looks invalid for FCM', 422);
    }

    $stmt = $pdo->prepare('
        INSERT INTO mobile_devices (bank_code, account_number, fcm_token)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE fcm_token = VALUES(fcm_token), updated_at = CURRENT_TIMESTAMP
    ');
    $stmt->execute([
        $session['bank_code'],
        $session['account_number'],
        $fcmToken,
    ]);

    mobile_json_ok(['registered' => true]);
} catch (Throwable $e) {
    mobile_json_err('Failed to register device', 500);
}
