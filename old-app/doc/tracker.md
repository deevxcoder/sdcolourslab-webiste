# Feature Tracker & Strategic Future Roadmap

This document serves as the master checklist of active capabilities and the high-level roadmap for the **SD Colours Photobook Lab** digital business ecosystem.

---

## 1. Unified Feature Matrix

The following grid tracks active features (`[x]`) and planned roadmap capabilities (`[ ]`) across the four core modules:

### 1.1 Next.js 16 Marketing Website
| Feature | Status | Target Phase | Notes |
| :--- | :---: | :---: | :--- |
| Responsive Layout & Navigation | `[x]` | Core | App Router with Vanilla CSS |
| Premium Craftsmanship Showcase | `[x]` | Core | Gold, Platinum, Wooden series visuals |
| Combo Pad Catalog Page | `[x]` | Core | Filterable grid showing list items |
| Interactive Cost Calculator | `[x]` | Core | Velvet, Silky, NTR, Lustre tables |
| Contact Form & Lead Routing | `[x]` | Core | Lead submission & direct mail broker |
| 1-Click WhatsApp Support Link | `[x]` | Core | Automated message template forwarding |
| Server-Side SEO Meta Headers | `[x]` | Core | Premium ranking & dynamic descriptions |
| Photographer Registration Portal | `[ ]` | Phase 2 | Redirect web signup directly to PHP API |

---

### 1.2 Central PHP Backend & APIs
| Feature | Status | Target Phase | Notes |
| :--- | :---: | :---: | :--- |
| Router-Based API Routing | `[x]` | Core | Uniform `/api/*` resolution logic |
| Standardized JSON Response Shapes | `[x]` | Core | Consistent `{ success, data, message }` envelope |
| Secure Password Bcrypt Hashing | `[x]` | Core | Implemented during user registration |
| Bearer Token Authentication Guards | `[x]` | Core | Stateful `api_tokens` (30-day lifespan) |
| Relational DB Integrations (PDO) | `[x]` | Core | Seamless MySQL and PostgreSQL support |
| Public Docs Dashboard (`docs.php`) | `[x]` | Core | Interactive, self-hosted API docs page |
| Order Queue Status Pipeline | `[x]` | Core | Handlers for status transitions |
| Live Push Broadcast Engine | `[ ]` | Phase 2 | Firebase Cloud Messaging integration |
| Auto-Export Reports (Excel/PDF) | `[ ]` | Phase 3 | Automated financial CSV & PDF generators |

---

### 1.3 Photographer Mobile App (`photographer_mobile_app`)
| Feature | Status | Target Phase | Notes |
| :--- | :---: | :---: | :--- |
| Secured Splash/Login Onboarding | `[x]` | Core | Fast local storage check for token cache |
| Photographer registration & verification | `[x]` | Core | Leads to "Account Pending Approval" block |
| Interactive Shop Catalog Dashboard | `[x]` | Core | Tabs, visual grids, sorting search |
| Dynamic Sheet Rate Calculator | `[x]` | Core | Computes price dynamically in app memory |
| Local E-Commerce Cart Manager | `[x]` | Core | Multi-variant persistent cart storage |
| Order Checkout & Spec Upload | `[x]` | Core | Integrates API endpoints with text notes |
| Live Progress Milestones Timeline | `[x]` | Core | Real-time status update checking |
| FCM Push Notification Listener | `[ ]` | Phase 2 | Background status changes and alerts |
| Offline-First Cache Layer (SQLite) | `[ ]` | Phase 3 | Synced offline data fallback via local DB |

---

### 1.4 Lab Windows Desktop App (`lab_desktop_app`)
| Feature | Status | Target Phase | Notes |
| :--- | :---: | :---: | :--- |
| Administrative Auth login panel | `[x]` | Core | Restricts entry to verified admin emails |
| Operator Dashboard Metrics Grid | `[x]` | Core | Visual stats cards & graphs |
| User KYC Account Review Console | `[x]` | Core | Direct 1-click photographer approval |
| Advanced Live Order Search Filter | `[x]` | Core | Search by ID, studio, or state |
| Product Catalog Editor (CRUD) | `[x]` | Core | Complete product management panel |
| Active Product Visibility Switch | `[x]` | Core | Live database soft toggle |
| Global push broadcast cockpit | `[ ]` | Phase 2 | Allows direct alerts creation |
| Automated Photoshop Layout Engine | `[ ]` | Phase 3 | JSX-based print sheet compilation |

---

## 2. Long-Term Strategic Roadmap

To advance the digital infrastructure of SD Colours Photobook Lab beyond core operations, we have structured development into three successive growth phases:

```
┌────────────────────────────────────────────────────────────────────────┐
│  PHASE 1: Infrastructure Consolidation & API Hardening                  │
│  - Secure database credential rotation (env configuration)            │
│  - Secure Cloud SSL/TLS routing setup                                  │
│  - Enterprise database backup pipelines                                │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│  PHASE 2: Mobile Engagement & Active Notifications                     │
│  - Firebase Cloud Messaging integration for status updates            │
│  - Instant automated push triggers on state changes                    │
│  - Direct web photographer signup portal on Next.js marketing site      │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│  PHASE 3: AI-Assisted Design & Industrial Print Orchestration          │
│  - Adobe Photoshop JSX Scripting Engine (Automated Album Generation)   │
│  - Offline-first photographer cart state syncing (local SQLite DB)     │
│  - Excel/PDF analytical exporting suite for administrative operators   │
└────────────────────────────────────────────────────────────────────────┘
```

### 2.1 Phase 1: Infrastructure Consolidation & API Hardening
* **Production Database Isolation**: Move from local development SQLite/MySQL to high-availability PostgreSQL on standard cloud hosting platforms. Initialize database migrations and clean data seed scripts.
* **API Hardening**: Introduce strict rate limit layers on the `/auth/login` and `/auth/register` PHP routes using client IP logging.
* **Environment Variables Migration**: Ensure all database connection tokens, secrets, and system configs are read strictly from secure server environment configurations (`.env`) rather than being hardcoded in code blocks.
* **Enterprise Logging Suite**: Implement a structured file logger inside `sdcolorslab/includes/logger.php` to track failed admin actions, database timeouts, and system exceptions securely.

### 2.2 Phase 2: Mobile Engagement & Active Notifications
* **Firebase Cloud Messaging Integration**: Setup Firebase inside both `photographer_mobile_app` and `lab_desktop_app`. Register target devices to active account rows in the user database.
* **State Change Automated Push**: Automate the central PHP backend to trigger a silent push notification payload to the photographer's device whenever an admin updates the order status (e.g., transitioning from `processing` to `shipped` or `delivered`).
* **Web Registration Channel**: Enable photographers to sign up directly on the Next.js marketing site, saving time and keeping registration platform-agnostic.

### 2.3 Phase 3: AI-Assisted Design & Industrial Print Orchestration
* **Adobe Photoshop JSX Script Orchestration**: Develop custom JavaScript (`.jsx`) script packages that lab managers can run directly inside Adobe Photoshop on the Windows workstation. The script will fetch order details and high-resolution layout configurations from the database, automatically creating canvas documents, aligning margins, and generating 1-click ready-to-print PSD layouts.
* **Offline-First Synchronization**: Refactor the Flutter photographer app to support robust local draft persistence using SQLite. If a wedding photographer experiences network failure while configuring an album in a remote location, their changes are saved locally and synced automatically once an active connection is restored.
* **Reporting Engine**: Implement direct CSV and formatted PDF exports inside the admin desktop app for revenue sheets, active product catalog statistics, and photographer balance ledger audits.
