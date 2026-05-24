<?php
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    setCorsHeaders(); http_response_code(204); exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path   = preg_replace('#^/api#', '', rtrim($uri, '/'));

// ═══════════════════════════════════════════════════════════
//  AUTH
// ═══════════════════════════════════════════════════════════

// POST /api/auth/login
if ($method === 'POST' && $path === '/auth/login') {
    $b = getBody();
    require_fields($b, ['email', 'password']);
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([strtolower(trim($b['email']))]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($b['password'], $user['password_hash'])) {
        error('Invalid email or password.', 401);
    }
    if ($user['status'] === 'pending')  error('Account pending admin approval.', 403);
    if ($user['status'] === 'rejected') error('Account has been rejected.', 403);
    $token = storeToken((int)$user['id']);
    success([
        'token'      => $token,
        'expires_in' => '30 days',
        'user' => [
            'id'          => (int)$user['id'],
            'name'        => $user['name'],
            'email'       => $user['email'],
            'role'        => $user['role'],
            'phone'       => $user['phone'],
            'studio_name' => $user['studio_name'],
            'city'        => $user['city'],
            'status'      => $user['status'],
        ],
    ], 'Login successful.');
}

// POST /api/auth/register
if ($method === 'POST' && $path === '/auth/register') {
    $b = getBody();
    require_fields($b, ['name', 'email', 'password', 'phone']);
    $db = getDB();
    $chk = $db->prepare("SELECT id FROM users WHERE email = ?");
    $chk->execute([strtolower(trim($b['email']))]);
    if ($chk->fetch()) error('An account with this email already exists.');
    $hash = password_hash($b['password'], PASSWORD_DEFAULT);
    $db->prepare("INSERT INTO users (name, email, password_hash, phone, studio_name, city, role, status) VALUES (?,?,?,?,?,?,'photographer','pending')")
       ->execute([trim($b['name']), strtolower(trim($b['email'])), $hash, trim($b['phone']), trim($b['studio_name'] ?? ''), trim($b['city'] ?? '')]);
    success(null, 'Registration successful. Your account is pending admin approval.', 201);
}

// POST /api/auth/logout
if ($method === 'POST' && $path === '/auth/logout') {
    $tok = getBearerToken();
    if ($tok) revokeToken($tok);
    success(null, 'Logged out successfully.');
}

// GET /api/auth/me
if ($method === 'GET' && $path === '/auth/me') {
    $user = authenticate();
    success([
        'id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email'],
        'role' => $user['role'], 'phone' => $user['phone'],
        'studio_name' => $user['studio_name'], 'city' => $user['city'], 'status' => $user['status'],
    ]);
}

// PATCH /api/auth/me
if ($method === 'PATCH' && $path === '/auth/me') {
    $user = authenticate();
    $b    = getBody();
    $db   = getDB();
    $fields = []; $vals = [];
    foreach (['name','phone','studio_name','city'] as $k) {
        if (isset($b[$k])) { $fields[] = "$k = ?"; $vals[] = trim($b[$k]); }
    }
    if (isset($b['password']) && strlen($b['password']) >= 6) {
        $fields[] = "password_hash = ?"; $vals[] = password_hash($b['password'], PASSWORD_DEFAULT);
    }
    if (empty($fields)) error('No updatable fields provided.');
    $vals[] = $user['id'];
    $db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?")->execute($vals);
    success(null, 'Profile updated.');
}

// GET /api/announcements
if ($method === 'GET' && $path === '/announcements') {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 10");
    success($stmt->fetchAll());
}

// ═══════════════════════════════════════════════════════════
//  PRODUCTS (public)
// ═══════════════════════════════════════════════════════════

// GET /api/products
if ($method === 'GET' && $path === '/products') {
    $db   = getDB();
    $cat  = $_GET['category'] ?? null;
    $sql  = "SELECT id,name,category,description,price,price_alt,sizes,features,tag,image FROM products WHERE active = 1";
    $args = [];
    if ($cat) { $sql .= " AND category = ?"; $args[] = $cat; }
    $sql .= " ORDER BY sort_order, name";
    $stmt = $db->prepare($sql); $stmt->execute($args);
    success(array_map('formatProduct', $stmt->fetchAll()));
}

