<?php
require_once __DIR__ . '/../includes/db.php';

function setCorsHeaders(): void {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

function jsonResponse(array $data, int $code = 200): void {
    setCorsHeaders();
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function success($data = null, string $message = null, int $code = 200): void {
    $res = ['success' => true];
    if ($message !== null) $res['message'] = $message;
    if ($data !== null)    $res['data']    = $data;
    jsonResponse($res, $code);
}

function error(string $message, int $code = 400): void {
    jsonResponse(['success' => false, 'message' => $message], $code);
}

function getBody(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

function generateToken(): string {
    return bin2hex(random_bytes(32));
}

function storeToken(int $userId): string {
    $db  = getDB();
    $tok = generateToken();
    $exp = date('Y-m-d H:i:s', strtotime('+30 days'));
    $db->prepare("INSERT INTO api_tokens (user_id, token, expires_at) VALUES (?,?,?)")
       ->execute([$userId, $tok, $exp]);
    return $tok;
}

function revokeToken(string $token): void {
    getDB()->prepare("DELETE FROM api_tokens WHERE token = ?")->execute([$token]);
}

function authenticate(string $requiredRole = null): array {
    $headers = getallheaders();
    $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!preg_match('/^Bearer\s+(\S+)$/', $auth, $m)) {
        error('Authentication required. Send: Authorization: Bearer <token>', 401);
    }

    $db   = getDB();
    $stmt = $db->prepare("
        SELECT u.id, u.name, u.email, u.role, u.phone, u.studio_name, u.city, u.status
        FROM users u
        JOIN api_tokens t ON t.user_id = u.id
        WHERE t.token = ? AND t.expires_at > NOW()
    ");
    $stmt->execute([$m[1]]);
    $user = $stmt->fetch();

    if (!$user) error('Invalid or expired token. Please log in again.', 401);
    if ($user['status'] === 'pending')  error('Your account is pending admin approval.', 403);
    if ($user['status'] === 'rejected' || $user['status'] === 'disabled') error('Your account has been rejected or disabled.', 403);
    if ($requiredRole && $user['role'] !== $requiredRole)
        error('Access denied: ' . $requiredRole . ' role required.', 403);

    return $user;
}

function getBearerToken(): ?string {
    $headers = getallheaders();
    $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    preg_match('/^Bearer\s+(\S+)$/', $auth, $m);
    return $m[1] ?? null;
}

function require_fields(array $body, array $fields): void {
    foreach ($fields as $f) {
        if (!isset($body[$f]) || trim((string)$body[$f]) === '') {
            error("Field '$f' is required.");
        }
    }
}

function decodeJson(?string $val): array {
    if (!$val) return [];
    $decoded = json_decode($val, true);
    return is_array($decoded) ? $decoded : [];
}

function formatProduct(array $r): array {
    return [
        'id'          => (int)$r['id'],
        'name'        => $r['name'],
        'category'    => $r['category'],
        'description' => $r['description'],
        'price'       => (float)$r['price'],
        'price_alt'   => $r['price_alt'] !== null ? (float)$r['price_alt'] : null,
        'sizes'       => decodeJson($r['sizes']),
        'features'    => decodeJson($r['features']),
        'tag'         => $r['tag'],
        'image'       => $r['image'],
    ];
}
