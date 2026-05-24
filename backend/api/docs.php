<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SD Colours API Documentation</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;background:#0f1117;color:#e2e8f0;line-height:1.6}
a{color:#60a5fa;text-decoration:none}a:hover{text-decoration:underline}
.layout{display:flex;min-height:100vh}
.sidebar{width:270px;min-width:270px;background:#1a1d27;border-right:1px solid #2d3748;position:sticky;top:0;height:100vh;overflow-y:auto;padding:1.5rem 0}
.sidebar-logo{padding:.5rem 1.5rem 1.5rem;border-bottom:1px solid #2d3748;margin-bottom:1rem}
.sidebar-logo h1{font-size:1.1rem;font-weight:800;color:#fff}
.sidebar-logo p{font-size:.75rem;color:#94a3b8;margin-top:.25rem}
.nav-section{padding:.5rem 1.5rem .25rem;font-size:.65rem;font-weight:700;text-transform:uppercase;color:#64748b;letter-spacing:.08em}
.nav-link{display:flex;align-items:center;gap:.5rem;padding:.4rem 1.5rem;font-size:.82rem;color:#94a3b8;transition:all .15s}
.nav-link:hover,.nav-link.active{background:#1e2235;color:#e2e8f0;text-decoration:none}
.badge{display:inline-block;padding:.1rem .4rem;border-radius:4px;font-size:.65rem;font-weight:700;letter-spacing:.03em}
.GET{background:#065f46;color:#6ee7b7}.POST{background:#1e3a8a;color:#93c5fd}
.PATCH{background:#713f12;color:#fde68a}.PUT{background:#5b21b6;color:#ddd6fe}
.DELETE{background:#7f1d1d;color:#fca5a5}
.main{flex:1;padding:2.5rem;max-width:900px}
.hero{background:linear-gradient(135deg,#1a1a2e,#16213e);border-radius:16px;padding:2rem;margin-bottom:2.5rem;border:1px solid #2d3748}
.hero h2{font-size:1.8rem;font-weight:800;color:#fff;margin-bottom:.5rem}
.hero p{color:#94a3b8;max-width:600px}
.tag-row{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:1rem}
.tag{background:#1e2235;border:1px solid #2d3748;border-radius:20px;padding:.25rem .75rem;font-size:.78rem;color:#94a3b8}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2.5rem}
.info-box{background:#1a1d27;border:1px solid #2d3748;border-radius:12px;padding:1.25rem}
.info-box h3{font-size:.8rem;font-weight:700;text-transform:uppercase;color:#64748b;margin-bottom:.75rem;letter-spacing:.06em}
.info-box code{display:block;background:#0f1117;border-radius:6px;padding:.75rem;font-size:.82rem;color:#a5b4fc;font-family:'Courier New',monospace;word-break:break-all}
.info-box p{font-size:.85rem;color:#94a3b8;margin-top:.5rem}
.section{margin-bottom:3rem}
.section-title{font-size:1.2rem;font-weight:800;color:#fff;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid #2d3748}
.endpoint{background:#1a1d27;border:1px solid #2d3748;border-radius:12px;margin-bottom:1rem;overflow:hidden}
.ep-head{display:flex;align-items:center;gap:.75rem;padding:1rem 1.25rem;cursor:pointer;user-select:none}
.ep-head:hover{background:#1e2235}
.ep-method{min-width:62px;text-align:center}
.ep-path{font-family:'Courier New',monospace;font-size:.88rem;color:#e2e8f0;flex:1}
.ep-desc{font-size:.8rem;color:#64748b}
.ep-lock{font-size:.85rem;color:#fbbf24}
.ep-body{display:none;padding:1.25rem;border-top:1px solid #2d3748}
.ep-body.open{display:block}
.param-table{width:100%;border-collapse:collapse;font-size:.82rem;margin-top:.5rem}
.param-table th{text-align:left;padding:.5rem .75rem;background:#0f1117;color:#64748b;font-weight:600;font-size:.75rem;text-transform:uppercase}
.param-table td{padding:.5rem .75rem;border-top:1px solid #1e2235;vertical-align:top}
.param-table td:first-child{font-family:'Courier New',monospace;color:#a5b4fc;white-space:nowrap}
.param-table td:nth-child(2){color:#6ee7b7;white-space:nowrap}
.param-table td:last-child{color:#94a3b8}
.required{color:#f87171!important}
.code-block{background:#0f1117;border-radius:8px;padding:1rem;font-family:'Courier New',monospace;font-size:.8rem;overflow-x:auto;color:#d1fae5;margin-top:.5rem;white-space:pre}
.sub-label{font-size:.75rem;font-weight:700;text-transform:uppercase;color:#64748b;margin:1rem 0 .4rem;letter-spacing:.05em}
@media(max-width:768px){.layout{flex-direction:column}.sidebar{width:100%;height:auto;position:static}.info-grid{grid-template-columns:1fr}.main{padding:1.25rem}}
</style>
</head>
<body>
<div class="layout">

<nav class="sidebar">
  <div class="sidebar-logo">
    <h1>SD Colours API</h1>
    <p>v1.0 &nbsp;·&nbsp; Base: <code style="font-size:.7rem;color:#a5b4fc">/api</code></p>
  </div>

  <div class="nav-section">Auth</div>
  <a class="nav-link" href="#auth-login"><span class="badge POST">POST</span> /auth/login</a>
  <a class="nav-link" href="#auth-register"><span class="badge POST">POST</span> /auth/register</a>
  <a class="nav-link" href="#auth-me"><span class="badge GET">GET</span> /auth/me</a>
  <a class="nav-link" href="#auth-me-patch"><span class="badge PATCH">PATCH</span> /auth/me</a>
  <a class="nav-link" href="#auth-logout"><span class="badge POST">POST</span> /auth/logout</a>

  <div class="nav-section">Products (Public)</div>
  <a class="nav-link" href="#products-list"><span class="badge GET">GET</span> /products</a>
  <a class="nav-link" href="#products-detail"><span class="badge GET">GET</span> /products/{id}</a>

  <div class="nav-section">Photographer</div>
  <a class="nav-link" href="#photo-dashboard"><span class="badge GET">GET</span> /photographer/dashboard</a>
  <a class="nav-link" href="#photo-orders"><span class="badge GET">GET</span> /photographer/orders</a>
  <a class="nav-link" href="#photo-order-detail"><span class="badge GET">GET</span> /photographer/orders/{id}</a>
  <a class="nav-link" href="#photo-order-create"><span class="badge POST">POST</span> /photographer/orders</a>

  <div class="nav-section">Admin</div>
  <a class="nav-link" href="#admin-dashboard"><span class="badge GET">GET</span> /admin/dashboard</a>
  <a class="nav-link" href="#admin-orders"><span class="badge GET">GET</span> /admin/orders</a>
  <a class="nav-link" href="#admin-order-detail"><span class="badge GET">GET</span> /admin/orders/{id}</a>
  <a class="nav-link" href="#admin-order-update"><span class="badge PATCH">PATCH</span> /admin/orders/{id}</a>
  <a class="nav-link" href="#admin-photographers"><span class="badge GET">GET</span> /admin/photographers</a>
  <a class="nav-link" href="#admin-photographer-update"><span class="badge PATCH">PATCH</span> /admin/photographers/{id}</a>
  <a class="nav-link" href="#admin-products-list"><span class="badge GET">GET</span> /admin/products</a>
  <a class="nav-link" href="#admin-products-create"><span class="badge POST">POST</span> /admin/products</a>
  <a class="nav-link" href="#admin-products-update"><span class="badge PUT">PUT</span> /admin/products/{id}</a>
  <a class="nav-link" href="#admin-products-toggle"><span class="badge PATCH">PATCH</span> /admin/products/{id}/toggle</a>
  <a class="nav-link" href="#admin-products-delete"><span class="badge DELETE">DELETE</span> /admin/products/{id}</a>
</nav>

<main class="main">

  <div class="hero">
    <h2>SD Colours Photobook Lab — REST API</h2>
    <p>JSON API for the Flutter photographer mobile app and the admin desktop application. All endpoints return <code style="color:#a5b4fc">application/json</code>.</p>
    <div class="tag-row">
      <span class="tag">Base URL: https://your-domain.replit.app/api</span>
      <span class="tag">JSON body for all POST/PATCH/PUT</span>
      <span class="tag">Bearer token auth</span>
      <span class="tag">CORS enabled</span>
    </div>
  </div>

  <div class="info-grid">
    <div class="info-box">
      <h3>Authentication</h3>
      <code>Authorization: Bearer &lt;token&gt;</code>
      <p>Obtain a token via <strong>POST /auth/login</strong>. Tokens expire in 30 days. Send in every protected request header.</p>
    </div>
    <div class="info-box">
      <h3>Response Shape</h3>
      <code>{ "success": true, "data": ..., "message": "..." }</code>
      <p>Errors return <code style="color:#f87171">"success": false</code> with an appropriate HTTP status code and a <code>message</code> string.</p>
    </div>
    <div class="info-box">
      <h3>Roles</h3>
      <code>🔓 Public &nbsp; 📸 photographer &nbsp; 🔑 admin</code>
      <p>Photographer endpoints reject admin tokens and vice-versa. Admin credentials: <strong>admin@sdcolours.com</strong>.</p>
    </div>
    <div class="info-box">
      <h3>Order Statuses</h3>
      <code>pending · processing · shipped · delivered · cancelled</code>
      <p>Only admins can update order status. Photographers can read their own order status.</p>
    </div>
  </div>

  <!-- ── AUTH ── -->
  <div class="section">
    <div class="section-title">Authentication</div>

    <div class="endpoint" id="auth-login">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge POST ep-method">POST</span>
        <span class="ep-path">/auth/login</span>
        <span class="ep-desc">Authenticate and receive a Bearer token</span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Request Body</div>
        <table class="param-table"><tr><th>Field</th><th>Type</th><th>Notes</th></tr>
          <tr><td>email</td><td class="required">string*</td><td>Registered email address</td></tr>
          <tr><td>password</td><td class="required">string*</td><td>Account password</td></tr>
        </table>
        <div class="sub-label">Example Request</div>
        <div class="code-block">POST /api/auth/login
Content-Type: application/json

{
  "email": "photographer@example.com",
  "password": "secret123"
}</div>
        <div class="sub-label">Example Response <span style="color:#6ee7b7">200</span></div>
        <div class="code-block">{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "a3f9c2d...",
    "expires_in": "30 days",
    "user": {
      "id": 5,
      "name": "Ravi Studio",
      "email": "ravi@example.com",
      "role": "photographer",
      "phone": "9876543210",
      "studio_name": "Ravi Photography",
      "city": "Rourkela",
      "status": "active"
    }
  }
}</div>
      </div>
    </div>

    <div class="endpoint" id="auth-register">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge POST ep-method">POST</span>
        <span class="ep-path">/auth/register</span>
        <span class="ep-desc">Register a new photographer account</span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Request Body</div>
        <table class="param-table"><tr><th>Field</th><th>Type</th><th>Notes</th></tr>
          <tr><td>name</td><td class="required">string*</td><td>Full name</td></tr>
          <tr><td>email</td><td class="required">string*</td><td>Unique email address</td></tr>
          <tr><td>password</td><td class="required">string*</td><td>Minimum 6 characters</td></tr>
          <tr><td>phone</td><td class="required">string*</td><td>Mobile number</td></tr>
          <tr><td>studio_name</td><td>string</td><td>Optional studio / business name</td></tr>
          <tr><td>city</td><td>string</td><td>Optional city</td></tr>
        </table>
        <div class="sub-label">Response <span style="color:#6ee7b7">201</span></div>
        <div class="code-block">{ "success": true, "message": "Registration successful. Your account is pending admin approval." }</div>
        <p style="margin-top:.75rem;font-size:.82rem;color:#fbbf24">⚠ Account is <strong>pending</strong> until an admin approves it. Login will return 403 until approved.</p>
      </div>
    </div>

    <div class="endpoint" id="auth-me">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/auth/me</span>
        <span class="ep-desc">Get current authenticated user's profile <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Headers</div>
        <div class="code-block">Authorization: Bearer &lt;token&gt;</div>
        <div class="sub-label">Response <span style="color:#6ee7b7">200</span></div>
        <div class="code-block">{ "success": true, "data": { "id": 5, "name": "...", "email": "...", "role": "photographer", ... } }</div>
      </div>
    </div>

    <div class="endpoint" id="auth-me-patch">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge PATCH ep-method">PATCH</span>
        <span class="ep-path">/auth/me</span>
        <span class="ep-desc">Update own profile or change password <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Request Body (all optional, send only fields to change)</div>
        <table class="param-table"><tr><th>Field</th><th>Type</th><th>Notes</th></tr>
          <tr><td>name</td><td>string</td><td></td></tr>
          <tr><td>phone</td><td>string</td><td></td></tr>
          <tr><td>studio_name</td><td>string</td><td></td></tr>
          <tr><td>city</td><td>string</td><td></td></tr>
          <tr><td>password</td><td>string</td><td>New password, min 6 chars</td></tr>
        </table>
      </div>
    </div>

    <div class="endpoint" id="auth-logout">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge POST ep-method">POST</span>
        <span class="ep-path">/auth/logout</span>
        <span class="ep-desc">Revoke current token <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Headers</div>
        <div class="code-block">Authorization: Bearer &lt;token&gt;</div>
        <div class="sub-label">Response <span style="color:#6ee7b7">200</span></div>
        <div class="code-block">{ "success": true, "message": "Logged out successfully." }</div>
      </div>
    </div>
  </div>

  <!-- ── PRODUCTS PUBLIC ── -->
  <div class="section">
    <div class="section-title">Products (Public — no auth required)</div>

    <div class="endpoint" id="products-list">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/products</span>
        <span class="ep-desc">List all active products</span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Query Parameters</div>
        <table class="param-table"><tr><th>Param</th><th>Type</th><th>Notes</th></tr>
          <tr><td>category</td><td>string</td><td>Filter by category, e.g. <code>Albums</code></td></tr>
        </table>
        <div class="sub-label">Response <span style="color:#6ee7b7">200</span></div>
        <div class="code-block">{
  "success": true,
  "data": [
    { "id": 1, "name": "Flush Mount Album 12x36", "description": "...", "category": "Albums",
      "price": 1200.00, "unit": "per piece", "min_qty": 1, "image": "..." },
    ...
  ]
}</div>
      </div>
    </div>

    <div class="endpoint" id="products-detail">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/products/{id}</span>
        <span class="ep-desc">Get a single product by ID</span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Path Parameter</div>
        <table class="param-table"><tr><th>Param</th><th>Type</th><th>Notes</th></tr>
          <tr><td>id</td><td class="required">integer*</td><td>Product ID</td></tr>
        </table>
      </div>
    </div>
  </div>

  <!-- ── PHOTOGRAPHER ── -->
  <div class="section">
    <div class="section-title">Photographer Endpoints 📸 <small style="font-size:.8rem;color:#64748b;font-weight:400">— require photographer token</small></div>

    <div class="endpoint" id="photo-dashboard">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/photographer/dashboard</span>
        <span class="ep-desc">Dashboard stats — orders, spend, recent activity <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Response <span style="color:#6ee7b7">200</span></div>
        <div class="code-block">{
  "success": true,
  "data": {
    "total_orders": 12,
    "total_spent": 45600.00,
    "orders_by_status": { "pending": 2, "delivered": 9, "cancelled": 1 },
    "recent_orders": [
      { "id": 45, "total": 3400.00, "status": "pending", "item_count": 3, "created_at": "..." },
      ...
    ]
  }
}</div>
      </div>
    </div>

    <div class="endpoint" id="photo-orders">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/photographer/orders</span>
        <span class="ep-desc">All orders for the logged-in photographer <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Response <span style="color:#6ee7b7">200</span></div>
        <div class="code-block">{
  "success": true,
  "data": [
    { "id": 45, "total": 3400.00, "status": "pending", "notes": "", "item_count": 3, "created_at": "2025-04-10 11:30:00" },
    ...
  ]
}</div>
      </div>
    </div>

    <div class="endpoint" id="photo-order-detail">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/photographer/orders/{id}</span>
        <span class="ep-desc">Full detail of a single order with items <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Response <span style="color:#6ee7b7">200</span></div>
        <div class="code-block">{
  "success": true,
  "data": {
    "id": 45,
    "total": 3400.00,
    "status": "processing",
    "notes": "Urgent order",
    "created_at": "...",
    "items": [
      { "id": 1, "product_name": "Flush Mount Album", "category": "Albums",
        "quantity": 2, "price": 1200.00, "subtotal": 2400.00, "unit": "per piece" },
      ...
    ]
  }
}</div>
      </div>
    </div>

    <div class="endpoint" id="photo-order-create">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge POST ep-method">POST</span>
        <span class="ep-path">/photographer/orders</span>
        <span class="ep-desc">Place a new order <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Request Body</div>
        <table class="param-table"><tr><th>Field</th><th>Type</th><th>Notes</th></tr>
          <tr><td>items</td><td class="required">array*</td><td>Array of order line objects (see below)</td></tr>
          <tr><td>items[].product_id</td><td class="required">integer*</td><td>ID of the product</td></tr>
          <tr><td>items[].quantity</td><td class="required">integer*</td><td>Must meet product min_qty</td></tr>
          <tr><td>notes</td><td>string</td><td>Optional special instructions</td></tr>
        </table>
        <div class="sub-label">Example Request</div>
        <div class="code-block">POST /api/photographer/orders
Authorization: Bearer &lt;token&gt;
Content-Type: application/json

{
  "items": [
    { "product_id": 1, "quantity": 2 },
    { "product_id": 5, "quantity": 10 }
  ],
  "notes": "Please use matte finish"
}</div>
        <div class="sub-label">Response <span style="color:#6ee7b7">201</span></div>
        <div class="code-block">{ "success": true, "message": "Order placed successfully.", "data": { "order_id": 46, "total": 5600.00 } }</div>
      </div>
    </div>
  </div>

  <!-- ── ADMIN ── -->
  <div class="section">
    <div class="section-title">Admin Endpoints 🔑 <small style="font-size:.8rem;color:#64748b;font-weight:400">— require admin token</small></div>

    <div class="endpoint" id="admin-dashboard">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/admin/dashboard</span>
        <span class="ep-desc">Lab overview stats <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Response <span style="color:#6ee7b7">200</span></div>
        <div class="code-block">{
  "success": true,
  "data": {
    "total_orders": 120,
    "total_revenue": 384000.00,
    "pending_orders": 8,
    "active_photographers": 34,
    "pending_photographers": 3,
    "orders_by_status": { "pending": 8, "processing": 12, "shipped": 5, "delivered": 92, "cancelled": 3 },
    "recent_orders": [ { "id": 46, "total": 5600.00, "status": "pending", "photographer": "Ravi Studio", ... }, ... ]
  }
}</div>
      </div>
    </div>

    <div class="endpoint" id="admin-orders">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/admin/orders</span>
        <span class="ep-desc">All orders with photographer info <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Query Parameters</div>
        <table class="param-table"><tr><th>Param</th><th>Type</th><th>Notes</th></tr>
          <tr><td>status</td><td>string</td><td>Filter: pending · processing · shipped · delivered · cancelled</td></tr>
          <tr><td>search</td><td>string</td><td>Search by photographer name, studio name, or order ID</td></tr>
        </table>
      </div>
    </div>

    <div class="endpoint" id="admin-order-detail">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/admin/orders/{id}</span>
        <span class="ep-desc">Full order detail including photographer contact <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Response includes</div>
        <p style="font-size:.82rem;color:#94a3b8">All order fields + <code>photographer</code>, <code>studio_name</code>, <code>photographer_email</code>, <code>photographer_phone</code>, and <code>items[]</code> array.</p>
      </div>
    </div>

    <div class="endpoint" id="admin-order-update">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge PATCH ep-method">PATCH</span>
        <span class="ep-path">/admin/orders/{id}</span>
        <span class="ep-desc">Update order status and/or notes <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Request Body (send one or both)</div>
        <table class="param-table"><tr><th>Field</th><th>Type</th><th>Notes</th></tr>
          <tr><td>status</td><td>string</td><td>pending · processing · shipped · delivered · cancelled</td></tr>
          <tr><td>notes</td><td>string</td><td>Internal admin notes</td></tr>
        </table>
        <div class="sub-label">Example</div>
        <div class="code-block">PATCH /api/admin/orders/46
Authorization: Bearer &lt;admin-token&gt;
Content-Type: application/json

{ "status": "processing", "notes": "Started printing — ETA 3 days" }</div>
      </div>
    </div>

    <div class="endpoint" id="admin-photographers">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/admin/photographers</span>
        <span class="ep-desc">List all photographer accounts <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Query Parameters</div>
        <table class="param-table"><tr><th>Param</th><th>Type</th><th>Notes</th></tr>
          <tr><td>status</td><td>string</td><td>Filter: active · pending · rejected</td></tr>
        </table>
        <div class="sub-label">Response data fields</div>
        <p style="font-size:.82rem;color:#94a3b8;margin-top:.5rem"><code>id, name, email, phone, studio_name, city, status, created_at</code></p>
      </div>
    </div>

    <div class="endpoint" id="admin-photographer-update">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge PATCH ep-method">PATCH</span>
        <span class="ep-path">/admin/photographers/{id}</span>
        <span class="ep-desc">Approve, reject, or reset a photographer <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Request Body</div>
        <table class="param-table"><tr><th>Field</th><th>Type</th><th>Notes</th></tr>
          <tr><td>status</td><td class="required">string*</td><td>approved · rejected · pending</td></tr>
        </table>
        <div class="sub-label">Example</div>
        <div class="code-block">{ "status": "approved" }   // approve the photographer</div>
      </div>
    </div>

    <div class="endpoint" id="admin-products-list">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge GET ep-method">GET</span>
        <span class="ep-path">/admin/products</span>
        <span class="ep-desc">All products including inactive ones <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <p style="font-size:.82rem;color:#94a3b8">Returns all products with <code>active: true/false</code>. The public <code>/products</code> endpoint only returns active products.</p>
      </div>
    </div>

    <div class="endpoint" id="admin-products-create">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge POST ep-method">POST</span>
        <span class="ep-path">/admin/products</span>
        <span class="ep-desc">Create a new product <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <div class="sub-label">Request Body</div>
        <table class="param-table"><tr><th>Field</th><th>Type</th><th>Notes</th></tr>
          <tr><td>name</td><td class="required">string*</td><td>Product name</td></tr>
          <tr><td>category</td><td class="required">string*</td><td>e.g. Albums, Frames, Prints</td></tr>
          <tr><td>price</td><td class="required">number*</td><td>Price in INR</td></tr>
          <tr><td>unit</td><td class="required">string*</td><td>e.g. per piece, per sq ft</td></tr>
          <tr><td>min_qty</td><td class="required">integer*</td><td>Minimum order quantity</td></tr>
          <tr><td>description</td><td>string</td><td>Optional description</td></tr>
          <tr><td>image</td><td>string</td><td>Optional image URL</td></tr>
        </table>
        <div class="sub-label">Response <span style="color:#6ee7b7">201</span></div>
        <div class="code-block">{ "success": true, "message": "Product created.", "data": { "product_id": 12 } }</div>
      </div>
    </div>

    <div class="endpoint" id="admin-products-update">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge PUT ep-method">PUT</span>
        <span class="ep-path">/admin/products/{id}</span>
        <span class="ep-desc">Replace all fields of a product <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <p style="font-size:.82rem;color:#94a3b8">Same fields as POST /admin/products. All required fields must be sent (full replacement).</p>
      </div>
    </div>

    <div class="endpoint" id="admin-products-toggle">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge PATCH ep-method">PATCH</span>
        <span class="ep-path">/admin/products/{id}/toggle</span>
        <span class="ep-desc">Toggle a product active/inactive <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <p style="font-size:.82rem;color:#94a3b8">No request body needed. Flips the product's <code>active</code> status. Inactive products won't appear on the photographer shop.</p>
      </div>
    </div>

    <div class="endpoint" id="admin-products-delete">
      <div class="ep-head" onclick="toggle(this)">
        <span class="badge DELETE ep-method">DELETE</span>
        <span class="ep-path">/admin/products/{id}</span>
        <span class="ep-desc">Permanently delete a product <span class="ep-lock">🔒</span></span>
      </div>
      <div class="ep-body">
        <p style="font-size:.82rem;color:#fbbf24">⚠ Irreversible. Consider using the toggle endpoint to deactivate instead of deleting.</p>
        <div class="code-block">{ "success": true, "message": "Product deleted." }</div>
      </div>
    </div>

  </div>

  <div style="text-align:center;color:#4b5563;font-size:.8rem;padding:2rem 0;border-top:1px solid #1e2235">
    SD Colours Photobook Lab API &nbsp;·&nbsp; Rourkela, India &nbsp;·&nbsp; v1.0
  </div>

</main>
</div>

<script>
function toggle(head) {
  const body = head.nextElementSibling;
  body.classList.toggle('open');
}
// Open the first endpoint by default
document.querySelector('.ep-body').classList.add('open');
</script>
</body>
</html>
