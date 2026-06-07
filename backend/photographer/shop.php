<?php
$pageTitle = 'Order Products – SD Colours';
require_once '../includes/auth.php';
requirePhotographer();
require_once '../includes/db.php';
startSession();
$db = getDB();

$categories = ['album' => 'Albums', 'combo' => 'Combos', 'led_frame' => 'LED Frames', 'wall_acrylic' => 'Acrylic'];
$cat = $_GET['cat'] ?? 'album';
if (!array_key_exists($cat, $categories)) {
    $cat = 'album';
}
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int)$_POST['product_id'];
    $size      = trim($_POST['size'] ?? '');
    $qty       = max(1, (int)($_POST['quantity'] ?? 1));
    $notes     = trim($_POST['notes'] ?? '');

    $stmt = $db->prepare('SELECT * FROM products WHERE id=? AND active=1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        $price = (float)$product['price'];
        $name  = $product['name'];
        $finalSize = $size;

        if ($product['category'] === 'album') {
            $paperType = trim($_POST['paper_type'] ?? '');
            $pageCount = max(20, (int)($_POST['page_count'] ?? 30));
            
            // Find the rate for this paper type from the features list
            $features = json_decode($product['features'], true) ?: [];
            $rate = (float)$product['price']; // default fallback
            foreach ($features as $f) {
                if (preg_match('/^(.*?)\s*–\s*₹?\s*(\d+)/u', $f, $matches)) {
                    if (trim($matches[1]) === $paperType) {
                        $rate = (float)$matches[2];
                        break;
                    }
                }
            }
            $price = $rate * $pageCount;
            if ($paperType) {
                $name .= ' (' . $paperType . ')';
            }
            $finalSize = $size . ' (' . $pageCount . ' pages)';
        }

        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $cartKey = $productId . '_' . str_replace(' ', '_', $finalSize);
        
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $qty;
            if ($product['category'] === 'led_frame') {
                $newQty = $_SESSION['cart'][$cartKey]['quantity'];
                $_SESSION['cart'][$cartKey]['price'] = getLEDFrameDiscountPrice(
                    $newQty,
                    json_decode($product['features'], true),
                    (float)$product['price']
                );
            }
        } else {
            if ($product['category'] === 'led_frame') {
                $price = getLEDFrameDiscountPrice($qty, json_decode($product['features'], true), (float)$product['price']);
            }
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'name'       => $name,
                'price'      => $price,
                'size'       => $finalSize,
                'quantity'   => $qty,
                'notes'      => $notes,
            ];
        }
        $message = 'success';
    }
}

$where  = "AND category=?";
$params = [$cat];
$stmt   = $db->prepare("SELECT * FROM products WHERE active=1 $where ORDER BY sort_order");
$stmt->execute($params);
$products = $stmt->fetchAll();

// Group products of wall_acrylic and led_frame by category (preserving sort order)
$groupedProducts = [];
$acrylicIndex = null;
$ledIndex = null;

foreach ($products as $p) {
    $category = $p['category'];
    if ($category === 'wall_acrylic') {
        if ($acrylicIndex === null) {
            $groupedProducts[] = [
                'is_grouped'  => true,
                'category'    => 'wall_acrylic',
                'name'        => 'Acrylic Photo',
                'description' => '5mm Wall Acrylic Photo Print',
                'tag'         => null,
                'image'       => null,
                'variants'    => []
            ];
            $acrylicIndex = count($groupedProducts) - 1;
        }
        $sizesDecoded = json_decode($p['sizes'], true) ?: [];
        $size = !empty($sizesDecoded) ? $sizesDecoded[0] : '';
        $groupedProducts[$acrylicIndex]['variants'][] = [
            'id'          => $p['id'],
            'size'        => $size,
            'price'       => (float)$p['price'],
            'price_alt'   => $p['price_alt'] ? (float)$p['price_alt'] : null,
            'features'    => json_decode($p['features'], true) ?: [],
            'description' => $p['description']
        ];
    } elseif ($category === 'led_frame') {
        if ($ledIndex === null) {
            $groupedProducts[] = [
                'is_grouped'  => true,
                'category'    => 'led_frame',
                'name'        => 'LED Frame',
                'description' => 'Includes Panel + Guard + Adaptor. Bulk quantity discounts available.',
                'tag'         => null,
                'image'       => null,
                'variants'    => []
            ];
            $ledIndex = count($groupedProducts) - 1;
        }
        $sizesDecoded = json_decode($p['sizes'], true) ?: [];
        $size = !empty($sizesDecoded) ? $sizesDecoded[0] : '';
        $groupedProducts[$ledIndex]['variants'][] = [
            'id'          => $p['id'],
            'size'        => $size,
            'price'       => (float)$p['price'],
            'price_alt'   => $p['price_alt'] ? (float)$p['price_alt'] : null,
            'features'    => json_decode($p['features'], true) ?: [],
            'description' => $p['description']
        ];
    } else {
        $groupedProducts[] = [
            'is_grouped' => false,
            'product'    => $p
        ];
    }
}

