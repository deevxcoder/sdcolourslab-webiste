<?php
/**
 * Public Billing Invoice Page
 * Securely accessible via order id and secure key.
 * Example: /invoice.php?id=100&key=abcdef1234567890
 */

require_once __DIR__ . '/includes/db.php';
$db = getDB();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$secureKey = isset($_GET['key']) ? $_GET['key'] : '';

if (!$orderId || empty($secureKey)) {
    http_response_code(400);
    die('Invalid request parameters. Missing ID or security key.');
}

// Fetch order
$stmt = $db->prepare("SELECT o.*, u.name as photographer_name, u.email as photographer_email, u.phone as photographer_phone, u.studio_name 
                      FROM orders o 
                      LEFT JOIN users u ON o.photographer_id=u.id 
                      WHERE o.id=?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order || $order['secure_key'] !== $secureKey) {
    http_response_code(404);
    die('Invoice not found or invalid security credentials.');
}

// Fetch items
$iStmt = $db->prepare("SELECT * FROM order_items WHERE order_id=?");
$iStmt->execute([$orderId]);
$items = $iStmt->fetchAll();

// Resolve client details
$clientName = ($order['photographer_id'] === null) ? $order['manual_studio_name'] : ($order['studio_name'] ?: $order['photographer_name']);
$clientPhone = ($order['photographer_id'] === null) ? $order['manual_phone'] : $order['photographer_phone'];
$orderSize = ($order['photographer_id'] === null) ? $order['manual_size'] : ($items[0]['size'] ?? '');

// Format items for the exact bill layout (exactly 10 rows)
$displayRows = [];
$sn = 1;
foreach ($items as $item) {
    // If it's Tharmal or empty unit price, don't show price
    $showRate = ($item['unit_price'] > 0) ? number_format($item['unit_price']) : '';
    $showAmount = ($item['unit_price'] > 0) ? number_format($item['unit_price'] * $item['quantity']) . '/-' : '';
    
    $displayRows[] = [
        'sn' => $sn++,
        'print_type' => $item['print_type'] ?: 'Addition Product',
        'print_name' => $item['product_name'],
        'qty' => $item['quantity'],
        'rate' => $showRate,
        'amount' => $showAmount
    ];
}

// Pad with empty rows matching the physical design up to 10 rows
while (count($displayRows) < 10) {
    $displayRows[] = [
        'sn' => $sn++,
        'print_type' => 'Addition Product',
        'print_name' => '',
        'qty' => '',
        'rate' => '',
        'amount' => ''
    ];
}

// Subtotal & Discount calculations
$subtotal = $order['total'];
$discountPercent = $order['discount_percent'] ?? 0;
$discountAmount = $order['discount_amount'] ?? 0;
$netPay = $order['net_pay'] ?? $subtotal;

