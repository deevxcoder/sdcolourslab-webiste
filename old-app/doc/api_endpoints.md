# REST API Endpoints Specification — SD Colours Photobook Lab

This document defines the complete REST API specification for the **SD Colours Photobook Lab** digital ecosystem. It serves as the master contract between the backend PHP engine and the client applications (the Flutter photographer mobile app and the Flutter desktop admin console).

---

## 1. Global API Configuration & Standards

### 1.1 Base URL
All API requests must be directed to the unified API router:
* **Local Development (Laragon/PHP Server)**: `http://localhost/sdcolorslab/api`
* **Production Cloud (Replit/Staging)**: `https://your-domain.replit.app/api`

### 1.2 Communication Protocol
* **Content Type**: All requests transmitting payloads must use the `Content-Type: application/json` header.
* **Response Envelope**: Every response returns a standardized JSON wrapper containing a `success` boolean, a user-facing `message`, and an optional `data` block.
* **CORS Policies**: Cross-Origin Resource Sharing (CORS) is enabled globally on the PHP engine to accommodate headless Next.js frontend calls.

### 1.3 Standard Response Wrapper Shapes

#### Successful Response (HTTP 200/201)
```json
{
  "success": true,
  "message": "Operation completed successfully.",
  "data": {
    "key": "value"
  }
}
```

#### Error Response (HTTP 400/401/403/404/500)
```json
{
  "success": false,
  "message": "Detailed error description goes here.",
  "data": null
}
```

### 1.4 HTTP Status Code Map
The API utilizes standard HTTP status codes to communicate request results:
* `200 OK`: Request succeeded. Returned on standard queries (`GET`), updates (`PATCH`/`PUT`), and standard operations.
* `201 Created`: Resource successfully created. Returned on successful registration or order submission (`POST`).
* `400 Bad Request`: Payload validation failed or required parameters are missing.
* `401 Unauthorized`: Authentication token is missing, expired, or invalid.
* `403 Forbidden`: Authenticated user lacks permission to access the resource (e.g., photographer attempting admin actions or a pending KYC account).
* `404 Not Found`: The requested resource (product, order, or route) does not exist.
* `500 Internal Server Error`: Backend database failure or unexpected runtime crash.

---

## 2. Authentication & Authorization Guard

Secure endpoints require token-based authentication.

### 2.1 The Bearer Token Header
To access protected endpoints, obtain a session token via `POST /auth/login` and include it in the headers of all subsequent requests:
```http
Authorization: Bearer <your_session_token_here>
```

### 2.2 Token Lifetime
Tokens are persisted in the `api_tokens` database table and have a strict lifespan of **30 days** from generation. Expired tokens yield an automatic `401 Unauthorized` response, prompting clients to re-authenticate.

### 2.3 Role Access Control (RBAC) Matrix
The API validates roles stored in the `users.role` table field to prevent unauthorized escalation:
* **Public**: Accessible without any authorization token.
* **Photographer**: Requires a token with `role = 'photographer'` AND `status = 'approved'`.
* **Admin**: Requires a token with `role = 'admin'`.

---

## 3. Auth Endpoints Group (5 Endpoints)

Handles user registration, login, profile inspection, updates, and session termination.

### 3.1 POST `/auth/login`
* **Access**: Public
* **Description**: Verifies credentials and issues a 30-day Bearer Token for subsequent authorized sessions.

#### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `email` | `string` | **Yes** | Registered user email address |
| `password` | `string` | **Yes** | Account password |

