<?php
$pageTitle = 'Checkout – SD Colours';
require_once '../includes/auth.php';
requirePhotographer();
require_once '../includes/db.php';

startSession();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: /photographer/cart.php');
    exit;
}

$db = getDB();
$userId = $_SESSION['user_id'];
$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
$error = '';
$success = false;

// Auto-migrate: ensure user_addresses table and shipping_address column exist
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `user_addresses` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` int(10) UNSIGNED NOT NULL,
        `label` varchar(150) NOT NULL,
        `address_line` text NOT NULL,
        `city` varchar(100) NOT NULL,
        `state` varchar(100) NOT NULL,
        `pincode` varchar(20) NOT NULL,
        `phone` varchar(20) DEFAULT NULL,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `fk_addr_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (PDOException $e) { /* table may already exist */ }

try {
    $db->exec("ALTER TABLE `orders` ADD COLUMN `shipping_address` TEXT DEFAULT NULL");
} catch (PDOException $e) { /* column may already exist */ }

try {
    $db->exec("ALTER TABLE `orders` ADD COLUMN `drive_link` VARCHAR(500) DEFAULT NULL");
} catch (PDOException $e) { /* column may already exist */ }

// Fetch saved addresses
$addrStmt = $db->prepare("SELECT * FROM user_addresses WHERE user_id=? ORDER BY id DESC");
$addrStmt->execute([$userId]);
$savedAddresses = $addrStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notes = trim($_POST['notes'] ?? '');
    $driveLink = trim($_POST['drive_link'] ?? '');
    $addressSelection = trim($_POST['address_selection'] ?? 'new');
    
    try {
        $db->beginTransaction();
        
        if (empty($driveLink)) {
            throw new Exception("Please provide a Google Drive or WeTransfer link for your files.");
        }
        
        $shippingAddress = '';
        
        if ($addressSelection !== 'new' && !empty($savedAddresses)) {
            // Selected a saved address
            $addressId = (int)$addressSelection;
            $stmt = $db->prepare("SELECT * FROM user_addresses WHERE id=? AND user_id=?");
            $stmt->execute([$addressId, $userId]);
            $addr = $stmt->fetch();
            if (!$addr) {
                throw new Exception("Invalid address selection.");
            }
            $shippingAddress = $addr['label'] . "\n"
                             . $addr['address_line'] . "\n"
                             . $addr['city'] . ", " . $addr['state'] . " - " . $addr['pincode'] . "\n"
                             . "Phone: " . ($addr['phone'] ?: 'N/A');
        } else {
            // Adding a new address
            $label       = trim($_POST['new_label'] ?? '');
            $addressLine = trim($_POST['new_address_line'] ?? '');
            $city        = trim($_POST['new_city'] ?? '');
            $state       = trim($_POST['new_state'] ?? '');
            $pincode     = trim($_POST['new_pincode'] ?? '');
            $phone       = trim($_POST['new_phone'] ?? '');
            
            if (empty($label) || empty($addressLine) || empty($city) || empty($state) || empty($pincode)) {
                throw new Exception("Please fill in all required shipping address fields.");
            }
            
            // Insert new address
            $stmt = $db->prepare("INSERT INTO user_addresses (user_id, label, address_line, city, state, pincode, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $label, $addressLine, $city, $state, $pincode, $phone]);
            
            $shippingAddress = $label . "\n"
                             . $addressLine . "\n"
                             . $city . ", " . $state . " - " . $pincode . "\n"
                             . "Phone: " . ($phone ?: 'N/A');
        }
        
        $secureKey = bin2hex(random_bytes(16));
        $stmt = $db->prepare("INSERT INTO orders (photographer_id, total, notes, status, shipping_address, drive_link, secure_key, net_pay) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?)");
        $stmt->execute([$userId, $total, $notes, $shippingAddress, $driveLink, $secureKey, $total]);
        $orderId = $db->lastInsertId();
        
        foreach ($cart as $item) {
            $is = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, size, quantity, unit_price, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $is->execute([$orderId, $item['product_id'], $item['name'], $item['size'], $item['quantity'], $item['price'], $item['notes']]);
        }
        
        $db->commit();
        unset($_SESSION['cart']);
        $success = $orderId;
    } catch (Exception $e) {
        $db->rollBack();
        $error = $e->getMessage() ?: 'Failed to place order. Please try again.';
    }
}

