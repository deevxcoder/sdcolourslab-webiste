<?php
$pageTitle = 'Lab Settings – Admin';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();
$message = '';
$error = '';

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $phone = trim($_POST['phone_number'] ?? '');
    $whatsapp = trim($_POST['whatsapp_number'] ?? '');
    $address = trim($_POST['lab_address'] ?? '');
    $branchesJson = trim($_POST['branches_json'] ?? '[]');

    // Validate branches JSON
    $decodedBranches = json_decode($branchesJson, true);
    if ($decodedBranches === null) {
        $decodedBranches = [];
        $branchesJson = '[]';
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("INSERT INTO `settings` (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=?");
        
        // SQLite doesn't support ON DUPLICATE KEY UPDATE. Let's check driver and handle it.
        $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $stmtInsert = $db->prepare("INSERT OR REPLACE INTO `settings` (`key`, `value`) VALUES (?, ?)");
            $stmtInsert->execute(['phone_number', $phone]);
            $stmtInsert->execute(['whatsapp_number', $whatsapp]);
            $stmtInsert->execute(['lab_address', $address]);
            $stmtInsert->execute(['branches', $branchesJson]);
        } else {
            // MySQL
            $stmt->execute(['phone_number', $phone, $phone]);
            $stmt->execute(['whatsapp_number', $whatsapp, $whatsapp]);
            $stmt->execute(['lab_address', $address, $address]);
            $stmt->execute(['branches', $branchesJson, $branchesJson]);
        }

        $db->commit();
        $message = 'Settings updated successfully.';
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $error = 'Failed to save settings: ' . $e->getMessage();
    }
}

// Fetch current settings
$settings = [];
try {
    $rows = $db->query("SELECT * FROM `settings`")->fetchAll();
    foreach ($rows as $row) {
        $settings[$row['key']] = $row['value'];
    }
} catch (PDOException $e) {
    // Table may not exist yet
}

$phoneVal = $settings['phone_number'] ?? '8895838987, 8260754410';
$whatsappVal = $settings['whatsapp_number'] ?? '8895838987';
$addressVal = $settings['lab_address'] ?? 'Madhusudan marg, Naredi Tower Complex (In front of Raymond showroom) RKL- 769001 (ODISHA)';
$branchesVal = $settings['branches'] ?? '[]';

require_once '../includes/admin_header.php';
?>

<div class="mb-8 flex justify-between items-center">
  <div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Lab Settings</h1>
    <p class="text-zinc-400 text-sm mt-1">Configure contact details, primary office addresses, and manage branch offices dynamically.</p>
  </div>
</div>

<?php if ($message): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 text-sm font-semibold mb-6 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    <?= htmlspecialchars($message) ?>
  </div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 text-sm font-semibold mb-6 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
    </svg>
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<form method="POST" onsubmit="serializeBranches()" class="space-y-6 max-w-4xl">
  <!-- General Configuration Card -->
  <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 sm:p-8 shadow-xl space-y-6">
    <h2 class="text-base font-bold text-white border-b border-white/5 pb-3">Contact & Address Configuration</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2 font-bold">Contact Phone Numbers</label>
        <input type="text" name="phone_number" required value="<?= htmlspecialchars($phoneVal) ?>" placeholder="e.g. 8895838987, 8260754410"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2 font-bold">WhatsApp Billing Number</label>
        <input type="text" name="whatsapp_number" required value="<?= htmlspecialchars($whatsappVal) ?>" placeholder="e.g. 8895838987"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2 font-bold">Primary Lab / Head Office Address</label>
      <textarea name="lab_address" required rows="3" placeholder="Madhusudan marg, Naredi Tower..."
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm resize-none"><?= htmlspecialchars($addressVal) ?></textarea>
    </div>
  </div>

  <!-- Branches Configuration Card -->
  <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 sm:p-8 shadow-xl space-y-6">
    <div class="flex justify-between items-center border-b border-white/5 pb-3">
      <h2 class="text-base font-bold text-white">Office Branches & Corporate Locations</h2>
      <button type="button" onclick="addNewBranchRow()" class="text-xs bg-primary hover:bg-primary-dark text-secondary font-bold px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
        ➕ Add Branch
      </button>
    </div>

    <input type="hidden" name="branches_json" id="branches_json" value="<?= htmlspecialchars($branchesVal) ?>">

    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm" id="branches-table">
        <thead>
          <tr class="text-xs uppercase text-zinc-400 font-bold border-b border-white/5">
            <th class="py-3 px-2 w-8">#</th>
            <th class="py-3 px-2 w-1/3">Branch / Location Name</th>
            <th class="py-3 px-2">Location Address</th>
            <th class="py-3 px-2 w-12 text-center"></th>
          </tr>
        </thead>
        <tbody id="branches-body" class="divide-y divide-white/5">
          <!-- Dynamically populated branch rows -->
        </tbody>
      </table>
    </div>
  </div>

  <div class="text-right">
    <button type="submit" name="update_settings" class="bg-primary hover:bg-primary-dark text-secondary font-bold py-3.5 px-8 rounded-xl shadow-lg transition-all duration-200 text-sm cursor-pointer uppercase tracking-wider font-extrabold">
      Save Settings Changes
    </button>
  </div>
