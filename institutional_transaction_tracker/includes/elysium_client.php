<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

/**
 * POST JSON to a single Elysium site with tracker API key.
 */
function elysium_post(array $site, string $path, array $body, ?int $timeoutMs = null): array
{
    $base = rtrim((string) ($site['base_url'] ?? ''), '/');
    $url = $base . '/' . ltrim($path, '/');
    $timeoutMs = $timeoutMs ?? (int) ($site['timeout_ms'] ?? 4000);
    $timeoutSec = max(1.0, $timeoutMs / 1000);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . (string) ($site['api_key'] ?? ''),
            'X-Tracker-Api-Key: ' . (string) ($site['api_key'] ?? ''),
            'X-Tracker-Site-Id: institutional-tracker',
        ],
        CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
        CURLOPT_CONNECTTIMEOUT => (int) ceil($timeoutSec),
        CURLOPT_TIMEOUT => (int) ceil($timeoutSec),
    ]);
    $raw = curl_exec($ch);
    $errno = curl_errno($ch);
    $err = curl_error($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno) {
        return ['ok' => false, 'http' => 0, 'error' => $err ?: 'curl error', 'data' => null];
    }
    $data = json_decode((string) $raw, true);
    return [
        'ok' => $code >= 200 && $code < 300,
        'http' => $code,
        'error' => null,
        'data' => is_array($data) ? $data : null,
        'raw' => $raw,
    ];
}

/**
 * Parallel locate across all enabled sites.
 * @return array{matches: list<array>, errors: list<array>}
 */
function elysium_locate_fanout(string $uetr): array
{
    $sites = tracker_enabled_sites();
    $matches = [];
    $errors = [];

    if ($sites === []) {
        return ['matches' => [], 'errors' => [['code' => 'not_configured', 'message' => 'No enabled sites configured with API keys']]];
    }

    $mh = curl_multi_init();
    $handles = [];
    foreach ($sites as $idx => $site) {
        $base = rtrim((string) $site['base_url'], '/');
        $url = $base . '/api/tracker_locate.php';
        $timeoutMs = (int) ($site['timeout_ms'] ?? 4000);
        $timeoutSec = max(1.0, $timeoutMs / 1000);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . (string) $site['api_key'],
                'X-Tracker-Api-Key: ' . (string) $site['api_key'],
                'X-Tracker-Site-Id: institutional-tracker',
            ],
            CURLOPT_POSTFIELDS => json_encode(['uetr' => $uetr]),
            CURLOPT_CONNECTTIMEOUT => (int) ceil($timeoutSec),
            CURLOPT_TIMEOUT => (int) ceil($timeoutSec),
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[$idx] = ['ch' => $ch, 'site' => $site];
    }

    do {
        $status = curl_multi_exec($mh, $running);
        if ($running) {
            curl_multi_select($mh, 1.0);
        }
    } while ($running && ($status === CURLM_OK || $status === CURLM_CALL_MULTI_PERFORM));

    foreach ($handles as $item) {
        $ch = $item['ch'];
        $site = $item['site'];
        $raw = curl_multi_getcontent($ch);
        $errno = curl_errno($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);

        $siteId = (string) ($site['id'] ?? '');
        if ($errno) {
            $errors[] = ['site_id' => $siteId, 'code' => 'network', 'http' => 0];
            continue;
        }
        if ($code === 401 || $code === 403) {
            $errors[] = ['site_id' => $siteId, 'code' => 'auth_failed', 'http' => $code];
            continue;
        }
        if ($code >= 500) {
            $errors[] = ['site_id' => $siteId, 'code' => 'upstream_error', 'http' => $code];
            continue;
        }

        $data = json_decode((string) $raw, true);
        if (!is_array($data)) {
            $errors[] = ['site_id' => $siteId, 'code' => 'invalid_json', 'http' => $code];
            continue;
        }
        if (array_key_exists('success', $data) && $data['success'] === false && empty($data['found'])) {
            // Soft not-found from some hosts, or explicit failure
            if ($code >= 400) {
                $errors[] = ['site_id' => $siteId, 'code' => 'upstream_reject', 'http' => $code];
            }
            continue;
        }
        if (!empty($data['found'])) {
            $matches[] = [
                'site_id' => (string) ($data['site_id'] ?? $site['id']),
                'rail' => (string) ($data['rail'] ?? 'local'),
                'name' => (string) ($site['name'] ?? $site['id']),
                'base_url' => (string) $site['base_url'],
            ];
        }
    }
    curl_multi_close($mh);

    return ['matches' => $matches, 'errors' => $errors];
}
