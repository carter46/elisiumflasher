<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: /transfer_selection.php');
    exit;
}

$bgMap = '/assets/' . rawurlencode('world map_7325629.png');
$logoUrl = 'https://lh3.googleusercontent.com/aida/AP1WRLvhokjFDu6qYj6dVduoYJnLfG5t89iSCEgECKyN-t8IDzK0Fdw42m7A_q66Iy6j2A2qvFJ4cLAngjtlkZQTOPInJ84ykd5znTULXFKtt11AcPpyOY57--4EXCxRrdEMJaYQid8yaOFG2rnzdmq3MffpLatLCNfu3sBs2RpnkAdIdyeTBnlmm_zNAZLH3IqBvJR0DrBLiRBL7nVe_dtWUeWTdetVyoM31s8NhND9TW_p_-u-b1qTU_K_A8Y';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Elysium Server | Initialize</title>
<style>
@layer base {
  html, body { margin: 0; padding: 0; }
  body { overscroll-behavior: none; }
  main > :first-child { margin-top: 0 !important; }
  main > :last-child { margin-bottom: 0 !important; }
}
::-webkit-scrollbar { display: none; }
.technical-grid {
  background-image:
    linear-gradient(to right, #F1F5F9 1px, transparent 1px),
    linear-gradient(to bottom, #F1F5F9 1px, transparent 1px);
  background-size: 24px 24px;
}
.page-map-bg {
  position: fixed;
  inset: 0;
  z-index: 0;
  background-image: url('<?= htmlspecialchars($bgMap, ENT_QUOTES, 'UTF-8') ?>');
  background-size: 420px auto;
  background-position: center top;
  background-repeat: repeat;
  opacity: 0.15;
  pointer-events: none;
}
.page-map-veil {
  display: none;
}
body.home-fit {
  height: 100dvh;
  max-height: 100dvh;
  overflow: hidden;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}
@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
@keyframes success-pop {
  0% { transform: scale(0.6); opacity: 0; }
  60% { transform: scale(1.08); opacity: 1; }
  100% { transform: scale(1); opacity: 1; }
}
@keyframes confetti-fall {
  0% { transform: translate3d(0, -12px, 0) rotate(0deg); opacity: 1; }
  100% { transform: translate3d(var(--dx), 120px, 0) rotate(var(--rot)); opacity: 0; }
}
.success-pop {
  animation: success-pop 0.55s cubic-bezier(0.2, 0.9, 0.2, 1) both;
}
.confetti-piece {
  position: absolute;
  top: 28%;
  left: 50%;
  width: 8px;
  height: 10px;
  border-radius: 1px;
  animation: confetti-fall 1.35s ease-out forwards;
}
</style>
<script src="https://cdn.tailwindcss.com"></script>
<script id="tailwind-config">
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        'on-tertiary': '#ffffff',
        'on-secondary-container': '#5c647a',
        'secondary-fixed': '#dae2fd',
        'primary-fixed-dim': '#b8c4ff',
        'error-container': '#ffdad6',
        'on-background': '#191c1e',
        'on-secondary': '#ffffff',
        'tertiary': '#611e00',
        'tertiary-fixed': '#ffdbce',
        'secondary-container': '#dae2fd',
        'on-primary-fixed-variant': '#173bab',
        'primary': '#00288e',
        'on-error': '#ffffff',
        'surface-dim': '#d8dadc',
        'inverse-on-surface': '#eff1f3',
        'tertiary-fixed-dim': '#ffb59a',
        'error': '#ba1a1a',
        'on-primary-fixed': '#001453',
        'surface-tint': '#3755c3',
        'inverse-surface': '#2d3133',
        'on-surface': '#191c1e',
        'on-tertiary-fixed-variant': '#802a00',
        'on-primary-container': '#a8b8ff',
        'technical-grid': '#F1F5F9',
        'surface-container-lowest': '#ffffff',
        'secondary': '#565e74',
        'surface-container-highest': '#e0e3e5',
        'on-tertiary-fixed': '#380d00',
        'on-secondary-fixed': '#131b2e',
        'surface-variant': '#e0e3e5',
        'border-subtle': '#E2E8F0',
        'primary-fixed': '#dde1ff',
        'surface-container-high': '#e6e8ea',
        'on-primary': '#ffffff',
        'on-secondary-fixed-variant': '#3f465c',
        'surface-container-low': '#f2f4f6',
        'surface': '#f7f9fb',
        'outline': '#757684',
        'tertiary-container': '#872d00',
        'surface-bright': '#f7f9fb',
        'outline-variant': '#c4c5d5',
        'primary-container': '#1e40af',
        'surface-container': '#eceef0',
        'background': '#f7f9fb',
        'on-error-container': '#93000a',
        'on-surface-variant': '#444653',
        'secondary-fixed-dim': '#bec6e0',
        'inverse-primary': '#b8c4ff',
        'on-tertiary-container': '#ffa583',
      },
      borderRadius: {
        DEFAULT: '0.125rem',
        lg: '0.25rem',
        xl: '0.5rem',
        full: '0.75rem',
      },
      spacing: {
        'grid-unit': '4px',
        gutter: '24px',
        'container-max': '1440px',
        'margin-mobile': '16px',
        'margin-desktop': '40px',
      },
      fontFamily: {
        'headline-lg': ['Manrope'],
        'headline-md': ['Manrope'],
        'meta-mono': ['JetBrains Mono'],
        'body-md': ['Manrope'],
        'meta-technical': ['JetBrains Mono'],
        'headline-sm': ['Manrope'],
        'body-lg': ['Manrope'],
        'body-sm': ['Manrope'],
      },
      fontSize: {
        'headline-lg': ['30px', { lineHeight: '40px', letterSpacing: '-0.02em', fontWeight: '700' }],
        'headline-md': ['24px', { lineHeight: '32px', letterSpacing: '-0.01em', fontWeight: '600' }],
        'meta-mono': ['12px', { lineHeight: '16px', letterSpacing: '0em', fontWeight: '400' }],
        'body-md': ['14px', { lineHeight: '20px', letterSpacing: '0em', fontWeight: '400' }],
        'meta-technical': ['12px', { lineHeight: '16px', letterSpacing: '0.06em', fontWeight: '500' }],
        'headline-sm': ['18px', { lineHeight: '24px', letterSpacing: '0em', fontWeight: '600' }],
        'body-lg': ['16px', { lineHeight: '24px', letterSpacing: '0em', fontWeight: '400' }],
        'body-sm': ['13px', { lineHeight: '18px', letterSpacing: '0em', fontWeight: '400' }],
      },
    },
  },
};
</script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@100..900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet"/>
</head>
<body class="home-fit bg-surface font-body-md text-on-surface technical-grid min-h-0 flex flex-col relative">
<div class="page-map-bg" aria-hidden="true"></div>

