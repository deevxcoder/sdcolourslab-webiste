<?php
$pageTitle = 'My Cart – SD Colours';
require_once '../includes/auth.php';
requirePhotographer();
require_once '../includes/db.php';
startSession();

if (isset($_POST['remove'])) {
    unset($_SESSION['cart'][$_POST['remove']]);
    header('Location: /photographer/cart.php'); exit;
}
if (isset($_POST['update_qty'])) {
    $key = $_POST['cart_key'];
    $qty = max(1, (int)$_POST['quantity']);
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['quantity'] = $qty;
        
        $db = getDB();
        $stmt = $db->prepare('SELECT category, price, features FROM products WHERE id=?');
        $stmt->execute([$_SESSION['cart'][$key]['product_id']]);
        $product = $stmt->fetch();
        if ($product) {
            if ($product['category'] === 'led_frame') {
                $_SESSION['cart'][$key]['price'] = getLEDFrameDiscountPrice(
                    $qty,
                    json_decode($product['features'], true),
                    (float)$product['price']
                );
            }
        }
    }
    header('Location: /photographer/cart.php'); exit;
}

$cart  = $_SESSION['cart'] ?? [];
$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));

require_once '../includes/photographer_header.php';
?>

<!-- ═══ MOBILE ═══ -->
<div class="md:hidden pt-14 mobile-nav-safe">

  <!-- Header -->
  <div class="px-4 pt-5 pb-4 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-black text-white">My Cart</h1>
      <p class="text-zinc-500 text-xs"><?= count($cart) ?> item<?= count($cart) !== 1 ? 's' : '' ?></p>
    </div>
    <a href="/photographer/shop.php" class="text-primary text-sm font-bold">+ Add More</a>
  </div>

  <?php if (empty($cart)): ?>
  <!-- Empty cart -->
  <div class="mx-4 mt-8 bg-darkcard border border-white/5 rounded-3xl py-16 text-center px-6">
    <div class="text-6xl mb-4">🛒</div>
    <h2 class="text-white font-bold text-lg mb-2">Cart is empty</h2>
    <p class="text-zinc-500 text-sm mb-6">Browse our products and add items to get started.</p>
    <a href="/photographer/shop.php" class="bg-primary text-secondary text-sm font-bold px-8 py-3.5 rounded-2xl inline-block touch-btn">Browse Products</a>
  </div>

  <?php else: ?>
  <!-- Cart Items list -->
  <div class="px-4 space-y-3 mb-4">
    <?php foreach ($cart as $key => $item): ?>
    <div class="bg-darkcard border border-white/5 rounded-2xl p-4">
      <div class="flex justify-between gap-2 mb-3">
        <div class="flex-grow min-w-0">
          <div class="text-white font-bold text-sm leading-snug"><?= htmlspecialchars($item['name']) ?></div>
          <?php if ($item['size']): ?><div class="text-zinc-500 text-xs mt-0.5">Size: <?= htmlspecialchars($item['size']) ?></div><?php endif; ?>
          <?php if ($item['notes']): ?><div class="text-zinc-600 text-xs mt-0.5 italic"><?= htmlspecialchars($item['notes']) ?></div><?php endif; ?>
          <div class="text-primary font-black text-base mt-1">₹<?= number_format($item['price'] * $item['quantity']) ?></div>
          <div class="text-zinc-600 text-xs">₹<?= number_format($item['price']) ?> each</div>
        </div>
        <!-- Remove button -->
        <form method="POST" class="flex-shrink-0">
          <input type="hidden" name="remove" value="<?= htmlspecialchars($key) ?>">
          <button type="submit" class="w-8 h-8 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center touch-btn active:bg-red-500/20" title="Remove">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-red-400"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
          </button>
        </form>
      </div>
      <!-- Qty stepper -->
      <form method="POST" class="flex items-center gap-3">
        <input type="hidden" name="cart_key" value="<?= htmlspecialchars($key) ?>">
        <div class="flex items-center bg-zinc-800 border border-white/10 rounded-xl overflow-hidden">
          <button type="button" onclick="stepQty(this,-1)" class="px-4 py-2 text-zinc-400 text-lg font-bold touch-btn active:bg-white/10">−</button>
          <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="999" class="w-12 text-center bg-transparent text-white font-bold text-sm focus:outline-none" />
          <button type="button" onclick="stepQty(this,1)" class="px-4 py-2 text-zinc-400 text-lg font-bold touch-btn active:bg-white/10">+</button>
        </div>
        <button type="submit" name="update_qty" class="flex-1 bg-white/5 border border-white/10 text-zinc-300 text-xs font-bold py-2.5 rounded-xl touch-btn active:bg-white/10">Update</button>
      </form>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Sticky checkout footer -->
  <div class="sticky bottom-16 left-0 right-0 px-4 pb-4 bg-darkbg/95 backdrop-blur-sm border-t border-white/5 pt-4">
    <div class="flex justify-between items-center mb-3">
      <span class="text-zinc-400 text-sm">Order Total</span>
      <span class="text-primary font-black text-2xl">₹<?= number_format($total) ?></span>
    </div>
    <a href="/photographer/checkout.php" class="flex items-center justify-center gap-2 w-full bg-primary text-secondary font-black text-base py-4 rounded-2xl touch-btn shadow-xl shadow-primary/20">
      Place Order
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
    </a>
    <p class="text-zinc-600 text-center text-xs mt-2">* Shipping calculated at confirmation</p>
  </div>
  <?php endif; ?>