require_once '../includes/photographer_header.php';
?>

<!-- ═══ MOBILE ═══ -->
<div class="md:hidden pt-14 pb-24 mobile-nav-safe">

  <!-- Added to cart toast -->
  <?php if ($message === 'success'): ?>
  <div id="toast" class="fixed top-16 left-4 right-4 z-50 bg-green-500 text-white text-sm font-bold px-4 py-3 rounded-2xl flex items-center gap-3 shadow-xl">
    <span class="text-lg">✅</span>
    <span>Added to cart!</span>
    <a href="/photographer/cart.php" class="ml-auto underline text-xs font-bold">View Cart →</a>
  </div>
  <script>setTimeout(()=>{const t=document.getElementById('toast');if(t)t.style.display='none'},3000);</script>
  <?php endif; ?>

  <!-- Category horizontal scroll -->
  <div class="px-4 pt-4 pb-3">
    <div class="flex gap-2 overflow-x-auto hide-scroll pb-1">
      <?php foreach ($categories as $key => $label): ?>
      <a href="/photographer/shop.php?cat=<?= $key ?>"
         class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap touch-btn transition-all
                <?= $cat === $key ? 'bg-primary text-secondary' : 'bg-darkcard border border-white/10 text-zinc-300' ?>">
        <?= $label ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Product count -->
  <div class="px-4 mb-3">
    <p class="text-zinc-500 text-xs"><?= count($groupedProducts) ?> product<?= count($groupedProducts) !== 1 ? 's' : '' ?></p>
  </div>

  <!-- Product Cards (mobile) -->
  <div class="px-4 space-y-4">
    <?php foreach ($groupedProducts as $gp): ?>
      <?php if ($gp['is_grouped']):
        $category = $gp['category'];
        $variants = $gp['variants'];
        if (empty($variants)) continue;
        $defaultVar = $variants[0];
        $groupId    = 'grouped_' . $category;
      ?>
      <div class="bg-darkcard border border-white/5 rounded-2xl overflow-hidden p-4" 
           data-grouped-card="<?= $groupId ?>"
           data-active-price="<?= $defaultVar['price'] ?>"
           data-active-price-alt="<?= $defaultVar['price_alt'] ? $defaultVar['price_alt'] : 'null' ?>"
           data-active-features="<?= htmlspecialchars(json_encode($defaultVar['features']), ENT_QUOTES, 'UTF-8') ?>">
        <div class="flex items-start justify-between gap-2 mb-1">
          <h3 class="text-white font-bold text-base leading-tight"><?= htmlspecialchars($gp['name']) ?></h3>
        </div>
        <p class="text-zinc-500 text-xs mb-3 leading-relaxed"><?= htmlspecialchars($gp['description']) ?></p>

        <!-- Price -->
        <div class="text-primary font-black text-xl mb-3 price-display">
          ₹<?= number_format($defaultVar['price']) ?><?= $defaultVar['price_alt'] ? ' – ₹' . number_format($defaultVar['price_alt']) : '' ?>
        </div>

        <!-- Features (collapsed, 4 max) -->
        <div class="mb-3 space-y-1 features-display">
          <?php foreach ($defaultVar['features'] as $f): ?>
          <div class="flex items-center gap-2 text-xs text-zinc-400">
            <div class="w-1 h-1 rounded-full bg-primary flex-shrink-0"></div>
            <?= htmlspecialchars($f) ?>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Add to cart form -->
        <form method="POST" class="space-y-3">
          <input type="hidden" name="product_id" value="<?= $defaultVar['id'] ?>">
          <input type="hidden" name="size" value="<?= htmlspecialchars($defaultVar['size']) ?>">

          <!-- Sizing selector pills -->
          <div class="space-y-1.5 mb-3">
            <span class="text-zinc-500 text-xs font-semibold">Select Size:</span>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($variants as $idx => $v): ?>
              <button type="button" data-size="<?= htmlspecialchars($v['size']) ?>"
                      onclick="selectVariant('<?= $groupId ?>', <?= $v['id'] ?>, '<?= htmlspecialchars($v['size']) ?>', <?= $v['price'] ?>, <?= $v['price_alt'] ? $v['price_alt'] : 'null' ?>, '<?= htmlspecialchars(json_encode($v['features']), ENT_QUOTES, 'UTF-8') ?>')"
                      class="variant-btn flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all
                             <?= $idx === 0 ? 'bg-primary text-secondary' : 'bg-zinc-800 border border-white/10 text-zinc-300' ?>">
                <?= htmlspecialchars($v['size']) ?>
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="flex gap-2">
            <div class="flex items-center bg-zinc-800 border border-white/10 rounded-xl overflow-hidden flex-shrink-0">
              <button type="button" onclick="adjustShopQty(this,-1)" class="px-3 py-3 text-zinc-400 text-lg font-bold touch-btn active:bg-white/10">−</button>
              <input type="number" name="quantity" value="1" min="1" max="999" readonly
                     class="w-12 text-center bg-transparent text-white font-bold text-sm py-3 focus:outline-none" />
              <button type="button" onclick="adjustShopQty(this,1)" class="px-3 py-3 text-zinc-400 text-lg font-bold touch-btn active:bg-white/10">+</button>
            </div>
            <button type="submit" name="add_to_cart"
                    class="flex-1 bg-primary text-secondary font-black text-sm py-3 rounded-xl touch-btn active:bg-primary-dark">
              + Add to Cart
            </button>
          </div>

          <input type="text" name="notes" placeholder="Special instructions (optional)"
                 class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-2.5 text-xs placeholder-zinc-600 focus:border-primary focus:outline-none" />
        </form>
      </div>

      <?php else:
        $p = $gp['product'];
        $sizes    = json_decode($p['sizes'], true) ?: [];
        $features = json_decode($p['features'], true) ?: [];
      ?>
      <div class="bg-darkcard border border-white/5 rounded-2xl overflow-hidden product-card-container"
           data-active-price="<?= $p['price'] ?>"
           data-active-price-alt="<?= $p['price_alt'] ? $p['price_alt'] : 'null' ?>"
           data-active-features="<?= htmlspecialchars(json_encode($features), ENT_QUOTES, 'UTF-8') ?>">
        <!-- Show thumbnail only if image is not empty (for combos) -->
        <?php if (!empty($p['image'])): ?>
        <div class="h-40 overflow-hidden">
          <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover" loading="lazy" />
        </div>
        <?php endif; ?>

        <div class="p-4">
          <!-- Tag + Name -->
          <div class="flex items-start justify-between gap-2 mb-1">
            <h3 class="text-white font-bold text-base leading-tight"><?= htmlspecialchars($p['name']) ?></h3>
            <?php if ($p['tag']): ?>
            <span class="flex-shrink-0 text-[10px] font-black px-2 py-0.5 rounded-full
                         <?= $p['tag'] === 'Premium' ? 'bg-purple-500/20 text-purple-400' : 'bg-primary/20 text-primary' ?>">
              <?= htmlspecialchars($p['tag']) ?>
            </span>
            <?php endif; ?>
          </div>

          <!-- Price -->
          <div class="text-primary font-black text-xl mb-3 price-display">
            ₹<?= number_format($p['price']) ?><?= $p['price_alt'] ? ' – ₹' . number_format($p['price_alt']) : '' ?>
            <span class="text-zinc-600 text-xs font-normal ml-1"><?= strpos($p['description'] ?? '', 'page') !== false ? '/page' : '' ?></span>
          </div>

          <!-- Features (collapsed, 3 max) -->
          <?php if ($features): ?>
          <div class="mb-3 space-y-1 text-left">
            <?php foreach (array_slice($features, 0, 3) as $f): ?>
            <div class="flex items-center gap-2 text-xs text-zinc-400">
              <div class="w-1 h-1 rounded-full bg-primary flex-shrink-0"></div>
              <?= htmlspecialchars($f) ?>
            </div>
            <?php endforeach; ?>
            <?php if (count($features) > 3): ?>
            <div class="text-xs text-zinc-600">+<?= count($features) - 3 ?> more</div>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <!-- Add to cart form -->
          <form method="POST" class="space-y-2.5">
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            
            <?php if ($p['category'] === 'album'): 
              $papers = [];
              foreach ($features as $f) {
                if (preg_match('/^(.*?)\s*–\s*₹?\s*(\d+)/u', $f, $matches)) {
                  $papers[] = ['name' => trim($matches[1]), 'rate' => (float)$matches[2]];
                }
              }
            ?>
              <!-- Paper Type Select -->
              <div class="relative">
                <select name="paper_type" required onchange="updateAlbumCardPrice(this)"
                        class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm font-semibold focus:border-primary focus:outline-none appearance-none pr-10">
                  <?php foreach ($papers as $paper): ?>
                  <option value="<?= htmlspecialchars($paper['name']) ?>" data-rate="<?= $paper['rate'] ?>">
                    <?= htmlspecialchars($paper['name']) ?> (₹<?= $paper['rate'] ?>/page)
                  </option>
                  <?php endforeach; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-400">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </div>
              </div>

              <!-- Size Select -->
              <?php if ($sizes): ?>
              <div class="relative">
                <select name="size" required
                        class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm font-semibold focus:border-primary focus:outline-none appearance-none pr-10">
                  <option value="">Select Size</option>
                  <?php foreach ($sizes as $s): ?>
                  <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-400">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </div>
              </div>
              <?php endif; ?>

              <!-- Page Count Input -->
              <div class="flex items-center gap-3 bg-zinc-800 border border-white/10 rounded-xl px-4 py-2.5">
                <span class="text-zinc-400 text-xs font-semibold whitespace-nowrap">Pages (Sides):</span>
                <input type="number" name="page_count" value="30" min="20" max="100" step="2"
                       oninput="updateAlbumCardPrice(this)" onchange="updateAlbumCardPrice(this)"
                       class="w-full bg-transparent text-white font-bold text-sm text-right focus:outline-none" />
              </div>

            <?php else: ?>
              <!-- Non-album size select -->
              <?php if ($sizes): ?>
              <div class="relative">
                <select name="size" required
                        class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-3 text-sm font-semibold focus:border-primary focus:outline-none appearance-none pr-10">
                  <option value="">Select Size</option>
                  <?php foreach ($sizes as $s): ?>
                  <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-400">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </div>
              </div>
              <?php endif; ?>
            <?php endif; ?>

            <div class="flex gap-2">
              <div class="flex items-center bg-zinc-800 border border-white/10 rounded-xl overflow-hidden flex-shrink-0">
                <button type="button" onclick="adjustShopQty(this,-1)" class="px-3 py-3 text-zinc-400 text-lg font-bold touch-btn active:bg-white/10">−</button>
                <input type="number" name="quantity" value="1" min="1" max="999" readonly
                       class="w-12 text-center bg-transparent text-white font-bold text-sm py-3 focus:outline-none" />
                <button type="button" onclick="adjustShopQty(this,1)" class="px-3 py-3 text-zinc-400 text-lg font-bold touch-btn active:bg-white/10">+</button>
              </div>
              <button type="submit" name="add_to_cart"
                      class="flex-1 bg-primary text-secondary font-black text-sm py-3 rounded-xl touch-btn active:bg-primary-dark">
                + Add to Cart
              </button>
            </div>

            <input type="text" name="notes" placeholder="Special instructions (optional)"
                   class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-4 py-2.5 text-xs placeholder-zinc-600 focus:border-primary focus:outline-none" />
          </form>
        </div>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!$groupedProducts): ?>
    <div class="py-16 text-center text-zinc-500">
      <div class="text-5xl mb-3">🔍</div>
      <p>No products in this category.</p>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ═══ DESKTOP ═══ -->
