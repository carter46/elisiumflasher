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
          <div class="inline-flex items-center gap-2 self-start px-2.5 py-1 rounded-full bg-money-soft border border-emerald-100">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <span class="text-xs font-semibold text-money">System operational</span>
          </div>
          <h1 class="text-3xl font-bold tracking-tight text-on-surface">Transfer dashboard</h1>
          <p class="text-sm text-on-surface-variant max-w-lg">Send local settlements, monitor rail health, and review recent transactions.</p>
        </div>
        <div id="totalBalanceWrap" class="hidden flex flex-col md:items-end bg-white border border-border-subtle rounded-xl px-5 py-4 shadow-sm min-w-[240px]">
          <span class="text-xs font-semibold uppercase tracking-wide text-on-surface-variant mb-1">Available balance</span>
          <div id="totalBalanceDisplay" class="money text-[28px] leading-8 font-bold text-primary">—</div>
        </div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div id="transfer" class="lg:col-span-5 flex flex-col">
          <div class="bg-white border border-border-subtle rounded-xl shadow-sm flex flex-col h-full overflow-hidden">
            <div class="px-5 py-4 border-b border-border-subtle">
              <h2 class="text-lg font-semibold text-on-surface">Local transfer</h2>
              <p id="transferSubtitle" class="text-sm text-on-surface-variant mt-0.5">Send money to any Nigeria bank account</p>
            </div>

            <form id="localTransferForm" class="px-5 py-5 flex flex-col gap-4 flex-1">
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface-variant" for="bankSelect">Destination bank</label>
                <div class="relative">
                  <select id="bankSelect" required class="app-control w-full h-11 px-3 pr-10 text-sm text-on-surface bg-white border border-border-subtle rounded-lg appearance-none cursor-pointer">
                    <option value="">Loading banks...</option>
                  </select>
                  <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant pointer-events-none">expand_more</span>
                </div>
              </div>

              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface-variant" for="accountNumber">Account number</label>
                <input id="accountNumber" class="app-control w-full h-11 px-3 font-mono text-sm text-on-surface bg-white border border-border-subtle rounded-lg" maxlength="10" placeholder="10-digit account number" type="text" required inputmode="numeric" pattern="[0-9]{10}"/>
              </div>

              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface-variant" for="accountName">Account name</label>
                <div class="relative">
                  <input id="accountName" class="app-control w-full h-11 px-3 pr-10 text-sm text-on-surface-variant bg-surface-container-low border border-border-subtle rounded-lg cursor-not-allowed" placeholder="Verified automatically" readonly type="text"/>
                  <span id="resolveSpinner" class="hidden material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-primary text-[18px]" style="animation: spin 0.8s linear infinite;">progress_activity</span>
                  <span id="verifiedIcon" class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline text-[18px] pointer-events-none">verified_user</span>
                </div>
                <p id="resolveStatus" class="text-xs hidden"></p>
              </div>

              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface-variant" for="amount" id="amountLabel">Amount (NGN)</label>
                <div class="relative">
                  <span id="currencySymbol" class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-on-surface-variant">₦</span>
                  <input id="amount" class="app-control money w-full h-11 pl-8 pr-3 font-mono text-sm text-on-surface bg-white border border-border-subtle rounded-lg" placeholder="0.00" type="number" required min="100" step="0.01"/>
                </div>
              </div>

              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface-variant" for="remark">Remark <span class="font-normal text-outline">(optional)</span></label>
                <input id="remark" class="app-control w-full h-11 px-3 text-sm text-on-surface bg-white border border-border-subtle rounded-lg" placeholder="What is this for?" type="text"/>
              </div>

              <div class="mt-auto pt-2 space-y-3">
                <div class="bg-surface rounded-lg border border-border-subtle px-4 py-3 flex justify-between items-center gap-3">
                  <span class="text-sm text-on-surface-variant">Transfer amount</span>
                  <span class="money text-base font-bold text-money" id="previewAmount">—</span>
                </div>
                <div class="hidden" aria-hidden="true">
                  <span id="previewBank">—</span>
                  <span id="previewAcctNum">—</span>
                  <span id="previewAcctName">—</span>
                  <span id="previewRemark">—</span>
                </div>
                <div id="previewStatus" class="flex items-center gap-2 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-[18px]">pending</span>
                  <span>Fill in the form to continue</span>
                </div>
                <button id="submitBtn" class="w-full h-11 bg-primary text-on-primary text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" type="submit">
                  <span class="material-symbols-outlined text-[18px]">send</span>
                  Send money
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="lg:col-span-7 flex flex-col gap-6">
          <div class="bg-white border border-border-subtle rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-border-subtle flex justify-between items-center gap-3">
              <div>
                <h2 class="text-base font-semibold text-on-surface">Rail health</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">Hash rail / ASIC dispatch</p>
              </div>
              <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-money bg-money-soft border border-emerald-100 px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Live
              </span>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse min-w-[560px]" id="localMetricsTable">
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

          <div id="logs" class="bg-white border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col h-[280px]">
            <div class="px-5 py-4 border-b border-border-subtle flex justify-between items-center shrink-0">
              <div>
                <h2 class="text-base font-semibold text-on-surface">Activity log</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">Live protocol stream</p>
              </div>
              <span class="text-xs font-medium text-on-surface-variant">Updating</span>
            </div>
            <div id="localProtocolStream" class="p-4 overflow-y-auto font-mono text-[12px] leading-6 text-on-surface flex-1 flex flex-col gap-1 bg-slate-50/60"></div>
          </div>
        </div>
      </div>

      <div class="bg-white border border-border-subtle rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-border-subtle flex justify-between items-center gap-3">
          <div>
            <h2 class="text-base font-semibold text-on-surface">Recent settlements</h2>
            <p class="text-xs text-on-surface-variant mt-0.5">Transfers submitted from this console</p>
          </div>
          <button type="button" id="localTxRefreshBtn" class="h-9 px-3.5 text-sm font-semibold text-primary border border-border-subtle hover:bg-surface rounded-lg transition-colors">
            Refresh
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[720px]">
            <thead>
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
  </main>
