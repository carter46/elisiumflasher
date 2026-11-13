<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/tracker_status.php';
require_once __DIR__ . '/../includes/tracker_uetr.php';
require_once __DIR__ . '/../includes/tracker_store.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';
require_login();

$pdo = get_db();
tracker_server_ensure_schema($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id > 0) {
        $stmt = $pdo->prepare('
            SELECT id, reference, uetr, beneficiary_iban, beneficiary_name, beneficiary_address, swift_code, routing_number, bank_name,
                   country_code, country_name,
                   amount, currency, message_type, delivery_date, status, tracker_status, tracker_status_updated_at, created_at
            FROM international_transactions
            WHERE id = ?
            LIMIT 1
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_response(['success' => false, 'message' => 'Not found'], 404);
        }
        json_response(['success' => true, 'transaction' => $row]);
    }

    $limit = isset($_GET['limit']) ? max(1, min(200, (int) $_GET['limit'])) : 50;
    $offset = isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : 0;

    $stmt = $pdo->prepare('
        SELECT id, reference, uetr, beneficiary_iban, beneficiary_name, beneficiary_address, swift_code, routing_number, bank_name,
               country_code, country_name,
               amount, currency, message_type, delivery_date, status, tracker_status, tracker_status_updated_at, created_at
        FROM international_transactions
        ORDER BY id DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    json_response(['success' => true, 'transactions' => $rows, 'limit' => $limit, 'offset' => $offset]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check platform status first
    $platformStmt = $pdo->query('SELECT status FROM platform_status WHERE id = 1 LIMIT 1');
    $platformRow = $platformStmt ? $platformStmt->fetch() : null;
    $platformStatus = $platformRow ? (string) $platformRow['status'] : 'on';
    if ($platformStatus === 'off') {
        json_response([
            'success' => false,
            'message' => 'Platform is currently under maintenance. All transfers are temporarily disabled. Please try again later.',
            'platform_offline' => true
        ], 503);
    }

    $input = json_decode(file_get_contents('php://input') ?: '{}', true);
    if (!is_array($input)) {
        json_response(['success' => false, 'message' => 'Invalid JSON'], 400);
    }

    $reference = trim((string) ($input['reference'] ?? ''));
    $beneficiaryIban = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', (string) ($input['beneficiaryIban'] ?? '')));
    $beneficiaryName = trim((string) ($input['accountName'] ?? ''));
    $beneficiaryAddress = trim((string) ($input['beneficiaryAddress'] ?? ''));
    $swiftCode = trim((string) ($input['swiftCode'] ?? ''));
    $routingNumber = preg_replace('/\D+/', '', (string) ($input['routingNumber'] ?? ''));
    $bankName = trim((string) ($input['bankName'] ?? ''));
    $countryCode = strtoupper(trim((string) ($input['countryCode'] ?? '')));
    $countryName = trim((string) ($input['countryName'] ?? ''));
    $amountRaw = (string) ($input['amount'] ?? '');
    $currency = strtoupper(trim((string) ($input['currency'] ?? '')));
    $messageType = trim((string) ($input['messageType'] ?? ''));

    // Fetch admin-set delivery date
    $senderStmt = $pdo->query('SELECT default_delivery_date FROM international_sender_settings WHERE id = 1 LIMIT 1');
    $senderRow = $senderStmt ? $senderStmt->fetch() : null;
    $deliveryDate = $senderRow && $senderRow['default_delivery_date'] ? (string) $senderRow['default_delivery_date'] : '';
    if ($deliveryDate === '') {
        // Fallback to 3 days from now if not set
        $deliveryDate = date('Y-m-d', strtotime('+3 days'));
    }

    // Countries that use routing numbers instead of SWIFT
    $routingCountries = ['US'];
    $usesRouting = in_array($countryCode, $routingCountries, true);

    // Basic required fields
    if ($reference === '' || $beneficiaryIban === '' || $beneficiaryName === '' || $bankName === '' || $countryCode === '' || $countryName === '' || $amountRaw === '' || $currency === '' || $messageType === '') {
        json_response(['success' => false, 'message' => 'Missing required fields'], 422);
    }

    // Validate SWIFT or Routing based on country
    if ($usesRouting) {
        if ($routingNumber === '' || strlen($routingNumber) !== 9) {
            json_response(['success' => false, 'message' => 'Valid 9-digit routing number required for US transfers'], 422);
        }
    } else {
        if ($swiftCode === '') {
            json_response(['success' => false, 'message' => 'SWIFT code required'], 422);
        }
    }
    if (!preg_match('/^[A-Z]{2}$/', $countryCode)) {
        json_response(['success' => false, 'message' => 'Invalid country'], 422);
    }
    // Accept any ISO-style 3-letter currency code (e.g. USD, EUR, GBP, KRW, AED, NGN).
    if (!preg_match('/^[A-Z]{3}$/', $currency)) {
        json_response(['success' => false, 'message' => 'Invalid currency'], 422);
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $deliveryDate)) {
        json_response(['success' => false, 'message' => 'Invalid delivery date'], 422);
    }

    $amount = (float) $amountRaw;
    if (!is_finite($amount) || $amount <= 0) {
        json_response(['success' => false, 'message' => 'Invalid amount'], 422);
    }

    $statusStmt = $pdo->query('SELECT status FROM international_status WHERE id = 1 LIMIT 1');
    $rowStatus = $statusStmt ? $statusStmt->fetch() : null;
    $globalStatus = $rowStatus && isset($rowStatus['status']) ? (string) $rowStatus['status'] : 'none';
    $globalStatus = strtolower(trim($globalStatus));
    $map = [
        'none' => 'SUCCESSFUL',
        'pending' => 'PENDING',
        'processing' => 'PROCESSING',
        'failed' => 'FAILED',
        'reversed' => 'REVERSED',
        'network_error' => 'NETWORK_ERROR',
    ];
    $finalStatus = $map[$globalStatus] ?? 'PENDING';
    $trackerDefault = tracker_status_normalize(
        strtoupper((string) (tracker_app_setting_get($pdo, 'international_tracker_status', 'PDNG') ?? 'PDNG')),
        'PDNG'
    );
    $uetr = tracker_uetr_generate_unique($pdo, 'international_transactions', 'uetr');

    $stmt = $pdo->prepare('
        INSERT INTO international_transactions
          (reference, uetr, beneficiary_iban, beneficiary_name, beneficiary_address, swift_code, routing_number, bank_name, country_code, country_name, amount, currency, message_type, delivery_date, status, tracker_status, tracker_status_updated_at)
        VALUES
          (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ');

    try {
        $stmt->execute([
            $reference,
            $uetr,
            $beneficiaryIban,
            $beneficiaryName,
            $beneficiaryAddress,
            $swiftCode,
            $routingNumber,
            $bankName,
            $countryCode,
            $countryName,
            $amount,
            $currency,
            $messageType,
            $deliveryDate,
            $finalStatus,
            $trackerDefault,
        ]);
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'duplicate')) {
            $check = $pdo->prepare('SELECT id, uetr, tracker_status FROM international_transactions WHERE reference = ? LIMIT 1');
            $check->execute([$reference]);
            $row = $check->fetch();
            if ($row && isset($row['id'])) {
                if (empty($row['uetr'])) {
                    try {
                        $uetrFix = tracker_uetr_generate_unique($pdo, 'international_transactions', 'uetr');
                        $pdo->prepare('UPDATE international_transactions SET uetr = ? WHERE id = ? AND (uetr IS NULL OR uetr = \'\')')
                            ->execute([$uetrFix, (int) $row['id']]);
                        $row['uetr'] = $uetrFix;
                    } catch (Throwable $ignore) {
                    }
                }
                if (empty($row['tracker_status'])) {
                    try {
                        $pdo->prepare('UPDATE international_transactions SET tracker_status = ?, tracker_status_updated_at = NOW() WHERE id = ? AND (tracker_status IS NULL OR tracker_status = \'\')')
                            ->execute(['PDNG', (int) $row['id']]);
                        $row['tracker_status'] = 'PDNG';
                    } catch (Throwable $ignore) {
                        $row['tracker_status'] = 'PDNG';
                    }
                }
                json_response([
                    'success' => true,
                    'id' => (int) $row['id'],
                    'reference' => $reference,
                    'uetr' => $row['uetr'] ?? null,
                    'tracker_status' => $row['tracker_status'] ?? 'PDNG',
                ]);
            }
        }
        json_response(['success' => false, 'message' => 'Failed to create transaction'], 500);
    }

    json_response([
        'success' => true,
        'id' => (int) $pdo->lastInsertId(),
        'reference' => $reference,
        'uetr' => $uetr,
        'status' => $finalStatus,
        'tracker_status' => $trackerDefault,
    ]);
}

json_response(['success' => false, 'message' => 'Method not allowed'], 405);