<header class="shrink-0 relative z-50 bg-surface/40 backdrop-blur-sm">
  <div class="h-12 w-full px-margin-desktop flex items-center justify-between">
    <div class="flex items-center gap-3">
      <span id="headerServerIp" class="font-meta-mono text-meta-mono text-on-surface tracking-wide">—.—.—.—</span>
    </div>
    <div class="w-7 h-7 rounded-full bg-primary flex items-center justify-center">
      <span class="material-symbols-outlined text-on-primary text-[16px]">person</span>
    </div>
  </div>
</header>

<main class="relative z-10 flex-1 min-h-0 w-full max-w-container-max mx-auto px-margin-desktop flex flex-col">
  <div class="flex flex-col flex-1 min-h-0 w-full justify-center items-center py-4 relative">

    <!-- Central Alignment Container -->
    <div class="flex flex-col items-center w-full max-w-xl relative z-10 px-6 sm:px-8 bg-surface-container-lowest border border-border-subtle rounded-xl py-8 sm:py-10 shadow-sm">

      <!-- Subtle Structural Lines (Background Detail) -->
      <div aria-hidden="true" class="absolute inset-0 w-full h-full pointer-events-none flex justify-center -z-10">
        <div class="w-[1px] h-full bg-border-subtle opacity-50 relative">
          <div class="absolute top-[20%] -left-3 w-6 h-[1px] bg-border-subtle opacity-50"></div>
          <div class="absolute top-[80%] -left-3 w-6 h-[1px] bg-border-subtle opacity-50"></div>
        </div>
        <div class="absolute top-1/2 left-0 w-full h-[1px] bg-border-subtle opacity-30 transform -translate-y-1/2"></div>
      </div>

      <!-- Header / Logo -->
      <div id="brandBlock" class="mb-6 relative flex flex-col items-center">
        <div class="absolute -top-3 -left-3 w-3 h-3 border-t border-l border-primary/30"></div>
        <div class="absolute -top-3 -right-3 w-3 h-3 border-t border-r border-primary/30"></div>
        <div class="absolute -bottom-3 -left-3 w-3 h-3 border-b border-l border-primary/30"></div>
        <div class="absolute -bottom-3 -right-3 w-3 h-3 border-b border-r border-primary/30"></div>
        <img alt="Elysium App Identity" class="w-14 h-14 object-contain mb-4 mix-blend-multiply" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"/>
        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight uppercase mb-1">Elysium Server</h1>
        <div class="flex items-center gap-3">
          <span class="font-body-md text-body-md text-primary tracking-wide">Secure Transfer Gateway</span>
          <span class="font-meta-mono text-meta-mono text-primary bg-primary-fixed-dim/20 px-2 py-0.5 rounded-sm">v7.2.1</span>
        </div>
      </div>

      <!-- Initial state -->
      <div id="initialState" class="w-full flex flex-col items-center">
        <p class="font-body-lg text-body-lg text-on-surface-variant text-center max-w-md mb-8 leading-relaxed">
          Initialize a secure connection to the Elysium transfer gateway.
        </p>
        <div class="flex flex-col items-center w-full gap-5">
          <button id="startBtn" type="button" class="bg-primary text-on-primary font-headline-sm text-headline-sm px-12 py-3.5 rounded-sm hover:bg-primary/90 transition-colors duration-300 w-full sm:w-auto relative group overflow-hidden border border-primary disabled:opacity-60">
            <span class="relative z-10 flex items-center justify-center gap-2">
              START SERVER
              <span class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span>
            </span>
            <div class="absolute inset-0 border border-white/20 rounded-sm opacity-0 group-hover:opacity-100 transition-opacity"></div>
          </button>
          <div class="flex flex-col items-center gap-1.5">
            <div class="flex items-center gap-2">
              <span class="text-[10px] text-primary" style="font-variation-settings: 'FILL' 1;">●</span>
              <span class="font-meta-technical text-meta-technical text-on-surface tracking-widest uppercase">System Ready</span>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant/70 text-center">
              All sessions are encrypted and authenticated.
            </p>
          </div>
        </div>
      </div>

      <!-- Loading (runtime, not in design sample) -->
      <div id="loadingSection" class="hidden w-full">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 border-2 border-primary/20 border-t-primary rounded-full" style="animation: spin 1s linear infinite;"></div>
          <div id="loadingText" class="font-headline-sm text-headline-sm text-on-surface">Initializing Server...</div>
        </div>
        <div class="w-full h-2 bg-surface-container-high rounded-sm overflow-hidden mb-4">
          <div id="progressBar" class="h-full w-0 bg-primary transition-all duration-100" style="background-size: 200% 100%; animation: shimmer 2s linear infinite;"></div>
        </div>
        <div class="flex items-center justify-between gap-4 mb-6">
          <div class="font-headline-md text-headline-md text-primary tabular-nums"><span id="progressPercent">0</span>%</div>
          <div id="progressStatus" class="font-body-sm text-body-sm text-on-surface-variant text-right max-w-[220px]">Preparing secure environment...</div>
        </div>
        <div class="w-full border border-border-subtle bg-surface-container-low rounded-lg p-4 max-h-48 overflow-y-auto">
          <div class="font-meta-technical text-meta-technical text-outline tracking-widest uppercase mb-3">System Log</div>
          <div id="logEntries" class="font-meta-mono text-meta-mono space-y-1 text-on-surface-variant"></div>
        </div>
      </div>

      <!-- Login (runtime, not in design sample) -->
      <div id="loginSection" class="hidden w-full max-w-md mx-auto">
        <div class="text-center mb-8">
          <div class="font-headline-sm text-headline-sm text-on-surface mb-1">Server Online</div>
          <div class="font-body-sm text-body-sm text-on-surface-variant">Enter your license key to continue</div>
        </div>
        <form id="loginForm" class="flex flex-col gap-5">
          <div class="flex flex-col gap-1.5">
            <label class="font-meta-technical text-meta-technical text-on-surface-variant tracking-widest uppercase" for="licenseKey">License Key</label>
            <input id="licenseKey" class="w-full border border-border-subtle rounded-sm bg-white px-3 py-3 font-body-md text-body-md text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" type="text" placeholder="Enter license key" required autocomplete="off"/>
          </div>
          <div id="loginMessage" class="hidden text-sm px-3 py-2 rounded-sm"></div>
          <button type="submit" id="loginBtn" class="w-full bg-primary text-on-primary font-headline-sm text-headline-sm px-8 py-4 rounded-sm hover:bg-primary/90 transition-colors border border-primary disabled:opacity-60">
            Authenticate
          </button>
        </form>
        <div class="mt-6 text-center font-meta-mono text-meta-mono text-outline-variant tracking-widest uppercase">
          BUILD: 2024.03.27 | ELYSIUM
        </div>
      </div>
    </div>

    <!-- Footer Metadata -->
    <div class="mt-4 w-full flex justify-center">
      <div class="font-meta-mono text-meta-mono text-outline-variant tracking-widest uppercase flex items-center gap-4">
        <span>BUILD: 2024.03.27</span>
        <span class="w-[1px] h-3 bg-outline-variant"></span>
        <span>ELYSIUM</span>
      </div>
    </div>
  </div>
