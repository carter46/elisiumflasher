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
  <title>International settings | Elysium Server</title>
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
  <div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between gap-4 mb-6">
      <a href="/admin/index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:underline">
        <span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle;">arrow_back</span>
        Back
      </a>
      <h1 class="text-2xl font-semibold text-slate-900">International settings</h1>
    </div>

    <div id="messageBox" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm"></div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <div class="flex items-center justify-between gap-4 mb-4">
        <div>
          <div class="text-sm font-semibold text-slate-700 mb-1">International sender details</div>
          <p class="text-xs text-slate-500">These appear as the sender on the international receipt.</p>
        </div>
        <button id="saveSenderBtn" class="px-4 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold">Save sender</button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Sender name</div>
          <input id="senderNameInput" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
        </div>
        <div>
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Sender bank</div>
          <input id="senderBankInput" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
        </div>
        <div>
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Sender country</div>
          <input id="senderCountryInput" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
        </div>
        <div>
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Sender SWIFT (optional)</div>
          <input id="senderSwiftInput" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
        </div>
        <div class="md:col-span-2">
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Address line 1</div>
          <input id="senderAddr1Input" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
        </div>
        <div class="md:col-span-2">
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Address line 2 (optional)</div>
          <input id="senderAddr2Input" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
        </div>
        <div class="md:col-span-2">
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Address line 3 (optional)</div>
          <input id="senderAddr3Input" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
        </div>
        <div class="md:col-span-2">
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Sender IBAN (optional)</div>
          <input id="senderIbanInput" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
        </div>
        <div class="md:col-span-2">
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Default Delivery Date</div>
          <input type="date" id="senderDeliveryDateInput" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200" />
          <p class="mt-1 text-xs text-slate-400">This date will be shown to users on the international transfer dashboard.</p>
        </div>
        <div class="md:col-span-2 border-t border-slate-100 pt-4 mt-2">
          <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Receipt logo (optional)</div>
          <p class="text-xs text-slate-500 mb-3">Replaces the SWIFT logo on the MT103 receipt. If cleared, the default SWIFT logo is used.</p>
          <div class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col gap-2">
              <input type="file" id="receiptLogoInput" accept="image/jpeg,image/png,image/webp,image/gif" class="text-sm" />
              <div class="flex gap-2">
                <button type="button" id="clearReceiptLogoBtn" class="px-3 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Clear logo</button>
              </div>
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-xs text-slate-500">Preview</span>
              <div class="w-32 h-32 border border-slate-200 rounded-xl bg-slate-50 flex items-center justify-center overflow-hidden">
                <img id="receiptLogoPreview" src="/assets/swift-logo.svg" alt="Logo preview" class="max-w-full max-h-full object-contain" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-3 text-xs text-slate-500">Last updated: <span id="senderUpdatedAt">—</span></div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">
      <div class="flex items-center justify-between gap-4 mb-4">
        <h2 class="text-xl font-semibold text-slate-900">International transactions</h2>
        <button id="reloadBtn" class="px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-semibold">Reload</button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
          <thead>
            <tr class="border-b">
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Date</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Reference</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Beneficiary</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Bank</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Amount</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Delivery</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Status</th>
              <th class="text-left py-3 font-semibold text-slate-700 px-2">Tracker</th>
              <th class="text-right py-3 font-semibold text-slate-700 px-2">Actions</th>
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
      function formatMoney(cur, v) {
        const n = Number(v || 0);
        return (cur || 'USD') + ' ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

      const senderNameInput = document.getElementById('senderNameInput');
      const senderBankInput = document.getElementById('senderBankInput');
      const senderCountryInput = document.getElementById('senderCountryInput');
      const senderSwiftInput = document.getElementById('senderSwiftInput');
      const senderAddr1Input = document.getElementById('senderAddr1Input');
      const senderAddr2Input = document.getElementById('senderAddr2Input');
      const senderAddr3Input = document.getElementById('senderAddr3Input');
      const senderIbanInput = document.getElementById('senderIbanInput');
      const senderDeliveryDateInput = document.getElementById('senderDeliveryDateInput');
      const senderUpdatedAt = document.getElementById('senderUpdatedAt');
      const saveSenderBtn = document.getElementById('saveSenderBtn');
      const receiptLogoInput = document.getElementById('receiptLogoInput');
      const receiptLogoPreview = document.getElementById('receiptLogoPreview');
      const clearReceiptLogoBtn = document.getElementById('clearReceiptLogoBtn');

      function setLogoPreview(path) {
        if (!receiptLogoPreview) return;
        receiptLogoPreview.src = path ? path : '/assets/swift-logo.svg';
      }

      async function loadSender() {
        const res = await fetch('/api/international_sender_settings.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load sender settings');
        const s = data.sender || {};
        senderNameInput.value = s.sender_name || '';
        senderBankInput.value = s.sender_bank || '';
        senderCountryInput.value = s.sender_country || '';
        senderSwiftInput.value = s.sender_swift || '';
        senderAddr1Input.value = s.sender_address_line1 || '';
        senderAddr2Input.value = s.sender_address_line2 || '';
        senderAddr3Input.value = s.sender_address_line3 || '';
        senderIbanInput.value = s.sender_iban || '';
        senderDeliveryDateInput.value = s.default_delivery_date || '';
        senderUpdatedAt.textContent = s.updated_at || '—';
        setLogoPreview(s.receipt_logo_path || '');
        if (receiptLogoInput) receiptLogoInput.value = '';
      }

      if (receiptLogoInput) {
        receiptLogoInput.addEventListener('change', async () => {
          const f = receiptLogoInput.files && receiptLogoInput.files[0];
          if (!f) return;
          try {
            const fd = new FormData();
            fd.append('logo', f);
            const res = await fetch('/api/international_sender_logo.php', { method: 'POST', body: fd });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) throw new Error(data.message || 'Upload failed');
            setLogoPreview(data.receipt_logo_path || '');
            senderUpdatedAt.textContent = data.updated_at || '—';
            showMessage('Receipt logo updated.', 'success');
          } catch (err) {
            showMessage(err.message || 'Upload failed', 'error');
          } finally {
            receiptLogoInput.value = '';
          }
        });
      }

      if (clearReceiptLogoBtn) {
        clearReceiptLogoBtn.addEventListener('click', async () => {
          try {
            clearReceiptLogoBtn.disabled = true;
            const res = await fetch('/api/international_sender_logo.php', { method: 'DELETE' });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) throw new Error(data.message || 'Failed to clear');
            setLogoPreview('');
            senderUpdatedAt.textContent = data.updated_at || senderUpdatedAt.textContent;
            showMessage('Receipt logo cleared.', 'success');
          } catch (err) {
            showMessage(err.message || 'Failed to clear', 'error');
          } finally {
            clearReceiptLogoBtn.disabled = false;
          }
        });
      }

      saveSenderBtn.addEventListener('click', async () => {
        try {
          saveSenderBtn.disabled = true;
          const payload = {
            sender_name: senderNameInput.value,
            sender_bank: senderBankInput.value,
            sender_country: senderCountryInput.value,
            sender_swift: senderSwiftInput.value,
            sender_address_line1: senderAddr1Input.value,
            sender_address_line2: senderAddr2Input.value,
            sender_address_line3: senderAddr3Input.value,
            sender_iban: senderIbanInput.value,
            default_delivery_date: senderDeliveryDateInput.value,
          };
          const res = await fetch('/api/international_sender_settings.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
          });
          const data = await res.json().catch(() => ({}));
          if (!res.ok || !data.success) throw new Error(data.message || 'Failed to save sender');
          senderUpdatedAt.textContent = data.sender.updated_at || '—';
          showMessage('Sender settings saved.', 'success');
        } catch (err) {
          showMessage(err.message || 'Failed to save sender', 'error');
        } finally {
          saveSenderBtn.disabled = false;
        }
      });

      const txBody = document.getElementById('txBody');
      const reloadBtn = document.getElementById('reloadBtn');

      function statusBadge(status) {
        const s = String(status || '').toUpperCase();
        if (s === 'FAILED' || s === 'NETWORK_ERROR') return 'bg-red-50 text-red-800 border-red-200';
        if (s === 'PROCESSING') return 'bg-sky-50 text-sky-800 border-sky-200';
        if (s === 'PENDING') return 'bg-amber-50 text-amber-800 border-amber-200';
        if (s === 'REVERSED') return 'bg-slate-100 text-slate-800 border-slate-200';
        return 'bg-green-50 text-green-800 border-green-200';
      }

      async function loadTransactions() {
        const res = await fetch('/api/international_transactions_admin.php?limit=100&offset=0');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load transactions');
        const txs = Array.isArray(data.transactions) ? data.transactions : [];
        txBody.innerHTML = '';
        const trackerStatuses = ['PDNG','ACSP','ACSC','ACCC','AVLB','HOLD','RJCT','RETN','CNCL'];
        if (!txs.length) {
          txBody.innerHTML = '<tr><td colspan="9" class="py-10 text-center text-slate-600">No transactions found</td></tr>';
          return;
        }

        txs.forEach((t) => {
          const date = t.created_at ? new Date(t.created_at) : null;
          const dateStr = date ? date.toLocaleDateString() : '';
          const timeStr = date ? date.toLocaleTimeString() : '';
          const trackerStatus = String(t.tracker_status || 'PDNG').toUpperCase();

          const tr = document.createElement('tr');
          tr.className = 'border-b';
          tr.innerHTML = `
            <td class="py-3 px-2">
              ${dateStr}<div class="text-xs text-slate-500">${timeStr}</div>
            </td>
            <td class="py-3 px-2 font-mono text-xs">
              <div>${t.reference || ''}</div>
              <div class="text-[10px] text-slate-500 mt-1 break-all">${t.uetr || '—'}</div>
            </td>
            <td class="py-3 px-2">${t.beneficiary_name || ''}</td>
            <td class="py-3 px-2">${t.bank_name || ''}</td>
            <td class="py-3 px-2 font-semibold">${formatMoney(t.currency, t.amount)}</td>
            <td class="py-3 px-2">${t.delivery_date || ''}</td>
            <td class="py-3 px-2">
              <select class="statusSel border border-slate-200 rounded-xl px-2 py-1 text-xs font-semibold">
                <option value="SUCCESSFUL">SUCCESSFUL</option>
                <option value="PENDING">PENDING</option>
                <option value="PROCESSING">PROCESSING</option>
                <option value="FAILED">FAILED</option>
                <option value="REVERSED">REVERSED</option>
                <option value="NETWORK_ERROR">NETWORK_ERROR</option>
              </select>
              <span class="ml-2 inline-flex items-center px-2.5 py-1 rounded-lg border text-xs font-semibold ${statusBadge(t.status)}">${String(t.status || '').toUpperCase()}</span>
            </td>
            <td class="py-3 px-2">
              <select class="trackerSel border border-slate-200 rounded-xl px-2 py-1 text-xs font-semibold">
                ${trackerStatuses.map((s) => `<option value="${s}" ${s === trackerStatus ? 'selected' : ''}>${s}</option>`).join('')}
              </select>
            </td>
            <td class="py-3 px-2 text-right">
              <div class="flex justify-end gap-2">
                <button class="saveBtn px-3 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold text-xs">Save</button>
                <button class="deleteBtn px-3 py-2 rounded-xl border border-red-200 bg-white text-red-600 font-semibold text-xs">Delete</button>
              </div>
            </td>
          `;
          const sel = tr.querySelector('select.statusSel');
          const trackerSel = tr.querySelector('select.trackerSel');
          sel.value = String(t.status || 'SUCCESSFUL').toUpperCase();

          tr.querySelector('button.saveBtn').addEventListener('click', async () => {
            try {
              tr.querySelector('button.saveBtn').disabled = true;
              const res2 = await fetch('/api/international_transactions_admin.php', {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id: t.id, status: sel.value, tracker_status: trackerSel.value })
              });
              const data2 = await res2.json().catch(() => ({}));
              if (!res2.ok || !data2.success) throw new Error(data2.message || 'Failed to update status');
              showMessage('Transaction status updated.', 'success');
              await loadTransactions();
            } catch (err) {
              showMessage(err.message || 'Failed to update status', 'error');
            } finally {
              tr.querySelector('button.saveBtn').disabled = false;
            }
          });

          tr.querySelector('button.deleteBtn').addEventListener('click', async () => {
            if (!confirm('Delete this transaction? This cannot be undone.')) return;
            try {
              tr.querySelector('button.deleteBtn').disabled = true;
              const res2 = await fetch('/api/international_transactions_admin.php?id=' + encodeURIComponent(t.id), {
                method: 'DELETE'
              });
              const data2 = await res2.json().catch(() => ({}));
              if (!res2.ok || !data2.success) throw new Error(data2.message || 'Failed to delete transaction');
              showMessage('Transaction deleted.', 'success');
              await loadTransactions();
            } catch (err) {
              showMessage(err.message || 'Failed to delete transaction', 'error');
            } finally {
              tr.querySelector('button.deleteBtn').disabled = false;
            }
          });

          txBody.appendChild(tr);
        });
      }

      reloadBtn.addEventListener('click', () => {
        loadTransactions().catch((e) => showMessage(e.message || 'Failed to load', 'error'));
      });

      (async function init() {
        try {
          await loadSender();
          await loadTransactions();
        } catch (err) {
          showMessage(err.message || 'Failed to load', 'error');
        }
      })();
    })();
  </script>
</body>
</html>