$user = getCurrentUser();
require_once '../includes/photographer_header.php';
?>
  <!-- Responsive Checkout Layout (Premium Dark Theme) -->
  <?php if ($success): ?>
  <!-- SUCCESS STATE -->
  <div class="max-w-md mx-auto pt-20 pb-24 px-4 text-center">
    <div class="w-20 h-20 rounded-full bg-green-500/10 border border-green-500/30 flex items-center justify-center mx-auto mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-10 h-10 text-green-400">
        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
      </svg>
    </div>
    
    <h1 class="text-2xl font-black text-white mb-2">Order Placed!</h1>
    <p class="text-zinc-400 text-sm mb-8 leading-relaxed">
      Your order <strong class="text-primary">#<?= $success ?></strong> has been received. We'll process it shortly and update the status in your dashboard.
    </p>
    
    <div class="flex flex-col gap-3">
      <a href="/photographer/orders.php" class="w-full bg-primary text-secondary font-bold py-4 rounded-2xl text-center touch-btn shadow-lg shadow-primary/10">
        View My Orders
      </a>
      <a href="/photographer/shop.php" class="w-full bg-darkcard border border-white/5 text-zinc-300 font-semibold py-4 rounded-2xl text-center touch-btn">
        Shop More
      </a>
    </div>
  </div>

  <?php else: ?>
  
  <!-- ═══ MOBILE LAYOUT ═══ -->
  <div class="md:hidden pt-14 pb-24 mobile-nav-safe">
    <!-- Header -->
    <div class="px-4 pt-5 pb-4 flex items-center gap-3">
      <a href="/photographer/cart.php" class="w-9 h-9 rounded-xl bg-darkcard border border-white/10 flex items-center justify-center flex-shrink-0 touch-btn">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-zinc-300">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
      </a>
      <div>
        <h1 class="text-xl font-black text-white">Checkout</h1>
        <p class="text-zinc-500 text-xs">Review your B2B order details</p>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="mx-4 mb-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl p-4 text-sm text-center">
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Entire checkout page wrapped in form to submit all details together -->
    <form method="POST" class="space-y-4">
      <!-- Order Summary Card -->
      <div class="mx-4 bg-darkcard border border-white/5 rounded-2xl overflow-hidden">
        <div class="px-4 py-3 border-b border-white/5 flex justify-between items-center">
          <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Order Summary</span>
          <span class="text-xs text-zinc-500"><?= count($cart) ?> item<?= count($cart) !== 1 ? 's' : '' ?></span>
        </div>
        <div class="divide-y divide-white/5">
          <?php foreach ($cart as $item): ?>
          <div class="px-4 py-3.5 flex justify-between items-start gap-2">
            <div class="min-w-0">
              <div class="text-white font-bold text-sm leading-snug"><?= htmlspecialchars($item['name']) ?></div>
              <?php if ($item['size']): ?><div class="text-zinc-500 text-xs mt-0.5">Size: <?= htmlspecialchars($item['size']) ?></div><?php endif; ?>
              <?php if ($item['notes']): ?><div class="text-zinc-600 text-xs mt-0.5 italic"><?= htmlspecialchars($item['notes']) ?></div><?php endif; ?>
              <div class="text-zinc-500 text-xs mt-1"><?= $item['quantity'] ?> × ₹<?= number_format($item['price']) ?></div>
            </div>
            <div class="text-white font-bold text-sm flex-shrink-0">₹<?= number_format($item['price'] * $item['quantity']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="px-4 py-4 bg-white/3 flex justify-between items-center border-t border-white/5">
          <span class="text-zinc-300 font-bold">Total</span>
          <span class="text-primary font-black text-xl">₹<?= number_format($total) ?></span>
        </div>
      </div>

      <!-- Shipping Address Card -->
      <div class="mx-4 bg-darkcard border border-white/5 rounded-2xl p-4">
        <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-3">Shipping Address</div>
        
        <?php if (!empty($savedAddresses)): ?>
        <!-- Radio Group for Saved Addresses -->
        <div class="space-y-3 mb-4">
          <?php foreach ($savedAddresses as $idx => $addr): ?>
          <label class="flex items-start gap-3 bg-white/3 rounded-xl p-3 border border-white/5 cursor-pointer hover:border-primary/40 transition-all block">
            <input type="radio" name="address_selection" value="<?= $addr['id'] ?>" <?= $idx === 0 ? 'checked' : '' ?>
                   onchange="toggleNewAddressForm(false)"
                   class="mt-1 accent-primary flex-shrink-0" />
            <div class="text-sm">
              <div class="font-bold text-white"><?= htmlspecialchars($addr['label']) ?></div>
              <div class="text-zinc-400 text-xs mt-0.5"><?= htmlspecialchars($addr['address_line']) ?></div>
              <div class="text-zinc-500 text-[11px] mt-0.5"><?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['state']) ?> - <?= htmlspecialchars($addr['pincode']) ?></div>
              <?php if ($addr['phone']): ?><div class="text-zinc-500 text-[11px]">Phone: <?= htmlspecialchars($addr['phone']) ?></div><?php endif; ?>
            </div>
          </label>
          <?php endforeach; ?>
          
          <label class="flex items-center gap-3 bg-white/3 rounded-xl p-3 border border-white/5 cursor-pointer hover:border-primary/40 transition-all block">
            <input type="radio" name="address_selection" value="new"
                   onchange="toggleNewAddressForm(true)"
                   class="accent-primary flex-shrink-0" />
            <span class="text-sm font-bold text-white">+ Add New Address</span>
          </label>
        </div>
        <?php else: ?>
        <input type="hidden" name="address_selection" value="new" />
        <?php endif; ?>

        <!-- Add New Address Form (Hidden by default if we have saved addresses) -->
        <div id="new-address-form" class="<?= empty($savedAddresses) ? '' : 'hidden' ?> space-y-3 pt-2">
          <?php if (!empty($savedAddresses)): ?>
          <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2 border-t border-white/5 pt-3">New Shipping Address</div>
          <?php endif; ?>
          <div>
            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Address Label (e.g. Home, Office, Client Name)</label>
            <input type="text" name="new_label" placeholder="e.g. Home, Office, Client: Rajesh"
                   class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-2.5 text-xs focus:border-primary focus:outline-none" />
          </div>
          <div>
            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Street Address / Landmark</label>
            <textarea name="new_address_line" rows="2" placeholder="House No, Street, Area, Landmark"
                      class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-2.5 text-xs focus:border-primary focus:outline-none"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">City</label>
              <input type="text" name="new_city" placeholder="City" value="<?= htmlspecialchars($user['city'] ?? '') ?>"
                     class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-2.5 text-xs focus:border-primary focus:outline-none" />
            </div>
            <div>
              <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">State</label>
              <input type="text" name="new_state" placeholder="State" value="Odisha"
                     class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-2.5 text-xs focus:border-primary focus:outline-none" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Pincode</label>
              <input type="text" name="new_pincode" placeholder="Pincode"
                     class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-2.5 text-xs focus:border-primary focus:outline-none" />
            </div>
            <div>
              <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Contact Phone</label>
              <input type="text" name="new_phone" placeholder="Phone (optional)" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                     class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-2.5 text-xs focus:border-primary focus:outline-none" />
            </div>
          </div>
        </div>
      </div>

      <!-- Drive Link Card -->
      <div class="mx-4 bg-darkcard border border-white/5 rounded-2xl p-4">
        <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Google Drive / WeTransfer Link *</label>
        <input type="url" name="drive_link" required placeholder="https://drive.google.com/drive/folders/..."
               class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-xs placeholder-zinc-600 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
        <p class="text-zinc-500 text-[10px] mt-1.5 leading-relaxed">Please upload your album sheets/photos and share the link here. Make sure sharing is set to "Anyone with the link".</p>
      </div>

      <!-- Notes Card -->
      <div class="mx-4 bg-darkcard border border-white/5 rounded-2xl p-4">
        <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Order Notes / Instructions</label>
        <textarea name="notes" rows="3" placeholder="Any specific requirements, sizing, layout notes, cover details, etc."
                  class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl p-3 text-sm placeholder-zinc-600 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
      </div>

      <div class="mx-4 pt-2">
        <button type="submit" class="flex items-center justify-center gap-2 w-full bg-primary text-secondary font-black text-base py-4 rounded-2xl touch-btn shadow-xl shadow-primary/20">
          Confirm Order
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
        </button>
        <p class="text-zinc-600 text-center text-xs mt-3">* Shipping costs will be added at billing confirmation</p>
      </div>
    </form>
  </div>

  <!-- ═══ DESKTOP LAYOUT ═══ -->
  <div class="hidden md:block">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-white">Review & Place Order</h1>
      <a href="/photographer/cart.php" class="text-primary text-sm font-bold hover:underline">← Edit Cart</a>
    </div>

    <?php if ($error): ?>
    <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 text-sm font-semibold">
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left side: Shipping Details & Notes Form (2 cols) -->
      <div class="lg:col-span-2">
        <form method="POST" id="checkout-desktop-form" class="space-y-6">
          <!-- Shipping Address Selection -->
          <div class="bg-darkcard border border-white/5 rounded-2xl p-6">
            <h2 class="text-base font-bold text-white mb-4 uppercase tracking-wider">Shipping Address</h2>
            
            <?php if (!empty($savedAddresses)): ?>
            <!-- Grid of Saved Addresses -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <?php foreach ($savedAddresses as $idx => $addr): ?>
              <label class="flex items-start gap-3 bg-white/3 rounded-xl p-4 border border-white/5 cursor-pointer hover:border-primary/45 hover:bg-white/5 transition-all">
                <input type="radio" name="address_selection" value="<?= $addr['id'] ?>" <?= $idx === 0 ? 'checked' : '' ?>
                       onchange="toggleNewAddressForm(false)"
                       class="mt-1 accent-primary flex-shrink-0" />
                <div class="text-sm">
                  <div class="font-bold text-white"><?= htmlspecialchars($addr['label']) ?></div>
                  <div class="text-zinc-400 text-xs mt-1 leading-snug"><?= htmlspecialchars($addr['address_line']) ?></div>
                  <div class="text-zinc-500 text-[11px] mt-1"><?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['state']) ?> - <?= htmlspecialchars($addr['pincode']) ?></div>
                  <?php if ($addr['phone']): ?><div class="text-zinc-500 text-[11px] mt-0.5">Phone: <?= htmlspecialchars($addr['phone']) ?></div><?php endif; ?>
                </div>
              </label>
              <?php endforeach; ?>
              
              <label class="flex items-center gap-3 bg-white/3 rounded-xl p-4 border border-white/5 cursor-pointer hover:border-primary/45 hover:bg-white/5 transition-all">
                <input type="radio" name="address_selection" value="new"
                       onchange="toggleNewAddressForm(true)"
                       class="accent-primary flex-shrink-0" />
                <span class="text-sm font-bold text-white">+ Add New Address</span>
              </label>
            </div>
            <?php else: ?>
            <input type="hidden" name="address_selection" value="new" />
            <?php endif; ?>

            <!-- New Address Fields (Desktop) -->
            <div id="new-address-form-desktop" class="<?= empty($savedAddresses) ? '' : 'hidden' ?> space-y-4 pt-4">
              <?php if (!empty($savedAddresses)): ?>
              <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2 border-t border-white/5 pt-4">New Shipping Address</div>
              <?php endif; ?>
              <div>
                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Address Label (e.g. Home, Office, Client Name)</label>
                <input type="text" name="new_label" placeholder="e.g. Home, My Studio, Client: Ravi"
                       class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-primary focus:outline-none" />
              </div>
              <div>
                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Street Address / Landmark</label>
                <textarea name="new_address_line" rows="2" placeholder="House No, Street, Area, Landmark"
                          class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-primary focus:outline-none"></textarea>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">City</label>
                  <input type="text" name="new_city" placeholder="City" value="<?= htmlspecialchars($user['city'] ?? '') ?>"
                         class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-primary focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">State</label>
                  <input type="text" name="new_state" placeholder="State" value="Odisha"
                         class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-primary focus:outline-none" />
                </div>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Pincode</label>
                  <input type="text" name="new_pincode" placeholder="Pincode"
                         class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-primary focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Contact Phone</label>
                  <input type="text" name="new_phone" placeholder="Phone (optional)" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                         class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-primary focus:outline-none" />
                </div>
              </div>
            </div>
          </div>

          <!-- Drive Link Box -->
          <div class="bg-darkcard border border-white/5 rounded-2xl p-6">
            <label class="block text-sm font-bold text-white mb-3 uppercase tracking-wider">Google Drive / WeTransfer Link *</label>
            <input type="url" name="drive_link" required placeholder="https://drive.google.com/drive/folders/..."
                   class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm placeholder-zinc-600 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
            <p class="text-zinc-500 text-xs mt-2 leading-relaxed">Upload your printing files to Google Drive or WeTransfer and paste the link here. Ensure link settings are set to **"Anyone with the link"**.</p>
          </div>

          <!-- Notes Box -->
          <div class="bg-darkcard border border-white/5 rounded-2xl p-6">
            <label class="block text-sm font-bold text-white mb-3 uppercase tracking-wider">Order Notes / Special Instructions (optional)</label>
            <textarea name="notes" rows="4" placeholder="Mention cover requirements, lamination preference, specific size adjustments, or package combinations..."
                      class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl p-4 text-sm placeholder-zinc-600 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
          </div>
        </form>
      </div>

      <!-- Right side: Summary & Confirm Button (1 col) -->
      <div class="space-y-6">
        <div class="bg-darkcard border border-white/5 rounded-2xl p-6">
          <h2 class="text-base font-bold text-white mb-4 uppercase tracking-wider">Order Summary</h2>
          
          <div class="divide-y divide-white/5 max-h-[350px] overflow-y-auto pr-1">
            <?php foreach ($cart as $item): ?>
            <div class="py-3 flex justify-between items-start gap-2">
              <div>
                <div class="text-white font-bold text-sm"><?= htmlspecialchars($item['name']) ?></div>
                <?php if ($item['size']): ?><div class="text-zinc-500 text-xs mt-0.5">Size: <?= htmlspecialchars($item['size']) ?></div><?php endif; ?>
                <?php if ($item['notes']): ?><div class="text-zinc-600 text-xs mt-0.5 italic truncate max-w-[200px]"><?= htmlspecialchars($item['notes']) ?></div><?php endif; ?>
                <div class="text-zinc-500 text-xs mt-1"><?= $item['quantity'] ?> × ₹<?= number_format($item['price']) ?></div>
              </div>
              <span class="text-white font-bold text-sm">₹<?= number_format($item['price'] * $item['quantity']) ?></span>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="border-t border-white/5 pt-4 mt-2 flex justify-between items-center">
            <span class="text-zinc-300 font-bold">Total</span>
            <span class="text-primary font-black text-2xl">₹<?= number_format($total) ?></span>
          </div>

          <div class="mt-6">
            <button type="submit" form="checkout-desktop-form" class="w-full bg-primary text-secondary font-black text-sm py-3.5 rounded-xl hover:bg-primary-dark transition-all duration-200 shadow-lg shadow-primary/10">
              Confirm Order
            </button>
            <p class="text-zinc-600 text-center text-xs mt-3">* Shipping costs will be added at billing confirmation</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

