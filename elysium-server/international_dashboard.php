<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();
?>

<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>International Transfer Dashboard | Elysium Server</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "background": "#faf8ff",
            "error-container": "#ffdad6",
            "error": "#ba1a1a",
            "on-tertiary-fixed": "#001e2f",
            "primary": "#4648d4",
            "surface-bright": "#faf8ff",
            "primary-fixed-dim": "#c0c1ff",
            "on-error-container": "#93000a",
            "on-primary-container": "#fffbff",
            "on-tertiary-container": "#fcfcff",
            "surface-dim": "#d2d9f4",
            "inverse-on-surface": "#eef0ff",
            "surface-container-high": "#e2e7ff",
            "on-primary": "#ffffff",
            "on-primary-fixed": "#07006c",
            "on-surface": "#131b2e",
            "on-secondary": "#ffffff",
            "on-secondary-fixed-variant": "#005236",
            "inverse-primary": "#c0c1ff",
            "outline": "#767586",
            "tertiary-container": "#007cb1",
            "surface-container-low": "#f2f3ff",
            "on-error": "#ffffff",
            "on-tertiary": "#ffffff",
            "secondary-container": "#6cf8bb",
            "on-primary-fixed-variant": "#2f2ebe",
            "on-secondary-fixed": "#002113",
            "outline-variant": "#c7c4d7",
            "primary-fixed": "#e1e0ff",
            "secondary": "#006c49",
            "tertiary-fixed": "#c9e6ff",
            "on-surface-variant": "#464554",
            "surface-variant": "#dae2fd",
            "secondary-fixed-dim": "#4edea3",
            "inverse-surface": "#283044",
            "surface-container-lowest": "#ffffff",
            "surface": "#faf8ff",
            "surface-tint": "#494bd6",
            "on-background": "#131b2e",
            "tertiary": "#00628d",
            "surface-container-highest": "#dae2fd",
            "tertiary-fixed-dim": "#89ceff",
            "on-tertiary-fixed-variant": "#004c6e",
            "surface-container": "#eaedff",
            "on-secondary-container": "#00714d",
            "primary-container": "#6063ee",
            "secondary-fixed": "#6ffbbe"
          },
          fontFamily: {
            "headline": ["Inter"],
            "body": ["Inter"],
            "label": ["Inter"]
          },
          borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "2xl": "1rem", "full": "9999px"},
        },
      },
    }
  </script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined {
      font-family: 'Material Symbols Outlined';
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      line-height: 1;
      text-transform: none;
      letter-spacing: normal;
      word-wrap: normal;
      white-space: nowrap;
      direction: ltr;
      -webkit-font-smoothing: antialiased;
    }
    .login-card {
      background: #ffffff;
      box-shadow: 0 16px 48px rgba(19, 27, 46, 0.1);
    }
    .login-input-wrap {
      border: 2px solid #0f0f0f;
      border-radius: 0.75rem;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .login-input-wrap:focus-within {
      border-color: #000000;
      box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.12);
    }
    .glass-effect {
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(24px);
    }
    .intl-term-panel {
      background: #070707;
      border: 1px solid rgba(20, 83, 45, 0.7);
    }
    .intl-term-header {
      background: #0f180f;
      border-bottom: 1px solid rgba(20, 83, 45, 0.5);
    }
    /* Transfer timeline: fixed footprint; stacks vertically when card is narrow */
    #intlTimelineCard.intl-tl-card {
      max-width: 100%;
      container-type: inline-size;
      container-name: intltl;
    }
    #intlTimelineCard.intl-tl--idle .intl-tl-idle-msg {
      display: block;
    }
    #intlTimelineCard.intl-tl--running .intl-tl-idle-msg {
      display: none;
    }
    .intl-tl-idle-msg {
      display: none;
      font-size: 11px;
      line-height: 1.4;
      color: rgba(148, 163, 184, 0.95);
      margin-top: 0.35rem;
      max-width: 100%;
    }
    #intlTlTrack {
      display: grid;
      grid-template-columns: 1fr minmax(12px, 18px) 1fr minmax(12px, 18px) 1fr;
      align-items: start;
      gap: 0.15rem 0;
      width: 100%;
      max-width: 100%;
      overflow: hidden;
      padding-top: 0.25rem;
      min-height: 5.5rem;
    }
    /* Narrow card: single column so the panel does not force the grid column wider */
    @container intltl (max-width: 340px) {
      #intlTlTrack {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.2rem;
        min-height: 0;
        padding-top: 0.35rem;
      }
      #intlTlTrack .intl-tl-line {
        width: min(70%, 9rem);
        height: 2px;
        min-height: 2px;
        margin: 0.1rem 0;
        flex-shrink: 0;
        align-self: center;
      }
      .intl-tl-step {
        width: 100%;
        max-width: 15rem;
      }
    }
    @container intltl (max-width: 240px) {
      #intlTlTrack .intl-tl-line {
        width: min(85%, 10rem);
      }
    }
    /* Fallback without container queries */
    @supports not (container-type: inline-size) {
      @media (max-width: 420px) {
        #intlTimelineCard #intlTlTrack {
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 0.2rem;
          min-height: 0;
        }
        #intlTimelineCard #intlTlTrack .intl-tl-line {
          width: min(70%, 9rem);
          height: 2px;
          margin: 0.1rem 0;
        }
        #intlTimelineCard .intl-tl-step {
          width: 100%;
          max-width: 15rem;
        }
      }
    }
    .intl-tl-line {
      width: 100%;
      height: 2px;
      background: rgba(148, 163, 184, 0.4);
      align-self: center;
      margin-top: 1.1rem;
    }
    .intl-tl-step {
      min-width: 0;
      text-align: center;
      padding: 0.2rem 0.1rem;
      border-radius: 0.5rem;
      transition: background 0.35s ease, box-shadow 0.35s ease, opacity 0.35s ease;
    }
    .intl-tl-step.is-active {
      background: rgba(255, 255, 255, 0.06);
      box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.45);
    }
    .intl-tl-step.is-pending {
      opacity: 0.42;
    }
    .intl-tl-step.is-done {
      opacity: 1;
    }
    .intl-tl-ico-wrap {
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 2rem;
      height: 2rem;
      margin: 0 auto 0.2rem;
    }
    .intl-tl-ico {
      font-size: 1.125rem !important;
      line-height: 1 !important;
      position: relative;
      z-index: 1;
    }
    .intl-tl-step-spinner {
      position: absolute;
      inset: -3px;
      border-radius: 50%;
      border: 2px solid rgba(16, 185, 129, 0.25);
      border-top-color: rgba(52, 211, 153, 0.95);
      opacity: 0;
      pointer-events: none;
      animation: intlTlSpin 0.85s linear infinite;
    }
    .intl-tl-step.is-active .intl-tl-step-spinner {
      opacity: 1;
    }
    @keyframes intlTlSpin {
      to { transform: rotate(360deg); }
    }
    @keyframes intlCheckPop {
      0% { transform: scale(0); opacity: 0; }
      55% { transform: scale(1.12); }
      100% { transform: scale(1); opacity: 1; }
    }
    .intl-tl-check-pop {
      animation: intlCheckPop 0.5s ease-out forwards;
    }
  </style>