// GET /api/products/{id}
if ($method === 'GET' && preg_match('#^/products/(\d+)$#', $path, $m)) {
    $stmt = getDB()->prepare("SELECT id,name,category,description,price,price_alt,sizes,features,tag,image FROM products WHERE id = ? AND active = 1");
    $stmt->execute([$m[1]]);
    $row  = $stmt->fetch();
    if (!$row) error('Product not found.', 404);
    success(formatProduct($row));
}

// ═══════════════════════════════════════════════════════════
//  PHOTOGRAPHER
// ═══════════════════════════════════════════════════════════

// GET /api/photographer/dashboard
if ($method === 'GET' && $path === '/photographer/dashboard') {
    $user = authenticate('photographer');
    $db   = getDB();
    $uid  = (int)$user['id'];

    $total    = $db->prepare("SELECT COUNT(*) FROM orders WHERE photographer_id=?"); $total->execute([$uid]);
    $spent    = $db->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE photographer_id=?"); $spent->execute([$uid]);
    $byStatus = $db->prepare("SELECT status, COUNT(*) FROM orders WHERE photographer_id=? GROUP BY status"); $byStatus->execute([$uid]);
    $recent   = $db->prepare("
        SELECT o.id, o.total, o.status, o.created_at, COUNT(oi.id) AS item_count
        FROM orders o LEFT JOIN order_items oi ON oi.order_id = o.id
        WHERE o.photographer_id = ?
        GROUP BY o.id, o.total, o.status, o.created_at
        ORDER BY o.created_at DESC LIMIT 5
    ");
    $recent->execute([$uid]);
    $rows = $recent->fetchAll();
    foreach ($rows as &$r) { $r['id'] = (int)$r['id']; $r['total'] = (float)$r['total']; $r['item_count'] = (int)$r['item_count']; }

    $announcement = $db->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 1")->fetch();

    success([
        'total_orders'     => (int)$total->fetchColumn(),
        'total_spent'      => (float)$spent->fetchColumn(),
        'orders_by_status' => $byStatus->fetchAll(PDO::FETCH_KEY_PAIR),
        'recent_orders'    => $rows,
        'announcement'     => $announcement
    ]);
}

// GET /api/photographer/orders
if ($method === 'GET' && $path === '/photographer/orders') {
    $user = authenticate('photographer');
    $db   = getDB();
    $stmt = $db->prepare("
        SELECT o.id, o.total, o.status, o.notes, o.created_at, COUNT(oi.id) AS item_count
        FROM orders o LEFT JOIN order_items oi ON oi.order_id = o.id
        WHERE o.photographer_id = ?
        GROUP BY o.id, o.total, o.status, o.notes, o.created_at
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([(int)$user['id']]);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) { $r['id'] = (int)$r['id']; $r['total'] = (float)$r['total']; $r['item_count'] = (int)$r['item_count']; }
    success($rows);
}

// GET /api/photographer/orders/{id}
if ($method === 'GET' && preg_match('#^/photographer/orders/(\d+)$#', $path, $m)) {
    $user = authenticate('photographer');
    $db   = getDB();
    $stmt = $db->prepare("SELECT id,total,status,notes,created_at,updated_at FROM orders WHERE id=? AND photographer_id=?");
    $stmt->execute([$m[1], (int)$user['id']]);
    $order = $stmt->fetch();
    if (!$order) error('Order not found.', 404);
    $items = $db->prepare("SELECT id,product_name,size,quantity,unit_price,notes FROM order_items WHERE order_id=?");
    $items->execute([$m[1]]);
    $irows = $items->fetchAll();
    foreach ($irows as &$ir) { $ir['id'] = (int)$ir['id']; $ir['quantity'] = (int)$ir['quantity']; $ir['unit_price'] = (float)$ir['unit_price']; $ir['subtotal'] = round((float)$ir['unit_price'] * (int)$ir['quantity'], 2); }
    $order['id'] = (int)$order['id']; $order['total'] = (float)$order['total'];
    $order['items'] = $irows;
    success($order);
}

// POST /api/photographer/orders
if ($method === 'POST' && $path === '/photographer/orders') {
    $user = authenticate('photographer');
    $b    = getBody();
    if (empty($b['items']) || !is_array($b['items'])) error("'items' array is required.");
    $db    = getDB();
    $total = 0;
    $lines = [];
    foreach ($b['items'] as $item) {
        if (empty($item['product_id']) || empty($item['quantity'])) error("Each item needs product_id and quantity.");
        $qty  = max(1, (int)$item['quantity']);
        $pstmt = $db->prepare("SELECT * FROM products WHERE id=? AND active=1"); $pstmt->execute([$item['product_id']]);
        $prod = $pstmt->fetch();
        if (!$prod) error("Product ID {$item['product_id']} not found.");
        $price = !empty($item['use_alt_price']) && $prod['price_alt'] ? (float)$prod['price_alt'] : (float)$prod['price'];
        $total += $qty * $price;
        $lines[] = ['product_id' => (int)$prod['id'], 'product_name' => $prod['name'], 'size' => trim($item['size'] ?? ''), 'quantity' => $qty, 'unit_price' => $price, 'notes' => trim($item['notes'] ?? '')];
    }
    $db->beginTransaction();
    $ins = $db->prepare("INSERT INTO orders (photographer_id, total, status, notes) VALUES (?,?,'pending',?)");
    $ins->execute([(int)$user['id'], $total, trim($b['notes'] ?? '')]);
    $orderId = (int)$db->lastInsertId();
    $iins = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, size, quantity, unit_price, notes) VALUES (?,?,?,?,?,?,?)");
    foreach ($lines as $l) $iins->execute([$orderId, $l['product_id'], $l['product_name'], $l['size'], $l['quantity'], $l['unit_price'], $l['notes']]);
    $db->commit();
    success(['order_id' => $orderId, 'total' => $total], 'Order placed successfully.', 201);
}

