<?php
declare(strict_types=1);

require_once __DIR__ . '/tracker_mail.php';
require_once __DIR__ . '/tracker_uetr.php';

function tracker_otp_ensure_table(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tracker_otp_challenges (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            uetr CHAR(36) NOT NULL,
            email VARCHAR(190) NOT NULL,
            otp_hash VARCHAR(255) NOT NULL,
            unlock_token_hash VARCHAR(255) NULL,
            attempts INT UNSIGNED NOT NULL DEFAULT 0,
            expires_at DATETIME NOT NULL,
            unlocked_at DATETIME NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tracker_otp_uetr_email (uetr, email),
            INDEX idx_tracker_otp_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

function tracker_otp_generate_code(): string
{
    return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function tracker_otp_create_challenge(PDO $pdo, string $uetr, string $email): array
{
    tracker_otp_ensure_table($pdo);
    $uetr = tracker_uetr_normalize($uetr);
    $email = strtolower(trim($email));
    $code = tracker_otp_generate_code();
    $hash = password_hash($code, PASSWORD_DEFAULT);
    $expires = (new DateTimeImmutable('+10 minutes'))->format('Y-m-d H:i:s');

    // Invalidate prior open challenges for same uetr+email
    $pdo->prepare('DELETE FROM tracker_otp_challenges WHERE uetr = ? AND email = ? AND unlocked_at IS NULL')
        ->execute([$uetr, $email]);

    $ins = $pdo->prepare('
        INSERT INTO tracker_otp_challenges (uetr, email, otp_hash, expires_at)
        VALUES (?, ?, ?, ?)
    ');
    $ins->execute([$uetr, $email, $hash, $expires]);

    return ['code' => $code, 'expires_at' => $expires, 'id' => (int) $pdo->lastInsertId()];
}

function tracker_otp_send_email(PDO $pdo, string $email, string $uetr, string $code): bool
{
    $subject = 'Your Institutional Tracker verification code';
    $body = "Your one-time verification code is: {$code}\n\n"
        . "UETR: {$uetr}\n"
        . "This code expires in 10 minutes.\n\n"
        . "If you did not request this, ignore this email.\n";
    return tracker_send_mail($pdo, $email, $subject, $body);
}

function tracker_otp_verify(PDO $pdo, string $uetr, string $email, string $otp): ?string
{
    tracker_otp_ensure_table($pdo);
    $uetr = tracker_uetr_normalize($uetr);
    $email = strtolower(trim($email));
    $otp = trim($otp);

    $stmt = $pdo->prepare('
        SELECT * FROM tracker_otp_challenges
        WHERE uetr = ? AND email = ? AND unlocked_at IS NULL
        ORDER BY id DESC LIMIT 1
    ');
    $stmt->execute([$uetr, $email]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    if ((int) $row['attempts'] >= 5) {
        return null;
    }

    $expires = strtotime((string) $row['expires_at']);
    if ($expires === false || $expires < time()) {
        return null;
    }

    if (!password_verify($otp, (string) $row['otp_hash'])) {
        $pdo->prepare('UPDATE tracker_otp_challenges SET attempts = attempts + 1 WHERE id = ?')
            ->execute([(int) $row['id']]);
        return null;
    }

    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $pdo->prepare('
        UPDATE tracker_otp_challenges
        SET unlock_token_hash = ?, unlocked_at = NOW()
        WHERE id = ?
    ')->execute([$tokenHash, (int) $row['id']]);

    return $token;
}

function tracker_otp_unlock_valid(PDO $pdo, string $uetr, string $token): bool
{
    tracker_otp_ensure_table($pdo);
    $uetr = tracker_uetr_normalize($uetr);
    $token = trim($token);
    if ($token === '') {
        return false;
    }
    $tokenHash = hash('sha256', $token);
    $stmt = $pdo->prepare('
        SELECT id, unlocked_at FROM tracker_otp_challenges
        WHERE uetr = ? AND unlock_token_hash = ? AND unlocked_at IS NOT NULL
        ORDER BY id DESC LIMIT 1
    ');
    $stmt->execute([$uetr, $tokenHash]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }
    $unlocked = strtotime((string) $row['unlocked_at']);
    // Unlock tokens valid for 30 minutes after verify
    return $unlocked !== false && ($unlocked + 1800) >= time();
}