#### Example Payload
```json
{
  "email": "ravi@example.com",
  "password": "secretpassword123"
}
```

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "4a7b9e8c2f10d93a8b7c6d5e...",
    "expires_in": "30 days",
    "user": {
      "id": 5,
      "name": "Ravi Kumar",
      "email": "ravi@example.com",
      "role": "photographer",
      "phone": "9876543210",
      "studio_name": "Ravi Studio Rourkela",
      "city": "Rourkela",
      "status": "approved"
    }
  }
}
```

---

### 3.2 POST `/auth/register`
* **Access**: Public
* **Description**: Registers a new photographer partner account. Newly registered accounts start in a `'pending'` status and cannot log in until approved by an admin.

#### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | `string` | **Yes** | Full name of the photographer |
| `email` | `string` | **Yes** | Unique email address |
| `password` | `string` | **Yes** | Account password (minimum 6 characters) |
| `phone` | `string` | **Yes** | Mobile phone number |
| `studio_name` | `string` | No | Registered photography studio name |
| `city` | `string` | No | City of operation |

#### Example Payload
```json
{
  "name": "Anil Mohanty",
  "email": "anil@studio.com",
  "password": "secureanil99",
  "phone": "9437123456",
  "studio_name": "Anil Wedding Films",
  "city": "Sambalpur"
}
```

#### Successful Response (`201 Created`)
```json
{
  "success": true,
  "message": "Registration successful. Your account is pending admin approval.",
  "data": null
}
```

> [!WARNING]
> Attempting to log in via `/auth/login` right after registration will return an `HTTP 403 Forbidden` response: `{"success":false,"message":"Your account is pending admin approval. Please contact the lab."}`.

---

### 3.3 GET `/auth/me`
* **Access**: Photographer or Admin (Token Required)
* **Description**: Retrieves the profile details of the currently authenticated user.

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Profile retrieved.",
  "data": {
    "id": 5,
    "name": "Ravi Kumar",
    "email": "ravi@example.com",
    "role": "photographer",
    "phone": "9876543210",
    "studio_name": "Ravi Studio Rourkela",
    "city": "Rourkela",
    "status": "approved",
    "created_at": "2026-04-12 10:20:00"
  }
}
```

---

### 3.4 PATCH `/auth/me`
* **Access**: Photographer or Admin (Token Required)
* **Description**: Allows users to update their profile information or change their password. Send only the fields to be updated.

#### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | `string` | No | Updated full name |
| `phone` | `string` | No | Updated mobile number |
| `studio_name` | `string` | No | Updated studio name |
| `city` | `string` | No | Updated city |
| `password` | `string` | No | New password (minimum 6 characters) |

#### Example Payload
```json
{
  "studio_name": "Ravi Luxury Weddings",
  "phone": "9937098765"
}
```

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Profile updated successfully.",
  "data": {
    "id": 5,
    "name": "Ravi Kumar",
    "email": "ravi@example.com",
    "role": "photographer",
    "phone": "9937098765",
    "studio_name": "Ravi Luxury Weddings",
    "city": "Rourkela",
    "status": "approved",
    "created_at": "2026-04-12 10:20:00"
  }
}
```

---

### 3.5 POST `/auth/logout`
* **Access**: Photographer or Admin (Token Required)
* **Description**: Instantly revokes the active Bearer Token, terminating the session.

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Logged out successfully.",
  "data": null
}
```

---

## 4. Public Products Group (2 Endpoints)

Provides public access to product catalogs. Authentication is not required.

### 4.1 GET `/products`
* **Access**: Public (No Token Required)
* **Description**: Retrieves a list of all active products. Highly optimized for the Next.js marketing catalog and guest shop views.

#### Query Parameters
| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `category` | `string` | No | Filter products by category (e.g., `combo`, `album`, `led_frame`, `wall_acrylic`) |

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Products retrieved.",
  "data": [
    {
      "id": 1,
      "name": "Flush Mount Album 12x36",
      "category": "album",
      "description": "Premium layout with thick flat sheets, custom padded box.",
      "price": 1200.00,
      "price_alt": 60.00,
      "sizes": "[\"12x30\", \"12x36\"]",
      "features": "[\"Matte Velvet Board\", \"30 Sheets Standard\"]",
      "tag": "Best Seller",
      "image": "images/products/flushmount.jpg",
      "sort_order": 1
    },
    {
      "id": 2,
      "name": "Gold+ 6-in-1 Combo Pad",
      "category": "combo",
      "description": "Includes 12x36 Album, two small master prints, matching pad case, and calendar set.",
      "price": 5500.00,
      "price_alt": null,
      "sizes": "[\"Unified Size\"]",
      "features": "[\"Padded Leather Box\", \"Metallic Top Plate\"]",
      "tag": "Trending",
      "image": "images/products/gold_combo.jpg",
      "sort_order": 2
    }
  ]
}
```

---

### 4.2 GET `/products/{id}`
* **Access**: Public (No Token Required)
* **Description**: Retrieves the full specifications and dimensions of a single product.

#### Path Parameters
* `id` (integer, required): Product ID

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Product retrieved.",
  "data": {
    "id": 1,
    "name": "Flush Mount Album 12x36",
    "category": "album",
    "description": "Premium layout with thick flat sheets, custom padded box.",
    "price": 1200.00,
    "price_alt": 60.00,
    "sizes": "[\"12x30\", \"12x36\"]",
    "features": "[\"Matte Velvet Board\", \"30 Sheets Standard\"]",
    "tag": "Best Seller",
    "image": "images/products/flushmount.jpg",
    "sort_order": 1
  }
}
```

