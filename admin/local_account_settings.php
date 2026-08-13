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
  <title>Account settings | Elysium Server</title>
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
    <div class="flex items-center justify-between gap-4 mb-6">
      <a href="/admin/index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:underline">
        <span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle;">arrow_back</span>
        Back
      </a>
      <h1 class="text-2xl font-semibold text-slate-900">Account settings</h1>
    </div>

    <div id="messageBox" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm"></div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <div class="mb-4">
        <div class="flex items-center justify-between gap-4">
          <div>
            <div class="text-sm font-semibold text-slate-700 mb-1">Account Name</div>
            <p id="accountNameDisplay" class="text-lg font-semibold text-slate-900"></p>
          </div>
          <button id="editNameBtn" class="px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-semibold">Edit</button>
        </div>
        <div id="accountNameEdit" class="hidden mt-3 flex items-center gap-3">
          <input id="accountNameInput" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
          <button id="saveNameBtn" class="px-4 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold">Save</button>
          <button id="cancelNameBtn" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold">Cancel</button>
        </div>
      </div>

      <div class="mb-4">
        <div class="flex items-center justify-between gap-4">
          <div>
            <div class="text-sm font-semibold text-slate-700 mb-1">Account Number</div>
            <p id="accountNumberDisplay" class="text-lg font-semibold text-slate-900"></p>
          </div>
          <button id="editNumberBtn" class="px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-semibold">Edit</button>
        </div>
        <div id="accountNumberEdit" class="hidden mt-3 flex items-center gap-3">
          <input id="accountNumberInput" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
          <button id="saveNumberBtn" class="px-4 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold">Save</button>
          <button id="cancelNumberBtn" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold">Cancel</button>
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between gap-4">
          <div>
            <div class="text-sm font-semibold text-slate-700 mb-1">Total balance (NGN)</div>
            <p id="balanceDisplay" class="text-lg font-semibold text-slate-900"></p>
          </div>
          <button id="editBalanceBtn" class="px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-semibold">Edit</button>
        </div>
        <div id="balanceEdit" class="hidden mt-3 flex items-center gap-3">
          <input id="balanceInput" type="number" step="0.01" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
          <button id="saveBalanceBtn" class="px-4 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold">Save</button>
          <button id="cancelBalanceBtn" class="px-4 py-2 rounded-xl border border-slate-200 bg-white font-semibold">Cancel</button>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">
      <div class="flex items-center justify-between gap-4 mb-4">
        <h2 class="text-xl font-semibold text-slate-900">Transaction History</h2>
        <div id="txCount" class="text-sm text-slate-600"></div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
          <thead>
            <tr class="border-b">
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Date</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Reference</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Amount</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Beneficiary</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Bank</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Status</th>
              <th class="text-right py-3 font-semibold text-slate-700 px-2">Delete</th>
            </tr>
          </thead>
          <tbody id="txBody"></tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const messageBox = document.getElementById('messageBox');
      function showMessage(text, type) {
        messageBox.classList.remove('hidden');
        messageBox.textContent = text || '';
        if (type === 'success') {
          messageBox.className = 'mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800';
        } else {
          messageBox.className = 'mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800';
        }
      }
      function formatNGN(v) {
        const n = Number(v || 0);
        return 'NGN ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

      const accountNameDisplay = document.getElementById('accountNameDisplay');
      const accountNumberDisplay = document.getElementById('accountNumberDisplay');
      const balanceDisplay = document.getElementById('balanceDisplay');

      const editNameBtn = document.getElementById('editNameBtn');
      const editNumberBtn = document.getElementById('editNumberBtn');
      const editBalanceBtn = document.getElementById('editBalanceBtn');

      const accountNameEdit = document.getElementById('accountNameEdit');
      const accountNumberEdit = document.getElementById('accountNumberEdit');
      const balanceEdit = document.getElementById('balanceEdit');

      const accountNameInput = document.getElementById('accountNameInput');
      const accountNumberInput = document.getElementById('accountNumberInput');
      const balanceInput = document.getElementById('balanceInput');

      const txBody = document.getElementById('txBody');
      const txCount = document.getElementById('txCount');

      const saveNameBtn = document.getElementById('saveNameBtn');
      const cancelNameBtn = document.getElementById('cancelNameBtn');
      const saveNumberBtn = document.getElementById('saveNumberBtn');
      const cancelNumberBtn = document.getElementById('cancelNumberBtn');
      const saveBalanceBtn = document.getElementById('saveBalanceBtn');
      const cancelBalanceBtn = document.getElementById('cancelBalanceBtn');

      function setEditing() {
        editNameBtn.disabled = true;
        editNumberBtn.disabled = true;
        editBalanceBtn.disabled = true;
      }

      function setViewing() {
        editNameBtn.disabled = false;
        editNumberBtn.disabled = false;
        editBalanceBtn.disabled = false;
      }

      async function loadAccountData() {
        const res = await fetch('/api/local_account_settings.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load local account settings');

        accountNameDisplay.textContent = data.account.account_name || '';
        accountNumberDisplay.textContent = data.account.account_number || '';
        balanceDisplay.textContent = formatNGN(data.account.balance);
      }

      async function loadTransactions() {
        const res = await fetch('/api/local_transactions.php?limit=50&offset=0&_=' + Date.now(), { cache: 'no-store' });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load transactions');

        const txs = Array.isArray(data.transactions) ? data.transactions : [];
        txCount.textContent = 'Total Transactions: ' + txs.length;
        txBody.innerHTML = '';

        if (!txs.length) {
          txBody.innerHTML = '<tr><td colspan="7" class="py-10 text-center text-slate-600">No transactions found</td></tr>';
          return;
        }

        txs.forEach((t) => {
          const date = t.transaction_date ? new Date(t.transaction_date) : null;
          const dateStr = date ? date.toLocaleDateString() : '';
          const timeStr = date ? date.toLocaleTimeString() : '';

          const status = (t.status || '').toString().toUpperCase();
          const statuses = ['SUCCESSFUL', 'COMPLETED', 'PENDING', 'FAILED', 'REVERSED'];

          const tr = document.createElement('tr');
          tr.className = 'border-b';
          tr.innerHTML = `
            <td class="py-3 px-2">
              ${dateStr}<div class="text-xs text-slate-500">${timeStr}</div>
            </td>
            <td class="py-3 px-2 font-mono text-xs">${t.reference || ''}</td>
            <td class="py-3 px-2 font-semibold">${t.currency || 'NGN'} ${(Number(t.amount || 0)).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            <td class="py-3 px-2">${t.beneficiary_name || ''}</td>
            <td class="py-3 px-2">${t.beneficiary_bank || ''}</td>
            <td class="py-3 px-2">
              <select class="tx-status-select border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-semibold bg-white outline-none focus:ring-2 focus:ring-indigo-200" data-id="${t.id}">
                ${statuses.map((s) => `<option value="${s}" ${s === status ? 'selected' : ''}>${s}</option>`).join('')}
              </select>
            </td>
            <td class="py-3 px-2 text-right">
              <button type="button" class="tx-delete-btn px-3 py-2 rounded-xl border border-red-200 bg-white text-red-600 font-semibold text-sm" title="Delete Transaction">🗑</button>
            </td>
          `;

          const statusSelect = tr.querySelector('.tx-status-select');
          statusSelect.addEventListener('change', async () => {
            const next = statusSelect.value;
            if (!confirm('Change status to ' + next + '? Balance will adjust if needed.')) {
              statusSelect.value = status;
              return;
            }
            try {
              setEditing();
              statusSelect.disabled = true;
              const putRes = await fetch('/api/local_transactions.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: t.id, status: next }),
              });
              const put = await putRes.json().catch(() => ({}));
              if (!putRes.ok || !put.success) throw new Error(put.message || 'Failed to update status');
              showMessage('Transaction status updated to ' + next + '.', 'success');
              await loadAccountData();
              await loadTransactions();
            } catch (err) {
              statusSelect.value = status;
              showMessage(err.message || 'Failed to update status', 'error');
            } finally {
              statusSelect.disabled = false;
              setViewing();
            }
          });

          tr.querySelector('.tx-delete-btn').addEventListener('click', async () => {
            if (!confirm('Delete transaction? Balance will be restored if this status had deducted funds.')) return;
            try {
              setEditing();
              const delRes = await fetch('/api/local_transactions.php?id=' + encodeURIComponent(t.id), { method: 'DELETE' });
              const del = await delRes.json().catch(() => ({}));
              if (!delRes.ok || !del.success) throw new Error(del.message || 'Failed to delete transaction');
              showMessage(del.message || 'Transaction deleted successfully.', 'success');
              await loadAccountData();
              await loadTransactions();
            } catch (err) {
              showMessage(err.message || 'Failed to delete transaction', 'error');
            } finally {
              setViewing();
            }
          });
          txBody.appendChild(tr);
        });
      }

      function wireEditControls() {
        editNameBtn.addEventListener('click', () => {
          accountNameInput.value = accountNameDisplay.textContent.trim();
          accountNameEdit.classList.remove('hidden');
          accountNameDisplay.parentElement.classList.add('hidden');
        });
        cancelNameBtn.addEventListener('click', () => {
          accountNameEdit.classList.add('hidden');
          accountNameDisplay.parentElement.classList.remove('hidden');
        });
        saveNameBtn.addEventListener('click', async () => {
          try {
            setEditing();
            const res = await fetch('/api/local_account_settings.php', {
              method: 'PUT',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify({ account_name: accountNameInput.value })
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) throw new Error(data.message || 'Failed to update account name');
            showMessage('Account name updated successfully!', 'success');
            await loadAccountData();
            accountNameEdit.classList.add('hidden');
            accountNameDisplay.parentElement.classList.remove('hidden');
          } catch (err) {
            showMessage(err.message || 'Failed to update account name', 'error');
          } finally {
            setViewing();
          }
        });

        editNumberBtn.addEventListener('click', () => {
          accountNumberInput.value = accountNumberDisplay.textContent.trim();
          accountNumberEdit.classList.remove('hidden');
          accountNumberDisplay.parentElement.classList.add('hidden');
        });
        cancelNumberBtn.addEventListener('click', () => {
          accountNumberEdit.classList.add('hidden');
          accountNumberDisplay.parentElement.classList.remove('hidden');
        });
        saveNumberBtn.addEventListener('click', async () => {
          try {
            setEditing();
            const res = await fetch('/api/local_account_settings.php', {
              method: 'PUT',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify({ account_number: accountNumberInput.value })
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) throw new Error(data.message || 'Failed to update account number');
            showMessage('Account number updated successfully!', 'success');
            await loadAccountData();
            accountNumberEdit.classList.add('hidden');
            accountNumberDisplay.parentElement.classList.remove('hidden');
          } catch (err) {
            showMessage(err.message || 'Failed to update account number', 'error');
          } finally {
            setViewing();
          }
        });

        editBalanceBtn.addEventListener('click', () => {
          const txt = balanceDisplay.textContent.replace('NGN', '').trim();
          balanceInput.value = txt.replace(/,/g, '');
          balanceEdit.classList.remove('hidden');
          balanceDisplay.parentElement.classList.add('hidden');
        });
        cancelBalanceBtn.addEventListener('click', () => {
          balanceEdit.classList.add('hidden');
          balanceDisplay.parentElement.classList.remove('hidden');
        });
        saveBalanceBtn.addEventListener('click', async () => {
          try {
            setEditing();
            const res = await fetch('/api/local_account_settings.php', {
              method: 'PUT',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify({ balance: balanceInput.value })
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) throw new Error(data.message || 'Failed to update balance');
            showMessage('Balance updated successfully!', 'success');
            await loadAccountData();
            balanceEdit.classList.add('hidden');
            balanceDisplay.parentElement.classList.remove('hidden');
          } catch (err) {
            showMessage(err.message || 'Failed to update balance', 'error');
          } finally {
            setViewing();
          }
        });
      }

      wireEditControls();

      (async () => {
        try {
          await loadAccountData();
          await loadTransactions();
        } catch (err) {
          showMessage(err.message || 'Failed to load data', 'error');
        }
      })();
    })();
  </script>
</body>
</html>

