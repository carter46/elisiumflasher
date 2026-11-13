<?php
declare(strict_types=1);

/**
 * Institutional tracker status catalog (SWIFT / ISO 20022 inspired).
 * Independent from receipt statuses (SUCCESSFUL / PENDING / FAILED / …).
 */

function tracker_status_catalog(): array
{
    return [
        'PDNG' => [
            'code' => 'PDNG',
            'label' => 'Pending / In Progress',
            'description' => 'The transfer has been initiated or received, but is still being processed, routed, converted, or awaiting the next clearing window.',
            'family' => 'in_flight',
            'badge_class' => 'pending',
            'timeline' => [
                ['step' => 1, 'title' => '01. Initiated', 'description' => 'Payment instruction accepted into the rail.', 'state' => 'complete'],
                ['step' => 2, 'title' => '02. Clearing & Processing', 'description' => 'Awaiting routing, conversion, or next clearing window.', 'state' => 'active'],
                ['step' => 3, 'title' => '03. Pending Settlement', 'description' => 'Ledger allocation queued.', 'state' => 'pending'],
            ],
        ],
        'ACSP' => [
            'code' => 'ACSP',
            'label' => 'Accepted Settlement in Process',
            'description' => 'The payment instruction has been accepted and settlement between financial institutions is actively progressing.',
            'family' => 'in_flight',
            'badge_class' => 'pending',
            'timeline' => [
                ['step' => 1, 'title' => '01. Initiated', 'description' => 'Payment instruction accepted into the rail.', 'state' => 'complete'],
                ['step' => 2, 'title' => '02. Clearing & Transit', 'description' => 'Interbank verification and settlement in process.', 'state' => 'active'],
                ['step' => 3, 'title' => '03. Pending Settlement', 'description' => 'Final credit confirmation pending.', 'state' => 'pending'],
            ],
        ],
        'ACSC' => [
            'code' => 'ACSC',
            'label' => 'Accepted Settlement Completed',
            'description' => 'Settlement on the debtor / sending side has completed. Beneficiary credit may still be pending at the receiving institution.',
            'family' => 'near_final',
            'badge_class' => 'pending',
            'timeline' => [
                ['step' => 1, 'title' => '01. Initiated', 'description' => 'Payment instruction accepted into the rail.', 'state' => 'complete'],
                ['step' => 2, 'title' => '02. Clearing & Transit', 'description' => 'Interbank verification cleared.', 'state' => 'complete'],
                ['step' => 3, 'title' => '03. Settlement Completed', 'description' => 'Debtor-side settlement confirmed; beneficiary credit may follow.', 'state' => 'active'],
            ],
        ],
        'ACCC' => [
            'code' => 'ACCC',
            'label' => 'Accepted Credit Settlement Completed',
            'description' => 'Settlement on the creditor account is complete. Funds have been credited to the recipient\'s bank.',
            'family' => 'terminal_ok',
            'badge_class' => 'success',
            'timeline' => [
                ['step' => 1, 'title' => '01. Initiated', 'description' => 'Payment instruction accepted into the rail.', 'state' => 'complete'],
                ['step' => 2, 'title' => '02. Clearing & Transit', 'description' => 'Interbank verification cleared.', 'state' => 'complete'],
                ['step' => 3, 'title' => '03. Completed & Credited', 'description' => 'Beneficiary account credited.', 'state' => 'complete'],
            ],
        ],
        'AVLB' => [
            'code' => 'AVLB',
            'label' => 'Available / Ready for Pickup',
            'description' => 'Funds have arrived at the destination and are available for cash pickup or agent collection with valid ID and tracking reference.',
            'family' => 'terminal_ok',
            'badge_class' => 'success',
            'timeline' => [
                ['step' => 1, 'title' => '01. Initiated', 'description' => 'Payment instruction accepted into the rail.', 'state' => 'complete'],
                ['step' => 2, 'title' => '02. Clearing & Transit', 'description' => 'Funds routed to destination agent network.', 'state' => 'complete'],
                ['step' => 3, 'title' => '03. Ready for Pickup', 'description' => 'Available for collection with ID and tracking number.', 'state' => 'complete'],
            ],
        ],
        'HOLD' => [
            'code' => 'HOLD',
            'label' => 'Under Review / Held',
            'description' => 'The transfer is paused for compliance, AML screening, anti-fraud verification, or because beneficiary details require manual inspection.',
            'family' => 'held',
            'badge_class' => 'pending',
            'timeline' => [
                ['step' => 1, 'title' => '01. Initiated', 'description' => 'Payment instruction accepted into the rail.', 'state' => 'complete'],
                ['step' => 2, 'title' => '02. Under Review', 'description' => 'Held for compliance, AML, or data verification.', 'state' => 'active'],
                ['step' => 3, 'title' => '03. Pending Release', 'description' => 'Awaiting clearance before settlement can continue.', 'state' => 'pending'],
            ],
        ],
        'RJCT' => [
            'code' => 'RJCT',
            'label' => 'Rejected',
            'description' => 'The payment was formally declined by an intermediary or receiving bank due to compliance issues, closed accounts, or invalid routing details.',
            'family' => 'terminal_fail',
            'badge_class' => 'failed',
            'timeline' => [
                ['step' => 1, 'title' => '01. Initiated', 'description' => 'Payment instruction accepted into the rail.', 'state' => 'complete'],
                ['step' => 2, 'title' => '02. Rejected at Clearing', 'description' => 'Declined by intermediary or receiving bank.', 'state' => 'failed'],
                ['step' => 3, 'title' => '03. Terminated', 'description' => 'No further settlement will occur.', 'state' => 'failed'],
            ],
        ],
        'RETN' => [
            'code' => 'RETN',
            'label' => 'Returned',
            'description' => 'Funds were sent back down the routing chain toward the sender, usually because recipient details were wrong or the receiving bank refused the credit.',
            'family' => 'terminal_fail',
            'badge_class' => 'failed',
            'timeline' => [
                ['step' => 1, 'title' => '01. Initiated', 'description' => 'Payment instruction accepted into the rail.', 'state' => 'complete'],
                ['step' => 2, 'title' => '02. Return Initiated', 'description' => 'Receiving bank refused credit; return in progress.', 'state' => 'failed'],
                ['step' => 3, 'title' => '03. Returned to Sender', 'description' => 'Funds routed back toward the originating account.', 'state' => 'failed'],
            ],
        ],
        'CNCL' => [
            'code' => 'CNCL',
            'label' => 'Cancelled / Refunded',
            'description' => 'The transfer was stopped by sender request or automatically by the system, and funds are being routed back or credited to the sender.',
            'family' => 'terminal_fail',
            'badge_class' => 'failed',
            'timeline' => [
                ['step' => 1, 'title' => '01. Initiated', 'description' => 'Payment instruction accepted into the rail.', 'state' => 'complete'],
                ['step' => 2, 'title' => '02. Cancellation Accepted', 'description' => 'Recall or cancellation processed.', 'state' => 'failed'],
                ['step' => 3, 'title' => '03. Refunded', 'description' => 'Funds returned or credited to the sender.', 'state' => 'failed'],
            ],
        ],
    ];
}

function tracker_status_codes(): array
{
    return array_keys(tracker_status_catalog());
}

function tracker_status_normalize(?string $code, string $default = 'PDNG'): string
{
    $code = strtoupper(trim((string) $code));
    $catalog = tracker_status_catalog();
    if ($code !== '' && isset($catalog[$code])) {
        return $code;
    }
    $default = strtoupper(trim($default));
    return isset($catalog[$default]) ? $default : 'PDNG';
}

function tracker_status_meta(string $code): array
{
    $code = tracker_status_normalize($code);
    return tracker_status_catalog()[$code];
}

function tracker_status_options_html(?string $selected = null): string
{
    $selected = tracker_status_normalize($selected ?? 'PDNG');
    $html = '';
    foreach (tracker_status_catalog() as $code => $meta) {
        $sel = $code === $selected ? ' selected' : '';
        $html .= '<option value="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>'
            . htmlspecialchars($code . ' — ' . $meta['label'], ENT_QUOTES, 'UTF-8')
            . '</option>';
    }
    return $html;
}
