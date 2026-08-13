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
    linear-gradient(to right, rgba(226, 232, 240, 0.7) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(226, 232, 240, 0.7) 1px, transparent 1px);
  background-size: 20px 20px;
}
.page-map-bg {
  position: fixed;
  inset: 0;
  z-index: 0;
  background-image: url('<?= htmlspecialchars($bgMap, ENT_QUOTES, 'UTF-8') ?>');
  background-size: 420px auto;
  background-position: center top;
  background-repeat: repeat;
  opacity: 0.1;
  pointer-events: none;
}
body.home-fit {
  height: 100dvh;
  max-height: 100dvh;
  overflow: hidden;
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
@keyframes spin {
  to { transform: rotate(360deg); }
}
@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
@keyframes success-in {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}
.success-in {
  animation: success-in 0.35s ease-out both;
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
<body class="home-fit bg-surface-container-low font-body-md text-on-surface technical-grid min-h-0 flex flex-col relative">
<div class="page-map-bg" aria-hidden="true"></div>

<header class="app-titlebar shrink-0 relative z-50 border-b border-border-subtle">
  <div class="h-10 w-full px-4 md:px-6 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
      <span class="font-meta-technical text-meta-technical text-on-surface tracking-widest uppercase">Elysium Server</span>
      <span class="hidden sm:inline w-px h-3 bg-border-subtle"></span>
      <span class="hidden sm:inline font-meta-mono text-meta-mono text-on-surface-variant">Gateway Console</span>
    </div>
    <div class="flex items-center gap-3 shrink-0">
      <span class="inline-flex items-center gap-1.5 font-meta-mono text-meta-mono text-on-surface-variant">
        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
        ONLINE
      </span>
      <span class="w-px h-3 bg-border-subtle"></span>
      <span id="headerServerIp" class="font-meta-mono text-meta-mono text-on-surface tracking-wide">—.—.—.—</span>
    </div>
  </div>
</header>

<main class="relative z-10 flex-1 min-h-0 w-full px-4 md:px-6 py-4 flex flex-col">
  <div class="flex flex-col flex-1 min-h-0 w-full justify-center items-center">
    <section class="app-panel w-full max-w-xl bg-surface-container-lowest border border-border-subtle rounded overflow-hidden flex flex-col">
      <div class="h-9 px-3 border-b border-border-subtle bg-surface-container-low flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
          <span class="material-symbols-outlined text-[16px] text-primary">dns</span>
          <span class="font-meta-technical text-meta-technical text-on-surface tracking-widest uppercase truncate">Session Bootstrap</span>
        </div>
        <span class="font-meta-mono text-meta-mono text-on-surface-variant shrink-0">v7.2.1</span>
      </div>

      <div class="px-5 py-5 sm:px-6 sm:py-6 flex flex-col items-center">
        <div id="brandBlock" class="mb-5 flex flex-col items-center text-center">
          <img alt="Elysium App Identity" class="w-11 h-11 object-contain mb-3 mix-blend-multiply" src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"/>
          <h1 class="font-headline-md text-headline-md text-on-surface tracking-tight uppercase mb-1">Elysium Server</h1>
          <p class="font-meta-mono text-meta-mono text-on-surface-variant">Secure Transfer Gateway</p>
        </div>

        <div id="initialState" class="w-full flex flex-col items-center">
          <p class="font-body-sm text-body-sm text-on-surface-variant text-center max-w-md mb-5">
            Initialize a secure connection to the transfer gateway.
          </p>
          <button id="startBtn" type="button" class="app-btn bg-primary text-on-primary font-meta-technical text-meta-technical tracking-widest uppercase h-9 px-6 rounded-sm hover:bg-primary/90 transition-colors w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-primary disabled:opacity-50 disabled:cursor-not-allowed">
            Start Server
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
          </button>
          <div class="mt-4 flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
            <span class="font-meta-technical text-meta-technical text-on-surface-variant tracking-widest uppercase">System Ready</span>
          </div>
        </div>

        <div id="loadingSection" class="hidden w-full">
          <div class="flex items-center gap-2.5 mb-4">
            <div class="w-5 h-5 border-2 border-primary/20 border-t-primary rounded-full" style="animation: spin 0.8s linear infinite;"></div>
            <div id="loadingText" class="font-meta-technical text-meta-technical text-on-surface tracking-widest uppercase">Initializing Server</div>
          </div>
          <div class="w-full h-1.5 bg-surface-container-high rounded-sm overflow-hidden mb-3">
            <div id="progressBar" class="h-full w-0 bg-primary transition-all duration-100" style="background-size: 200% 100%; animation: shimmer 2s linear infinite;"></div>
          </div>
          <div class="flex items-center justify-between gap-4 mb-4">
            <div class="font-meta-mono text-[18px] leading-6 text-primary tabular-nums font-semibold"><span id="progressPercent">0</span>%</div>
            <div id="progressStatus" class="font-body-sm text-body-sm text-on-surface-variant text-right max-w-[240px]">Preparing secure environment...</div>
          </div>
          <div class="w-full border border-border-subtle bg-surface-container-low rounded-sm p-3 max-h-40 overflow-y-auto">
            <div class="font-meta-technical text-meta-technical text-outline tracking-widest uppercase mb-2">System Log</div>
            <div id="logEntries" class="font-meta-mono text-meta-mono space-y-1 text-on-surface-variant"></div>
          </div>
        </div>

        <div id="loginSection" class="hidden w-full max-w-md mx-auto">
          <div class="mb-4 text-center">
            <div class="font-meta-technical text-meta-technical text-on-surface tracking-widest uppercase mb-1">Server Online</div>
            <div class="font-body-sm text-body-sm text-on-surface-variant">Enter license key to authenticate</div>
          </div>
          <form id="loginForm" class="flex flex-col gap-3">
            <div class="flex flex-col gap-1">
              <label class="font-meta-technical text-meta-technical text-on-surface-variant tracking-widest uppercase" for="licenseKey">License Key</label>
              <input id="licenseKey" class="app-control w-full h-9 border border-border-subtle rounded-sm bg-white px-3 font-meta-mono text-meta-mono text-on-surface" type="text" placeholder="XXXX-XXXX-XXXX" required autocomplete="off"/>
            </div>
            <div id="loginMessage" class="hidden text-sm px-3 py-2 rounded-sm"></div>
            <button type="submit" id="loginBtn" class="app-btn w-full h-9 bg-primary text-on-primary font-meta-technical text-meta-technical tracking-widest uppercase rounded-sm hover:bg-primary/90 transition-colors border border-primary disabled:opacity-50 disabled:cursor-not-allowed">
              Authenticate
            </button>
          </form>
        </div>
      </div>

      <div class="h-8 px-3 border-t border-border-subtle bg-surface-container-low flex items-center justify-between gap-3">
        <span class="font-meta-mono text-meta-mono text-on-surface-variant">BUILD 2024.03.27</span>
        <span class="font-meta-mono text-meta-mono text-on-surface-variant">TLS 1.3</span>
      </div>
    </section>
  </div>
</main>

<footer class="app-statusbar relative z-10 shrink-0 w-full h-8 border-t border-border-subtle">
  <div class="h-full px-4 md:px-6 flex justify-between items-center gap-4">
    <div class="flex items-center gap-4 min-w-0">
      <span class="font-meta-mono text-meta-mono text-on-surface-variant uppercase truncate">ENV Production</span>
      <span class="hidden sm:inline font-meta-mono text-meta-mono text-on-surface-variant uppercase">Build v4.2.0-stable</span>
    </div>
    <div class="font-meta-mono text-meta-mono text-primary uppercase shrink-0">Secure Node Connected</div>
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

  function showGatewayConnectionLoading() {
    if (!loginSection) return;
    loginSection.innerHTML =
      '<div id="gatewayConnectPhase" class="text-center py-4">' +
        '<div class="w-6 h-6 border-2 border-primary/20 border-t-primary rounded-full mx-auto mb-4" style="animation: spin 0.8s linear infinite;"></div>' +
        '<div class="font-meta-technical text-meta-technical text-on-surface tracking-widest uppercase mb-1">Connecting</div>' +
        '<div class="font-body-sm text-body-sm text-on-surface-variant mb-4">Establishing secure session…</div>' +
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
          '<div class="text-center py-5 flex flex-col items-center gap-3 success-in">' +
            '<div class="w-10 h-10 rounded-sm bg-emerald-50 border border-emerald-200 flex items-center justify-center">' +
              '<span class="material-symbols-outlined text-emerald-600 text-[28px]" style="font-variation-settings: \'FILL\' 1;">check</span>' +
            '</div>' +
            '<div class="font-meta-technical text-meta-technical text-emerald-700 tracking-widest uppercase">Connection Successful</div>' +
            '<div class="font-meta-mono text-meta-mono text-on-surface-variant">Secure session ready · continuing</div>' +
          '</div>';
        setTimeout(function () {
          window.location.href = '/transfer_selection.php';
        }, 2200);
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
