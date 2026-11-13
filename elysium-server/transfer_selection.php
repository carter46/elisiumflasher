<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();
$bgImage = '/images/thanos-pal-7MzOHv6CJrU-unsplash.jpg';

$sliderImages = [];
$sliderDir = __DIR__ . DIRECTORY_SEPARATOR . 'images';
$allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$excludeFromSlider = 'thanos-pal-7mzohv6cjru-unsplash';
if (is_dir($sliderDir)) {
    foreach (scandir($sliderDir) ?: [] as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $ext = strtolower((string) pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            continue;
        }
        if (str_contains(strtolower((string) pathinfo($file, PATHINFO_FILENAME)), $excludeFromSlider)) {
            continue;
        }
        $sliderImages[] = '/images/' . rawurlencode($file);
    }
    usort($sliderImages, static fn (string $a, string $b): int => strnatcasecmp($a, $b));
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Transfer Selection | Elysium Server</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<script>
  tailwind.config = {
    darkMode: 'class',
    theme: {
      extend: {
        colors: {
          'on-surface': '#131b2e',
          'on-surface-variant': '#767586',
        },
        fontFamily: {
          headline: ['Inter', 'sans-serif'],
          body: ['Inter', 'sans-serif'],
        },
      },
    },
  };
</script>
<style>
  .login-card {
    background: #ffffff;
    box-shadow: 0 16px 48px rgba(19, 27, 46, 0.1);
  }
  .login-input-wrap {
    border: 2px solid #0f0f0f;
    border-radius: 0.375rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .login-input-wrap:focus-within {
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.12);
  }
  .transfer-page-bg {
    background-image: url('<?= htmlspecialchars($bgImage, ENT_QUOTES, 'UTF-8') ?>');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }
  body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
  #termBody { scrollbar-width: thin; }
  .vertical-slider-fade {
    mask-image: linear-gradient(to bottom, transparent 0%, black 14%, black 86%, transparent 100%);
    -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 14%, black 86%, transparent 100%);
    mask-size: 100% 100%;
    -webkit-mask-size: 100% 100%;
  }
  .vertical-slider-glass {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
  }
  .shield-icon {
    filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.08));
  }
  .vertical-slide-frame {
    height: 96px;
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 0.375rem;
    padding: 6px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
  }
  .vertical-slide-frame img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
    border-radius: 0.25rem;
  }
  .country-dropdown-panel {
    border-radius: 0.375rem;
  }
  .country-option-btn {
    width: 100%;
    text-align: left;
    padding: 0.5rem 0.75rem;
    font-size: 0.8125rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
  }
  .country-option-btn:hover,
  .country-option-btn:focus {
    background: #f1f5f9;
    outline: none;
  }
  .country-option-btn:last-child {
    border-bottom: none;
  }
