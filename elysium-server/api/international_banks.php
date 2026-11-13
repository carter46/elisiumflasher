<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$countryCode = strtoupper(trim((string) ($_GET['country_code'] ?? '')));
if (!preg_match('/^[A-Z]{2}$/', $countryCode)) {
    json_response(['success' => false, 'message' => 'Invalid country_code'], 422);
}

$q = mb_strtolower(trim((string) ($_GET['q'] ?? '')), 'UTF-8');
$pdo = get_db();

if ($q === '') {
    $stmt = $pdo->prepare('
        SELECT bank_name, swift_prefix
        FROM international_banks
        WHERE is_active = 1 AND country_code = ?
        ORDER BY bank_name
        LIMIT 30
    ');
    $stmt->execute([$countryCode]);
    json_response(['success' => true, 'banks' => $stmt->fetchAll()]);
}

$stmt = $pdo->prepare('
    SELECT bank_name, swift_prefix
    FROM international_banks
    WHERE is_active = 1 AND country_code = ?
      AND (LOWER(bank_name) LIKE ? OR LOWER(COALESCE(swift_prefix, \'\')) LIKE ?)
    ORDER BY bank_name
    LIMIT 30
');
$like = '%' . $q . '%';
$stmt->execute([$countryCode, $like, $like]);
json_response(['success' => true, 'banks' => $stmt->fetchAll()]);

