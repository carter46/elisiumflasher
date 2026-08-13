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
      <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="serverIpSelect">Server IP</label>
      <div class="login-input-wrap relative flex items-center px-3">
        <select id="serverIpSelect" name="server_ip" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded text-sm cursor-pointer" required>
          <option value="">— Select —</option>
        </select>
      </div>
    </div>

    <div id="currencyFieldWrap" class="hidden flex flex-col gap-1.5">
      <label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="currencySelect">Currency</label>
      <div class="login-input-wrap relative flex items-center px-3">
        <select id="currencySelect" name="currency" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded text-sm cursor-pointer">
          <option value="">— Select —</option>
          <option value="NGN">Naira</option>
          <option value="USD">Dollars</option>
        </select>
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
  const LOCAL_PATH = '/local_dashboard.php';
  const portInput = document.getElementById('portInput');
  const serverSelect = document.getElementById('serverSelect');
  const serverIpSelect = document.getElementById('serverIpSelect');
  const currencyFieldWrap = document.getElementById('currencyFieldWrap');
  const currencySelect = document.getElementById('currencySelect');
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

  function randomOctet() {
    return Math.floor(Math.random() * 254) + 1;
  }

  function randomIpv4() {
    return randomOctet() + '.' + randomOctet() + '.' + randomOctet() + '.' + randomOctet();
  }

  function populateServerIps() {
    const seen = {};
    const ips = [];
    while (ips.length < 5) {
      const ip = randomIpv4();
      if (seen[ip]) continue;
      seen[ip] = true;
      ips.push(ip);
    }
    ips.forEach(function (ip) {
      const opt = document.createElement('option');
      opt.value = ip;
      opt.textContent = ip;
      serverIpSelect.appendChild(opt);
    });
  }

  function syncCurrencyVisibility() {
    const hasIp = !!(serverIpSelect && serverIpSelect.value);
    if (!currencyFieldWrap || !currencySelect) return;
    currencyFieldWrap.classList.toggle('hidden', !hasIp);
    currencySelect.required = hasIp;
    if (!hasIp) {
      currencySelect.value = '';
    }
  }

  populateServerIps();
  serverIpSelect.addEventListener('change', syncCurrencyVisibility);
  syncCurrencyVisibility();

  function sessionSnapshot() {
    const port = (portInput.value || '').trim();
    const server = serverSelect.value || '';
    const encRadio = document.querySelector('input[name="enc_key"]:checked');
    const enc = encRadio && encRadio.value ? [encRadio.value] : [];
    const serverIp = serverIpSelect.value || '';
    const currency = currencySelect.value || '';
    const currencyLabel = currency === 'NGN' ? 'Naira' : (currency === 'USD' ? 'Dollars' : '');
    return {
      port,
      server,
      enc,
      serverIp,
      currency,
      currencyLabel,
    };
  }

  function formatContextLines(snap) {
    const encStr = snap.enc.length ? snap.enc[0] : 'None selected';
    return [
      'Port: ' + (snap.port || '—'),
      'Server: ' + (snap.server || '—'),
      'Encryption Key: ' + encStr,
      'Server IP: ' + (snap.serverIp || '—'),
      'Currency: ' + (snap.currencyLabel || snap.currency || '—'),
    ];
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

  initiateLogForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const port = (portInput.value || '').trim();
    const server = serverSelect.value;
    const serverIp = serverIpSelect.value;
    const currency = currencySelect.value;

    if (!initiateLogForm.checkValidity()) {
      initiateLogForm.reportValidity();
      return;
    }
    const encPicked = document.querySelector('input[name="enc_key"]:checked');
    if (port === '' || !server || !encPicked || !serverIp || !currency) {
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
    } catch (err) {
      platformOn = true;
    }

    const started = Date.now();
    const tick = setInterval(() => {
      termBody.textContent += randomLine() + '\n';
      termBody.scrollTop = termBody.scrollHeight;
      if (Date.now() - started >= 10000) {
        clearInterval(tick);
        finishFlow(platformOn, snap);
      }
    }, 140);

    for (let i = 0; i < 4; i++) {
      termBody.textContent += randomLine() + '\n';
    }
    termBody.scrollTop = termBody.scrollHeight;
  });

  function finishFlow(platformOn, snap) {
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
    successSecondary.textContent =
      'Port ' + snap.port + ' · ' + encStr + ' · IP ' + snap.serverIp + ' · ' + (snap.currencyLabel || snap.currency) + '. Secure session ready — opening workspace…';
    successPhase.classList.remove('hidden');
    setTimeout(function () {
      try {
        sessionStorage.setItem('selectedCountryCode', 'NG');
        sessionStorage.setItem('selectedCountryName', 'Nigeria');
        sessionStorage.setItem('selectedCurrency', String(snap.currency || 'NGN'));
        sessionStorage.setItem('selectedServerIp', String(snap.serverIp || ''));
      } catch (err) {}

      // Refresh PHP session activity before navigating so require_login() on the dashboard does not bounce to /.
      fetch('/api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ action: 'check' })
      }).catch(function () { /* continue anyway */ }).finally(function () {
        window.location.href = LOCAL_PATH;
      });
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
