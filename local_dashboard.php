<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$logoUrl = 'https://lh3.googleusercontent.com/aida/AP1WRLvhokjFDu6qYj6dVduoYJnLfG5t89iSCEgECKyN-t8IDzK0Fdw42m7A_q66Iy6j2A2qvFJ4cLAngjtlkZQTOPInJ84ykd5znTULXFKtt11AcPpyOY57--4EXCxRrdEMJaYQid8yaOFG2rnzdmq3MffpLatLCNfu3sBs2RpnkAdIdyeTBnlmm_zNAZLH3IqBvJR0DrBLiRBL7nVe_dtWUeWTdetVyoM31s8NhND9TW_p_-u-b1qTU_K_A8Y';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Local Transfer | Elysium Server</title>
<style>
@layer base {
  html, body { margin: 0; padding: 0; }
  body { overscroll-behavior: none; }
}
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-thumb { background: #c4c5d5; border-radius: 4px; }
.app-sidebar, .app-titlebar {
  background: #ffffff;
}
.app-control:focus {
  outline: none;
  border-color: #1e40af;
  box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.12);
}
.money {
  font-variant-numeric: tabular-nums;
  letter-spacing: -0.02em;
}
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
tailwind.config = {
  theme: {
    extend: {
      colors: {
        'error': '#ba1a1a',
        'on-secondary-container': '#5c647a',
        'error-container': '#ffdad6',
        'on-secondary': '#ffffff',
        'on-primary-fixed-variant': '#173bab',
        'surface-bright': '#f7f9fb',
        'on-primary': '#ffffff',
        'outline': '#757684',
        'surface-variant': '#e0e3e5',
        'on-error': '#ffffff',
        'on-surface': '#191c1e',
        'on-primary-container': '#a8b8ff',
        'surface-container-low': '#f2f4f6',
        'outline-variant': '#c4c5d5',
        'secondary': '#565e74',
        'on-error-container': '#93000a',
        'surface-container-highest': '#e0e3e5',
        'on-surface-variant': '#444653',
        'inverse-primary': '#b8c4ff',
        'surface-container-lowest': '#ffffff',
        'inverse-surface': '#2d3133',
        'inverse-on-surface': '#eff1f3',
        'border-subtle': '#E2E8F0',
        'surface-container-high': '#e6e8ea',
        'surface-container': '#eceef0',
        'on-primary-fixed': '#001453',
        'surface': '#f7f9fb',
        'primary-container': '#1e40af',
        'primary-fixed': '#dde1ff',
        'primary': '#00288e',
        'on-background': '#191c1e',
        'background': '#f7f9fb',
        'money': '#006c49',
        'money-soft': '#ecfdf5',
      },
      borderRadius: {
        DEFAULT: '0.5rem',
        lg: '0.75rem',
        xl: '1rem',
        full: '9999px',
      },
      spacing: {
        gutter: '24px',
        'margin-desktop': '28px',
        'container-max': '1440px',
        'sidebar-w': '248px',
      },
      fontFamily: {
        sans: ['Manrope', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
      },
    },
  },
};
</script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet"/>
</head>
<body class="bg-surface font-sans text-on-surface antialiased min-h-screen">

<aside class="app-sidebar fixed left-0 top-0 h-full w-sidebar-w border-r border-border-subtle z-50 flex flex-col">
  <div class="h-14 px-5 flex items-center gap-3 border-b border-border-subtle shrink-0">
    <img alt="Logo" class="h-8 w-auto object-contain" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"/>
    <div class="min-w-0">
      <div class="text-sm font-semibold text-on-surface leading-tight">Operations</div>
      <div class="text-xs text-on-surface-variant">Local transfers</div>
    </div>
  </div>
  <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
    <a class="flex items-center px-3 h-10 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="#overview">
      <span class="material-symbols-outlined mr-3 text-[20px]">grid_view</span>Overview
    </a>
    <a aria-current="page" class="flex items-center px-3 h-10 rounded-lg text-sm font-semibold bg-primary text-on-primary" href="#transfer">
      <span class="material-symbols-outlined mr-3 text-[20px]">account_balance_wallet</span>Local Transfer
    </a>
    <a class="flex items-center px-3 h-10 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="#logs">
      <span class="material-symbols-outlined mr-3 text-[20px]">terminal</span>Activity Log
    </a>
    <a class="flex items-center px-3 h-10 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="/transfer_selection.php">
      <span class="material-symbols-outlined mr-3 text-[20px]">tune</span>Session
    </a>
  </nav>
  <div class="px-4 py-4 border-t border-border-subtle flex items-center gap-3 shrink-0">
    <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center">
      <span class="material-symbols-outlined text-[18px]">person</span>
    </div>
    <div class="flex flex-col min-w-0">
      <span class="text-sm font-semibold text-on-surface truncate">Operator</span>
      <span class="text-xs text-money font-medium">Online</span>
    </div>
  </div>
</aside>

<div class="pl-sidebar-w min-h-screen flex flex-col">
  <header class="app-titlebar sticky top-0 h-14 border-b border-border-subtle z-40 flex items-center justify-between px-margin-desktop">
    <div class="flex items-center gap-2 text-sm text-on-surface-variant">
      <span class="material-symbols-outlined text-[18px]">dns</span>
      <span class="font-medium text-on-surface">Main server</span>
      <span class="text-outline">·</span>
      <span>Node 04</span>
    </div>
    <div class="flex items-center gap-3">
      <div class="flex items-center gap-2 px-3 h-8 bg-money-soft rounded-full border border-emerald-100">
        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
        <span class="text-xs font-semibold text-money">14 ms latency</span>
      </div>
      <button type="button" id="logoutBtn" class="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-red-50 hover:text-error transition-colors" aria-label="Sign out" title="Sign out">
        <span class="material-symbols-outlined text-[18px]">power_settings_new</span>
        <span class="hidden sm:inline">Sign out</span>
      </button>
    </div>
  </header>

  <main class="relative flex-1">
    <div id="overview" class="relative z-10 w-full max-w-container-max mx-auto px-margin-desktop py-7 flex flex-col gap-6">
      <section class="flex flex-col md:flex-row md:items-end justify-between gap-5">
        <div class="flex flex-col gap-2">
          <h1 class="text-3xl font-bold tracking-tight text-on-surface">Transfer dashboard</h1>
          <p class="text-sm text-on-surface-variant max-w-lg">Send local settlements, monitor rail health, and review recent transactions.</p>
        </div>
        <div id="totalBalanceWrap" class="hidden flex flex-col md:items-end bg-white border border-border-subtle rounded-xl px-5 py-4 shadow-sm min-w-[240px]">
          <span class="text-xs font-semibold uppercase tracking-wide text-on-surface-variant mb-1">Available balance</span>
          <div id="totalBalanceDisplay" class="money text-[28px] leading-8 font-bold text-primary">—</div>
        </div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <div id="transfer" class="lg:col-span-5 flex flex-col min-h-[420px]">
          <div class="bg-white border border-border-subtle rounded-xl shadow-sm flex flex-col h-full overflow-hidden">
            <div class="px-5 py-4 border-b border-border-subtle shrink-0">
              <h2 class="text-lg font-semibold text-on-surface">Local transfer</h2>
              <p id="transferSubtitle" class="text-sm text-on-surface-variant mt-0.5">Send money to any Nigeria bank account</p>
            </div>
            <div class="px-4 py-4 grid grid-cols-2 sm:grid-cols-3 gap-2.5 flex-1 content-start">
              <button type="button" id="openSendBtn" class="aspect-square min-h-[72px] rounded-md bg-gradient-to-br from-[#00288e] to-[#1e40af] text-white text-[11px] font-semibold hover:opacity-95 transition-opacity inline-flex flex-col items-center justify-center gap-1 px-1.5 text-center shadow-sm">
                <span class="material-symbols-outlined text-[20px]">send</span>
                Send
              </button>
              <button type="button" id="openAddFundsBtn" class="aspect-square min-h-[72px] rounded-md bg-[#eef1f4] text-slate-600 text-[11px] font-semibold hover:bg-[#e4e8ec] transition-colors inline-flex flex-col items-center justify-center gap-1 px-1.5 text-center">
                <span class="material-symbols-outlined text-[20px]">add_card</span>
                Add funds
              </button>
              <button type="button" id="scrollTxBtn" class="aspect-square min-h-[72px] rounded-md bg-[#eef1f4] text-slate-600 text-[11px] font-semibold hover:bg-[#e4e8ec] transition-colors inline-flex flex-col items-center justify-center gap-1 px-1.5 text-center">
                <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                Transactions
              </button>
              <a href="/transfer_selection.php" class="aspect-square min-h-[72px] rounded-md bg-[#eef1f4] text-slate-600 text-[11px] font-semibold hover:bg-[#e4e8ec] transition-colors inline-flex flex-col items-center justify-center gap-1 px-1.5 text-center">
                <span class="material-symbols-outlined text-[20px]">tune</span>
                Session
              </a>
              <button type="button" id="verifyLogBtn" class="aspect-square min-h-[72px] rounded-md bg-[#eef1f4] text-slate-600 text-[11px] font-semibold hover:bg-[#e4e8ec] transition-colors inline-flex flex-col items-center justify-center gap-1 px-1.5 text-center">
                <span class="material-symbols-outlined text-[20px]">fact_check</span>
                Verify log
              </button>
              <button type="button" id="blockTransferBtn" class="aspect-square min-h-[72px] rounded-md bg-[#eef1f4] text-slate-600 text-[11px] font-semibold hover:bg-[#e4e8ec] transition-colors inline-flex flex-col items-center justify-center gap-1 px-1.5 text-center">
                <span class="material-symbols-outlined text-[20px]">block</span>
                Block transfer
              </button>
              <button type="button" id="tracePaymentsBtn" class="aspect-square min-h-[72px] rounded-md bg-[#eef1f4] text-slate-600 text-[11px] font-semibold hover:bg-[#e4e8ec] transition-colors inline-flex flex-col items-center justify-center gap-1 px-1.5 text-center">
                <span class="material-symbols-outlined text-[20px]">timeline</span>
                Trace payments
              </button>
              <button type="button" id="linkedAccountBtn" class="aspect-square min-h-[72px] rounded-md bg-[#eef1f4] text-slate-600 text-[11px] font-semibold hover:bg-[#e4e8ec] transition-colors inline-flex flex-col items-center justify-center gap-1 px-1.5 text-center">
                <span class="material-symbols-outlined text-[20px]">link</span>
                Linked account
              </button>
              <button type="button" id="panelLogoutBtn" class="aspect-square min-h-[72px] rounded-md bg-red-600 text-white text-[11px] font-semibold hover:bg-red-700 transition-colors inline-flex flex-col items-center justify-center gap-1 px-1.5 text-center">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                Sign out
              </button>
            </div>
          </div>
        </div>

        <div id="settlements" class="lg:col-span-7 flex flex-col min-h-[420px]">
          <div class="bg-white border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-5 py-4 border-b border-border-subtle flex justify-between items-center gap-3 shrink-0">
              <div>
                <h2 class="text-base font-semibold text-on-surface">Recent settlements</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">Transfers submitted from this console</p>
              </div>
              <button type="button" id="localTxRefreshBtn" class="h-9 px-3.5 text-sm font-semibold text-primary border border-border-subtle hover:bg-surface rounded-lg transition-colors">
                Refresh
              </button>
            </div>
            <div class="overflow-auto flex-1 min-h-0">
              <table class="w-full text-left border-collapse min-w-[640px]">
                <thead class="sticky top-0 z-[1]">
                  <tr class="bg-surface border-b border-border-subtle">
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant">Reference</th>
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant">Beneficiary</th>
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant">Bank</th>
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant text-right">Amount</th>
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant">Date</th>
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant text-center">Status</th>
                  </tr>
                </thead>
                <tbody id="localTxTbody" class="text-sm text-on-surface divide-y divide-border-subtle"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col min-h-[280px]">
          <div class="px-5 py-4 border-b border-border-subtle flex justify-between items-center gap-3 shrink-0">
            <div>
              <h2 class="text-base font-semibold text-on-surface">Rail health</h2>
              <p class="text-xs text-on-surface-variant mt-0.5">Hash rail / ASIC dispatch</p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-money bg-money-soft border border-emerald-100 px-2.5 py-1 rounded-full">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              Live
            </span>
          </div>
          <div class="overflow-auto flex-1 min-h-0">
            <table class="w-full text-left border-collapse min-w-[480px]" id="localMetricsTable">
              <thead>
                <tr class="bg-surface border-b border-border-subtle">
                  <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant">Worker</th>
                  <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant text-right">Accepted</th>
                  <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant text-right">Rejected</th>
                  <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant text-right">GH/s</th>
                  <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant text-center">Status</th>
                </tr>
              </thead>
              <tbody id="localMetricsTbody" class="text-sm text-on-surface divide-y divide-border-subtle"></tbody>
            </table>
          </div>
        </div>

        <div id="logs" class="bg-white border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col min-h-[280px]">
          <div class="px-5 py-4 border-b border-border-subtle flex justify-between items-center shrink-0">
            <div>
              <h2 class="text-base font-semibold text-on-surface">Activity log</h2>
              <p class="text-xs text-on-surface-variant mt-0.5">Live protocol stream</p>
            </div>
            <span class="text-xs font-medium text-on-surface-variant">Updating</span>
          </div>
          <div id="localProtocolStream" class="p-4 overflow-y-auto font-mono text-[12px] leading-6 text-on-surface flex-1 flex flex-col gap-1 bg-slate-50/60 min-h-0"></div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Send Money Modal -->
<div id="sendModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 px-4 py-6" aria-hidden="true">
  <div class="bg-white border border-border-subtle rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
    <div class="px-5 py-4 border-b border-border-subtle sticky top-0 bg-white z-10">
      <h3 id="sendModalTitle" class="text-lg font-semibold text-on-surface">Send Money</h3>
    </div>
    <div class="px-5 py-5">

      <!-- Step 1: Destination Account -->
      <div id="sendStep1" class="flex flex-col gap-4">
        <div class="flex flex-col gap-1.5">
          <label for="bankSelect" class="text-sm font-medium text-on-surface">Destination Bank</label>
          <select id="bankSelect" class="app-control w-full h-12 px-3 bg-white border border-border-subtle rounded-lg text-sm text-on-surface">
            <option value="">Select Institution...</option>
          </select>
        </div>
        <div class="flex flex-col gap-1.5">
          <label for="accountNumber" class="text-sm font-medium text-on-surface">Account Number</label>
          <input type="text" id="accountNumber" name="beneficiary_account" inputmode="numeric" maxlength="10" placeholder="Enter 10-digit account number" autocomplete="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-1p-ignore="true" class="app-control w-full h-12 px-3 bg-white border border-border-subtle rounded-lg text-sm text-on-surface font-mono"/>
        </div>
        <div id="resolveRow" class="hidden flex flex-col gap-1.5 min-h-[24px]">
          <div id="resolveSpinner" class="hidden w-4 h-4 border-2 border-money/20 border-t-money rounded-full" style="animation: spin 0.8s linear infinite;"></div>
          <div id="resolvedNameBlock" class="hidden flex flex-col gap-0.5">
            <span class="text-sm font-medium text-black">Account Name</span>
            <p id="resolvedNameDisplay" class="text-sm font-semibold text-money"></p>
          </div>
          <p id="resolveError" class="text-xs text-error hidden"></p>
        </div>
        <div class="flex gap-3 mt-1">
          <button type="button" id="cancelStep1Btn" class="flex-1 h-11 rounded-lg bg-error text-on-error text-sm font-semibold hover:bg-red-700 transition-colors">
            Cancel
          </button>
          <button type="button" id="syncAccountBtn" class="flex-1 h-11 rounded-lg bg-money text-white text-sm font-semibold hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
            Sync Account
          </button>
        </div>
      </div>

      <!-- Step 2: Looking up (5s UI only) -->
      <div id="sendStep2" class="hidden flex flex-col items-center text-center py-8 gap-3">
        <div class="w-10 h-10 border-[3px] border-money/15 border-t-money rounded-full" style="animation: spin 0.8s linear infinite;"></div>
        <p class="text-lg font-semibold text-on-surface">Looking up account</p>
        <p class="text-sm text-on-surface-variant">Please wait while we verify your account number.</p>
      </div>

      <!-- Step 3: Synced Successfully -->
      <div id="sendStep3" class="hidden flex flex-col gap-4">
        <div class="flex flex-col items-center text-center gap-2 py-2">
          <div class="w-12 h-12 rounded-full bg-money-soft flex items-center justify-center">
            <span class="material-symbols-outlined text-money text-[28px]">check_circle</span>
          </div>
          <p class="text-lg font-semibold text-on-surface">Account Synced Successfully</p>
          <p class="text-sm text-on-surface-variant">Your bank account has been verified and linked to the system.</p>
        </div>
        <div class="rounded-xl bg-money-soft/70 border border-emerald-100 px-4 py-3 flex flex-col gap-2 text-sm">
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">Bank</span>
            <span id="syncCardBank" class="font-semibold text-on-surface text-right"></span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">Account Name</span>
            <span id="syncCardName" class="font-semibold text-money text-right"></span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">Account Number</span>
            <span id="syncCardAcct" class="font-semibold text-on-surface font-mono text-right"></span>
          </div>
        </div>
        <div class="flex gap-3 mt-1">
          <button type="button" id="proceedPaymentBtn" class="flex-1 h-11 rounded-lg bg-money text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
            Proceed to Payment
          </button>
        </div>
      </div>

      <!-- Step 4: Payment Information -->
      <div id="sendStep4" class="hidden flex flex-col gap-4">
        <div class="rounded-xl bg-surface-container border border-border-subtle px-4 py-3 flex flex-col gap-2 text-sm">
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">Bank</span>
            <span id="payCardBank" class="font-semibold text-on-surface text-right"></span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">Account Name</span>
            <span id="payCardName" class="font-semibold text-on-surface text-right"></span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">Account Number</span>
            <span id="payCardAcct" class="font-semibold text-on-surface font-mono text-right"></span>
          </div>
        </div>
        <div class="flex flex-col gap-1.5">
          <label for="amount" id="amountLabel" class="text-sm font-medium text-on-surface">Amount</label>
          <div class="relative">
            <span id="currencySymbol" class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-on-surface-variant"></span>
            <input type="number" id="amount" min="100" step="0.01" placeholder="0.00" class="app-control w-full h-12 pl-8 pr-3 bg-white border border-border-subtle rounded-lg text-sm text-on-surface money"/>
          </div>
        </div>
        <div class="flex flex-col gap-1.5">
          <label for="remark" class="text-sm font-medium text-on-surface">Narration</label>
          <input type="text" id="remark" placeholder="Optional remark" maxlength="120" class="app-control w-full h-12 px-3 bg-white border border-border-subtle rounded-lg text-sm text-on-surface"/>
        </div>
        <p id="amountError" class="text-xs text-error hidden"></p>
        <div class="flex gap-3 mt-1">
          <button type="button" id="cancelStep4Btn" class="flex-1 h-11 rounded-lg bg-error text-on-error text-sm font-semibold hover:bg-red-700 transition-colors">
            Cancel
          </button>
          <button type="button" id="sendPaymentBtn" class="flex-1 h-11 rounded-lg bg-money text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
            Send
          </button>
        </div>
      </div>

      <!-- Step 5a: shared wait/loader -->
      <div id="sendStep5a" class="hidden flex flex-col items-center text-center py-8 gap-3">
        <div class="w-10 h-10 border-[3px] border-money/15 border-t-money rounded-full" style="animation: spin 0.8s linear infinite;"></div>
        <p id="waitStepTitle" class="text-lg font-semibold text-on-surface">Processing</p>
        <p id="waitStepBody" class="text-sm text-on-surface-variant">Please wait…</p>
      </div>

      <!-- Step 5b: Enter Wallet PIN -->
      <div id="sendStep5b" class="hidden flex flex-col gap-4">
        <div class="flex flex-col items-center text-center gap-2">
          <div class="w-14 h-14 rounded-full bg-money-soft flex items-center justify-center">
            <span class="material-symbols-outlined text-money text-[28px]">key</span>
          </div>
          <p class="text-lg font-semibold text-on-surface">Enter Wallet PIN</p>
          <p class="text-sm text-on-surface-variant">Enter your 6 digit wallet PIN to confirm this transaction.</p>
        </div>
        <div class="rounded-xl bg-surface-container border border-border-subtle px-4 py-3 flex flex-col gap-2 text-sm">
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">To</span>
            <span id="pinSummaryTo" class="font-semibold text-on-surface text-right"></span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">Amount</span>
            <span id="pinSummaryAmount" class="font-semibold text-money money text-right"></span>
          </div>
        </div>
        <div class="flex flex-col gap-1.5">
          <input type="password" id="pinInput" name="wallet_pin" placeholder="••••••" inputmode="numeric" maxlength="6" autocomplete="one-time-code" autocapitalize="off" spellcheck="false" data-lpignore="true" data-1p-ignore="true" class="app-control w-full h-12 px-3 bg-white border border-border-subtle rounded-lg text-center tracking-[0.35em] font-mono text-base text-on-surface"/>
          <p id="pinError" class="text-xs text-error hidden"></p>
        </div>
        <div class="flex gap-3 mt-1">
          <button type="button" id="cancelPinBtn" class="flex-1 h-11 rounded-lg bg-error text-on-error text-sm font-semibold hover:bg-red-700 transition-colors">
            Cancel
          </button>
          <button type="button" id="confirmPinBtn" class="flex-1 h-11 rounded-lg bg-money text-white text-sm font-semibold hover:bg-emerald-700 transition-colors disabled:opacity-50">
            Confirm
          </button>
        </div>
      </div>

      <!-- Step 6: Result -->
      <div id="sendStep6" class="hidden flex flex-col items-center text-center py-6 gap-3">
        <div id="resultIconWrap" class="w-12 h-12 rounded-full flex items-center justify-center bg-money-soft">
          <span id="resultIcon" class="material-symbols-outlined text-money text-[28px]">check_circle</span>
        </div>
        <p id="resultTitle" class="text-lg font-semibold text-on-surface">Transfer status</p>
        <p id="resultBody" class="text-sm text-on-surface-variant"></p>
        <div class="flex gap-3 mt-2 w-full">
          <button type="button" id="resultCloseBtn" class="flex-1 h-11 rounded-lg bg-primary text-on-primary text-sm font-semibold hover:bg-primary/90 transition-colors">
            Close
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Add Funds Modal (stub) -->
<div id="addFundsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 px-4 py-6" aria-hidden="true">
  <div class="bg-white border border-border-subtle rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
    <div class="px-5 py-4 border-b border-border-subtle">
      <h3 class="text-lg font-semibold text-on-surface">Add funds</h3>
    </div>
    <div class="px-5 py-6 flex flex-col gap-4">
      <p class="text-sm text-on-surface-variant text-center">Coming soon</p>
      <div class="flex gap-3">
        <button type="button" id="cancelAddFundsBtn" class="flex-1 h-11 rounded-lg bg-error text-on-error text-sm font-semibold hover:bg-red-700 transition-colors">
          Cancel
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Stub feature modal -->
<div id="stubFeatureModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 px-4 py-6" aria-hidden="true">
  <div class="bg-white border border-border-subtle rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
    <div class="px-5 py-4 border-b border-border-subtle">
      <h3 id="stubFeatureTitle" class="text-lg font-semibold text-on-surface">Feature</h3>
    </div>
    <div class="px-5 py-6 flex flex-col gap-4">
      <p id="stubFeatureBody" class="text-sm text-on-surface-variant text-center">Coming soon</p>
      <div class="flex gap-3">
        <button type="button" id="stubFeatureCloseBtn" class="flex-1 h-11 rounded-lg bg-error text-on-error text-sm font-semibold hover:bg-red-700 transition-colors">
          Cancel
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Linked accounts modal -->
<div id="linkedAccountsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 px-4 py-6" aria-hidden="true">
  <div class="bg-white border border-border-subtle rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">
    <div class="px-5 py-4 border-b border-border-subtle shrink-0">
      <h3 class="text-lg font-semibold text-on-surface">Linked accounts</h3>
      <p class="text-xs text-on-surface-variant mt-1">Accounts verified and linked from this console</p>
    </div>
    <div id="linkedAccountsList" class="px-5 py-4 overflow-y-auto flex-1 min-h-0 space-y-2">
      <p class="text-sm text-on-surface-variant text-center py-6">No linked accounts yet</p>
    </div>
    <div class="px-5 py-4 border-t border-border-subtle shrink-0">
      <div class="flex gap-3">
        <button type="button" id="linkedAccountsCloseBtn" class="flex-1 h-11 rounded-lg bg-error text-on-error text-sm font-semibold hover:bg-red-700 transition-colors">
          Cancel
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const CODEX = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz@#$%*?';

  function gibToken(len) {
    let s = '';
    for (let i = 0; i < len; i++) s += CODEX[Math.floor(Math.random() * CODEX.length)];
    return s;
  }

  function pad2(n) {
    return String(n).padStart(2, '0');
  }

  function stamp() {
    const d = new Date();
    return pad2(d.getHours()) + ':' + pad2(d.getMinutes()) + ':' + pad2(d.getSeconds()) + '.' + String(d.getMilliseconds()).padStart(3, '0');
  }

  function randomProtocolLine() {
    const kinds = [
      { tag: '[INFO]', cls: 'text-primary', msg: 'Handshake established with upstream node ' + (Math.floor(Math.random() * 200) + 20) + '.' + (Math.floor(Math.random() * 200) + 1) + '.' + (Math.floor(Math.random() * 200) + 1) + '.' + (Math.floor(Math.random() * 200) + 1) + ':443' },
      { tag: '[INFO]', cls: 'text-primary', msg: 'Verifying ledger integrity... OK' },
      { tag: '[WARN]', cls: 'text-amber-500', msg: 'Slight latency detected on routing layer B, rerouting packet TR-' + gibToken(4) },
      { tag: '[INFO]', cls: 'text-primary', msg: 'Block #' + (800000 + Math.floor(Math.random() * 90000)) + ' mined. Hash: 0x' + gibToken(4) + '...' + gibToken(4) },
      { tag: '[INFO]', cls: 'text-primary', msg: 'Awaiting incoming TX payloads...' },
      { tag: '[INFO]', cls: 'text-primary', msg: 'TX_RECV: Settlement requested. Ref: ELY-' + gibToken(6).toUpperCase() },
      { tag: '[INFO]', cls: 'text-primary', msg: 'Executing rail constraints... Passed.' },
      { tag: '[INFO]', cls: 'text-primary', msg: '0x' + gibToken(6) + ' SYN :: ' + gibToken(10) + '...' + gibToken(8) },
    ];
    return kinds[Math.floor(Math.random() * kinds.length)];
  }

  function rnd(min, max) {
    return Math.floor(min + Math.random() * (max - min + 1));
  }

  function fmtNum(n) {
    return n.toLocaleString('en-US');
  }

  function fillMetricsTable() {
    const tbody = document.getElementById('localMetricsTbody');
    if (!tbody) return;
    const rowCount = 4;
    const rows = tbody.querySelectorAll('tr');
    for (let i = 0; i < rowCount; i++) {
      const accepted = rnd(12000, 16000);
      const rejected = rnd(0, 8);
      const ghs = (rnd(9800, 11000) / 100).toFixed(2);
      let tr = rows[i];
      if (!tr) {
        tr = document.createElement('tr');
        tr.className = 'hover:bg-surface transition-colors';
        for (let c = 0; c < 5; c++) tr.appendChild(document.createElement('td'));
        tbody.appendChild(tr);
      }
      const cells = tr.querySelectorAll('td');
      cells[0].className = 'py-3 px-4 font-mono text-[13px] text-primary font-medium';
      cells[0].textContent = 'ASIC-NODE-0' + (i + 1) + '.ELY';
      cells[1].className = 'py-3 px-4 text-right money';
      cells[1].textContent = fmtNum(accepted);
      cells[2].className = 'py-3 px-4 text-right money text-error';
      cells[2].textContent = fmtNum(rejected);
      cells[3].className = 'py-3 px-4 text-right money';
      cells[3].textContent = ghs;
      cells[4].className = 'py-3 px-4 text-center';
      cells[4].innerHTML = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-money-soft text-money border border-emerald-100">Synced</span>';
    }
  }

  function appendProtocolLine() {
    const container = document.getElementById('localProtocolStream');
    if (!container) return;
    const item = randomProtocolLine();
    const line = document.createElement('div');
    line.className = 'flex gap-3';
    line.innerHTML =
      '<span class="text-on-surface-variant shrink-0">' + stamp() + '</span>' +
      '<span class="' + item.cls + ' shrink-0">' + item.tag + '</span>' +
      '<span>' + item.msg + '</span>';
    container.appendChild(line);
    while (container.childElementCount > 40) container.removeChild(container.firstChild);
    container.scrollTop = container.scrollHeight;
  }

  setInterval(fillMetricsTable, 2000);
  setInterval(appendProtocolLine, 800);
  fillMetricsTable();
  for (let i = 0; i < 8; i++) appendProtocolLine();

  const sendModal = document.getElementById('sendModal');
  const sendModalTitle = document.getElementById('sendModalTitle');
  const sendStep1 = document.getElementById('sendStep1');
  const sendStep2 = document.getElementById('sendStep2');
  const sendStep3 = document.getElementById('sendStep3');
  const sendStep4 = document.getElementById('sendStep4');
  const sendStep5a = document.getElementById('sendStep5a');
  const sendStep5b = document.getElementById('sendStep5b');
  const sendStep6 = document.getElementById('sendStep6');

  const bankSelect = document.getElementById('bankSelect');
  const accountNumber = document.getElementById('accountNumber');
  const resolveRow = document.getElementById('resolveRow');
  const resolveSpinner = document.getElementById('resolveSpinner');
  const resolvedNameBlock = document.getElementById('resolvedNameBlock');
  const resolvedNameDisplay = document.getElementById('resolvedNameDisplay');
  const resolveError = document.getElementById('resolveError');
  const syncAccountBtn = document.getElementById('syncAccountBtn');
  const cancelStep1Btn = document.getElementById('cancelStep1Btn');
  const proceedPaymentBtn = document.getElementById('proceedPaymentBtn');
  const amount = document.getElementById('amount');
  const remark = document.getElementById('remark');
  const amountError = document.getElementById('amountError');
  const cancelStep4Btn = document.getElementById('cancelStep4Btn');
  const sendPaymentBtn = document.getElementById('sendPaymentBtn');
  const pinInput = document.getElementById('pinInput');
  const pinError = document.getElementById('pinError');
  const cancelPinBtn = document.getElementById('cancelPinBtn');
  const confirmPinBtn = document.getElementById('confirmPinBtn');
  const resultTitle = document.getElementById('resultTitle');
  const resultBody = document.getElementById('resultBody');
  const resultIcon = document.getElementById('resultIcon');
  const resultIconWrap = document.getElementById('resultIconWrap');
  const resultCloseBtn = document.getElementById('resultCloseBtn');
  const waitStepTitle = document.getElementById('waitStepTitle');
  const waitStepBody = document.getElementById('waitStepBody');

  const addFundsModal = document.getElementById('addFundsModal');
  const stubFeatureModal = document.getElementById('stubFeatureModal');
  const stubFeatureTitle = document.getElementById('stubFeatureTitle');
  const stubFeatureBody = document.getElementById('stubFeatureBody');
  const stubFeatureCloseBtn = document.getElementById('stubFeatureCloseBtn');
  const linkedAccountsModal = document.getElementById('linkedAccountsModal');
  const linkedAccountsList = document.getElementById('linkedAccountsList');
  const linkedAccountsCloseBtn = document.getElementById('linkedAccountsCloseBtn');
  const openSendBtn = document.getElementById('openSendBtn');
  const openAddFundsBtn = document.getElementById('openAddFundsBtn');
  const scrollTxBtn = document.getElementById('scrollTxBtn');
  const cancelAddFundsBtn = document.getElementById('cancelAddFundsBtn');
  const verifyLogBtn = document.getElementById('verifyLogBtn');
  const blockTransferBtn = document.getElementById('blockTransferBtn');
  const tracePaymentsBtn = document.getElementById('tracePaymentsBtn');
  const linkedAccountBtn = document.getElementById('linkedAccountBtn');
  const logoutBtn = document.getElementById('logoutBtn');
  const panelLogoutBtn = document.getElementById('panelLogoutBtn');
  const localTxTbody = document.getElementById('localTxTbody');
  const localTxRefreshBtn = document.getElementById('localTxRefreshBtn');
  const LINKED_ACCOUNTS_KEY = 'linkedLocalAccounts';

  let bankMap = {};
  let resolvedAccountName = '';
  let resolveSeq = 0;
  let lastResolvedKey = '';
  let syncDelayTimer = null;
  let sendDelayTimer = null;
  let stepDelayTimer = null;
  let successRedirectTimer = null;

  const selectedCountryCode = 'NG';
  const selectedCountryName = 'Nigeria';
  let selectedCurrency = sessionStorage.getItem('selectedCurrency') || 'NGN';
  if (selectedCurrency !== 'NGN' && selectedCurrency !== 'USD') {
    selectedCurrency = 'NGN';
  }
  try {
    sessionStorage.setItem('selectedCountryCode', selectedCountryCode);
    sessionStorage.setItem('selectedCountryName', selectedCountryName);
    sessionStorage.setItem('selectedCurrency', selectedCurrency);
  } catch (e) {}

  const transferSubtitle = document.getElementById('transferSubtitle');
  if (transferSubtitle) {
    transferSubtitle.textContent = 'Send money to any Nigeria bank account';
  }

  const currencySymbols = {
    'NGN': '₦',
    'USD': '$'
  };
  const currSymbol = currencySymbols[selectedCurrency] || selectedCurrency + ' ';
  const currencySymbolEl = document.getElementById('currencySymbol');
  const amountLabel = document.getElementById('amountLabel');
  if (currencySymbolEl) {
    currencySymbolEl.textContent = currSymbol;
  }
  if (amountLabel) {
    amountLabel.textContent = 'Amount (' + selectedCurrency + ')';
  }

  function redirectIfSessionExpired(res, data) {
    if (res.status === 401 && data && data.redirect) {
      window.location.href = data.redirect;
      return true;
    }
    return false;
  }

  function formatMoney(amt) {
    const num = Number(amt) || 0;
    return currSymbol + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function formatBalanceNGN(amt) {
    const num = Number(amt) || 0;
    return '₦' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function applyTotalBalanceFromPayload(payload) {
    const wrap = document.getElementById('totalBalanceWrap');
    const el = document.getElementById('totalBalanceDisplay');
    if (!wrap || !el) return;
    const profile = payload && payload.data && payload.data.profile;
    if (profile && profile.balance != null && profile.balance !== '') {
      el.textContent = formatBalanceNGN(profile.balance);
      wrap.classList.remove('hidden');
      wrap.classList.add('flex');
    } else {
      el.textContent = '—';
      wrap.classList.add('hidden');
      wrap.classList.remove('flex');
    }
  }

  async function refreshTotalBalance() {
    const wrap = document.getElementById('totalBalanceWrap');
    const el = document.getElementById('totalBalanceDisplay');
    if (!wrap || !el) return;
    try {
      const res = await fetch('/api/dashboard_content.php?page=local');
      const data = await res.json().catch(() => ({}));
      if (redirectIfSessionExpired(res, data)) return;
      if (res.ok && data.success && data.data) {
        applyTotalBalanceFromPayload(data);
      } else {
        el.textContent = '—';
        wrap.classList.add('hidden');
        wrap.classList.remove('flex');
      }
    } catch (e) {
      el.textContent = '—';
      wrap.classList.add('hidden');
      wrap.classList.remove('flex');
    }
  }

  async function loadBanks() {
    try {
      let banks = [];
      const res = await fetch('/api/dashboard_content.php?page=local');
      const data = await res.json().catch(() => ({}));
      if (redirectIfSessionExpired(res, data)) return;
      if (res.ok && data.success && data.data) {
        banks = Array.isArray(data.data.banks) ? data.data.banks : [];
        applyTotalBalanceFromPayload(data);
      }

      bankSelect.innerHTML = '<option value="">Select Institution...</option>';
      bankMap = {};
      banks.forEach(b => {
        const opt = document.createElement('option');
        opt.value = b.bank_code || '';
        opt.textContent = b.bank_name || '';
        opt.dataset.name = b.bank_name || '';
        bankSelect.appendChild(opt);
        bankMap[b.bank_code] = b.bank_name;
      });

      if (banks.length === 0) {
        bankSelect.innerHTML = '<option value="">No banks available for Nigeria</option>';
      }
    } catch (e) {
      bankSelect.innerHTML = '<option value="">Failed to load banks</option>';
    }
  }

  async function loadTransactions() {
    try {
      const res = await fetch('/api/local_transactions.php');
      const data = await res.json().catch(() => ({}));
      if (redirectIfSessionExpired(res, data)) return;

      if (!res.ok || !data.success) {
        localTxTbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-on-surface-variant">No transactions found</td></tr>';
        return;
      }

      const transactions = Array.isArray(data.transactions) ? data.transactions : [];
      if (transactions.length === 0) {
        localTxTbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-on-surface-variant">No transactions found</td></tr>';
        return;
      }

      localTxTbody.innerHTML = transactions.map(tx => {
        const ok = tx.status === 'SUCCESSFUL';
        const pending = tx.status === 'PENDING';
        const badge = ok
          ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-money-soft text-money border border-emerald-100"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Successful</span>'
          : pending
            ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-100"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span>'
            : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 border border-red-100"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>' + (tx.status || 'Failed') + '</span>';
        const date = tx.transaction_date
          ? new Date(tx.transaction_date).toLocaleString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
          : '—';
        const curr = tx.currency || selectedCurrency;
        const sym = currencySymbols[curr] || curr + ' ';
        return `
          <tr class="hover:bg-surface/80 transition-colors">
            <td class="py-3.5 px-4 font-mono text-[13px] text-on-surface-variant">${tx.reference || '—'}</td>
            <td class="py-3.5 px-4 font-medium">${tx.beneficiary_name || '—'}</td>
            <td class="py-3.5 px-4 text-on-surface-variant">${tx.bank_name || '—'}</td>
            <td class="py-3.5 px-4 money text-right font-semibold text-money">${sym}${Number(tx.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
            <td class="py-3.5 px-4 text-on-surface-variant">${date}</td>
            <td class="py-3.5 px-4 text-center">${badge}</td>
          </tr>
        `;
      }).join('');
    } catch (e) {
      localTxTbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-error">Failed to load transactions</td></tr>';
    }
  }

  const allSteps = [sendStep1, sendStep2, sendStep3, sendStep4, sendStep5a, sendStep5b, sendStep6];

  function showStep(stepEl, title) {
    allSteps.forEach(el => {
      if (!el) return;
      el.classList.add('hidden');
      el.classList.remove('flex');
    });
    if (stepEl) {
      stepEl.classList.remove('hidden');
      stepEl.classList.add('flex');
    }
    if (title && sendModalTitle) {
      sendModalTitle.textContent = title;
    }
  }

  function clearPendingTimers() {
    if (syncDelayTimer) { clearTimeout(syncDelayTimer); syncDelayTimer = null; }
    if (sendDelayTimer) { clearTimeout(sendDelayTimer); sendDelayTimer = null; }
    if (stepDelayTimer) { clearTimeout(stepDelayTimer); stepDelayTimer = null; }
    if (successRedirectTimer) { clearTimeout(successRedirectTimer); successRedirectTimer = null; }
  }

  function showWaitThen(ms, modalTitle, waitTitle, waitBody, thenFn) {
    clearPendingTimers();
    if (waitStepTitle) waitStepTitle.textContent = waitTitle || 'Please wait';
    if (waitStepBody) waitStepBody.textContent = waitBody || 'Please wait…';
    showStep(sendStep5a, modalTitle || 'Please wait');
    stepDelayTimer = setTimeout(function () {
      stepDelayTimer = null;
      if (sendModal.classList.contains('hidden')) return;
      thenFn();
    }, ms);
  }

  function waitAtLeast(ms) {
    return new Promise(function (resolve) {
      if (stepDelayTimer) { clearTimeout(stepDelayTimer); stepDelayTimer = null; }
      stepDelayTimer = setTimeout(function () {
        stepDelayTimer = null;
        resolve();
      }, ms);
    });
  }

  function resetSendState() {
    clearPendingTimers();
    resolvedAccountName = '';
    resolveSeq += 1;
    lastResolvedKey = '';
    bankSelect.value = '';
    accountNumber.value = '';
    amount.value = '';
    remark.value = '';
    pinInput.value = '';
    resolvedNameDisplay.textContent = '';
    resolvedNameBlock.classList.add('hidden');
    resolvedNameBlock.classList.remove('flex');
    resolveError.textContent = '';
    resolveError.classList.add('hidden');
    resolveSpinner.classList.add('hidden');
    resolveRow.classList.add('hidden');
    resolveRow.classList.remove('flex');
    syncAccountBtn.disabled = true;
    amountError.classList.add('hidden');
    pinError.classList.add('hidden');
    confirmPinBtn.disabled = false;
  }

  function openSendModal() {
    resetSendState();
    sendModal.classList.remove('hidden');
    sendModal.classList.add('flex');
    sendModal.setAttribute('aria-hidden', 'false');
    showStep(sendStep1, 'Send Money');
  }

  function closeSendModal() {
    sendModal.classList.add('hidden');
    sendModal.classList.remove('flex');
    sendModal.setAttribute('aria-hidden', 'true');
    resetSendState();
    showStep(sendStep1, 'Send Money');
  }

  function openAddFundsModal() {
    addFundsModal.classList.remove('hidden');
    addFundsModal.classList.add('flex');
    addFundsModal.setAttribute('aria-hidden', 'false');
  }

  function closeAddFundsModal() {
    addFundsModal.classList.add('hidden');
    addFundsModal.classList.remove('flex');
    addFundsModal.setAttribute('aria-hidden', 'true');
  }

  function openStubFeatureModal(title, body) {
    stubFeatureTitle.textContent = title || 'Feature';
    stubFeatureBody.textContent = body || 'Coming soon';
    stubFeatureModal.classList.remove('hidden');
    stubFeatureModal.classList.add('flex');
    stubFeatureModal.setAttribute('aria-hidden', 'false');
  }

  function closeStubFeatureModal() {
    stubFeatureModal.classList.add('hidden');
    stubFeatureModal.classList.remove('flex');
    stubFeatureModal.setAttribute('aria-hidden', 'true');
  }

  function readLinkedAccounts() {
    try {
      const raw = sessionStorage.getItem(LINKED_ACCOUNTS_KEY);
      const parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      return [];
    }
  }

  function writeLinkedAccounts(list) {
    try {
      sessionStorage.setItem(LINKED_ACCOUNTS_KEY, JSON.stringify(list.slice(0, 50)));
    } catch (e) {}
  }

  function accountKey(acct) {
    return String(acct.bank_code || '') + ':' + String(acct.account_number || '');
  }

  function saveLinkedAccount(entry) {
    if (!entry || !entry.account_number || !entry.account_name) return;
    const list = readLinkedAccounts();
    const key = accountKey(entry);
    const next = list.filter(a => accountKey(a) !== key);
    next.unshift({
      account_number: entry.account_number,
      account_name: entry.account_name,
      bank_code: entry.bank_code || '',
      bank_name: entry.bank_name || '',
      linked_at: entry.linked_at || new Date().toISOString()
    });
    writeLinkedAccounts(next);
  }

  function mergeLinkedFromTransactions(transactions) {
    if (!Array.isArray(transactions) || transactions.length === 0) return;
    const list = readLinkedAccounts();
    const map = {};
    list.forEach(a => { map[accountKey(a)] = a; });
    transactions.forEach(tx => {
      const acctNum = String(tx.beneficiary_account || '').replace(/\D/g, '');
      const name = tx.beneficiary_name || '';
      if (!acctNum || !name) return;
      const bankName = tx.beneficiary_bank || tx.bank_name || '';
      const entry = {
        account_number: acctNum,
        account_name: name,
        bank_code: tx.bank_code || '',
        bank_name: bankName,
        linked_at: tx.transaction_date || new Date().toISOString()
      };
      const key = accountKey(entry);
      if (!map[key]) map[key] = entry;
    });
    writeLinkedAccounts(Object.values(map));
  }

  function renderLinkedAccountsList() {
    const list = readLinkedAccounts();
    if (!linkedAccountsList) return;
    if (list.length === 0) {
      linkedAccountsList.innerHTML = '<p class="text-sm text-on-surface-variant text-center py-6">No linked accounts yet</p>';
      return;
    }
    linkedAccountsList.innerHTML = list.map(a => `
      <div class="border border-border-subtle rounded-lg px-4 py-3 bg-surface">
        <div class="text-sm font-semibold text-on-surface">${a.account_name || '—'}</div>
        <div class="text-xs text-on-surface-variant mt-1">${a.bank_name || 'Bank'} · <span class="font-mono">${a.account_number || '—'}</span></div>
      </div>
    `).join('');
  }

  async function openLinkedAccountsModal() {
    try {
      const res = await fetch('/api/local_transactions.php?limit=100');
      const data = await res.json().catch(() => ({}));
      if (!redirectIfSessionExpired(res, data) && res.ok && data.success) {
        mergeLinkedFromTransactions(Array.isArray(data.transactions) ? data.transactions : []);
      }
    } catch (e) {}
    renderLinkedAccountsList();
    linkedAccountsModal.classList.remove('hidden');
    linkedAccountsModal.classList.add('flex');
    linkedAccountsModal.setAttribute('aria-hidden', 'false');
  }

  function closeLinkedAccountsModal() {
    linkedAccountsModal.classList.add('hidden');
    linkedAccountsModal.classList.remove('flex');
    linkedAccountsModal.setAttribute('aria-hidden', 'true');
  }

  async function signOut() {
    try {
      await fetch('/api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'logout' })
      });
    } finally {
      window.location.href = '/';
    }
  }

  function clearResolveUi() {
    resolvedAccountName = '';
    lastResolvedKey = '';
    resolvedNameDisplay.textContent = '';
    resolvedNameBlock.classList.add('hidden');
    resolvedNameBlock.classList.remove('flex');
    resolveError.textContent = '';
    resolveError.classList.add('hidden');
    syncAccountBtn.disabled = true;
  }

  async function resolveAccount() {
    const acctNum = (accountNumber.value || '').replace(/\D/g, '');
    const bankCode = bankSelect.value || '';
    const key = bankCode + ':' + acctNum;

    if (acctNum.length < 10 || !bankCode) {
      clearResolveUi();
      resolveRow.classList.add('hidden');
      resolveRow.classList.remove('flex');
      return;
    }

    if (lastResolvedKey === key && resolvedAccountName) return;

    const seq = ++resolveSeq;
    clearResolveUi();
    resolveRow.classList.remove('hidden');
    resolveRow.classList.add('flex');
    resolveSpinner.classList.remove('hidden');

    try {
      const res = await fetch('/api/resolve_account.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ account_number: acctNum, bank_code: bankCode })
      });
      const data = await res.json().catch(() => ({}));
      if (seq !== resolveSeq) return;
      if (redirectIfSessionExpired(res, data)) return;

      if (!res.ok || !data.success || !data.data || !data.data.account_name) {
        throw new Error(data.message || 'Unable to resolve account');
      }

      resolvedAccountName = data.data.account_name;
      lastResolvedKey = key;
      resolvedNameDisplay.textContent = resolvedAccountName;
      resolvedNameBlock.classList.remove('hidden');
      resolvedNameBlock.classList.add('flex');
      resolveError.classList.add('hidden');
      syncAccountBtn.disabled = false;
    } catch (err) {
      if (seq !== resolveSeq) return;
      resolvedAccountName = '';
      lastResolvedKey = '';
      resolvedNameBlock.classList.add('hidden');
      resolvedNameBlock.classList.remove('flex');
      resolveError.textContent = err.message || 'Could not verify account';
      resolveError.classList.remove('hidden');
      syncAccountBtn.disabled = true;
    } finally {
      if (seq === resolveSeq) {
        resolveSpinner.classList.add('hidden');
      }
    }
  }

  function fillSyncedCards() {
    const bankCode = bankSelect.value || '';
    const bankName = bankMap[bankCode] || '';
    const acctNum = (accountNumber.value || '').replace(/\D/g, '');
    document.getElementById('syncCardBank').textContent = bankName || '—';
    document.getElementById('syncCardName').textContent = resolvedAccountName || '—';
    document.getElementById('syncCardAcct').textContent = acctNum || '—';
    document.getElementById('payCardBank').textContent = bankName || '—';
    document.getElementById('payCardName').textContent = resolvedAccountName || '—';
    document.getElementById('payCardAcct').textContent = acctNum || '—';
  }

  function showResult(title, body, isError) {
    showStep(sendStep6, 'Transfer status');
    resultTitle.textContent = title;
    resultBody.textContent = body;
    if (isError) {
      resultTitle.className = 'text-lg font-semibold text-error';
      resultIcon.textContent = 'error';
      resultIcon.className = 'material-symbols-outlined text-error text-[28px]';
      resultIconWrap.className = 'w-12 h-12 rounded-full flex items-center justify-center bg-red-50';
    } else {
      resultTitle.className = 'text-lg font-semibold text-money';
      resultIcon.textContent = 'check_circle';
      resultIcon.className = 'material-symbols-outlined text-money text-[28px]';
      resultIconWrap.className = 'w-12 h-12 rounded-full flex items-center justify-center bg-money-soft';
    }
  }

  accountNumber.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 10);
    if (this.value.length === 10 && bankSelect.value) {
      resolveAccount();
    } else {
      clearResolveUi();
      if (this.value.length > 0 || bankSelect.value) {
        resolveRow.classList.remove('hidden');
        resolveRow.classList.add('flex');
      }
    }
  });

  bankSelect.addEventListener('change', function () {
    lastResolvedKey = '';
    resolvedAccountName = '';
    syncAccountBtn.disabled = true;
    if ((accountNumber.value || '').replace(/\D/g, '').length === 10) {
      resolveAccount();
    } else {
      clearResolveUi();
    }
  });

  syncAccountBtn.addEventListener('click', function () {
    if (!resolvedAccountName || syncAccountBtn.disabled) return;
    const bankCode = bankSelect.value || '';
    saveLinkedAccount({
      account_number: (accountNumber.value || '').replace(/\D/g, ''),
      account_name: resolvedAccountName,
      bank_code: bankCode,
      bank_name: bankMap[bankCode] || ''
    });
    fillSyncedCards();
    clearPendingTimers();
    showStep(sendStep2, 'Send Money');
    syncDelayTimer = setTimeout(function () {
      syncDelayTimer = null;
      if (sendModal.classList.contains('hidden')) return;
      showStep(sendStep3, 'Send Money');
    }, 5000);
  });

  proceedPaymentBtn.addEventListener('click', function () {
    fillSyncedCards();
    amountError.classList.add('hidden');
    showWaitThen(2000, 'Payment Information', 'Preparing payment', 'Please wait…', function () {
      showStep(sendStep4, 'Payment Information');
    });
  });

  sendPaymentBtn.addEventListener('click', function () {
    const amt = parseFloat(amount.value) || 0;
    if (amt < 100) {
      amountError.textContent = 'Minimum amount is ' + formatMoney(100);
      amountError.classList.remove('hidden');
      return;
    }
    amountError.classList.add('hidden');
    showWaitThen(3000, 'Payment Information', 'Processing', 'Please wait…', function () {
      document.getElementById('pinSummaryTo').textContent = resolvedAccountName || '—';
      document.getElementById('pinSummaryAmount').textContent = formatMoney(amt);
      pinInput.value = '';
      pinError.classList.add('hidden');
      confirmPinBtn.disabled = false;
      showStep(sendStep5b, 'Enter Wallet PIN');
      pinInput.focus();
    });
  });

  confirmPinBtn.addEventListener('click', async function () {
    const pinVal = (pinInput.value || '').replace(/\D/g, '');
    if (!/^\d{6}$/.test(pinVal)) {
      pinError.textContent = 'Please enter your 6 digit wallet PIN';
      pinError.classList.remove('hidden');
      return;
    }

    confirmPinBtn.disabled = true;
    pinError.classList.add('hidden');

    const bankCode = bankSelect.value || '';
    const bankName = bankMap[bankCode] || '';
    const acctNum = (accountNumber.value || '').replace(/\D/g, '');
    const amt = parseFloat(amount.value) || 0;
    const remarkVal = (remark.value || '').trim();
    const finalAccountName = resolvedAccountName;

    try {
      const pinRes = await fetch('/api/wallet_pin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'validate', pin: pinVal })
      });
      const pinData = await pinRes.json().catch(() => ({}));
      if (redirectIfSessionExpired(pinRes, pinData)) return;

      if (!pinRes.ok || !pinData.success) {
        pinError.textContent = pinData.message || 'Incorrect PIN';
        pinError.classList.remove('hidden');
        confirmPinBtn.disabled = false;
        return;
      }

      if (waitStepTitle) waitStepTitle.textContent = 'Processing';
      if (waitStepBody) waitStepBody.textContent = 'Please wait…';
      showStep(sendStep5a, 'Processing');
      const minProcessWait = waitAtLeast(2000);

      const platRes = await fetch('/api/platform_status.php');
      const platData = await platRes.json().catch(() => ({}));
      if (platRes.ok && platData && platData.success && platData.status && platData.status !== 'on') {
        await minProcessWait;
        if (sendModal.classList.contains('hidden')) return;
        showResult('Transfer Failed', 'Platform is under maintenance. Please try again later.', true);
        confirmPinBtn.disabled = false;
        return;
      }

      const bankRes = await fetch('/api/bank_status.php?bank_code=' + encodeURIComponent(bankCode));
      const bankData = await bankRes.json().catch(() => ({}));
      if (bankRes.ok && bankData && bankData.success) {
        const s = (bankData.bank_status && bankData.bank_status.status) || 'full_logs';
        if (s !== 'full_logs') {
          const statusTitles = {
            weak_logs: { title: 'Weak Log Warning', detail: 'Logs are weak and might not complete a 100% transaction.' },
            pending_request: { title: 'Pending Request Detected', detail: 'No debit is performed right now for this bank.' },
            post_no_debit: { title: 'Post No Debit', detail: 'Debit transactions are temporarily restricted for this bank.' },
            fixed_account: { title: 'Fixed Account', detail: 'This bank account is fixed. Transfers are not allowed right now.' },
          };
          const mapped = statusTitles[s] || { title: 'Transfer Blocked', detail: 'Transfer blocked by bank status.' };
          await minProcessWait;
          if (sendModal.classList.contains('hidden')) return;
          showResult(mapped.title, mapped.detail, true);
          confirmPinBtn.disabled = false;
          return;
        }
      }

      const reference = 'LOC' + Date.now() + Math.floor(Math.random() * 1000);

      const txRes = await fetch('/api/local_transactions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          reference,
          bank_code: bankCode,
          bank_name: bankName,
          beneficiary_account: acctNum,
          beneficiary_name: finalAccountName,
          amount: amt,
          currency: selectedCurrency,
          remark: remarkVal
        })
      });
      const txData = await txRes.json().catch(() => ({}));
      if (redirectIfSessionExpired(txRes, txData)) return;

      if (!txRes.ok || !txData.success) {
        const bankStatus = txData.bank_status || null;
        const msg = txData.message || 'Transaction failed';
        const statusTitles = {
          weak_logs: { title: 'Weak Log Warning', detail: 'Logs are weak and might not complete a 100% transaction.' },
          pending_request: { title: 'Pending Request Detected', detail: 'No debit is performed right now for this bank.' },
          post_no_debit: { title: 'Post No Debit', detail: 'Debit transactions are temporarily restricted for this bank.' },
          fixed_account: { title: 'Fixed Account', detail: 'This bank account is fixed. Transfers are not allowed right now.' },
        };
        await minProcessWait;
        if (sendModal.classList.contains('hidden')) return;
        if (bankStatus && statusTitles[bankStatus]) {
          showResult(statusTitles[bankStatus].title, statusTitles[bankStatus].detail, true);
          confirmPinBtn.disabled = false;
          return;
        }
        if ((msg || '').toLowerCase().includes('maintenance')) {
          showResult('Platform Maintenance', 'The platform is currently under maintenance. Please try again later.', true);
          confirmPinBtn.disabled = false;
          return;
        }
        throw new Error(msg);
      }

      const txForReceipt = Object.assign({}, txData.transaction || {
        reference,
        bank_name: bankName,
        beneficiary_bank: bankName,
        beneficiary_account: acctNum,
        beneficiary_name: finalAccountName,
        amount: amt,
        currency: selectedCurrency,
        country_code: selectedCountryCode,
        country_name: selectedCountryName,
        purpose: remarkVal,
        status: 'SUCCESSFUL',
        transaction_date: new Date().toISOString()
      }, {
        bank_code: bankCode,
        bank_name: bankName,
        beneficiary_bank: (txData.transaction && txData.transaction.beneficiary_bank) || bankName
      });
      sessionStorage.setItem('lastLocalTransaction', JSON.stringify(txForReceipt));

      await minProcessWait;
      if (sendModal.classList.contains('hidden')) return;
      showResult('Transfer Successful!', 'Your transfer has been completed successfully.', false);
      successRedirectTimer = setTimeout(function () {
        successRedirectTimer = null;
        window.location.href = '/local_transfer_success.php';
      }, 1500);
    } catch (err) {
      showResult('Transfer Failed', err.message || 'Transfer failed', true);
      confirmPinBtn.disabled = false;
    }
  });

  pinInput.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 6);
  });

  cancelStep1Btn.addEventListener('click', closeSendModal);
  cancelStep4Btn.addEventListener('click', closeSendModal);
  cancelPinBtn.addEventListener('click', closeSendModal);
  resultCloseBtn.addEventListener('click', closeSendModal);

  openSendBtn.addEventListener('click', openSendModal);
  openAddFundsBtn.addEventListener('click', openAddFundsModal);
  cancelAddFundsBtn.addEventListener('click', closeAddFundsModal);

  verifyLogBtn.addEventListener('click', function () {
    openStubFeatureModal('Verify log', 'Coming soon');
  });
  blockTransferBtn.addEventListener('click', function () {
    openStubFeatureModal('Block transfer', 'Coming soon');
  });
  tracePaymentsBtn.addEventListener('click', function () {
    openStubFeatureModal('Trace payments', 'Coming soon');
  });
  stubFeatureCloseBtn.addEventListener('click', closeStubFeatureModal);

  linkedAccountBtn.addEventListener('click', openLinkedAccountsModal);
  linkedAccountsCloseBtn.addEventListener('click', closeLinkedAccountsModal);

  scrollTxBtn.addEventListener('click', function () {
    const el = document.getElementById('settlements');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  localTxRefreshBtn.addEventListener('click', function () {
    loadTransactions();
    refreshTotalBalance();
  });

  logoutBtn.addEventListener('click', signOut);
  panelLogoutBtn.addEventListener('click', signOut);

  loadBanks();
  loadTransactions();
})();
</script>
</body>
</html>
