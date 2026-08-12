<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

// If already authenticated, skip to transfer selection
if (is_logged_in()) {
    header('Location: /transfer_selection.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Elysium Server | Initialize</title>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "JetBrains Mono", "Courier New", monospace;
  }

  body {
    min-height: 100vh;
    background: linear-gradient(135deg, #1a1a0a 0%, #2d2d00 25%, #3d3d00 50%, #1a1a0a 100%);
    padding: 40px 60px 48px;
    position: relative;
    overflow-x: hidden;
    overflow-y: auto;
  }

  @media (max-width: 768px) {
    body {
      padding: 30px 20px 40px;
    }
  }

  .bg-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 500px;
    height: 500px;
    opacity: 0.05;
    pointer-events: none;
    z-index: 0;
  }

  .content {
    position: relative;
    z-index: 1;
    max-width: 600px;
    width: 100%;
    margin: clamp(16px, 4vw, 40px) auto;
    padding: clamp(24px, 5vw, 48px);
    border: 2px solid rgba(212, 160, 0, 0.4);
    border-radius: 20px;
    background: rgba(0, 0, 0, 0.35);
    box-shadow:
      0 12px 40px rgba(0, 0, 0, 0.45),
      inset 0 1px 0 rgba(255, 255, 255, 0.06);
  }

  @media (max-width: 768px) {
    .content {
      margin: 16px 12px;
      padding: 22px 18px;
    }
  }

  .logo-section {
    margin-bottom: 40px;
  }

  .logo-section img,
  .logo-section svg {
    height: 70px;
    width: auto;
    display: block;
    margin: 0 auto;
  }

  .logo-section svg {
    color: #d4a000;
  }

  @media (min-width: 768px) {
    .logo-section img,
    .logo-section svg {
      height: 80px;
    }
  }

  .title {
    font-size: 28px;
    font-weight: 800;
    color: #d4a000;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  @media (min-width: 768px) {
    .title {
      font-size: 36px;
    }
  }

  .subtitle {
    font-size: 14px;
    color: #888;
    letter-spacing: 2px;
  }

  @media (min-width: 768px) {
    .subtitle {
      font-size: 16px;
    }
  }

  .description {
    color: #777;
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 15px;
  }

  @media (min-width: 768px) {
    .description {
      font-size: 16px;
    }
  }

  .start-btn {
    background: linear-gradient(135deg, #d4a000 0%, #b8860b 100%);
    color: #000;
    border: none;
    padding: 18px 56px;
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 3px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 30px;
  }

  @media (min-width: 768px) {
    .start-btn {
      padding: 20px 64px;
      font-size: 18px;
    }
  }

  .start-btn:hover {
    background: linear-gradient(135deg, #e6b000 0%, #d4a000 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 160, 0, 0.3);
  }

  .start-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }

  .loading-section {
    display: none;
    margin-top: 30px;
  }

  .loading-section.active {
    display: block;
  }

  .loading-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
  }

  .spinner {
    width: 32px;
    height: 32px;
    border: 4px solid rgba(212, 160, 0, 0.2);
    border-top-color: #d4a000;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  .loading-text {
    font-size: 16px;
    font-weight: 700;
    color: #d4a000;
    letter-spacing: 2px;
    text-transform: uppercase;
  }

  @media (min-width: 768px) {
    .loading-text {
      font-size: 18px;
    }
  }

  .progress-container {
    background: rgba(255, 255, 255, 0.1);
    height: 10px;
    width: 100%;
    overflow: hidden;
    margin-bottom: 15px;
  }

  .progress-bar {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #d4a000 0%, #ffd700 50%, #d4a000 100%);
    background-size: 200% 100%;
    animation: shimmer 2s linear infinite;
    transition: width 0.1s linear;
  }

  @keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .progress-percent {
    font-size: 32px;
    font-weight: 800;
    color: #d4a000;
  }

  @media (min-width: 768px) {
    .progress-percent {
      font-size: 40px;
    }
  }

  .progress-status {
    font-size: 12px;
    color: #888;
    text-align: right;
    max-width: 220px;
  }

  @media (min-width: 768px) {
    .progress-status {
      font-size: 13px;
    }
  }

  .log-section {
    margin-top: 30px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(212, 160, 0, 0.2);
    padding: 15px;
    max-height: 200px;
    overflow-y: auto;
  }

  .log-title {
    font-size: 11px;
    font-weight: 700;
    color: #666;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  .log-entry {
    font-size: 11px;
    color: #6a6a6a;
    margin: 4px 0;
    padding-left: 10px;
    border-left: 2px solid #333;
  }

  .log-entry.success {
    color: #4a9;
    border-left-color: #4a9;
  }

  .log-entry.warning {
    color: #d4a000;
    border-left-color: #d4a000;
  }

  .log-entry.info {
    color: #888;
    border-left-color: #555;
  }

  .version-info {
    max-width: 600px;
    margin: 8px auto 0;
    padding: 0 4px;
    font-size: 11px;
    color: rgba(255, 255, 255, 0.35);
    letter-spacing: 1px;
    text-align: center;
  }

  /* Login Section */
  .login-section {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a1a0a 0%, #2d2d00 25%, #3d3d00 50%, #1a1a0a 100%);
    z-index: 100;
  }

  .login-section.active {
    display: flex;
  }

  .login-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
    max-width: 420px;
    width: 90%;
  }

  .login-card-header {
    text-align: center;
    margin-bottom: 30px;
  }

  .login-card-header img,
  .login-card-header svg {
    height: 50px;
    width: auto;
    display: block;
    margin: 0 auto 15px;
  }

  .login-card-header svg {
    color: #d4a000;
  }

  .login-card-title {
    font-family: "Inter", sans-serif;
    font-size: 22px;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 5px;
  }

  .login-card-subtitle {
    font-size: 12px;
    color: #666;
    letter-spacing: 1px;
  }

  .login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .login-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .login-label {
    font-family: "Inter", sans-serif;
    font-size: 11px;
    font-weight: 600;
    color: #555;
    letter-spacing: 1px;
    text-transform: uppercase;
  }

  .login-input-wrap {
    border: 2px solid #0f0f0f;
    border-radius: 12px;
    padding: 0 15px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  .login-input-wrap:focus-within {
    border-color: #d4a000;
    box-shadow: 0 0 0 3px rgba(212, 160, 0, 0.15);
  }

  .login-input {
    font-family: "Inter", sans-serif;
    width: 100%;
    border: none;
    outline: none;
    padding: 14px 0;
    font-size: 14px;
    color: #1a1a1a;
    background: transparent;
  }

  .login-input::placeholder {
    color: #aaa;
  }

  .login-message {
    display: none;
    font-family: "Inter", sans-serif;
    font-size: 13px;
    font-weight: 500;
    text-align: center;
    padding: 10px;
    border-radius: 8px;
  }

  .login-message.error {
    display: block;
    background: #fef2f2;
    color: #dc2626;
  }

  .login-message.success {
    display: block;
    background: #f0fdf4;
    color: #16a34a;
  }

  .login-btn {
    font-family: "Inter", sans-serif;
    background: linear-gradient(135deg, #d4a000 0%, #b8860b 100%);
    color: #000;
    border: none;
    padding: 16px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
  }

  .login-btn:hover {
    background: linear-gradient(135deg, #e6b000 0%, #d4a000 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(212, 160, 0, 0.3);
  }

  .login-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }

  .login-footer {
    text-align: center;
    margin-top: 20px;
    font-size: 10px;
    color: #999;
    letter-spacing: 1px;
  }
</style>
</head>
<body>

<div class="content">
  <div class="logo-section">
    <svg viewBox="0 0 56 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path fill="currentColor" d="M28 2 6 12v18c0 14.5 9.2 27.4 22 32 12.8-4.6 22-17.5 22-32V12L28 2Z"/>
      <path fill="#fff" fill-opacity=".2" d="M28 8 14 14.2V28c0 10.2 6.4 19.3 14 23 7.6-3.7 14-12.8 14-23V14.2L28 8Z"/>
    </svg>
    <div class="title" style="margin-top: 20px;">Elysium Server</div>
    <div class="subtitle">Secure Transfer Gateway v7.2.1</div>
  </div>

  <!-- Initial State -->
  <div id="initialState">
    <div class="description">
      Initialize a secure connection to the Elysium transfer gateway.<br>
      All sessions are encrypted and authenticated.
    </div>
    <button class="start-btn" id="startBtn">Start Server</button>
  </div>

  <!-- Loading State -->
  <div class="loading-section" id="loadingSection">
    <div class="loading-header">
      <div class="spinner"></div>
      <div class="loading-text" id="loadingText">Initializing Server...</div>
    </div>

    <div class="progress-container">
      <div class="progress-bar" id="progressBar"></div>
    </div>

    <div class="progress-info">
      <div class="progress-percent"><span id="progressPercent">0</span>%</div>
      <div class="progress-status" id="progressStatus">Preparing secure environment...</div>
    </div>

    <div class="log-section" id="logSection">
      <div class="log-title">System Log</div>
      <div id="logEntries"></div>
    </div>
  </div>

  <!-- Login State -->
  <div class="login-section" id="loginSection">
    <div class="login-card">
      <div class="login-card-header">
        <svg viewBox="0 0 56 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path fill="currentColor" d="M28 2 6 12v18c0 14.5 9.2 27.4 22 32 12.8-4.6 22-17.5 22-32V12L28 2Z"/>
          <path fill="#fff" fill-opacity=".2" d="M28 8 14 14.2V28c0 10.2 6.4 19.3 14 23 7.6-3.7 14-12.8 14-23V14.2L28 8Z"/>
        </svg>
        <div class="login-card-title">Server Online</div>
        <div class="login-card-subtitle">Enter your license key to continue</div>
      </div>
      <form class="login-form" id="loginForm">
        <div class="login-field">
          <label class="login-label" for="licenseKey">License Key</label>
          <div class="login-input-wrap">
            <input class="login-input" type="text" id="licenseKey" placeholder="Enter license key" required autocomplete="off" />
          </div>
        </div>
        <div class="login-message" id="loginMessage"></div>
        <button type="submit" class="login-btn" id="loginBtn">Authenticate</button>
      </form>
      <div class="login-footer">
        BUILD: 2024.03.27 | ELYSIUM
      </div>
    </div>
  </div>
</div>

<div class="version-info">
  BUILD: 2024.03.27 | ELYSIUM
</div>

<script>
(function() {
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

  var TOTAL_DURATION = 25000; // 25 seconds
  var startTime = null;
  var animationFrame = null;

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

  var lastLogIndex = -1;

  function addLogEntry(text, type) {
    var entry = document.createElement('div');
    entry.className = 'log-entry ' + (type || 'info');
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

    if (percent >= 50) {
      loadingText.textContent = 'Connecting to Transfer Gateway...';
    }
    if (percent >= 80) {
      loadingText.textContent = 'Finalizing Configuration...';
    }
    if (percent >= 100) {
      loadingText.textContent = 'Server Ready!';
    }
  }

  function showLoginSection() {
    loadingSection.classList.remove('active');
    loginSection.classList.add('active');
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

  startBtn.addEventListener('click', function() {
    startBtn.disabled = true;
    initialState.style.display = 'none';
    loadingSection.classList.add('active');
    
    addLogEntry('User initiated server start', 'info');
    
    animationFrame = requestAnimationFrame(animate);
  });

  // Login form handling
  function showMessage(text, isError) {
    if (!loginMessage) return;
    loginMessage.textContent = text;
    loginMessage.className = 'login-message ' + (isError ? 'error' : 'success');
  }

  function hideMessage() {
    if (loginMessage) {
      loginMessage.className = 'login-message';
      loginMessage.textContent = '';
    }
  }

  // Post-login gateway connection loading
  function showGatewayConnectionLoading() {
    var loginCard = document.querySelector('.login-card');
    if (loginCard) {
      loginCard.innerHTML = `
        <div style="text-align: center; padding: 20px;">
          <svg style="height: 60px; width: 52px; margin: 0 auto 20px; color: #d4a000;" viewBox="0 0 56 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path fill="currentColor" d="M28 2 6 12v18c0 14.5 9.2 27.4 22 32 12.8-4.6 22-17.5 22-32V12L28 2Z"/>
            <path fill="#fff" fill-opacity=".2" d="M28 8 14 14.2V28c0 10.2 6.4 19.3 14 23 7.6-3.7 14-12.8 14-23V14.2L28 8Z"/>
          </svg>
          <div style="margin-bottom: 20px;">
            <div style="width: 50px; height: 50px; border: 4px solid rgba(212, 160, 0, 0.2); border-top-color: #d4a000; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto;"></div>
          </div>
          <div style="font-family: 'JetBrains Mono', monospace; font-size: 16px; font-weight: 700; color: #d4a000; letter-spacing: 1px; margin-bottom: 10px;">
            CONNECTING TO ELYSIUM SERVER
          </div>
          <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #666;">
            Establishing secure connection...
          </div>
          <div style="margin-top: 20px; background: rgba(212, 160, 0, 0.1); height: 6px; border-radius: 3px; overflow: hidden;">
            <div id="gatewayProgressBar" style="height: 100%; width: 0%; background: linear-gradient(90deg, #d4a000 0%, #ffd700 50%, #d4a000 100%); transition: width 0.1s linear;"></div>
          </div>
        </div>
      `;

      var progress = 0;
      var gatewayBar = document.getElementById('gatewayProgressBar');
      var interval = setInterval(function() {
        progress += 2;
        if (gatewayBar) gatewayBar.style.width = progress + '%';
        if (progress >= 100) {
          clearInterval(interval);
          window.location.href = '/transfer_selection.php';
        }
      }, 100);
    }
  }

  if (loginForm) {
    loginForm.addEventListener('submit', async function(e) {
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
        if (!res.ok || !data.success) {
          throw new Error(data.message || 'Authentication failed');
        }
        showMessage('Authentication successful!', false);
        setTimeout(function() {
          showGatewayConnectionLoading();
        }, 500);
      } catch (err) {
        showMessage(err.message, true);
        if (loginBtn) loginBtn.disabled = false;
      }
    });
  }
})();
</script>
</body>
</html>
