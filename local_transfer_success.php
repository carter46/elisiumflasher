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
  <div class="text-center mb-5 no-print">
    <div class="w-14 h-14 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
      <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
    </div>
    <h1 class="text-xl font-bold text-slate-900 mb-1">Transfer Successful!</h1>
    <p class="text-sm text-slate-500">Your transaction has been completed successfully</p>
  </div>

  <!-- Receipt Card -->
  <div id="receiptCard" class="receipt-card w-full max-w-md rounded-2xl overflow-hidden">
    <!-- Bank Header -->
    <div id="bankHeader" class="naira-gradient text-white px-4 py-3.5">
      <div class="flex items-center gap-3">
        <div id="bankLogoWrap" class="w-11 h-11 bg-white rounded-lg flex items-center justify-center overflow-hidden shrink-0">
          <img id="bankLogoImg" alt="" class="w-full h-full object-contain p-1 hidden"/>
          <span id="bankLogoFallback" class="material-symbols-outlined text-secondary text-[26px]">account_balance</span>
        </div>
        <div class="min-w-0">
          <h2 id="bankNameHeader" class="text-base font-bold truncate">Loading...</h2>
          <p class="text-white/70 text-xs">Transaction Receipt</p>
        </div>
      </div>
    </div>

    <!-- Receipt Details -->
    <div class="px-4 py-4 space-y-3">
      <!-- Amount -->
      <div class="text-center py-2 border-b border-slate-100">
        <p class="text-[11px] text-slate-500 uppercase tracking-wider mb-0.5">Amount Transferred</p>
        <p id="amountDisplay" class="text-2xl font-bold text-slate-900">₦0.00</p>
      </div>

      <!-- Transaction Details -->
      <div class="space-y-2.5">
        <div class="flex justify-between gap-3">
          <span class="text-xs text-slate-500">Beneficiary Name</span>
          <span id="beneficiaryName" class="text-xs font-semibold text-slate-900 text-right">--</span>
        </div>
        <div class="flex justify-between gap-3 items-center">
          <span class="text-xs text-slate-500">Beneficiary Bank</span>
          <span class="inline-flex items-center gap-1.5 justify-end min-w-0">
            <img id="beneficiaryBankLogo" alt="" class="w-5 h-5 rounded object-contain hidden bg-white border border-slate-100"/>
            <span id="beneficiaryBankFallback" class="material-symbols-outlined text-slate-400 text-[16px] hidden">account_balance</span>
            <span id="beneficiaryBank" class="text-xs font-semibold text-slate-900 text-right truncate">--</span>
          </span>
        </div>
        <div class="flex justify-between gap-3">
          <span class="text-xs text-slate-500">Account Number</span>
          <span id="accountNumber" class="text-xs font-mono font-semibold text-slate-900">--</span>
        </div>
        <div class="flex justify-between gap-3">
          <span class="text-xs text-slate-500">Reference</span>
          <span id="reference" class="text-xs font-mono text-slate-700">--</span>
        </div>
        <div class="flex justify-between gap-3">
          <span class="text-xs text-slate-500">Date & Time</span>
          <span id="dateTime" class="text-xs text-slate-700 text-right">--</span>
        </div>
        <div class="flex justify-between gap-3">
          <span class="text-xs text-slate-500">Status</span>
          <span id="status" class="text-xs font-semibold text-green-600">SUCCESSFUL</span>
        </div>
        <div id="remarkRow" class="flex justify-between gap-3 hidden">
          <span class="text-xs text-slate-500">Remark</span>
          <span id="remark" class="text-xs text-slate-700 text-right max-w-[60%]">--</span>
        </div>
      </div>

      <!-- Sender Details -->
      <div class="pt-3 border-t border-slate-100 space-y-2.5">
        <p class="text-[11px] text-slate-400 uppercase tracking-wider">Sender Details</p>
        <div class="flex justify-between gap-3">
          <span class="text-xs text-slate-500">Sender Name</span>
          <span id="senderName" class="text-xs font-semibold text-slate-900 text-right">--</span>
        </div>
        <div class="flex justify-between gap-3">
          <span class="text-xs text-slate-500">Sender Account</span>
          <span id="senderAccount" class="text-xs font-mono text-slate-700">--</span>
        </div>
      </div>

      <!-- Footer -->
      <div class="pt-3 border-t border-slate-100 text-center">
        <p class="text-[11px] text-slate-400">Transaction processed by Elysium Server</p>
        <p class="text-[11px] text-slate-400 mt-0.5" id="footerTimestamp">--</p>
      </div>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="flex gap-3 mt-6 no-print">
    <button id="printBtn" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl font-semibold text-sm flex items-center gap-2 hover:bg-slate-700 transition-colors">
      <span class="material-symbols-outlined text-lg">print</span>
      Print Receipt
    </button>
    <button id="downloadBtn" class="px-5 py-2.5 bg-secondary text-white rounded-xl font-semibold text-sm flex items-center gap-2 hover:opacity-90 transition-opacity">
      <span class="material-symbols-outlined text-lg">download</span>
      Download PDF
    </button>
  </div>

  <a href="/local_dashboard.php" class="mt-5 text-secondary font-semibold text-sm hover:underline no-print">
    Make Another Transfer
  </a>
</main>

