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
  <title>Wallet PIN | Elysium Server</title>
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
  <div class="max-w-xl mx-auto">
    <div class="flex items-center justify-between gap-4 mb-6">
      <a href="/admin/index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:underline">
        <span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle;">arrow_back</span>
        Back
      </a>
      <h1 class="text-2xl font-semibold text-slate-900">Wallet PIN</h1>
    </div>

    <div id="messageBox" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm"></div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">
      <div class="flex items-center justify-between gap-4 mb-6">
        <div>
          <div class="text-sm font-semibold text-slate-700">Status</div>
          <p id="pinStatus" class="text-lg font-semibold text-slate-900 mt-1">Loading…</p>
        </div>
        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center">
          <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">key</span>
        </div>
      </div>

      <form id="pinForm" class="flex flex-col gap-4">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="newPin">New PIN</label>
          <input id="newPin" type="password" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="new-password" placeholder="6 digits" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 tracking-[0.3em] text-center font-mono"/>
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="confirmPin">Confirm PIN</label>
          <input id="confirmPin" type="password" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="new-password" placeholder="6 digits" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 tracking-[0.3em] text-center font-mono"/>
        </div>
        <button type="submit" id="saveBtn" class="w-full h-11 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors disabled:opacity-50">
          Save PIN
        </button>
      </form>
      <p class="text-xs text-slate-500 mt-4">The PIN is stored as a one-way hash. The current PIN is never shown or retrieved.</p>
    </div>
  </div>

  <script>
  (function () {
    const statusEl = document.getElementById('pinStatus');
    const messageBox = document.getElementById('messageBox');
    const form = document.getElementById('pinForm');
    const newPin = document.getElementById('newPin');
    const confirmPin = document.getElementById('confirmPin');
    const saveBtn = document.getElementById('saveBtn');

    function showMessage(text, isError) {
      messageBox.textContent = text;
      messageBox.className = isError
        ? 'mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800'
        : 'mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800';
      messageBox.classList.remove('hidden');
    }

    function digitsOnly(el) {
      el.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
      });
    }
    digitsOnly(newPin);
    digitsOnly(confirmPin);

    async function loadStatus() {
      try {
        const res = await fetch('/api/wallet_pin.php');
        const data = await res.json().catch(() => ({}));
        if (res.status === 401 && data.redirect) {
          window.location.href = data.redirect;
          return;
        }
        if (!res.ok || !data.success) {
          statusEl.textContent = 'Unknown';
          return;
        }
        statusEl.textContent = data.configured ? 'Configured' : 'Not configured';
        statusEl.className = data.configured
          ? 'text-lg font-semibold text-emerald-700 mt-1'
          : 'text-lg font-semibold text-amber-700 mt-1';
      } catch (e) {
        statusEl.textContent = 'Unknown';
      }
    }

    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      messageBox.classList.add('hidden');
      const pin = (newPin.value || '').trim();
      const confirm = (confirmPin.value || '').trim();
      if (!/^\d{6}$/.test(pin)) {
        showMessage('PIN must be exactly 6 digits.', true);
        return;
      }
      if (pin !== confirm) {
        showMessage('PIN confirmation does not match.', true);
        return;
      }
      saveBtn.disabled = true;
      try {
        const res = await fetch('/api/wallet_pin.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'save', pin: pin, confirm_pin: confirm })
        });
        const data = await res.json().catch(() => ({}));
        if (res.status === 401 && data.redirect) {
          window.location.href = data.redirect;
          return;
        }
        if (!res.ok || !data.success) {
          throw new Error(data.message || 'Failed to save PIN');
        }
        newPin.value = '';
        confirmPin.value = '';
        showMessage('Wallet PIN saved successfully.', false);
        await loadStatus();
      } catch (err) {
        showMessage(err.message || 'Failed to save PIN', true);
      } finally {
        saveBtn.disabled = false;
      }
    });

    loadStatus();
  })();
  </script>
</body>
</html>
