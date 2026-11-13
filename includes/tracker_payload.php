<?php
declare(strict_types=1);

require_once __DIR__ . '/tracker_status.php';
require_once __DIR__ . '/tracker_uetr.php';

/**
 * Build public tracker payload from a local or international transaction row.
 * Never includes receipt status.
 */
function tracker_build_payload(array $tx, string $rail = 'local'): array
{
    $code = tracker_status_normalize((string) ($tx['tracker_status'] ?? 'PDNG'));
    $meta = tracker_status_meta($code);

    $amount = number_format((float) ($tx['amount'] ?? 0), 2, '.', '');
    $currency = strtoupper((string) ($tx['currency'] ?? 'NGN'));
    $txDate = (string) ($tx['transaction_date'] ?? $tx['created_at'] ?? '');
    $updated = (string) ($tx['tracker_status_updated_at'] ?? $txDate);

    $payload = [
        'success' => true,
        'found' => true,
        'rail' => $rail === 'international' ? 'international' : 'local',
        'uetr' => (string) ($tx['uetr'] ?? ''),
        'tracker_status' => $code,
        'tracker_label' => $meta['label'],
        'tracker_description' => $meta['description'],
        'tracker_family' => $meta['family'],
        'badge_class' => $meta['badge_class'],
        'timeline' => $meta['timeline'],
        'amount' => $amount,
        'currency' => $currency,
        'transaction_date' => $txDate,
        'last_updated_iso_utc' => $updated !== '' ? date('c', strtotime($updated) ?: time()) : date('c'),
        'reference' => (string) ($tx['reference'] ?? ''),
        'purpose' => (string) ($tx['purpose'] ?? $tx['message_type'] ?? ''),
        'sender' => [
            'name' => (string) ($tx['sender_name'] ?? ''),
            'account_masked' => tracker_mask_account((string) ($tx['sender_account'] ?? '')),
            'bank' => (string) ($tx['sender_bank'] ?? ''),
            'swift' => (string) ($tx['sender_swift'] ?? ''),
        ],
        'beneficiary' => [
            'name' => (string) ($tx['beneficiary_name'] ?? ''),
            'bank' => (string) ($tx['beneficiary_bank'] ?? $tx['bank_name'] ?? ''),
            'account_masked' => tracker_mask_account((string) ($tx['beneficiary_account'] ?? '')),
            'iban_masked' => tracker_mask_account((string) ($tx['beneficiary_iban'] ?? '')),
            'swift' => (string) ($tx['swift_code'] ?? ''),
            'country' => (string) ($tx['country_name'] ?? $tx['country_code'] ?? ''),
        ],
    ];

    if ($rail === 'international') {
        $payload['international'] = [
            'message_type' => (string) ($tx['message_type'] ?? ''),
            'delivery_date' => (string) ($tx['delivery_date'] ?? ''),
            'beneficiary_address' => (string) ($tx['beneficiary_address'] ?? ''),
        ];
        if ($payload['purpose'] === '' && !empty($tx['message_type'])) {
            $payload['purpose'] = (string) $tx['message_type'];
        }
    }

    return $payload;
}
