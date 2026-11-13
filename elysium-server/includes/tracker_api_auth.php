<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

function tracker_app_setting_get(PDO $pdo, string $key, ?string $default = null): ?string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if (!$row || $row['setting_value'] === null) {
        return $default;
    }
    $val = trim((string) $row['setting_value']);
    return $val === '' ? $default : $val;
}

function tracker_app_setting_set(PDO $pdo, string $key, string $value): void
{
    $stmt = $pdo->prepare('SELECT id FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if ($row) {
        $upd = $pdo->prepare('UPDATE app_settings SET setting_value = ? WHERE setting_key = ?');
        $upd->execute([$value, $key]);
        return;
    }
    $ins = $pdo->prepare('INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?)');
    $ins->execute([$key, $value]);
}

function tracker_api_key_ensure(PDO $pdo): string
{
    $existing = tracker_app_setting_get($pdo, 'tracker_api_key');
    if ($existing !== null && $existing !== '') {
        return $existing;
    }
    $key = bin2hex(random_bytes(24));
    tracker_app_setting_set($pdo, 'tracker_api_key', $key);
    return $key;
}

function tracker_request_api_key(): ?string
{
    $header = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
    if (preg_match('/^\s*Bearer\s+(\S+)\s*$/i', $header, $m)) {
        return trim($m[1]);
    }
    $alt = (string) ($_SERVER['HTTP_X_TRACKER_API_KEY'] ?? '');
    if ($alt !== '') {
        return trim($alt);
    }
    return null;
}

function tracker_require_api_key(PDO $pdo): void
{
    $expected = tracker_api_key_ensure($pdo);
    $provided = tracker_request_api_key();
    if ($provided === null || $provided === '' || !hash_equals($expected, $provided)) {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }
}

function tracker_json_body(): array
{
    $raw = file_get_contents('php://input') ?: '{}';
    $input = json_decode($raw, true);
    return is_array($input) ? $input : [];
}
