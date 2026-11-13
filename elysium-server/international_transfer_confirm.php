<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>SWIFT MT103 Message</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<style>
  * { font-family: "JetBrains Mono", "Courier New", monospace; margin: 0; padding: 0; box-sizing: border-box; }
  body { font-size: 11px; line-height: 1.35; background: #e8edf2; }

  @keyframes summaryCheckDraw {
    0% { transform: scale(0) rotate(-12deg); opacity: 0; }
    55% { transform: scale(1.12) rotate(4deg); opacity: 1; }
    100% { transform: scale(1) rotate(0deg); opacity: 1; }
  }
  .summary-check-ring {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(145deg, #10b981, #059669);
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.45);
    flex-shrink: 0;
    position: relative;
    z-index: 3;
    animation: summaryCheckDraw 0.65s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
  }
  .summary-check-ring svg {
    width: 26px;
    height: 26px;
    color: #fff;
  }
  .summary-check-ring.pending-processing {
    background: linear-gradient(145deg, #f59e0b, #d97706);
    box-shadow: 0 4px 14px rgba(217, 119, 6, 0.45);
    animation: summaryPendingSpin 1.25s linear infinite;
  }
  .summary-check-ring.processing-active {
    background: linear-gradient(145deg, #0ea5e9, #0284c7);
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.45);
    animation: summaryPendingSpin 1.1s linear infinite;
  }
  @keyframes summaryPendingSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  #summaryStatusCard.summary-card-shell {
    border-radius: 16px;
    overflow: visible;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.12), 0 1px 0 rgba(255,255,255,0.9) inset;
    border: 2px solid #e2e8f0;
  }
  #summaryStatusCard .summary-card-body {
    border-radius: 0 0 14px 14px;
    overflow: hidden;
  }
  #summaryStatusBanner.summary-banner-row {
    display: block;
    padding: 18px 20px 26px;
    font-weight: 800;
    font-size: 1.125rem;
    letter-spacing: 0.03em;
    line-height: 1.25;
    position: relative;
    overflow: hidden;
  }
  .summary-banner-stack {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 12px;
    width: 100%;
    z-index: 1;
  }
  #summaryBannerTitle {
    display: block;
    width: 100%;
    text-align: center;
    font-size: 1.2rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    line-height: 1.25;
  }
  .summary-icon-celebrate {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 52px;
    min-width: 52px;
  }
  .summary-confetti-root {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 0;
    height: 0;
    pointer-events: none;
    z-index: 2;
  }
  .summary-confetti-bit {
    position: absolute;
    left: 0;
    top: 0;
    width: 5px;
    height: 7px;
    border-radius: 1px;
    opacity: 0;
    margin-left: -2.5px;
    margin-top: -3.5px;
    animation-name: summaryConfettiFall;
    animation-timing-function: cubic-bezier(0.22, 1, 0.36, 1);
    animation-fill-mode: forwards;
  }
  @keyframes summaryConfettiFall {
    0% {
      opacity: 1;
      transform: translate3d(0, -6px, 0) rotate(0deg) scale(1);
    }
    100% {
      opacity: 0;
      transform: translate3d(var(--dx), var(--dy), 0) rotate(var(--spin)) scale(0.6);
    }
  }
  .summary-pulse-ring {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 52px;
    height: 52px;
    margin-left: -26px;
    margin-top: -26px;
    border-radius: 50%;
    border: 2px solid rgba(16, 185, 129, 0.55);
    opacity: 0;
    pointer-events: none;
    z-index: 0;
  }
  #summaryIconWrap.is-celebrating .summary-pulse-ring {
    animation: summaryPulseRing 1.85s ease-out infinite;
  }
  @keyframes summaryPulseRing {
    0% { transform: scale(0.72); opacity: 0.95; }
    70% { opacity: 0.35; }
    100% { transform: scale(1.65); opacity: 0; }
  }
  #fullReceiptWrap.receipt-pattern-bg {
    padding: 28px 18px 36px;
    border-radius: 0 0 14px 14px;
    background-color: #d4e4ee;
    background-image:
      radial-gradient(ellipse 120% 80% at 50% -20%, rgba(255,255,255,0.75), transparent 55%),
      repeating-linear-gradient(135deg, rgba(6, 95, 70, 0.045) 0, rgba(6, 95, 70, 0.045) 1px, transparent 1px, transparent 10px),
      repeating-linear-gradient(45deg, rgba(15, 23, 42, 0.04) 0, rgba(15, 23, 42, 0.04) 1px, transparent 1px, transparent 14px),
      linear-gradient(180deg, rgba(255,255,255,0.5) 0%, transparent 35%);
  }
  
  /* A4 Page dimensions: 210mm x 297mm */
  .a4-page {
    width: 210mm;
    min-height: 297mm;
    background: #fff;
    margin: 20px auto;
    padding: 15mm 18mm;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    position: relative;
    page-break-after: always;
  }
  .a4-page:last-child {
    page-break-after: auto;
  }
  
  .page-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 350px;
    height: 350px;
    opacity: 0.03;
    pointer-events: none;
    z-index: 0;
  }
  
  .page-content {
    position: relative;
    z-index: 1;
    min-height: calc(297mm - 30mm - 25mm); /* page height - padding - footer */
  }
  
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
    margin-bottom: 10px;
  }
  
  .page-footer {
    position: absolute;
    bottom: 15mm;
    left: 18mm;
    right: 18mm;
    border-top: 1px solid #ccc;
    padding-top: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 9px;
    color: #666;
  }
  
  .hidden { display: none !important; }

  @media print {
    .no-print { display: none !important; }
    #summaryStatusCard.summary-card-shell { overflow: hidden; }
    body { background: #fff !important; margin: 0; padding: 0; }
    .a4-page { 
      box-shadow: none !important; 
      margin: 0; 
      width: 100%;
      min-height: auto;
      page-break-after: always;
    }
    .a4-page:last-child { page-break-after: auto; }
  }
  
  .section-line {
    border-top: 2px solid #000;
    margin: 8px 0 5px 0;
    position: relative;
  }
  .section-line span {
    position: absolute;
    top: -8px;
    left: 0;
    background: #fff;
    padding-right: 8px;
    font-weight: 800;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .field-row {
    display: flex;
    margin: 1px 0;
    font-size: 10px;
  }
  .field-label {
    min-width: 280px;
    font-weight: 700;
    color: #333;
  }
  .field-value {
    font-weight: 600;
    color: #000;
    flex: 1;
  }
  .data-block {
    margin: 2px 0;
    padding-left: 15px;
    font-size: 10px;
  }
  .mt-field {
    margin: 6px 0;
  }
  .mt-field-header {
    font-weight: 800;
    text-decoration: underline;
    margin-bottom: 1px;
    font-size: 10px;
  }
  .stamp { transform: rotate(-12deg); }

  /* Page 2 — disclaimer & transaction notice (print-safe) */
  .receipt-legal-box {
    margin-top: 20px;
    padding: 12px 14px;
    background: linear-gradient(180deg, #f0f0f0 0%, #e4e4e4 100%);
    border: 2px solid #9ca3af;
    border-radius: 2px;
    font-size: 8.5px;
    line-height: 1.55;
    color: #2d2d2d;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
  }
  .receipt-legal-title {
    font-weight: 900;
    font-size: 9px;
    letter-spacing: 0.06em;
    color: #0a0a0a;
    margin: 0 0 5px 0;
    text-transform: uppercase;
  }
  .receipt-legal-title + .receipt-legal-body {
    margin-top: 0;
  }
  .receipt-legal-body {
    font-weight: 700;
    color: #1f2937;
    margin: 0;
  }
  .receipt-legal-body strong {
    font-weight: 900;
    color: #030712;
  }
  .receipt-legal-divider {
    margin: 10px 0 8px;
    padding-top: 8px;
    border-top: 1px solid #9ca3af;
  }
</style>
</head>
<body>

<header class="no-print" style="max-width: 210mm; margin: 20px auto 10px; display: flex; justify-content: space-between; align-items: center; padding: 0 18mm;">
  <a href="/international_dashboard.php" style="font-size: 12px; font-weight: bold; color: #1e3a8a; text-decoration: none;">&larr; Back to Dashboard</a>
  <img src="/assets/swift-logo.svg" alt="" class="js-receipt-brand-logo" style="height: 28px;" />
</header>

<div id="confirmationSummary" class="no-print" style="max-width: 560px; margin: 0 auto 24px; padding: 0 20px;">
  <div id="summaryStatusCard" class="summary-card-shell">
    <div id="summaryStatusBanner" class="summary-banner-row">
      <div class="summary-banner-stack">
        <span id="summaryBannerTitle">Transfer status</span>
        <div class="summary-icon-celebrate" id="summaryIconWrap">
          <div id="summaryConfettiRoot" class="summary-confetti-root" aria-hidden="true"></div>
          <span class="summary-pulse-ring" aria-hidden="true"></span>
          <span id="summaryBannerIcon" class="summary-check-ring hidden" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
          </span>
        </div>
      </div>
    </div>
    <div class="summary-card-body" style="padding: 20px 20px 22px; font-family: Inter, system-ui, sans-serif;">
      <p style="font-size: 12px; color: #64748b; margin-bottom: 14px; line-height: 1.5;" id="summaryStatusLead">Review your transfer details below. Download the official MT103 receipt when ready.</p>
      <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
        <tbody>
          <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 8px 0; color: #64748b; font-weight: 600;">Reference</td><td style="padding: 8px 0; text-align: right; font-weight: 700; color: #0f172a;" id="sumReference">—</td></tr>
          <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 8px 0; color: #64748b; font-weight: 600;">Initiated</td><td style="padding: 8px 0; text-align: right; font-weight: 600; color: #0f172a;" id="sumInitiated">—</td></tr>
          <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 8px 0; color: #64748b; font-weight: 600;">Estimated delivery</td><td style="padding: 8px 0; text-align: right; font-weight: 600; color: #0f172a;" id="sumDelivery">—</td></tr>
          <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 8px 0; color: #64748b; font-weight: 600;">Beneficiary</td><td style="padding: 8px 0; text-align: right; font-weight: 600; color: #0f172a;" id="sumBeneficiary">—</td></tr>
          <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 8px 0; color: #64748b; font-weight: 600;">Bank</td><td style="padding: 8px 0; text-align: right; font-weight: 600; color: #0f172a;" id="sumBank">—</td></tr>
          <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 8px 0; color: #64748b; font-weight: 600;">Account / IBAN</td><td style="padding: 8px 0; text-align: right; font-weight: 600; color: #0f172a; word-break: break-all;" id="sumAccount">—</td></tr>
          <tr><td style="padding: 8px 0; color: #64748b; font-weight: 600;">Amount</td><td style="padding: 8px 0; text-align: right; font-weight: 800; font-size: 15px; color: #065f46;" id="sumAmount">—</td></tr>
        </tbody>
      </table>
      <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px;">
        <button type="button" id="btnToggleFullReceipt" style="padding: 10px 18px; background: #f8fafc; color: #0f172a; font-size: 12px; font-weight: 700; border: 2px solid #cbd5e1; border-radius: 8px; cursor: pointer;">View full receipt</button>
        <button type="button" id="btnDownloadPdf" style="padding: 10px 18px; background: #000; color: #fff; font-size: 12px; font-weight: 700; border: none; border-radius: 8px; cursor: pointer;">Download receipt (PDF)</button>
        <button type="button" id="btnPrintReceipt" style="padding: 10px 18px; background: #fff; color: #000; font-size: 12px; font-weight: 700; border: 2px solid #000; border-radius: 8px; cursor: pointer;">Print receipt</button>
      </div>
    </div>
  </div>
</div>

<div id="fullReceiptWrap" class="hidden receipt-pattern-bg">
<!-- PAGE 1 -->
<div class="a4-page" id="page1">
  <img src="/assets/swift-logo.svg" alt="" class="page-watermark js-receipt-brand-logo" />
  
  <div class="page-content">
    <!-- PAGE 1 HEADER -->
    <div class="page-header">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
          <img src="/assets/swift-logo.svg" alt="" class="js-receipt-brand-logo" style="height: 38px; max-width: 43px; object-fit: contain;" />
        </div>
        <div>
          <div id="rv_header_bank" style="font-size: 14px; font-weight: 800;">SWIFT MESSAGE</div>
          <div style="font-size: 8px; font-weight: 600; color: #666;">SOCIETY FOR WORLDWIDE INTERBANK FINANCIAL TELECOMMUNICATION</div>
        </div>
      </div>
      <div style="text-align: right;">
        <svg id="receiptBarcode" style="height: 40px; width: 160px;"></svg>
        <div id="rv_barcode_num" style="font-size: 8px; font-weight: 600; color: #666;"></div>
      </div>
    </div>

    <!-- REPORT METADATA -->
    <div style="font-size: 10px; margin-bottom: 8px;">
      <div class="field-row"><span class="field-label">Report time zone:</span><span id="rv_timezone" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">Delivery date:</span><span id="rv_delivery_date_header" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">Message reference:</span><span id="rv_msg_ref" class="field-value"></span></div>
    </div>

    <!-- TRANSMISSION SECTION -->
    <div class="section-line"><span>TRANSMISSION</span></div>
    <div style="font-size: 10px;">
      <div class="field-row"><span class="field-label">FIN MT103 SINGLE CUSTOMER CREDIT TRANSFER</span></div>
      <div class="field-row"><span class="field-label">RCVD+DATE: INPUT TIME –</span><span id="rv_input_time" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+SENDER:</span><span id="rv_sender_swift_code" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+SENDER'S BANK NAME:</span><span id="rv_sender_bank_name" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+SENDER'S BANK ADDRESS:</span><span id="rv_sender_bank_addr" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVS+SENDER'S ACCOUNT NAME:</span><span id="rv_sender_acct_name" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVS+SENDER'S ACCOUNT:</span><span id="rv_sender_acct_no" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVS+SENDER'S IBAN:</span><span id="rv_sender_iban" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+SENDER'S SWIFT CODE:</span><span id="rv_sender_swift_full" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+SENDER'S UETR CODE:</span><span id="rv_uetr" class="field-value"></span></div>
    </div>

    <!-- INSTANT TYPE AND TRANSMISSION -->
    <div class="section-line"><span>Instant type and transmission</span></div>
    <div style="font-size: 10px;">
      <div class="field-row"><span class="field-label">RCVD+NOTIFICATION (TRANSMISSION) OF ORIGINAL SENT</span></div>
      <div class="field-row"><span class="field-label">SECURITY CONFIRMATION CODE:</span><span id="rv_security_code" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+BRANCH NETWORK:</span><span id="rv_branch_network" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+MESSAGE INPUT REFERENCE:</span><span id="rv_input_ref" class="field-value"></span></div>
    </div>

    <!-- MESSAGE HEADER -->
    <div class="section-line"><span>Message Header</span></div>
    <div style="font-size: 10px;">
      <div class="field-row"><span class="field-label">RCVD+SWIFT MESSAGE TYPE:</span><span class="field-value">ACK 103 BOX NETWORK</span></div>
      <div class="field-row"><span class="field-label">RCVD+FORMAT MESSAGE:</span><span id="rv_format_msg" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+RECEIVER'S BANK:</span><span id="rv_receiver_bank" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+RECEIVER'S BANK ADDRESS:</span><span id="rv_receiver_bank_addr" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+RECEIVER'S ACCOUNT NAME:</span><span id="rv_receiver_acct_name" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+RECEIVER'S ACCOUNT NO:</span><span id="rv_receiver_acct_no" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+RECEIVER'S IBAN:</span><span id="rv_receiver_iban" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+RECEIVER'S SWIFT CODE:</span><span id="rv_receiver_swift" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+MESSAGE OUTPUT REFERENCE:</span><span id="rv_output_ref" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">SESSION:</span><span id="rv_session" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">SEQUENCE:</span><span id="rv_sequence" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+DATE:</span><span id="rv_rcvd_date" class="field-value"></span></div>
      <div class="field-row"><span class="field-label">RCVD+VALUE AMOUNT:</span><span id="rv_value_amount" class="field-value" style="font-weight: 800;"></span></div>
    </div>

    <!-- MESSAGE TEXT START -->
    <div class="section-line"><span>Message Text</span></div>
    <div style="font-size: 10px;">
      <div class="field-row"><span class="field-label">RCVD+ACK:</span><span class="field-value">SWIFT AUTHENTICATION CORRECT TRN21</span></div>
      <div class="field-row"><span class="field-label">RCVD+00:</span><span class="field-value">EMBEDDED MESSAGE INITIALIZED</span></div>
      
      <div class="mt-field">
        <div class="mt-field-header">F20 TRANSACTION REFERENCE NUMBER:</div>
        <div id="rv_f20" class="data-block" style="font-weight: 700;"></div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F23B BANK OPERATION CODE:</div>
        <div class="data-block">CRED</div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F32A VALUE DATE/CURRENCY/INTERBANK SETTLED AMOUNT:</div>
        <div id="rv_f32a" class="data-block"></div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F33B CURRENCY/INSTRUCTED AMOUNT:</div>
        <div id="rv_f33b" class="data-block"></div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F50A ORDERING CUSTOMER:</div>
        <div class="data-block">
          <div>IBAN: <span id="rv_f50a_iban"></span></div>
          <div>SWIFT CODE: <span id="rv_f50a_swift"></span></div>
          <div id="rv_f50a_name" style="font-weight: 700;"></div>
          <div id="rv_f50a_addr"></div>
        </div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F52A ORDERING INSTITUTION:</div>
        <div class="data-block">
          <div>SWIFT CODE: <span id="rv_f52a_swift"></span></div>
          <div id="rv_f52a_name" style="font-weight: 700;"></div>
          <div id="rv_f52a_addr"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- PAGE 1 FOOTER -->
  <div class="page-footer">
    <div><span style="font-weight: 700;">DOC:</span> MT103-SCCT-CONF | <span style="font-weight: 700;">REF:</span> <span id="rv_footer_ref1"></span></div>
    <div>Page 1 of 2</div>
    <div id="rv_footer_timestamp1"></div>
  </div>
</div>

<!-- PAGE 2 -->
<div class="a4-page" id="page2">
  <img src="/assets/swift-logo.svg" alt="" class="page-watermark js-receipt-brand-logo" />
  
  <div class="page-content">
    <!-- PAGE 2 HEADER -->
    <div class="page-header">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
          <img src="/assets/swift-logo.svg" alt="" class="js-receipt-brand-logo" style="height: 38px; max-width: 43px; object-fit: contain;" />
        </div>
        <div>
          <div id="rv_header_bank2" style="font-size: 14px; font-weight: 800;">SWIFT MESSAGE</div>
          <div style="font-size: 8px; font-weight: 600; color: #666;">MESSAGE TEXT (CONTINUED)</div>
        </div>
      </div>
      <div style="text-align: right; font-size: 9px;">
        <div style="font-weight: 700;">Transaction Reference:</div>
        <div id="rv_header_ref2" style="font-weight: 800; font-size: 11px;"></div>
      </div>
    </div>

    <div style="font-size: 10px;">
      <div class="mt-field">
        <div class="mt-field-header">F53A SENDER'S CORRESPONDENT BANK:</div>
        <div class="data-block">
          <div>SWIFT CODE: <span id="rv_f53a_swift"></span></div>
          <div id="rv_f53a_name"></div>
          <div id="rv_f53a_addr"></div>
        </div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F54A RECEIVER'S CORRESPONDENT BANK:</div>
        <div class="data-block">
          <div>SWIFT CODE: <span id="rv_f54a_swift"></span></div>
          <div id="rv_f54a_name"></div>
          <div id="rv_f54a_addr"></div>
        </div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F56A INTERMEDIARY INSTITUTION:</div>
        <div class="data-block">
          <div>SWIFT CODE: <span id="rv_f56a_swift"></span></div>
          <div id="rv_f56a_name"></div>
        </div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F57A ACCOUNT WITH INSTITUTION:</div>
        <div class="data-block">
          <div>SWIFT CODE: <span id="rv_f57a_swift"></span></div>
          <div id="rv_f57a_name" style="font-weight: 700;"></div>
          <div id="rv_f57a_addr"></div>
        </div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F59A BENEFICIARY CUSTOMER/ADDRESS:</div>
        <div class="data-block">
          <div>IBAN: <span id="rv_f59_iban"></span></div>
          <div>SWIFT CODE: <span id="rv_f59_swift"></span></div>
          <div id="rv_f59_name" style="font-weight: 700;"></div>
          <div id="rv_f59_addr" style="white-space: pre-line;"></div>
        </div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F70 REMITTANCE INFORMATION:</div>
        <div id="rv_f70" class="data-block"></div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F71A DETAILS OF CHARGES:</div>
        <div class="data-block">OUR</div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F71F SENDER'S CHARGES:</div>
        <div id="rv_f71f" class="data-block"></div>
      </div>

      <div class="mt-field">
        <div class="mt-field-header">F72 SENDER TO RECEIVER INFORMATION:</div>
        <div id="rv_f72" class="data-block" style="font-size: 9px; line-height: 1.4;"></div>
      </div>
    </div>

    <!-- AUTHORIZATION -->
    <div class="section-line"><span>Authorization</span></div>
    <div style="font-size: 10px; margin-top: 8px;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <div style="font-weight: 700;">FOR AND ON BEHALF OF:</div>
          <div id="rv_auth_bank" style="font-weight: 800;"></div>
          <div id="rv_auth_addr" style="font-size: 9px;"></div>
        </div>
        <div style="width: 70px; height: 70px; border: 2px solid #888; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; transform: rotate(-12deg); opacity: 0.7;">
          <div style="font-size: 7px; font-weight: 800;">VERIFIED</div>
          <div style="font-size: 8px; font-weight: 800; text-align: center;">SWIFT</div>
          <div id="rv_stamp_date" style="font-size: 6px; font-weight: 600;"></div>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 15px;">
        <div>
          <div id="rv_auth_name1" style="font-weight: 700;"></div>
          <div id="rv_auth_title1" style="font-size: 9px;"></div>
          <div style="font-size: 9px;">PIN: <span id="rv_auth_pin1"></span></div>
        </div>
        <div>
          <div id="rv_auth_name2" style="font-weight: 700;"></div>
          <div id="rv_auth_title2" style="font-size: 9px;"></div>
          <div style="font-size: 9px;">PIN: <span id="rv_auth_pin2"></span></div>
        </div>
      </div>
    </div>

    <!-- DISCLAIMER & TRANSACTION NOTICE -->
    <div class="receipt-legal-box">
      <div class="receipt-legal-title">Disclaimer</div>
      <p class="receipt-legal-body">This is a computer-generated SWIFT MT103 payment confirmation. This document serves as proof of payment and can be used for payment tracing purposes. Banks typically charge $25+ and take up to a week to provide MT103 copies. For any queries regarding this transaction, please quote the <strong>Transaction Reference Number (TRN)</strong>. This message has been authenticated via the SWIFT secure messaging network and message integrity has been confirmed.</p>
      <div class="receipt-legal-divider">
        <div class="receipt-legal-title" style="margin-bottom: 4px;">Transaction notice</div>
        <p class="receipt-legal-body" style="margin-bottom: 6px;">MT103 funds may be held due to compliance checks or pending bank verification. Contact your bank&rsquo;s <strong>Remittance Department</strong> to confirm if <strong>KYC</strong>, <strong>CCI</strong>, invoice, or <strong>payment purpose</strong> documents are required.</p>
        <p class="receipt-legal-body">Also check the <strong>Value Date</strong> in field <strong>:32A:</strong>. If the date has passed, provide the <strong>UETR</strong> to your bank so they can trace and release the funds into your available balance.</p>
      </div>
    </div>
  </div>

  <!-- PAGE 2 FOOTER -->
  <div class="page-footer">
    <div><span style="font-weight: 700;">DOC:</span> MT103-SCCT-CONF | <span style="font-weight: 700;">VER:</span> 7.0.2 | SWIFT SCRL &copy; 2024</div>
    <div>Page 2 of 2</div>
    <div id="rv_footer_timestamp2"></div>
  </div>
</div>
</div>

<script>
(function () {
  var STORAGE_KEY = 'intlTransferReceipt';
  var idFromUrl = (new URLSearchParams(window.location.search)).get('id');
  var downloadFlag = (new URLSearchParams(window.location.search)).get('download') === '1';
  var autoCloseFlag = (new URLSearchParams(window.location.search)).get('autoclose') === '1';

  function formatDateLong(iso) {
    try {
      var d = new Date(iso);
      if (isNaN(d.getTime())) {
        if (typeof iso === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(iso)) {
          var parts = iso.split('-');
          d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        } else {
          return iso || '';
        }
      }
      var months = ['JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE','JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'];
      return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    } catch (e) { return iso || ''; }
  }

  function formatDateDot(iso) {
    try {
      var d = new Date(iso);
      if (isNaN(d.getTime())) {
        if (typeof iso === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(iso)) {
          var parts = iso.split('-');
          return parts[2] + '.' + parts[1] + '.' + parts[0];
        }
        return '';
      }
      return String(d.getDate()).padStart(2,'0') + '.' + String(d.getMonth()+1).padStart(2,'0') + '.' + d.getFullYear();
    } catch (e) { return ''; }
  }

  function formatTime12(iso) {
    try {
      var d = new Date(iso);
      if (isNaN(d.getTime())) return '';
      var h = d.getHours(), m = d.getMinutes(), s = d.getSeconds();
      var ampm = h >= 12 ? 'PM' : 'AM';
      h = h % 12 || 12;
      return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0') + ' ' + ampm;
    } catch (e) { return ''; }
  }

  function formatMoney(amt, cur) {
    var n = Number(amt);
    if (!isFinite(n)) n = 0;
    return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function formatMoneyWords(amt, cur) {
    var n = Number(amt);
    if (!isFinite(n)) n = 0;
    var cc = String(cur || 'USD').toUpperCase();
    var symbols = { USD: '$', EUR: '€', GBP: '£' };
    var names = { USD: 'US DOLLARS', EUR: 'EUROS', GBP: 'POUNDS STERLING' };
    var sym = symbols[cc] || '$';
    var name = names[cc] || 'DOLLARS';
    var formatted = sym + formatMoney(n, cur);
    var words = numberToWords(n);
    return formatted + ' (' + words + ' ' + name + ')';
  }

  function numberToWords(n) {
    if (n === 0) return 'ZERO';
    var ones = ['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE','TEN','ELEVEN','TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN','SEVENTEEN','EIGHTEEN','NINETEEN'];
    var tens = ['','','TWENTY','THIRTY','FORTY','FIFTY','SIXTY','SEVENTY','EIGHTY','NINETY'];
    var scales = ['','THOUSAND','MILLION','BILLION'];
    function chunk(num) {
      if (num === 0) return '';
      if (num < 20) return ones[num];
      if (num < 100) return tens[Math.floor(num/10)] + (num%10 ? ' ' + ones[num%10] : '');
      return ones[Math.floor(num/100)] + ' HUNDRED' + (num%100 ? ' ' + chunk(num%100) : '');
    }
    var parts = [], scaleIdx = 0, num = Math.floor(n);
    while (num > 0) {
      var c = num % 1000;
      if (c > 0) parts.unshift(chunk(c) + (scales[scaleIdx] ? ' ' + scales[scaleIdx] : ''));
      num = Math.floor(num / 1000);
      scaleIdx++;
    }
    return parts.join(' ').trim();
  }

  function generateUETR() {
    // Fallback only if DB UETR missing (legacy rows). Prefer persisted server UETR.
    var hex = '0123456789abcdef';
    var uetr = '';
    for (var i = 0; i < 32; i++) {
      if (i === 8 || i === 12 || i === 16 || i === 20) uetr += '-';
      uetr += hex[Math.floor(Math.random() * 16)];
    }
    return uetr;
  }

  function generateRef(len) {
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var s = '';
    for (var i = 0; i < len; i++) s += chars[Math.floor(Math.random() * chars.length)];
    return s;
  }

  function generateNumeric(len) {
    var s = '';
    for (var i = 0; i < len; i++) s += Math.floor(Math.random() * 10);
    return s;
  }

  function rnd(arr) { return arr[Math.floor(Math.random() * arr.length)]; }

  async function loadPayload() {
    if (idFromUrl) {
      try {
        var res = await fetch('/api/international_transactions.php?id=' + encodeURIComponent(idFromUrl));
        var data = await res.json().catch(function () { return {}; });
        if (res.status === 401 && data && data.redirect) {
          window.location.href = data.redirect;
          return null;
        }
        if (res.ok && data && data.success && data.transaction) {
          var t = data.transaction;
          return {
            id: t.id, reference: t.reference, uetr: t.uetr || '',
            completedAt: t.created_at,
            countryCode: t.country_code, countryName: t.country_name,
            beneficiaryIban: t.beneficiary_iban, accountName: t.beneficiary_name,
            beneficiaryAddress: t.beneficiary_address,
            swiftCode: t.swift_code, routingNumber: t.routing_number,
            bankName: t.bank_name, amount: t.amount, currency: t.currency,
            messageType: t.message_type, deliveryDate: t.delivery_date, status: t.status,
            tracker_status: t.tracker_status || '',
          };
        }
      } catch (e) { console.error('loadPayload:', e); }
    }
    var raw = null;
    try { raw = sessionStorage.getItem(STORAGE_KEY); } catch (e) {}
    if (!raw) return null;
    try { return JSON.parse(raw); } catch (e) { return null; }
  }

  async function loadSenderSettings() {
    try {
      var res = await fetch('/api/international_sender_settings.php');
      var data = await res.json().catch(function () { return {}; });
      if (res.ok && data && data.success && data.sender) return data.sender;
    } catch (e) {}
    return null;
  }

  function generateCorrespondentBanks() {
    var banks = [
      { swift: 'BNPAFRPP', name: 'BNP PARIBAS SA', addr: '16 BOULEVARD DES ITALIENS, PARIS, FRANCE' },
      { swift: 'CHASUS33', name: 'JPMORGAN CHASE BANK N.A.', addr: '270 PARK AVENUE, NEW YORK, USA' },
      { swift: 'CITIUS33', name: 'CITIBANK N.A.', addr: '388 GREENWICH STREET, NEW YORK, USA' },
      { swift: 'DEUTDEFF', name: 'DEUTSCHE BANK AG', addr: 'TAUNUSANLAGE 12, 60325 FRANKFURT AM MAIN, GERMANY' },
      { swift: 'BOFAUS3N', name: 'BANK OF AMERICA N.A.', addr: '100 NORTH TRYON STREET, CHARLOTTE, USA' },
      { swift: 'HABORSHK', name: 'HSBC HONG KONG', addr: '1 QUEEN\'S ROAD CENTRAL, HONG KONG' }
    ];
    return { sender: rnd(banks), receiver: rnd(banks), intermediary: rnd(banks) };
  }

  function applyReceiptLogos(sender) {
    var url = '/assets/swift-logo.svg';
    if (sender && sender.receipt_logo_path) {
      url = sender.receipt_logo_path;
    }
    document.querySelectorAll('.js-receipt-brand-logo').forEach(function (img) {
      img.src = url;
    });
  }

  function clearSummaryConfetti() {
    var root = document.getElementById('summaryConfettiRoot');
    if (root) root.innerHTML = '';
  }

  function fireSummaryConfetti() {
    var root = document.getElementById('summaryConfettiRoot');
    if (!root) return;
    clearSummaryConfetti();
    var colors = ['#10b981', '#34d399', '#fbbf24', '#22d3ee', '#818cf8', '#f472b6', '#a3e635'];
    var n = 64;
    for (var i = 0; i < n; i++) {
      var b = document.createElement('span');
      b.className = 'summary-confetti-bit';
      var angle = (Math.PI * 2 * i) / n + Math.random() * 0.4;
      var dist = 38 + Math.random() * 72;
      var dx = Math.round(Math.cos(angle) * dist);
      var dy = Math.round(28 + Math.random() * 52);
      var spin = Math.floor(Math.random() * 540 - 120) + 'deg';
      b.style.setProperty('--dx', dx + 'px');
      b.style.setProperty('--dy', dy + 'px');
      b.style.setProperty('--spin', spin);
      b.style.animationDuration = (0.95 + Math.random() * 0.65) + 's';
      b.style.animationDelay = (Math.random() * 0.2) + 's';
      b.style.background = colors[i % colors.length];
      if (Math.random() > 0.5) {
        b.style.width = '6px';
        b.style.height = '6px';
        b.style.borderRadius = '50%';
      }
      root.appendChild(b);
    }
  }

  function fillSummaryCard(p) {
    var st = String(p.status || 'SUCCESSFUL').trim().toUpperCase();
    var banner = document.getElementById('summaryStatusBanner');
    var card = document.getElementById('summaryStatusCard');
    var lead = document.getElementById('summaryStatusLead');
    var iconWrap = document.getElementById('summaryIconWrap');
    var map = {
      SUCCESSFUL: { title: 'Successful MT103 Cash Transfer Has Been Initiated!', bg: '#ecfdf5', border: '#059669', color: '#065f46', lead: 'Your transfer was recorded successfully. Download the full MT103 receipt below.' },
      PENDING: { title: 'Transfer pending', bg: '#fffbeb', border: '#d97706', color: '#92400e', lead: 'This transfer is pending further processing.' },
      PROCESSING: { title: 'Transaction processing', bg: '#eff6ff', border: '#0284c7', color: '#075985', lead: 'This transfer is currently being processed by the remittance network. Processing checks are in progress.' },
      FAILED: { title: 'Transfer failed', bg: '#fef2f2', border: '#dc2626', color: '#991b1b', lead: 'This transfer could not be completed.' },
      REVERSED: { title: 'Transfer reversed', bg: '#f1f5f9', border: '#64748b', color: '#0f172a', lead: 'This transfer has been reversed.' },
      NETWORK_ERROR: { title: 'Network error', bg: '#fef2f2', border: '#dc2626', color: '#991b1b', lead: 'A network error was reported for this transfer.' }
    };
    var cfg = map[st] || map.SUCCESSFUL;
    var titleEl = document.getElementById('summaryBannerTitle');
    var iconEl = document.getElementById('summaryBannerIcon');
    var successSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
    var pendingSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg>';
    if (banner) {
      banner.style.background = cfg.bg;
      banner.style.color = cfg.color;
      banner.style.borderBottom = '1px solid ' + cfg.border;
    }
    if (titleEl) titleEl.textContent = cfg.title;
    if (iconWrap) iconWrap.classList.remove('is-celebrating');
    clearSummaryConfetti();
    if (iconEl) {
      iconEl.classList.remove('pending-processing');
      iconEl.classList.remove('processing-active');
      if (st === 'SUCCESSFUL') {
        iconEl.classList.remove('hidden');
        iconEl.innerHTML = successSvg;
        void iconEl.offsetWidth;
        iconEl.style.animation = 'none';
        iconEl.style.animation = '';
        if (iconWrap) iconWrap.classList.add('is-celebrating');
        window.requestAnimationFrame(function () {
          window.requestAnimationFrame(function () {
            fireSummaryConfetti();
          });
        });
      } else if (st === 'PENDING') {
        iconEl.classList.remove('hidden');
        iconEl.innerHTML = pendingSvg;
        iconEl.classList.add('pending-processing');
      } else if (st === 'PROCESSING') {
        iconEl.classList.remove('hidden');
        iconEl.innerHTML = pendingSvg;
        iconEl.classList.add('processing-active');
      } else {
        iconEl.classList.add('hidden');
      }
    }
    if (card) {
      card.style.borderColor = cfg.border;
      card.classList.add('summary-card-shell');
    }
    if (lead) lead.textContent = cfg.lead;

    var initiated = '—';
    try {
      var d0 = new Date(p.completedAt || Date.now());
      if (!isNaN(d0.getTime())) {
        initiated = d0.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
      }
    } catch (e) {}

    var cur = String(p.currency || 'USD').toUpperCase();
    var sym = cur === 'EUR' ? '€' : (cur === 'GBP' ? '£' : (cur === 'USD' ? '$' : cur + ' '));
    var amtStr = sym + formatMoney(p.amount, cur);

    var sumRef = document.getElementById('sumReference');
    var sumIni = document.getElementById('sumInitiated');
    var sumDel = document.getElementById('sumDelivery');
    var sumBen = document.getElementById('sumBeneficiary');
    var sumBnk = document.getElementById('sumBank');
    var sumAcc = document.getElementById('sumAccount');
    var sumAmt = document.getElementById('sumAmount');
    if (sumRef) sumRef.textContent = p.reference || '—';
    if (sumIni) sumIni.textContent = initiated;
    if (sumDel) sumDel.textContent = formatDateLong(p.deliveryDate) || '—';
    if (sumBen) sumBen.textContent = p.accountName || '—';
    if (sumBnk) sumBnk.textContent = p.bankName || '—';
    if (sumAcc) sumAcc.textContent = p.beneficiaryIban || '—';
    if (sumAmt) sumAmt.textContent = amtStr;
  }

  Promise.all([loadPayload(), loadSenderSettings()]).then(function (results) {
    var p = results[0];
    var s = results[1];
    if (!p || !p.reference) {
      window.location.replace('/international_dashboard.php');
      return;
    }

    applyReceiptLogos(s || {});
    fillSummaryCard(p);

    var cur = String(p.currency || 'USD').toUpperCase();
    if (cur !== 'EUR' && cur !== 'GBP') cur = 'USD';
    var cc = String(p.countryCode || '').toUpperCase();
    var usesRouting = (cc === 'US');
    var now = new Date(p.completedAt || Date.now());
    var corrBanks = generateCorrespondentBanks();
    var benefSwift = usesRouting ? (p.routingNumber || generateNumeric(9)) : ((p.swiftCode || '').toUpperCase() || generateRef(8) + 'XXX');
    var chargeAmt = (Math.floor(15 + Math.random() * 35));
    var timestamp = new Date(p.completedAt || Date.now()).toISOString();

    // Use actual beneficiary address from form
    var benefAddress = p.beneficiaryAddress ? p.beneficiaryAddress.toUpperCase() : '';

    // Header metadata - use delivery date
    document.getElementById('rv_timezone').textContent = '+0000 H. (GMT) / LOCAL TIME';
    document.getElementById('rv_delivery_date_header').textContent = formatDateLong(p.deliveryDate);
    document.getElementById('rv_msg_ref').textContent = generateNumeric(9) + 'G';
    document.getElementById('rv_barcode_num').textContent = generateNumeric(20);

    // Transmission
    document.getElementById('rv_input_time').textContent = formatTime12(p.completedAt);

    // Message header
    document.getElementById('rv_format_msg').textContent = 'FIN ' + (p.messageType || 'MT 103') + ' CREDIT TRANSFER';
    document.getElementById('rv_receiver_bank').textContent = (p.bankName || '').toUpperCase();
    document.getElementById('rv_receiver_bank_addr').textContent = benefAddress;
    document.getElementById('rv_receiver_acct_name').textContent = (p.accountName || '').toUpperCase();
    document.getElementById('rv_receiver_acct_no').textContent = generateNumeric(7);
    document.getElementById('rv_receiver_iban').textContent = p.beneficiaryIban || '';
    document.getElementById('rv_receiver_swift').textContent = benefSwift;
    document.getElementById('rv_output_ref').textContent = formatDateDot(p.completedAt).replace(/\./g, '') + benefSwift + generateNumeric(13);
    document.getElementById('rv_session').textContent = String(now.getFullYear());
    document.getElementById('rv_sequence').textContent = String(now.getFullYear()) + ' PAYMENT';
    document.getElementById('rv_rcvd_date').textContent = formatDateDot(p.deliveryDate);
    document.getElementById('rv_value_amount').textContent = formatMoneyWords(p.amount, cur);

    // Security codes
    document.getElementById('rv_uetr').textContent = (p && p.uetr) ? p.uetr : '—';
    document.getElementById('rv_security_code').textContent = generateNumeric(9) + 'S';
    document.getElementById('rv_branch_network').textContent = generateRef(8) + 'XXX';
    document.getElementById('rv_input_ref').textContent = formatDateDot(p.completedAt).replace(/\./g, '') + generateRef(8) + 'XXX' + generateNumeric(12);

    // MT103 Fields - Page 1
    document.getElementById('rv_f20').textContent = p.reference;
    document.getElementById('rv_f32a').textContent = formatDateDot(p.deliveryDate) + ' / ' + cur + ' ' + formatMoney(p.amount, cur);
    document.getElementById('rv_f33b').textContent = cur + ' ' + formatMoney(p.amount, cur);

    // Page 2 fields
    document.getElementById('rv_header_ref2').textContent = p.reference;
    document.getElementById('rv_f57a_swift').textContent = benefSwift;
    document.getElementById('rv_f57a_name').textContent = (p.bankName || '').toUpperCase();
    document.getElementById('rv_f57a_addr').textContent = benefAddress;
    document.getElementById('rv_f59_iban').textContent = p.beneficiaryIban || '';
    document.getElementById('rv_f59_swift').textContent = benefSwift;
    document.getElementById('rv_f59_name').textContent = (p.accountName || '').toUpperCase();
    document.getElementById('rv_f59_addr').textContent = benefAddress;
    document.getElementById('rv_f70').textContent = 'INTERNATIONAL WIRE TRANSFER / INV-' + generateNumeric(8);
    document.getElementById('rv_f71f').textContent = cur + ' ' + chargeAmt + '.00';

    // Correspondent banks
    document.getElementById('rv_f53a_swift').textContent = corrBanks.sender.swift;
    document.getElementById('rv_f53a_name').textContent = corrBanks.sender.name;
    document.getElementById('rv_f53a_addr').textContent = corrBanks.sender.addr;
    document.getElementById('rv_f54a_swift').textContent = corrBanks.receiver.swift;
    document.getElementById('rv_f54a_name').textContent = corrBanks.receiver.name;
    document.getElementById('rv_f54a_addr').textContent = corrBanks.receiver.addr;
    document.getElementById('rv_f56a_swift').textContent = corrBanks.intermediary.swift;
    document.getElementById('rv_f56a_name').textContent = corrBanks.intermediary.name;

    // Stamps and footers
    document.getElementById('rv_stamp_date').textContent = formatDateLong(p.completedAt).split(' ').slice(0,2).join(' ');
    document.getElementById('rv_footer_ref1').textContent = p.reference;
    document.getElementById('rv_footer_timestamp1').textContent = timestamp;
    document.getElementById('rv_footer_timestamp2').textContent = timestamp;

    // Sender settings (s loaded with Promise.all)
    if (!s) {
      document.getElementById('rv_sender_acct_name').textContent = 'SENDER NOT CONFIGURED';
    } else {
      var senderAddr = [s.sender_address_line1, s.sender_address_line2, s.sender_address_line3].filter(Boolean).join(', ').toUpperCase();

      document.getElementById('rv_sender_swift_code').textContent = (s.sender_swift || '').toUpperCase() + 'XXX';
      document.getElementById('rv_sender_bank_name').textContent = (s.sender_bank || '').toUpperCase();
      document.getElementById('rv_sender_bank_addr').textContent = senderAddr + ', ' + (s.sender_country || '').toUpperCase();
      document.getElementById('rv_sender_acct_name').textContent = (s.sender_name || '').toUpperCase();
      document.getElementById('rv_sender_acct_no').textContent = generateNumeric(9);
      document.getElementById('rv_sender_iban').textContent = (s.sender_iban || '').toUpperCase();
      document.getElementById('rv_sender_swift_full').textContent = (s.sender_swift || '').toUpperCase() + 'XXX';

      document.getElementById('rv_f50a_iban').textContent = (s.sender_iban || '').toUpperCase();
      document.getElementById('rv_f50a_swift').textContent = (s.sender_swift || '').toUpperCase() + 'XXX';
      document.getElementById('rv_f50a_name').textContent = (s.sender_name || '').toUpperCase();
      document.getElementById('rv_f50a_addr').textContent = senderAddr + ', ' + (s.sender_country || '').toUpperCase();

      document.getElementById('rv_f52a_swift').textContent = (s.sender_swift || '').toUpperCase() + 'XXX';
      document.getElementById('rv_f52a_name').textContent = (s.sender_bank || '').toUpperCase();
      document.getElementById('rv_f52a_addr').textContent = senderAddr + ', ' + (s.sender_country || '').toUpperCase();

      document.getElementById('rv_header_bank').textContent = (s.sender_bank || 'SWIFT MESSAGE').toUpperCase();
      document.getElementById('rv_header_bank2').textContent = (s.sender_bank || 'SWIFT MESSAGE').toUpperCase();

      document.getElementById('rv_auth_bank').textContent = (s.sender_bank || '').toUpperCase();
      document.getElementById('rv_auth_addr').textContent = senderAddr + ', ' + (s.sender_country || '').toUpperCase();

      var f72Text = 'PLEASE ADVISE ' + (p.accountName || '').toUpperCase() + ', THE BENEFICIARY OF THIS FUNDS TRANSFER VIA ' + (p.messageType || 'MT103') + ' IN THE AMOUNT OF ' + formatMoneyWords(p.amount, cur) + ' ONLY. THIS IRREVOCABLE FUNDS TRANSFER IS VALID FOR PAYMENT THE DAY OF RECEIPT, CAN BE RELIED UPON FOR FULL CASH AND IT IS SUBJECT TO THE INTERNATIONAL REMITTANCE REGULATIONS OF ' + (s.sender_bank || '').toUpperCase() + '.';
      document.getElementById('rv_f72').textContent = f72Text;

      var titles = ['VICE PRESIDENT', 'DIRECTOR', 'SENIOR MANAGER', 'HEAD OF OPERATIONS'];
      var names = ['MRS SABINE KESSLER', 'MR NORBERT VIEHMEYER', 'MR JONATHAN HART', 'MS ELENA FISCHER', 'MR DAVID CHEN'];
      document.getElementById('rv_auth_name1').textContent = rnd(names);
      document.getElementById('rv_auth_title1').textContent = rnd(titles);
      document.getElementById('rv_auth_pin1').textContent = generateNumeric(5);
      document.getElementById('rv_auth_name2').textContent = rnd(names);
      document.getElementById('rv_auth_title2').textContent = rnd(titles);
      document.getElementById('rv_auth_pin2').textContent = generateNumeric(5);
    }

    // Barcode
    try {
      JsBarcode('#receiptBarcode', p.reference + generateNumeric(8), {
        format: 'CODE128', width: 1.0, height: 35, displayValue: false,
        margin: 0, background: 'transparent', lineColor: '#000',
      });
    } catch (e) { console.warn('Barcode:', e); }

    window.__receiptRef = p.reference;
    if (downloadFlag) {
      setTimeout(function () {
        var b = document.getElementById('btnDownloadPdf');
        if (b) b.click();
      }, 900);
    }
  });

  async function generatePdfBlob() {
    var wrap = document.getElementById('fullReceiptWrap');
    var wasHidden = !!(wrap && wrap.classList.contains('hidden'));
    if (wrap && wasHidden) {
      wrap.classList.remove('hidden');
      await new Promise(function (r) { setTimeout(r, 500); });
    }
    try {
    var page1 = document.getElementById('page1');
    var page2 = document.getElementById('page2');
    if (!page1 || !page2 || typeof html2canvas === 'undefined' || !window.jspdf) {
      throw new Error('PDF libraries not loaded');
    }
    
    var pdfW = 210; // A4 width in mm
    var pdfH = 297; // A4 height in mm
    var doc = new window.jspdf.jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4', compress: true });

    // Use lower scale and JPEG format for smaller file size
    var canvasOptions = { backgroundColor: '#ffffff', scale: 1.5, useCORS: true, logging: false };

    // Render page 1
    var canvas1 = await html2canvas(page1, canvasOptions);
    var imgData1 = canvas1.toDataURL('image/jpeg', 0.85); // JPEG at 85% quality
    var imgH1 = (canvas1.height * pdfW) / canvas1.width;
    doc.addImage(imgData1, 'JPEG', 0, 0, pdfW, Math.min(imgH1, pdfH), undefined, 'FAST');

    // Add page 2
    doc.addPage('a4', 'portrait');
    var canvas2 = await html2canvas(page2, canvasOptions);
    var imgData2 = canvas2.toDataURL('image/jpeg', 0.85); // JPEG at 85% quality
    var imgH2 = (canvas2.height * pdfW) / canvas2.width;
    doc.addImage(imgData2, 'JPEG', 0, 0, pdfW, Math.min(imgH2, pdfH), undefined, 'FAST');

    return doc;
    } finally {
      if (wasHidden && wrap) wrap.classList.add('hidden');
    }
  }

  var btnToggle = document.getElementById('btnToggleFullReceipt');
  if (btnToggle) {
    btnToggle.addEventListener('click', function () {
      var w = document.getElementById('fullReceiptWrap');
      if (!w) return;
      var hid = w.classList.toggle('hidden');
      btnToggle.textContent = hid ? 'View full receipt' : 'Hide full receipt';
    });
  }

  var btnPrint = document.getElementById('btnPrintReceipt');
  if (btnPrint) {
    btnPrint.addEventListener('click', async function () {
      var wrap = document.getElementById('fullReceiptWrap');
      var wasHidden = wrap && wrap.classList.contains('hidden');
      if (wasHidden) wrap.classList.remove('hidden');
      await new Promise(function (r) { setTimeout(r, 400); });
      window.print();
      if (wasHidden && wrap) wrap.classList.add('hidden');
    });
  }

  var btnDownload = document.getElementById('btnDownloadPdf');
  if (btnDownload) {
    btnDownload.addEventListener('click', async function () {
      var btn = this;
      btn.disabled = true;
      btn.textContent = 'Generating...';
      try {
        var doc = await generatePdfBlob();
        var ref = window.__receiptRef || 'receipt';
        doc.save('SWIFT_MT103_' + ref.replace(/[^a-zA-Z0-9._-]/g, '_') + '.pdf');
        if (autoCloseFlag) setTimeout(function () { window.close(); }, 650);
      } catch (err) {
        console.error(err);
        alert('Could not generate PDF.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Download receipt (PDF)';
      }
    });
  }
})();
</script>
</body>
</html>