<script>
(function() {
  const txId = <?= json_encode($txId) ?>;

  const bankNameHeader = document.getElementById('bankNameHeader');
  const bankLogoImg = document.getElementById('bankLogoImg');
  const bankLogoFallback = document.getElementById('bankLogoFallback');
  const beneficiaryBankLogo = document.getElementById('beneficiaryBankLogo');
  const beneficiaryBankFallback = document.getElementById('beneficiaryBankFallback');
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

  const BANK_LOGO_BASE = '/assets/bank_logos/';
  const bankLogoMap = [
    { codes: ['044'], patterns: [/access\s*bank/i], file: 'access bank copy-CA1pDmlE.jpg' },
    { codes: ['057'], patterns: [/zenith/i], file: 'Zenith Bank-Bk4Bp8zG.png' },
    { codes: ['011'], patterns: [/first\s*bank|firstbank/i], file: 'FirstBank-receipt-logo-CigPY1wl.png' },
    { codes: ['058'], patterns: [/guaranty\s*trust|\bgtbank\b|\bgt\s*bank\b/i], file: 'GT BANK copy-Ci_apJ6b.jpg' },
    { codes: ['033'], patterns: [/\buba\b|united\s*bank/i], file: 'uba logo-dAnDPcuW.png' },
    { codes: ['070'], patterns: [/fidelity/i], file: 'Fidelity bank-DrJvLoF-.jpg' },
    { codes: ['032'], patterns: [/union\s*bank/i], file: 'Union Bank-B0hCf2DG.png' },
    { codes: ['232'], patterns: [/sterling/i], file: 'Sterling-CkhNfBRJ.jpg' },
    { codes: ['035'], patterns: [/wema/i], file: 'Wema Bank-x--rG7Uw.png' },
    { codes: ['082'], patterns: [/keystone/i], file: 'Keystone Bank-DKUU8Y29.jpeg' },
    { codes: ['030'], patterns: [/heritage/i], file: 'Heritage Bank-Ckn2H2OC.jpeg' },
    { codes: ['301'], patterns: [/jaiz/i], file: 'Jaiz Bank-KOojfEuW.jpg' },
    { codes: ['215'], patterns: [/unity\s*bank/i], file: 'Unity Bank-Dr4MHinx.jpg' },
    { codes: ['50211'], patterns: [/kuda/i], file: 'Kuda Bank-B3N3qDe-.jpeg' },
    { codes: ['50515'], patterns: [/moniepoint/i], file: 'Moniepoint-Dr-VIKCi.jpeg' },
    { codes: ['999992'], patterns: [/opay/i], file: 'OPay-Vx731-Wp.JPEG' },
    { codes: ['100033'], patterns: [/palmpay/i], file: 'PalmPay-Byc834Oz.jpg' },
  ];

  const bankColors = {
    'Access Bank': '#f26f21',
    'Zenith Bank': '#ed1c24',
    'First Bank': '#002d72',
    'UBA': '#ce181e',
    'GTBank': '#ff6600',
    'Guaranty Trust Bank': '#ff6600',
    'Fidelity Bank': '#00a650',
    'Union Bank': '#003366',
    'Sterling Bank': '#ed1c24',
    'Wema Bank': '#662d91',
    'Keystone Bank': '#00a1e0',
    'Heritage Bank': '#00703c',
    'Jaiz Bank': '#01a85a',
    'Unity Bank': '#00a651',
    'Kuda Bank': '#40196d',
    'Moniepoint': '#ff5a00',
    'OPay': '#1dbf73',
    'PalmPay': '#8b5cf6',
    'default': '#006c49'
  };

  function resolveBankLogo(bankName, bankCode) {
    const code = String(bankCode || '').trim();
    const name = String(bankName || '');
    for (const entry of bankLogoMap) {
      if (code && entry.codes.includes(code)) {
        return BANK_LOGO_BASE + encodeURIComponent(entry.file);
      }
    }
    for (const entry of bankLogoMap) {
      if (entry.patterns.some(re => re.test(name))) {
        return BANK_LOGO_BASE + encodeURIComponent(entry.file);
      }
    }
    return null;
  }

  function applyBankLogo(imgEl, fallbackEl, bankName, bankCode) {
    const src = resolveBankLogo(bankName, bankCode);
    if (!imgEl || !fallbackEl) return;
    imgEl.onload = function () {
      imgEl.classList.remove('hidden');
      fallbackEl.classList.add('hidden');
    };
    imgEl.onerror = function () {
      imgEl.classList.add('hidden');
      imgEl.removeAttribute('src');
      fallbackEl.classList.remove('hidden');
    };
    if (src) {
      fallbackEl.classList.add('hidden');
      imgEl.classList.add('hidden');
      imgEl.alt = bankName || 'Bank logo';
      imgEl.src = src;
    } else {
      imgEl.classList.add('hidden');
      imgEl.removeAttribute('src');
      fallbackEl.classList.remove('hidden');
    }
  }

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
    const bankCode = tx.bank_code || '';
    const color = getBankColor(bankName);

    bankHeader.style.background = `linear-gradient(135deg, ${color} 0%, ${adjustColor(color, 20)} 100%)`;
    bankNameHeader.textContent = bankName;
    applyBankLogo(bankLogoImg, bankLogoFallback, bankName, bankCode);
    applyBankLogo(beneficiaryBankLogo, beneficiaryBankFallback, bankName, bankCode);

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