#### Error Response (`404 Not Found`)
```json
{
  "success": false,
  "message": "Product not found.",
  "data": null
}
```

---

## 5. Photographer Endpoints Group (4 Endpoints)

B2B photographer operations. Require a token with photographer role.

### 5.1 GET `/photographer/dashboard`
* **Access**: Photographer (Token Required)
* **Description**: Compiles statistical summaries, dynamic metrics, and recent order history for the photographer's mobile dashboard.

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Dashboard statistics retrieved.",
  "data": {
    "total_orders": 14,
    "total_spent": 64800.00,
    "orders_by_status": {
      "pending": 2,
      "processing": 3,
      "shipped": 1,
      "delivered": 8,
      "cancelled": 0
    },
    "recent_orders": [
      {
        "id": 46,
        "total": 5500.00,
        "status": "pending",
        "item_count": 1,
        "created_at": "2026-05-15 14:30:00"
      },
      {
        "id": 42,
        "total": 12400.00,
        "status": "delivered",
        "item_count": 4,
        "created_at": "2026-05-01 11:15:00"
      }
    ]
  }
}
```

---

### 5.2 GET `/photographer/orders`
* **Access**: Photographer (Token Required)
* **Description**: Returns all historical invoices placed by the authenticated photographer.

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Orders retrieved.",
  "data": [
    {
      "id": 46,
      "total": 5500.00,
      "status": "pending",
      "notes": "Handle with extra care - Wedding album.",
      "item_count": 1,
      "created_at": "2026-05-15 14:30:00"
    },
    {
      "id": 42,
      "total": 12400.00,
      "status": "delivered",
      "notes": "Fast track delivery requested.",
      "item_count": 4,
      "created_at": "2026-05-01 11:15:00"
    }
  ]
}
```

---

### 5.3 GET `/photographer/orders/{id}`
* **Access**: Photographer (Token Required)
* **Description**: Retrieves the complete detail of a specific order, including all individual nested item lines and internal progress notes.

#### Path Parameters
* `id` (integer, required): Order ID

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Order detail retrieved.",
  "data": {
    "id": 46,
    "total": 5500.00,
    "status": "pending",
    "notes": "Handle with extra care - Wedding album.",
    "admin_notes": null,
    "created_at": "2026-05-15 14:30:00",
    "updated_at": "2026-05-15 14:30:00",
    "items": [
      {
        "id": 89,
        "product_name": "Gold+ 6-in-1 Combo Pad",
        "size": "Unified Size",
        "quantity": 1,
        "unit_price": 5500.00,
        "subtotal": 5500.00,
        "notes": "Metallic Plate text: 'Shalini & Rajat'"
      }
    ]
  }
}
```

#### Error Response (`403 Forbidden` / `404 Not Found`)
* Yields `403` if a photographer attempts to inspect another photographer's order.
* Yields `404` if the order does not exist in the database.

---

### 5.4 POST `/photographer/orders`
* **Access**: Photographer (Token Required)
* **Description**: Submits a new order to the print queue containing multiple customized products.

#### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `items` | `array` | **Yes** | List of order lines (described below) |
| `notes` | `string` | No | General printing/packaging requests |

#### Item Specifications (`items[]`)
| Sub-field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `product_id` | `integer` | **Yes** | Target product identifier |
| `size` | `string` | **Yes** | Selected product size (e.g. `12x36`) |
| `quantity` | `integer` | **Yes** | Ordering quantity (must be >= 1) |
| `notes` | `string` | No | Item-specific requests (e.g., custom engravings) |

#### Example Payload
```json
{
  "items": [
    {
      "product_id": 1,
      "size": "12x36",
      "quantity": 1,
      "notes": "Velvet non-tearable sheet selection."
    }
  ],
  "notes": "Courier delivery requested to Sambalpur address."
}
```

#### Successful Response (`201 Created`)
```json
{
  "success": true,
  "message": "Order placed successfully.",
  "data": {
    "order_id": 47,
    "total": 1200.00
  }
}
```

---

## 6. Admin Endpoints Group (11 Endpoints)

High-privilege office administration. Require a Bearer Token with admin role.

### 6.1 GET `/admin/dashboard`
* **Access**: Admin (Token Required)
* **Description**: Compiles overall business intelligence, total print queue metrics, active customer distributions, and recent orders for the desktop console dashboard.

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Admin dashboard metrics compiled.",
  "data": {
    "total_orders": 184,
    "total_revenue": 589400.00,
    "pending_orders": 6,
    "active_photographers": 42,
    "pending_photographers": 3,
    "orders_by_status": {
      "pending": 6,
      "processing": 12,
      "shipped": 4,
      "delivered": 158,
      "cancelled": 4
    },
    "recent_orders": [
      {
        "id": 46,
        "total": 5500.00,
        "status": "pending",
        "photographer": "Ravi Kumar",
        "studio_name": "Ravi Studio Rourkela",
        "created_at": "2026-05-15 14:30:00"
      }
    ]
  }
}
```

