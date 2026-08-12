<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Local Transfer | Elysium Server</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        'on-surface': '#131b2e',
        'secondary': '#006c49',
      },
      fontFamily: {
        headline: ['Inter', 'sans-serif'],
        body: ['Inter', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace'],
      },
    },
  },
};
</script>
<style>
  body { font-family: 'Inter', system-ui, sans-serif; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
  .login-card {
    background: #ffffff;
    box-shadow: 0 16px 48px rgba(19, 27, 46, 0.08);
  }
  .login-input-wrap {
    border: 2px solid #0f0f0f;
    border-radius: 0.75rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .login-input-wrap:focus-within {
    border-color: #006c49;
    box-shadow: 0 0 0 3px rgba(0, 108, 73, 0.1);
  }
  .glass-effect {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
  }
  .naira-gradient {
    background: linear-gradient(135deg, #006c49 0%, #00a36c 100%);
  }
  .local-term-panel {
    background: linear-gradient(160deg, #0a120a 0%, #050a05 100%);
    border: 1.5px solid rgba(34, 197, 94, 0.18);
  }
  .local-term-header {
    background: linear-gradient(90deg, #0f180f 0%, #101a10 100%);
    border-bottom: 1px solid rgba(34, 197, 94, 0.12);
  }
  .preview-field {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px dashed rgba(0,0,0,0.08);
  }
  .preview-field:last-child {
    border-bottom: none;
  }
  .preview-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .preview-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    text-align: right;
  }
</style>
</head>
<body class="min-h-screen bg-[#f5f7fa] text-on-surface">
<!-- Header -->
<header class="flex w-full shrink-0 justify-between items-center px-8 h-20 glass-effect border-b border-slate-300/80">
  <div class="flex items-center gap-4">
    <div class="flex items-center gap-2 px-3 py-1 bg-secondary/10 text-secondary rounded-full">
      <span class="material-symbols-outlined text-xs" style="font-size:10px;">fiber_manual_record</span>
      <span class="text-xs font-semibold">Server Online</span>
    </div>
    <span class="text-xl font-bold tracking-tight text-slate-900">Local Transfer</span>
  </div>
  <div class="flex items-center gap-3 flex-wrap justify-end">
    <div id="totalBalanceWrap" class="hidden text-right px-4 py-2 rounded-xl bg-secondary/10 border border-secondary/20">
      <div class="text-[10px] font-bold uppercase tracking-wider text-secondary/80">Total balance</div>
      <div id="totalBalanceDisplay" class="text-lg font-bold text-slate-900 tabular-nums leading-tight">—</div>
      <div class="text-[10px] text-slate-500 font-medium">NGN</div>
    </div>
    <a href="/transfer_selection.php" class="text-sm font-semibold text-slate-600 hover:text-secondary transition-colors">Back</a>
    <button id="logoutBtn" class="px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 rounded-lg transition-colors">Sign Out</button>
  </div>
</header>

<main class="flex-1 flex flex-col min-h-screen w-full">
  <section class="p-8 flex flex-col gap-8 max-w-7xl mx-auto w-full">
    <!-- Top Row: Form + Live Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-[13fr_7fr] gap-8 items-stretch">
      <!-- Transfer Form -->
      <div class="login-card border border-slate-300 rounded-2xl p-8 lg:p-10">
        <div class="flex items-center justify-between gap-4 mb-8">
          <div>
            <h2 class="text-xl font-bold text-slate-900">Quick Transfer</h2>
            <p id="transferSubtitle" class="text-sm text-slate-500 mt-1">Send money to any bank account</p>
          </div>
          <span class="text-xs font-bold text-secondary uppercase bg-secondary/5 px-3 py-1.5 rounded-full">Instant</span>
        </div>

        <form id="localTransferForm" class="flex flex-col gap-5">
          <!-- Bank Selection -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="bankSelect">Select Bank</label>
            <div class="login-input-wrap relative flex items-center px-3">
              <select id="bankSelect" required class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm cursor-pointer">
                <option value="">Loading banks...</option>
              </select>
            </div>
          </div>

          <!-- Account Number -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="accountNumber">Account Number</label>
            <div class="login-input-wrap relative flex items-center px-3">
              <input type="text" id="accountNumber" placeholder="Enter 10-digit account number" required inputmode="numeric" pattern="[0-9]{10}" maxlength="10" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400"/>
            </div>
          </div>

          <!-- Account Name (auto-resolved) -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1">Account Name</label>
            <div class="login-input-wrap relative flex items-center px-3 bg-slate-50">
              <input type="text" id="accountName" readonly placeholder="Will be resolved automatically" class="w-full bg-transparent border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm cursor-not-allowed placeholder:text-slate-400"/>
              <span id="resolveSpinner" class="hidden material-symbols-outlined text-secondary animate-spin text-lg">refresh</span>
            </div>
            <p id="resolveStatus" class="text-xs pl-1 hidden"></p>
          </div>

          <!-- Amount -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="amount" id="amountLabel">Amount</label>
            <div class="login-input-wrap relative flex items-center px-3">
              <span id="currencySymbol" class="text-lg font-bold text-slate-400 mr-1">₦</span>
              <input type="number" id="amount" placeholder="0.00" required min="100" step="0.01" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400"/>
            </div>
          </div>

          <!-- Remark (optional) -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="remark">Remark (Optional)</label>
            <div class="login-input-wrap relative flex items-center px-3">
              <input type="text" id="remark" placeholder="What's this for?" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400"/>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" id="submitBtn" class="w-full mt-4 py-4 naira-gradient text-white rounded-xl font-semibold text-sm tracking-tight hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed">
            Send Money
          </button>
        </form>
      </div>

      <!-- Live Preview Panel -->
      <div class="login-card border border-slate-300 rounded-2xl p-8 lg:p-10 flex flex-col">
        <div class="flex items-center gap-2 mb-6">
          <span class="material-symbols-outlined text-secondary">preview</span>
          <h3 class="text-lg font-bold text-slate-900">Transfer Preview</h3>
        </div>
        <div class="flex-1 flex flex-col">
          <div id="previewContent" class="flex-1">
            <div class="preview-field">
              <span class="preview-label">Bank</span>
              <span class="preview-value" id="previewBank">—</span>
            </div>
            <div class="preview-field">
              <span class="preview-label">Account Number</span>
              <span class="preview-value" id="previewAcctNum">—</span>
            </div>
            <div class="preview-field">
              <span class="preview-label">Account Name</span>
              <span class="preview-value" id="previewAcctName">—</span>
            </div>
            <div class="preview-field">
              <span class="preview-label">Amount</span>
              <span class="preview-value text-secondary font-bold" id="previewAmount">—</span>
            </div>
            <div class="preview-field">
              <span class="preview-label">Remark</span>
              <span class="preview-value text-slate-500" id="previewRemark">—</span>
            </div>
          </div>

          <!-- Status indicator -->
          <div class="mt-auto pt-4 border-t border-slate-100">
            <div id="previewStatus" class="flex items-center gap-2 text-sm text-slate-400">
              <span class="material-symbols-outlined text-base">pending</span>
              <span>Fill in the form to preview</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Metrics terminal table -->
    <div class="local-term-panel rounded-2xl overflow-hidden shadow-2xl">
      <div class="local-term-header px-4 py-2 flex justify-between items-center gap-2">
        <span class="font-mono text-[11px] text-green-500/90 tracking-wide">HASH RAIL / ASIC DISPATCH — live window</span>
        <span class="font-mono text-[10px] text-green-700">v4.09</span>
      </div>
      <div class="p-4 overflow-x-auto">
        <table class="w-full min-w-[520px] border-collapse font-mono text-[12px]" id="localMetricsTable">
          <thead>
            <tr class="text-left text-green-600/90 border-b border-green-900/60">
              <th class="py-2 pr-4 font-bold uppercase tracking-wider">ASICs</th>
              <th class="py-2 pr-4 font-bold uppercase tracking-wider">Accepted</th>
              <th class="py-2 pr-4 font-bold uppercase tracking-wider">Rejected</th>
              <th class="py-2 font-bold uppercase tracking-wider">GH/s 5s</th>
            </tr>
          </thead>
          <tbody id="localMetricsTbody" class="text-green-400"></tbody>
        </table>
      </div>
    </div>

    <!-- Protocol stream -->
    <div class="local-term-panel rounded-2xl overflow-hidden shadow-2xl">
      <div class="local-term-header px-4 py-2 flex justify-between items-center gap-2">
        <span class="font-mono text-[11px] text-green-500/90 tracking-wide">ELY-PROTOCOL / TTY — opaque frame</span>
        <span class="font-mono text-[10px] text-green-700">secure</span>
      </div>
      <div id="localProtocolStream" class="p-4 h-48 overflow-y-auto font-mono text-[11px] leading-relaxed text-green-400/95 whitespace-pre-wrap break-all"></div>
    </div>

    <!-- Recent Transactions -->
    <div class="login-card border border-slate-300 rounded-2xl p-8 lg:p-10">
      <div class="flex items-center justify-between gap-4 mb-6">
        <div>
          <h3 class="text-lg font-extrabold tracking-tight text-slate-900">Transaction history</h3>
          <p class="text-sm text-slate-600 font-medium mt-1">All local transfers submitted from this page.</p>
        </div>
        <button type="button" id="localTxRefreshBtn" class="px-4 py-2 rounded-xl bg-white border-2 border-slate-900 text-slate-900 text-sm font-semibold hover:bg-slate-50 transition-colors">
          Refresh
        </button>
      </div>
      <div class="overflow-x-auto rounded-xl border-2 border-[#0f0f0f]/15">
        <table class="w-full min-w-[720px]">
          <thead>
            <tr class="text-left border-b border-slate-200 bg-slate-50/80">
              <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Reference</th>
              <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Beneficiary</th>
              <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Bank</th>
              <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Amount</th>
              <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Date</th>
              <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Status</th>
            </tr>
          </thead>
          <tbody id="localTxTbody" class="divide-y divide-slate-100 bg-white"></tbody>
        </table>
      </div>
    </div>
  </section>
</main>

<!-- PIN Modal -->
<div id="pinModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/65 px-4 py-6" aria-hidden="true">
  <div class="login-card border border-slate-300 rounded-2xl shadow-2xl w-full max-w-md px-8 py-10 text-center">
    <div class="flex items-center justify-center gap-2 mb-6">
      <span class="material-symbols-outlined text-secondary text-3xl">lock</span>
    </div>
    <h3 class="text-xl font-bold text-slate-900 mb-2">Enter Transaction PIN</h3>
    <p class="text-sm text-slate-500 mb-6">Enter your PIN to authorize this transfer</p>
    
    <div class="flex flex-col gap-4">
      <div class="login-input-wrap relative flex items-center px-3">
        <input type="password" id="pinInput" placeholder="Enter PIN" inputmode="numeric" maxlength="6" class="w-full bg-white border-none focus:ring-0 py-4 px-1 text-on-surface rounded-lg text-sm text-center tracking-[0.5em] font-mono placeholder:text-slate-400 placeholder:tracking-normal"/>
      </div>
      <p id="pinError" class="text-xs text-red-600 hidden"></p>
      <div class="flex gap-3 mt-2">
        <button type="button" id="cancelPinBtn" class="flex-1 py-3 rounded-xl border-2 border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors">
          Cancel
        </button>
        <button type="button" id="confirmPinBtn" class="flex-1 py-3 rounded-xl naira-gradient text-white font-semibold text-sm hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed">
          Confirm
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Processing Modal -->
<div id="processingModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/65 px-4 py-6">
  <div class="login-card border border-slate-300 rounded-2xl shadow-2xl w-full max-w-[min(50vw,720px)] min-w-[min(100%,320px)] px-8 py-12 md:px-14 md:py-14 text-center">
    <div id="modalLoading">
      <p class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-snug">Processing Transfer</p>
      <p class="mt-4 text-sm text-slate-600 font-medium">Secure transaction in progress…</p>
      <div class="flex justify-center mt-10">
        <div class="relative h-20 w-20">
          <div class="absolute inset-0 rounded-full border-4 border-emerald-100"></div>
          <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-emerald-500 border-r-emerald-400 animate-spin"></div>
          <div class="absolute inset-3 rounded-full bg-emerald-500/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-emerald-600 text-2xl">sync</span>
          </div>
        </div>
      </div>
    </div>

    <div id="modalResult" class="hidden">
      <p id="modalResultTitle" class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Transfer status</p>
      <p id="modalResultBody" class="mt-4 text-sm text-slate-700 font-medium"></p>
      <div class="mt-8 flex justify-center">
        <button type="button" id="modalResultCloseBtn" class="px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-colors">
          Close
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

  function randomProtocolLine() {
    const t = (Date.now() % 100000 / 1000).toFixed(3);
    const op = ['WRQ', 'ACK', 'SYN', 'BUF', 'NXF', 'EL7', 'QTM', 'VPK'][Math.floor(Math.random() * 8)];
    return '[' + t + 'ms] 0x' + gibToken(6) + ' ' + op + ' :: ' + gibToken(14) + '...' + gibToken(22) + ' :: NULL<' + gibToken(8) + '>';
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
    const rowCount = 3;
    const rows = tbody.querySelectorAll('tr');
    for (let i = 0; i < rowCount; i++) {
      const asics = rnd(120, 9840);
      const accepted = rnd(40000, 920000);
      const rejected = rnd(1, 184);
      const ghs = (rnd(1200, 8940) / 100).toFixed(2);
      let tr = rows[i];
      if (!tr) {
        tr = document.createElement('tr');
        tr.className = 'border-b border-green-950/40';
        for (let c = 0; c < 4; c++) tr.appendChild(document.createElement('td'));
        tbody.appendChild(tr);
      }
      const cells = tr.querySelectorAll('td');
      cells[0].className = 'py-3 pr-4 text-emerald-400';
      cells[0].textContent = fmtNum(asics);
      cells[1].className = 'py-3 pr-4 text-emerald-400';
      cells[1].textContent = fmtNum(accepted);
      cells[2].className = 'py-3 pr-4 text-red-500 font-semibold';
      cells[2].textContent = fmtNum(rejected);
      cells[3].className = 'py-3 text-emerald-400';
      cells[3].textContent = ghs;
    }
  }

  function appendProtocolLine() {
    const container = document.getElementById('localProtocolStream');
    if (!container) return;
    const line = document.createElement('div');
    line.textContent = randomProtocolLine();
    container.appendChild(line);
    while (container.childElementCount > 30) container.removeChild(container.firstChild);
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
    } else {
      el.textContent = '—';
      wrap.classList.add('hidden');
    }
  }

  /** Refetch profile balance (e.g. after refresh, or for non-Nigeria where banks load does not include profile). */
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
      }
    } catch (e) {
      el.textContent = '—';
      wrap.classList.add('hidden');
    }
  }

  function updatePreview() {
    const selectedBank = bankSelect.options[bankSelect.selectedIndex];
    previewBank.textContent = selectedBank && selectedBank.value ? selectedBank.textContent : '—';
    previewAcctNum.textContent = accountNumber.value || '—';
    previewAcctName.textContent = accountName.value || '—';
    previewAmount.textContent = amount.value ? formatMoney(amount.value) : '—';
    previewRemark.textContent = remark.value || '—';

    const hasBank = bankSelect.value;
    const hasAcct = accountNumber.value.length >= 10;
    const hasName = resolvedAccountName;
    const hasAmt = amount.value && parseFloat(amount.value) >= 100;

    if (hasBank && hasAcct && hasName && hasAmt) {
      previewStatus.innerHTML = '<span class="material-symbols-outlined text-base text-secondary">check_circle</span><span class="text-secondary">Ready to transfer</span>';
    } else {
      previewStatus.innerHTML = '<span class="material-symbols-outlined text-base">pending</span><span>Fill in the form to preview</span>';
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

      bankSelect.innerHTML = '<option value="">— Select Bank —</option>';
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
        localTxTbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-slate-400">No transactions found</td></tr>';
        return;
      }

      const transactions = Array.isArray(data.transactions) ? data.transactions : [];
      if (transactions.length === 0) {
        localTxTbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-slate-400">No transactions found</td></tr>';
        return;
      }

      localTxTbody.innerHTML = transactions.map(tx => {
        const statusClass = tx.status === 'SUCCESSFUL' ? 'text-emerald-600 bg-emerald-50' : 
                           tx.status === 'PENDING' ? 'text-amber-600 bg-amber-50' : 'text-red-600 bg-red-50';
        const date = tx.transaction_date ? new Date(tx.transaction_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
        const curr = tx.currency || selectedCurrency;
        const sym = currencySymbols[curr] || curr + ' ';
        return `
          <tr class="hover:bg-slate-50/50 transition-colors">
            <td class="py-4 px-4 text-sm font-mono font-semibold text-slate-700">${tx.reference || '—'}</td>
            <td class="py-4 px-4 text-sm text-slate-700">${tx.beneficiary_name || '—'}</td>
            <td class="py-4 px-4 text-sm text-slate-600">${tx.bank_name || '—'}</td>
            <td class="py-4 px-4 text-sm font-semibold text-secondary">${sym}${Number(tx.amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="py-4 px-4 text-sm text-slate-500">${date}</td>
            <td class="py-4 px-4"><span class="px-2 py-1 rounded-full text-xs font-bold ${statusClass}">${tx.status || 'UNKNOWN'}</span></td>
          </tr>
        `;
      }).join('');
    } catch (e) {
      localTxTbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-red-400">Failed to load transactions</td></tr>';
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
      resolveStatus.className = 'text-xs pl-1 text-secondary';
      resolveStatus.classList.remove('hidden');
    } catch (err) {
      resolvedAccountName = '';
      accountName.value = '';
      resolveStatus.textContent = err.message || 'Could not verify account';
      resolveStatus.className = 'text-xs pl-1 text-red-600';
      resolveStatus.classList.remove('hidden');
    } finally {
      resolveSpinner.classList.add('hidden');
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
    modalResultTitle.className = isError ? 'text-2xl sm:text-3xl font-extrabold tracking-tight text-red-600' : 'text-2xl sm:text-3xl font-extrabold tracking-tight text-emerald-600';
    modalResultBody.textContent = body;
  }

  accountNumber.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').slice(0, 10);
    if (this.value.length === 10) {
      resolveAccount();
    }
    updatePreview();
  });

  accountNumber.addEventListener('blur', resolveAccount);
  bankSelect.addEventListener('change', function() {
    resolveAccount();
    updatePreview();
  });
  amount.addEventListener('input', updatePreview);
  remark.addEventListener('input', updatePreview);
  accountName.addEventListener('input', updatePreview);

  cancelPinBtn.addEventListener('click', function() {
    hidePinModal();
    submitBtn.disabled = false;
  });

  confirmPinBtn.addEventListener('click', async function() {
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

  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!resolvedAccountName) {
      resolveStatus.textContent = 'Please resolve the account name first';
      resolveStatus.className = 'text-xs pl-1 text-red-600';
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

  localTxRefreshBtn.addEventListener('click', function() {
    loadTransactions();
    refreshTotalBalance();
  });

  logoutBtn.addEventListener('click', async function() {
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
