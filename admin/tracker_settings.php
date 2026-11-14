<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_auth.php';
require_admin_login();
?>
<!doctype html>
<html lang="en">
<head>
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex"/>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tracker settings | Elysium Flasher</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    .material-symbols-outlined {
      font-family: 'Material Symbols Outlined';
      font-weight: normal;
      font-style: normal;
      line-height: 1;
      display: inline-block;
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
  </style>
</head>
<body class="min-h-screen bg-[#f5f5f5] p-6">
  <div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
      <a href="/admin/index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:underline">
        <span class="material-symbols-outlined" style="font-size:18px;">arrow_back</span> Back
      </a>
      <h1 class="text-2xl font-semibold text-slate-900">Institutional Tracker settings</h1>
    </div>

    <div id="messageBox" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm"></div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <h2 class="text-lg font-semibold text-slate-900 mb-2">Bolt Switch Trace connection</h2>
      <p class="text-sm text-slate-600 mb-4">On the Tracer admin, add this website URL, click <strong>Setup</strong>, then paste the generated values here. Save and test connection.</p>
      <div class="grid grid-cols-1 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Tracker URL</label>
          <input id="bstTrackerUrl" placeholder="https://your-tracer.example.com" class="w-full border border-slate-200 rounded-xl px-3 py-2 font-mono text-xs" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Integration ID</label>
          <input id="bstIntegrationId" class="w-full border border-slate-200 rounded-xl px-3 py-2 font-mono text-xs" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Client ID</label>
          <input id="bstClientId" class="w-full border border-slate-200 rounded-xl px-3 py-2 font-mono text-xs" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Client secret</label>
          <input id="bstClientSecret" type="password" autocomplete="off" class="w-full border border-slate-200 rounded-xl px-3 py-2 font-mono text-xs" placeholder="Paste secret from Tracer Setup" />
        </div>
      </div>
      <div class="flex flex-wrap gap-3 justify-end mt-5">
        <button id="testHubBtn" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm">Test connection</button>
        <button id="saveHubBtn" class="px-5 py-2 rounded-xl bg-[#0a1f44] text-white font-semibold text-sm">Save connection</button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <h2 class="text-lg font-semibold text-slate-900 mb-2">SMTP (OTP email)</h2>
      <p class="text-sm text-slate-600 mb-4">Used to email 2FA codes when visitors unlock a UETR on Bolt Switch Trace. Leave host empty to fall back to PHP mail().</p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Host</label>
          <input id="smtpHost" class="w-full border border-slate-200 rounded-xl px-3 py-2" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Port</label>
          <input id="smtpPort" class="w-full border border-slate-200 rounded-xl px-3 py-2" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Username</label>
          <input id="smtpUser" class="w-full border border-slate-200 rounded-xl px-3 py-2" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
          <input id="smtpPass" type="password" class="w-full border border-slate-200 rounded-xl px-3 py-2" placeholder="Leave blank to keep" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Encryption</label>
          <select id="smtpEnc" class="w-full border border-slate-200 rounded-xl px-3 py-2">
            <option value="tls">TLS</option>
            <option value="ssl">SSL</option>
            <option value="none">None</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">From email</label>
          <input id="smtpFromEmail" class="w-full border border-slate-200 rounded-xl px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-slate-600 mb-1">From name</label>
          <input id="smtpFromName" class="w-full border border-slate-200 rounded-xl px-3 py-2" />
        </div>
      </div>
      <div class="flex flex-wrap gap-3 justify-end mt-5">
        <input id="testEmail" type="email" placeholder="test@example.com" class="border border-slate-200 rounded-xl px-3 py-2 text-sm" />
        <button id="testMailBtn" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm">Send test</button>
        <button id="saveBtn" class="px-5 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold text-sm">Save SMTP</button>
      </div>
    </div>

    <details class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <summary class="cursor-pointer text-sm font-semibold text-slate-700">Legacy local API key (optional fallback)</summary>
      <p class="text-sm text-slate-600 mt-3 mb-3">Only used if Hub client secret is empty. Prefer Bolt Switch Trace Setup credentials above.</p>
      <div class="flex gap-2 items-center">
        <input id="apiKey" readonly class="flex-1 border border-slate-200 rounded-xl px-3 py-2 font-mono text-xs bg-slate-50" />
        <button id="copyKeyBtn" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm">Copy</button>
        <button id="rotateKeyBtn" class="px-4 py-2 rounded-xl bg-slate-800 text-white font-semibold text-sm">Rotate</button>
      </div>
    </details>
  </div>

  <script>
    (function () {
      const box = document.getElementById('messageBox');
      function show(msg, type) {
        box.classList.remove('hidden');
        box.className = type === 'success'
          ? 'mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800'
          : 'mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800';
        box.textContent = msg || '';
      }

      function apply(data) {
        document.getElementById('apiKey').value = data.tracker_api_key || '';
        const b = data.bst || {};
        document.getElementById('bstTrackerUrl').value = b.tracker_url || '';
        document.getElementById('bstIntegrationId').value = b.integration_id || '';
        document.getElementById('bstClientId').value = b.client_id || '';
        document.getElementById('bstClientSecret').value = '';
        document.getElementById('bstClientSecret').placeholder = b.client_secret_set ? '******** (unchanged)' : 'Paste secret from Tracer Setup';
        const s = data.smtp || {};
        document.getElementById('smtpHost').value = s.host || '';
        document.getElementById('smtpPort').value = s.port || 587;
        document.getElementById('smtpUser').value = s.user || '';
        document.getElementById('smtpPass').value = '';
        document.getElementById('smtpPass').placeholder = s.pass_set ? '******** (unchanged)' : 'Enter password';
        document.getElementById('smtpEnc').value = s.encryption || 'tls';
        document.getElementById('smtpFromEmail').value = s.from_email || '';
        document.getElementById('smtpFromName').value = s.from_name || '';
      }

      async function load() {
        const res = await fetch('/api/tracker_settings.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load');
        apply(data);
      }

      document.getElementById('saveHubBtn').addEventListener('click', async () => {
        const body = {
          bst: {
            tracker_url: document.getElementById('bstTrackerUrl').value.trim(),
            integration_id: document.getElementById('bstIntegrationId').value.trim(),
            client_id: document.getElementById('bstClientId').value.trim(),
          }
        };
        const secret = document.getElementById('bstClientSecret').value;
        if (secret) body.bst.client_secret = secret;
        const res = await fetch('/api/tracker_settings.php', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body)
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) return show(data.message || 'Save failed', 'error');
        apply(data);
        show('Connection settings saved', 'success');
      });

      document.getElementById('testHubBtn').addEventListener('click', async () => {
        const res = await fetch('/api/tracker_settings.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'test_hub' })
        });
        const data = await res.json().catch(() => ({}));
        show(data.message || (data.ok ? 'Connected' : 'Failed'), data.ok ? 'success' : 'error');
      });

      document.getElementById('copyKeyBtn').addEventListener('click', async () => {
        try {
          await navigator.clipboard.writeText(document.getElementById('apiKey').value);
          show('Legacy API key copied', 'success');
        } catch (e) {
          show('Copy failed', 'error');
        }
      });

      document.getElementById('rotateKeyBtn').addEventListener('click', async () => {
        if (!confirm('Rotate legacy local API key? Prefer Hub credentials from Bolt Switch Trace.')) return;
        const res = await fetch('/api/tracker_settings.php', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ rotate_api_key: true })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) return show(data.message || 'Rotate failed', 'error');
        apply(data);
        show('Legacy API key rotated', 'success');
      });

      document.getElementById('saveBtn').addEventListener('click', async () => {
        const body = {
          smtp: {
            host: document.getElementById('smtpHost').value,
            port: document.getElementById('smtpPort').value,
            user: document.getElementById('smtpUser').value,
            encryption: document.getElementById('smtpEnc').value,
            from_email: document.getElementById('smtpFromEmail').value,
            from_name: document.getElementById('smtpFromName').value,
          }
        };
        const pass = document.getElementById('smtpPass').value;
        if (pass) body.smtp.pass = pass;
        const res = await fetch('/api/tracker_settings.php', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body)
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) return show(data.message || 'Save failed', 'error');
        apply(data);
        show('SMTP settings saved', 'success');
      });

      document.getElementById('testMailBtn').addEventListener('click', async () => {
        const email = document.getElementById('testEmail').value;
        const res = await fetch('/api/tracker_settings.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'test_mail', email })
        });
        const data = await res.json().catch(() => ({}));
        show(data.message || (res.ok ? 'Sent' : 'Failed'), res.ok && data.success ? 'success' : 'error');
      });

      load().catch((e) => show(e.message || 'Load failed', 'error'));
    })();
  </script>
</body>
</html>
