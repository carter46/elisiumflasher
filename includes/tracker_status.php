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

/**
 * Admin documentation panel: tracker status codes with full titles and meanings.
 * Does not change per-transaction controls — reference only.
 */
function tracker_status_admin_guide_html(): string
{
    $familyLabel = static function (string $family): string {
        return match ($family) {
            'in_flight' => 'In progress',
            'near_final' => 'Near final',
            'terminal_ok' => 'Completed',
            'held' => 'Held',
            'terminal_fail' => 'Failed / closed',
            default => 'Other',
        };
    };
    $familyClass = static function (string $family): string {
        return match ($family) {
            'terminal_ok' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'terminal_fail' => 'bg-red-50 text-red-800 border-red-200',
            'held' => 'bg-amber-50 text-amber-900 border-amber-200',
            'near_final' => 'bg-sky-50 text-sky-900 border-sky-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    };

    $rows = '';
    foreach (tracker_status_catalog() as $code => $meta) {
        $family = (string) ($meta['family'] ?? '');
        $rows .= '<div class="border border-slate-200 rounded-xl p-4 bg-slate-50/60">'
            . '<div class="flex flex-wrap items-start justify-between gap-2 mb-2">'
            . '<div>'
            . '<div class="font-mono text-sm font-bold text-slate-900">' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div class="text-sm font-semibold text-slate-800 mt-0.5">' . htmlspecialchars((string) ($meta['label'] ?? ''), ENT_QUOTES, 'UTF-8') . '</div>'
            . '</div>'
            . '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold border '
            . $familyClass($family) . '">' . htmlspecialchars($familyLabel($family), ENT_QUOTES, 'UTF-8') . '</span>'
            . '</div>'
            . '<p class="text-sm text-slate-600 leading-relaxed">' . htmlspecialchars((string) ($meta['description'] ?? ''), ENT_QUOTES, 'UTF-8') . '</p>'
            . '</div>';
    }

    return '<div class="bg-white rounded-2xl shadow-sm border p-6 mt-4">'
        . '<div class="mb-4">'
        . '<h2 class="text-xl font-semibold text-slate-900">Tracker status reference</h2>'
        . '<p class="text-sm text-slate-500 mt-1 leading-relaxed">'
        . 'These codes appear in the Tracker column above and on Bolt Switch Trace. '
        . 'They are separate from receipt Status (Successful / Pending / Failed). '
        . 'Set tracker status on each transaction; this section is documentation only.'
        . '</p>'
        . '</div>'
        . '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">' . $rows . '</div>'
        . '</div>';
}
