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
  <title>Bank status | Elysium Server</title>
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
      <h1 class="text-2xl font-semibold text-slate-900">Bank status</h1>
    </div>

    <div id="messageBox" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm"></div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-slate-700">priority_high</span>
            <h2 class="text-lg font-semibold text-slate-900">Platform Status</h2>
          </div>
          <p class="text-sm text-slate-600 max-w-xl">
            When OFF, users cannot continue to local transfer. Separate from link wallet and transfer outcome settings below.
          </p>
          <p id="platformSummary" class="text-xs text-slate-500 mt-2"></p>
        </div>

        <div class="min-w-[240px]">
          <label class="block text-sm font-semibold text-slate-700 mb-2">Platform Status</label>
          <select id="platformStatus" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200">
            <option value="on">On</option>
            <option value="off">Off</option>
          </select>
        </div>
      </div>
      <div class="flex justify-end mt-5">
        <button id="savePlatformBtn" class="px-5 py-2 rounded-xl bg-[#0a1f44] text-white font-semibold">Save Platform</button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <div class="flex items-center gap-2 mb-2">
        <span class="material-symbols-outlined text-slate-700">account_balance</span>
        <h2 class="text-lg font-semibold text-slate-900">Local transfer bank status</h2>
      </div>
      <p class="text-sm text-slate-600 mb-6 max-w-2xl">
        These settings apply to <strong>any</strong> bank used for local transfers — not a single bank corridor.
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/60">
          <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-slate-700 text-[20px]">link</span>
            <h3 class="text-sm font-semibold text-slate-900">Link wallet status</h3>
          </div>
          <p class="text-xs text-slate-600 mb-3">
            When Off, Sync Account fails with a link-wallet error for every bank.
          </p>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
          <select id="linkWalletStatus" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-white outline-none focus:ring-4 focus:ring-indigo-200">
            <option value="on">On</option>
            <option value="off">Off</option>
          </select>
          <p id="linkWalletSummary" class="text-xs text-slate-500 mt-2"></p>
        </div>

        <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/60">
          <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-slate-700 text-[20px]">swap_horiz</span>
            <h3 class="text-sm font-semibold text-slate-900">Transfer status</h3>
          </div>
          <p class="text-xs text-slate-600 mb-3">
            Controls the recorded outcome for transfers on any bank. Successful keeps the current debit flow.
          </p>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Outcome</label>
          <select id="transferStatus" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-white outline-none focus:ring-4 focus:ring-indigo-200">
            <option value="successful">Successful</option>
            <option value="failed">Failed</option>
            <option value="pending">Pending</option>
            <option value="reversed">Reversed</option>
          </select>
          <p id="transferSummary" class="text-xs text-slate-500 mt-2"></p>
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <a href="/admin/index.php" class="px-5 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm">Cancel</a>
        <button id="saveLocalBtn" class="px-5 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold text-sm">Save local status</button>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const messageBox = document.getElementById('messageBox');
      const platformStatusEl = document.getElementById('platformStatus');
      const platformSummary = document.getElementById('platformSummary');
      const linkWalletStatusEl = document.getElementById('linkWalletStatus');
      const transferStatusEl = document.getElementById('transferStatus');
      const linkWalletSummary = document.getElementById('linkWalletSummary');
      const transferSummary = document.getElementById('transferSummary');

      function showMessage(text, type) {
        messageBox.classList.remove('hidden');
        if (type === 'success') {
          messageBox.className = 'mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800';
        } else {
          messageBox.className = 'mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800';
        }
        messageBox.textContent = text || '';
      }

      function statusSummary() {
        platformSummary.textContent = platformStatusEl.value === 'on'
          ? 'Platform is available for local transfers'
          : 'Platform is under maintenance (all transfers blocked)';
      }

      function localSummaries() {
        linkWalletSummary.textContent = linkWalletStatusEl.value === 'on'
          ? 'Accounts can be synced / linked for any bank.'
          : 'Link wallet is blocked for every bank.';
        const map = {
          successful: 'Transfers complete as Successful and debit balance.',
          failed: 'Transfers are recorded as Failed (no debit).',
          pending: 'Transfers are recorded as Pending (no debit).',
          reversed: 'Transfers are recorded as Reversed (no debit).',
        };
        transferSummary.textContent = map[transferStatusEl.value] || '';
      }

      async function loadPlatformStatus() {
        const res = await fetch('/api/platform_status.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load platform status');
        platformStatusEl.value = data.status || 'on';
        statusSummary();
      }

      async function loadLocalStatus() {
        const res = await fetch('/api/local_transfer_status.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load local transfer status');
        linkWalletStatusEl.value = data.link_wallet_status || 'on';
        transferStatusEl.value = data.transfer_status || 'successful';
        localSummaries();
      }

      async function savePlatform() {
        const res = await fetch('/api/platform_status.php', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ status: platformStatusEl.value })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to update platform');
        showMessage('Platform status updated successfully!', 'success');
        statusSummary();
      }

      async function saveLocal() {
        const res = await fetch('/api/local_transfer_status.php', {
          method: 'PUT',
          cache: 'no-store',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            link_wallet_status: linkWalletStatusEl.value,
            transfer_status: transferStatusEl.value
          })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to update local status');
        linkWalletStatusEl.value = data.link_wallet_status || linkWalletStatusEl.value;
        transferStatusEl.value = data.transfer_status || transferStatusEl.value;
        showMessage(
          'Local status saved. Link wallet: ' + linkWalletStatusEl.value + ', Transfer: ' + transferStatusEl.value,
          'success'
        );
        localSummaries();
      }

      document.getElementById('savePlatformBtn').addEventListener('click', async () => {
        try {
          showMessage('Saving...', 'success');
          await savePlatform();
        } catch (err) {
          showMessage(err.message || 'Failed to save platform status', 'error');
        }
      });

      document.getElementById('saveLocalBtn').addEventListener('click', async () => {
        try {
          showMessage('Saving...', 'success');
          await saveLocal();
        } catch (err) {
          showMessage(err.message || 'Failed to save local status', 'error');
        }
      });

      platformStatusEl.addEventListener('change', statusSummary);
      linkWalletStatusEl.addEventListener('change', localSummaries);
      transferStatusEl.addEventListener('change', localSummaries);

      (async () => {
        try {
          await loadPlatformStatus();
          await loadLocalStatus();
        } catch (err) {
          showMessage(err.message || 'Failed to load status data', 'error');
        }
      })();
    })();
  </script>
</body>
</html>