---

### 6.2 GET `/admin/orders`
* **Access**: Admin (Token Required)
* **Description**: Returns all orders placed by all photographers, complete with filter utilities.

#### Query Parameters
| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `status` | `string` | No | Filter by state: `pending`, `processing`, `shipped`, `delivered`, `cancelled` |
| `search` | `string` | No | Fuzzy search by photographer name, studio name, or Order ID |

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "All orders retrieved.",
  "data": [
    {
      "id": 46,
      "total": 5500.00,
      "status": "pending",
      "notes": "Handle with extra care - Wedding album.",
      "photographer_name": "Ravi Kumar",
      "studio_name": "Ravi Studio Rourkela",
      "created_at": "2026-05-15 14:30:00"
    }
  ]
}
```

---

### 6.3 GET `/admin/orders/{id}`
* **Access**: Admin (Token Required)
* **Description**: Retrieves full order metrics including internal admin logs and full photographer contact info (phone/email) for shipment booking.

#### Path Parameters
* `id` (integer, required): Order ID

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Admin order details retrieved.",
  "data": {
    "id": 46,
    "total": 5500.00,
    "status": "pending",
    "notes": "Handle with extra care - Wedding album.",
    "admin_notes": "Awaiting photographer payment verification.",
    "created_at": "2026-05-15 14:30:00",
    "updated_at": "2026-05-15 14:30:00",
    "photographer": {
      "id": 5,
      "name": "Ravi Kumar",
      "email": "ravi@example.com",
      "phone": "9876543210",
      "studio_name": "Ravi Studio Rourkela",
      "city": "Rourkela"
    },
    "items": [
      {
        "id": 89,
        "product_name": "Gold+ 6-in-1 Combo Pad",
        "size": "Unified Size",
        "quantity": 1,
        "unit_price": 5500.00,
        "subtotal": 5500.00,
        "notes": "Metallic Plate text: 'Shalini & Rajat'"
      }
    ]
  }
}
```

---

### 6.4 PATCH `/admin/orders/{id}`
* **Access**: Admin (Token Required)
* **Description**: Updates the production status and registers internal operational annotations.

#### Path Parameters
* `id` (integer, required): Order ID

#### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `status` | `string` | No | New state: `pending`, `processing`, `shipped`, `delivered`, `cancelled` |
| `admin_notes` | `string` | No | Internal production remarks |

#### Example Payload
```json
{
  "status": "processing",
  "admin_notes": "Printed on Matte Velvet. Binding in progress."
}
```

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Order status updated successfully.",
  "data": {
    "order_id": 46,
    "new_status": "processing"
  }
}
```

---

### 6.5 GET `/admin/photographers`
* **Access**: Admin (Token Required)
* **Description**: Retrieves registered photographers to facilitate KYC/verification processes.

#### Query Parameters
| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `status` | `string` | No | Filter by verification step: `pending`, `approved`, `rejected` |

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Photographers list retrieved.",
  "data": [
    {
      "id": 6,
      "name": "Anil Mohanty",
      "email": "anil@studio.com",
      "phone": "9437123456",
      "studio_name": "Anil Wedding Films",
      "city": "Sambalpur",
      "status": "pending",
      "created_at": "2026-05-16 09:12:00"
    }
  ]
}
```

