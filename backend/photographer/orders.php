<?php
$pageTitle = 'My Orders – SD Colours';
require_once '../includes/auth.php';
requirePhotographer();
require_once '../includes/db.php';

$db     = getDB();
$userId = $_SESSION['user_id'];
$viewId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$order  = null;
$items  = [];

if ($viewId) {
    $stmt = $db->prepare("SELECT * FROM orders WHERE id=? AND photographer_id=?");
    $stmt->execute([$viewId, $userId]);
    $order = $stmt->fetch();
    if ($order) {
        $iStmt = $db->prepare("SELECT * FROM order_items WHERE order_id=?");
        $iStmt->execute([$viewId]);
        $items = $iStmt->fetchAll();
    }
}

$stmt = $db->prepare("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id=oi.order_id WHERE o.photographer_id=? GROUP BY o.id ORDER BY o.created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

$statusMap = [
    'pending'    => ['bg-yellow-500/10 text-yellow-400', '⏳', 'Pending'],
    'paid'       => ['bg-green-500/10 text-green-400',  '💵', 'Paid'],
    'processing' => ['bg-blue-500/10 text-blue-400',    '⚙️', 'Processing'],
    'shipped'    => ['bg-purple-500/10 text-purple-400', '🚚', 'Shipped'],
    'delivered'  => ['bg-green-500/10 text-green-400',  '✅', 'Delivered'],
    'cancelled'  => ['bg-red-500/10 text-red-400',      '❌', 'Cancelled'],
];

require_once '../includes/photographer_header.php';
?>

<!-- ═══ MOBILE ═══ -->
<div class="md:hidden pt-14 pb-24 mobile-nav-safe">

  <!-- Header bar -->
  <div class="px-4 pt-5 pb-4 flex items-center gap-3">
    <?php if ($order): ?>
    <a href="/photographer/orders.php" class="w-9 h-9 rounded-xl bg-darkcard border border-white/10 flex items-center justify-center flex-shrink-0 touch-btn">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-zinc-300"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
    </a>
    <div>
      <h1 class="text-xl font-black text-white">Order #<?= $order['id'] ?></h1>
      <p class="text-zinc-500 text-xs"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
    </div>
    <?php else: ?>
    <div class="flex-grow">
      <h1 class="text-xl font-black text-white">My Orders</h1>
      <p class="text-zinc-500 text-xs"><?= count($orders) ?> total</p>
    </div>
    <a href="/photographer/shop.php" class="bg-primary text-secondary text-xs font-bold px-4 py-2.5 rounded-xl flex-shrink-0 touch-btn">+ New Order</a>
    <?php endif; ?>
  </div>

  <?php if ($order): ?>
  <!-- Order Detail View -->
  <?php [$sc, $si, $sl] = $statusMap[$order['status']] ?? ['bg-zinc-500/10 text-zinc-400', '📄', ucfirst($order['status'])]; ?>

  <!-- Status banner -->
  <div class="mx-4 mb-4 <?= $sc ?> rounded-2xl px-4 py-3 flex items-center gap-3">
    <span class="text-xl"><?= $si ?></span>
    <div>
      <div class="font-bold text-sm"><?= $sl ?></div>
      <div class="text-xs opacity-70">Order status</div>
    </div>
  </div>

  <!-- Items -->
  <div class="mx-4 bg-darkcard border border-white/5 rounded-2xl overflow-hidden mb-4">
    <div class="px-4 py-3 border-b border-white/5">
      <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Items Ordered</span>
    </div>
    <?php foreach ($items as $item): ?>
    <div class="px-4 py-3.5 border-b border-white/5 last:border-b-0">
      <div class="flex justify-between gap-2">
        <div class="flex-grow min-w-0">
          <div class="text-white font-bold text-sm leading-tight truncate"><?= htmlspecialchars($item['product_name']) ?></div>
          <?php if ($item['size']): ?><div class="text-zinc-500 text-xs mt-0.5">Size: <?= htmlspecialchars($item['size']) ?></div><?php endif; ?>
          <?php if ($item['notes']): ?><div class="text-zinc-600 text-xs mt-0.5 italic"><?= htmlspecialchars($item['notes']) ?></div><?php endif; ?>
          <div class="text-zinc-500 text-xs mt-1"><?= $item['quantity'] ?> × ₹<?= number_format($item['unit_price']) ?></div>
        </div>
        <div class="text-primary font-black text-sm flex-shrink-0">₹<?= number_format($item['unit_price'] * $item['quantity']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
    <div class="px-4 py-4 bg-white/3 flex justify-between items-center">
      <span class="text-zinc-300 font-bold">Total</span>
      <span class="text-primary font-black text-xl">₹<?= number_format($order['total']) ?></span>
    </div>
  </div>

  <!-- Shipping Address (Mobile) -->
  <?php if (!empty($order['shipping_address'])): ?>
  <div class="mx-4 mb-3 bg-darkcard border border-white/5 rounded-2xl px-4 py-3">
    <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Shipping Address</div>
    <div class="text-zinc-300 text-sm whitespace-pre-line"><?= htmlspecialchars($order['shipping_address']) ?></div>
  </div>
  <?php endif; ?>

  <!-- Notes -->
  <?php if ($order['notes']): ?>
  <div class="mx-4 mb-3 bg-darkcard border border-white/5 rounded-2xl px-4 py-3">
    <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Your Note</div>
    <div class="text-zinc-300 text-sm"><?= htmlspecialchars($order['notes']) ?></div>
  </div>
  <?php endif; ?>

  <!-- Drive Link -->
  <?php if (!empty($order['drive_link'])): ?>
  <div class="mx-4 mb-3 bg-darkcard border border-white/5 rounded-2xl px-4 py-3">
    <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Google Drive / WeTransfer Link</div>
    <a href="<?= htmlspecialchars($order['drive_link']) ?>" target="_blank" class="text-primary text-sm font-bold hover:underline break-all block mt-1">
      🔗 Visit Link
    </a>
  </div>
  <?php endif; ?>

  <?php if ($order['admin_notes']): ?>
  <div class="mx-4 mb-3 bg-blue-500/5 border border-blue-500/20 rounded-2xl px-4 py-3">
    <div class="text-[10px] font-bold text-blue-400 uppercase tracking-wider mb-1">📋 Admin Note</div>
    <div class="text-zinc-300 text-sm"><?= htmlspecialchars($order['admin_notes']) ?></div>
  </div>
  <?php endif; ?>

  <div class="px-4">
    <a href="/photographer/shop.php" class="flex items-center justify-center gap-2 w-full bg-primary text-secondary font-bold text-sm py-4 rounded-2xl touch-btn">
      + Place Another Order
    </a>
  </div>

  <?php elseif (empty($orders)): ?>
  <!-- Empty state -->
  <div class="mx-4 mt-8 bg-darkcard border border-white/5 rounded-3xl py-16 text-center px-6">
    <div class="text-6xl mb-4">📋</div>
    <h2 class="text-white font-bold text-lg mb-2">No orders yet</h2>
    <p class="text-zinc-500 text-sm mb-6">Start browsing products to place your first order.</p>
    <a href="/photographer/shop.php" class="bg-primary text-secondary text-sm font-bold px-8 py-3.5 rounded-2xl inline-block touch-btn">Browse Products</a>
  </div>

  <?php else: ?>
  <!-- Orders list -->
  <div class="px-4 space-y-3">
    <?php foreach ($orders as $o):
      [$sc, $si, $sl] = $statusMap[$o['status']] ?? ['bg-zinc-500/10 text-zinc-400', '📄', ucfirst($o['status'])];
    ?>
    <a href="/photographer/orders.php?id=<?= $o['id'] ?>" class="bg-darkcard border border-white/5 rounded-2xl p-4 flex items-center gap-4 touch-btn active:bg-white/5 block hover:border-primary/20 transition-all">
      <div class="w-12 h-12 rounded-2xl <?= $sc ?> flex items-center justify-center flex-shrink-0 text-xl"><?= $si ?></div>
      <div class="flex-grow min-w-0">
        <div class="flex items-center justify-between gap-2">
          <span class="text-white font-black text-sm">Order #<?= $o['id'] ?></span>
          <span class="text-white font-black text-sm">₹<?= number_format($o['total']) ?></span>
        </div>
        <div class="flex items-center justify-between mt-1 gap-2">
          <span class="text-zinc-500 text-xs"><?= date('d M Y', strtotime($o['created_at'])) ?> · <?= $o['item_count'] ?> item<?= $o['item_count'] != 1 ? 's' : '' ?></span>
          <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $sc ?>"><?= $sl ?></span>
        </div>
      </div>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-zinc-600 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ═══ DESKTOP ═══ -->
<div class="hidden md:block">
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
      <?php if ($order): ?>
      <a href="/photographer/orders.php" class="text-zinc-400 hover:text-white transition-all text-sm">← All Orders</a>
      <h1 class="text-2xl font-bold text-white">Order #<?= $order['id'] ?></h1>
      <?php else: ?>
      <h1 class="text-2xl font-bold text-white">My Orders</h1>
      <?php endif; ?>
    </div>
    <?php if (!$order): ?>
    <a href="/photographer/shop.php" class="bg-primary text-secondary px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-primary-dark transition-all">+ New Order</a>
    <?php endif; ?>
  </div>

  <?php if ($order): ?>
  <?php [$sc, $si, $sl] = $statusMap[$order['status']] ?? ['bg-zinc-500/10 text-zinc-400', '📄', ucfirst($order['status'])]; ?>
  <div class="bg-darkcard border border-white/5 rounded-2xl overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center">
      <div>
        <div class="text-zinc-400 text-xs">Order Date</div>
        <div class="text-white font-bold"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></div>
      </div>
      <span class="px-4 py-1.5 rounded-full text-sm font-bold <?= $sc ?>"><?= $sl ?></span>
    </div>
    <?php foreach ($items as $item): ?>
    <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center">
      <div>
        <div class="text-white font-bold"><?= htmlspecialchars($item['product_name']) ?><?= $item['size'] ? " – {$item['size']}" : '' ?></div>
        <?php if ($item['notes']): ?><div class="text-zinc-500 text-xs mt-0.5 italic"><?= htmlspecialchars($item['notes']) ?></div><?php endif; ?>
        <div class="text-zinc-500 text-xs mt-1"><?= $item['quantity'] ?> × ₹<?= number_format($item['unit_price']) ?></div>
      </div>
      <div class="text-primary font-black text-lg">₹<?= number_format($item['unit_price'] * $item['quantity']) ?></div>
    </div>
    <?php endforeach; ?>
    <div class="px-6 py-4 flex justify-between items-center bg-white/3">
      <span class="text-white font-bold text-lg">Total</span>
      <span class="text-primary font-black text-2xl">₹<?= number_format($order['total']) ?></span>
    </div>

    <!-- Shipping Address (Desktop) -->
    <?php if (!empty($order['shipping_address'])): ?>
    <div class="px-6 py-4 border-t border-white/5 bg-white/2">
      <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Shipping Address</div>
      <div class="text-zinc-300 whitespace-pre-line"><?= htmlspecialchars($order['shipping_address']) ?></div>
    </div>
    <?php endif; ?>

    <?php if ($order['notes']): ?>
    <div class="px-6 py-4 border-t border-white/5 bg-white/2">
      <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Your Note</div>
      <div class="text-zinc-300"><?= htmlspecialchars($order['notes']) ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($order['drive_link'])): ?>
    <div class="px-6 py-4 border-t border-white/5 bg-white/2">
      <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Google Drive / WeTransfer Link</div>
      <a href="<?= htmlspecialchars($order['drive_link']) ?>" target="_blank" class="text-primary text-sm font-bold hover:underline break-all block mt-1">
        <?= htmlspecialchars($order['drive_link']) ?>
      </a>
    </div>
    <?php endif; ?>
    <?php if ($order['admin_notes']): ?>
    <div class="px-6 py-4 border-t border-blue-500/20 bg-blue-500/5">
      <div class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Admin Note</div>
      <div class="text-zinc-300"><?= htmlspecialchars($order['admin_notes']) ?></div>
    </div>
    <?php endif; ?>
  </div>

  <?php elseif (empty($orders)): ?>
  <div class="bg-darkcard border border-white/5 rounded-2xl py-20 text-center">
    <div class="text-6xl mb-4">📋</div>
    <h2 class="text-white font-bold text-xl mb-2">No orders yet</h2>
    <p class="text-zinc-400 mb-6">Start shopping to place your first order.</p>
    <a href="/photographer/shop.php" class="bg-primary text-secondary text-sm font-bold px-8 py-3 rounded-xl hover:bg-primary-dark transition-all">Browse Products</a>
  </div>

  <?php else: ?>
  <div class="bg-darkcard border border-white/5 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="border-b border-white/5">
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Order #</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Date</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Items</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Total</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Action</th>
        </tr></thead>
        <tbody class="divide-y divide-white/5">
          <?php foreach ($orders as $o):
            [$sc, $si, $sl] = $statusMap[$o['status']] ?? ['bg-zinc-500/10 text-zinc-400', '📄', ucfirst($o['status'])];
          ?>
          <tr class="hover:bg-white/2 transition-colors">
            <td class="px-6 py-4 text-white font-bold">#<?= $o['id'] ?></td>
            <td class="px-6 py-4 text-zinc-400"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td class="px-6 py-4 text-zinc-300"><?= $o['item_count'] ?> item<?= $o['item_count'] != 1 ? 's' : '' ?></td>
            <td class="px-6 py-4 text-white font-semibold">₹<?= number_format($o['total']) ?></td>
            <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold <?= $sc ?>"><?= $sl ?></span></td>
            <td class="px-6 py-4"><a href="/photographer/orders.php?id=<?= $o['id'] ?>" class="text-primary text-xs font-bold hover:underline">View Details →</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once '../includes/photographer_footer.php'; ?>