</div>

<!-- ═══ DESKTOP ═══ -->
<div class="hidden md:block">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">My Cart</h1>
    <a href="/photographer/shop.php" class="text-primary text-sm font-bold hover:underline">← Continue Shopping</a>
  </div>

  <?php if (empty($cart)): ?>
  <div class="bg-darkcard border border-white/5 rounded-2xl py-20 text-center">
    <div class="text-6xl mb-4">🛒</div>
    <h2 class="text-white font-bold text-xl mb-2">Your cart is empty</h2>
    <p class="text-zinc-400 mb-6">Browse products and add items to get started.</p>
    <a href="/photographer/shop.php" class="bg-primary text-secondary text-sm font-bold px-8 py-3 rounded-xl hover:bg-primary-dark transition-all">Browse Products</a>
  </div>
  <?php else: ?>
  <div class="bg-darkcard border border-white/5 rounded-2xl overflow-hidden mb-6">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="border-b border-white/5">
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Product</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Size</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Price</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Qty</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Subtotal</th>
          <th class="px-6 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-white/5">
          <?php foreach ($cart as $key => $item): ?>
          <tr class="hover:bg-white/2 transition-colors">
            <td class="px-6 py-4">
              <div class="text-white font-bold"><?= htmlspecialchars($item['name']) ?></div>
              <?php if ($item['notes']): ?><div class="text-zinc-500 text-xs mt-0.5 italic"><?= htmlspecialchars($item['notes']) ?></div><?php endif; ?>
            </td>
            <td class="px-6 py-4 text-zinc-400"><?= htmlspecialchars($item['size'] ?: '—') ?></td>
            <td class="px-6 py-4 text-zinc-300">₹<?= number_format($item['price']) ?></td>
            <td class="px-6 py-4">
              <form method="POST" class="flex items-center gap-2">
                <input type="hidden" name="cart_key" value="<?= htmlspecialchars($key) ?>">
                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="999" class="w-16 bg-zinc-800 border border-white/10 text-white rounded-lg px-2 py-1.5 text-sm text-center focus:border-primary focus:outline-none" />
                <button type="submit" name="update_qty" class="text-primary text-xs font-bold hover:underline">Update</button>
              </form>
            </td>
            <td class="px-6 py-4 text-white font-bold">₹<?= number_format($item['price'] * $item['quantity']) ?></td>
            <td class="px-6 py-4">
              <form method="POST">
                <input type="hidden" name="remove" value="<?= htmlspecialchars($key) ?>">
                <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-bold transition-colors">Remove</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="bg-darkcard border border-white/5 rounded-2xl p-6 flex justify-between items-center flex-wrap gap-4">
    <div>
      <div class="text-zinc-400 text-sm">Order Total</div>
      <div class="text-primary font-black text-3xl">₹<?= number_format($total) ?></div>
      <div class="text-zinc-600 text-xs mt-1">* Shipping calculated on order confirmation</div>
    </div>
    <div class="flex gap-3 flex-wrap">
      <a href="/photographer/shop.php" class="px-6 py-3 border border-white/10 rounded-xl text-zinc-300 font-semibold text-sm hover:bg-white/5 transition-all">← Shop More</a>
      <a href="/photographer/checkout.php" class="px-8 py-3 bg-primary text-secondary font-bold text-sm rounded-xl hover:bg-primary-dark transition-all">Place Order →</a>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
function stepQty(btn, delta) {
  const input = btn.parentElement.querySelector('input[type=number]');
  input.value = Math.max(1, Math.min(999, parseInt(input.value || 1) + delta));
}
</script>

<?php require_once '../includes/photographer_footer.php'; ?>