</main>

<footer class="relative z-10 shrink-0 w-full py-3 border-t border-border-subtle bg-surface/80 backdrop-blur-md">
  <div class="max-w-container-max mx-auto px-margin-desktop flex justify-between items-center gap-4 flex-wrap">
    <div class="flex gap-gutter flex-wrap">
      <span class="font-meta-technical text-meta-technical text-on-surface-variant uppercase">Environment: Production</span>
      <span class="font-meta-technical text-meta-technical text-on-surface-variant uppercase">Build: v4.2.0-stable</span>
    </div>
    <div class="font-meta-mono text-meta-mono text-outline">SECURE NODE CONNECTED</div>
  </div>
</footer>

<script>
(function () {
  var headerServerIp = document.getElementById('headerServerIp');
  function randomOctet() {
    return Math.floor(Math.random() * 254) + 1;
  }
  if (headerServerIp) {
    headerServerIp.textContent =
      randomOctet() + '.' + randomOctet() + '.' + randomOctet() + '.' + randomOctet();
  }

  var startBtn = document.getElementById('startBtn');
  var initialState = document.getElementById('initialState');
  var loadingSection = document.getElementById('loadingSection');
  var loginSection = document.getElementById('loginSection');
  var progressBar = document.getElementById('progressBar');
  var progressPercent = document.getElementById('progressPercent');
  var progressStatus = document.getElementById('progressStatus');
  var loadingText = document.getElementById('loadingText');
  var logEntries = document.getElementById('logEntries');

  var loginForm = document.getElementById('loginForm');
  var licenseInput = document.getElementById('licenseKey');
  var loginBtn = document.getElementById('loginBtn');
  var loginMessage = document.getElementById('loginMessage');

  var TOTAL_DURATION = 25000;
  var startTime = null;
  var animationFrame = null;
  var lastLogIndex = -1;

  var statusMessages = [
    { pct: 0, status: 'Preparing secure environment...', log: 'System boot initiated', type: 'info' },
    { pct: 5, status: 'Loading cryptographic modules...', log: 'Loading OpenSSL libraries...', type: 'info' },
    { pct: 10, status: 'Initializing HSM connection...', log: 'HSM handshake successful', type: 'success' },
    { pct: 15, status: 'Verifying security certificates...', log: 'SSL/TLS certificates validated', type: 'success' },
    { pct: 20, status: 'Connecting to transfer gateway...', log: 'Establishing secure tunnel...', type: 'info' },
    { pct: 25, status: 'Authenticating gateway session...', log: 'Gateway link authenticated', type: 'success' },
    { pct: 30, status: 'Loading transfer schemas...', log: 'Local transfer schemas loaded', type: 'info' },
    { pct: 35, status: 'Initializing transfer interface...', log: 'Transfer service ready', type: 'success' },
    { pct: 40, status: 'Configuring message queues...', log: 'Input/Output queues configured', type: 'info' },
    { pct: 45, status: 'Loading bank corridor data...', log: 'Bank directory synchronized', type: 'success' },
    { pct: 50, status: 'Establishing session tracker...', log: 'Session tracking active', type: 'success' },
    { pct: 55, status: 'Verifying routing tables...', log: 'Local routing verified', type: 'info' },
    { pct: 60, status: 'Initializing compliance engine...', log: 'AML/CFT filters loaded', type: 'warning' },
    { pct: 65, status: 'Loading sanction lists...', log: 'Compliance lists updated', type: 'warning' },
    { pct: 70, status: 'Configuring audit logging...', log: 'Audit trail system active', type: 'info' },
    { pct: 75, status: 'Testing network latency...', log: 'Ping: 12ms to operations center', type: 'success' },
    { pct: 80, status: 'Syncing time servers...', log: 'NTP sync: UTC+0 confirmed', type: 'success' },
    { pct: 85, status: 'Loading user sessions...', log: 'Session manager initialized', type: 'info' },
    { pct: 90, status: 'Final security checks...', log: 'Firewall rules verified', type: 'warning' },
    { pct: 95, status: 'Starting application services...', log: 'All services started successfully', type: 'success' },
    { pct: 100, status: 'Server ready!', log: 'Elysium Gateway online', type: 'success' }
  ];

  function addLogEntry(text, type) {
    var entry = document.createElement('div');
    var color = type === 'success' ? 'text-primary' : (type === 'warning' ? 'text-tertiary' : 'text-on-surface-variant');
    entry.className = color;
    var now = new Date();
    var time = String(now.getHours()).padStart(2, '0') + ':' +
               String(now.getMinutes()).padStart(2, '0') + ':' +
               String(now.getSeconds()).padStart(2, '0');
    entry.textContent = '[' + time + '] ' + text;
    logEntries.appendChild(entry);
    logEntries.parentElement.scrollTop = logEntries.parentElement.scrollHeight;
  }

  function updateProgress(percent) {
    progressBar.style.width = percent + '%';
    progressPercent.textContent = Math.floor(percent);
    for (var i = statusMessages.length - 1; i >= 0; i--) {
      if (percent >= statusMessages[i].pct) {
        progressStatus.textContent = statusMessages[i].status;
        if (i > lastLogIndex) {
          addLogEntry(statusMessages[i].log, statusMessages[i].type);
          lastLogIndex = i;
        }
        break;
      }
    }
    if (percent >= 50) loadingText.textContent = 'Connecting to Transfer Gateway...';
    if (percent >= 80) loadingText.textContent = 'Finalizing Configuration...';
    if (percent >= 100) loadingText.textContent = 'Server Ready!';
  }

  function showLoginSection() {
    loadingSection.classList.add('hidden');
    loginSection.classList.remove('hidden');
    if (licenseInput) licenseInput.focus();
  }

  function animate(timestamp) {
    if (!startTime) startTime = timestamp;
    var elapsed = timestamp - startTime;
    var percent = Math.min((elapsed / TOTAL_DURATION) * 100, 100);
    updateProgress(percent);
    if (percent < 100) {
      animationFrame = requestAnimationFrame(animate);
    } else {
      setTimeout(showLoginSection, 1000);
    }
  }

  startBtn.addEventListener('click', function () {
    startBtn.disabled = true;
    initialState.classList.add('hidden');
    loadingSection.classList.remove('hidden');
    addLogEntry('User initiated server start', 'info');
    animationFrame = requestAnimationFrame(animate);
  });

  function showMessage(text, isError) {
    if (!loginMessage) return;
    loginMessage.textContent = text;
    loginMessage.className = isError
      ? 'text-sm px-3 py-2 rounded-sm bg-error-container text-on-error-container border border-error/20'
      : 'text-sm px-3 py-2 rounded-sm bg-primary-fixed text-on-primary-fixed border border-primary/20';
    loginMessage.classList.remove('hidden');
  }

  function hideMessage() {
    if (loginMessage) {
      loginMessage.classList.add('hidden');
      loginMessage.textContent = '';
    }
  }

  function spawnCelebration() {
    var burst = document.getElementById('celebrationBurst');
    if (!burst) return;
    var colors = ['#10b981', '#34d399', '#00288e', '#60a5fa', '#f59e0b', '#f472b6'];
    for (var i = 0; i < 28; i++) {
      var piece = document.createElement('span');
      piece.className = 'confetti-piece';
      var dx = (Math.random() * 220 - 110).toFixed(1) + 'px';
      var rot = (Math.random() * 520 - 260).toFixed(1) + 'deg';
      piece.style.setProperty('--dx', dx);
      piece.style.setProperty('--rot', rot);
      piece.style.background = colors[i % colors.length];
      piece.style.marginLeft = (Math.random() * 24 - 12).toFixed(1) + 'px';
      piece.style.animationDelay = (Math.random() * 0.2).toFixed(2) + 's';
      piece.style.width = (6 + Math.random() * 6).toFixed(1) + 'px';
      piece.style.height = (8 + Math.random() * 8).toFixed(1) + 'px';
      burst.appendChild(piece);
    }
  }

  function showGatewayConnectionLoading() {
    if (!loginSection) return;
    loginSection.innerHTML =
      '<div id="gatewayConnectPhase" class="text-center py-6">' +
        '<div class="w-12 h-12 border-2 border-primary/20 border-t-primary rounded-full mx-auto mb-6" style="animation: spin 1s linear infinite;"></div>' +
        '<div class="font-headline-sm text-headline-sm text-primary tracking-wide mb-2">CONNECTING TO ELYSIUM SERVER</div>' +
        '<div class="font-body-sm text-body-sm text-on-surface-variant mb-6">Establishing secure connection...</div>' +
        '<div class="w-full h-1.5 bg-surface-container-high rounded-sm overflow-hidden">' +
          '<div id="gatewayProgressBar" class="h-full w-0 bg-primary transition-all duration-100"></div>' +
        '</div>' +
      '</div>';

    var progress = 0;
    var gatewayBar = document.getElementById('gatewayProgressBar');
    var interval = setInterval(function () {
      progress += 2;
      if (gatewayBar) gatewayBar.style.width = progress + '%';
      if (progress >= 100) {
        clearInterval(interval);
        loginSection.innerHTML =
          '<div class="text-center py-8 flex flex-col items-center gap-4 relative overflow-hidden">' +
            '<div id="celebrationBurst" class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true"></div>' +
            '<div class="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center success-pop relative z-10">' +
              '<span class="material-symbols-outlined text-emerald-600 text-[40px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span>' +
            '</div>' +
            '<div class="font-headline-sm text-headline-sm text-emerald-600 tracking-wide relative z-10 success-pop">Connection successful</div>' +
            '<div class="font-body-sm text-body-sm text-on-surface-variant relative z-10">Secure session ready. Continuing…</div>' +
          '</div>';
        spawnCelebration();
        setTimeout(function () {
          window.location.href = '/transfer_selection.php';
        }, 3000);
      }
    }, 100);
  }

  if (loginForm) {
    loginForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      hideMessage();
      var key = (licenseInput && licenseInput.value || '').trim();
      if (!key) {
        showMessage('License key is required.', true);
        return;
      }
      if (loginBtn) loginBtn.disabled = true;
      try {
        var res = await fetch('/api/auth.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'login', client_key: key })
        });
        var data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message || 'Authentication failed');
        showMessage('Authentication successful!', false);
        setTimeout(showGatewayConnectionLoading, 500);
      } catch (err) {
        showMessage(err.message || 'Authentication failed', true);
        if (loginBtn) loginBtn.disabled = false;
      }
    });
  }
})();
</script>
</body>
</html>
