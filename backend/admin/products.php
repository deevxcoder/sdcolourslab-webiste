<?php
$pageTitle = 'Manage Products – Admin';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_product'])) {
        $name = trim($_POST['name']);
        $category = $_POST['category'];
        $description = trim($_POST['description'] ?? '');
        $price = (float)$_POST['price'];
        $priceAlt = $_POST['price_alt'] ? (float)$_POST['price_alt'] : null;
        $sizes = json_encode(array_filter(array_map('trim', explode(',', $_POST['sizes'] ?? ''))));

        if ($category === 'led_frame') {
            $feats = [];
            if (!empty($_POST['qty_15_price'])) $feats[] = 'Qty 15+: ' . trim($_POST['qty_15_price']);
            if (!empty($_POST['qty_25_price'])) $feats[] = 'Qty 25+: ' . trim($_POST['qty_25_price']);
            if (!empty($_POST['qty_50_price'])) $feats[] = 'Qty 50+: ' . trim($_POST['qty_50_price']);
            $features = json_encode($feats);
        } else {
            $features = json_encode(array_filter(array_map('trim', explode(',', $_POST['features'] ?? ''))));
        }

        $tag = trim($_POST['tag'] ?? '') ?: null;
        $active = isset($_POST['active']) ? true : false;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $filename = uniqid('combo_') . '.' . $ext;
                $destDir = __DIR__ . '/../images/combos/';
                if (!is_dir($destDir)) mkdir($destDir, 0755, true);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destDir . $filename)) {
                    $imagePath = '/images/combos/' . $filename;
                }
            }
        }

        if ($_POST['product_id']) {
            if ($imagePath) {
                $stmt = $db->prepare("UPDATE products SET name=?,category=?,description=?,price=?,price_alt=?,sizes=?,features=?,tag=?,active=?,sort_order=?,image=? WHERE id=?");
                $stmt->execute([$name,$category,$description,$price,$priceAlt,$sizes,$features,$tag,$active?1:0,$sortOrder,$imagePath,(int)$_POST['product_id']]);
            } else {
                $stmt = $db->prepare("UPDATE products SET name=?,category=?,description=?,price=?,price_alt=?,sizes=?,features=?,tag=?,active=?,sort_order=? WHERE id=?");
                $stmt->execute([$name,$category,$description,$price,$priceAlt,$sizes,$features,$tag,$active?1:0,$sortOrder,(int)$_POST['product_id']]);
            }
            $message = 'Product updated.';
        } else {
            $stmt = $db->prepare("INSERT INTO products (name,category,description,price,price_alt,sizes,features,tag,active,sort_order,image) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$name,$category,$description,$price,$priceAlt,$sizes,$features,$tag,$active?1:0,$sortOrder,$imagePath]);
            $message = 'Product added.';
        }
    }
    if (isset($_POST['toggle_active'])) {
        $stmt = $db->prepare("UPDATE products SET active = NOT active WHERE id=?");
        $stmt->execute([(int)$_POST['product_id']]);
        $message = 'Product status toggled.';
    }
    if (isset($_POST['delete_product'])) {
        $stmt = $db->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([(int)$_POST['product_id']]);
        $message = 'Product deleted.';
    }
    header("Location: /admin/products.php" . ($message ? "?msg=".urlencode($message) : ""));
    exit;
}

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editProduct = null;
$qty15 = $qty25 = $qty50 = '';
if ($editId) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
    if ($editProduct && $editProduct['category'] === 'led_frame') {
        $feats = json_decode($editProduct['features'], true) ?: [];
        foreach ($feats as $f) {
            if (preg_match('/Qty\s+(\d+)\+:\s*₹?\s*(\d+)/iu', $f, $m)) {
                if ($m[1] == '15') $qty15 = $m[2];
                if ($m[1] == '25') $qty25 = $m[2];
                if ($m[1] == '50') $qty50 = $m[2];
            }
        }
    }
}
$action = isset($_GET['action']) ? $_GET['action'] : ($editProduct ? 'edit' : 'list');

