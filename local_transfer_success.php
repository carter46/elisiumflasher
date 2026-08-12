<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$txId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Transfer Successful | Elysium Server</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        'on-surface': '#131b2e',
        'secondary': '#006c49',
      },
      fontFamily: {
        headline: ['Inter', 'sans-serif'],
        body: ['Inter', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace'],
      },
    },
  },
};
</script>
<style>
  body { font-family: 'Inter', system-ui, sans-serif; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
  .naira-gradient {
    background: linear-gradient(135deg, #006c49 0%, #00a36c 100%);
  }
  .receipt-card {
    background: #fff;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
  }
  @media print {
    body { background: white !important; }
    .no-print { display: none !important; }
    .receipt-card { box-shadow: none !important; }
  }
</style>
</head>
<body class="min-h-screen bg-[#f5f7fa] text-on-surface">

<main class="flex flex-col items-center justify-center min-h-screen p-6">
  <!-- Success Message -->
  <div class="text-center mb-8 no-print">
    <div class="w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
      <span class="material-symbols-outlined text-green-600 text-4xl">check_circle</span>
    </div>
    <h1 class="text-2xl font-bold text-slate-900 mb-2">Transfer Successful!</h1>
    <p class="text-slate-500">Your transaction has been completed successfully</p>
  </div>

  <!-- Receipt Card -->
  <div id="receiptCard" class="receipt-card w-full max-w-md rounded-2xl overflow-hidden">
    <!-- Bank Header -->
    <div id="bankHeader" class="naira-gradient text-white p-6">
      <div class="flex items-center gap-4">
        <div id="bankLogoWrap" class="w-14 h-14 bg-white rounded-xl flex items-center justify-center">
          <span id="bankInitial" class="text-xl font-bold text-secondary">--</span>
        </div>
        <div>
          <h2 id="bankNameHeader" class="text-lg font-bold">Loading...</h2>
          <p class="text-white/70 text-sm">Transaction Receipt</p>
        </div>
      </div>
    </div>

    <!-- Receipt Details -->
    <div class="p-6 space-y-5">
      <!-- Amount -->
      <div class="text-center py-4 border-b border-slate-100">
        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Amount Transferred</p>
        <p id="amountDisplay" class="text-3xl font-bold text-slate-900">₦0.00</p>
      </div>

      <!-- Transaction Details -->
      <div class="space-y-4">
        <div class="flex justify-between">
          <span class="text-sm text-slate-500">Beneficiary Name</span>
          <span id="beneficiaryName" class="text-sm font-semibold text-slate-900 text-right">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-sm text-slate-500">Beneficiary Bank</span>
          <span id="beneficiaryBank" class="text-sm font-semibold text-slate-900 text-right">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-sm text-slate-500">Account Number</span>
          <span id="accountNumber" class="text-sm font-mono font-semibold text-slate-900">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-sm text-slate-500">Reference</span>
          <span id="reference" class="text-sm font-mono text-slate-700">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-sm text-slate-500">Date & Time</span>
          <span id="dateTime" class="text-sm text-slate-700">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-sm text-slate-500">Status</span>
          <span id="status" class="text-sm font-semibold text-green-600">SUCCESSFUL</span>
        </div>
        <div id="remarkRow" class="flex justify-between hidden">
          <span class="text-sm text-slate-500">Remark</span>
          <span id="remark" class="text-sm text-slate-700 text-right max-w-[60%]">--</span>
        </div>
      </div>

      <!-- Sender Details -->
      <div class="pt-4 border-t border-slate-100 space-y-4">
        <p class="text-xs text-slate-400 uppercase tracking-wider">Sender Details</p>
        <div class="flex justify-between">
          <span class="text-sm text-slate-500">Sender Name</span>
          <span id="senderName" class="text-sm font-semibold text-slate-900 text-right">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-sm text-slate-500">Sender Account</span>
          <span id="senderAccount" class="text-sm font-mono text-slate-700">--</span>
        </div>
      </div>

      <!-- Footer -->
      <div class="pt-4 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-400">Transaction processed by Elysium Server</p>
        <p class="text-xs text-slate-400 mt-1" id="footerTimestamp">--</p>
      </div>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="flex gap-3 mt-8 no-print">
    <button id="printBtn" class="px-6 py-3 bg-slate-800 text-white rounded-xl font-semibold text-sm flex items-center gap-2 hover:bg-slate-700 transition-colors">
      <span class="material-symbols-outlined text-lg">print</span>
      Print Receipt
    </button>
    <button id="downloadBtn" class="px-6 py-3 bg-secondary text-white rounded-xl font-semibold text-sm flex items-center gap-2 hover:opacity-90 transition-opacity">
      <span class="material-symbols-outlined text-lg">download</span>
      Download PDF
    </button>
  </div>

  <a href="/local_dashboard.php" class="mt-6 text-secondary font-semibold text-sm hover:underline no-print">
    Make Another Transfer
  </a>
</main>

<script>
(function() {
  const txId = <?= json_encode($txId) ?>;

  const bankNameHeader = document.getElementById('bankNameHeader');
  const bankInitial = document.getElementById('bankInitial');
  const amountDisplay = document.getElementById('amountDisplay');
  const beneficiaryName = document.getElementById('beneficiaryName');
  const beneficiaryBank = document.getElementById('beneficiaryBank');
  const accountNumber = document.getElementById('accountNumber');
  const reference = document.getElementById('reference');
  const dateTime = document.getElementById('dateTime');
  const status = document.getElementById('status');
  const remarkRow = document.getElementById('remarkRow');
  const remark = document.getElementById('remark');
  const senderName = document.getElementById('senderName');
  const senderAccount = document.getElementById('senderAccount');
  const footerTimestamp = document.getElementById('footerTimestamp');
  const bankHeader = document.getElementById('bankHeader');

  const printBtn = document.getElementById('printBtn');
  const downloadBtn = document.getElementById('downloadBtn');

  const bankColors = {
    // Nigeria
    'Access Bank': '#f26f21',
    'Zenith Bank': '#ed1c24',
    'First Bank': '#002d72',
    'UBA': '#ce181e',
    'GTBank': '#ff6600',
    'Guaranty Trust Bank': '#ff6600',
    'Fidelity Bank': '#00a650',
    'Union Bank': '#003366',
    'Stanbic': '#004b87',
    'Sterling Bank': '#ed1c24',
    'Wema Bank': '#662d91',
    'Polaris Bank': '#78be20',
    'Keystone Bank': '#00a1e0',
    'Heritage Bank': '#00703c',
    'Jaiz Bank': '#01a85a',
    'Unity Bank': '#00a651',
    'FCMB': '#6a1c7e',
    'Ecobank': '#0066b3',
    'Standard Chartered': '#0072aa',
    'Citibank': '#004685',
    'Kuda Bank': '#40196d',
    'Moniepoint': '#ff5a00',
    'OPay': '#1dbf73',
    'PalmPay': '#8b5cf6',
    // South Africa
    'Standard Bank': '#003d6a',
    'Absa': '#af1e2d',
    'Nedbank': '#009639',
    'Capitec': '#0033a0',
    'FNB': '#009fda',
    // Ghana
    'GCB Bank': '#1a3c6e',
    'CalBank': '#0066b3',
    // Egypt
    'National Bank of Egypt': '#1a4a8c',
    'Banque Misr': '#c8102e',
    // Kenya
    'Kenya Commercial Bank': '#00a651',
    'Equity Bank': '#8b0000',
    'Co-operative Bank': '#00539b',
    // Morocco
    'Attijariwafa': '#e31937',
    // Rwanda
    'Bank of Kigali': '#003d6a',
    // Other African banks
    'CRDB': '#00539b',
    'NMB Bank': '#ff6600',
    'Zanaco': '#00539b',
    'CBZ': '#0072aa',
    'default': '#006c49'
  };

  function getBankColor(bankName) {
    for (const [key, color] of Object.entries(bankColors)) {
      if (bankName && bankName.toLowerCase().includes(key.toLowerCase())) {
        return color;
      }
    }
    return bankColors.default;
  }

  function formatMoney(amount, currency = 'NGN') {
    const num = Number(amount) || 0;
    const currencySymbols = {
      'NGN': '₦', 'ZAR': 'R', 'GHS': '₵', 'KES': 'KSh', 'UGX': 'USh',
      'TZS': 'TSh', 'EGP': 'E£', 'MAD': 'DH', 'XOF': 'CFA', 'XAF': 'CFA',
      'ETB': 'Br', 'BWP': 'P', 'ZMW': 'ZK', 'ZWL': 'Z$', 'RWF': 'FRw',
      'USD': '$', 'EUR': '€', 'GBP': '£'
    };
    const symbol = currencySymbols[currency] || currency + ' ';
    return symbol + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function formatDateTime(dateStr) {
    if (!dateStr) return '--';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', {
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  function maskAccount(acct) {
    if (!acct || acct.length < 6) return acct;
    return '*'.repeat(acct.length - 4) + acct.slice(-4);
  }

  async function loadTransaction() {
    if (!txId) {
      amountDisplay.textContent = 'Transaction not found';
      return;
    }

    try {
      // Load transaction details from admin API (GET is available for user)
      const res = await fetch('/api/local_transactions.php?id=' + txId);
      // If 401, try loading from session storage fallback
      if (res.status === 401) {
        const data = await res.json().catch(() => ({}));
        if (data.redirect) {
          window.location.href = data.redirect;
          return;
        }
      }

      // Try another approach - get from session storage
      const txDataStr = sessionStorage.getItem('lastLocalTransaction');
      if (txDataStr) {
        const tx = JSON.parse(txDataStr);
        populateReceipt(tx);
        sessionStorage.removeItem('lastLocalTransaction');
        return;
      }

      amountDisplay.textContent = 'Transaction data not available';
    } catch (err) {
      amountDisplay.textContent = 'Error loading transaction';
    }
  }

  function populateReceipt(tx) {
    const bankName = tx.beneficiary_bank || tx.bank_name || 'Unknown Bank';
    const color = getBankColor(bankName);

    bankHeader.style.background = `linear-gradient(135deg, ${color} 0%, ${adjustColor(color, 20)} 100%)`;
    bankNameHeader.textContent = bankName;
    bankInitial.textContent = bankName.substring(0, 2).toUpperCase();
    bankInitial.style.color = color;

    amountDisplay.textContent = formatMoney(tx.amount, tx.currency || 'NGN');
    beneficiaryName.textContent = tx.beneficiary_name || '--';
    beneficiaryBank.textContent = bankName;
    accountNumber.textContent = tx.beneficiary_account || '--';
    reference.textContent = tx.reference || '--';
    dateTime.textContent = formatDateTime(tx.transaction_date || tx.created_at);
    status.textContent = (tx.status || 'SUCCESSFUL').toUpperCase();
    
    if (tx.purpose || tx.remark) {
      remarkRow.classList.remove('hidden');
      remark.textContent = tx.purpose || tx.remark;
    }

    senderName.textContent = tx.sender_name || '--';
    senderAccount.textContent = maskAccount(tx.sender_account) || '--';
    footerTimestamp.textContent = new Date().toISOString();
  }

  function adjustColor(hex, percent) {
    const num = parseInt(hex.replace('#', ''), 16);
    const r = Math.min(255, Math.max(0, (num >> 16) + percent));
    const g = Math.min(255, Math.max(0, ((num >> 8) & 0x00FF) + percent));
    const b = Math.min(255, Math.max(0, (num & 0x0000FF) + percent));
    return '#' + (0x1000000 + r * 0x10000 + g * 0x100 + b).toString(16).slice(1);
  }

  printBtn.addEventListener('click', () => window.print());

  downloadBtn.addEventListener('click', async () => {
    const { jsPDF } = window.jspdf;
    const receipt = document.getElementById('receiptCard');

    try {
      const canvas = await html2canvas(receipt, {
        scale: 2,
        useCORS: true,
        backgroundColor: '#ffffff'
      });

      const imgData = canvas.toDataURL('image/jpeg', 0.9);
      const pdf = new jsPDF('p', 'mm', 'a4');

      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();
      const imgWidth = pageWidth - 20;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;

      const x = 10;
      const y = 10;

      pdf.addImage(imgData, 'JPEG', x, y, imgWidth, Math.min(imgHeight, pageHeight - 20));

      const refText = reference.textContent || 'receipt';
      pdf.save(`local_transfer_${refText}.pdf`);
    } catch (err) {
      alert('Failed to generate PDF');
    }
  });

  // Check for transaction data in sessionStorage first
  const txDataStr = sessionStorage.getItem('lastLocalTransaction');
  if (txDataStr) {
    try {
      const tx = JSON.parse(txDataStr);
      populateReceipt(tx);
      sessionStorage.removeItem('lastLocalTransaction');
    } catch (e) {
      loadTransaction();
    }
  } else {
    loadTransaction();
  }
})();
</script>
</body>
</html>