---

### 6.6 PATCH `/admin/photographers/{id}`
* **Access**: Admin (Token Required)
* **Description**: Updates verification status (Approving or Rejecting a registered partner).

#### Path Parameters
* `id` (integer, required): Photographer (User) ID

#### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `status` | `string` | **Yes** | Approved status transition: `approved` or `rejected` |

#### Example Payload
```json
{
  "status": "approved"
}
```

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Photographer status updated successfully.",
  "data": {
    "photographer_id": 6,
    "new_status": "approved"
  }
}
```

---

### 6.7 GET `/admin/products`
* **Access**: Admin (Token Required)
* **Description**: Lists all products in the database, including deactivated/inactive entries (which are excluded from public lists).

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "All products retrieved.",
  "data": [
    {
      "id": 1,
      "name": "Flush Mount Album 12x36",
      "category": "album",
      "price": 1200.00,
      "active": true
    },
    {
      "id": 3,
      "name": "Legacy Crystal Pad (Discontinued)",
      "category": "combo",
      "price": 4200.00,
      "active": false
    }
  ]
}
```

---

### 6.8 POST `/admin/products`
* **Access**: Admin (Token Required)
* **Description**: Creates a new product catalog listing.

#### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | `string` | **Yes** | Product name |
| `category` | `string` | **Yes** | Product category group |
| `description` | `string` | No | Comprehensive description |
| `price` | `number` | **Yes** | Primary unit cost in INR |
| `price_alt` | `number` | No | Optional secondary cost (e.g. per-page rate) |
| `sizes` | `array` | No | Standard sizes list (e.g., `["12x30", "12x36"]`) |
| `features` | `array` | No | Product bullet features |
| `tag` | `string` | No | Accent tag (e.g., `New Arrival`) |
| `image` | `string` | No | Relative location asset path |
| `sort_order` | `integer` | No | Numeric sorting weight |

#### Example Payload
```json
{
  "name": "LED Backlit Frame 24x36",
  "category": "led_frame",
  "description": "Ultra-bright backlit LED frame with touch sensitivity.",
  "price": 3800.00,
  "price_alt": null,
  "sizes": ["12x18", "24x36"],
  "features": ["3D Glow Guard", "Wall Mount Bracket Included"],
  "tag": "New",
  "image": "images/products/led_backlit.jpg",
  "sort_order": 5
}
```

#### Successful Response (`201 Created`)
```json
{
  "success": true,
  "message": "Product created successfully.",
  "data": {
    "product_id": 15
  }
}
```

---

### 6.9 PUT `/admin/products/{id}`
* **Access**: Admin (Token Required)
* **Description**: Replaces all fields of an existing product (Full HTTP Replacement contract).

#### Path Parameters
* `id` (integer, required): Product ID

#### Request Body
Requires all base parameters specified in `POST /admin/products` to implement full replacement.

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Product updated successfully.",
  "data": {
    "product_id": 15
  }
}
```

---

### 6.10 PATCH `/admin/products/{id}/toggle`
* **Access**: Admin (Token Required)
* **Description**: Toggles the active store visibility toggle (`products.active`) without deleting the database record.

#### Path Parameters
* `id` (integer, required): Product ID

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Product active status toggled.",
  "data": {
    "product_id": 15,
    "active": false
  }
}
```

---

### 6.11 DELETE `/admin/products/{id}`
* **Access**: Admin (Token Required)
* **Description**: Permanently drops a product record from the Relational Database.

#### Path Parameters
* `id` (integer, required): Product ID

> [!CAUTION]
> If a product is already linked to past invoices in the `order_items` table, the backend will return a relational error block. In such scenarios, use `PATCH /admin/products/{id}/toggle` to hide the product from client apps instead of a hard deletion.

#### Successful Response (`200 OK`)
```json
{
  "success": true,
  "message": "Product deleted successfully.",
  "data": null
}
```