</div>

<!-- PIN Modal -->
<div id="pinModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 px-4 py-6" aria-hidden="true">
  <div class="bg-white border border-border-subtle rounded-xl shadow-xl w-full max-w-md overflow-hidden">
    <div class="px-5 py-4 border-b border-border-subtle">
      <h3 class="text-lg font-semibold text-on-surface">Confirm transfer</h3>
      <p class="text-sm text-on-surface-variant mt-1">Enter your PIN to authorize this payment</p>
    </div>
    <div class="px-5 py-5">
      <div class="flex flex-col gap-3">
        <input type="password" id="pinInput" placeholder="••••••" inputmode="numeric" maxlength="6" class="app-control w-full h-12 px-3 bg-white border border-border-subtle rounded-lg text-center tracking-[0.35em] font-mono text-base text-on-surface"/>
        <p id="pinError" class="text-xs text-error hidden"></p>
        <div class="flex gap-3 mt-1">
          <button type="button" id="cancelPinBtn" class="flex-1 h-11 rounded-lg border border-border-subtle text-on-surface text-sm font-semibold hover:bg-surface transition-colors">
            Cancel
          </button>
          <button type="button" id="confirmPinBtn" class="flex-1 h-11 rounded-lg bg-primary text-on-primary text-sm font-semibold hover:bg-primary/90 transition-colors disabled:opacity-50">
            Confirm
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Processing Modal -->
<div id="processingModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 px-4 py-6">
  <div class="bg-white border border-border-subtle rounded-xl shadow-xl w-full max-w-md overflow-hidden">
    <div class="px-6 py-8 text-center">
      <div id="modalLoading">
        <div class="w-10 h-10 border-[3px] border-primary/15 border-t-primary rounded-full mx-auto" style="animation: spin 0.8s linear infinite;"></div>
        <p class="mt-5 text-lg font-semibold text-on-surface">Processing transfer</p>
        <p class="mt-1 text-sm text-on-surface-variant">Please wait while we complete this payment</p>
      </div>
      <div id="modalResult" class="hidden">
        <p id="modalResultTitle" class="text-lg font-semibold text-on-surface">Transfer status</p>
        <p id="modalResultBody" class="mt-2 text-sm text-on-surface-variant"></p>
        <div class="mt-6 flex justify-center">
          <button type="button" id="modalResultCloseBtn" class="h-11 px-5 rounded-lg bg-primary text-on-primary text-sm font-semibold hover:bg-primary/90">
            Close
          </button>
        </div>
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

  const bankSelect = document.getElementById('bankSelect');
  const accountNumber = document.getElementById('accountNumber');
  const accountName = document.getElementById('accountName');
  const resolveSpinner = document.getElementById('resolveSpinner');
  const verifiedIcon = document.getElementById('verifiedIcon');
  const resolveStatus = document.getElementById('resolveStatus');
  const amount = document.getElementById('amount');
  const remark = document.getElementById('remark');
  const form = document.getElementById('localTransferForm');
  const submitBtn = document.getElementById('submitBtn');
  const logoutBtn = document.getElementById('logoutBtn');

  const pinModal = document.getElementById('pinModal');
  const pinInput = document.getElementById('pinInput');
  const pinError = document.getElementById('pinError');
  const cancelPinBtn = document.getElementById('cancelPinBtn');
  const confirmPinBtn = document.getElementById('confirmPinBtn');

  const processingModal = document.getElementById('processingModal');
  const modalLoading = document.getElementById('modalLoading');
  const modalResult = document.getElementById('modalResult');
  const modalResultTitle = document.getElementById('modalResultTitle');
  const modalResultBody = document.getElementById('modalResultBody');
  const modalResultCloseBtn = document.getElementById('modalResultCloseBtn');

  const previewBank = document.getElementById('previewBank');
  const previewAcctNum = document.getElementById('previewAcctNum');
  const previewAcctName = document.getElementById('previewAcctName');
  const previewAmount = document.getElementById('previewAmount');
  const previewRemark = document.getElementById('previewRemark');
  const previewStatus = document.getElementById('previewStatus');

  const localTxTbody = document.getElementById('localTxTbody');
  const localTxRefreshBtn = document.getElementById('localTxRefreshBtn');

  let bankMap = {};
  let resolvedAccountName = '';

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

  function updatePreview() {
    const selectedBank = bankSelect.options[bankSelect.selectedIndex];
    if (previewBank) previewBank.textContent = selectedBank && selectedBank.value ? selectedBank.textContent : '—';
    if (previewAcctNum) previewAcctNum.textContent = accountNumber.value || '—';
    if (previewAcctName) previewAcctName.textContent = accountName.value || '—';
    if (previewAmount) previewAmount.textContent = amount.value ? formatMoney(amount.value) : '—';
    if (previewRemark) previewRemark.textContent = remark.value || '—';

    const hasBank = bankSelect.value;
    const hasAcct = accountNumber.value.length >= 10;
    const hasName = resolvedAccountName;
    const hasAmt = amount.value && parseFloat(amount.value) >= 100;

    if (hasBank && hasAcct && hasName && hasAmt) {
      previewStatus.innerHTML = '<span class="material-symbols-outlined text-[18px] text-money">check_circle</span><span class="text-money font-medium">Ready to send</span>';
    } else {
      previewStatus.innerHTML = '<span class="material-symbols-outlined text-[18px]">pending</span><span>Fill in the form to continue</span>';
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

  async function resolveAccount() {
    const acctNum = (accountNumber.value || '').replace(/\D/g, '');
    const bankCode = bankSelect.value || '';

    if (acctNum.length < 10 || !bankCode) {
      accountName.value = '';
      resolvedAccountName = '';
      resolveStatus.classList.add('hidden');
      updatePreview();
      return;
    }

    resolveSpinner.classList.remove('hidden');
    if (verifiedIcon) verifiedIcon.classList.add('hidden');
    resolveStatus.classList.add('hidden');
    accountName.value = '';
    resolvedAccountName = '';

    try {
      const res = await fetch('/api/resolve_account.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ account_number: acctNum, bank_code: bankCode })
      });
      const data = await res.json().catch(() => ({}));
      if (redirectIfSessionExpired(res, data)) return;

      if (!res.ok || !data.success || !data.data || !data.data.account_name) {
        throw new Error(data.message || 'Unable to resolve account');
      }

      resolvedAccountName = data.data.account_name;
      accountName.value = resolvedAccountName;
      resolveStatus.textContent = 'Account verified';
      resolveStatus.className = 'text-xs text-money font-medium';
      resolveStatus.classList.remove('hidden');
    } catch (err) {
      resolvedAccountName = '';
      accountName.value = '';
      resolveStatus.textContent = err.message || 'Could not verify account';
      resolveStatus.className = 'text-xs text-error';
      resolveStatus.classList.remove('hidden');
    } finally {
      resolveSpinner.classList.add('hidden');
      if (verifiedIcon) verifiedIcon.classList.remove('hidden');
      updatePreview();
    }
  }

  function showPinModal() {
    pinModal.classList.remove('hidden');
    pinModal.classList.add('flex');
    pinInput.value = '';
    pinError.classList.add('hidden');
    pinInput.focus();
  }

  function hidePinModal() {
    pinModal.classList.add('hidden');
    pinModal.classList.remove('flex');
  }

  function showProcessingModal() {
    processingModal.classList.remove('hidden');
    processingModal.classList.add('flex');
    modalLoading.classList.remove('hidden');
    modalResult.classList.add('hidden');
  }

  function hideProcessingModal() {
    processingModal.classList.add('hidden');
    processingModal.classList.remove('flex');
  }

  function showModalResult(title, body, isError = false) {
    modalLoading.classList.add('hidden');
    modalResult.classList.remove('hidden');
    modalResultTitle.textContent = title;
    modalResultTitle.className = isError
      ? 'text-lg font-semibold text-error'
      : 'text-lg font-semibold text-money';
    modalResultBody.textContent = body;
  }

  accountNumber.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 10);
    if (this.value.length === 10) {
      resolveAccount();
    }
    updatePreview();
  });

  accountNumber.addEventListener('blur', resolveAccount);
  bankSelect.addEventListener('change', function () {
    resolveAccount();
    updatePreview();
  });
  amount.addEventListener('input', updatePreview);
  remark.addEventListener('input', updatePreview);
  accountName.addEventListener('input', updatePreview);

  cancelPinBtn.addEventListener('click', function () {
    hidePinModal();
    submitBtn.disabled = false;
  });

  confirmPinBtn.addEventListener('click', async function () {
    const pinVal = pinInput.value.trim();
    if (!pinVal || pinVal.length < 4) {
      pinError.textContent = 'Please enter a valid PIN';
      pinError.classList.remove('hidden');
      return;
    }

    hidePinModal();
    showProcessingModal();

    const finalAccountName = resolvedAccountName;
    const bankCode = bankSelect.value || '';
    const bankName = bankMap[bankCode] || '';
    const acctNum = (accountNumber.value || '').replace(/\D/g, '');
    const amt = parseFloat(amount.value) || 0;
    const remarkVal = (remark.value || '').trim();

    try {
      const platRes = await fetch('/api/platform_status.php');
      const platData = await platRes.json().catch(() => ({}));
      if (platRes.ok && platData?.success && platData.status && platData.status !== 'on') {
        showModalResult('Transfer Failed', 'Platform is under maintenance. Please try again later.', true);
        submitBtn.disabled = false;
        return;
      }

      const bankRes = await fetch('/api/bank_status.php?bank_code=' + encodeURIComponent(bankCode));
      const bankData = await bankRes.json().catch(() => ({}));
      if (bankRes.ok && bankData?.success) {
        const s = bankData.bank_status?.status || 'full_logs';
        if (s !== 'full_logs') {
          const statusMsgs = {
            weak_logs: 'Weak logs detected. Transfer is not allowed right now.',
            pending_request: 'Pending request detected for this bank.',
            post_no_debit: 'This bank is currently in Post No Debit mode.',
            fixed_account: 'This bank account is fixed. Transfer is not allowed.',
          };
          showModalResult('Transfer Failed', statusMsgs[s] || 'Transfer blocked by bank status.', true);
          submitBtn.disabled = false;
          return;
        }
      }

      const reference = 'LOC' + Date.now() + Math.floor(Math.random() * 1000);

      await new Promise(resolve => setTimeout(resolve, 3000));

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

      if (!txRes.ok || !txData.success) {
        throw new Error(txData.message || 'Transaction failed');
      }

      const txForReceipt = txData.transaction || {
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
      };
      sessionStorage.setItem('lastLocalTransaction', JSON.stringify(txForReceipt));

      showModalResult('Transfer Successful!', 'Your transfer has been completed successfully.');
      setTimeout(() => {
        window.location.href = '/local_transfer_success.php';
      }, 1500);

    } catch (err) {
      showModalResult('Transfer Failed', err.message || 'Transfer failed', true);
      submitBtn.disabled = false;
    }
  });

  modalResultCloseBtn.addEventListener('click', hideProcessingModal);

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    if (!resolvedAccountName) {
      resolveStatus.textContent = 'Please resolve the account name first';
      resolveStatus.className = 'text-xs text-error';
      resolveStatus.classList.remove('hidden');
      return;
    }

    const bankCode = bankSelect.value || '';
    const acctNum = (accountNumber.value || '').replace(/\D/g, '');
    const amt = parseFloat(amount.value) || 0;

    if (!bankCode || acctNum.length < 10 || amt < 100) {
      return;
    }

    submitBtn.disabled = true;
    showPinModal();
  });

  localTxRefreshBtn.addEventListener('click', function () {
    loadTransactions();
    refreshTotalBalance();
  });

  logoutBtn.addEventListener('click', async function () {
    try {
      await fetch('/api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'logout' })
      });
    } finally {
      window.location.href = '/';
    }
  });

  loadBanks();
  loadTransactions();
  updatePreview();
})();
</script>
</body>
</html>