// WhatsApp Share link compilation
$invoiceUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$waMessage = "Hello *{$clientName}*, here is your billing invoice from SD Colours PhotoBook Lab:\nOrder No: {$order['id']}\nTotal Payable: ₹" . number_format($netPay) . "/-\nView/Print Bill: {$invoiceUrl}";
$waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $clientPhone) . "?text=" . urlencode($waMessage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice #<?= $order['id'] ?> – SD Colours PhotoBook Lab</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --bill-border: #111111;
      --font-serif: 'Playfair Display', Georgia, serif;
      --font-sans: 'Plus Jakarta Sans', system-ui, sans-serif;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: var(--font-sans);
      background-color: #f3f4f6;
      color: #111111;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
    }
    
    /* Control Bar styling */
    .control-bar {
      width: 100%;
      max-width: 1000px;
      background-color: #1f2937;
      border-radius: 12px;
      padding: 12px 24px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #ffffff;
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    
    .control-bar h3 {
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.05em;
    }
    
    .btn-group {
      display: flex;
      gap: 10px;
    }
    
    .btn {
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    
    .btn-primary {
      background-color: #cca353;
      color: #171717;
    }
    
    .btn-primary:hover {
      background-color: #b58c42;
    }
    
    .btn-secondary {
      background-color: rgba(255,255,255,0.1);
      color: #ffffff;
      border: 1px solid rgba(255,255,255,0.2);
    }
    
    .btn-secondary:hover {
      background-color: rgba(255,255,255,0.2);
    }
    
    /* Bill Slip container */
    .bill-container {
      width: 100%;
      max-width: 1000px;
      background-color: #ffffff;
      border: 2px solid var(--bill-border);
      padding: 15px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
    }
    
    /* Watermark styling */
    .bill-watermark {
      position: absolute;
      top: 58%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 55%;
      opacity: 0.04;
      pointer-events: none;
      z-index: 1;
    }
    
    /* Bill Header */
    .bill-header {
      border: 1px solid var(--bill-border);
      display: flex;
      flex-direction: column;
      position: relative;
      z-index: 2;
    }
    
    .header-top {
      display: flex;
      justify-content: space-between;
      padding: 12px 15px;
      align-items: center;
    }
    
    .header-reg {
      font-size: 9px;
      font-weight: 700;
      text-align: right;
      line-height: 1.4;
      font-family: monospace;
    }
    
    .header-logo-section {
      text-align: center;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .logo-container {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .logo-img {
      height: 48px;
      width: auto;
    }
    
    .logo-title {
      font-family: var(--font-serif);
      font-size: 26px;
      font-weight: 800;
      color: #111111;
      letter-spacing: -0.02em;
    }
    
    .logo-sub {
      font-size: 11px;
      color: #555555;
      font-weight: 600;
      margin-top: 3px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .logo-contact {
      font-size: 13px;
      font-weight: 700;
      margin-top: 5px;
    }
    
    .header-address-bar {
      background-color: #fdfdfd;
      border-top: 1px solid var(--bill-border);
      text-align: center;
      padding: 5px 10px;
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.02em;
    }
    
    /* Client Info */
    .bill-client-info {
      display: flex;
      justify-content: space-between;
      padding: 12px 5px 8px 5px;
      font-size: 13px;
      font-weight: 700;
      position: relative;
      z-index: 2;
    }
    
    .client-left, .client-right {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    
    .client-right {
      align-items: flex-end;
      text-align: right;
    }
    
    /* Items Table */
    .bill-items-table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid var(--bill-border);
      position: relative;
      z-index: 2;
    }
    
    .bill-items-table th {
      border: 1px solid var(--bill-border);
      background-color: #fdfdfd;
      padding: 6px;
      font-size: 12px;
      font-weight: 800;
      text-align: center;
    }
    
    .bill-items-table td {
      border-left: 1px solid var(--bill-border);
      border-right: 1px solid var(--bill-border);
      padding: 5px 8px;
      font-size: 12px;
      font-weight: 700;
      height: 24px;
    }
    
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    
    /* Summary Block */
    .bill-footer-section {
      display: flex;
      border-left: 1px solid var(--bill-border);
      border-right: 1px solid var(--bill-border);
      border-bottom: 1px solid var(--bill-border);
      position: relative;
      z-index: 2;
    }
    
    .footer-left-notes {
      width: 60%;
      border-right: 1px solid var(--bill-border);
      padding: 10px;
      font-size: 10px;
      color: #333333;
      font-weight: 600;
      display: flex;
      flex-direction: column;
    }
    
    .notes-title {
      font-weight: 800;
      text-transform: uppercase;
      color: #555555;
      font-size: 9px;
      letter-spacing: 0.05em;
      margin-bottom: 5px;
    }
    
    .notes-content {
      line-height: 1.4;
      white-space: pre-line;
    }
    
    .footer-right-pricing {
      width: 40%;
      display: flex;
      flex-direction: column;
    }
    
    .pricing-row {
      display: flex;
      border-bottom: 1px solid var(--bill-border);
      font-size: 13px;
      font-weight: 700;
    }
    
    .pricing-row:last-child {
      border-bottom: none;
    }
    
    .pricing-label {
      width: 60%;
      padding: 6px;
      text-align: right;
      border-right: 1px solid var(--bill-border);
      font-weight: 800;
    }
    
    .pricing-value {
      width: 40%;
      padding: 6px;
      text-align: right;
      font-weight: 800;
    }
    
    /* Sign Off area */
    .bill-sign-off {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      padding: 20px 10px 10px 10px;
      font-size: 12px;
      font-weight: 700;
      position: relative;
      z-index: 2;
    }
    
    .sign-left {
      width: 40%;
      height: 60px;
      border: 1px solid var(--bill-border);
      padding: 5px;
      font-size: 10px;
      color: #555555;
      font-weight: 600;
    }
    
    .sign-right {
      text-align: right;
      width: 45%;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 15px;
    }
    
    .authori-label {
      font-size: 12px;
      font-weight: 800;
    }
    
    .sign-line {
      width: 150px;
      border-bottom: 1px solid #111111;
      margin-top: 5px;
    }
    
    /* Printable configuration */
    @media print {
      body {
        background-color: #ffffff;
        padding: 0;
        min-height: auto;
      }
      .no-print {
        display: none !important;
      }
      .bill-container {
        border: 2px solid #000000;
        box-shadow: none;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

  <!-- Controls at the top (Hidden during printing) -->
  <div class="control-bar no-print">
    <div>
      <h3>SD COLOURS PHOTOBOOK LAB &mdash; BILLING SYSTEM</h3>
    </div>
    <div class="btn-group">
      <button onclick="window.print()" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print / Save PDF
      </button>
      <a href="<?= htmlspecialchars($waLink) ?>" target="_blank" class="btn btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        Send to WhatsApp
      </a>
      <a href="/admin/orders.php?id=<?= $order['id'] ?>" class="btn btn-secondary">
        Go to Order Manager
      </a>
    </div>
  </div>

  <!-- Billing Slip Container -->
  <div class="bill-container">
    
    <!-- Watermark Logo -->
    <img src="/images/logo.png" alt="" class="bill-watermark">
    
    <!-- Header Section -->
    <div class="bill-header">
      <div class="header-top">
        <div style="width: 20%;"></div>
        <div class="header-logo-section">
          <div class="logo-container">
            <img src="/images/logo.png" alt="SD Colours Logo" class="logo-img">
            <h1 class="logo-title">Colours Photo Book Lab</h1>
          </div>
          <div class="logo-sub">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            sdcoloursphotobooklab@gmail.com
          </div>
          <div class="logo-contact">
            Call : 8895838987, 8260754410
          </div>
        </div>
        <div class="header-reg">
          GSTIN NO - 21BKEPS9993R1Z0<br>
          REGISTRATION NO - OD-300011978
        </div>
      </div>
      <div class="header-address-bar">
        Madhusudan marg, Naredi Tower Complex (In front of Raymond showroom) RKL- 769001 (ODISHA)
      </div>
    </div>
    
    <!-- Client Info Bar -->
    <div class="bill-client-info">
      <div class="client-left">
        <div>Order No..<?= htmlspecialchars($order['id']) ?></div>
        <div>Studio Name : <?= htmlspecialchars($clientName) ?></div>
        <div>Mobile no : <?= htmlspecialchars($clientPhone) ?></div>
      </div>
      <div class="client-right">
        <div>Date : <?= date('d.m.Y', strtotime($order['created_at'])) ?></div>
        <div>Size : <?= htmlspecialchars($orderSize ?: 'N/A') ?></div>
      </div>
    </div>
    
    <!-- Items Table -->
    <table class="bill-items-table">
      <thead>
        <tr>
          <th style="width: 6%;">S.N</th>
          <th style="width: 22%;">Print Type</th>
          <th style="width: 44%;">Print Name</th>
          <th style="width: 8%;">Qty</th>
          <th style="width: 10%;">Rate</th>
          <th style="width: 10%;">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($displayRows as $row): ?>
          <tr>
            <td class="text-center"><?= $row['sn'] ?></td>
            <td><?= htmlspecialchars($row['print_type']) ?></td>
            <td><?= htmlspecialchars($row['print_name']) ?></td>
            <td class="text-center"><?= $row['qty'] ?></td>
            <td class="text-center"><?= $row['rate'] ?></td>
            <td class="text-right"><?= $row['amount'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    
    <!-- Summary Section -->
    <div class="bill-footer-section">
      <!-- Notes Box -->
      <div class="footer-left-notes">
        <span class="notes-title">Order Notes / Instructions:</span>
        <div class="notes-content">
          <?= htmlspecialchars($order['notes'] ?: 'No special instructions recorded.') ?>
        </div>
      </div>
      
      <!-- Financial Totals -->
      <div class="footer-right-pricing">
        <div class="pricing-row">
          <div class="pricing-label">Total</div>
          <div class="pricing-value"><?= number_format($subtotal) ?>/-</div>
        </div>
        <div class="pricing-row">
          <div class="pricing-label">Discount <?= $discountPercent ?>%</div>
          <div class="pricing-value"><?= number_format($discountAmount) ?>/-</div>
        </div>
        <div class="pricing-row" style="border-top: 1px solid var(--bill-border);">
          <div class="pricing-label" style="font-size: 14px; font-weight: 900;">Net Pay</div>
          <div class="pricing-value" style="font-size: 14px; font-weight: 900;"><?= number_format($netPay) ?>/-</div>
        </div>
      </div>
    </div>
    
    <!-- Sign Off Section -->
    <div class="bill-sign-off">
      <div class="sign-left">
        <!-- Optional space for Terms/QR/Stamp -->
        <span style="font-size: 9px; font-weight: 800; display: block; margin-bottom: 2px;">Terms:</span>
        1. All disputes subject to Rourkela jurisdiction.<br>
        2. Check layout & prints before dispatch.
      </div>
      <div class="sign-right">
        <div class="authori-label">For: SD Colours PhotoBook Lab</div>
        <div class="sign-line"></div>
        <span style="font-size: 9px; color: #555555; font-weight: 600;">Authori sign.</span>
      </div>
    </div>
    
  </div>

</body>
</html>
