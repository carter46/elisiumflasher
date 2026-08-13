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
        'body-sm': ['Inter'],
        'headline-lg': ['Manrope'],
        'headline-md': ['Manrope'],
        'label-caps': ['Inter'],
        'code-mono': ['JetBrains Mono'],
        'meta-mono': ['JetBrains Mono'],
        'meta-technical': ['Inter'],
        'body-lg': ['Inter'],
        'body-md': ['Inter'],
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
<body class="bg-surface-dim p-unit-2 min-h-screen">
<div class="bg-surface rounded-xl shadow-2xl flex overflow-hidden min-h-[calc(100vh-32px)] border border-outline-variant/30">
  <div class="flex-1 flex flex-col min-w-0 bg-background">
    <header class="h-12 flex items-center justify-between px-unit-3 border-b border-border-subtle bg-surface-primary/50 backdrop-blur-sm">
      <div class="flex items-center gap-unit-2">
        <span class="font-label-caps text-label-caps text-secondary">NODE_01 // SECURE</span>
      </div>
      <div class="flex items-center gap-unit-2">
        <button type="button" class="w-8 h-8 flex items-center justify-center hover:bg-surface-container rounded-lg transition-colors" aria-label="Minimize">
          <span class="material-symbols-outlined text-[16px] text-on-surface-variant">minimize</span>
        </button>
        <button type="button" class="w-8 h-8 flex items-center justify-center hover:bg-surface-container rounded-lg transition-colors" aria-label="Maximize">
          <span class="material-symbols-outlined text-[16px] text-on-surface-variant">check_box_outline_blank</span>
        </button>
        <button type="button" class="w-8 h-8 flex items-center justify-center hover:bg-error/10 group transition-colors rounded-lg" aria-label="Close">
          <span class="material-symbols-outlined text-[16px] text-on-surface-variant group-hover:text-error">close</span>
        </button>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto p-margin">
      <div class="max-w-6xl mx-auto">
        <div class="flex flex-col w-full h-full relative">
          <div class="absolute inset-0 pointer-events-none opacity-20" style="background-size: 24px 24px; background-image: linear-gradient(to right, #E2E8F0 1px, transparent 1px), linear-gradient(to bottom, #E2E8F0 1px, transparent 1px);"></div>

          <div class="flex-1 flex flex-col items-center justify-center relative z-10 px-4 md:px-margin-desktop py-8 min-h-0">
            <form id="initiateLogForm" class="w-full max-w-2xl bg-surface-container-lowest border border-border-subtle rounded-lg shadow-sm flex flex-col overflow-hidden">
              <div class="px-gutter py-gutter border-b border-border-subtle flex flex-col items-center text-center bg-surface-bright">
                <img alt="Elysium Logo" class="w-16 h-16 object-contain mb-4" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"/>
                <h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">Elysium Server</h1>
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full bg-primary-container inline-block"></span>
                  <span class="font-meta-mono text-meta-mono text-on-secondary-container tracking-wider">KEY LOG VERSION : 4.09</span>
                </div>
              </div>

              <div class="p-gutter flex flex-col gap-6 bg-surface-container-lowest">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                  <div class="flex flex-col gap-2">
                    <label class="font-meta-technical text-meta-technical text-on-surface uppercase tracking-widest" for="portInput">PORT</label>
                    <input class="w-full h-10 px-3 bg-surface-container-lowest border border-border-subtle rounded font-meta-mono text-meta-mono text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-all" id="portInput" name="port" placeholder="e.g. 443" type="number" min="0" max="65535" inputmode="numeric" required/>
                  </div>
                  <div class="flex flex-col gap-2 relative">
                    <label class="font-meta-technical text-meta-technical text-on-surface uppercase tracking-widest" for="serverSelect">SERVER</label>
                    <div class="relative w-full h-10">
                      <select class="w-full h-full appearance-none bg-surface-container-lowest border border-border-subtle rounded px-3 pr-10 font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-all cursor-pointer" id="serverSelect" name="server" required>
                        <option disabled selected value="">— Select —</option>
                        <option value="ISO 20022">ISO 20022</option>
                        <option value="BGD-234">BGD-234</option>
                        <option value="JNV 2345">JNV 2345</option>
                      </select>
                      <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                    </div>
                  </div>
                </div>

                <div class="flex flex-col gap-2">
                  <label class="font-meta-technical text-meta-technical text-on-surface uppercase tracking-widest">ENCRYPTION KEY</label>
                  <div class="flex items-center gap-4 border border-border-subtle rounded h-10 px-3 bg-surface-bright" role="radiogroup" aria-label="Encryption Key">
                    <label class="flex items-center gap-2 cursor-pointer group">
                      <input class="w-4 h-4 text-primary-container bg-surface-container border-border-subtle focus:ring-primary-container focus:ring-1 transition-all" id="encSsl" name="enc_key" type="radio" value="SSL" required/>
                      <span class="font-meta-mono text-meta-mono text-on-surface group-hover:text-primary-container transition-colors">SSL</span>
                    </label>
                    <div class="w-px h-4 bg-border-subtle"></div>
                    <label class="flex items-center gap-2 cursor-pointer group">
                      <input class="w-4 h-4 text-primary-container bg-surface-container border-border-subtle focus:ring-primary-container focus:ring-1 transition-all" id="encTsl" name="enc_key" type="radio" value="TSL"/>
                      <span class="font-meta-mono text-meta-mono text-on-surface group-hover:text-primary-container transition-colors">TSL</span>
                    </label>
                  </div>
                </div>

                <div class="flex flex-col gap-2">
                  <label class="font-meta-technical text-meta-technical text-on-surface uppercase tracking-widest" for="serverIpSelect">SERVER IP</label>
                  <div class="relative w-full h-10">
                    <select class="w-full h-full appearance-none bg-surface-container-lowest border border-border-subtle rounded px-3 pr-10 font-meta-mono text-meta-mono text-on-surface focus:outline-none focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-all cursor-pointer" id="serverIpSelect" name="server_ip" required>
                      <option disabled selected value="">— Select —</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                  </div>
                </div>

                <div id="currencyFieldWrap" class="hidden flex flex-col gap-2">
                  <label class="font-meta-technical text-meta-technical text-on-surface uppercase tracking-widest" for="currencySelect">CURRENCY</label>
                  <div class="relative w-full h-10">
                    <select class="w-full h-full appearance-none bg-surface-container-lowest border border-border-subtle rounded px-3 pr-10 font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-all cursor-pointer" id="currencySelect" name="currency">
                      <option value="">— Select —</option>
                      <option value="NGN">Naira</option>
                      <option value="USD">Dollars</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                  </div>
                </div>
              </div>

              <div class="px-gutter py-gutter border-t border-border-subtle bg-surface-bright flex justify-end">
                <button type="submit" id="initiateBtn" class="bg-primary-container text-on-primary-container font-headline-sm text-headline-sm px-6 py-3 rounded flex items-center gap-2 hover:bg-on-primary-fixed-variant transition-colors w-full sm:w-auto justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                  <span class="material-symbols-outlined text-[20px]">terminal</span>
                  Initiate Log
                </button>
              </div>
            </form>

            <div class="mt-8 text-center w-full max-w-2xl">
              <span class="font-meta-mono text-meta-mono text-on-secondary-container tracking-widest opacity-60">BUILD: 2024.03.27 | ELYSIUM</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
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