<div class="hidden md:block">
  <?php if ($message === 'success'): ?>
  <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2">
    ✅ Product added to cart! <a href="/photographer/cart.php" class="ml-2 underline">View Cart →</a>
  </div>
  <?php endif; ?>

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">Browse Products</h1>
    <a href="/photographer/cart.php" class="bg-primary text-secondary px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-primary-dark transition-all">🛒 Cart (<?= $cartCount ?>)</a>
  </div>

  <div class="flex gap-2 flex-wrap mb-6">
    <?php foreach ($categories as $key => $label): ?>
    <a href="/photographer/shop.php?cat=<?= $key ?>"
       class="px-4 py-2 rounded-full text-sm font-semibold transition-all <?= $cat === $key ? 'bg-primary text-secondary' : 'bg-darkcard border border-white/10 text-zinc-300 hover:border-primary/40' ?>">
      <?= $label ?>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="grid grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($groupedProducts as $gp): ?>
      <?php if ($gp['is_grouped']):
        $category = $gp['category'];
        $variants = $gp['variants'];
        if (empty($variants)) continue;
        $defaultVar = $variants[0];
        $groupId    = 'grouped_' . $category . '_desktop';
      ?>
      <div class="bg-darkcard border border-white/5 rounded-2xl overflow-hidden flex flex-col hover:border-primary/30 transition-all p-5" 
           data-grouped-card="<?= $groupId ?>"
           data-active-price="<?= $defaultVar['price'] ?>"
           data-active-price-alt="<?= $defaultVar['price_alt'] ? $defaultVar['price_alt'] : 'null' ?>"
           data-active-features="<?= htmlspecialchars(json_encode($defaultVar['features']), ENT_QUOTES, 'UTF-8') ?>">
        <h3 class="text-white font-bold mb-2"><?= htmlspecialchars($gp['name']) ?></h3>
        <p class="text-zinc-500 text-xs mb-3 leading-relaxed"><?= htmlspecialchars($gp['description']) ?></p>
        
        <div class="text-primary font-black text-xl mb-3 price-display">
          ₹<?= number_format($defaultVar['price']) ?><?= $defaultVar['price_alt'] ? ' – ₹' . number_format($defaultVar['price_alt']) : '' ?>
        </div>

        <ul class="text-zinc-500 text-xs space-y-1 mb-4 desktop-features-display">
          <?php foreach ($defaultVar['features'] as $f): ?>
            <li class="flex gap-2"><span class="text-primary mt-0.5">•</span><?= htmlspecialchars($f) ?></li>
          <?php endforeach; ?>
        </ul>

        <form method="POST" class="space-y-3 mt-auto">
          <input type="hidden" name="product_id" value="<?= $defaultVar['id'] ?>">
          <input type="hidden" name="size" value="<?= htmlspecialchars($defaultVar['size']) ?>">

          <div class="space-y-1.5 mb-3">
            <span class="text-zinc-500 text-xs font-semibold">Select Size:</span>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($variants as $idx => $v): ?>
              <button type="button" data-size="<?= htmlspecialchars($v['size']) ?>"
                      onclick="selectVariant('<?= $groupId ?>', <?= $v['id'] ?>, '<?= htmlspecialchars($v['size']) ?>', <?= $v['price'] ?>, <?= $v['price_alt'] ? $v['price_alt'] : 'null' ?>, '<?= htmlspecialchars(json_encode($v['features']), ENT_QUOTES, 'UTF-8') ?>')"
                      class="variant-btn flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all
                             <?= $idx === 0 ? 'bg-primary text-secondary' : 'bg-zinc-800 border border-white/10 text-zinc-300 hover:border-primary/40' ?>">
                <?= htmlspecialchars($v['size']) ?>
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="flex gap-2">
            <input type="number" name="quantity" value="1" min="1" max="999" 
                   oninput="updateCardPrice(this)" onchange="updateCardPrice(this)"
                   class="w-20 bg-zinc-800 border border-white/10 text-white rounded-xl px-3 py-2.5 text-sm text-center focus:border-primary focus:outline-none" />
            <button type="submit" name="add_to_cart" class="flex-1 bg-primary text-secondary font-bold text-sm py-2.5 rounded-xl hover:bg-primary-dark transition-all">Add to Cart</button>
          </div>
          <input type="text" name="notes" placeholder="Special instructions..." class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-3 py-2 text-xs placeholder-zinc-600 focus:border-primary focus:outline-none" />
        </form>
      </div>

      <?php else:
        $p = $gp['product'];
        $sizes    = json_decode($p['sizes'], true) ?: [];
        $features = json_decode($p['features'], true) ?: [];
      ?>
      <div class="bg-darkcard border border-white/5 rounded-2xl overflow-hidden flex flex-col hover:border-primary/30 transition-all product-card-container"
           data-active-price="<?= $p['price'] ?>"
           data-active-price-alt="<?= $p['price_alt'] ? $p['price_alt'] : 'null' ?>"
           data-active-features="<?= htmlspecialchars(json_encode($features), ENT_QUOTES, 'UTF-8') ?>">
        <!-- Show image thumbnail only if present -->
        <?php if (!empty($p['image'])): ?>
        <div class="h-44 overflow-hidden"><img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover" loading="lazy" /></div>
        <?php endif; ?>
        <div class="p-5 flex flex-col flex-grow">
          <?php if ($p['tag']): ?>
          <span class="text-[10px] font-black px-2 py-0.5 rounded-full mb-2 self-start <?= $p['tag'] === 'Premium' ? 'bg-purple-500/20 text-purple-400' : 'bg-primary/20 text-primary' ?>"><?= htmlspecialchars($p['tag']) ?></span>
          <?php endif; ?>
          <h3 class="text-white font-bold mb-2"><?= htmlspecialchars($p['name']) ?></h3>
          <div class="text-primary font-black text-xl mb-3 price-display">₹<?= number_format($p['price']) ?><?= $p['price_alt'] ? ' – ₹' . number_format($p['price_alt']) : '' ?></div>
          <?php if ($features): ?>
          <ul class="text-zinc-500 text-xs space-y-1 mb-4">
            <?php foreach (array_slice($features, 0, 4) as $f): ?><li class="flex gap-2"><span class="text-primary mt-0.5">•</span><?= htmlspecialchars($f) ?></li><?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <form method="POST" class="space-y-2 mt-auto">
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            
            <?php if ($p['category'] === 'album'): 
              $papers = [];
              foreach ($features as $f) {
                if (preg_match('/^(.*?)\s*–\s*₹?\s*(\d+)/u', $f, $matches)) {
                  $papers[] = ['name' => trim($matches[1]), 'rate' => (float)$matches[2]];
                }
              }
            ?>
              <!-- Paper Type Select -->
              <div class="relative">
                <select name="paper_type" required onchange="updateAlbumCardPrice(this)"
                        class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-3 py-2.5 pr-10 text-sm focus:border-primary focus:outline-none appearance-none">
                  <?php foreach ($papers as $paper): ?>
                  <option value="<?= htmlspecialchars($paper['name']) ?>" data-rate="<?= $paper['rate'] ?>">
                    <?= htmlspecialchars($paper['name']) ?> (₹<?= $paper['rate'] ?>/page)
                  </option>
                  <?php endforeach; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-400">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </div>
              </div>

              <!-- Size Select -->
              <?php if ($sizes): ?>
              <div class="relative">
                <select name="size" required
                        class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-3 py-2.5 pr-10 text-sm focus:border-primary focus:outline-none appearance-none">
                  <option value="">Select Size</option>
                  <?php foreach ($sizes as $s): ?>
                  <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-400">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </div>
              </div>
              <?php endif; ?>

              <!-- Page Count Input -->
              <div class="flex items-center gap-3 bg-zinc-800 border border-white/10 rounded-xl px-3 py-2">
                <span class="text-zinc-400 text-xs font-semibold whitespace-nowrap">Pages (Sides):</span>
                <input type="number" name="page_count" value="30" min="20" max="100" step="2"
                       oninput="updateAlbumCardPrice(this)" onchange="updateAlbumCardPrice(this)"
                       class="w-full bg-transparent text-white font-bold text-sm text-right focus:outline-none" />
              </div>

            <?php else: ?>
              <!-- Non-album size select -->
              <?php if ($sizes): ?>
              <div class="relative">
                <select name="size" required class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-3 py-2.5 pr-10 text-sm focus:border-primary focus:outline-none appearance-none">
                  <option value="">Select Size</option>
                  <?php foreach ($sizes as $s): ?><option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option><?php endforeach; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-400">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </div>
              </div>
              <?php endif; ?>
            <?php endif; ?>
            <div class="flex gap-2">
              <input type="number" name="quantity" value="1" min="1" max="999" 
                     oninput="updateCardPrice(this)" onchange="updateCardPrice(this)"
                     class="w-20 bg-zinc-800 border border-white/10 text-white rounded-xl px-3 py-2.5 text-sm text-center focus:border-primary focus:outline-none" />
              <button type="submit" name="add_to_cart" class="flex-1 bg-primary text-secondary font-bold text-sm py-2.5 rounded-xl hover:bg-primary-dark transition-all">Add to Cart</button>
            </div>
            <input type="text" name="notes" placeholder="Special instructions..." class="w-full bg-zinc-800 border border-white/10 text-white rounded-xl px-3 py-2 text-xs placeholder-zinc-600 focus:border-primary focus:outline-none" />
          </form>
        </div>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>