$products = $db->query("SELECT * FROM products ORDER BY sort_order, id")->fetchAll();
$cats = ['combo'=>'Combo Pad','album'=>'Album','led_frame'=>'LED Frame','wall_acrylic'=>'Wall Acrylic'];
require_once '../includes/admin_header.php';
?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight">
      <?= ($action === 'add' || $editProduct) ? ($editProduct ? 'Edit Product' : 'Add Product') : 'Products Catalog' ?>
    </h1>
    <p class="text-zinc-400 text-sm mt-1">
      <?= ($action === 'add' || $editProduct) ? 'Configure pricing details, product features, sizes, and layout descriptions.' : 'Create, edit, or toggle products visible to registering and logged-in photographers.' ?>
    </p>
  </div>
  <div class="flex gap-3 items-center">
    <?php if ($action !== 'add' && !$editProduct): ?>
      <a href="/admin/products.php?action=add" class="bg-primary hover:bg-primary-dark text-secondary font-bold text-xs px-4 py-2.5 rounded-xl transition-colors shadow-lg">
        + Add Product
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

<?php if (isset($_GET['msg'])): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 text-sm font-semibold mb-6 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    <?= htmlspecialchars($_GET['msg']) ?>
  </div>
<?php endif; ?>

<?php
$type = $editProduct ? $editProduct['category'] : ($_GET['type'] ?? '');
?>
<?php if ($action === 'add' && !$editProduct && empty($type)): ?>
  <div class="mb-8 max-w-5xl">
    <h2 class="text-xl font-bold text-white mb-6">Choose Product Type</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <a href="?action=add&type=album" class="group bg-secondary/40 border border-white/5 hover:border-primary/50 hover:bg-white/[0.02] rounded-3xl p-8 flex flex-col items-center text-center transition-all duration-300 shadow-xl hover:-translate-y-1">
        <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition-transform">📸</div>
        <h3 class="text-white font-bold text-lg mb-2">Album</h3>
        <p class="text-zinc-500 text-xs leading-relaxed">Multi-page photobooks with configurable paper types and sizes.</p>
      </a>

      <a href="?action=add&type=led_frame" class="group bg-secondary/40 border border-white/5 hover:border-primary/50 hover:bg-white/[0.02] rounded-3xl p-8 flex flex-col items-center text-center transition-all duration-300 shadow-xl hover:-translate-y-1">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition-transform">🖼️</div>
        <h3 class="text-white font-bold text-lg mb-2">LED Frame</h3>
        <p class="text-zinc-500 text-xs leading-relaxed">Single size frames with built-in bulk quantity tier pricing.</p>
      </a>

      <a href="?action=add&type=wall_acrylic" class="group bg-secondary/40 border border-white/5 hover:border-primary/50 hover:bg-white/[0.02] rounded-3xl p-8 flex flex-col items-center text-center transition-all duration-300 shadow-xl hover:-translate-y-1">
        <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition-transform">💎</div>
        <h3 class="text-white font-bold text-lg mb-2">Wall Acrylic</h3>
        <p class="text-zinc-500 text-xs leading-relaxed">Premium 5mm acrylic prints for wall mounting.</p>
      </a>

      <a href="?action=add&type=combo" class="group bg-secondary/40 border border-white/5 hover:border-primary/50 hover:bg-white/[0.02] rounded-3xl p-8 flex flex-col items-center text-center transition-all duration-300 shadow-xl hover:-translate-y-1">
        <div class="w-16 h-16 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition-transform">📦</div>
        <h3 class="text-white font-bold text-lg mb-2">Combo Pad</h3>
        <p class="text-zinc-500 text-xs leading-relaxed">Bundled items with a featured combo image upload.</p>
      </a>

    </div>
  </div>