<style>
  /* Remove sidebar while keeping header intact */
  aside{display:none !important;}
  main{margin-left:0 !important;}
</style>
</head>
<body class="bg-[#d4d4d4] text-on-surface min-h-screen flex">
<!-- SideNavBar -->
<aside class="fixed left-0 top-0 flex flex-col p-6 gap-2 h-screen w-64 border-r border-slate-200/15 bg-slate-50 z-40">
<div class="mb-10 px-2">
<h1 class="text-lg font-black text-slate-900">Global Finance</h1>
<p class="text-xs font-medium text-slate-500">Premium Tier</p>
</div>
<nav class="flex flex-col gap-1 flex-1">
<!-- Overview -->
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-100 rounded-xl transition-all duration-200 ease-in-out" href="#">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span class="font-inter text-sm font-medium">Overview</span>
</a>
<!-- Transfers (ACTIVE) -->
<a class="flex items-center gap-3 px-4 py-3 bg-white text-indigo-600 shadow-sm rounded-xl transition-all duration-200 ease-in-out" href="#">
<span class="material-symbols-outlined" data-icon="swap_horiz">swap_horiz</span>
<span class="font-inter text-sm font-semibold">Transfers</span>
</a>
<!-- Vaults -->
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-100 rounded-xl transition-all duration-200 ease-in-out" href="#">
<span class="material-symbols-outlined" data-icon="lock">lock</span>
<span class="font-inter text-sm font-medium">Vaults</span>
</a>
<!-- Analytics -->
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-100 rounded-xl transition-all duration-200 ease-in-out" href="#">
<span class="material-symbols-outlined" data-icon="leaderboard">leaderboard</span>
<span class="font-inter text-sm font-medium">Analytics</span>
</a>
<!-- Settings -->
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-100 rounded-xl transition-all duration-200 ease-in-out" href="#">
<span class="material-symbols-outlined" data-icon="settings">settings</span>
<span class="font-inter text-sm font-medium">Settings</span>
</a>
</nav>
<div class="mt-auto flex flex-col gap-4">
<button class="w-full py-3 bg-gradient-to-br from-primary to-primary-container text-white rounded-xl font-medium shadow-lg shadow-primary/20 hover:scale-[0.98] transition-transform">
        New Transfer
      </button>
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-100 rounded-xl transition-all" href="#">
<span class="material-symbols-outlined" data-icon="logout">logout</span>
<span class="font-inter text-sm font-medium">Sign Out</span>
</a>
</div>
</aside>
<!-- Main Content Area (header scrolls with page — not fixed / not sticky) -->
<main class="flex-1 flex flex-col min-h-screen w-full">
<header class="flex w-full shrink-0 justify-between items-center px-8 h-20 glass-effect border-b border-slate-300/80">
<div class="flex items-center gap-4">
<div class="flex items-center gap-2 px-3 py-1 bg-secondary/10 text-secondary rounded-full">
<span class="relative flex h-2 w-2">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
<span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span>
</span>
<span class="text-xs font-bold tracking-tight">Server Online</span>
</div>
<img src="/assets/swift-logo.svg" alt="SWIFT" class="h-11 w-auto select-none" />
</div>
<div class="flex items-center gap-6">
<button type="button" id="headerLogoutBtn" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow-sm transition-colors">
<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 0;">logout</span>
Logout
</button>
</div>
</header>
<!-- Canvas Section -->
<section class="p-8 flex flex-col gap-8 max-w-7xl mx-auto w-full">
<form id="intlTransferForm" class="contents" autocomplete="off">
<!-- Top row: ~65% / ~35% -->
<div class="grid grid-cols-1 lg:grid-cols-[13fr_7fr] gap-8 items-stretch">
<div class="login-card border border-slate-300 rounded-2xl p-8 lg:p-10 flex flex-col min-h-[320px] lg:min-h-[380px]">
<div class="flex flex-col gap-5 flex-1">
<span class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1">Beneficiary details</span>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="intlCountry">Country</label>
<div class="login-input-wrap relative flex items-center px-3 bg-slate-50">
<input type="text" id="intlCountry" name="country_display" readonly class="w-full bg-transparent border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm text-slate-700" value=""/>
<input type="hidden" id="intlCountryCode" name="country_code" value=""/>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="intlBeneficiaryIban">Beneficiary IBAN or account number</label>
<div class="login-input-wrap relative flex items-center px-3">
<input type="text" id="intlBeneficiaryIban" name="beneficiary_iban" inputmode="text" pattern="[A-Za-z0-9]{6,34}" title="Letters and numbers only" placeholder="Beneficiary IBAN or account number" maxlength="34" required class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400"/>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="intlAccountName">Account name</label>
<div class="login-input-wrap relative flex items-center px-3">
<input type="text" id="intlAccountName" name="account_name" placeholder="Beneficiary name" required class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400"/>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="intlBeneficiaryAddress">Beneficiary address</label>
<div class="login-input-wrap relative flex items-center px-3">
<textarea id="intlBeneficiaryAddress" name="beneficiary_address" placeholder="Full address (street, city, postal code, country)" required rows="2" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400 resize-none"></textarea>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div id="intlSwiftCodeWrap" class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="intlSwiftCode">SWIFT / BIC code</label>
<div class="login-input-wrap relative flex items-center px-3">
<input type="text" id="intlSwiftCode" name="swift_code" placeholder="BIC / SWIFT" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400"/>
</div>
</div>
<div id="intlRoutingWrap" class="flex flex-col gap-1.5 hidden">
<label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="intlRoutingNumber">ABA Routing Number</label>
<div class="login-input-wrap relative flex items-center px-3">
<input type="text" id="intlRoutingNumber" name="routing_number" placeholder="9-digit routing number" maxlength="9" pattern="[0-9]{9}" class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400"/>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="intlBankName">Bank name</label>
<div class="relative">
  <div class="login-input-wrap relative flex items-center px-3">
    <input type="text" id="intlBankName" name="bank_name_display" placeholder="Search and select a bank" autocomplete="off" required class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400"/>
    <input type="hidden" id="intlBankNameValue" name="bank_name" value=""/>
  </div>
  <div id="intlBankDropdown" class="absolute left-0 right-0 top-full z-30 mt-1 max-h-56 overflow-y-auto border-2 border-black/80 bg-white shadow-xl rounded-xl hidden" role="listbox"></div>