<script>
function adjustShopQty(btn, delta) {
  const input = btn.parentElement.querySelector('input[type=number]');
  const current = parseInt(input.value) || 1;
  const next = Math.max(1, Math.min(999, current + delta));
  input.value = next;
  updateCardPrice(input);
}

function selectVariant(cardId, productId, size, price, priceAlt, featuresJson) {
  const card = document.querySelector(`[data-grouped-card="${cardId}"]`);
  if (!card) return;
  
  card.setAttribute('data-active-price', price);
  card.setAttribute('data-active-price-alt', priceAlt);
  card.setAttribute('data-active-features', featuresJson);
  
  card.querySelector('input[name="product_id"]').value = productId;
  card.querySelector('input[name="size"]').value = size;
  
  const qtyInput = card.querySelector('input[name="quantity"]');
  if (qtyInput) {
    updateCardPrice(qtyInput);
  }
  
  const buttons = card.querySelectorAll('.variant-btn');
  buttons.forEach(btn => {
    if (btn.getAttribute('data-size') === size) {
      btn.className = 'variant-btn flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all bg-primary text-secondary';
    } else {
      const isDesktop = cardId.includes('desktop');
      btn.className = 'variant-btn flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all bg-zinc-800 border border-white/10 text-zinc-300' + (isDesktop ? ' hover:border-primary/40' : '');
    }
  });

  // Rebuild features display list dynamically
  let features = [];
  if (featuresJson) {
    try {
      features = JSON.parse(featuresJson);
    } catch(e) {}
  }
  
  const featuresDisplay = card.querySelector('.features-display');
  if (featuresDisplay) {
    featuresDisplay.innerHTML = '';
    features.forEach(f => {
      const div = document.createElement('div');
      div.className = 'flex items-center gap-2 text-xs text-zinc-400';
      div.innerHTML = `<div class="w-1 h-1 rounded-full bg-primary flex-shrink-0"></div>${escapeHtml(f)}`;
      featuresDisplay.appendChild(div);
    });
  }
  
  const desktopFeaturesDisplay = card.querySelector('.desktop-features-display');
  if (desktopFeaturesDisplay) {
    desktopFeaturesDisplay.innerHTML = '';
    features.forEach(f => {
      const li = document.createElement('li');
      li.className = 'flex gap-2';
      li.innerHTML = `<span class="text-primary mt-0.5">•</span>${escapeHtml(f)}`;
      desktopFeaturesDisplay.appendChild(li);
    });
  }
}