<?php elseif ($action === 'add' || $editProduct): ?>
  <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 sm:p-8 shadow-xl max-w-4xl">
    <div class="flex items-center gap-3 mb-6 pb-3 border-b border-white/5">
      <?php if (!$editProduct): ?>
        <a href="?action=add" class="text-zinc-500 hover:text-white transition-colors" title="Back to Product Types">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
        </a>
      <?php endif; ?>
      <h2 class="text-lg font-bold text-white">
        <?= $editProduct ? 'Edit ' . $cats[$type] . ': ' . htmlspecialchars($editProduct['name']) : 'Add New ' . $cats[$type] ?>
      </h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="product_id" value="<?= $editProduct ? $editProduct['id'] : '' ?>">
      <input type="hidden" name="category" value="<?= htmlspecialchars($type) ?>">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Product Name *</label>
          <input type="text" name="name" required value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>" placeholder="e.g. Premium Silk Album"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>
        
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Price (₹) *</label>
          <input type="number" step="0.01" name="price" required value="<?= $editProduct['price'] ?? '' ?>" placeholder="e.g. 2500"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>

        <?php if ($type !== 'led_frame'): ?>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Alt Price (₹) — optional</label>
          <input type="number" step="0.01" name="price_alt" value="<?= $editProduct['price_alt'] ?? '' ?>" placeholder="e.g. 3500"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>
        <?php endif; ?>

        <?php if ($type === 'combo'): ?>
        <div class="col-span-1 md:col-span-2">
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Combo Image (optional)</label>
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-primary text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all">
          <?php if (!empty($editProduct['image'])): ?>
             <div class="mt-2 text-xs text-zinc-500">Current: <a href="<?= htmlspecialchars($editProduct['image']) ?>" target="_blank" class="text-primary hover:underline">View Image</a></div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">
            <?= $type === 'led_frame' || $type === 'wall_acrylic' ? 'Size (e.g. 8x12)' : 'Sizes (comma-separated)' ?>
          </label>
          <input type="text" name="sizes" value="<?= htmlspecialchars(implode(', ', json_decode($editProduct['sizes'] ?? '[]', true))) ?>" placeholder="e.g. 12x24, 12x30"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>

        <?php if ($type !== 'led_frame'): ?>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Features (comma-separated)</label>
          <input type="text" name="features" value="<?= htmlspecialchars(implode(', ', json_decode($editProduct['features'] ?? '[]', true))) ?>" placeholder="e.g. Premium Finish, Includes Bag"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>
        <?php endif; ?>

        <?php if ($type === 'led_frame'): ?>
        <div class="col-span-1 md:col-span-2">
            <div class="bg-primary/5 border border-primary/20 rounded-xl p-4">
                <h3 class="text-sm font-bold text-primary mb-3">Bulk Quantity Pricing (Optional)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold tracking-wider text-zinc-400 mb-2">Qty 15+ Price (₹)</label>
                        <input type="number" step="0.01" name="qty_15_price" value="<?= htmlspecialchars($qty15) ?>" placeholder="e.g. 800"
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-wider text-zinc-400 mb-2">Qty 25+ Price (₹)</label>
                        <input type="number" step="0.01" name="qty_25_price" value="<?= htmlspecialchars($qty25) ?>" placeholder="e.g. 700"
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-wider text-zinc-400 mb-2">Qty 50+ Price (₹)</label>
                        <input type="number" step="0.01" name="qty_50_price" value="<?= htmlspecialchars($qty50) ?>" placeholder="e.g. 600"
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Tag (e.g. Best Seller)</label>
          <input type="text" name="tag" value="<?= htmlspecialchars($editProduct['tag'] ?? '') ?>" placeholder="e.g. Best Seller"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Sort Order</label>
          <input type="number" name="sort_order" value="<?= $editProduct['sort_order'] ?? 0 ?>"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>
      </div>
      
      <div class="mb-6">
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Description</label>
        <textarea name="description" rows="3" placeholder="Describe this product quality and options..."
                  class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm"><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
      </div>
      
      <div class="mb-6">
        <label class="inline-flex items-center gap-2.5 cursor-pointer">
          <input type="checkbox" name="active" <?= ($editProduct['active'] ?? true) ? 'checked' : '' ?> 
                 class="rounded bg-white/5 border-white/10 text-primary focus:ring-0 focus:ring-offset-0 h-4.5 w-4.5 cursor-pointer">
          <span class="text-sm font-semibold text-zinc-300 select-none">Active (visible to photographers in dashboard)</span>
        </label>
      </div>
      
      <div class="flex gap-4 flex-wrap">
        <button type="submit" name="save_product" class="bg-primary hover:bg-primary-dark text-secondary font-bold py-3.5 px-6 rounded-xl shadow-lg transition-all duration-200 text-sm cursor-pointer border-0">
          <?= $editProduct ? 'Save Changes' : 'Add ' . $cats[$type] ?>
        </button>
        <a href="<?= $editProduct ? '/admin/products.php' : '/admin/products.php?action=add' ?>" class="bg-white/5 border border-white/10 text-zinc-300 hover:bg-white/10 hover:border-white/20 font-bold py-3.5 px-6 rounded-xl transition-all duration-200 text-sm text-center">
          Cancel
        </a>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="mb-4 flex flex-col sm:flex-row gap-4 items-center bg-secondary/40 border border-white/5 rounded-2xl p-4 shadow-xl">
    <div class="flex-grow w-full relative">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-zinc-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
      </div>
      <input type="text" id="productSearch" placeholder="Search products by name..." class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-primary text-sm">
    </div>
    <div class="w-full sm:w-64">
      <select id="categoryFilter" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-primary text-sm">
        <option value="all" class="bg-secondary">All Categories</option>
        <?php foreach ($cats as $k=>$v): ?>
          <option value="<?= htmlspecialchars($v) ?>" class="bg-secondary"><?= htmlspecialchars($v) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="bg-secondary/40 border border-white/5 rounded-2xl overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left text-zinc-300">
        <thead class="text-xs uppercase bg-white/5 text-zinc-400 font-bold border-b border-white/5">
          <tr>
            <th scope="col" class="px-6 py-4">Product Name</th>
            <th scope="col" class="px-6 py-4">Category</th>
            <th scope="col" class="px-6 py-4">Price</th>
            <th scope="col" class="px-6 py-4">Tag</th>
            <th scope="col" class="px-6 py-4">Status</th>
            <th scope="col" class="px-6 py-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
            <tr class="product-row border-b border-white/5 hover:bg-white/[0.01] transition-colors <?= !$p['active'] ? 'opacity-60' : '' ?>">
              <td class="px-6 py-4 font-bold text-white product-name">
                <?= htmlspecialchars($p['name']) ?>
                <span class="text-[10px] text-zinc-500 font-normal block mt-0.5">Order weight: <?= $p['sort_order'] ?></span>
              </td>
              <td class="px-6 py-4">
                <span class="product-category inline-flex px-2.5 py-1 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                  <?= $cats[$p['category']] ?? $p['category'] ?>
                </span>
              </td>
              <td class="px-6 py-4 font-bold text-primary">
                ₹<?= number_format($p['price']) ?><?= $p['price_alt'] ? ' / ₹'.number_format($p['price_alt']) : '' ?>
              </td>
              <td class="px-6 py-4">
                <?= $p['tag'] ? '<span class="inline-flex px-2.5 py-1 rounded text-[10px] font-bold bg-primary/10 text-primary border border-primary/20">' . htmlspecialchars($p['tag']) . '</span>' : '&mdash;' ?>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold <?= $p['active'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-zinc-500/10 text-zinc-400 border border-zinc-500/20' ?>">
                  <?= $p['active'] ? 'Active' : 'Hidden' ?>
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex gap-2 justify-end flex-wrap">
                  <a href="/admin/products.php?edit=<?= $p['id'] ?>" class="text-xs bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 font-bold px-3.5 py-2 rounded-xl transition-colors">
                    Edit
                  </a>
                  <form method="POST" class="inline">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <button type="submit" name="toggle_active" class="text-xs font-bold px-3.5 py-2 rounded-xl transition-colors cursor-pointer border <?= $p['active'] ? 'bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border-emerald-500/20' ?>">
                      <?= $p['active'] ? 'Hide' : 'Show' ?>
                    </button>
                  </form>
                  <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <button type="submit" name="delete_product" class="text-xs bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 font-bold px-3.5 py-2 rounded-xl transition-colors cursor-pointer">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('productSearch');
  const catFilter = document.getElementById('categoryFilter');
  if (searchInput && catFilter) {
    function filterTable() {
      const query = searchInput.value.toLowerCase();
      const cat = catFilter.value;
      const rows = document.querySelectorAll('tbody tr.product-row');
      
      rows.forEach(row => {
        const name = row.querySelector('.product-name').textContent.toLowerCase();
        const rowCat = row.querySelector('.product-category').textContent.trim();
        
        const matchName = name.includes(query);
        const matchCat = (cat === 'all' || rowCat === cat);
        
        if (matchName && matchCat) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }
    
    searchInput.addEventListener('input', filterTable);
    catFilter.addEventListener('change', filterTable);
  }


});
</script>

<?php require_once '../includes/admin_footer.php'; ?>
