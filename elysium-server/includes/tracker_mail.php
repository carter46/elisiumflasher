<?php
declare(strict_types=1);

require_once __DIR__ . '/tracker_api_auth.php';

function tracker_smtp_config(PDO $pdo): array
{
    return [
        'host' => (string) (tracker_app_setting_get($pdo, 'smtp_host', getenv('SMTP_HOST') ?: '') ?: ''),
        'port' => (int) (tracker_app_setting_get($pdo, 'smtp_port', getenv('SMTP_PORT') ?: '587') ?: 587),
        'user' => (string) (tracker_app_setting_get($pdo, 'smtp_user', getenv('SMTP_USER') ?: '') ?: ''),
        'pass' => (string) (tracker_app_setting_get($pdo, 'smtp_pass', getenv('SMTP_PASS') ?: '') ?: ''),
        'encryption' => strtolower((string) (tracker_app_setting_get($pdo, 'smtp_encryption', getenv('SMTP_ENCRYPTION') ?: 'tls') ?: 'tls')),
        'from_email' => (string) (tracker_app_setting_get($pdo, 'smtp_from_email', getenv('SMTP_FROM_EMAIL') ?: '') ?: ''),
        'from_name' => (string) (tracker_app_setting_get($pdo, 'smtp_from_name', getenv('SMTP_FROM_NAME') ?: 'Elysium Tracker') ?: 'Elysium Tracker'),
    ];
}

/**
 * Send plain-text email via SMTP (socket) or PHP mail() fallback when SMTP host empty.
 * Returns true on accepted handoff.
 */
function tracker_send_mail(PDO $pdo, string $to, string $subject, string $body): bool
{
    $to = trim($to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $cfg = tracker_smtp_config($pdo);
    $fromEmail = $cfg['from_email'] !== '' ? $cfg['from_email'] : ('noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $fromName = $cfg['from_name'] !== '' ? $cfg['from_name'] : 'Elysium Tracker';

    // Without an SMTP host, PHP mail() often returns true with no delivery.
    // Treat empty host as unconfigured so callers can fall back to debug OTP.
    if ($cfg['host'] === '') {
        return false;
    }

    return tracker_smtp_send($cfg, $fromEmail, $fromName, $to, $subject, $body);
}

function tracker_smtp_send(array $cfg, string $fromEmail, string $fromName, string $to, string $subject, string $body): bool
{
    $host = $cfg['host'];
    $port = (int) ($cfg['port'] ?: 587);
    $enc = $cfg['encryption'];
    $remote = ($enc === 'ssl' ? 'ssl://' : '') . $host;
    $errno = 0;
    $errstr = '';
    $fp = @stream_socket_client($remote . ':' . $port, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
    if (!$fp) {
        return false;
    }
    stream_set_timeout($fp, 20);

    $read = static function () use ($fp): string {
        $data = '';
        while ($line = fgets($fp, 515)) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $data;
    };
    $write = static function (string $cmd) use ($fp): void {
        fwrite($fp, $cmd . "\r\n");
    };
    $expect = static function (string $resp, string $code): bool {
        return str_starts_with(trim($resp), $code);
    };

    try {
        if (!$expect($read(), '220')) {
            fclose($fp);
            return false;
        }
        $write('EHLO elysium-tracker');
        $ehlo = $read();
        if (!$expect($ehlo, '250')) {
            $write('HELO elysium-tracker');
            if (!$expect($read(), '250')) {
                fclose($fp);
                return false;
            }
        }

        if ($enc === 'tls') {
            $write('STARTTLS');
            if (!$expect($read(), '220')) {
                fclose($fp);
                return false;
            }
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($fp);
                return false;
            }
            $write('EHLO elysium-tracker');
            if (!$expect($read(), '250')) {
                fclose($fp);
                return false;
            }
        }

        if ($cfg['user'] !== '') {
            $write('AUTH LOGIN');
            if (!$expect($read(), '334')) {
                fclose($fp);
                return false;
            }
            $write(base64_encode($cfg['user']));
            if (!$expect($read(), '334')) {
                fclose($fp);
                return false;
            }
            $write(base64_encode($cfg['pass']));
            if (!$expect($read(), '235')) {
                fclose($fp);
                return false;
            }
        }

        $write('MAIL FROM:<' . $fromEmail . '>');
        if (!$expect($read(), '250')) {
            fclose($fp);
            return false;
        }
        $write('RCPT TO:<' . $to . '>');
        $rcpt = $read();
        if (!$expect($rcpt, '250') && !$expect($rcpt, '251')) {
            fclose($fp);
            return false;
        }
        $write('DATA');
        if (!$expect($read(), '354')) {
            fclose($fp);
            return false;
        }

        $headers = [
            'Date: ' . date('r'),
            'From: ' . sprintf('%s <%s>', $fromName, $fromEmail),
            'To: <' . $to . '>',
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];
        $payload = implode("\r\n", $headers) . "\r\n\r\n" . str_replace("\r\n.", "\r\n..", $body) . "\r\n.";
        $write($payload);
        if (!$expect($read(), '250')) {
            fclose($fp);
            return false;
        }
        $write('QUIT');
        fclose($fp);
        return true;
    } catch (Throwable $e) {
        if (is_resource($fp)) {
            fclose($fp);
        }
        return false;
    }
}