function updateCardPrice(input) {
  const card = input.closest('[data-grouped-card]') || input.closest('.product-card-container');
  if (!card) return;
  
  const qty = parseInt(input.value) || 1;
  const basePrice = parseFloat(card.getAttribute('data-active-price'));
  if (isNaN(basePrice)) return;
  
  const featuresJson = card.getAttribute('data-active-features');
  let features = [];
  if (featuresJson) {
    try {
      features = JSON.parse(featuresJson);
    } catch(e) {}
  }
  
  const paperSelect = card.querySelector('select[name="paper_type"]');
  let unitPrice;
  if (paperSelect) {
    const selectedOption = paperSelect.options[paperSelect.selectedIndex];
    const rate = selectedOption ? (parseFloat(selectedOption.getAttribute('data-rate')) || 0) : 0;
    const pageCountInput = card.querySelector('input[name="page_count"]');
    const pageCount = pageCountInput ? (parseInt(pageCountInput.value) || 30) : 30;
    unitPrice = rate * pageCount;
  } else {
    unitPrice = getDiscountedPrice(qty, basePrice, features);
  }
  
  const priceText = card.querySelector('.price-display');
  if (priceText) {
    let formatted = '₹' + Number(unitPrice).toLocaleString('en-IN');
    if (qty > 1) {
      const total = unitPrice * qty;
      formatted += ` <span class="text-zinc-500 text-xs font-normal">(${qty} × ₹${Number(unitPrice).toLocaleString('en-IN')} = ₹${Number(total).toLocaleString('en-IN')} total)</span>`;
    } else {
      if (!paperSelect) {
        const priceAlt = card.getAttribute('data-active-price-alt');
        if (priceAlt && priceAlt !== 'null') {
          formatted += ' – ₹' + Number(priceAlt).toLocaleString('en-IN');
        }
      }
    }
    priceText.innerHTML = formatted;
  }
}

function updateAlbumCardPrice(elem) {
  const card = elem.closest('.product-card-container');
  if (!card) return;
  const qtyInput = card.querySelector('input[name="quantity"]');
  if (qtyInput) {
    updateCardPrice(qtyInput);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const albumSelects = document.querySelectorAll('select[name="paper_type"]');
  albumSelects.forEach(select => {
    updateAlbumCardPrice(select);
  });
});

function getDiscountedPrice(qty, basePrice, features) {
  if (!features || !Array.isArray(features)) return basePrice;
  
  let activePrice = basePrice;
  let maxQtyThreshold = 0;
  
  features.forEach(f => {
    const match = f.match(/Qty\s+(\d+)\+:\s*₹?\s*(\d+)/i);
    if (match) {
      const threshold = parseInt(match[1]);
      const price = parseFloat(match[2]);
      if (qty >= threshold && threshold > maxQtyThreshold) {
        maxQtyThreshold = threshold;
        activePrice = price;
      }
    }
  });
  
  return activePrice;
}

function escapeHtml(text) {
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
</script>

<?php require_once '../includes/photographer_footer.php'; ?>