</div>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="intlAmount">Amount</label>
<div class="login-input-wrap relative flex items-center px-3">
<input type="number" id="intlAmount" name="amount" min="0.01" step="0.01" placeholder="0.00" required class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm placeholder:text-slate-400"/>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1" for="intlCurrency">Currency</label>
<div class="login-input-wrap relative flex items-center px-3">
<select id="intlCurrency" name="currency" required class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface rounded-lg text-sm cursor-pointer">
<option value="">— Select currency —</option>
</select>
</div>
</div>
</div>
</div>
</div>
<div class="login-card border border-slate-300 rounded-2xl p-8 lg:p-10 flex flex-col justify-between min-h-[320px] lg:min-h-[380px]">
<div class="flex flex-col gap-5 flex-1">
<span class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1">Transfer destination</span>
<div class="space-y-4">
<label class="block" for="intlRouteSelect">
<span class="text-xs font-semibold text-slate-600 tracking-wider uppercase pl-1 mb-2 block">Message type</span>
<div class="login-input-wrap flex items-center gap-2 px-1">
<span class="material-symbols-outlined text-slate-700 pl-2 text-xl shrink-0" data-icon="account_balance">account_balance</span>
<select id="intlRouteSelect" name="intl_route" required class="w-full bg-white border-none focus:ring-0 py-3 pr-3 pl-1 text-sm font-semibold text-slate-800 cursor-pointer rounded-lg">
<option value="">— Select message type —</option>
<option value="MT103 Cash Transfer">MT103 Cash Transfer</option>
<option value="MT103/202 Credit Cash">MT103/202 Credit Cash</option>
<option value="MT103 Via IPIPE IPID">MT103 Via IPIPE IPID</option>
<option value="MT103 GPI AUTOMATIC">MT103 GPI AUTOMATIC</option>
<option value="MT 199">MT 199</option>
<option value="MT 700">MT 700</option>
</select>
</div>
</label>
<div class="block">
<div id="intlTimelineCard" class="intl-tl-card intl-tl--idle mt-5 rounded-2xl border border-slate-800/25 overflow-hidden shadow-xl">
  <div class="bg-[#0c0f14] text-slate-100 px-3 py-3 sm:px-4 sm:py-3.5">
    <div class="flex flex-row flex-wrap items-start justify-between gap-x-3 gap-y-1">
      <p id="intlTlHeadTitle" class="text-sm sm:text-[15px] font-medium text-slate-200 leading-snug tracking-tight min-w-0 flex-1">Transfer timeline</p>
      <div id="intlTlHeadStatus" class="flex items-center gap-1.5 shrink-0">
        <span id="intlTlStatusDot" class="w-2 h-2 rounded-full bg-slate-500"></span>
        <span id="intlTlStatusText" class="text-[11px] sm:text-xs font-semibold text-slate-400 whitespace-nowrap">Idle</span>
      </div>
    </div>
    <p id="intlTlIdleLine" class="intl-tl-idle-msg">No transaction initiation detected. Submit a transfer to run the SWIFT preparation sequence.</p>
    <div class="h-px bg-white/[0.08] my-3"></div>
    <div id="intlTlTrack">
      <div id="intlTlS1" class="intl-tl-step is-pending flex flex-col items-center min-w-0">
        <div class="intl-tl-ico-wrap">
          <span class="intl-tl-step-spinner" aria-hidden="true"></span>
          <span class="material-symbols-outlined intl-tl-ico text-white">touch_app</span>
        </div>
        <span class="text-[10px] sm:text-[11px] font-semibold text-white leading-tight">Initiated</span>
        <span class="intl-tl-date text-[9px] sm:text-[10px] text-slate-400 mt-0.5 leading-snug">—</span>
      </div>
      <div class="intl-tl-line" aria-hidden="true"></div>
      <div id="intlTlS2" class="intl-tl-step is-pending flex flex-col items-center min-w-0">
        <div class="intl-tl-ico-wrap">
          <span class="intl-tl-step-spinner" aria-hidden="true"></span>
          <span class="material-symbols-outlined intl-tl-ico text-amber-400">schedule</span>
        </div>
        <span class="text-[10px] sm:text-[11px] font-semibold text-white leading-tight">In progress</span>
        <span class="intl-tl-date text-[9px] sm:text-[10px] text-slate-400 mt-0.5 leading-snug">—</span>
      </div>
      <div class="intl-tl-line" aria-hidden="true"></div>
      <div id="intlTlS3" class="intl-tl-step is-pending flex flex-col items-center min-w-0">
        <div class="intl-tl-ico-wrap">
          <span class="intl-tl-step-spinner" aria-hidden="true"></span>
          <span class="material-symbols-outlined intl-tl-ico text-emerald-400">check_circle</span>
        </div>
        <span class="text-[10px] sm:text-[11px] font-semibold text-white leading-tight">Connected</span>
        <span class="intl-tl-date text-[9px] sm:text-[10px] text-slate-400 mt-0.5 leading-snug">—</span>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="intlRouteDate" name="intl_route_date" value=""/>
</div>
</div>
</div>
<button type="submit" id="intlTransferSubmitBtn" class="w-full mt-6 py-4 bg-black text-gray-400 rounded-xl font-semibold text-sm tracking-tight hover:bg-zinc-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            Transfer
          </button>
</div>
</div>
</form>
<!-- Metrics terminal table -->
<div class="intl-term-panel rounded-2xl overflow-hidden shadow-2xl">
<div class="intl-term-header px-4 py-2 flex justify-between items-center gap-2">
<span class="font-mono text-[11px] text-green-500/90 tracking-wide">HASH RAIL / ASIC DISPATCH — live window</span>
<span class="font-mono text-[10px] text-green-700">v4.09</span>
</div>
<div class="p-4 overflow-x-auto">
<table class="w-full min-w-[520px] border-collapse font-mono text-[12px]" id="intlMetricsTable">
<thead>
<tr class="text-left text-green-600/90 border-b border-green-900/60">
<th class="py-2 pr-4 font-bold uppercase tracking-wider">ASICs</th>
<th class="py-2 pr-4 font-bold uppercase tracking-wider">Accepted</th>
<th class="py-2 pr-4 font-bold uppercase tracking-wider">Rejected</th>
<th class="py-2 font-bold uppercase tracking-wider">GH/s 5s</th>
</tr>
</thead>
<tbody id="intlMetricsTbody" class="text-green-400"></tbody>
</table>
</div>
</div>
<!-- Protocol stream (transfer_selection modal style) -->
<div class="intl-term-panel rounded-2xl overflow-hidden shadow-2xl">
<div class="intl-term-header px-4 py-2 flex justify-between items-center gap-2">
<span class="font-mono text-[11px] text-green-500/90 tracking-wide">ELY-PROTOCOL / TTY — opaque frame</span>
<span class="font-mono text-[10px] text-green-700">secure</span>
</div>
<div id="intlProtocolStream" class="p-4 h-48 overflow-y-auto font-mono text-[11px] leading-relaxed text-green-400/95 whitespace-pre-wrap break-all"></div>
</div>

