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
<title>Transfer Selection | Elysium Server</title>
<style>
@layer base {
  html, body { margin: 0; padding: 0; }
  body { overscroll-behavior: none; }
  main > :first-child { margin-top: 0 !important; }
  main > :last-child { margin-bottom: 0 !important; }
}
::-webkit-scrollbar { display: none; }
#termBody { scrollbar-width: thin; }
body.page-fit {
  height: 100dvh;
  max-height: 100dvh;
  overflow: hidden;
}
.technical-grid {
  background-image:
    linear-gradient(to right, rgba(226, 232, 240, 0.7) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(226, 232, 240, 0.7) 1px, transparent 1px);
  background-size: 20px 20px;
}
.app-titlebar, .app-statusbar {
  background: rgba(247, 249, 251, 0.92);
  backdrop-filter: blur(10px);
}
.app-panel {
  box-shadow: 0 1px 0 rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.06);
}
.app-control:focus {
  outline: none;
  border-color: #1e40af;
  box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.18);
}
.app-btn:active { transform: translateY(0.5px); }
</style>
<script src="https://cdn.tailwindcss.com"></script>
<script id="tailwind-config">
tailwind.config = {
  theme: {
    extend: {
      colors: {
        'tertiary-fixed-dim': '#4edea3',
        'tertiary-fixed': '#6ffbbe',
        'surface-container': '#eceef0',
        'surface-primary': '#FFFFFF',
        'surface-container-lowest': '#ffffff',
        'surface-tint': '#3755c3',
        'on-primary': '#ffffff',
        'surface-sidebar': '#0F172A',
        'primary-fixed': '#dde1ff',
        'surface-dim': '#d8dadc',
        'inverse-primary': '#b8c4ff',
        'on-secondary-fixed-variant': '#38485d',
        'on-primary-fixed': '#001453',
        'on-secondary-container': '#54647a',
        'surface-container-high': '#e6e8ea',
        'secondary-fixed': '#d3e4fe',
        'tertiary-container': '#00563a',
        'surface-variant': '#e0e3e5',
        'primary': '#00288e',
        'on-background': '#191c1e',
        'tertiary': '#003d27',
        'surface-container-low': '#f2f4f6',
        'on-tertiary-fixed-variant': '#005236',
        'surface-container-highest': '#e0e3e5',
        'error-container': '#ffdad6',
        'border-subtle': '#E2E8F0',
        'on-error-container': '#93000a',
        'inverse-surface': '#2d3133',
        'inverse-on-surface': '#eff1f3',
        'on-tertiary': '#ffffff',
        'secondary': '#505f76',
        'on-primary-container': '#a8b8ff',
        'on-secondary-fixed': '#0b1c30',
        'text-heading': '#111827',
        'background': '#f7f9fb',
        'primary-fixed-dim': '#b8c4ff',
        'on-surface': '#191c1e',
        'secondary-fixed-dim': '#b7c8e1',
        'surface': '#f7f9fb',
        'secondary-container': '#d0e1fb',
        'text-body': '#475569',
        'on-surface-variant': '#444653',
        'outline-variant': '#c4c5d5',
        'outline': '#757684',
        'error': '#ba1a1a',
        'surface-bright': '#f7f9fb',
        'on-tertiary-fixed': '#002113',
        'on-secondary': '#ffffff',
        'on-error': '#ffffff',
        'on-primary-fixed-variant': '#173bab',
        'primary-container': '#1e40af',
        'on-tertiary-container': '#3fd298',
      },
      borderRadius: {
        DEFAULT: '0.125rem',
        lg: '0.25rem',
        xl: '0.5rem',
        full: '0.75rem',
      },
      spacing: {
        'unit-1': '8px',
        'unit-4': '32px',
        'unit-3': '24px',
        margin: '32px',
        'unit-2': '16px',
        gutter: '24px',
        base: '8px',
        'sidebar-collapsed': '64px',
        'sidebar-width': '240px',
        'margin-desktop': '40px',
      },
      fontFamily: {
        'headline-sm': ['Manrope'],
        'body-sm': ['Manrope'],
        'headline-lg': ['Manrope'],
        'headline-md': ['Manrope'],
        'label-caps': ['Manrope'],
        'code-mono': ['JetBrains Mono'],
        'meta-mono': ['JetBrains Mono'],
        'meta-technical': ['JetBrains Mono'],
        'body-lg': ['Manrope'],
        'body-md': ['Manrope'],
      },
      fontSize: {
        'headline-sm': ['18px', { lineHeight: '26px', fontWeight: '600' }],
        'body-sm': ['13px', { lineHeight: '18px', fontWeight: '400' }],
        'headline-lg': ['30px', { lineHeight: '38px', letterSpacing: '-0.02em', fontWeight: '700' }],
        'headline-md': ['24px', { lineHeight: '32px', letterSpacing: '-0.01em', fontWeight: '600' }],
        'label-caps': ['12px', { lineHeight: '16px', letterSpacing: '0.05em', fontWeight: '600' }],
        'code-mono': ['13px', { lineHeight: '20px', fontWeight: '400' }],
        'meta-mono': ['12px', { lineHeight: '16px', letterSpacing: '0em', fontWeight: '400' }],
        'meta-technical': ['12px', { lineHeight: '16px', letterSpacing: '0.06em', fontWeight: '500' }],
        'body-lg': ['16px', { lineHeight: '24px', fontWeight: '400' }],
        'body-md': ['14px', { lineHeight: '20px', fontWeight: '400' }],
      },
    },
  },
};
</script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet"/>
</head>
<body class="page-fit bg-surface-container-low font-body-md text-on-surface technical-grid flex flex-col">
<header class="app-titlebar shrink-0 relative z-50 border-b border-border-subtle">
  <div class="h-10 w-full px-4 md:px-6 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
      <span class="font-meta-mono text-meta-mono text-on-surface-variant">Session Console</span>
    </div>
    <div class="flex items-center gap-3 shrink-0">
      <span class="inline-flex items-center gap-1.5 font-meta-mono text-meta-mono text-on-surface-variant">
        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
        AUTHENTICATED
      </span>
      <span class="w-px h-3 bg-border-subtle"></span>
      <span class="font-meta-mono text-meta-mono text-on-surface tracking-wide">KEY LOG 4.09</span>
    </div>
  </div>
