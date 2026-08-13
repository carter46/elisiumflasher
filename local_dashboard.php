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
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}
.app-sidebar {
  transition: transform 0.25s ease;
}
@media (max-width: 1023px) {
  .app-sidebar {
    transform: translateX(-100%);
    box-shadow: 0 10px 40px rgba(15, 23, 42, 0.12);
  }
  .app-sidebar.is-open {
    transform: translateX(0);
  }
  body.sidebar-open {
    overflow: hidden;
  }
}
.sidebar-backdrop {
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s ease;
}
.sidebar-backdrop.is-open {
  opacity: 1;
  pointer-events: auto;
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

<aside id="appSidebar" class="app-sidebar fixed left-0 top-0 h-full w-sidebar-w border-r border-border-subtle z-[60] flex flex-col" aria-label="Main navigation">
  <div class="h-14 px-5 flex items-center justify-between gap-3 border-b border-border-subtle shrink-0">
    <div class="flex items-center gap-3 min-w-0">
      <img alt="Logo" class="h-8 w-auto object-contain" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"/>
      <div class="min-w-0">
        <div class="text-sm font-semibold text-on-surface leading-tight">Operations</div>
        <div class="text-xs text-on-surface-variant">Local transfers</div>
      </div>
    </div>
    <button type="button" id="sidebarCloseBtn" class="lg:hidden h-9 w-9 inline-flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" aria-label="Close menu">
      <span class="material-symbols-outlined text-[20px]">close</span>
    </button>
  </div>
  <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto" id="sidebarNav">
    <a class="sidebar-link flex items-center px-3 h-10 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="#overview">
      <span class="material-symbols-outlined mr-3 text-[20px]">grid_view</span>Overview
    </a>
    <a aria-current="page" class="sidebar-link flex items-center px-3 h-10 rounded-lg text-sm font-semibold bg-primary text-on-primary" href="#transfer">
      <span class="material-symbols-outlined mr-3 text-[20px]">account_balance_wallet</span>Local Transfer
    </a>
    <a class="sidebar-link flex items-center px-3 h-10 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="#logs">
      <span class="material-symbols-outlined mr-3 text-[20px]">terminal</span>Activity Log
    </a>
    <a class="sidebar-link flex items-center px-3 h-10 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="/transfer_selection.php">
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

<div id="sidebarBackdrop" class="sidebar-backdrop fixed inset-0 z-[55] bg-black/40 lg:hidden" aria-hidden="true"></div>

<div class="lg:pl-sidebar-w min-h-screen flex flex-col">
  <header class="app-titlebar sticky top-0 h-14 border-b border-border-subtle z-40 flex items-center justify-between px-4 sm:px-6 lg:px-margin-desktop gap-2">
    <div class="flex items-center gap-2 min-w-0">
      <button type="button" id="sidebarOpenBtn" class="lg:hidden h-9 w-9 inline-flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors shrink-0" aria-label="Open menu" aria-controls="appSidebar" aria-expanded="false">
        <span class="material-symbols-outlined text-[22px]">menu</span>
      </button>
      <div class="flex items-center gap-2 text-sm text-on-surface-variant min-w-0">
        <span class="material-symbols-outlined text-[18px] shrink-0">dns</span>
        <span class="font-medium text-on-surface truncate">Main server</span>
        <span class="text-outline hidden sm:inline">·</span>
        <span class="hidden sm:inline">Node 04</span>
      </div>
    </div>
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
      <div class="hidden xs:flex sm:flex items-center gap-2 px-2.5 sm:px-3 h-8 bg-money-soft rounded-full border border-emerald-100">
        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
        <span class="text-xs font-semibold text-money whitespace-nowrap">14 ms</span>
      </div>
      <button type="button" id="logoutBtn" class="h-9 px-2.5 sm:px-3 inline-flex items-center gap-1.5 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-red-50 hover:text-error transition-colors" aria-label="Sign out" title="Sign out">
        <span class="material-symbols-outlined text-[18px]">power_settings_new</span>
        <span class="hidden sm:inline">Sign out</span>
      </button>
    </div>
  </header>

  <main class="relative flex-1">
    <div id="overview" class="relative z-10 w-full max-w-container-max mx-auto px-4 sm:px-6 lg:px-margin-desktop py-5 sm:py-7 flex flex-col gap-5 sm:gap-6">
      <section class="flex flex-col md:flex-row md:items-end justify-between gap-4 sm:gap-5">
        <div class="flex flex-col gap-2">
          <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-on-surface">Transfer dashboard</h1>
          <p class="text-sm text-on-surface-variant max-w-lg">Send local settlements, monitor rail health, and review recent transactions.</p>
        </div>
        <div id="totalBalanceWrap" class="hidden flex flex-col md:items-end bg-white border border-border-subtle rounded-xl px-4 sm:px-5 py-3.5 sm:py-4 shadow-sm w-full md:w-auto md:min-w-[240px]">
          <div class="w-full flex items-center justify-between gap-3 mb-1">
            <span class="text-xs font-semibold uppercase tracking-wide text-on-surface-variant">Available balance</span>
            <button type="button" id="toggleBalanceBtn" class="h-7 w-7 inline-flex items-center justify-center rounded-md text-on-surface-variant hover:bg-surface hover:text-on-surface transition-colors" aria-label="Hide balance" title="Hide balance">
              <span id="toggleBalanceIcon" class="material-symbols-outlined text-[18px]">visibility</span>
            </button>
          </div>
          <div id="totalBalanceDisplay" class="money text-[24px] sm:text-[28px] leading-8 font-bold text-primary">—</div>
        </div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-stretch">
        <div id="transfer" class="lg:col-span-5 flex flex-col min-h-0 lg:min-h-[420px]">
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

        <div id="settlements" class="lg:col-span-7 flex flex-col min-h-0 lg:min-h-[420px]">
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
              <table class="w-full text-left border-collapse min-w-[480px]">
                <thead class="sticky top-0 z-[1]">
                  <tr class="bg-surface border-b border-border-subtle">
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant">Beneficiary</th>
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant">Bank</th>
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant text-right">Amount</th>
                    <th class="py-3 px-4 text-xs font-semibold text-on-surface-variant text-center">Status</th>
                  </tr>
                </thead>
                <tbody id="localTxTbody" class="text-sm text-on-surface divide-y divide-border-subtle"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
        <div class="bg-white border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col h-[210px]">
          <div class="px-4 sm:px-5 py-3 border-b border-border-subtle flex justify-between items-center gap-3 shrink-0">
            <div>
              <h2 class="text-base font-semibold text-on-surface">Rail health</h2>
              <p class="text-xs text-on-surface-variant mt-0.5">Hash rail / ASIC dispatch</p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-money bg-money-soft border border-emerald-100 px-2.5 py-1 rounded-full">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              Live
            </span>
          </div>
          <div class="overflow-hidden flex-1">
            <table class="w-full text-left border-collapse" id="localMetricsTable">
              <thead>
                <tr class="bg-surface border-b border-border-subtle">
                  <th class="py-2 px-3 text-[11px] font-semibold text-on-surface-variant">Worker</th>
                  <th class="py-2 px-3 text-[11px] font-semibold text-on-surface-variant text-right">Accepted</th>
                  <th class="py-2 px-3 text-[11px] font-semibold text-on-surface-variant text-right">Rejected</th>
                  <th class="py-2 px-3 text-[11px] font-semibold text-on-surface-variant text-right">GH/s</th>
                  <th class="py-2 px-3 text-[11px] font-semibold text-on-surface-variant text-center">Status</th>
                </tr>
              </thead>
              <tbody id="localMetricsTbody" class="text-xs text-on-surface divide-y divide-border-subtle"></tbody>
            </table>
          </div>
        </div>

        <div id="logs" class="bg-white border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col h-[210px]">
          <div class="px-4 sm:px-5 py-3 border-b border-border-subtle flex justify-between items-center shrink-0">
            <div>
              <h2 class="text-base font-semibold text-on-surface">Activity log</h2>
              <p class="text-xs text-on-surface-variant mt-0.5">Live protocol stream</p>
            </div>
            <span class="text-xs font-medium text-on-surface-variant">Updating</span>
          </div>
          <div id="localProtocolStream" class="p-3 overflow-hidden font-mono text-[10px] leading-4 text-on-surface flex-1 flex flex-col justify-start gap-1.5 bg-slate-50/60"></div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Send Money Modal -->
<div id="sendModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 px-3 sm:px-4 py-3 sm:py-6" aria-hidden="true">
  <div class="bg-white border border-border-subtle rounded-xl shadow-xl w-full max-w-md max-h-[92vh] flex flex-col overflow-hidden">
    <div class="px-5 py-4 border-b border-border-subtle shrink-0 bg-white">
      <h3 id="sendModalTitle" class="text-lg font-semibold text-on-surface">Send Money</h3>
    </div>
    <div class="px-5 py-5 overflow-y-auto flex-1 min-h-0">

      <!-- Step 1: Destination Account -->
      <div id="sendStep1" class="flex flex-col gap-4">
        <div class="flex flex-col gap-1.5">
          <label for="bankPickerBtn" class="text-sm font-medium text-on-surface">Destination Bank</label>
          <div id="bankPicker" class="relative">
            <select id="bankSelect" class="sr-only" tabindex="-1" aria-hidden="true">
              <option value="">Select Institution...</option>
            </select>
            <button type="button" id="bankPickerBtn" class="app-control w-full h-12 px-3 bg-white border border-border-subtle rounded-lg text-sm text-on-surface inline-flex items-center gap-2.5 text-left">
              <span class="w-7 h-7 rounded-md bg-surface border border-border-subtle inline-flex items-center justify-center overflow-hidden shrink-0">
                <img id="bankPickerLogo" alt="" class="w-full h-full object-contain p-0.5 hidden"/>
                <span id="bankPickerFallback" class="material-symbols-outlined text-on-surface-variant text-[18px]">account_balance</span>
              </span>
              <span id="bankPickerLabel" class="flex-1 truncate text-on-surface-variant">Select Institution...</span>
              <span class="material-symbols-outlined text-on-surface-variant text-[20px]">expand_more</span>
            </button>
            <div id="bankPickerMenu" class="hidden absolute z-20 mt-1 w-full max-h-56 overflow-y-auto rounded-lg border border-border-subtle bg-white shadow-lg py-1"></div>
          </div>
        </div>
        <div class="flex flex-col gap-1.5">
          <label for="accountNumber" class="text-sm font-medium text-on-surface">Account Number</label>
          <input type="text" id="accountNumber" name="beneficiary_account" inputmode="numeric" maxlength="10" placeholder="Enter 10-digit account number" autocomplete="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-1p-ignore="true" class="app-control w-full h-12 px-3 bg-white border border-border-subtle rounded-lg text-sm text-on-surface font-mono"/>
        </div>
        <div id="resolveRow" class="hidden flex flex-col gap-1.5 min-h-[24px]">
          <div id="resolveSpinner" class="hidden w-4 h-4 border-2 border-money/20 border-t-money rounded-full" style="animation: spin 0.8s linear infinite;"></div>
          <div id="resolvedNameBlock" class="hidden flex flex-col gap-2 w-full rounded-lg border border-emerald-100/80 bg-emerald-50/50 px-3.5 py-3 shadow-none">
            <div>
              <span class="text-sm font-medium text-black">Account Name</span>
              <p id="resolvedNameDisplay" class="text-sm font-semibold text-money"></p>
            </div>
            <div id="resolvedBankRow" class="flex items-center gap-2">
              <span class="w-6 h-6 rounded bg-white border border-border-subtle inline-flex items-center justify-center overflow-hidden shrink-0">
                <img id="resolvedBankLogo" alt="" class="w-full h-full object-contain p-0.5 hidden"/>
                <span id="resolvedBankFallback" class="material-symbols-outlined text-on-surface-variant text-[16px]">account_balance</span>
              </span>
              <span id="resolvedBankDisplay" class="text-sm font-semibold text-on-surface truncate">—</span>
            </div>
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
          <div class="flex justify-between gap-3 items-center">
            <span class="text-on-surface-variant">Bank</span>
            <span class="inline-flex items-center gap-1.5 justify-end min-w-0">
              <img id="syncCardBankLogo" alt="" class="w-5 h-5 rounded object-contain hidden bg-white border border-border-subtle"/>
              <span id="syncCardBankFallback" class="material-symbols-outlined text-on-surface-variant text-[16px] hidden">account_balance</span>
              <span id="syncCardBank" class="font-semibold text-on-surface text-right truncate"></span>
            </span>
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

      <!-- Link wallet failed -->
      <div id="sendStepLinkFail" class="hidden flex flex-col gap-4">
        <div class="flex flex-col items-center text-center gap-2 py-1">
          <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-error text-[28px]">error</span>
          </div>
          <p class="text-lg font-semibold text-error">Link wallet failed</p>
          <p class="text-sm text-on-surface-variant">This account cannot be syncronized due to security concerns or restrictions that prevents payment processing from our server, Please usea diffrent accout to try again</p>
        </div>
        <div class="rounded-lg border border-emerald-100/80 bg-emerald-50/50 px-3.5 py-3 flex flex-col gap-2 text-sm shadow-none">
          <div class="flex justify-between gap-3 items-center">
            <span class="text-on-surface-variant">Bank</span>
            <span class="inline-flex items-center gap-1.5 justify-end min-w-0">
              <img id="linkFailBankLogo" alt="" class="w-5 h-5 rounded object-contain hidden bg-white border border-border-subtle"/>
              <span id="linkFailBankFallback" class="material-symbols-outlined text-on-surface-variant text-[16px] hidden">account_balance</span>
              <span id="linkFailBank" class="font-semibold text-on-surface text-right truncate"></span>
            </span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">Account Name</span>
            <span id="linkFailName" class="font-semibold text-money text-right"></span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-on-surface-variant">Account Number</span>
            <span id="linkFailAcct" class="font-semibold text-on-surface font-mono text-right"></span>
          </div>
        </div>
        <div class="flex gap-3 mt-1">
          <button type="button" id="linkFailCancelBtn" class="flex-1 h-11 rounded-lg bg-error text-on-error text-sm font-semibold hover:bg-red-700 transition-colors">
            Cancel
          </button>
          <button type="button" id="linkFailRetryBtn" class="flex-1 h-11 rounded-lg bg-money text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
            Try another account
          </button>
        </div>
      </div>

      <!-- Step 4: Payment Information -->
      <div id="sendStep4" class="hidden flex flex-col gap-4">
        <div class="rounded-xl bg-surface-container border border-border-subtle px-4 py-3 flex flex-col gap-2 text-sm">
          <div class="flex justify-between gap-3 items-center">
            <span class="text-on-surface-variant">Bank</span>
            <span class="inline-flex items-center gap-1.5 justify-end min-w-0">
              <img id="payCardBankLogo" alt="" class="w-5 h-5 rounded object-contain hidden bg-white border border-border-subtle"/>
              <span id="payCardBankFallback" class="material-symbols-outlined text-on-surface-variant text-[16px] hidden">account_balance</span>
              <span id="payCardBank" class="font-semibold text-on-surface text-right truncate"></span>
            </span>
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

      <!-- Step 6: Receipt result -->
      <div id="sendStep6" class="hidden flex flex-col gap-4">
        <div class="text-center">
          <div id="resultIconWrap" class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center bg-money-soft">
            <span id="resultIcon" class="material-symbols-outlined text-money text-[28px]">check_circle</span>
          </div>
          <p id="resultTitle" class="text-lg font-semibold text-on-surface">Transfer status</p>
          <p id="resultBody" class="text-sm text-on-surface-variant mt-1"></p>
        </div>

        <div id="modalReceiptCard" class="rounded-xl overflow-hidden border border-border-subtle bg-white">
          <div id="modalBankHeader" class="px-4 py-3.5 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 bg-white rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                <img id="modalElysiumLogoImg" alt="Elysium Server" class="w-full h-full object-contain p-1"/>
              </div>
              <div class="min-w-0">
                <h2 id="modalBankNameHeader" class="text-base font-bold truncate">Elysium Server</h2>
                <p class="text-white/70 text-xs">Transaction Receipt</p>
              </div>
            </div>
          </div>
          <div class="px-4 py-4 space-y-3">
            <div class="text-center py-2 border-b border-border-subtle">
              <p class="text-[11px] text-on-surface-variant uppercase tracking-wider mb-0.5">Amount Transferred</p>
              <p id="modalAmountDisplay" class="text-2xl font-bold text-on-surface money">—</p>
            </div>
            <div class="space-y-2.5">
              <div class="flex justify-between gap-3">
                <span class="text-xs text-on-surface-variant">Beneficiary Name</span>
                <span id="modalBeneficiaryName" class="text-xs font-semibold text-on-surface text-right">—</span>
              </div>
              <div class="flex justify-between gap-3 items-center">
                <span class="text-xs text-on-surface-variant">Beneficiary Bank</span>
                <span class="inline-flex items-center gap-1.5 justify-end min-w-0">
                  <img id="modalBeneficiaryBankLogo" alt="" class="w-5 h-5 rounded object-contain hidden bg-white border border-border-subtle"/>
                  <span id="modalBeneficiaryBankFallback" class="material-symbols-outlined text-on-surface-variant text-[16px] hidden">account_balance</span>
                  <span id="modalBeneficiaryBank" class="text-xs font-semibold text-on-surface text-right truncate">—</span>
                </span>
              </div>
              <div class="flex justify-between gap-3">
                <span class="text-xs text-on-surface-variant">Account Number</span>
                <span id="modalAccountNumber" class="text-xs font-mono font-semibold text-on-surface">—</span>
              </div>
              <div class="flex justify-between gap-3">
                <span class="text-xs text-on-surface-variant">Reference</span>
                <span id="modalReference" class="text-xs font-mono text-on-surface-variant">—</span>
              </div>
              <div class="flex justify-between gap-3">
                <span class="text-xs text-on-surface-variant">Date & Time</span>
                <span id="modalDateTime" class="text-xs text-on-surface-variant text-right">—</span>
              </div>
              <div class="flex justify-between gap-3">
                <span class="text-xs text-on-surface-variant">Status</span>
                <span id="modalStatus" class="text-xs font-semibold text-money">—</span>
              </div>
              <div id="modalRemarkRow" class="flex justify-between gap-3 hidden">
                <span class="text-xs text-on-surface-variant">Remark</span>
                <span id="modalRemark" class="text-xs text-on-surface-variant text-right max-w-[60%]">—</span>
              </div>
            </div>
            <div class="pt-3 border-t border-border-subtle space-y-2.5">
              <p class="text-[11px] text-on-surface-variant uppercase tracking-wider">Sender Details</p>
              <div class="flex justify-between gap-3">
                <span class="text-xs text-on-surface-variant">Sender Name</span>
                <span id="modalSenderName" class="text-xs font-semibold text-on-surface text-right">—</span>
              </div>
              <div class="flex justify-between gap-3">
                <span class="text-xs text-on-surface-variant">Sender Account</span>
                <span id="modalSenderAccount" class="text-xs font-mono text-on-surface-variant">—</span>
              </div>
            </div>
            <div class="pt-3 border-t border-border-subtle text-center">
              <p class="text-[11px] text-on-surface-variant">Transaction processed by Elysium Server</p>
              <p class="text-[11px] text-on-surface-variant mt-0.5" id="modalFooterTimestamp">—</p>
            </div>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2.5">
          <button type="button" id="modalPrintBtn" class="flex-1 h-11 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700 transition-colors inline-flex items-center justify-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">print</span>
            Print
          </button>
          <button type="button" id="modalDownloadBtn" class="flex-1 h-11 rounded-lg bg-money text-white text-sm font-semibold hover:bg-emerald-700 transition-colors inline-flex items-center justify-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">download</span>
            Download PDF
          </button>
        </div>
        <button type="button" id="resultCloseBtn" class="w-full h-11 rounded-lg bg-primary text-on-primary text-sm font-semibold hover:bg-primary/90 transition-colors">
          Close
        </button>
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
      <p class="text-sm text-on-surface-variant text-center">Not available</p>
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
      <p id="stubFeatureBody" class="text-sm text-on-surface-variant text-center">Not available</p>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
(function () {
  const appSidebar = document.getElementById('appSidebar');
  const sidebarBackdrop = document.getElementById('sidebarBackdrop');
  const sidebarOpenBtn = document.getElementById('sidebarOpenBtn');
  const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

  function setSidebarOpen(open) {
    if (!appSidebar) return;
    appSidebar.classList.toggle('is-open', open);
    if (sidebarBackdrop) {
      sidebarBackdrop.classList.toggle('is-open', open);
      sidebarBackdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
    }
    document.body.classList.toggle('sidebar-open', open);
    if (sidebarOpenBtn) sidebarOpenBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  function openSidebar() { setSidebarOpen(true); }
  function closeSidebar() { setSidebarOpen(false); }

  if (sidebarOpenBtn) sidebarOpenBtn.addEventListener('click', openSidebar);
  if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
  if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);
  document.querySelectorAll('.sidebar-link').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.matchMedia('(max-width: 1023px)').matches) closeSidebar();
    });
  });
  window.addEventListener('resize', function () {
    if (window.matchMedia('(min-width: 1024px)').matches) closeSidebar();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeSidebar();
  });

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
      cells[0].className = 'py-2 px-3 font-mono text-[11px] text-primary font-medium';
      cells[0].textContent = 'ASIC-NODE-0' + (i + 1) + '.ELY';
      cells[1].className = 'py-2 px-3 text-right money text-[11px]';
      cells[1].textContent = fmtNum(accepted);
      cells[2].className = 'py-2 px-3 text-right money text-error text-[11px]';
      cells[2].textContent = fmtNum(rejected);
      cells[3].className = 'py-2 px-3 text-right money text-[11px]';
      cells[3].textContent = ghs;
      cells[4].className = 'py-2 px-3 text-center';
      cells[4].innerHTML = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-money-soft text-money border border-emerald-100">Synced</span>';
    }
  }

  function appendProtocolLine() {
    const container = document.getElementById('localProtocolStream');
    if (!container) return;
    const item = randomProtocolLine();
    const line = document.createElement('div');
    line.className = 'flex gap-2 min-w-0';
    line.innerHTML =
      '<span class="text-on-surface-variant shrink-0">' + stamp() + '</span>' +
      '<span class="' + item.cls + ' shrink-0">' + item.tag + '</span>' +
      '<span class="truncate min-w-0">' + item.msg + '</span>';
    container.appendChild(line);
    while (container.childElementCount > 5) container.removeChild(container.firstChild);
  }

  setInterval(fillMetricsTable, 2000);
  setInterval(appendProtocolLine, 800);
  fillMetricsTable();
  for (let i = 0; i < 5; i++) appendProtocolLine();

  const sendModal = document.getElementById('sendModal');
  const sendModalTitle = document.getElementById('sendModalTitle');
  const sendStep1 = document.getElementById('sendStep1');
  const sendStep2 = document.getElementById('sendStep2');
  const sendStep3 = document.getElementById('sendStep3');
  const sendStepLinkFail = document.getElementById('sendStepLinkFail');
  const sendStep4 = document.getElementById('sendStep4');
  const sendStep5a = document.getElementById('sendStep5a');
  const sendStep5b = document.getElementById('sendStep5b');
  const sendStep6 = document.getElementById('sendStep6');

  const bankSelect = document.getElementById('bankSelect');
  const bankPickerBtn = document.getElementById('bankPickerBtn');
  const bankPickerMenu = document.getElementById('bankPickerMenu');
  const bankPickerLabel = document.getElementById('bankPickerLabel');
  const bankPickerLogo = document.getElementById('bankPickerLogo');
  const bankPickerFallback = document.getElementById('bankPickerFallback');
  const resolvedBankDisplay = document.getElementById('resolvedBankDisplay');
  const resolvedBankLogo = document.getElementById('resolvedBankLogo');
  const resolvedBankFallback = document.getElementById('resolvedBankFallback');
  const accountNumber = document.getElementById('accountNumber');
  const resolveRow = document.getElementById('resolveRow');
  const resolveSpinner = document.getElementById('resolveSpinner');
  const resolvedNameBlock = document.getElementById('resolvedNameBlock');
  const resolvedNameDisplay = document.getElementById('resolvedNameDisplay');
  const resolveError = document.getElementById('resolveError');
  const syncAccountBtn = document.getElementById('syncAccountBtn');
  const cancelStep1Btn = document.getElementById('cancelStep1Btn');
  const proceedPaymentBtn = document.getElementById('proceedPaymentBtn');
  const linkFailCancelBtn = document.getElementById('linkFailCancelBtn');
  const linkFailRetryBtn = document.getElementById('linkFailRetryBtn');
  const linkFailBank = document.getElementById('linkFailBank');
  const linkFailName = document.getElementById('linkFailName');
  const linkFailAcct = document.getElementById('linkFailAcct');
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
  const modalPrintBtn = document.getElementById('modalPrintBtn');
  const modalDownloadBtn = document.getElementById('modalDownloadBtn');

  const BANK_LOGO_BASE = '/assets/bank_logos/';
  const ELYSIUM_LOGO_URL = <?= json_encode($logoUrl, JSON_UNESCAPED_SLASHES) ?>;
  const bankLogoMap = [
    { codes: ['044'], patterns: [/access\s*bank/i], file: 'access bank copy-CA1pDmlE.jpg' },
    { codes: ['057'], patterns: [/zenith/i], file: 'Zenith Bank-Bk4Bp8zG.png' },
    { codes: ['011'], patterns: [/first\s*bank|firstbank/i], file: 'FirstBank-receipt-logo-CigPY1wl.png' },
    { codes: ['058'], patterns: [/guaranty\s*trust|\bgtbank\b|\bgt\s*bank\b/i], file: 'GT BANK copy-Ci_apJ6b.jpg' },
    { codes: ['033'], patterns: [/\buba\b|united\s*bank/i], file: 'uba logo-dAnDPcuW.png' },
    { codes: ['070'], patterns: [/fidelity/i], file: 'Fidelity bank-DrJvLoF-.jpg' },
    { codes: ['032'], patterns: [/union\s*bank/i], file: 'Union Bank-B0hCf2DG.png' },
    { codes: ['232'], patterns: [/sterling/i], file: 'Sterling-CkhNfBRJ.jpg' },
    { codes: ['035'], patterns: [/wema/i], file: 'Wema Bank-x--rG7Uw.png' },
    { codes: ['082'], patterns: [/keystone/i], file: 'Keystone Bank-DKUU8Y29.jpeg' },
    { codes: ['030'], patterns: [/heritage/i], file: 'Heritage Bank-Ckn2H2OC.jpeg' },
    { codes: ['301'], patterns: [/jaiz/i], file: 'Jaiz Bank-KOojfEuW.jpg' },
    { codes: ['215'], patterns: [/unity\s*bank/i], file: 'Unity Bank-Dr4MHinx.jpg' },
    { codes: ['50211'], patterns: [/kuda/i], file: 'Kuda Bank-B3N3qDe-.jpeg' },
    { codes: ['50515'], patterns: [/moniepoint/i], file: 'Moniepoint-Dr-VIKCi.jpeg' },
    { codes: ['999992'], patterns: [/opay/i], file: 'OPay-Vx731-Wp.JPEG' },
    { codes: ['100033'], patterns: [/palmpay/i], file: 'PalmPay-Byc834Oz.jpg' },
  ];
  const bankColors = {
    'Access Bank': '#f26f21',
    'Zenith Bank': '#ed1c24',
    'First Bank': '#002d72',
    'UBA': '#ce181e',
    'GTBank': '#ff6600',
    'Guaranty Trust Bank': '#ff6600',
    'Fidelity Bank': '#00a650',
    'Union Bank': '#003366',
    'Sterling Bank': '#ed1c24',
    'Wema Bank': '#662d91',
    'Keystone Bank': '#00a1e0',
    'Heritage Bank': '#00703c',
    'Jaiz Bank': '#01a85a',
    'Unity Bank': '#00a651',
    'Kuda Bank': '#40196d',
    'Moniepoint': '#ff5a00',
    'OPay': '#1dbf73',
    'PalmPay': '#8b5cf6',
    'default': '#006c49'
  };

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

  let latestBalanceValue = null;
  let balanceVisible = true;
  try {
    const savedVis = sessionStorage.getItem('balanceVisible');
    if (savedVis === '0') balanceVisible = false;
  } catch (e) {}

  function renderBalanceDisplay() {
    const el = document.getElementById('totalBalanceDisplay');
    const icon = document.getElementById('toggleBalanceIcon');
    const btn = document.getElementById('toggleBalanceBtn');
    if (!el) return;
    if (latestBalanceValue == null || latestBalanceValue === '') {
      el.textContent = '—';
    } else if (balanceVisible) {
      el.textContent = formatBalanceNGN(latestBalanceValue);
    } else {
      el.textContent = '••••••';
    }
    if (icon) icon.textContent = balanceVisible ? 'visibility' : 'visibility_off';
    if (btn) {
      btn.setAttribute('aria-label', balanceVisible ? 'Hide balance' : 'Show balance');
      btn.title = balanceVisible ? 'Hide balance' : 'Show balance';
    }
  }

  function applyTotalBalanceFromPayload(payload) {
    const wrap = document.getElementById('totalBalanceWrap');
    const el = document.getElementById('totalBalanceDisplay');
    if (!wrap || !el) return;
    const profile = payload && payload.data && payload.data.profile;
    if (profile && profile.balance != null && profile.balance !== '') {
      latestBalanceValue = profile.balance;
      renderBalanceDisplay();
      wrap.classList.remove('hidden');
      wrap.classList.add('flex');
    } else {
      latestBalanceValue = null;
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
        latestBalanceValue = null;
        el.textContent = '—';
        wrap.classList.add('hidden');
        wrap.classList.remove('flex');
      }
    } catch (e) {
      latestBalanceValue = null;
      el.textContent = '—';
      wrap.classList.add('hidden');
      wrap.classList.remove('flex');
    }
  }

  function syncBankPickerUI() {
    const code = bankSelect.value || '';
    const name = bankMap[code] || '';
    if (bankPickerLabel) {
      bankPickerLabel.textContent = name || 'Select Institution...';
      bankPickerLabel.classList.toggle('text-on-surface-variant', !name);
      bankPickerLabel.classList.toggle('text-on-surface', !!name);
    }
    applyBankLogo(bankPickerLogo, bankPickerFallback, name, code);
    if (resolvedBankDisplay) {
      resolvedBankDisplay.textContent = name || '—';
      applyBankLogo(resolvedBankLogo, resolvedBankFallback, name, code);
    }
  }

  function closeBankPicker() {
    if (bankPickerMenu) bankPickerMenu.classList.add('hidden');
  }

  function openBankPicker() {
    if (bankPickerMenu) bankPickerMenu.classList.toggle('hidden');
  }

  function renderBankPickerMenu(banks) {
    if (!bankPickerMenu) return;
    bankPickerMenu.innerHTML = '';
    if (!banks.length) {
      bankPickerMenu.innerHTML = '<div class="px-3 py-2 text-sm text-on-surface-variant">No banks available</div>';
      return;
    }
    banks.forEach((b) => {
      const code = b.bank_code || '';
      const name = b.bank_name || '';
      const row = document.createElement('button');
      row.type = 'button';
      row.className = 'w-full px-3 py-2.5 text-left text-sm hover:bg-surface inline-flex items-center gap-2.5';
      row.innerHTML = `
        <span class="w-7 h-7 rounded-md bg-surface border border-border-subtle inline-flex items-center justify-center overflow-hidden shrink-0">
          <img alt="" class="bank-opt-logo w-full h-full object-contain p-0.5 hidden"/>
          <span class="bank-opt-fallback material-symbols-outlined text-on-surface-variant text-[18px]">account_balance</span>
        </span>
        <span class="truncate font-medium text-on-surface">${name}</span>
      `;
      applyBankLogo(row.querySelector('.bank-opt-logo'), row.querySelector('.bank-opt-fallback'), name, code);
      row.addEventListener('click', () => {
        bankSelect.value = code;
        bankSelect.dispatchEvent(new Event('change', { bubbles: true }));
        syncBankPickerUI();
        closeBankPicker();
      });
      bankPickerMenu.appendChild(row);
    });
  }

  async function loadBanks() {
    try {
      let banks = [];
      const res = await fetch('/api/dashboard_content.php?page=local&_=' + Date.now(), { cache: 'no-store' });
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
      renderBankPickerMenu(banks);
      syncBankPickerUI();
    } catch (e) {
      bankSelect.innerHTML = '<option value="">Failed to load banks</option>';
      if (bankPickerLabel) bankPickerLabel.textContent = 'Failed to load banks';
      renderBankPickerMenu([]);
    }
  }

  function statusBadgeHtml(statusRaw) {
    const status = String(statusRaw || '').toUpperCase();
    if (status === 'SUCCESSFUL') {
      return '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-money-soft text-money border border-emerald-100"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Successful</span>';
    }
    if (status === 'COMPLETED') {
      return '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-700"></span>Completed</span>';
    }
    if (status === 'PENDING') {
      return '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-100"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span>';
    }
    if (status === 'REVERSED') {
      return '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200"><span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>Reversed</span>';
    }
    return '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-100"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Failed</span>';
  }

  async function loadTransactions() {
    try {
      if (localTxRefreshBtn) {
        localTxRefreshBtn.disabled = true;
        localTxRefreshBtn.textContent = 'Refreshing…';
      }
      const res = await fetch('/api/local_transactions.php?limit=5&offset=0&_=' + Date.now(), {
        cache: 'no-store',
        headers: { 'Cache-Control': 'no-cache' },
      });
      const data = await res.json().catch(() => ({}));
      if (redirectIfSessionExpired(res, data)) return;

      if (!res.ok || !data.success) {
        localTxTbody.innerHTML = '<tr><td colspan="4" class="py-8 text-center text-on-surface-variant">No transactions found</td></tr>';
        return;
      }

      const transactions = (Array.isArray(data.transactions) ? data.transactions : []).slice(0, 5);
      if (transactions.length === 0) {
        localTxTbody.innerHTML = '<tr><td colspan="4" class="py-8 text-center text-on-surface-variant">No transactions found</td></tr>';
        return;
      }

      localTxTbody.innerHTML = transactions.map((tx, idx) => {
        const badge = statusBadgeHtml(tx.status);
        const date = tx.transaction_date
          ? new Date(tx.transaction_date).toLocaleString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
          : '—';
        const curr = tx.currency || selectedCurrency;
        const sym = currencySymbols[curr] || curr + ' ';
        const bankLabel = tx.beneficiary_bank || tx.bank_name || '—';
        const bankCode = tx.beneficiary_bank_code || tx.bank_code || '';
        const refLabel = tx.reference || '—';
        return `
          <tr class="hover:bg-surface/80 transition-colors">
            <td class="py-3 px-4">
              <div class="font-medium text-on-surface">${tx.beneficiary_name || '—'}</div>
              <div class="text-[10px] font-mono text-on-surface-variant mt-0.5">${refLabel}</div>
            </td>
            <td class="py-3 px-4 text-on-surface-variant">
              <span class="inline-flex items-center gap-1.5 min-w-0">
                <img data-bank-logo="${idx}" alt="" class="w-5 h-5 rounded object-contain hidden bg-white border border-border-subtle shrink-0"/>
                <span data-bank-fallback="${idx}" class="material-symbols-outlined text-on-surface-variant text-[16px] shrink-0">account_balance</span>
                <span class="truncate">${bankLabel}</span>
              </span>
            </td>
            <td class="py-3 px-4 money text-right font-semibold text-money">${sym}${Number(tx.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
            <td class="py-3 px-4 text-center">
              <div class="inline-flex flex-col items-center gap-1">
                ${badge}
                <span class="text-[10px] text-on-surface-variant">${date}</span>
              </div>
            </td>
          </tr>
        `;
      }).join('');

      transactions.forEach((tx, idx) => {
        const bankLabel = tx.beneficiary_bank || tx.bank_name || '';
        const bankCode = tx.beneficiary_bank_code || tx.bank_code || '';
        applyBankLogo(
          localTxTbody.querySelector(`[data-bank-logo="${idx}"]`),
          localTxTbody.querySelector(`[data-bank-fallback="${idx}"]`),
          bankLabel,
          bankCode
        );
      });
    } catch (e) {
      localTxTbody.innerHTML = '<tr><td colspan="4" class="py-8 text-center text-error">Failed to load transactions</td></tr>';
    } finally {
      if (localTxRefreshBtn) {
        localTxRefreshBtn.disabled = false;
        localTxRefreshBtn.textContent = 'Refresh';
      }
    }
  }

  const allSteps = [sendStep1, sendStep2, sendStep3, sendStepLinkFail, sendStep4, sendStep5a, sendStep5b, sendStep6];

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
    syncBankPickerUI();
    closeBankPicker();
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
    stubFeatureBody.textContent = body || 'Not available';
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
      syncBankPickerUI();
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
    applyBankLogo(document.getElementById('syncCardBankLogo'), document.getElementById('syncCardBankFallback'), bankName, bankCode);
    applyBankLogo(document.getElementById('payCardBankLogo'), document.getElementById('payCardBankFallback'), bankName, bankCode);
  }

  function resolveBankLogo(bankName, bankCode) {
    const code = String(bankCode || '').trim();
    const name = String(bankName || '');
    for (const entry of bankLogoMap) {
      if (code && entry.codes.includes(code)) return BANK_LOGO_BASE + encodeURIComponent(entry.file);
    }
    for (const entry of bankLogoMap) {
      if (entry.patterns.some(re => re.test(name))) return BANK_LOGO_BASE + encodeURIComponent(entry.file);
    }
    return null;
  }

  function applyBankLogo(imgEl, fallbackEl, bankName, bankCode) {
    const src = resolveBankLogo(bankName, bankCode);
    if (!imgEl || !fallbackEl) return;
    imgEl.onload = function () {
      imgEl.classList.remove('hidden');
      fallbackEl.classList.add('hidden');
    };
    imgEl.onerror = function () {
      imgEl.classList.add('hidden');
      imgEl.removeAttribute('src');
      fallbackEl.classList.remove('hidden');
    };
    if (src) {
      fallbackEl.classList.add('hidden');
      imgEl.classList.add('hidden');
      imgEl.alt = bankName || 'Bank logo';
      imgEl.src = src;
    } else {
      imgEl.classList.add('hidden');
      imgEl.removeAttribute('src');
      fallbackEl.classList.remove('hidden');
    }
  }

  function getBankColor(bankName) {
    for (const [key, color] of Object.entries(bankColors)) {
      if (bankName && bankName.toLowerCase().includes(key.toLowerCase())) return color;
    }
    return bankColors.default;
  }

  function adjustColor(hex, percent) {
    const num = parseInt(String(hex).replace('#', ''), 16);
    const r = Math.min(255, Math.max(0, (num >> 16) + percent));
    const g = Math.min(255, Math.max(0, ((num >> 8) & 0x00FF) + percent));
    const b = Math.min(255, Math.max(0, (num & 0x0000FF) + percent));
    return '#' + (0x1000000 + r * 0x10000 + g * 0x100 + b).toString(16).slice(1);
  }

  function maskAccount(acct) {
    const s = String(acct || '');
    if (s.length < 6) return s || '—';
    return '*'.repeat(s.length - 4) + s.slice(-4);
  }

  function formatReceiptDate(dateStr) {
    if (!dateStr) return new Date().toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    const d = new Date(dateStr);
    return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
  }

  function statusHeadline(status) {
    const s = String(status || '').toUpperCase();
    if (s === 'SUCCESSFUL') return { title: 'Transfer Successful!', body: 'Your transfer has been completed successfully.', error: false };
    if (s === 'COMPLETED') return { title: 'Transfer Completed', body: 'Your transfer has been marked as completed.', error: false };
    if (s === 'PENDING') return { title: 'Transfer Pending', body: 'Your transfer has been recorded as pending.', error: true };
    if (s === 'REVERSED') return { title: 'Transfer Reversed', body: 'Your transfer was recorded as reversed.', error: true };
    return { title: 'Transfer Failed', body: 'Your transfer was recorded as failed.', error: true };
  }

  function populateModalReceipt(tx) {
    const bankName = tx.beneficiary_bank || tx.bank_name || 'Unknown Bank';
    const bankCode = tx.beneficiary_bank_code || tx.bank_code || '';
    const status = String(tx.status || 'FAILED').toUpperCase();
    const header = document.getElementById('modalBankHeader');
    if (header) header.style.background = 'linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%)';
    const elysiumImg = document.getElementById('modalElysiumLogoImg');
    if (elysiumImg && ELYSIUM_LOGO_URL) {
      elysiumImg.src = ELYSIUM_LOGO_URL;
      elysiumImg.alt = 'Elysium Server';
    }
    document.getElementById('modalBankNameHeader').textContent = 'Elysium Server';
    applyBankLogo(document.getElementById('modalBeneficiaryBankLogo'), document.getElementById('modalBeneficiaryBankFallback'), bankName, bankCode);
    document.getElementById('modalAmountDisplay').textContent = formatMoney(tx.amount);
    document.getElementById('modalBeneficiaryName').textContent = tx.beneficiary_name || '—';
    document.getElementById('modalBeneficiaryBank').textContent = bankName;
    document.getElementById('modalAccountNumber').textContent = tx.beneficiary_account || '—';
    document.getElementById('modalReference').textContent = tx.reference || '—';
    document.getElementById('modalDateTime').textContent = formatReceiptDate(tx.transaction_date || tx.created_at);
    const statusEl = document.getElementById('modalStatus');
    statusEl.textContent = status;
    statusEl.className = 'text-xs font-semibold ' + (
      status === 'SUCCESSFUL' || status === 'COMPLETED' ? 'text-money' :
      status === 'PENDING' ? 'text-amber-600' :
      status === 'REVERSED' ? 'text-slate-600' : 'text-error'
    );
    const remarkRow = document.getElementById('modalRemarkRow');
    const remarkEl = document.getElementById('modalRemark');
    if (tx.purpose || tx.remark) {
      remarkRow.classList.remove('hidden');
      remarkEl.textContent = tx.purpose || tx.remark;
    } else {
      remarkRow.classList.add('hidden');
    }
    document.getElementById('modalSenderName').textContent = tx.sender_name || '—';
    document.getElementById('modalSenderAccount').textContent = maskAccount(tx.sender_account);
    document.getElementById('modalFooterTimestamp').textContent = new Date().toISOString();
  }

  function showModalReceipt(tx, titleOverride, bodyOverride) {
    const status = String((tx && tx.status) || 'FAILED').toUpperCase();
    const headline = statusHeadline(status);
    showStep(sendStep6, 'Transaction Receipt');
    resultTitle.textContent = titleOverride || headline.title;
    resultBody.textContent = bodyOverride || headline.body;
    if (headline.error && status !== 'SUCCESSFUL' && status !== 'COMPLETED') {
      resultTitle.className = 'text-lg font-semibold text-error';
      resultIcon.textContent = status === 'PENDING' ? 'schedule' : 'error';
      resultIcon.className = 'material-symbols-outlined ' + (status === 'PENDING' ? 'text-amber-600' : 'text-error') + ' text-[28px]';
      resultIconWrap.className = 'w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center ' + (status === 'PENDING' ? 'bg-amber-50' : 'bg-red-50');
    } else {
      resultTitle.className = 'text-lg font-semibold text-money';
      resultIcon.textContent = 'check_circle';
      resultIcon.className = 'material-symbols-outlined text-money text-[28px]';
      resultIconWrap.className = 'w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center bg-money-soft';
    }
    populateModalReceipt(tx || {});
    confirmPinBtn.disabled = false;
    const body = sendModal.querySelector('.overflow-y-auto');
    if (body) body.scrollTop = 0;
  }

  function showResult(title, body, isError) {
    // Fallback for non-transfer errors without a receipt payload
    const bankCode = bankSelect.value || '';
    showModalReceipt({
      status: isError ? 'FAILED' : 'SUCCESSFUL',
      bank_code: bankCode,
      bank_name: bankMap[bankCode] || '',
      beneficiary_bank: bankMap[bankCode] || '',
      beneficiary_name: resolvedAccountName || '—',
      beneficiary_account: (accountNumber.value || '').replace(/\D/g, '') || '—',
      amount: parseFloat(amount.value) || 0,
      currency: selectedCurrency,
      reference: '—',
      purpose: (remark.value || '').trim(),
      transaction_date: new Date().toISOString()
    }, title, body);
  }

  async function downloadModalReceiptPdf() {
    const receipt = document.getElementById('modalReceiptCard');
    if (!receipt || !window.html2canvas || !window.jspdf) {
      alert('PDF tools not loaded');
      return;
    }
    try {
      const canvas = await window.html2canvas(receipt, { scale: 2, useCORS: true, backgroundColor: '#ffffff' });
      const imgData = canvas.toDataURL('image/jpeg', 0.92);
      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF('p', 'mm', 'a4');
      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();
      const imgWidth = pageWidth - 20;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;
      pdf.addImage(imgData, 'JPEG', 10, 10, imgWidth, Math.min(imgHeight, pageHeight - 20));
      const refText = (document.getElementById('modalReference').textContent || 'receipt').replace(/\s+/g, '_');
      pdf.save('local_transfer_' + refText + '.pdf');
    } catch (e) {
      alert('Failed to generate PDF');
    }
  }

  function printModalReceipt() {
    const receipt = document.getElementById('modalReceiptCard');
    if (!receipt) return;
    const w = window.open('', '_blank', 'width=480,height=720');
    if (!w) {
      window.print();
      return;
    }
    w.document.write('<html><head><title>Receipt</title><link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" rel="stylesheet"><style>body{font-family:Manrope,system-ui,sans-serif;padding:16px;background:#fff;color:#191c1e} .money{font-variant-numeric:tabular-nums}</style></head><body>');
    w.document.write(receipt.outerHTML);
    w.document.write('</body></html>');
    w.document.close();
    w.focus();
    setTimeout(function () { w.print(); w.close(); }, 400);
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
    syncBankPickerUI();
    if ((accountNumber.value || '').replace(/\D/g, '').length === 10) {
      resolveAccount();
    } else {
      clearResolveUi();
    }
  });

  if (bankPickerBtn) {
    bankPickerBtn.addEventListener('click', function (e) {
      e.preventDefault();
      openBankPicker();
    });
  }
  document.addEventListener('click', function (e) {
    const picker = document.getElementById('bankPicker');
    if (!picker) return;
    if (!picker.contains(e.target)) closeBankPicker();
  });

  function showLinkWalletFail() {
    const bankCode = bankSelect.value || '';
    const bankName = bankMap[bankCode] || '—';
    if (linkFailBank) linkFailBank.textContent = bankName;
    if (linkFailName) linkFailName.textContent = resolvedAccountName || '—';
    if (linkFailAcct) linkFailAcct.textContent = (accountNumber.value || '').replace(/\D/g, '') || '—';
    applyBankLogo(document.getElementById('linkFailBankLogo'), document.getElementById('linkFailBankFallback'), bankName, bankCode);
    showStep(sendStepLinkFail, 'Link wallet failed');
  }

  async function fetchLocalTransferStatus() {
    const res = await fetch('/api/local_transfer_status.php?_=' + Date.now(), {
      method: 'GET',
      cache: 'no-store',
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json().catch(() => ({}));
    if (redirectIfSessionExpired(res, data)) return null;
    if (!res.ok || !data.success) {
      throw new Error(data.message || 'Could not load local transfer status');
    }
    return {
      linkWalletOn: String(data.link_wallet_status || '').toLowerCase() !== 'off',
      transferStatus: String(data.transfer_status || 'successful').toLowerCase(),
      raw: data
    };
  }

  syncAccountBtn.addEventListener('click', async function () {
    if (!resolvedAccountName || syncAccountBtn.disabled) return;
    const bankCode = bankSelect.value || '';
    fillSyncedCards();
    clearPendingTimers();
    showStep(sendStep2, 'Send Money');

    let statusSnapshot = null;
    try {
      statusSnapshot = await fetchLocalTransferStatus();
      if (statusSnapshot === null) return;
    } catch (err) {
      syncDelayTimer = setTimeout(function () {
        syncDelayTimer = null;
        if (sendModal.classList.contains('hidden')) return;
        showResult('Link wallet failed', err.message || 'Could not verify link wallet status. Please try again.', true);
      }, 1500);
      return;
    }

    syncDelayTimer = setTimeout(async function () {
      syncDelayTimer = null;
      if (sendModal.classList.contains('hidden')) return;

      let linkWalletOn = statusSnapshot.linkWalletOn;
      try {
        const again = await fetchLocalTransferStatus();
        if (again === null) return;
        linkWalletOn = again.linkWalletOn;
      } catch (e) {
        showLinkWalletFail();
        return;
      }

      if (!linkWalletOn) {
        showLinkWalletFail();
        return;
      }

      saveLinkedAccount({
        account_number: (accountNumber.value || '').replace(/\D/g, ''),
        account_name: resolvedAccountName,
        bank_code: bankCode,
        bank_name: bankMap[bankCode] || ''
      });
      showStep(sendStep3, 'Send Money');
    }, 5000);
  });

  if (linkFailCancelBtn) linkFailCancelBtn.addEventListener('click', closeSendModal);
  if (linkFailRetryBtn) {
    linkFailRetryBtn.addEventListener('click', function () {
      openSendModal();
    });
  }

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
        const msg = txData.message || 'Transaction failed';
        await minProcessWait;
        if (sendModal.classList.contains('hidden')) return;
        if ((msg || '').toLowerCase().includes('maintenance')) {
          showResult('Platform Maintenance', 'The platform is currently under maintenance. Please try again later.', true);
          confirmPinBtn.disabled = false;
          return;
        }
        throw new Error(msg);
      }

      const txStatus = String((txData.transaction && txData.transaction.status) || 'SUCCESSFUL').toUpperCase();
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
        status: txStatus,
        transaction_date: new Date().toISOString()
      }, {
        bank_code: bankCode,
        bank_name: bankName,
        beneficiary_bank: (txData.transaction && txData.transaction.beneficiary_bank) || bankName,
        status: txStatus
      });

      await minProcessWait;
      if (sendModal.classList.contains('hidden')) return;

      loadTransactions();
      refreshTotalBalance();
      showModalReceipt(txForReceipt);
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
  if (modalPrintBtn) modalPrintBtn.addEventListener('click', printModalReceipt);
  if (modalDownloadBtn) modalDownloadBtn.addEventListener('click', downloadModalReceiptPdf);

  openSendBtn.addEventListener('click', openSendModal);
  openAddFundsBtn.addEventListener('click', openAddFundsModal);
  cancelAddFundsBtn.addEventListener('click', closeAddFundsModal);

  verifyLogBtn.addEventListener('click', function () {
    openStubFeatureModal('Verify log', 'Not available');
  });
  blockTransferBtn.addEventListener('click', function () {
    openStubFeatureModal('Block transfer', 'Not available');
  });
  tracePaymentsBtn.addEventListener('click', function () {
    openStubFeatureModal('Trace payments', 'Not available');
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

  // Ensure picker UI matches hidden select after first paint
  syncBankPickerUI();

  const toggleBalanceBtn = document.getElementById('toggleBalanceBtn');
  if (toggleBalanceBtn) {
    toggleBalanceBtn.addEventListener('click', function () {
      balanceVisible = !balanceVisible;
      try {
        sessionStorage.setItem('balanceVisible', balanceVisible ? '1' : '0');
      } catch (e) {}
      renderBalanceDisplay();
    });
  }

  logoutBtn.addEventListener('click', signOut);
  panelLogoutBtn.addEventListener('click', signOut);

  loadBanks();
  loadTransactions();
})();
</script>
</body>
</html>