<!-- International transaction history -->
<div class="login-card border border-slate-300 rounded-2xl p-8 lg:p-10">
  <div class="flex items-center justify-between gap-4 mb-6">
    <div>
      <h3 class="text-lg font-extrabold tracking-tight text-slate-900">Transaction history</h3>
      <p class="text-sm text-slate-600 font-medium mt-1">All international transfers submitted from this page.</p>
    </div>
    <button type="button" id="intlTxRefreshBtn" class="px-4 py-2 rounded-xl bg-white border-2 border-slate-900 text-slate-900 text-sm font-semibold hover:bg-slate-50 transition-colors">
      Refresh
    </button>
  </div>
  <div class="overflow-x-auto rounded-xl border-2 border-[#0f0f0f]/15">
    <table class="w-full min-w-[820px]">
      <thead>
        <tr class="text-left border-b border-slate-200 bg-slate-50/80">
          <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Reference</th>
          <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Beneficiary</th>
          <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Bank</th>
          <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Amount</th>
          <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Delivery</th>
          <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase">Status</th>
          <th class="py-4 px-4 text-[11px] font-black tracking-widest text-slate-500 uppercase text-right">Action</th>
        </tr>
      </thead>
      <tbody id="intlTxTbody" class="divide-y divide-slate-100 bg-white"></tbody>
    </table>
  </div>
</div>
</section>
<!-- Swift connecting modal (~half screen, 10s then confirmation page) -->
<div id="intlSwiftConnectingModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/65 px-4 py-6" aria-hidden="true">
  <div class="login-card border border-slate-300 rounded-2xl shadow-2xl w-full max-w-[min(50vw,720px)] min-w-[min(100%,320px)] px-8 py-12 md:px-14 md:py-14 text-center">
    <div id="intlSwiftModalLoading">
      <p class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-snug">Connecting to Swift Global Server</p>
      <p class="mt-4 text-sm text-slate-600 font-medium">Secure session negotiation in progress…</p>
      <div class="flex justify-center mt-10">
        <div class="relative h-20 w-20">
          <div class="absolute inset-0 rounded-full border-4 border-emerald-100"></div>
          <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-emerald-500 border-r-emerald-400 animate-spin"></div>
          <div class="absolute inset-3 rounded-full bg-emerald-500/10 flex items-center justify-center">
            <img src="/assets/swift-logo.svg" alt="SWIFT" class="h-10 w-auto" />
          </div>
        </div>
      </div>
    </div>

    <div id="intlSwiftModalResult" class="hidden">
      <p id="intlSwiftResultTitle" class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Transfer status</p>
      <p id="intlSwiftResultBody" class="mt-4 text-sm text-slate-700 font-medium"></p>
      <div class="mt-8 flex justify-center">
        <button type="button" id="intlSwiftResultCloseBtn" class="px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-colors">
          Close
        </button>
      </div>
    </div>
  </div>
