<?php
$pageTitle = 'Manage Orders – Admin';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $adminNotes = trim($_POST['admin_notes'] ?? '');
    $allowed = ['pending','paid','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        $stmt = $db->prepare("UPDATE orders SET status=?, admin_notes=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$status, $adminNotes, $orderId]);
    }
    header("Location: /admin/orders.php?id=$orderId&updated=1");
    exit;
}

$viewId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$statusFilter = $_GET['status'] ?? 'all';
$order = null;
$items = [];

if ($viewId) {
    $stmt = $db->prepare("SELECT o.*, u.name as photographer_name, u.email as photographer_email, u.phone as photographer_phone, u.studio_name FROM orders o LEFT JOIN users u ON o.photographer_id=u.id WHERE o.id=?");
    $stmt->execute([$viewId]);
    $order = $stmt->fetch();
    if ($order) {
        $iStmt = $db->prepare("SELECT * FROM order_items WHERE order_id=?");
        $iStmt->execute([$viewId]);
        $items = $iStmt->fetchAll();
    }
}

$where = $statusFilter !== 'all' ? "WHERE o.status=?" : "WHERE 1=1";
$params = $statusFilter !== 'all' ? [$statusFilter] : [];
$stmt = $db->prepare("SELECT o.*, COALESCE(u.name, o.manual_studio_name, 'Offline Client') as photographer_name, COUNT(oi.id) as item_count FROM orders o LEFT JOIN users u ON o.photographer_id=u.id LEFT JOIN order_items oi ON o.id=oi.order_id $where GROUP BY o.id, u.name, o.manual_studio_name ORDER BY o.created_at DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();

require_once '../includes/admin_header.php';

// Helper status classes
$statusClasses = [
  'pending' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
  'paid' => 'bg-green-500/10 text-green-400 border border-green-500/20',
  'processing' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
  'shipped' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
  'delivered' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
  'cancelled' => 'bg-red-500/10 text-red-400 border border-red-500/20',
];
?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight"><?= $order ? 'Order Details' : 'Manage Orders' ?></h1>
    <p class="text-zinc-400 text-sm mt-1">
      <?= $order ? 'Reviewing specific photobook printing order information.' : 'View, track, and update incoming client printing orders.' ?>
    </p>
  </div>
  <div class="flex items-center gap-3">
    <?php if (!$order): ?>
      <a href="/admin/create_order.php" class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary-dark text-secondary text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-lg">
        ➕ Add Manual Order
      </a>
    <?php endif; ?>
    <a href="/admin/index.php" class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline font-bold">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
      </svg>
      Back to Dashboard
    </a>
  </div>
</div>

<?php if ($order): ?>
  <!-- View Single Order Details -->
  <?php if (isset($_GET['updated'])): ?>
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 text-sm font-semibold mb-6 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
      </svg>
      Order updated successfully.
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Order Details Card -->
    <div class="bg-secondary/40 border border-white/5 rounded-2xl overflow-hidden shadow-xl lg:col-span-2">
      <!-- Order Detail Head -->
      <div class="bg-white/5 px-6 py-5 border-b border-white/5 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
          <span class="text-xs text-zinc-500 font-semibold uppercase tracking-wider block">Order ID</span>
          <h2 class="text-lg font-bold text-white">#<?= $order['id'] ?> &mdash; <span class="text-zinc-400 text-sm font-normal"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span></h2>
        </div>
        <div class="flex items-center gap-3">
          <a href="/invoice.php?id=<?= $order['id'] ?>&key=<?= $order['secure_key'] ?>" target="_blank" class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary-dark text-secondary text-xs font-bold px-3 py-1.5 rounded-lg transition-colors shadow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656" />
            </svg>
            Generate Invoice / Bill
          </a>
          <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold <?= $statusClasses[$order['status']] ?? 'bg-zinc-500/10 text-zinc-400' ?>">
            Status: <?= ucfirst($order['status']) ?>
          </span>
        </div>
      </div>
      
      <!-- Items List -->
      <div class="divide-y divide-white/5">
        <?php foreach ($items as $item): ?>
          <div class="px-6 py-5 flex flex-col sm:flex-row justify-between sm:items-center gap-4 hover:bg-white/[0.01] transition-colors">
            <div>
              <h3 class="font-bold text-white text-base flex flex-wrap items-center gap-2">
                <?php if (!empty($item['print_type'])): ?>
                  <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 bg-white/5 border border-white/10 rounded text-zinc-400"><?= htmlspecialchars($item['print_type']) ?></span>
                <?php endif; ?>
                <span><?= htmlspecialchars($item['product_name']) ?></span>
                <?php if ($item['size']): ?>
                  <span class="text-primary text-xs font-medium border border-primary/20 bg-primary/5 rounded px-2 py-0.5 ml-2 inline-block"><?= htmlspecialchars($item['size']) ?></span>
                <?php endif; ?>
              </h3>
              
              <?php if ($item['notes']): ?>
                <div class="mt-2 text-xs text-zinc-400 bg-white/5 border border-white/5 rounded-lg p-2.5 max-w-lg">
                  <span class="text-zinc-500 font-bold uppercase block text-[10px] tracking-wider mb-0.5">Special Layout Note</span>
                  <?= htmlspecialchars($item['notes']) ?>
                </div>
              <?php endif; ?>
              
              <div class="mt-2.5 text-xs text-zinc-500 font-medium">
                Quantity: <span class="text-zinc-300 font-semibold"><?= $item['quantity'] ?></span> &times; ₹<?= number_format($item['unit_price']) ?>
              </div>
            </div>
            <div class="text-right text-base font-extrabold text-white sm:self-start">
              ₹<?= number_format($item['unit_price'] * $item['quantity']) ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Photographer notes -->
      <?php if ($order['notes']): ?>
        <div class="bg-white/5 border-t border-white/5 p-6">
          <h4 class="text-xs text-zinc-500 font-semibold uppercase tracking-wider mb-2">Photographer Order Level Note</h4>
          <p class="text-sm text-zinc-300 bg-white/5 border border-white/5 rounded-xl p-4 italic">
            "<?= htmlspecialchars($order['notes']) ?>"
          </p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Info Sidebar Card -->
    <div class="space-y-6">
      <!-- Photographer Details Card -->
      <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 shadow-xl">
        <h3 class="text-xs text-zinc-500 font-semibold uppercase tracking-wider border-b border-white/5 pb-3 mb-4">
          <?= ($order['photographer_id'] === null) ? 'Manual Client Info' : 'Photographer Info' ?>
        </h3>
        <div class="space-y-4">
          <?php if ($order['photographer_id'] === null): ?>
            <div>
              <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Studio Name / Client</span>
              <span class="text-sm font-bold text-white block"><?= htmlspecialchars($order['manual_studio_name']) ?></span>
            </div>
            <?php if ($order['manual_phone']): ?>
              <div>
                <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Phone Contact</span>
                <a href="tel:<?= htmlspecialchars($order['manual_phone']) ?>" class="text-sm text-zinc-300 hover:text-primary transition-colors block"><?= htmlspecialchars($order['manual_phone']) ?></a>
              </div>
            <?php endif; ?>
            <?php if ($order['manual_size']): ?>
              <div>
                <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Order Size (Slip Header)</span>
                <span class="text-sm font-bold text-zinc-300 block"><?= htmlspecialchars($order['manual_size']) ?></span>
              </div>
            <?php endif; ?>
            <div>
              <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Order Type</span>
              <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase mt-0.5">Offline / Manual</span>
            </div>
          <?php else: ?>
            <div>
              <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Full Name</span>
              <span class="text-sm font-bold text-white block"><?= htmlspecialchars($order['photographer_name']) ?></span>
            </div>
            <?php if ($order['studio_name']): ?>
              <div>
                <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Studio Name</span>
                <span class="text-sm font-bold text-primary block"><?= htmlspecialchars($order['studio_name']) ?></span>
              </div>
            <?php endif; ?>
            <div>
              <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Email Address</span>
              <a href="mailto:<?= htmlspecialchars($order['photographer_email']) ?>" class="text-sm text-zinc-300 hover:text-primary transition-colors block"><?= htmlspecialchars($order['photographer_email']) ?></a>
            </div>
            <?php if ($order['photographer_phone']): ?>
              <div>
                <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Phone Contact</span>
                <a href="tel:<?= htmlspecialchars($order['photographer_phone']) ?>" class="text-sm text-zinc-300 hover:text-primary transition-colors block"><?= htmlspecialchars($order['photographer_phone']) ?></a>
              </div>
            <?php endif; ?>
          <?php endif; ?>
          
          <?php if (!empty($order['drive_link'])): ?>
            <div class="border-t border-white/5 pt-3 mt-3">
              <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Design Files (Drive / WeTransfer)</span>
              <a href="<?= htmlspecialchars($order['drive_link']) ?>" target="_blank" 
                 class="inline-flex items-center gap-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 text-xs font-bold px-3.5 py-2 rounded-xl transition-all border border-blue-500/20 mt-1.5 w-full justify-center">
                🔗 Open Files Link
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Financial Card -->
      <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 shadow-xl">
        <h3 class="text-xs text-zinc-500 font-semibold uppercase tracking-wider border-b border-white/5 pb-3 mb-4">Pricing Total</h3>
        <div class="space-y-3 py-2 text-sm text-zinc-300">
          <?php if (isset($order['discount_amount']) && $order['discount_amount'] > 0): ?>
            <div class="flex justify-between">
              <span>Gross Subtotal:</span>
              <span class="font-bold text-white">₹<?= number_format($order['total']) ?></span>
            </div>
            <div class="flex justify-between text-red-400">
              <span>Discount (<?= $order['discount_percent'] ?>%):</span>
              <span class="font-bold">-₹<?= number_format($order['discount_amount']) ?></span>
            </div>
            <div class="border-t border-white/5 pt-3 flex justify-between items-center">
              <span class="text-xs font-semibold text-zinc-400 uppercase">Grand Net Price:</span>
              <span class="text-2xl font-black text-primary">₹<?= number_format($order['net_pay']) ?></span>
            </div>
          <?php else: ?>
            <div class="text-center py-2">
              <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block mb-1">Grand Net Price</span>
              <span class="text-3xl font-black text-primary">₹<?= number_format($order['total']) ?></span>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Updates Actions Card -->
  <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 sm:p-8 shadow-xl mb-6">
    <h2 class="text-base font-bold text-white border-b border-white/5 pb-3 mb-6">Update Order Dispatch State</h2>
    <form method="POST">
      <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Order Status</label>
          <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
            <?php foreach (['pending','paid','processing','shipped','delivered','cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?> class="bg-secondary text-white"><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Admin Notes (Tracking, shipping details)</label>
          <input type="text" name="admin_notes" value="<?= htmlspecialchars($order['admin_notes'] ?? '') ?>" placeholder="e.g. Dispatched via DTDC, Tracking ID: 123456" 
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>
      </div>
      <div>
        <button type="submit" name="update_order" class="bg-primary hover:bg-primary-dark text-secondary font-bold py-3.5 px-6 rounded-xl shadow-lg transition-all duration-200 text-sm cursor-pointer">
          Save Changes
        </button>
      </div>
    </form>
  </div>

  <div class="text-left mb-6">
    <a href="/admin/orders.php" class="inline-flex items-center gap-1 text-xs text-zinc-400 hover:text-white font-semibold">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
      </svg>
      Back to All Orders
    </a>
  </div>

<?php else: ?>
  <!-- View Orders List -->
  <div class="flex gap-2 flex-wrap mb-6">
    <?php foreach (['all'=>'All Orders','pending'=>'Pending','paid'=>'Paid','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $k=>$v): ?>
      <?php
        $isActive = $statusFilter===$k;
        $tabClass = $isActive 
          ? 'bg-primary text-secondary font-bold border-primary' 
          : 'bg-white/5 border-white/10 text-zinc-300 hover:bg-white/10 hover:border-white/20';
      ?>
      <a href="/admin/orders.php?status=<?= $k ?>" class="px-4 py-2 border rounded-full text-xs font-semibold transition-all duration-200 <?= $tabClass ?>">
        <?= $v ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if ($orders): ?>
    <div class="bg-secondary/40 border border-white/5 rounded-2xl overflow-hidden shadow-xl">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-zinc-300">
          <thead class="text-xs uppercase bg-white/5 text-zinc-400 font-bold border-b border-white/5">
            <tr>
              <th scope="col" class="px-6 py-4">Order ID</th>
              <th scope="col" class="px-6 py-4">Photographer</th>
              <th scope="col" class="px-6 py-4">Date</th>
              <th scope="col" class="px-6 py-4">Items Count</th>
              <th scope="col" class="px-6 py-4">Total Amount</th>
              <th scope="col" class="px-6 py-4">Status</th>
              <th scope="col" class="px-6 py-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
              <tr class="border-b border-white/5 hover:bg-white/[0.01] transition-colors">
                <td class="px-6 py-4 font-bold text-white">#<?= $o['id'] ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($o['photographer_name']) ?></td>
                <td class="px-6 py-4"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                <td class="px-6 py-4 font-semibold text-zinc-400"><?= $o['item_count'] ?> items</td>
                <td class="px-6 py-4 font-bold text-primary">₹<?= number_format($o['total']) ?></td>
                <td class="px-6 py-4">
                  <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold <?= $statusClasses[$o['status']] ?? 'bg-zinc-500/10 text-zinc-400' ?>">
                    <?= ucfirst($o['status']) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <a href="/admin/orders.php?id=<?= $o['id'] ?>" class="text-xs bg-primary hover:bg-primary-dark text-secondary font-bold px-3.5 py-2 rounded-xl transition-colors inline-block">
                    Manage
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="bg-secondary/40 border border-white/5 rounded-2xl p-12 text-center text-zinc-500 shadow-xl">
      No orders found.
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php require_once '../includes/admin_footer.php'; ?>
