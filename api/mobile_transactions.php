<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/mobile_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    mobile_json_err('Method not allowed', 405);
}

$pdo = get_db();

try {
    $session = mobile_require_session($pdo);
    $bankCode = $session['bank_code'];
    $accountNumber = $session['account_number'];

    $limit = isset($_GET['limit']) ? max(1, min(200, (int) $_GET['limit'])) : 50;
    $offset = isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : 0;

    $total = mobile_count_successful($pdo, $bankCode, $accountNumber);
    $rows = mobile_fetch_successful_rows($pdo, $bankCode, $accountNumber, $limit, $offset);
    $transactions = [];
    foreach ($rows as $row) {
        $transactions[] = mobile_receipt_dto($row, $bankCode);
    }

    mobile_json_ok([
        'transactions' => $transactions,
        'total' => $total,
    ]);
} catch (Throwable $e) {
    mobile_json_err('Failed to load transactions', 500);
}