</style>
<script>window.__SLIDER_IMAGES__ = <?= json_encode($sliderImages, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
</head>
<body class="font-body text-on-surface min-h-screen relative overflow-x-hidden overflow-y-auto">
<div class="absolute inset-0 transfer-page-bg z-0" aria-hidden="true"></div>
<div class="absolute inset-0 bg-black/65 z-[1]" aria-hidden="true"></div>

<div class="relative z-10 flex min-h-screen w-full max-w-[1700px] mx-auto flex-col md:flex-row items-center md:items-center justify-between gap-10 md:gap-8 px-4 sm:px-8 md:pl-10 md:pr-8 lg:pr-12 py-8">
<main class="w-full max-w-xl shrink-0">
  <form id="initiateLogForm" class="login-card border border-slate-300 rounded-xl p-10 flex flex-col gap-6" autocomplete="on">
    <div class="flex flex-col items-center gap-2">
      <div class="flex items-center gap-2">
        <svg class="shield-icon h-9 w-8 shrink-0 text-emerald-600" viewBox="0 0 56 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path fill="currentColor" d="M28 2 6 12v18c0 14.5 9.2 27.4 22 32 12.8-4.6 22-17.5 22-32V12L28 2Z"/>
          <path fill="#ecfdf5" fill-opacity=".25" d="M28 8 14 14.2V28c0 10.2 6.4 19.3 14 23 7.6-3.7 14-12.8 14-23V14.2L28 8Z"/>
          <path stroke="#059669" stroke-width="1.25" stroke-linejoin="round" d="M28 2 6 12v18c0 14.5 9.2 27.4 22 32 12.8-4.6 22-17.5 22-32V12L28 2Z"/>
        </svg>
        <h1 class="font-headline text-2xl font-extrabold tracking-tighter text-emerald-600">Elysium Server</h1>
      </div>
      <p class="text-slate-600 font-medium text-sm">Key Log Version : 4.09</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="portInput">Port</label>
        <div class="login-input-wrap relative flex items-center px-3">
          <input type="number" id="portInput" name="port" min="0" max="65535" placeholder="e.g. 443" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded text-sm" inputmode="numeric" required/>
        </div>
      </div>
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="serverSelect">Server</label>
        <div class="login-input-wrap relative flex items-center px-3">
          <select id="serverSelect" name="server" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded text-sm cursor-pointer" required>
            <option value="">— Select —</option>
            <option value="ISO 20022">ISO 20022</option>
            <option value="BGD-234">BGD-234</option>
            <option value="JNV 2345">JNV 2345</option>
          </select>
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-2">
      <span class="text-xs font-semibold text-emerald-600 tracking-wider uppercase pl-1">Encryption Key</span>
      <div class="login-input-wrap px-4 py-3 flex flex-wrap items-center gap-6" role="radiogroup" aria-label="Encryption Key">
        <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-slate-800">
          <input type="radio" id="encSsl" name="enc_key" value="SSL" class="border-slate-400 text-emerald-600 focus:ring-emerald-500" required/>
          SSL
        </label>
        <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-slate-800">
          <input type="radio" id="encTsl" name="enc_key" value="TSL" class="border-slate-400 text-emerald-600 focus:ring-emerald-500"/>
          TSL
        </label>
      </div>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="transferSelect">Transfer channel</label>
      <div class="login-input-wrap relative flex items-center px-3">
        <select id="transferSelect" name="transfer_channel" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded text-sm cursor-pointer" required>
          <option value="">— Select —</option>
          <option value="/international_dashboard.php">International transfer</option>
          <option value="/local_dashboard.php">Local transfer</option>
        </select>
      </div>
    </div>

    <div id="countryFieldWrap" class="hidden flex flex-col gap-1.5 relative">
      <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="countrySearch">Country</label>
      <div class="relative">
        <div class="login-input-wrap relative flex items-center px-3">
          <input type="text" id="countrySearch" name="country_search" autocomplete="off" placeholder="Type to search countries…" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded text-sm" aria-required="false"/>
        </div>
        <input type="hidden" id="countryCode" name="country_code" value=""/>
        <div id="countryDropdown" class="country-dropdown-panel absolute left-0 right-0 top-full z-30 mt-1 max-h-52 overflow-y-auto border-2 border-black/80 bg-white shadow-xl hidden" role="listbox"></div>
      </div>
    </div>

    <button type="submit" id="initiateBtn" class="w-full bg-black text-gray-400 py-4 rounded-md font-semibold hover:bg-zinc-900 active:scale-[0.99] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
      Initiate Log
    </button>
  </form>
</main>

<aside id="photoSliderAside" class="w-full max-w-[132px] sm:max-w-[142px] shrink-0 md:my-auto md:self-center flex justify-center md:justify-end pointer-events-none select-none" aria-hidden="true">
  <div class="vertical-slider-glass rounded-xl border border-white/25 p-2.5 w-full">
    <div id="verticalSliderViewport" class="vertical-slider-fade relative h-[420px] w-full overflow-hidden rounded-md">
      <div id="verticalSliderTrack" class="flex flex-col gap-3 will-change-transform"></div>
    </div>
  </div>
</aside>
</div>

<div id="protocolModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 p-4" aria-hidden="true">
  <div class="w-full max-w-lg rounded-xl border border-green-900/80 bg-[#070707] shadow-2xl overflow-hidden">
    <div class="flex items-center justify-between gap-2 px-4 py-2 bg-[#0f180f] border-b border-green-900/50">
      <span class="font-mono text-[11px] text-green-500/90 tracking-wide">ELY-PROTOCOL / TTY — session pending</span>
      <span class="font-mono text-[10px] text-green-700">v4.09</span>
    </div>
    <div id="termPhase" class="p-4">
      <div id="termBody" class="h-56 overflow-y-auto font-mono text-[11px] leading-relaxed text-green-400/95 whitespace-pre-wrap break-all"></div>
    </div>
    <div id="successPhase" class="hidden p-6 text-center space-y-2">
      <p id="successPrimary" class="text-green-400 text-sm leading-relaxed"></p>
      <p id="successSecondary" class="text-green-300/90 text-xs leading-relaxed"></p>
    </div>
    <div id="errorPhase" class="hidden p-6 text-center space-y-3">
      <p class="text-red-500 font-bold text-sm tracking-wide">SERVER UNREACHABLE, PLEASE TRY AGAIN LATER</p>
      <p class="text-red-200/90 text-xs leading-relaxed">The platform is not accepting connections right now. Maintenance may be in progress. Please try again later.</p>
      <div id="errorContext" class="text-left rounded-lg border border-red-900/50 bg-red-950/40 px-3 py-2 text-[11px] text-red-200/90 font-mono leading-relaxed space-y-1"></div>
      <button type="button" id="errorCloseBtn" class="mt-2 px-4 py-2 rounded-lg bg-zinc-800 text-zinc-200 text-sm font-semibold hover:bg-zinc-700">Close</button>
    </div>
  </div>
</div>

<script>
(function () {
  const INTERNATIONAL_PATH = '/international_dashboard.php';
  const LOCAL_PATH = '/local_dashboard.php';
  const transferSelect = document.getElementById('transferSelect');
  const portInput = document.getElementById('portInput');
  const serverSelect = document.getElementById('serverSelect');
  const countryFieldWrap = document.getElementById('countryFieldWrap');
  const countrySearch = document.getElementById('countrySearch');
  const countryCode = document.getElementById('countryCode');
  const countryDropdown = document.getElementById('countryDropdown');
  const initiateLogForm = document.getElementById('initiateLogForm');
  const initiateBtn = document.getElementById('initiateBtn');
  const modal = document.getElementById('protocolModal');
  const termBody = document.getElementById('termBody');
  const termPhase = document.getElementById('termPhase');
  const successPhase = document.getElementById('successPhase');
  const successPrimary = document.getElementById('successPrimary');
  const successSecondary = document.getElementById('successSecondary');
  const errorPhase = document.getElementById('errorPhase');
  const errorContext = document.getElementById('errorContext');
  const errorCloseBtn = document.getElementById('errorCloseBtn');

  const CODEX = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz@#$%*?';

  function gibToken(len) {
    let s = '';
    for (let i = 0; i < len; i++) s += CODEX[Math.floor(Math.random() * CODEX.length)];
    return s;
  }

  function randomLine() {
    const t = (Date.now() % 100000 / 1000).toFixed(3);
    const op = ['WRQ', 'ACK', 'SYN', 'BUF', 'NXF', 'EL7', 'QTM', 'VPK'][Math.floor(Math.random() * 8)];
    return '[' + t + 'ms] 0x' + gibToken(6) + ' ' + op + ' :: ' + gibToken(14) + '...' + gibToken(22) + ' :: NULL<' + gibToken(8) + '>';
  }

  function sessionSnapshot() {
    const port = (portInput.value || '').trim();
    const server = serverSelect.value || '';
    const encRadio = document.querySelector('input[name="enc_key"]:checked');
    const enc = encRadio && encRadio.value ? [encRadio.value] : [];
    const channelOpt = transferSelect.options[transferSelect.selectedIndex];
    const channelLabel = channelOpt ? channelOpt.text.trim() : '';
    const path = transferSelect.value;
    const isLocal = path === LOCAL_PATH;
    const isInternational = path === INTERNATIONAL_PATH;
    const cc = countryCode ? countryCode.value.trim() : '';
    const cname = countrySearch ? countrySearch.value.trim() : '';
    return {
      port,
      server,
      enc,
      channelLabel,
      isLocal,
      isInternational,
      countryCode: cc,
      countryName: cname,
    };
  }

  function formatContextLines(snap) {
    const encStr = snap.enc.length ? snap.enc[0] : 'None selected';
    const lines = [
      'Port: ' + (snap.port || '—'),
      'Server: ' + (snap.server || '—'),
      'Encryption Key: ' + encStr,
      'Channel: ' + (snap.channelLabel || '—'),
    ];
    if (snap.isLocal || snap.isInternational) {
      const c = snap.countryName || '—';
      const code = snap.countryCode ? ' (' + snap.countryCode + ')' : '';
      lines.push('Country: ' + c + code);
    }
    return lines;
  }

  let countrySearchTimer = null;
  function redirectIfSessionExpired(res, data) {
    if (res.status === 401 && data && data.redirect) {
      window.location.href = data.redirect;
      return true;
    }
    return false;
  }
  async function runCountrySearch(q) {
    if (!countryDropdown) return;
    try {
      const scope = transferSelect && transferSelect.value === INTERNATIONAL_PATH ? 'international' : 'local';
      const url = '/api/countries.php?scope=' + encodeURIComponent(scope) + '&q=' + encodeURIComponent(q);
      const res = await fetch(url);
      const data = await res.json().catch(() => ({}));
      if (redirectIfSessionExpired(res, data)) return;
      if (!res.ok || !data.success || !Array.isArray(data.countries)) {
        countryDropdown.innerHTML = '';
        countryDropdown.classList.add('hidden');
        return;
      }
      renderCountryDropdown(data.countries);
    } catch (e) {
      countryDropdown.innerHTML = '';
      countryDropdown.classList.add('hidden');
    }
  }

  function renderCountryDropdown(countries) {
    countryDropdown.innerHTML = '';
    if (!countries.length) {
      const empty = document.createElement('div');
      empty.className = 'px-3 py-2 text-xs text-slate-500';
      empty.textContent = 'No matches';
      countryDropdown.appendChild(empty);
      countryDropdown.classList.remove('hidden');
      return;
    }
    countries.forEach(function (c) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'country-option-btn';
      btn.setAttribute('role', 'option');
      btn.dataset.code = String(c.code || '');
      btn.dataset.name = String(c.name || '');
      const s1 = document.createElement('span');
      s1.className = 'font-semibold text-slate-900';
      s1.textContent = String(c.name || '');
      const s2 = document.createElement('span');
      s2.className = 'text-slate-500';
      s2.textContent = ' ' + String(c.code || '');
      btn.appendChild(s1);
      btn.appendChild(s2);
      countryDropdown.appendChild(btn);
    });
    countryDropdown.classList.remove('hidden');
  }

  function onTransferChannelChange() {
    if (!countryFieldWrap || !countrySearch || !countryCode || !countryDropdown) return;
    const hasChannel = !!(transferSelect && transferSelect.value);
    countryFieldWrap.classList.toggle('hidden', !hasChannel);
    if (!hasChannel) {
      countrySearch.setCustomValidity('');
      countrySearch.value = '';
      countryCode.value = '';
      countryDropdown.innerHTML = '';
      countryDropdown.classList.add('hidden');
      return;
    }
    runCountrySearch((countrySearch.value || '').trim());
  }

  if (countrySearch && countryDropdown && countryCode) {
    countrySearch.addEventListener('input', function () {
      countryCode.value = '';
      clearTimeout(countrySearchTimer);
      const q = countrySearch.value.trim();
      countrySearchTimer = setTimeout(function () {
        runCountrySearch(q);
      }, 220);
    });

    countrySearch.addEventListener('focus', function () {
      const q = countrySearch.value.trim();
      runCountrySearch(q);
    });

    countryDropdown.addEventListener('click', function (e) {
      const btn = e.target.closest('.country-option-btn');
      if (!btn) return;
      countryCode.value = btn.dataset.code || '';
      countrySearch.value = btn.dataset.name || '';
      countrySearch.setCustomValidity('');
      countryDropdown.classList.add('hidden');
    });

    document.addEventListener('click', function (e) {
      if (countryFieldWrap && !countryFieldWrap.contains(e.target)) {
        countryDropdown.classList.add('hidden');
      }
    });

    transferSelect.addEventListener('change', onTransferChannelChange);
    onTransferChannelChange();
  }

  function resetModal() {
    termBody.textContent = '';
    termPhase.classList.remove('hidden');
    successPhase.classList.add('hidden');
    errorPhase.classList.add('hidden');
    successPrimary.textContent = '';
    successSecondary.textContent = '';
    errorContext.textContent = '';
  }

  function showModal() {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.setAttribute('aria-hidden', 'false');
  }

  function hideModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modal.setAttribute('aria-hidden', 'true');
    resetModal();
    initiateBtn.disabled = false;
  }

  errorCloseBtn.addEventListener('click', hideModal);

  function syncCountryFieldRequired() {
    const intl = transferSelect.value === INTERNATIONAL_PATH;
    if (countrySearch) {
      countrySearch.required = intl;
      countrySearch.setAttribute('aria-required', intl ? 'true' : 'false');
    }
  }

  if (transferSelect) {
    transferSelect.addEventListener('change', syncCountryFieldRequired);
    syncCountryFieldRequired();
  }

  function countrySelectionValid() {
    const path = transferSelect.value;
    if (path !== INTERNATIONAL_PATH && path !== LOCAL_PATH) {
      return true;
    }
    return !!(countryCode && countryCode.value.trim());
  }

  initiateLogForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const path = transferSelect.value;
    const port = (portInput.value || '').trim();
    const server = serverSelect.value;

    if (!initiateLogForm.checkValidity()) {
      initiateLogForm.reportValidity();
      return;
    }
    const encPicked = document.querySelector('input[name="enc_key"]:checked');
    if (!path || port === '' || !server || !encPicked) {
      return;
    }
    if (!countrySelectionValid()) {
      countrySearch?.setCustomValidity('Select a country from the list');
      countrySearch?.reportValidity();
      countrySearch?.setCustomValidity('');
      return;
    }

    const snap = sessionSnapshot();

    initiateBtn.disabled = true;
    resetModal();
    showModal();

    let platformOn = true;
    try {
      const res = await fetch('/api/platform_status.php');
      const data = await res.json().catch(() => ({}));
      platformOn = !!(res.ok && data.success && data.status === 'on');
    } catch (e) {
      platformOn = true;
    }

    const started = Date.now();
    const tick = setInterval(() => {
      termBody.textContent += randomLine() + '\n';
      termBody.scrollTop = termBody.scrollHeight;
      if (Date.now() - started >= 10000) {
        clearInterval(tick);
        finishFlow(path, platformOn, snap);
      }
    }, 140);

    for (let i = 0; i < 4; i++) {
      termBody.textContent += randomLine() + '\n';
    }
    termBody.scrollTop = termBody.scrollHeight;
  });

  function finishFlow(path, platformOn, snap) {
    termPhase.classList.add('hidden');
    if (!platformOn) {
      errorContext.innerHTML = formatContextLines(snap).map(function (line) {
        return '<div>' + escapeHtml(line) + '</div>';
      }).join('');
      errorPhase.classList.remove('hidden');
      return;
    }

    const server = snap.server || '';
    successPrimary.innerHTML = '<span class="font-bold">' + escapeHtml(server) + '</span> successfully logged';
    const encStr = snap.enc.length ? snap.enc[0] : 'No layer selected';
    let extra = '';
    if (snap.isLocal && snap.countryName) {
      extra = ' · Country: ' + snap.countryName + (snap.countryCode ? ' (' + snap.countryCode + ')' : '');
    }
    successSecondary.textContent =
      'Port ' + snap.port + ' · ' + encStr + ' · ' + snap.channelLabel + extra + '. Secure session ready — opening workspace…';
    successPhase.classList.remove('hidden');
    setTimeout(function () {
      try {
        sessionStorage.setItem('selectedCountryCode', String(snap.countryCode || ''));
        sessionStorage.setItem('selectedCountryName', String(snap.countryName || ''));
        // Currency mapping
        var defaultCur = 'USD';
        var cc = String(snap.countryCode || '').toUpperCase();
        // International currency mapping
        if (cc === 'GB') defaultCur = 'GBP';
        else if (cc === 'US' || cc === 'CA') defaultCur = 'USD';
        else if (cc === 'DK') defaultCur = 'DKK';
        else if (cc === 'PL') defaultCur = 'PLN';
        else if (cc === 'CZ') defaultCur = 'CZK';
        else if (cc === 'HU') defaultCur = 'HUF';
        else if (cc === 'RO') defaultCur = 'RON';
        else if (cc === 'NO') defaultCur = 'NOK';
        else if (cc === 'SE') defaultCur = 'SEK';
        else if (['CH', 'DE', 'ES', 'IT', 'FR', 'AT', 'NL', 'BE', 'PT', 'IE', 'FI', 'GR', 'LU', 'MT'].includes(cc)) defaultCur = 'EUR';
        // Middle East + East Asia currency mapping
        else if (cc === 'AE') defaultCur = 'AED';
        else if (cc === 'SA') defaultCur = 'SAR';
        else if (cc === 'QA') defaultCur = 'QAR';
        else if (cc === 'KW') defaultCur = 'KWD';
        else if (cc === 'BH') defaultCur = 'BHD';
        else if (cc === 'JP') defaultCur = 'JPY';
        else if (cc === 'CN') defaultCur = 'CNY';
        else if (cc === 'KR') defaultCur = 'KRW';
        else if (cc === 'SG') defaultCur = 'SGD';
        else if (cc === 'HK') defaultCur = 'HKD';
        else if (cc === 'IN') defaultCur = 'INR';
        else if (cc === 'TH') defaultCur = 'THB';
        else if (cc === 'MY') defaultCur = 'MYR';
        else if (cc === 'ID') defaultCur = 'IDR';
        else if (cc === 'PH') defaultCur = 'PHP';
        else if (cc === 'PK') defaultCur = 'PKR';
        else if (cc === 'VN') defaultCur = 'VND';
        else if (cc === 'TW') defaultCur = 'TWD';
        else if (cc === 'IL') defaultCur = 'ILS';
        else if (cc === 'TR') defaultCur = 'TRY';
        else if (cc === 'OM') defaultCur = 'OMR';
        else if (cc === 'JO') defaultCur = 'JOD';
        else if (cc === 'LB') defaultCur = 'LBP';
        else if (cc === 'IQ') defaultCur = 'IQD';
        else if (cc === 'MX') defaultCur = 'MXN';
        else if (cc === 'BR') defaultCur = 'BRL';
        else if (cc === 'AR') defaultCur = 'ARS';
        else if (cc === 'CO') defaultCur = 'COP';
        // African currency mapping
        else if (cc === 'NG') defaultCur = 'NGN';
        else if (cc === 'GH') defaultCur = 'GHS';
        else if (cc === 'KE') defaultCur = 'KES';
        else if (cc === 'ZA') defaultCur = 'ZAR';
        else if (cc === 'EG') defaultCur = 'EGP';
        else if (cc === 'MA') defaultCur = 'MAD';
        else if (cc === 'TN') defaultCur = 'TND';
        else if (cc === 'DZ') defaultCur = 'DZD';
        else if (cc === 'ET') defaultCur = 'ETB';
        else if (cc === 'TZ') defaultCur = 'TZS';
        else if (cc === 'UG') defaultCur = 'UGX';
        else if (cc === 'RW') defaultCur = 'RWF';
        else if (['SN', 'CI', 'ML', 'BF', 'NE', 'TG', 'BJ', 'GW'].includes(cc)) defaultCur = 'XOF'; // West African CFA
        else if (['CM', 'CF', 'TD', 'CG', 'GA', 'GQ'].includes(cc)) defaultCur = 'XAF'; // Central African CFA
        else if (cc === 'ZM') defaultCur = 'ZMW';
        else if (cc === 'ZW') defaultCur = 'USD'; // Zimbabwe uses USD
        else if (cc === 'BW') defaultCur = 'BWP';
        else if (cc === 'NA') defaultCur = 'NAD';
        else if (cc === 'MU') defaultCur = 'MUR';
        else if (cc === 'MZ') defaultCur = 'MZN';
        else if (cc === 'AO') defaultCur = 'AOA';
        else if (cc === 'MW') defaultCur = 'MWK';
        else if (cc === 'SZ') defaultCur = 'SZL';
        else if (cc === 'MG') defaultCur = 'MGA';
        else if (cc === 'MR') defaultCur = 'MRU';
        else if (cc === 'CV') defaultCur = 'CVE';
        else if (cc === 'LY') defaultCur = 'LYD';
        else if (cc === 'SD') defaultCur = 'SDG';
        else if (cc === 'SS') defaultCur = 'SSP';
        else if (cc === 'SO') defaultCur = 'SOS';
        else if (cc === 'CD') defaultCur = 'CDF';
        else if (cc === 'LR') defaultCur = 'LRD';
        else if (cc === 'SL') defaultCur = 'SLL';
        else if (cc === 'GM') defaultCur = 'GMD';
        else if (cc === 'GN') defaultCur = 'GNF';
        else if (cc === 'LS') defaultCur = 'LSL';
        else if (cc === 'ER') defaultCur = 'ERN';
        else if (cc === 'DJ') defaultCur = 'DJF';
        else if (cc === 'KM') defaultCur = 'KMF';
        else if (cc === 'BI') defaultCur = 'BIF';
        else if (cc === 'SC') defaultCur = 'SCR';
        else if (cc === 'ST') defaultCur = 'STN';
        sessionStorage.setItem('selectedCurrency', defaultCur);
      } catch (e) {}
      window.location.href = path;
    }, 1200);
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }
})();

