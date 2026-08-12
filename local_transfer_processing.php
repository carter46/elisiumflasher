<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$bankCode = htmlspecialchars($_GET['bank_code'] ?? '', ENT_QUOTES, 'UTF-8');
$bankName = htmlspecialchars($_GET['bank_name'] ?? '', ENT_QUOTES, 'UTF-8');
$accountNumber = htmlspecialchars($_GET['account_number'] ?? '', ENT_QUOTES, 'UTF-8');
$accountName = htmlspecialchars($_GET['account_name'] ?? '', ENT_QUOTES, 'UTF-8');
$amount = htmlspecialchars($_GET['amount'] ?? '0.00', ENT_QUOTES, 'UTF-8');
$remark = htmlspecialchars($_GET['remark'] ?? '', ENT_QUOTES, 'UTF-8');
$reference = htmlspecialchars($_GET['reference'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Local Transfer Processing | Elysium Server</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-[#faf8ff] min-h-screen font-[Inter] text-slate-900 flex items-center justify-center p-6">
  <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl p-8 space-y-6">
    <h1 class="text-3xl font-extrabold tracking-tight">Local Transfer Processing</h1>
    <p class="text-sm text-slate-500">Transfer request prepared successfully. This page confirms resolved beneficiary details.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
      <div class="p-4 rounded-xl bg-slate-50"><span class="font-semibold">Bank:</span> <?= $bankName ?></div>
      <div class="p-4 rounded-xl bg-slate-50"><span class="font-semibold">Account Number:</span> <?= $accountNumber ?></div>
      <div class="p-4 rounded-xl bg-slate-50"><span class="font-semibold">Account Name:</span> <?= $accountName ?></div>
      <div class="p-4 rounded-xl bg-slate-50"><span class="font-semibold">Amount:</span> ₦<?= $amount ?></div>
    </div>
    <div class="p-4 rounded-xl bg-slate-50 text-sm"><span class="font-semibold">Remark:</span> <?= $remark !== '' ? $remark : 'N/A' ?></div>
    <div id="resultBox" class="p-4 rounded-xl bg-slate-50 text-sm text-slate-700">
      Processing transfer...
    </div>
    <div class="flex gap-3">
      <a class="px-5 py-3 rounded-xl bg-[#006c49] text-white font-semibold" href="/local_dashboard.php">Back to Local Transfer</a>
      <a class="px-5 py-3 rounded-xl bg-[#4648d4] text-white font-semibold" href="/transfer_selection.php">Back to Selection</a>
    </div>
  </div>
  <script>
    (async function () {
      const resultBox = document.getElementById('resultBox');
      const payload = {
        bank_code: <?= json_encode($bankCode) ?>,
        bank_name: <?= json_encode($bankName) ?>,
        beneficiary_account: <?= json_encode($accountNumber) ?>,
        beneficiary_name: <?= json_encode($accountName) ?>,
        amount: <?= json_encode((string) $amount) ?>,
        remark: <?= json_encode($remark) ?>,
        reference: <?= json_encode($reference) ?>
      };

      try {
        const res = await fetch('/api/local_transactions.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(payload)
        });

        const data = await res.json().catch(() => null);

        if (!data) {
          resultBox.className = 'p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800';
          resultBox.textContent = 'Failed to process transfer.';
          return;
        }

        if (res.status === 401 && data.redirect) {
          window.location.href = data.redirect;
          return;
        }

        if (res.ok && data.success) {
          const tx = data.transaction || {};
          resultBox.className = 'p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-800';
          resultBox.innerHTML = `
            <div class="font-semibold text-base mb-1">Transaction Successful</div>
            <div class="text-sm text-green-800">Reference: <span class="font-mono text-xs">${tx.reference || payload.reference || ''}</span></div>
            <div class="text-sm text-green-800 mt-1">Amount: ${tx.currency || 'NGN'} ${Number(tx.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
          `;
          return;
        }

        const bankStatus = data.bank_status || null;
        const msg = data.message || 'Transfer blocked by current bank settings.';

        const statusMap = {
          pending_request: {
            title: 'Pending Request Detected',
            detail: 'No debit is performed right now for this bank.'
          },
          weak_logs: {
            title: 'Weak Log Warning',
            detail: 'Logs are weak and might not complete a 100% transaction.'
          },
          post_no_debit: {
            title: 'Post No Debit',
            detail: 'Debit transactions are temporarily restricted for this bank.'
          },
          fixed_account: {
            title: 'Fixed Account',
            detail: 'This bank account is fixed. Transfers are not allowed right now.'
          },
        };

        let title = 'Transfer Blocked';
        let detail = msg;
        if (bankStatus && statusMap[bankStatus]) {
          title = statusMap[bankStatus].title;
          detail = statusMap[bankStatus].detail;
        } else if ((msg || '').toLowerCase().includes('maintenance')) {
          title = 'Platform Maintenance';
          detail = 'The platform is currently under maintenance. Please try again later.';
        }

        resultBox.className = 'p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900';
        resultBox.innerHTML = `
          <div class="font-semibold text-base mb-1">${title}</div>
          <div class="text-sm">${detail}</div>
        `;
      } catch (err) {
        resultBox.className = 'p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800';
        resultBox.textContent = 'Network error while processing transfer.';
      }
    })();
  </script>
</body>
</html>