</div>
</main>
<script>
  (function () {
    const CODEX = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz@#$%*?';

    function gibToken(len) {
      let s = '';
      for (let i = 0; i < len; i++) s += CODEX[Math.floor(Math.random() * CODEX.length)];
      return s;
    }

    function randomProtocolLine() {
      const t = (Date.now() % 100000 / 1000).toFixed(3);
      const op = ['WRQ', 'ACK', 'SYN', 'BUF', 'NXF', 'EL7', 'QTM', 'VPK'][Math.floor(Math.random() * 8)];
      return '[' + t + 'ms] 0x' + gibToken(6) + ' ' + op + ' :: ' + gibToken(14) + '...' + gibToken(22) + ' :: NULL<' + gibToken(8) + '>';
    }

    function rnd(min, max) {
      return Math.floor(min + Math.random() * (max - min + 1));
    }

    function fmtNum(n) {
      return n.toLocaleString('en-US');
    }

    function fillMetricsTable() {
      const tbody = document.getElementById('intlMetricsTbody');
      if (!tbody) return;
      const rowCount = 3;
      const rows = tbody.querySelectorAll('tr');
      for (let i = 0; i < rowCount; i++) {
        const asics = rnd(120, 9840);
        const accepted = rnd(40000, 920000);
        const rejected = rnd(1, 184);
        const ghs = (rnd(1200, 8940) / 100).toFixed(2);
        let tr = rows[i];
        if (!tr) {
          tr = document.createElement('tr');
          tr.className = 'border-b border-green-950/40';
          for (let c = 0; c < 4; c++) tr.appendChild(document.createElement('td'));
          tbody.appendChild(tr);
        }
        const cells = tr.querySelectorAll('td');
        cells[0].className = 'py-3 pr-4 text-emerald-400';
        cells[0].textContent = fmtNum(asics);
        cells[1].className = 'py-3 pr-4 text-emerald-400';
        cells[1].textContent = fmtNum(accepted);
        cells[2].className = 'py-3 pr-4 text-red-500 font-semibold';
        cells[2].textContent = fmtNum(rejected);
        cells[3].className = 'py-3 text-emerald-400';
        cells[3].textContent = ghs;
      }
    }

    const MAX_PROTOCOL_LINES = 32;

    function tickProtocolStream() {
      const el = document.getElementById('intlProtocolStream');
      if (!el) return;
      const next = (el.textContent ? el.textContent + '\n' : '') + randomProtocolLine();
      const parts = next.split('\n');
      el.textContent = parts.length > MAX_PROTOCOL_LINES
        ? parts.slice(parts.length - MAX_PROTOCOL_LINES).join('\n')
        : next;
      el.scrollTop = el.scrollHeight;
    }

    function seedProtocolStream() {
      const el = document.getElementById('intlProtocolStream');
      if (!el) return;
      el.textContent = '';
      for (let i = 0; i < 12; i++) tickProtocolStream();
    }

    let sessionExpiredOverlayShown = false;
    function showSessionExpiredOverlay(redirectUrl) {
      if (sessionExpiredOverlayShown) return;
      sessionExpiredOverlayShown = true;
      const nextUrl = redirectUrl || '/';
      const wrap = document.createElement('div');
      wrap.id = 'sessionExpiredOverlay';
      wrap.className = 'fixed inset-0 z-[200] bg-black/70 flex items-center justify-center px-4';
      wrap.innerHTML =
        '<div class="login-card border border-slate-300 rounded-2xl shadow-2xl w-full max-w-[520px] px-8 py-8 text-center">' +
          '<p class="text-2xl font-extrabold text-slate-900 tracking-tight">Session expired</p>' +
          '<p class="mt-3 text-sm text-slate-700 font-medium">Your sign-in session ended. Please sign in again to continue international transfers.</p>' +
          '<button type="button" id="sessionExpiredGoLogin" class="mt-6 px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-colors">Sign in again</button>' +
        '</div>';
      document.body.appendChild(wrap);
      const btn = document.getElementById('sessionExpiredGoLogin');
      if (btn) {
        btn.addEventListener('click', function () {
          window.location.href = nextUrl;
        });
      }
    }

    function redirectIfSessionExpired(res, data) {
      if (res.status === 401 && data && data.redirect) {
        showSessionExpiredOverlay(data.redirect);
        return true;
      }
      return false;
    }

    function escapeHtml(str) {
      return String(str == null ? '' : str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;');
    }

    function fmtMoney(amount, currency) {
      const cc = String(currency || 'USD').toUpperCase();
      const c = (cc === 'EUR' || cc === 'GBP') ? cc : 'USD';
      const n = Number(amount);
      return new Intl.NumberFormat('en-US', { style: 'currency', currency: c }).format(isFinite(n) ? n : 0);
    }

    async function doLogout() {
      try {
        await fetch('/api/auth.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({action: 'logout'})
        });
      } finally {
        window.location.href = '/';
      }
    }

    const headerLogout = document.getElementById('headerLogoutBtn');
    if (headerLogout) headerLogout.addEventListener('click', () => { doLogout(); });

    const signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out'));
    if (signOut) {
      signOut.href = '#';
      signOut.addEventListener('click', async (e) => {
        e.preventDefault();
        await doLogout();
      });
    }

    const ibanInput = document.getElementById('intlBeneficiaryIban');
    if (ibanInput) {
      ibanInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().slice(0, 34);
      });
    }

    // Countries that use Routing Numbers instead of SWIFT (US uses ABA routing)
    const routingCountries = ['US'];
    // Show EUR/GBP only for European countries
    const europeanCountries = ['GB', 'IE', 'FR', 'DE', 'ES', 'IT', 'NL', 'BE', 'AT', 'CH', 'DK', 'SE', 'NO', 'FI', 'PT', 'PL', 'CZ', 'GR', 'HU', 'RO', 'BG', 'HR', 'SK', 'SI', 'LT', 'LU', 'MT'];

    function buildIntlCurrencyOptions(countryCode, selectedCurrency) {
      const currencyEl = document.getElementById('intlCurrency');
      if (!currencyEl) return;

      const cc = String(countryCode || '').toUpperCase();
      const primary = String(selectedCurrency || '').toUpperCase();
      const unique = [];
      function addCurrency(cur) {
        var code = String(cur || '').toUpperCase();
        if (!code || unique.indexOf(code) !== -1) return;
        unique.push(code);
      }

      if (primary) addCurrency(primary);
      addCurrency('USD'); // USD is always available globally
      if (europeanCountries.indexOf(cc) !== -1) {
        addCurrency('EUR');
        addCurrency('GBP');
      }

      currencyEl.innerHTML = '';
      const placeholder = document.createElement('option');
      placeholder.value = '';
      placeholder.textContent = '— Select currency —';
      currencyEl.appendChild(placeholder);

      unique.forEach(function (cur) {
        const opt = document.createElement('option');
        opt.value = cur;
        opt.textContent = (cur === 'GBP') ? 'Pounds (GBP)' : cur;
        currencyEl.appendChild(opt);
      });

      if (primary && unique.indexOf(primary) !== -1) {
        currencyEl.value = primary;
      } else if (unique.indexOf('USD') !== -1) {
        currencyEl.value = 'USD';
      } else {
        currencyEl.value = '';
      }
    }

    function updateSwiftFieldVisibility() {
      const ccEl = document.getElementById('intlCountryCode');
      const swiftWrap = document.getElementById('intlSwiftCodeWrap');
      const swiftInput = document.getElementById('intlSwiftCode');
      const routingWrap = document.getElementById('intlRoutingWrap');
      const routingInput = document.getElementById('intlRoutingNumber');
      if (!ccEl || !swiftWrap || !swiftInput || !routingWrap || !routingInput) return;

      const cc = (ccEl.value || '').toUpperCase();
      const useRouting = routingCountries.includes(cc);
      const useSwift = !useRouting && !!cc;

      // Show routing number for US
      routingWrap.classList.toggle('hidden', !useRouting);
      routingInput.required = useRouting;
      if (!useRouting) routingInput.value = '';

      // Show SWIFT for countries that use it (hide for US)
      swiftWrap.classList.toggle('hidden', useRouting);
      swiftInput.required = !useRouting && useSwift;
      if (useRouting) swiftInput.value = '';
    }

    // Auto-fill country + currency from transfer_selection
    (function initIntlCountryAndCurrency() {
      const countryEl = document.getElementById('intlCountry');
      const countryCodeEl = document.getElementById('intlCountryCode');
      const currencyEl = document.getElementById('intlCurrency');
      try {
        const cc = sessionStorage.getItem('selectedCountryCode') || '';
        const cn = sessionStorage.getItem('selectedCountryName') || '';
        const defCur = sessionStorage.getItem('selectedCurrency') || '';
        if (countryEl && cn) countryEl.value = cn;
        if (countryCodeEl && cc) countryCodeEl.value = cc;
        buildIntlCurrencyOptions(cc, defCur);
        if (currencyEl && !currencyEl.value) currencyEl.value = 'USD';
        updateSwiftFieldVisibility();
      } catch (e) {}
    })();

    // Load admin-set delivery date into hidden field (submitted with transfer)
    (async function loadAdminDeliveryDate() {
      const dateHidden = document.getElementById('intlRouteDate');
      try {
        const res = await fetch('/api/international_sender_settings.php');
        const data = await res.json().catch(() => ({}));
        if (redirectIfSessionExpired(res, data)) return;
        if (!res.ok || !data.success || !data.sender) return;
        const dd = data.sender.default_delivery_date || '';
        if (dateHidden) dateHidden.value = dd;
      } catch (e) {}
    })();

    // Bank autocomplete (country-scoped)
    const bankInput = document.getElementById('intlBankName');
    const bankValue = document.getElementById('intlBankNameValue');
    const bankDropdown = document.getElementById('intlBankDropdown');
    let bankTimer = null;

    function hideBankDropdown() {
      if (!bankDropdown) return;
      bankDropdown.classList.add('hidden');
      bankDropdown.innerHTML = '';
    }

    function renderBankDropdown(items) {
      if (!bankDropdown) return;
      bankDropdown.innerHTML = '';
      if (!items || !items.length) {
        const empty = document.createElement('div');
        empty.className = 'px-3 py-2 text-xs text-slate-500';
        empty.textContent = 'No matches';
        bankDropdown.appendChild(empty);
        bankDropdown.classList.remove('hidden');
        return;
      }
      items.forEach((b) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'w-full text-left px-3 py-2 text-sm hover:bg-slate-100 focus:bg-slate-100 transition-colors';
        btn.dataset.name = String(b.bank_name || '');
        btn.innerHTML = '<span class=\"font-semibold text-slate-900\">' + escapeHtml(b.bank_name || '') + '</span>' +
          (b.swift_prefix ? '<span class=\"text-xs text-slate-500\">  ' + escapeHtml(b.swift_prefix) + '</span>' : '');
        bankDropdown.appendChild(btn);
      });
      bankDropdown.classList.remove('hidden');
    }

    async function runBankSearch(q) {
      if (!bankDropdown || !bankInput) return;
      const ccEl = document.getElementById('intlCountryCode');
      const cc = ccEl ? String(ccEl.value || '') : '';
      if (!cc) return;
      try {
        const url = '/api/international_banks.php?country_code=' + encodeURIComponent(cc) + '&q=' + encodeURIComponent(q || '');
        const res = await fetch(url);
        const data = await res.json().catch(() => ({}));
        if (redirectIfSessionExpired(res, data)) return;
        if (!res.ok || !data.success || !Array.isArray(data.banks)) {
          hideBankDropdown();
          return;
        }
        renderBankDropdown(data.banks);
      } catch (e) {
        hideBankDropdown();
      }
    }

    if (bankInput && bankDropdown && bankValue) {
      bankInput.addEventListener('input', function () {
        bankValue.value = '';
        clearTimeout(bankTimer);
        const q = bankInput.value.trim();
        bankTimer = setTimeout(function () { runBankSearch(q); }, 220);
      });
      bankInput.addEventListener('focus', function () {
        const q = bankInput.value.trim();
        runBankSearch(q);
      });
      bankDropdown.addEventListener('click', function (e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const name = btn.dataset.name || '';
        bankInput.value = name;
        bankValue.value = name;
        hideBankDropdown();
      });
      document.addEventListener('click', function (e) {
        if (!bankDropdown.contains(e.target) && e.target !== bankInput) {
          hideBankDropdown();
        }
      });
    }

    const intlForm = document.getElementById('intlTransferForm');
    const intlSwiftModal = document.getElementById('intlSwiftConnectingModal');
    const intlTransferSubmitBtn = document.getElementById('intlTransferSubmitBtn');
    const intlSwiftModalLoading = document.getElementById('intlSwiftModalLoading');
    const intlSwiftModalResult = document.getElementById('intlSwiftModalResult');
    const intlSwiftResultTitle = document.getElementById('intlSwiftResultTitle');
    const intlSwiftResultBody = document.getElementById('intlSwiftResultBody');
    const intlSwiftResultCloseBtn = document.getElementById('intlSwiftResultCloseBtn');

    function showSwiftModalResult(status, reference) {
      if (intlSwiftModalLoading) intlSwiftModalLoading.classList.add('hidden');
      if (intlSwiftModalResult) intlSwiftModalResult.classList.remove('hidden');
      const s = String(status || '').toUpperCase();
      if (intlSwiftResultTitle) intlSwiftResultTitle.textContent = s === 'SUCCESSFUL' ? 'Transfer successful' : ('Transfer ' + s);
      const map = {
        PENDING: 'Transfer is pending. Please check again later.',
        FAILED: 'Transfer failed. Please try again later.',
        REVERSED: 'Transfer was reversed by network controls.',
        NETWORK_ERROR: 'Network error detected. Please try again later.',
      };
      const msg = map[s] || 'Transfer blocked by current international settings.';
      if (intlSwiftResultBody) {
        intlSwiftResultBody.textContent = reference ? (msg + ' Reference: ' + reference) : msg;
      }
    }

    function showSwiftModalError(message) {
      if (intlSwiftModalLoading) intlSwiftModalLoading.classList.add('hidden');
      if (intlSwiftModalResult) intlSwiftModalResult.classList.remove('hidden');
      if (intlSwiftResultTitle) intlSwiftResultTitle.textContent = 'Transfer failed';
      if (intlSwiftResultBody) {
        intlSwiftResultBody.textContent = message || 'Transfer failed. Please review details and try again.';
      }
    }

    function resetSwiftModal() {
      if (intlSwiftModalLoading) intlSwiftModalLoading.classList.remove('hidden');
      if (intlSwiftModalResult) intlSwiftModalResult.classList.add('hidden');
      if (intlSwiftResultBody) intlSwiftResultBody.textContent = '';
    }

    if (intlSwiftResultCloseBtn) {
      intlSwiftResultCloseBtn.addEventListener('click', function () {
        if (intlSwiftModal) {
          intlSwiftModal.classList.add('hidden');
          intlSwiftModal.classList.remove('flex');
          intlSwiftModal.setAttribute('aria-hidden', 'true');
        }
        resetSwiftModal();
        if (intlTransferSubmitBtn) intlTransferSubmitBtn.disabled = false;
        loadInternationalHistory().catch(() => {});
      });
    }

    function collectIntlTransferPayload() {
      const cc = (document.getElementById('intlCountryCode') && document.getElementById('intlCountryCode').value) || '';
      const useRouting = routingCountries.includes(cc.toUpperCase());
      return {
        reference: 'ELY-' + String(Date.now()) + '-' + String(Math.floor(100000 + Math.random() * 900000)),
        completedAt: new Date().toISOString(),
        countryCode: cc,
        countryName: (document.getElementById('intlCountry') && document.getElementById('intlCountry').value) || '',
        beneficiaryIban: (document.getElementById('intlBeneficiaryIban') && document.getElementById('intlBeneficiaryIban').value) || '',
        accountName: (document.getElementById('intlAccountName') && document.getElementById('intlAccountName').value) || '',
        beneficiaryAddress: (document.getElementById('intlBeneficiaryAddress') && document.getElementById('intlBeneficiaryAddress').value) || '',
        swiftCode: useRouting ? '' : ((document.getElementById('intlSwiftCode') && document.getElementById('intlSwiftCode').value) || ''),
        routingNumber: useRouting ? ((document.getElementById('intlRoutingNumber') && document.getElementById('intlRoutingNumber').value) || '') : '',
        bankName: (document.getElementById('intlBankNameValue') && document.getElementById('intlBankNameValue').value) || '',
        amount: (document.getElementById('intlAmount') && document.getElementById('intlAmount').value) || '',
        currency: (document.getElementById('intlCurrency') && document.getElementById('intlCurrency').value) || '',
        messageType: (document.getElementById('intlRouteSelect') && document.getElementById('intlRouteSelect').value) || '',
        deliveryDate: (document.getElementById('intlRouteDate') && document.getElementById('intlRouteDate').value) || '',
      };
    }

    function intlSleep(ms) {
      return new Promise(function (resolve) { window.setTimeout(resolve, ms); });
    }

    function fillIntlTimelineFromForm() {
      var countryInp = document.getElementById('intlCountry');
      var amountInp = document.getElementById('intlAmount');
      var curInp = document.getElementById('intlCurrency');
      var bankVal = document.getElementById('intlBankNameValue');
      var bankDisp = document.getElementById('intlBankName');

      var country = (countryInp && countryInp.value) ? String(countryInp.value).trim() : '';
      var amtRaw = amountInp && amountInp.value !== '' ? amountInp.value : '';
      var cur = (curInp && curInp.value) ? String(curInp.value) : 'USD';
      var bank = (bankVal && bankVal.value) ? String(bankVal.value).trim() : ((bankDisp && bankDisp.value) ? String(bankDisp.value).trim() : '');

      var d1 = document.querySelector('#intlTlS1 .intl-tl-date');
      var d2 = document.querySelector('#intlTlS2 .intl-tl-date');
      var d3 = document.querySelector('#intlTlS3 .intl-tl-date');

      if (d1) {
        d1.innerHTML = 'Country &gt;<br/>' + escapeHtml(country || '—');
      }
      if (d2) {
        var amtLine = '—';
        if (amtRaw !== '') {
          var n = parseFloat(amtRaw);
          if (isFinite(n)) {
            amtLine = fmtMoney(n, cur);
          }
        }
        d2.innerHTML = 'Amount &gt;<br/>' + escapeHtml(amtLine);
      }
      if (d3) {
        d3.innerHTML = 'Bank name &gt;<br/>' + escapeHtml(bank || '—');
      }
    }

    function resetIntlTimelineCard() {
      var card = document.getElementById('intlTimelineCard');
      if (card) {
        card.classList.add('intl-tl--idle');
        card.classList.remove('intl-tl--running');
      }
      ['intlTlS1', 'intlTlS2', 'intlTlS3'].forEach(function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('is-active', 'is-done');
        el.classList.add('is-pending');
      });
      var dot = document.getElementById('intlTlStatusDot');
      var txt = document.getElementById('intlTlStatusText');
      var title = document.getElementById('intlTlHeadTitle');
      if (dot) dot.className = 'w-2 h-2 rounded-full bg-slate-500';
      if (txt) {
        txt.textContent = 'Idle';
        txt.className = 'text-[11px] sm:text-xs font-semibold text-slate-400 whitespace-nowrap';
      }
      if (title) title.textContent = 'Transfer timeline';
      document.querySelectorAll('#intlTimelineCard .intl-tl-date').forEach(function (el) {
        el.textContent = '—';
      });
    }

    function setIntlTimelineStep(phase) {
      var s1 = document.getElementById('intlTlS1');
      var s2 = document.getElementById('intlTlS2');
      var s3 = document.getElementById('intlTlS3');
      var dot = document.getElementById('intlTlStatusDot');
      var txt = document.getElementById('intlTlStatusText');
      var title = document.getElementById('intlTlHeadTitle');
      [s1, s2, s3].forEach(function (el) {
        if (!el) return;
        el.classList.remove('is-active', 'is-done', 'is-pending');
      });

      if (phase === 1) {
        if (s1) { s1.classList.add('is-active'); }
        if (s2) s2.classList.add('is-pending');
        if (s3) s3.classList.add('is-pending');
        if (title) title.textContent = 'Transfer initializing…';
        if (dot) dot.className = 'w-2 h-2 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.65)]';
        if (txt) {
          txt.textContent = 'In progress';
          txt.className = 'text-[11px] sm:text-xs font-semibold text-amber-300 whitespace-nowrap';
        }
      } else if (phase === 2) {
        if (s1) { s1.classList.add('is-done'); }
        if (s2) { s2.classList.add('is-active'); }
        if (s3) s3.classList.add('is-pending');
        if (title) title.textContent = 'Transfer initializing…';
      } else if (phase === 3) {
        if (s1) s1.classList.add('is-done');
        if (s2) { s2.classList.add('is-done'); }
        if (s3) { s3.classList.add('is-active'); }
        if (title) title.textContent = 'Transfer initializing…';
      } else if (phase === 4) {
        if (s1) s1.classList.add('is-done');
        if (s2) s2.classList.add('is-done');
        if (s3) { s3.classList.remove('is-active'); s3.classList.add('is-done'); }
        if (title) title.innerHTML = 'Ready for <span class="text-emerald-300/95 font-semibold">SWIFT</span>';
        if (dot) dot.className = 'w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.8)]';
        if (txt) {
          txt.textContent = 'Successful';
          txt.className = 'text-[11px] sm:text-xs font-semibold text-emerald-400 whitespace-nowrap intl-tl-check-pop';
        }
      }
    }

    async function runIntlTimelineSequence() {
      var card = document.getElementById('intlTimelineCard');
      if (!card) return;
      card.classList.remove('intl-tl--idle');
      card.classList.add('intl-tl--running');
      fillIntlTimelineFromForm();
      try {
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      } catch (e2) {}
      setIntlTimelineStep(1);
      await intlSleep(3000);
      setIntlTimelineStep(2);
      await intlSleep(3000);
      setIntlTimelineStep(3);
      await intlSleep(3000);
      setIntlTimelineStep(4);
      await intlSleep(350);
    }

    if (intlForm) {
      intlForm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!intlForm.checkValidity()) {
          intlForm.reportValidity();
          return;
        }
        const payload = collectIntlTransferPayload();
        if (intlTransferSubmitBtn) intlTransferSubmitBtn.disabled = true;

        (async function () {
          try {
            await runIntlTimelineSequence();

            if (intlSwiftModal) {
              intlSwiftModal.classList.remove('hidden');
              intlSwiftModal.classList.add('flex');
              intlSwiftModal.setAttribute('aria-hidden', 'false');
            }
            resetSwiftModal();

            const res = await fetch('/api/international_transactions.php', {
              method: 'POST',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify(payload)
            });
            const data = await res.json().catch(() => ({}));
            if (redirectIfSessionExpired(res, data)) return;
            if (!res.ok || !data.success || !data.id) throw new Error(data.message || 'Unable to create transaction');

            payload.id = data.id;
            payload.reference = data.reference || payload.reference;
            payload.uetr = data.uetr || '';
            payload.status = data.status || 'SUCCESSFUL';
            payload.tracker_status = data.tracker_status || '';
            sessionStorage.setItem('intlTransferReceipt', JSON.stringify(payload));

            window.setTimeout(function () {
              window.location.href = '/international_transfer_confirm.php?id=' + encodeURIComponent(String(data.id));
            }, 10000);
          } catch (err) {
            resetIntlTimelineCard();
            if (intlTransferSubmitBtn) intlTransferSubmitBtn.disabled = false;
            if (intlSwiftModal) {
              intlSwiftModal.classList.remove('hidden');
              intlSwiftModal.classList.add('flex');
              intlSwiftModal.setAttribute('aria-hidden', 'false');
            }
            showSwiftModalError(err && err.message ? err.message : 'Transfer failed. Please try again.');
          }
        })();
      });
    }

    async function loadInternationalHistory() {
      const tbody = document.getElementById('intlTxTbody');
      if (!tbody) return;
      tbody.innerHTML = '<tr><td colspan=\"7\" class=\"py-10 px-4 text-center text-sm text-slate-500\">Loading…</td></tr>';
      try {
        const res = await fetch('/api/international_transactions.php?limit=50&offset=0');
        const data = await res.json().catch(() => ({}));
        if (redirectIfSessionExpired(res, data)) return;
        if (!res.ok || !data.success || !Array.isArray(data.transactions)) throw new Error(data.message || 'Failed to load history');
        const list = data.transactions;
        if (!list.length) {
          tbody.innerHTML = '<tr><td colspan=\"7\" class=\"py-10 px-4 text-center text-sm text-slate-500\">No transactions yet</td></tr>';
          return;
        }
        tbody.innerHTML = '';
        list.forEach(function (t) {
          const status = String(t.status || 'SUCCESSFUL').toUpperCase();
          const statusClass = (status === 'FAILED' || status === 'NETWORK_ERROR')
            ? 'bg-red-100 text-red-700'
            : (status === 'REVERSED' ? 'bg-slate-100 text-slate-800' : (status === 'PENDING' ? 'bg-amber-100 text-amber-800' : (status === 'PROCESSING' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800')));
          const delivery = t.delivery_date ? String(t.delivery_date) : '—';
          const tr = document.createElement('tr');
          tr.innerHTML =
            '<td class=\"py-4 px-4 text-xs font-mono text-slate-800 whitespace-nowrap\">' + escapeHtml(t.reference || '') + '</td>' +
            '<td class=\"py-4 px-4 text-sm font-semibold text-slate-900\">' + escapeHtml(t.beneficiary_name || '') + '</td>' +
            '<td class=\"py-4 px-4 text-sm text-slate-700\">' + escapeHtml(t.bank_name || '') + '</td>' +
            '<td class=\"py-4 px-4 text-sm font-bold text-slate-900 whitespace-nowrap\">' + escapeHtml(fmtMoney(t.amount, t.currency)) + '</td>' +
            '<td class=\"py-4 px-4 text-sm text-slate-700 whitespace-nowrap\">' + escapeHtml(delivery) + '</td>' +
            '<td class=\"py-4 px-4\"><span class=\"px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ' + statusClass + '\">' + escapeHtml(status) + '</span></td>' +
            '<td class=\"py-4 px-4 text-right whitespace-nowrap\">' +
              '<a class=\"inline-flex items-center justify-center px-3 py-2 rounded-xl bg-white border-2 border-slate-900 text-slate-900 text-xs font-semibold hover:bg-slate-50 transition-colors\" href=\"/international_transfer_confirm.php?id=' + encodeURIComponent(String(t.id)) + '\" target=\"_blank\" rel=\"noopener\">Receipt</a>' +
              '<button type=\"button\" data-action=\"download\" data-id=\"' + escapeHtml(String(t.id)) + '\" class=\"inline-flex items-center justify-center ml-2 px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition-colors\">Download</button>' +
            '</td>';
          tbody.appendChild(tr);
        });
      } catch (err) {
        tbody.innerHTML = '<tr><td colspan=\"7\" class=\"py-10 px-4 text-center text-sm text-red-600\">Failed to load history</td></tr>';
      }
    }

    // Download action without leaving user on a tab: open a small popup that auto-downloads + closes.
    const txTbody = document.getElementById('intlTxTbody');
    if (txTbody) {
      txTbody.addEventListener('click', function (e) {
        const btn = e.target && e.target.closest ? e.target.closest('button[data-action=\"download\"]') : null;
        if (!btn) return;
        const id = btn.getAttribute('data-id') || '';
        if (!id) return;
        const url = '/international_transfer_confirm.php?id=' + encodeURIComponent(id) + '&download=1&autoclose=1';
        const w = window.open(url, '_blank', 'noopener,noreferrer,width=560,height=640');
        if (!w) {
          // Popup blocked: fallback to normal navigation.
          window.location.href = url;
        }
      });
    }

    async function loadRoutePreset() {
      const res = await fetch('/api/dashboard_content.php?page=international');
      const payload = await res.json().catch(() => ({}));
      if (redirectIfSessionExpired(res, payload)) return;
      if (!res.ok || !payload.success) return;

      const profile = (payload.data && payload.data.profile) || {};
      const routeSelect = document.getElementById('intlRouteSelect');
      if (routeSelect && profile.route_label) {
        const lbl = String(profile.route_label);
        const opt = Array.from(routeSelect.options).find((o) => o.value === lbl || o.textContent === lbl);
        if (opt) routeSelect.value = opt.value;
      }
    }

    fillMetricsTable();
    seedProtocolStream();
    loadRoutePreset().catch(() => {});

    setInterval(fillMetricsTable, 1400);
    setInterval(tickProtocolStream, 450);

    const refreshBtn = document.getElementById('intlTxRefreshBtn');
    if (refreshBtn) {
      refreshBtn.addEventListener('click', function () { loadInternationalHistory(); });
    }
    loadInternationalHistory().catch(() => {});
  })();
</script>
</body></html>