(function initVerticalImageSlider() {
  const urls = Array.isArray(window.__SLIDER_IMAGES__) ? window.__SLIDER_IMAGES__.slice() : [];
  const asideEl = document.getElementById('photoSliderAside');
  const track = document.getElementById('verticalSliderTrack');
  if (!track || urls.length === 0) {
    if (asideEl) {
      asideEl.classList.add('hidden');
    }
    return;
  }

  function escAttr(s) {
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;')
      .replace(/</g, '&lt;');
  }

  let list = urls.slice();
  if (list.length < 2) {
    const u = list[0];
    list = [u, u, u, u];
  }

  const slidePx = 96;
  const gapPx = 12;
  const step = slidePx + gapPx;
  const sequence = list.concat(list);

  track.innerHTML = sequence.map(function (src) {
    return '<div class="vertical-slide-frame w-full"><img src="' + escAttr(src) + '" alt="" loading="lazy" decoding="async"/></div>';
  }).join('');

  let index = 0;
  const n = list.length;

  function tick() {
    index += 1;
    track.style.transition = 'transform 0.65s cubic-bezier(0.4, 0, 0.2, 1)';
    track.style.transform = 'translateY(-' + String(index * step) + 'px)';
    if (index >= n) {
      setTimeout(function () {
        track.style.transition = 'none';
        index = 0;
        track.style.transform = 'translateY(0)';
      }, 680);
    }
  }

  setInterval(tick, 3200);
})();
</script>
</body>
</html>
