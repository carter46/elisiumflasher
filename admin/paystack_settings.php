<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_auth.php';
require_admin_login();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment &amp; account resolve | Elysium Server</title>
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
      white-space: nowrap;
      -webkit-font-smoothing: antialiased;
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
  </style>
</head>
<body class="min-h-screen bg-[#f5f5f5] p-6">
  <div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
      <a href="/admin/index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:underline">
        <span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle;">arrow_back</span>
        Back
      </a>
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">Payment &amp; account resolve</h1>
        <p class="text-sm text-slate-600 mt-1">Paystack and Flutterwave keys for Nigerian bank account name lookup. Test/Live follows the toggle below.</p>
      </div>
    </div>

    <div id="messageBox" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm"></div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-6">
      <div class="text-sm font-semibold text-slate-900 mb-4">Account name resolution (Nigeria)</div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/80 cursor-pointer">
          <input id="paystackResolveToggle" type="checkbox" class="h-5 w-5 mt-0.5">
          <span>
            <span class="block font-semibold text-slate-900">Enable Paystack resolve</span>
            <span class="block text-xs text-slate-600 mt-1">Use Paystack when verifying 10-digit accounts (tried first if Flutterwave is also on).</span>
          </span>
        </label>
        <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/80 cursor-pointer">
          <input id="flutterwaveResolveToggle" type="checkbox" class="h-5 w-5 mt-0.5">
          <span>
            <span class="block font-semibold text-slate-900">Enable Flutterwave resolve</span>
            <span class="block text-xs text-slate-600 mt-1">Used if Paystack is off or fails. Requires Flutterwave secret keys.</span>
          </span>
        </label>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">
      <p class="text-xs text-slate-600 mb-4 rounded-lg bg-slate-50 border border-slate-100 px-3 py-2">
        Public keys reload in full after save. Secret keys stay on the server; empty fields on Save keep the existing secret. Use <strong>Test</strong> with an empty secret field to verify the saved key.
      </p>
      <div id="paystackSection" class="hidden">
        <div class="text-sm font-semibold text-slate-900 mb-4">Paystack</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Test Public Key</label>
              <input id="testPublic" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" placeholder="pk_test_..." autocomplete="off">
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Test Secret Key</label>
              <p id="testSecretHint" class="text-xs text-slate-500 mb-1.5 min-h-[1rem]"></p>
              <div class="flex gap-3">
                <input id="testSecret" type="password" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" placeholder="Leave blank to keep saved key" autocomplete="new-password">
                <button type="button" id="testTestSecretBtn" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm shrink-0">Test</button>
              </div>
              <div id="testSecretStatus" class="text-xs text-slate-500 mt-2"></div>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Live Public Key</label>
              <input id="livePublic" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" placeholder="pk_live_..." autocomplete="off">
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Live Secret Key</label>
              <p id="liveSecretHint" class="text-xs text-slate-500 mb-1.5 min-h-[1rem]"></p>
              <div class="flex gap-3">
                <input id="liveSecret" type="password" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" placeholder="Leave blank to keep saved key" autocomplete="new-password">
                <button type="button" id="testLiveSecretBtn" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm shrink-0">Test</button>
              </div>
              <div id="liveSecretStatus" class="text-xs text-slate-500 mt-2"></div>
            </div>
          </div>
        </div>
      </div>

      <div id="environmentSection" class="hidden mt-6 p-4 bg-slate-50 rounded-xl flex items-center justify-between gap-4 flex-wrap">
        <div>
          <div class="text-sm font-semibold text-slate-900">Use Live Environment</div>
          <div id="useLiveHint" class="text-xs text-slate-600 mt-1"></div>
        </div>
        <label class="inline-flex items-center gap-3 cursor-pointer">
          <input id="useLiveToggle" type="checkbox" class="h-5 w-5">
          <span class="text-sm font-semibold text-slate-800">Use Live</span>
        </label>
      </div>

      <div id="flutterwaveSection" class="hidden mt-8 pt-8 border-t border-slate-100">
        <div class="text-sm font-semibold text-slate-900 mb-4">Flutterwave</div>
        <p class="text-xs text-slate-500 mb-4">Public keys are for reference or future client-side use; account resolve on the server uses the secret key only.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Test Public Key</label>
              <input id="fwTestPublic" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" placeholder="FLWPUBK_TEST-...">
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Test Secret Key</label>
              <p id="fwTestSecretHint" class="text-xs text-slate-500 mb-1.5 min-h-[1rem]"></p>
              <div class="flex gap-3">
                <input id="fwTestSecret" type="password" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" placeholder="Leave blank to keep saved key" autocomplete="new-password">
                <button id="testFwTestBtn" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm shrink-0">Test</button>
              </div>
              <div id="fwTestSecretStatus" class="text-xs text-slate-500 mt-2"></div>
            </div>
          </div>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Live Public Key</label>
              <input id="fwLivePublic" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" placeholder="FLWPUBK-...">
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Live Secret Key</label>
              <p id="fwLiveSecretHint" class="text-xs text-slate-500 mb-1.5 min-h-[1rem]"></p>
              <div class="flex gap-3">
                <input id="fwLiveSecret" type="password" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" placeholder="Leave blank to keep saved key" autocomplete="new-password">
                <button id="testFwLiveBtn" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm shrink-0">Test</button>
              </div>
              <div id="fwLiveSecretStatus" class="text-xs text-slate-500 mt-2"></div>
            </div>
          </div>
        </div>
        <p class="text-xs text-slate-500 mt-3">The Test/Live switch above selects which Flutterwave keys are active for resolve.</p>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <a href="/admin/index.php" class="px-5 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm">Cancel</a>
        <button id="saveBtn" class="px-5 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold text-sm">Save</button>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const messageBox = document.getElementById('messageBox');
      const setMessage = (text, type) => {
        messageBox.textContent = text || '';
        messageBox.className = type === 'success'
          ? 'mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 block'
          : 'mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 block';
        messageBox.classList.remove('hidden');
      };

      const testPublic = document.getElementById('testPublic');
      const testSecret = document.getElementById('testSecret');
      const livePublic = document.getElementById('livePublic');
      const liveSecret = document.getElementById('liveSecret');

      const testSecretStatus = document.getElementById('testSecretStatus');
      const liveSecretStatus = document.getElementById('liveSecretStatus');

      const useLiveToggle = document.getElementById('useLiveToggle');
      const useLiveHint = document.getElementById('useLiveHint');
      const paystackResolveToggle = document.getElementById('paystackResolveToggle');
      const flutterwaveResolveToggle = document.getElementById('flutterwaveResolveToggle');
      const fwTestPublic = document.getElementById('fwTestPublic');
      const fwLivePublic = document.getElementById('fwLivePublic');
      const fwTestSecret = document.getElementById('fwTestSecret');
      const fwLiveSecret = document.getElementById('fwLiveSecret');
      const fwTestSecretStatus = document.getElementById('fwTestSecretStatus');
      const fwLiveSecretStatus = document.getElementById('fwLiveSecretStatus');

      const testSecretHint = document.getElementById('testSecretHint');
      const liveSecretHint = document.getElementById('liveSecretHint');
      const fwTestSecretHint = document.getElementById('fwTestSecretHint');
      const fwLiveSecretHint = document.getElementById('fwLiveSecretHint');

      let hasPaystackTest = false;
      let hasPaystackLive = false;
      let hasFwTest = false;
      let hasFwLive = false;

      const paystackSection = document.getElementById('paystackSection');
      const environmentSection = document.getElementById('environmentSection');
      const flutterwaveSection = document.getElementById('flutterwaveSection');

      function syncProviderSectionsVisibility() {
        const ps = paystackResolveToggle.checked;
        const fw = flutterwaveResolveToggle.checked;
        paystackSection.classList.toggle('hidden', !ps);
        flutterwaveSection.classList.toggle('hidden', !fw);
        environmentSection.classList.toggle('hidden', !ps && !fw);
      }

      const updateHint = () => {
        useLiveHint.textContent = useLiveToggle.checked
          ? 'Currently using Live API keys for Paystack and Flutterwave account verification'
          : 'Currently using Test API keys for Paystack and Flutterwave account verification';
      };

      updateHint();
      useLiveToggle.addEventListener('change', updateHint);
      paystackResolveToggle.addEventListener('change', syncProviderSectionsVisibility);
      flutterwaveResolveToggle.addEventListener('change', syncProviderSectionsVisibility);

      const apiFetch = (url, options = {}) =>
        fetch(url, { credentials: 'include', ...options });

      function redirectIfAdmin401(res, data) {
        if (res.status === 401 && data && data.redirect) {
          window.location.href = data.redirect;
          return true;
        }
        return false;
      }

      function setSecretHint(el, has, masked) {
        if (!el) return;
        if (has && masked) {
          el.textContent = 'Saved on server: ' + masked;
          el.className = 'text-xs text-emerald-800 font-medium mb-1.5 min-h-[1rem]';
        } else if (has) {
          el.textContent = 'Saved on server (secret stored)';
          el.className = 'text-xs text-emerald-800 font-medium mb-1.5 min-h-[1rem]';
        } else {
          el.textContent = 'No secret saved yet — paste a key and click Save.';
          el.className = 'text-xs text-slate-500 mb-1.5 min-h-[1rem]';
        }
      }

      async function loadSettings() {
        const res = await apiFetch('/api/paystack_settings.php');
        const data = await res.json().catch(() => ({}));
        if (redirectIfAdmin401(res, data)) return;
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load payment settings');

        testPublic.value = data.test_public_key || '';
        testSecret.value = '';
        livePublic.value = data.live_public_key || '';
        liveSecret.value = '';

        useLiveToggle.checked = !!data.use_live;
        paystackResolveToggle.checked = data.paystack_resolve_enabled !== false;
        flutterwaveResolveToggle.checked = !!data.flutterwave_resolve_enabled;
        fwTestPublic.value = data.flutterwave_test_public_key || '';
        fwLivePublic.value = data.flutterwave_live_public_key || '';
        fwTestSecret.value = '';
        fwLiveSecret.value = '';

        hasPaystackTest = !!data.has_paystack_test_secret;
        hasPaystackLive = !!data.has_paystack_live_secret;
        hasFwTest = !!data.has_flutterwave_test_secret;
        hasFwLive = !!data.has_flutterwave_live_secret;

        setSecretHint(testSecretHint, hasPaystackTest, data.paystack_test_secret_masked);
        setSecretHint(liveSecretHint, hasPaystackLive, data.paystack_live_secret_masked);
        setSecretHint(fwTestSecretHint, hasFwTest, data.flutterwave_test_secret_masked);
        setSecretHint(fwLiveSecretHint, hasFwLive, data.flutterwave_live_secret_masked);

        updateHint();
        syncProviderSectionsVisibility();
      }

      async function postKeyTest(body, statusEl) {
        statusEl.textContent = 'Testing…';
        statusEl.className = 'text-xs text-slate-500 mt-2';

        const res = await apiFetch('/api/paystack_settings.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(body)
        });
        const data = await res.json().catch(() => ({}));
        if (redirectIfAdmin401(res, data)) return;
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to test key');

        const ok = !!data.valid;
        statusEl.textContent = data.message || (ok ? 'Valid key' : 'Invalid key');
        statusEl.className = 'text-xs mt-2 ' + (ok ? 'text-green-700 font-medium' : 'text-red-700 font-medium');
      }

      async function runPaystackTest(which) {
        const typed = which === 'test' ? testSecret.value.trim() : liveSecret.value.trim();
        const statusEl = which === 'test' ? testSecretStatus : liveSecretStatus;
        const slot = which === 'test' ? 'paystack_test' : 'paystack_live';
        const has = which === 'test' ? hasPaystackTest : hasPaystackLive;

        if (!typed && !has) {
          throw new Error('No saved ' + (which === 'test' ? 'Test' : 'Live') + ' secret. Paste a key and Save, or type a key to test.');
        }
        const body = typed
          ? { provider: 'paystack', api_key: typed }
          : { provider: 'paystack', use_stored_secret: true, secret_slot: slot };
        await postKeyTest(body, statusEl);
      }

      async function runFlutterwaveTest(which) {
        const typed = which === 'test' ? fwTestSecret.value.trim() : fwLiveSecret.value.trim();
        const statusEl = which === 'test' ? fwTestSecretStatus : fwLiveSecretStatus;
        const slot = which === 'test' ? 'flutterwave_test' : 'flutterwave_live';
        const has = which === 'test' ? hasFwTest : hasFwLive;

        if (!typed && !has) {
          throw new Error('No saved ' + (which === 'test' ? 'Test' : 'Live') + ' secret. Paste a key and Save, or type a key to test.');
        }
        const body = typed
          ? { provider: 'flutterwave', api_key: typed }
          : { provider: 'flutterwave', use_stored_secret: true, secret_slot: slot };
        await postKeyTest(body, statusEl);
      }

      document.getElementById('testTestSecretBtn').addEventListener('click', async () => {
        try {
          await runPaystackTest('test');
        } catch (err) {
          testSecretStatus.textContent = err.message || 'Failed to test key';
          testSecretStatus.className = 'text-xs mt-2 text-red-700';
        }
      });

      document.getElementById('testLiveSecretBtn').addEventListener('click', async () => {
        try {
          await runPaystackTest('live');
        } catch (err) {
          liveSecretStatus.textContent = err.message || 'Failed to test key';
          liveSecretStatus.className = 'text-xs mt-2 text-red-700';
        }
      });

      document.getElementById('testFwTestBtn').addEventListener('click', async () => {
        try {
          await runFlutterwaveTest('test');
        } catch (err) {
          fwTestSecretStatus.textContent = err.message || 'Failed to test key';
          fwTestSecretStatus.className = 'text-xs mt-2 text-red-700';
        }
      });

      document.getElementById('testFwLiveBtn').addEventListener('click', async () => {
        try {
          await runFlutterwaveTest('live');
        } catch (err) {
          fwLiveSecretStatus.textContent = err.message || 'Failed to test key';
          fwLiveSecretStatus.className = 'text-xs mt-2 text-red-700';
        }
      });

      document.getElementById('saveBtn').addEventListener('click', async () => {
        try {
          const payload = {
            use_live: useLiveToggle.checked ? 1 : 0,
            paystack_resolve_enabled: paystackResolveToggle.checked ? 1 : 0,
            flutterwave_resolve_enabled: flutterwaveResolveToggle.checked ? 1 : 0
          };
          const tp = testPublic.value.trim();
          const ts = testSecret.value.trim();
          const lp = livePublic.value.trim();
          const ls = liveSecret.value.trim();
          const fts = fwTestSecret.value.trim();
          const fls = fwLiveSecret.value.trim();
          const ftp = fwTestPublic.value.trim();
          const flp = fwLivePublic.value.trim();

          if (tp !== '') payload.test_public_key = tp;
          if (ts !== '') payload.test_secret_key = ts;
          if (lp !== '') payload.live_public_key = lp;
          if (ls !== '') payload.live_secret_key = ls;
          if (ftp !== '') payload.flutterwave_test_public_key = ftp;
          if (flp !== '') payload.flutterwave_live_public_key = flp;
          if (fts !== '') payload.flutterwave_test_secret_key = fts;
          if (fls !== '') payload.flutterwave_live_secret_key = fls;

          const res = await apiFetch('/api/paystack_settings.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
          });
          const data = await res.json().catch(() => ({}));
          if (redirectIfAdmin401(res, data)) return;
          if (!res.ok || !data.success) throw new Error(data.message || 'Failed to save payment settings');
          setMessage('Payment settings updated successfully!', 'success');
          await loadSettings();
        } catch (err) {
          setMessage(err.message || 'Failed to save payment settings', 'error');
        }
      });

      (async () => {
        try {
          await loadSettings();
        } catch (err) {
          setMessage(err.message || 'Failed to load payment settings', 'error');
        }
      })();
    })();
  </script>
</body>
</html>

