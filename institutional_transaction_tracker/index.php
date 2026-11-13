<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$cfg = tracker_config();
$loadingMs = (int) ($cfg['otp']['loading_ms'] ?? 5000);
$postEmailMs = (int) ($cfg['otp']['post_email_wait_ms'] ?? 2000);
$sitesConfigured = count(tracker_enabled_sites());
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Institutional Transaction Tracker | Elysium</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#006837',
            'state-success': '#16834A',
            'state-success-bg': '#EFF8F1',
            'state-pending': '#D97706',
            'state-pending-bg': '#FEF3C7',
            'state-failed': '#C43D3D',
            'state-failed-bg': '#FFF1F1',
            'border-subtle': '#E5E5E5',
            'text-primary': '#171717',
            'text-secondary': '#666666',
            'text-muted': '#858585',
          },
          fontFamily: {
            sans: ['Geist', 'system-ui', 'sans-serif'],
            mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
          }
        }
      }
    }
  </script>
  <style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    body { font-family: Geist, system-ui, sans-serif; }
    .tabular { font-variant-numeric: tabular-nums; }
  </style>
</head>
<body class="bg-[#F7F8F7] text-text-primary min-h-screen">
  <header class="sticky top-0 z-40 bg-[#0B3D2E] text-white border-b border-white/10">
    <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded bg-white/10 flex items-center justify-center font-mono text-xs font-semibold">EF</div>
        <div>
          <div class="text-sm font-semibold tracking-wide">Elysium Flash</div>
          <div class="text-[10px] uppercase tracking-[0.08em] text-white/60">Institutional Tracker</div>
        </div>
      </div>
      <nav class="hidden sm:flex items-center gap-6 text-xs font-semibold uppercase tracking-wide text-white/80">
        <a href="#lookup" class="hover:text-white">Track Transaction</a>
      </nav>
    </div>
  </header>

  <main>
    <section id="lookup" class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-[#0B3D2E] via-[#0F5A3F] to-[#16834A]"></div>
      <div class="relative max-w-3xl mx-auto px-4 py-16 sm:py-20 text-white">
        <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight">Track Your Transaction.</h1>
        <p class="mt-3 text-white/80 text-sm sm:text-base max-w-xl">Enter the Unique End-to-End Transaction Reference (UETR) to retrieve institutional rail status after email verification.</p>

        <form id="tracker-form" class="mt-8 bg-white text-text-primary rounded-md border border-border-subtle shadow-sm p-5 sm:p-6" onsubmit="return false;">
          <label class="block text-[11px] font-semibold uppercase tracking-[0.06em] text-text-secondary mb-2">UETR</label>
          <div class="flex flex-col sm:flex-row gap-3">
            <input id="transaction-input" type="text" maxlength="36" placeholder="xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx"
                   class="flex-1 h-10 px-3 rounded border border-border-subtle font-mono text-sm outline-none focus:border-text-primary" />
            <button id="trackBtn" type="button" class="h-10 px-5 rounded bg-[#171717] text-white text-sm font-semibold hover:bg-[#333]">
              Track Reference
            </button>
          </div>
          <p class="mt-2 text-xs text-text-muted">UUID v4 format · 36 characters including hyphens · <?= (int) $sitesConfigured ?> site(s) configured</p>
        </form>
      </div>
    </section>

    <section class="max-w-4xl mx-auto px-4 -mt-6 pb-16 relative z-10">
      <div id="result-container" class="hidden bg-white border border-border-subtle rounded-md overflow-hidden">
        <div class="px-5 py-4 border-b border-border-subtle flex flex-wrap items-start justify-between gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-[0.06em] text-text-muted font-semibold">UETR</div>
            <div id="res-ref" class="font-mono text-sm font-semibold break-all"></div>
            <div id="res-meta" class="text-xs text-text-muted mt-1"></div>
          </div>
          <span id="status-badge" class="inline-flex items-center px-3 py-1.5 rounded text-xs font-mono font-semibold tracking-wide uppercase border"></span>
        </div>

        <div id="res-not-found" class="hidden px-5 py-10 text-center">
          <div class="material-symbols-outlined text-4xl text-text-muted mb-2">search_off</div>
          <p class="text-sm font-semibold">No record found</p>
          <p class="text-xs text-text-muted mt-1">This UETR was not located on any configured Elysium site.</p>
        </div>

        <div id="res-data-content" class="hidden">
          <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-border-subtle">
            <div>
              <div class="text-[11px] uppercase tracking-[0.06em] text-text-muted font-semibold">Amount</div>
              <div id="res-amount" class="text-lg font-semibold tabular mt-1"></div>
            </div>
            <div>
              <div class="text-[11px] uppercase tracking-[0.06em] text-text-muted font-semibold">Sender</div>
              <div id="res-sender" class="text-sm font-semibold mt-1"></div>
              <div id="res-sender-sub" class="text-xs font-mono text-text-muted"></div>
            </div>
            <div>
              <div class="text-[11px] uppercase tracking-[0.06em] text-text-muted font-semibold">Beneficiary</div>
              <div id="res-beneficiary" class="text-sm font-semibold mt-1"></div>
              <div id="res-beneficiary-sub" class="text-xs font-mono text-text-muted"></div>
            </div>
          </div>

          <div class="px-5 py-4 border-b border-border-subtle grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div class="flex justify-between gap-3"><span class="text-text-muted">Date &amp; time</span><span id="res-datetime" class="font-medium text-right"></span></div>
            <div class="flex justify-between gap-3"><span class="text-text-muted">Internal reference</span><span id="res-reference" class="font-mono text-xs text-right break-all"></span></div>
            <div class="flex justify-between gap-3"><span class="text-text-muted">Narration</span><span id="res-purpose" class="text-right max-w-[60%]"></span></div>
            <div class="flex justify-between gap-3"><span class="text-text-muted">Source site</span><span id="res-site" class="font-medium text-right"></span></div>
          </div>

          <div id="intl-block" class="hidden px-5 py-4 border-b border-border-subtle grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div class="flex justify-between gap-3"><span class="text-text-muted">Message type</span><span id="res-msgtype" class="text-right"></span></div>
            <div class="flex justify-between gap-3"><span class="text-text-muted">Delivery date</span><span id="res-delivery" class="text-right"></span></div>
            <div class="flex justify-between gap-3 sm:col-span-2"><span class="text-text-muted">SWIFT / IBAN</span><span id="res-swift-iban" class="font-mono text-xs text-right"></span></div>
          </div>

          <div class="px-5 py-4">
            <p id="res-description" class="text-sm text-text-secondary mb-4"></p>
            <div id="timeline-steps" class="grid grid-cols-1 sm:grid-cols-3 gap-3"></div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Gate modal -->
  <div id="gate-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative w-full max-w-md bg-white border border-border-subtle rounded-md shadow-lg overflow-hidden">
      <div class="px-5 py-4 border-b border-border-subtle">
        <h2 id="gate-title" class="text-base font-semibold">Locating transaction</h2>
        <p id="gate-subtitle" class="text-xs text-text-muted mt-1">Querying configured Elysium sites…</p>
      </div>
      <div class="px-5 py-6">
        <div id="gate-loading" class="text-center py-4">
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-full border-2 border-primary/30 border-t-primary animate-spin mb-3"></div>
          <p class="text-sm text-text-secondary">Please wait…</p>
        </div>
        <div id="gate-email" class="hidden space-y-3">
          <label class="block text-[11px] font-semibold uppercase tracking-[0.06em] text-text-secondary">Email for 2FA</label>
          <input id="email-input" type="email" class="w-full h-10 px-3 rounded border border-border-subtle text-sm outline-none focus:border-text-primary" placeholder="name@institution.com" />
          <button id="sendOtpBtn" type="button" class="w-full h-10 rounded bg-[#171717] text-white text-sm font-semibold">Send verification code</button>
        </div>
        <div id="gate-otp" class="hidden space-y-3">
          <label class="block text-[11px] font-semibold uppercase tracking-[0.06em] text-text-secondary">Enter OTP</label>
          <input id="otp-input" type="text" inputmode="numeric" maxlength="6" class="w-full h-10 px-3 rounded border border-border-subtle font-mono text-center text-lg tracking-[0.3em] outline-none focus:border-text-primary" placeholder="••••••" />
          <p id="otp-hint" class="text-xs text-text-muted"></p>
          <button id="verifyOtpBtn" type="button" class="w-full h-10 rounded bg-[#171717] text-white text-sm font-semibold">Verify &amp; view status</button>
        </div>
        <div id="gate-error" class="hidden text-sm text-state-failed bg-state-failed-bg border border-red-200 rounded px-3 py-2"></div>
      </div>
      <div class="px-5 py-3 border-t border-border-subtle flex justify-end">
        <button id="gate-close" type="button" class="text-sm text-text-secondary hover:text-text-primary">Cancel</button>
      </div>
    </div>
  </div>

  <footer class="border-t border-border-subtle bg-white">
    <div class="max-w-6xl mx-auto px-4 py-6 text-xs text-text-muted flex flex-wrap justify-between gap-2">
      <span>© <?= date('Y') ?> Elysium Flash · Institutional Transaction Tracker</span>
      <span class="font-mono">Multi-site S2S lookup</span>
    </div>
  </footer>

  <script>
    (function () {
      const LOADING_MS = <?= (int) $loadingMs ?>;
      const POST_EMAIL_MS = <?= (int) $postEmailMs ?>;

      const modal = document.getElementById('gate-modal');
      const gateLoading = document.getElementById('gate-loading');
      const gateEmail = document.getElementById('gate-email');
      const gateOtp = document.getElementById('gate-otp');
      const gateError = document.getElementById('gate-error');
      const gateTitle = document.getElementById('gate-title');
      const gateSubtitle = document.getElementById('gate-subtitle');

      let currentUetr = '';
      let currentEmail = '';

      function showModal() { modal.classList.remove('hidden'); }
      function hideModal() { modal.classList.add('hidden'); }
      function showError(msg) {
        gateError.textContent = msg || 'Something went wrong';
        gateError.classList.remove('hidden');
      }
      function clearError() { gateError.classList.add('hidden'); gateError.textContent = ''; }
      function setPhase(phase) {
        clearError();
        gateLoading.classList.toggle('hidden', phase !== 'loading');
        gateEmail.classList.toggle('hidden', phase !== 'email');
        gateOtp.classList.toggle('hidden', phase !== 'otp');
      }

      function sleep(ms) { return new Promise((r) => setTimeout(r, ms)); }

      function badgeClass(family) {
        if (family === 'terminal_ok') return 'bg-state-success-bg text-state-success border-state-success/30';
        if (family === 'terminal_fail') return 'bg-state-failed-bg text-state-failed border-state-failed/30';
        return 'bg-state-pending-bg text-state-pending border-state-pending/30';
      }

      function stepBorder(state) {
        if (state === 'complete') return 'border-state-success';
        if (state === 'active') return 'border-state-pending';
        if (state === 'failed') return 'border-state-failed';
        return 'border-border-subtle opacity-60';
      }

      function stepColor(state) {
        if (state === 'complete') return 'text-state-success';
        if (state === 'active') return 'text-state-pending';
        if (state === 'failed') return 'text-state-failed';
        return 'text-text-muted';
      }

      function stepIcon(state) {
        if (state === 'complete') return 'check_circle';
        if (state === 'active') return 'sync';
        if (state === 'failed') return 'cancel';
        return 'hourglass_empty';
      }

      function renderPayload(data) {
        const container = document.getElementById('result-container');
        const content = document.getElementById('res-data-content');
        const notFound = document.getElementById('res-not-found');
        container.classList.remove('hidden');
        notFound.classList.add('hidden');
        content.classList.remove('hidden');

        document.getElementById('res-ref').textContent = data.uetr || currentUetr;
        document.getElementById('res-meta').textContent = 'Recorded: ' + (data.transaction_date || data.last_updated_iso_utc || '—');
        const badge = document.getElementById('status-badge');
        badge.className = 'inline-flex items-center px-3 py-1.5 rounded text-xs font-mono font-semibold tracking-wide uppercase border ' + badgeClass(data.tracker_family);
        badge.textContent = 'STATUS: ' + (data.tracker_status || '') + ' · ' + (data.tracker_label || '');

        const amount = Number(data.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('res-amount').textContent = amount + ' ' + (data.currency || '');
        document.getElementById('res-sender').textContent = (data.sender && data.sender.name) || '—';
        document.getElementById('res-sender-sub').textContent = [
          data.sender && data.sender.account_masked,
          data.sender && data.sender.bank,
          data.sender && data.sender.swift
        ].filter(Boolean).join(' · ') || '—';
        document.getElementById('res-beneficiary').textContent = (data.beneficiary && data.beneficiary.name) || '—';
        document.getElementById('res-beneficiary-sub').textContent = [
          data.beneficiary && data.beneficiary.bank,
          data.beneficiary && data.beneficiary.account_masked,
          data.beneficiary && data.beneficiary.iban_masked,
          data.beneficiary && data.beneficiary.swift
        ].filter(Boolean).join(' · ') || '—';

        document.getElementById('res-datetime').textContent = data.transaction_date || '—';
        document.getElementById('res-reference').textContent = data.reference || '—';
        document.getElementById('res-purpose').textContent = data.purpose || '—';
        document.getElementById('res-site').textContent = (data.site_name || data.site_id || '') + (data.rail ? ' · ' + data.rail : '');
        document.getElementById('res-description').textContent = data.tracker_description || '';

        const intl = document.getElementById('intl-block');
        if (data.rail === 'international' && data.international) {
          intl.classList.remove('hidden');
          document.getElementById('res-msgtype').textContent = data.international.message_type || '—';
          document.getElementById('res-delivery').textContent = data.international.delivery_date || '—';
          document.getElementById('res-swift-iban').textContent = [
            data.beneficiary && data.beneficiary.swift,
            data.beneficiary && data.beneficiary.iban_masked
          ].filter(Boolean).join(' / ') || '—';
        } else {
          intl.classList.add('hidden');
        }

        const timeline = Array.isArray(data.timeline) ? data.timeline : [];
        const wrap = document.getElementById('timeline-steps');
        wrap.innerHTML = timeline.map((step) => {
          const state = step.state || 'pending';
          return `
            <div class="border-t-4 pt-3 bg-[#F9FBF9] p-3 rounded-b ${stepBorder(state)}">
              <div class="flex items-center gap-1.5 mb-1 ${stepColor(state)}">
                <span class="material-symbols-outlined text-[18px]">${stepIcon(state)}</span>
                <span class="text-xs font-semibold">${step.title || ''}</span>
              </div>
              <p class="text-xs text-text-secondary">${step.description || ''}</p>
            </div>`;
        }).join('');

        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }

      function renderNotFound(uetr) {
        const container = document.getElementById('result-container');
        container.classList.remove('hidden');
        document.getElementById('res-data-content').classList.add('hidden');
        document.getElementById('res-not-found').classList.remove('hidden');
        document.getElementById('res-ref').textContent = uetr || 'UNKNOWN';
        document.getElementById('res-meta').textContent = 'Lookup executed just now';
        const badge = document.getElementById('status-badge');
        badge.className = 'inline-flex items-center px-3 py-1.5 rounded text-xs font-mono font-semibold tracking-wide uppercase border bg-[#F3F4F6] text-text-muted border-border-subtle';
        badge.textContent = 'STATUS: NOT FOUND';
      }

      async function startTrack() {
        const input = document.getElementById('transaction-input');
        const uetr = (input.value || '').trim();
        currentUetr = uetr;
        if (!uetr) {
          showModal();
          setPhase('loading');
          gateTitle.textContent = 'Missing UETR';
          gateSubtitle.textContent = 'Enter a valid UUID v4 UETR to continue.';
          await sleep(800);
          hideModal();
          return;
        }

        showModal();
        setPhase('loading');
        gateTitle.textContent = 'Locating transaction';
        gateSubtitle.textContent = 'Searching configured Elysium sites…';

        const locatePromise = fetch('api/locate.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ uetr })
        }).then(async (res) => ({ res, data: await res.json().catch(() => ({})) }));

        await sleep(LOADING_MS);
        const { res, data } = await locatePromise;

        if (!res.ok || !data.success) {
          setPhase('email');
          gateTitle.textContent = 'Unable to locate';
          gateSubtitle.textContent = data.message || 'Locate failed';
          showError(data.message || 'Locate failed');
          return;
        }
        if (!data.found) {
          hideModal();
          renderNotFound(uetr);
          return;
        }

        gateTitle.textContent = 'Verify identity';
        gateSubtitle.textContent = 'Found on ' + (data.site_name || data.site_id) + ' · Enter email for OTP';
        setPhase('email');
      }

      async function sendOtp() {
        clearError();
        const email = (document.getElementById('email-input').value || '').trim();
        currentEmail = email;
        if (!email) return showError('Email is required');

        setPhase('loading');
        gateTitle.textContent = 'Sending verification code';
        gateSubtitle.textContent = 'Delivering OTP to your email…';

        const req = fetch('api/otp_request.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ uetr: currentUetr, email })
        }).then(async (res) => ({ res, data: await res.json().catch(() => ({})) }));

        await sleep(POST_EMAIL_MS);
        const { res, data } = await req;
        if (!res.ok || !data.success) {
          setPhase('email');
          return showError(data.message || 'Failed to send OTP');
        }

        gateTitle.textContent = 'Enter verification code';
        gateSubtitle.textContent = data.mail_sent
          ? 'A 6-digit code was sent to ' + email
          : 'OTP generated (configure SMTP on Elysium for email delivery)';
        document.getElementById('otp-hint').textContent = data.debug_otp
          ? ('Simulation OTP (SMTP not configured): ' + data.debug_otp)
          : 'Check your inbox for the 2FA code.';
        setPhase('otp');
      }

      async function verifyOtp() {
        clearError();
        const otp = (document.getElementById('otp-input').value || '').trim();
        if (!otp) return showError('Enter the OTP code');

        setPhase('loading');
        gateTitle.textContent = 'Verifying';
        gateSubtitle.textContent = 'Unlocking institutional record…';

        const res = await fetch('api/otp_verify.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ uetr: currentUetr, email: currentEmail, otp })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) {
          setPhase('otp');
          return showError(data.message || 'Verification failed');
        }

        hideModal();
        renderPayload(data);
      }

      document.getElementById('trackBtn').addEventListener('click', startTrack);
      document.getElementById('transaction-input').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); startTrack(); }
      });
      document.getElementById('sendOtpBtn').addEventListener('click', sendOtp);
      document.getElementById('verifyOtpBtn').addEventListener('click', verifyOtp);
      document.getElementById('gate-close').addEventListener('click', hideModal);
    })();
  </script>
</body>
</html>