</header>

<main class="relative z-10 flex-1 min-h-0 w-full overflow-y-auto px-4 md:px-6 py-4 flex flex-col">
  <div class="flex-1 min-h-0 flex flex-col items-center justify-center">
    <form id="initiateLogForm" class="app-panel relative z-10 w-full max-w-xl bg-surface-container-lowest border border-border-subtle rounded overflow-hidden flex flex-col">
      <div class="h-9 px-3 border-b border-border-subtle bg-surface-container-low flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
          <span class="material-symbols-outlined text-[16px] text-primary">tune</span>
          <span class="font-meta-technical text-meta-technical text-on-surface tracking-widest uppercase truncate">Session Configuration</span>
        </div>
        <span class="font-meta-mono text-meta-mono text-on-surface-variant shrink-0">NG Local</span>
      </div>

      <div class="px-4 py-4 border-b border-border-subtle bg-surface-bright flex items-center gap-3">
        <img alt="Elysium Logo" class="w-9 h-9 object-contain shrink-0" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"/>
        <div class="min-w-0">
          <div class="font-headline-sm text-headline-sm text-on-surface leading-tight">Elysium Server</div>
          <div class="font-meta-mono text-meta-mono text-on-surface-variant">Configure transfer endpoint parameters</div>
        </div>
      </div>

      <div class="px-4 py-4 flex flex-col gap-3 bg-surface-container-lowest">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div class="flex flex-col gap-1">
            <label class="font-meta-technical text-meta-technical text-on-surface-variant uppercase tracking-widest" for="portInput">Port</label>
            <input class="app-control w-full h-8 px-2.5 bg-white border border-border-subtle rounded-sm font-meta-mono text-meta-mono text-on-surface placeholder:text-on-surface-variant" id="portInput" name="port" placeholder="443" type="number" min="0" max="65535" inputmode="numeric" required/>
          </div>
          <div class="flex flex-col gap-1">
            <label class="font-meta-technical text-meta-technical text-on-surface-variant uppercase tracking-widest" for="serverSelect">Server</label>
            <div class="relative w-full h-8">
              <select class="app-control w-full h-full appearance-none bg-white border border-border-subtle rounded-sm px-2.5 pr-8 font-body-md text-body-md text-on-surface cursor-pointer" id="serverSelect" name="server" required>
                <option disabled selected value="">Select</option>
                <option value="ISO 20022">ISO 20022</option>
                <option value="BGD-234">BGD-234</option>
                <option value="JNV 2345">JNV 2345</option>
              </select>
              <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant pointer-events-none">expand_more</span>
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-1">
          <label class="font-meta-technical text-meta-technical text-on-surface-variant uppercase tracking-widest">Encryption Key</label>
          <div class="flex items-center h-8 border border-border-subtle rounded-sm bg-surface-container-low overflow-hidden" role="radiogroup" aria-label="Encryption Key">
            <label class="flex-1 h-full flex items-center justify-center gap-2 cursor-pointer hover:bg-white/70 transition-colors border-r border-border-subtle">
              <input class="w-3.5 h-3.5 accent-primary" id="encSsl" name="enc_key" type="radio" value="SSL" required/>
              <span class="font-meta-mono text-meta-mono text-on-surface">SSL</span>
            </label>
            <label class="flex-1 h-full flex items-center justify-center gap-2 cursor-pointer hover:bg-white/70 transition-colors">
              <input class="w-3.5 h-3.5 accent-primary" id="encTsl" name="enc_key" type="radio" value="TSL"/>
              <span class="font-meta-mono text-meta-mono text-on-surface">TSL</span>
            </label>
          </div>
        </div>

        <div class="flex flex-col gap-1">
          <label class="font-meta-technical text-meta-technical text-on-surface-variant uppercase tracking-widest" for="serverIpSelect">Server IP</label>
          <div class="relative w-full h-8">
            <select class="app-control w-full h-full appearance-none bg-white border border-border-subtle rounded-sm px-2.5 pr-8 font-meta-mono text-meta-mono text-on-surface cursor-pointer" id="serverIpSelect" name="server_ip" required>
              <option disabled selected value="">Select</option>
            </select>
            <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant pointer-events-none">expand_more</span>
          </div>
        </div>

        <div id="currencyFieldWrap" class="hidden flex flex-col gap-1">
          <label class="font-meta-technical text-meta-technical text-on-surface-variant uppercase tracking-widest" for="currencySelect">Currency</label>
          <div class="relative w-full h-8">
            <select class="app-control w-full h-full appearance-none bg-white border border-border-subtle rounded-sm px-2.5 pr-8 font-body-md text-body-md text-on-surface cursor-pointer" id="currencySelect" name="currency">
              <option value="">Select</option>
              <option value="NGN">Naira</option>
              <option value="USD">Dollars</option>
            </select>
            <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant pointer-events-none">expand_more</span>
          </div>
        </div>
      </div>

      <div class="h-11 px-3 border-t border-border-subtle bg-surface-container-low flex items-center justify-end">
        <button type="submit" id="initiateBtn" class="app-btn h-8 px-4 bg-primary text-on-primary font-meta-technical text-meta-technical tracking-widest uppercase rounded-sm inline-flex items-center gap-1.5 hover:bg-primary/90 transition-colors border border-primary disabled:opacity-50 disabled:cursor-not-allowed">
          <span class="material-symbols-outlined text-[16px]">terminal</span>
          Initiate Log
        </button>
      </div>
    </form>
  </div>
</main>

<footer class="app-statusbar relative z-10 shrink-0 w-full h-8 border-t border-border-subtle">
  <div class="h-full px-4 md:px-6 flex justify-between items-center gap-4">
    <div class="flex items-center gap-4 min-w-0">
      <span class="font-meta-mono text-meta-mono text-on-surface-variant uppercase truncate">ENV Production</span>
      <span class="hidden sm:inline font-meta-mono text-meta-mono text-on-surface-variant uppercase">Build 2024.03.27</span>
    </div>
    <div class="font-meta-mono text-meta-mono text-primary uppercase shrink-0">Awaiting Configuration</div>
  </div>
</footer>

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
    if (!hasIp) currencySelect.value = '';
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
    return { port, server, enc, serverIp, currency, currencyLabel };
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
    if (port === '' || !server || !encPicked || !serverIp || !currency) return;

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

    for (let i = 0; i < 4; i++) termBody.textContent += randomLine() + '\n';
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

      fetch('/api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ action: 'check' })
      }).catch(function () {}).finally(function () {
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
</script>
</body>
</html>