// ═══════════════════════════════════════════════════════════
//  ADMIN
// ═══════════════════════════════════════════════════════════

// GET /api/admin/dashboard
if ($method === 'GET' && $path === '/admin/dashboard') {
    authenticate('admin');
    $db = getDB();
    $totalOrders  = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalRev     = $db->query("SELECT COALESCE(SUM(total),0) FROM orders")->fetchColumn();
    $pendingCount = $db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
    $photoCount   = $db->query("SELECT COUNT(*) FROM users WHERE role='photographer' AND status='approved'")->fetchColumn();
    $pendingPhoto = $db->query("SELECT COUNT(*) FROM users WHERE role='photographer' AND status='pending'")->fetchColumn();
    $byStatus     = $db->query("SELECT status, COUNT(*) FROM orders GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
    $recent = $db->query("
        SELECT o.id, o.total, o.status, o.created_at, u.name AS photographer, u.studio_name
        FROM orders o JOIN users u ON u.id = o.photographer_id
        ORDER BY o.created_at DESC LIMIT 10
    ")->fetchAll();
    foreach ($recent as &$r) { $r['id'] = (int)$r['id']; $r['total'] = (float)$r['total']; }
    success([
        'total_orders'          => (int)$totalOrders,
        'total_revenue'         => (float)$totalRev,
        'pending_orders'        => (int)$pendingCount,
        'active_photographers'  => (int)$photoCount,
        'pending_photographers' => (int)$pendingPhoto,
        'orders_by_status'      => $byStatus,
        'recent_orders'         => $recent,
    ]);
}

// GET /api/admin/orders
if ($method === 'GET' && $path === '/admin/orders') {
    authenticate('admin');
    $db     = getDB();
    $status = $_GET['status'] ?? null;
    $search = $_GET['search'] ?? null;
    $sql    = "SELECT o.id, o.total, o.status, o.notes, o.admin_notes, o.created_at,
                      u.name AS photographer, u.studio_name, u.email AS photographer_email,
                      COUNT(oi.id) AS item_count
               FROM orders o
               JOIN users u ON u.id = o.photographer_id
               LEFT JOIN order_items oi ON oi.order_id = o.id";
    $args  = []; $where = [];
    if ($status) { $where[] = "o.status = ?"; $args[] = $status; }
    if ($search) { $where[] = "(u.name LIKE ? OR u.studio_name LIKE ? OR CAST(o.id AS CHAR) = ?)"; $args[] = "%$search%"; $args[] = "%$search%"; $args[] = $search; }
    if ($where) $sql .= " WHERE " . implode(' AND ', $where);
    $sql .= " GROUP BY o.id, o.total, o.status, o.notes, o.admin_notes, o.created_at, u.name, u.studio_name, u.email ORDER BY o.created_at DESC";
    $stmt = $db->prepare($sql); $stmt->execute($args);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) { $r['id'] = (int)$r['id']; $r['total'] = (float)$r['total']; $r['item_count'] = (int)$r['item_count']; }
    success($rows);
}

// GET /api/admin/orders/{id}
if ($method === 'GET' && preg_match('#^/admin/orders/(\d+)$#', $path, $m)) {
    authenticate('admin');
    $db   = getDB();
    $stmt = $db->prepare("
        SELECT o.*, u.name AS photographer, u.studio_name, u.email AS photographer_email, u.phone AS photographer_phone
        FROM orders o JOIN users u ON u.id = o.photographer_id WHERE o.id = ?
    ");
    $stmt->execute([$m[1]]);
    $order = $stmt->fetch();
    if (!$order) error('Order not found.', 404);
    $items = $db->prepare("SELECT id,product_name,size,quantity,unit_price,notes FROM order_items WHERE order_id=?");
    $items->execute([$m[1]]);
    $irows = $items->fetchAll();
    foreach ($irows as &$ir) { $ir['id'] = (int)$ir['id']; $ir['quantity'] = (int)$ir['quantity']; $ir['unit_price'] = (float)$ir['unit_price']; $ir['subtotal'] = round((float)$ir['unit_price'] * (int)$ir['quantity'], 2); }
    $order['id'] = (int)$order['id']; $order['total'] = (float)$order['total'];
    $order['items'] = $irows;
    success($order);
}

// PATCH /api/admin/orders/{id}
if ($method === 'PATCH' && preg_match('#^/admin/orders/(\d+)$#', $path, $m)) {
    authenticate('admin');
    $b = getBody(); $db = getDB();
    $valid = ['pending','processing','shipped','delivered','cancelled'];
    $fields = []; $vals = [];
    if (isset($b['status'])) {
        if (!in_array($b['status'], $valid)) error('Invalid status. Allowed: ' . implode(', ', $valid));
        $fields[] = "status = ?"; $vals[] = $b['status'];
    }
    if (isset($b['admin_notes'])) { $fields[] = "admin_notes = ?"; $vals[] = trim($b['admin_notes']); }
    if (isset($b['notes']))       { $fields[] = "notes = ?";       $vals[] = trim($b['notes']); }
    if (empty($fields)) error('Provide status, notes, or admin_notes to update.');
    $vals[] = $m[1];
    $db->prepare("UPDATE orders SET " . implode(', ', $fields) . " WHERE id = ?")->execute($vals);
    success(null, 'Order updated.');
}

// GET /api/admin/photographers
if ($method === 'GET' && $path === '/admin/photographers') {
    authenticate('admin');
    $db     = getDB();
    $status = $_GET['status'] ?? null;
    $sql    = "SELECT id,name,email,phone,studio_name,city,status,created_at FROM users WHERE role='photographer'";
    $args   = [];
    if ($status) { $sql .= " AND status=?"; $args[] = $status; }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $db->prepare($sql); $stmt->execute($args);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) $r['id'] = (int)$r['id'];
    success($rows);
}

// PATCH /api/admin/photographers/{id}
if ($method === 'PATCH' && preg_match('#^/admin/photographers/(\d+)$#', $path, $m)) {
    authenticate('admin');
    $b     = getBody();
    $valid = ['approved','rejected','pending'];
    if (empty($b['status']) || !in_array($b['status'], $valid)) error("status must be: approved, rejected, or pending.");
    getDB()->prepare("UPDATE users SET status=? WHERE id=? AND role='photographer'")->execute([$b['status'], $m[1]]);
    success(null, 'Photographer status updated to ' . $b['status'] . '.');
}

// GET /api/admin/products
if ($method === 'GET' && $path === '/admin/products') {
    authenticate('admin');
    $rows = getDB()->query("SELECT id,name,category,description,price,price_alt,sizes,features,tag,image,active FROM products ORDER BY sort_order,name")->fetchAll();
    $out = [];
    foreach ($rows as $r) { $p = formatProduct($r); $p['active'] = (bool)$r['active']; $out[] = $p; }
    success($out);
}

// POST /api/admin/products
if ($method === 'POST' && $path === '/admin/products') {
    authenticate('admin');
    $b = getBody();
    require_fields($b, ['name', 'category', 'price']);
    $db = getDB();
    $sizes    = json_encode($b['sizes']    ?? []);
    $features = json_encode($b['features'] ?? []);
    $db->prepare("INSERT INTO products (name,description,category,price,price_alt,sizes,features,tag,image,active) VALUES (?,?,?,?,?,?,?,?,?,1)")
       ->execute([trim($b['name']), trim($b['description'] ?? ''), trim($b['category']), (float)$b['price'], $b['price_alt'] ? (float)$b['price_alt'] : null, $sizes, $features, trim($b['tag'] ?? ''), trim($b['image'] ?? '')]);
    success(['product_id' => (int)$db->lastInsertId()], 'Product created.', 201);
}

// PUT /api/admin/products/{id}
if ($method === 'PUT' && preg_match('#^/admin/products/(\d+)$#', $path, $m)) {
    authenticate('admin');
    $b = getBody();
    require_fields($b, ['name', 'category', 'price']);
    $sizes    = json_encode($b['sizes']    ?? []);
    $features = json_encode($b['features'] ?? []);
    getDB()->prepare("UPDATE products SET name=?,description=?,category=?,price=?,price_alt=?,sizes=?,features=?,tag=?,image=? WHERE id=?")
           ->execute([trim($b['name']), trim($b['description'] ?? ''), trim($b['category']), (float)$b['price'], $b['price_alt'] ? (float)$b['price_alt'] : null, $sizes, $features, trim($b['tag'] ?? ''), trim($b['image'] ?? ''), $m[1]]);
    success(null, 'Product updated.');
}

// PATCH /api/admin/products/{id}/toggle
if ($method === 'PATCH' && preg_match('#^/admin/products/(\d+)/toggle$#', $path, $m)) {
    authenticate('admin');
    getDB()->prepare("UPDATE products SET active = CASE WHEN active=1 THEN 0 ELSE 1 END WHERE id=?")->execute([$m[1]]);
    success(null, 'Product visibility toggled.');
}

// POST /api/admin/broadcast
if ($method === 'POST' && $path === '/admin/broadcast') {
    authenticate('admin');
    $b = getBody();
    require_fields($b, ['title', 'message']);
    $db = getDB();
    $db->prepare("INSERT INTO announcements (title, message, type) VALUES (?,?,?)")
       ->execute([trim($b['title']), trim($b['message']), trim($b['type'] ?? 'info')]);
    success(null, 'Announcement broadcasted successfully.', 201);
}

// GET /api/admin/reports
if ($method === 'GET' && $path === '/admin/reports') {
    authenticate('admin');
    $db = getDB();
    $revenueByMonth = $db->query("
        SELECT DATE_FORMAT(created_at, '%b %Y') AS month, SUM(total) as revenue, COUNT(*) as count
        FROM orders WHERE status != 'cancelled'
        GROUP BY month ORDER BY MIN(created_at) DESC LIMIT 12
    ")->fetchAll();
    $categoryVolume = $db->query("
        SELECT category, COUNT(*) as count FROM products p
        JOIN order_items oi ON oi.product_id = p.id
        GROUP BY category
    ")->fetchAll();
    success([
        'revenue_by_month' => $revenueByMonth,
        'category_volume'  => $categoryVolume
    ]);
}

// GET /api/admin/settings
if ($method === 'GET' && $path === '/admin/settings') {
    authenticate('admin');
    $settings = getDB()->query("SELECT * FROM lab_settings")->fetchAll();
    success($settings);
}

// PUT /api/admin/settings
if ($method === 'PUT' && $path === '/admin/settings') {
    authenticate('admin');
    $b  = getBody();
    $db = getDB();
    $stmt = $db->prepare("UPDATE lab_settings SET value = ? WHERE `key` = ?");
    foreach ($b as $k => $v) {
        $stmt->execute([$v, $k]);
    }
    success(null, 'Settings updated.');
}

// DELETE /api/admin/products/{id}
if ($method === 'DELETE' && preg_match('#^/admin/products/(\d+)$#', $path, $m)) {
    authenticate('admin');
    getDB()->prepare("DELETE FROM products WHERE id=?")->execute([$m[1]]);
    success(null, 'Product deleted.');
}

// ═══════════════════════════════════════════════════════════
//  FALLBACK
// ═══════════════════════════════════════════════════════════
error('Endpoint not found: ' . strtoupper($method) . ' /api' . $path, 404);