<script>
function toggleNewAddressForm(show) {
  const formMobile = document.getElementById('new-address-form');
  const formDesktop = document.getElementById('new-address-form-desktop');
  
  if (formMobile) {
    if (show) {
      formMobile.classList.remove('hidden');
      formMobile.querySelectorAll('input, textarea').forEach(el => {
        if (el.name !== 'new_phone') el.required = true;
      });
    } else {
      formMobile.classList.add('hidden');
      formMobile.querySelectorAll('input, textarea').forEach(el => el.required = false);
    }
  }
  
  if (formDesktop) {
    if (show) {
      formDesktop.classList.remove('hidden');
      formDesktop.querySelectorAll('input, textarea').forEach(el => {
        if (el.name !== 'new_phone') el.required = true;
      });
    } else {
      formDesktop.classList.add('hidden');
      formDesktop.querySelectorAll('input, textarea').forEach(el => el.required = false);
    }
  }
}

// Initial setup on load
document.addEventListener('DOMContentLoaded', () => {
  const selectedRadio = document.querySelector('input[name="address_selection"]:checked');
  if (selectedRadio) {
    toggleNewAddressForm(selectedRadio.value === 'new');
  } else {
    toggleNewAddressForm(true); // default to new if none checked
  }
});
</script>

<?php require_once '../includes/photographer_footer.php'; ?>
