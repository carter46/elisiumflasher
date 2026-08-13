<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/mobile_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    mobile_json_err('Method not allowed', 405);
}

$pdo = get_db();

try {
    $session = mobile_require_eligible_session($pdo);
    $rawId = (string) ($_GET['id'] ?? '');
    $sourceId = mobile_parse_receipt_id($rawId);
    if ($sourceId === null || $sourceId <= 0) {
        mobile_json_err('Invalid receipt id', 422);
    }

    $row = mobile_fetch_owned_transaction(
        $pdo,
        $session['bank_code'],
        $session['account_number'],
        $sourceId
    );
    if ($row === null) {
        mobile_json_err('Receipt not found', 404);
    }

    mobile_json_ok(mobile_receipt_dto($row, $session['bank_code'], $pdo));
} catch (Throwable $e) {
    mobile_json_err('Failed to load receipt', 500);
}