</form>

<script>
let branchCount = 0;

window.addEventListener('DOMContentLoaded', () => {
    // Parse current branches
    const hiddenInput = document.getElementById('branches_json');
    let branches = [];
    try {
        branches = JSON.parse(hiddenInput.value) || [];
    } catch (e) {
        branches = [];
    }
    
    if (branches.length === 0) {
        // Default placeholders
        addNewBranchRow('Corporate Office', 'Madhusudan marg, Naredi Tower Complex, RKL- 769001 (ODISHA)');
    } else {
        branches.forEach(b => {
            addNewBranchRow(b.name, b.address);
        });
    }
});

function addNewBranchRow(name = '', address = '') {
    branchCount++;
    const tbody = document.getElementById('branches-body');
    const tr = document.createElement('tr');
    tr.id = `branch-row-${branchCount}`;
    tr.className = 'branch-row-item hover:bg-white/[0.01] transition-colors';
    
    tr.innerHTML = `
        <td class="py-3 px-2 text-zinc-500 font-bold text-xs branch-num"></td>
        <td class="py-3 px-2">
            <input type="text" name="branch_name[]" value="${escapeHtml(name)}" placeholder="e.g. Sambalpur Branch" required
                   class="w-full bg-white/5 border border-white/5 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-primary text-xs font-medium">
        </td>
        <td class="py-3 px-2">
            <input type="text" name="branch_address[]" value="${escapeHtml(address)}" placeholder="e.g. Budharaja, Sambalpur - 768004" required
                   class="w-full bg-white/5 border border-white/5 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-primary text-xs font-medium">
        </td>
        <td class="py-3 px-2 text-center">
            <button type="button" onclick="removeBranchRow(${branchCount})" class="text-zinc-500 hover:text-red-400 p-1 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(tr);
    updateBranchNumbers();
}

function removeBranchRow(id) {
    const row = document.getElementById(`branch-row-${id}`);
    if (row) {
        row.remove();
        updateBranchNumbers();
    }
}

function updateBranchNumbers() {
    const rows = document.querySelectorAll('#branches-body tr');
    rows.forEach((row, index) => {
        row.querySelector('.branch-num').textContent = index + 1;
    });
}

function serializeBranches() {
    const rows = document.querySelectorAll('#branches-body tr');
    const branches = [];
    
    rows.forEach(row => {
        const nameInput = row.querySelector('input[name="branch_name[]"]');
        const addressInput = row.querySelector('input[name="branch_address[]"]');
        if (nameInput && addressInput) {
            branches.push({
                name: nameInput.value.trim(),
                address: addressInput.value.trim()
            });
        }
    });
    
    document.getElementById('branches_json').value = JSON.stringify(branches);
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

<?php require_once '../includes/admin_footer.php'; ?>
