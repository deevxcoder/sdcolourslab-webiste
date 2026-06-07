<?php
$pageTitle = 'Create Manual Order – Admin';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();
$error = '';
$success = '';

// Fetch all approved photographers for dropdown
$photographers = $db->query("SELECT id, name, studio_name, phone FROM users WHERE role='photographer' AND status='approved' ORDER BY name ASC")->fetchAll();
$selectedPhotographerId = isset($_GET['photographer_id']) ? (int)$_GET['photographer_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $photographerId = isset($_POST['photographer_id']) && $_POST['photographer_id'] !== '' ? (int)$_POST['photographer_id'] : null;
    $studioName = trim($_POST['manual_studio_name'] ?? '');
    $phone = trim($_POST['manual_phone'] ?? '');
    $size = trim($_POST['manual_size'] ?? '');
    $orderDate = trim($_POST['order_date'] ?? date('Y-m-d H:i:s'));
    $discountPercent = (float)($_POST['discount_percent'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    $driveLink = trim($_POST['drive_link'] ?? '');

    // Form validation
    if (empty($studioName) || empty($phone)) {
        $error = 'Studio Name and Mobile Number are required.';
    } else {
        $printTypes = $_POST['print_type'] ?? [];
        $printNames = $_POST['print_name'] ?? [];
        $quantities = $_POST['qty'] ?? [];
        $rates = $_POST['rate'] ?? [];

        // Validate items
        $validItems = [];
        $grossTotal = 0;

        for ($i = 0; $i < count($printNames); $i++) {
            $pName = trim($printNames[$i] ?? '');
            if (empty($pName)) continue; // skip blank names

            $pType = trim($printTypes[$i] ?? 'Addition Product');
            $qty = (int)($quantities[$i] ?? 1);
            $rate = (float)($rates[$i] ?? 0);
            $amount = $qty * $rate;

            $validItems[] = [
                'print_type' => $pType,
                'print_name' => $pName,
                'qty' => $qty,
                'rate' => $rate,
                'amount' => $amount
            ];

            $grossTotal += $amount;
        }

        if (empty($validItems)) {
            $error = 'At least one print item is required.';
        } else {
            // Compute financial values
            $discountAmount = round($grossTotal * ($discountPercent / 100), 2);
            $netPay = round($grossTotal - $discountAmount, 2);
            $secureKey = bin2hex(random_bytes(16));

            try {
                $db->beginTransaction();

                // 1. Insert order
                $stmt = $db->prepare("INSERT INTO `orders` (
                    `photographer_id`, `status`, `total`, `discount_percent`, `discount_amount`, `net_pay`, 
                    `manual_studio_name`, `manual_phone`, `manual_size`, `notes`, `drive_link`, `secure_key`, `created_at`, `updated_at`
                ) VALUES (?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                
                $stmt->execute([
                    $photographerId,
                    $grossTotal,
                    $discountPercent,
                    $discountAmount,
                    $netPay,
                    $studioName,
                    $phone,
                    $size,
                    $notes,
                    $driveLink,
                    $secureKey,
                    $orderDate . ' ' . date('H:i:s')
                ]);

                $orderId = $db->lastInsertId();

                // 2. Insert order items
                $itemStmt = $db->prepare("INSERT INTO `order_items` (
                    `order_id`, `product_id`, `product_name`, `print_type`, `size`, `quantity`, `unit_price`, `notes`
                ) VALUES (?, NULL, ?, ?, ?, ?, ?, '')");

                foreach ($validItems as $item) {
                    $itemStmt->execute([
                        $orderId,
                        $item['print_name'],
                        $item['print_type'],
                        $size, // default to main size
                        $item['qty'],
                        $item['rate']
                    ]);
                }

                $db->commit();
                header("Location: /admin/orders.php?id=" . $orderId . "&created=1");
                exit;

            } catch (Exception $e) {
                $db->rollBack();
                $error = 'Failed to create order: ' . $e->getMessage();
            }
        }
    }
}

require_once '../includes/admin_header.php';
?>

<div class="mb-8 flex justify-between items-center">
  <div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Add Manual Order</h1>
    <p class="text-zinc-400 text-sm mt-1">Record an offline printing order and generate a WhatsApp-compatible bill.</p>
  </div>
  <div>
    <a href="/admin/orders.php" class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline font-bold">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
      </svg>
      Back to Orders
    </a>
  </div>
</div>

<?php if ($error): ?>
  <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 text-sm font-semibold mb-6 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
    </svg>
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<form method="POST" id="manual-order-form" class="space-y-6">
  <!-- General Order Info Card -->
  <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 sm:p-8 shadow-xl">
    <h2 class="text-base font-bold text-white border-b border-white/5 pb-3 mb-6">Client & Order Details</h2>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
      <!-- Searchable Photographer Dropdown -->
      <div class="relative" id="photographer-select-container">
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2 font-bold">Select Photographer</label>
        <div class="relative">
          <input type="text" id="photographer-search-input" onfocus="showPhotographerDropdown()" oninput="filterPhotographers()" placeholder="Type to search photographer..." autocomplete="off"
                 class="w-full bg-white/5 border border-white/10 rounded-xl pl-4 pr-10 py-3 text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm cursor-pointer">
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
          </div>
        </div>
        
        <input type="hidden" name="photographer_id" id="hidden-photographer-id" value="<?= $selectedPhotographerId ?: '' ?>">
        
        <div id="photographer-dropdown-menu" class="hidden absolute z-50 mt-1.5 w-full bg-[#1e1e24] border border-white/10 rounded-xl shadow-2xl max-h-60 overflow-y-auto divide-y divide-white/5 backdrop-blur-md">
          <div onclick="selectPhotographer('', 'Offline Client (No Account)', '', '')" 
               class="px-4 py-3 hover:bg-primary hover:text-secondary text-zinc-300 text-xs font-bold cursor-pointer transition-colors">
            Offline Client (No Account)
          </div>
          <?php foreach ($photographers as $ph): ?>
            <div data-id="<?= $ph['id'] ?>" 
                 data-search="<?= htmlspecialchars(strtolower($ph['name'] . ' ' . ($ph['studio_name'] ?? '') . ' ' . $ph['phone'])) ?>"
                 onclick="selectPhotographer('<?= $ph['id'] ?>', '<?= htmlspecialchars($ph['name']) ?> (<?= htmlspecialchars($ph['studio_name'] ?? 'No Studio') ?>)', '<?= htmlspecialchars($ph['studio_name'] ?? $ph['name']) ?>', '<?= htmlspecialchars($ph['phone'] ?? '') ?>')"
                 class="photographer-option px-4 py-2.5 hover:bg-primary hover:text-secondary text-zinc-300 text-xs cursor-pointer transition-colors flex justify-between items-center group">
              <div>
                <span class="font-extrabold text-white group-hover:text-secondary"><?= htmlspecialchars($ph['name']) ?></span>
                <?php if ($ph['studio_name']): ?>
                  <span class="text-[10px] text-zinc-500 block group-hover:text-secondary/70"><?= htmlspecialchars($ph['studio_name']) ?></span>
                <?php endif; ?>
              </div>
              <span class="text-[10px] text-zinc-500 group-hover:text-secondary/80 font-semibold"><?= htmlspecialchars($ph['phone'] ?? '') ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Studio Name *</label>
        <input type="text" name="manual_studio_name" required placeholder="e.g. Chulbul Munna (Bolangir)" 
               value="<?= htmlspecialchars($_POST['manual_studio_name'] ?? $_GET['studio'] ?? '') ?>"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Mobile Number *</label>
        <input type="text" name="manual_phone" required placeholder="e.g. 7008043918" 
               value="<?= htmlspecialchars($_POST['manual_phone'] ?? $_GET['phone'] ?? '') ?>"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Album Size (Header)</label>
        <input type="text" name="manual_size" placeholder="e.g. 12x36" 
               value="<?= htmlspecialchars($_POST['manual_size'] ?? $_GET['size'] ?? '') ?>"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Order Date</label>
        <input type="date" name="order_date" value="<?= date('Y-m-d') ?>" 
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
    </div>
  </div>

  <!-- Items Grid Card -->
  <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 sm:p-8 shadow-xl">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-white/5 pb-3 mb-6">
      <h2 class="text-base font-bold text-white">Line Items (Billing Slip Rows)</h2>
      
      <!-- Quick add buttons -->
      <div class="flex flex-wrap gap-1.5">
        <span class="text-xs text-zinc-500 self-center mr-1">Quick Add:</span>
        <button type="button" onclick="addPredefinedRow('Exclusive Pod')" class="text-[10px] uppercase font-extrabold px-2.5 py-1.5 bg-white/5 hover:bg-primary/20 hover:text-primary border border-white/10 rounded-lg transition-all cursor-pointer">Exclusive Pod</button>
        <button type="button" onclick="addPredefinedRow('Tharmal')" class="text-[10px] uppercase font-extrabold px-2.5 py-1.5 bg-white/5 hover:bg-primary/20 hover:text-primary border border-white/10 rounded-lg transition-all cursor-pointer">Tharmal</button>
        <button type="button" onclick="addPredefinedRow('Exclusive Pad')" class="text-[10px] uppercase font-extrabold px-2.5 py-1.5 bg-white/5 hover:bg-primary/20 hover:text-primary border border-white/10 rounded-lg transition-all cursor-pointer">Exclusive Pad</button>
        <button type="button" onclick="addPredefinedRow('Addition Product')" class="text-[10px] uppercase font-extrabold px-2.5 py-1.5 bg-white/5 hover:bg-primary/20 hover:text-primary border border-white/10 rounded-lg transition-all cursor-pointer">Addition</button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm" id="items-table">
        <thead>
          <tr class="text-xs uppercase text-zinc-400 font-bold border-b border-white/5">
            <th class="py-3 px-2 w-8">#</th>
            <th class="py-3 px-2 w-1/4">Print Type</th>
            <th class="py-3 px-2 w-1/3">Print Name</th>
            <th class="py-3 px-2 w-16 text-center">Qty</th>
            <th class="py-3 px-2 w-24">Rate</th>
            <th class="py-3 px-2 w-28 text-right">Amount</th>
            <th class="py-3 px-2 w-12 text-center"></th>
          </tr>
        </thead>
        <tbody id="items-body" class="divide-y divide-white/5">
          <!-- Dynamically populated rows -->
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-between items-center">
      <button type="button" onclick="addNewRow()" class="text-xs bg-white/5 hover:bg-white/10 border border-white/10 px-4 py-2.5 rounded-lg text-white font-bold transition-all cursor-pointer">
        ➕ Add Row
      </button>
    </div>
  </div>

  <!-- Financials & Notes Footer -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Notes & Drive Link Card -->
    <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 shadow-xl space-y-4">
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2 font-bold">Google Drive / WeTransfer Link (Optional)</label>
        <input type="url" name="drive_link" placeholder="https://drive.google.com/drive/folders/..." 
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2 font-bold">Notes / Extra Info (Will show in the bottom empty box on the invoice)</label>
        <textarea name="notes" rows="3" placeholder="e.g. Layout styles, binding directives, cover paper selection, or custom terms..." 
                  class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm resize-none"></textarea>
      </div>
    </div>

    <!-- Financial Card -->
    <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 shadow-xl space-y-4">
      <h3 class="text-xs text-zinc-500 font-semibold uppercase tracking-wider border-b border-white/5 pb-2">Financial Calculation</h3>
      
      <div class="flex justify-between items-center text-sm text-zinc-400">
        <span>Gross Subtotal:</span>
        <span class="font-extrabold text-white" id="summary-subtotal">₹0.00</span>
      </div>

      <div class="flex justify-between items-center gap-4">
        <span class="text-sm text-zinc-400">Discount Percent (%):</span>
        <div class="w-32">
          <input type="number" name="discount_percent" id="discount-percent" value="0" min="0" max="100" step="0.5" oninput="calculateTotal()"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-primary text-center text-sm font-bold">
        </div>
      </div>

      <div class="flex justify-between items-center text-sm text-zinc-400">
        <span>Discount Amount:</span>
        <span class="font-bold text-red-400" id="summary-discount-amount">-₹0.00</span>
      </div>

      <div class="border-t border-white/5 pt-4 flex justify-between items-center">
        <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Net Payable Amount:</span>
        <span class="text-2xl font-black text-primary" id="summary-net">₹0.00</span>
      </div>
    </div>
  </div>

  <div class="text-right">
    <button type="submit" name="create_order" class="bg-primary hover:bg-primary-dark text-secondary font-bold py-3.5 px-8 rounded-xl shadow-lg transition-all duration-200 text-sm cursor-pointer uppercase tracking-wider font-extrabold">
      Save Order & Generate Bill
    </button>
  </div>
</form>

<script>
const photographers = <?= json_encode($photographers) ?>;

function showPhotographerDropdown() {
    document.getElementById('photographer-dropdown-menu').classList.remove('hidden');
}

function filterPhotographers() {
    const query = document.getElementById('photographer-search-input').value.toLowerCase();
    const options = document.querySelectorAll('.photographer-option');
    options.forEach(opt => {
        const text = opt.getAttribute('data-search');
        if (text.includes(query)) {
            opt.classList.remove('hidden');
        } else {
            opt.classList.add('hidden');
        }
    });
}

function selectPhotographer(id, label, studio, phone) {
    document.getElementById('hidden-photographer-id').value = id;
    document.getElementById('photographer-search-input').value = label;
    document.getElementById('photographer-dropdown-menu').classList.add('hidden');
    
    const studioInput = document.getElementsByName('manual_studio_name')[0];
    const phoneInput = document.getElementsByName('manual_phone')[0];
    
    if (id !== '') {
        studioInput.value = studio;
        phoneInput.value = phone;
    }
}

// Close dropdown when clicking outside
window.addEventListener('click', function(e) {
    const container = document.getElementById('photographer-select-container');
    const menu = document.getElementById('photographer-dropdown-menu');
    if (container && !container.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

let rowCount = 0;

// On load, add three default rows
window.addEventListener('DOMContentLoaded', () => {
    // Set initial text for custom select
    const initialId = parseInt(document.getElementById('hidden-photographer-id').value);
    if (initialId > 0) {
        const ph = photographers.find(p => p.id === initialId);
        if (ph) {
            document.getElementById('photographer-search-input').value = `${ph.name} (${ph.studio_name ? ph.studio_name : 'No Studio'})`;
        }
    } else {
        document.getElementById('photographer-search-input').value = 'Offline Client (No Account)';
    }

    addNewRow('Exclusive Pod');
    addNewRow('Tharmal');
    addNewRow();
});

function addNewRow(type = '', name = '', qty = 1, rate = '') {
    rowCount++;
    const tbody = document.getElementById('items-body');
    const tr = document.createElement('tr');
    tr.id = `row-${rowCount}`;
    tr.className = 'hover:bg-white/[0.01] transition-colors';
    
    tr.innerHTML = `
        <td class="py-3 px-2 text-zinc-500 font-bold text-xs row-num"></td>
        <td class="py-3 px-2">
            <input type="text" name="print_type[]" value="${type}" placeholder="e.g. Exclusive Pod" 
                   class="w-full bg-white/5 border border-white/5 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-primary text-xs font-medium">
        </td>
        <td class="py-3 px-2">
            <input type="text" name="print_name[]" value="${name}" placeholder="e.g. Regular 12X36" required
                   class="w-full bg-white/5 border border-white/5 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-primary text-xs font-medium">
        </td>
        <td class="py-3 px-2">
            <input type="number" name="qty[]" value="${qty}" min="1" step="1" oninput="updateRowAmount(${rowCount})"
                   class="w-full bg-white/5 border border-white/5 rounded-lg px-2 py-2 text-white focus:outline-none focus:border-primary text-xs text-center font-semibold">
        </td>
        <td class="py-3 px-2">
            <input type="number" name="rate[]" value="${rate}" min="0" step="any" placeholder="0.00" oninput="updateRowAmount(${rowCount})"
                   class="w-full bg-white/5 border border-white/5 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-primary text-xs font-semibold">
        </td>
        <td class="py-3 px-2 text-right font-bold text-white text-xs select-none pr-4 amount-cell">
            ₹0.00
        </td>
        <td class="py-3 px-2 text-center">
            <button type="button" onclick="removeRow(${rowCount})" class="text-zinc-500 hover:text-red-400 p-1 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(tr);
    updateRowNumbers();
    updateRowAmount(rowCount);
}

function addPredefinedRow(type) {
    addNewRow(type);
}

function removeRow(id) {
    const row = document.getElementById(`row-${id}`);
    if (row) {
        row.remove();
        updateRowNumbers();
        calculateTotal();
    }
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('#items-body tr');
    rows.forEach((row, index) => {
        row.querySelector('.row-num').textContent = index + 1;
    });
}

function updateRowAmount(id) {
    const row = document.getElementById(`row-${id}`);
    if (!row) return;
    
    const qty = parseInt(row.querySelector('input[name="qty[]"]').value) || 0;
    const rate = parseFloat(row.querySelector('input[name="rate[]"]').value) || 0.00;
    const amount = qty * rate;
    
    row.querySelector('.amount-cell').textContent = `₹${amount.toFixed(2)}`;
    calculateTotal();
}

function calculateTotal() {
    const rows = document.querySelectorAll('#items-body tr');
    let subtotal = 0;
    
    rows.forEach(row => {
        const qty = parseInt(row.querySelector('input[name="qty[]"]').value) || 0;
        const rate = parseFloat(row.querySelector('input[name="rate[]"]').value) || 0.00;
        subtotal += qty * rate;
    });
    
    const discountPercent = parseFloat(document.getElementById('discount-percent').value) || 0;
    const discountAmount = subtotal * (discountPercent / 100);
    const net = subtotal - discountAmount;
    
    document.getElementById('summary-subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    document.getElementById('summary-discount-amount').textContent = `-₹${discountAmount.toFixed(2)}`;
    document.getElementById('summary-net').textContent = `₹${net.toFixed(2)}`;
}
</script>

<?php require_once '../includes/admin_footer.php'; ?>
