<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$page = $_GET['page'] ?? '';
$pdo = get_db();

if ($page === 'local') {
    $profile = $pdo->query('SELECT account_type, account_name, account_number, balance, masked_pan, tier_label FROM local_dashboard_profile ORDER BY id DESC LIMIT 1')->fetch();
    $banks = $pdo->query('SELECT bank_name, bank_code FROM local_transfer_banks WHERE is_active = 1 ORDER BY bank_name')->fetchAll();
    $transfers = $pdo->query('SELECT recipient_name, subtitle, amount, direction, status_label FROM local_recent_transfers ORDER BY sort_order, id')->fetchAll();
    $frequent = $pdo->query('SELECT recipient_name, sort_order FROM local_frequent_recipients ORDER BY sort_order, id')->fetchAll();
    json_response(['success' => true, 'data' => ['profile' => $profile, 'banks' => $banks, 'recent_transfers' => $transfers, 'frequent_recipients' => $frequent]]);
}

json_response(['success' => false, 'message' => 'Invalid page'], 400);
