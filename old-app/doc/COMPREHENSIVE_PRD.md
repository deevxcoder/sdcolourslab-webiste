# Comprehensive Product Requirements Document (PRD)
## SD Colours Photobook Lab - Complete Ecosystem

**Version:** 2.0  
**Last Updated:** May 23, 2026  
**Project Status:** Active Development

---

## Table of Contents
1. [Executive Summary](#1-executive-summary)
2. [Project Structure Overview](#2-project-structure-overview)
3. [Target Personas & User Roles](#3-target-personas--user-roles)
4. [System Architecture](#4-system-architecture)
5. [Module-by-Module Feature Breakdown](#5-module-by-module-feature-breakdown)
6. [Database Schema](#6-database-schema)
7. [API Endpoints](#7-api-endpoints)
8. [Technology Stack](#8-technology-stack)
9. [Development Roadmap](#9-development-roadmap)
10. [Non-Functional Requirements](#10-non-functional-requirements)

---

## 1. Executive Summary

### 1.1 Product Vision
**SD Colours Photobook Lab** is a premier, specialized photo printing and custom album manufacturing facility located in Rourkela, Odisha, India. The lab caters specifically to professional wedding and event photographers, providing top-tier, high-quality printing services including custom Combo Photo Pads, premium Flush Mount albums, LED photo frames, and crystal-clear Wall Acrylic photo prints.

### 1.2 Business Problem
Professional photographers handle large volumes of high-resolution digital prints and custom-bound albums. Historically, placing album orders involved:
- Tedious manual coordination
- Inconsistent sizing specifications
- Lack of order-status transparency
- High administrative overhead for lab managers
- Difficulty tracking approvals, pricing tiers, and paper quality

### 1.3 Solution Overview
The SD Colours Photobook Lab digital ecosystem is a **four-tier architecture** that bridges these gaps:

1. **Next.js 16 Marketing Site** - High-impact, premium public-facing catalog and gallery
2. **PHP Core Engine & PostgreSQL Backend** - Robust legacy engine serving as secure relational data warehouse
3. **Flutter Photographer Mobile App** - Native mobile application for verified photographers
4. **Flutter Windows Desktop Admin Console** - Intensive office dashboard for lab operators

---

## 2. Project Structure Overview

### 2.1 Monorepo Directory Layout

```
sdcolourslab-webiste/
├── doc/                                    # Universal Ecosystem Documentation
│   ├── prd.md                              # Original Product Requirements Document
│   ├── architecture.md                     # System Architecture & Technical Specifications
│   ├── api_endpoints.md                    # Complete REST API Reference
│   ├── tracker.md                          # Feature Progress & Development Roadmap
│   └── COMPREHENSIVE_PRD.md                # This Document
│
├── src/                                    # Next.js 16 Public Website Source
│   ├── app/                                # Next.js App Router (React 19 Pages)
│   │   ├── page.tsx                        # Homepage Hero & Live Highlights
│   │   ├── layout.tsx                      # Nav, Meta Headers, Base UI Context
│   │   ├── globals.css                     # Global Styles & Tailwind Configuration
│   │   ├── favicon.ico                     # Site Favicon
│   │   ├── about/                          # History & Rourkela Printing Standards
│   │   │   └── page.tsx                    # About page content
│   │   ├── contact/                        # Google Map, Inquiry Forms
│   │   │   └── page.tsx                    # Contact page with form
│   │   ├── gallery/                        # Interactive Media Grid
│   │   │   └── page.tsx                    # Gallery showcase
│   │   ├── pricing/                        # Responsive Rate Cards
│   │   │   └── page.tsx                    # Pricing tables
│   │   ├── products/                       # Browse Signature Albums & Combos
│   │   │   └── page.tsx                    # Product catalog
│   │   └── register/                       # Photographer Registration
│   │       └── page.tsx                    # Registration form
│   └── components/                         # Reusable UI Components
│       ├── layout/                         # Layout Components
│       │   ├── Header.tsx                  # Navigation Header
│       │   └── Footer.tsx                  # Site Footer
│       └── ui/                             # UI Components
│           ├── FloatingWhatsApp.tsx        # WhatsApp Chat Widget
│           └── ProductCard.tsx             # Product Display Card
│
├── sdcolorslab/                            # Central PHP Backend & APIs
│   ├── index.php                           # Legacy Public Home & Direct Landing
│   ├── index.html                          # Static HTML Homepage
│   ├── router.php                          # Central Request Router & Static File Handler
│   ├── database.sql                        # Core SQL Database Schema (Postgres/MySQL compatible)
│   ├── style.css                           # Legacy Web Stylesheet
│   ├── includes/                           # Modular PHP Code Blocks
│   │   ├── db.php                          # PDO Multi-Dialect Connection Broker
│   │   ├── auth.php                        # Session & Cookie Verification Guard
│   │   ├── config.php                      # Database Configuration
│   │   ├── config.local.php                # Local Development Configuration
│   │   ├── header.php                      # Shared HTML Header & Nav Links
│   │   └── footer.php                      # Shared HTML Footer & Contact Info
│   ├── admin/                              # Administrative Web Portal Pages
│   │   ├── index.php                       # Admin Dashboard Grid
│   │   ├── orders.php                      # Batch Orders Management
│   │   ├── products.php                    # Products Management Console
│   │   └── photographers.php               # Account Verification Manager
│   ├── photographer/                       # Web Photographer Member Portal
│   │   ├── index.php                       # Account Dashboard
│   │   ├── shop.php                        # Online Catalog & Size Configurator
│   │   ├── cart.php                        # Active Session-based Shopping Cart
│   │   ├── checkout.php                    # Checkout Page & Notes Handler
│   │   └── orders.php                      # My History & Invoices List
│   ├── api/                                # REST JSON API Handlers
│   │   ├── .htaccess                       # API Routing Configuration
│   │   ├── index.php                       # Core API Endpoint Multiplexer
│   │   ├── docs.php                        # Live, Self-Hosted API Documentation
│   │   ├── helpers.php                     # API Helper Functions
│   │   ├── check_users.php                 # User Verification Utility
│   │   ├── verify_pass.php                 # Password Verification Utility
│   │   ├── repair_database.php             # Database Repair Tool
│   │   └── setup_enhanced_tools.php        # Enhanced Tools Setup
│   ├── images/                             # Logo and Combo Product Monograms
│   │   └── combos/                         # High-Resolution Marketing Assets
│   ├── login.php                           # Login Page
│   ├── logout.php                          # Logout Handler
│   ├── register.php                        # Registration Page
│   ├── about.php / about.html              # About Pages (PHP & HTML)
│   ├── contact.php / contact.html          # Contact Pages (PHP & HTML)
│   ├── gallery.php / gallery.html          # Gallery Pages (PHP & HTML)
│   ├── pricing.php / pricing.html          # Pricing Pages (PHP & HTML)
│   ├── products.php / products.html        # Products Pages (PHP & HTML)
│   ├── products.json                       # Product Data JSON
│   ├── orders.json                         # Order Data JSON
│   ├── users.json                          # User Data JSON
│   ├── order_items.json                    # Order Items Data JSON
│   ├── test_db.php                         # Database Test Script
│   ├── test_live_api.php                   # API Test Script
│   ├── check_paths.php                     # Path Verification Script
│   ├── u953522373_sdcolourslab.sql         # Database Backup
│   ├── import_data.sql                     # Data Import Script
│   └── .git/                               # Git Repository
│
├── lab_desktop_app/                        # Flutter Windows Administrative App
│   ├── lib/                                # Flutter Application Core
│   │   ├── main.dart                       # Windows Process Entry Point & Multi-Provider Bindings
│   │   ├── models/                         # Serialization Models
│   │   │   ├── order.dart                  # Order Data Model
│   │   │   ├── user.dart                   # User Data Model
│   │   │   └── product.dart                # Product Data Model
│   │   ├── services/                       # HTTP Request Brokers
│   │   │   └── api_service.dart            # API Service Layer
│   │   ├── providers/                      # MVVM State Engines
│   │   │   ├── auth_provider.dart          # Authentication State Management
│   │   │   ├── order_provider.dart         # Order State Management
│   │   │   ├── product_provider.dart       # Product State Management
│   │   │   └── photographer_provider.dart   # Photographer State Management
│   │   ├── screens/                        # High-Resolution Desktop Interfaces
│   │   │   ├── login_screen.dart           # Admin Login Screen
│   │   │   ├── dashboard_screen.dart       # Admin Dashboard
│   │   │   ├── orders_screen.dart          # Order Management Screen
│   │   │   ├── photographers_screen.dart   # Photographer Verification Screen
│   │   │   ├── products_screen.dart        # Product Management Screen
│   │   │   ├── reports_screen.dart         # Analytics & Reports Screen
│   │   │   ├── broadcast_screen.dart       # Broadcast Messaging Screen
│   │   │   ├── settings_screen.dart        # Settings Screen
│   │   │   └── main_layout.dart           # Main Layout Wrapper
│   │   └── widgets/                        # Reusable Widgets
│   ├── pubspec.yaml                        # Windows Native Dependencies & Assets
│   ├── windows/                            # Windows-specific Configuration
│   └── test/                              # Test Suite
│
├── photographer_mobile_app/                # Flutter Android/iOS E-Commerce App
│   ├── lib/                                # Mobile Application Core
│   │   ├── main.dart                       # App Init & Route Guards
│   │   ├── models/                         # Mobile Data Models
│   │   │   ├── cart_item.dart              # Cart Item Model
│   │   │   ├── user.dart                   # User Model
│   │   │   ├── order.dart                  # Order Model
│   │   │   └── product.dart                # Product Model
│   │   ├── services/                       # Mobile API Connection Broker
│   │   │   └── api_service.dart            # API Service Layer
│   │   ├── providers/                      # Mobile State Engines
│   │   │   ├── auth_provider.dart          # Authentication State
│   │   │   ├── cart_provider.dart          # Shopping Cart State
│   │   │   ├── catalog_provider.dart       # Product Catalog State
│   │   │   └── order_provider.dart         # Order History State
│   │   └── screens/                        # Responsive Native View Screens
│   │       ├── login_screen.dart           # Photographer Login
│   │       ├── register_screen.dart       # Photographer Registration
│   │       ├── pending_screen.dart         # Pending Approval Screen
│   │       ├── home_screen.dart            # Home Dashboard
│   │       ├── catalog_screen.dart         # Product Catalog
│   │       ├── cart_screen.dart            # Shopping Cart
│   │       ├── orders_screen.dart          # Order History
│   │       └── main_layout.dart           # Main Layout Wrapper
│   ├── pubspec.yaml                        # Mobile Device Dependencies & Plugins
│   ├── android/                            # Android-specific Configuration
│   ├── ios/                                # iOS-specific Configuration
│   └── test/                              # Test Suite
│
├── public/                                 # Static Assets for Next.js
│   ├── logo.png                            # Company Logo
│   ├── monogram.png                        # Brand Monogram
│   ├── price_list.pdf                      # Price List Document
│   ├── combo_price_list.pdf                # Combo Price List
│   ├── test.png                            # Test Image
│   └── [SVG Icons]                         # Various SVG Icons
│
├── package.json                            # Next.js Dependencies
├── next.config.ts                          # Next.js Configuration
├── tsconfig.json                           # TypeScript Configuration
├── tailwind.config.ts                      # Tailwind CSS Configuration
├── postcss.config.mjs                      # PostCSS Configuration
├── eslint.config.mjs                       # ESLint Configuration
├── .gitignore                              # Git Ignore Rules
├── README.md                               # Project README
├── AGENTS.md                               # Agent Configuration
└── CLAUDE.md                               # Claude Configuration
```

---

## 3. Target Personas & User Roles

### 3.1 Role-Based Access Control (RBAC)

The system enforces strict role-based access control across three user types:

```
┌─────────────────────────────────────────────────────────────┐
│                    USER ROLE HIERARCHY                      │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────────┐                                       │
│  │ Public Guest     │ → Can view: Marketing site, Gallery,  │
│  │ (Unauthenticated)│   Pricing, Products, Contact          │
│  └────────┬─────────┘                                       │
│           │ Registers                                        │
│           ▼                                                  │
│  ┌──────────────────┐                                       │
│  │ Pending          │ → Must wait for admin approval         │
│  │ Photographer     │ → Cannot place orders                 │
│  └────────┬─────────┘                                       │
│           │ Approved by Admin                                │
│           ▼                                                  │
│  ┌──────────────────┐                                       │
│  │ Approved         │ → Can: Browse catalog, Add to cart,   │
│  │ Photographer     │   Place orders, Track status           │
│  └──────────────────┘                                       │
│                                                              │
│  ┌──────────────────┐                                       │
│  │ Lab Administrator │ → Can: Approve users, Manage orders,  │
│  │ (Admin)          │   Edit products, View reports,         │
│  │                  │   Broadcast messages                   │
│  └──────────────────┘                                       │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 Public Guest / Visitor
- **Profile**: Freelance photographers, event coordinators, or local clients browsing the web for printing services
- **Context**: Seeks rapid validation of print quality, list of combo styles, active product pricing, and simple contact routes
- **Key Goals**: 
  - Browse public catalogs
  - View the high-definition gallery
  - Register for a professional partner account
  - Initiate contact via WhatsApp
- **Access Level**: Public pages only (no authentication required)

### 3.3 Registered Professional Photographer (Mobile E-Commerce User)
- **Profile**: B2B photography studio owners, wedding filmmakers, and premium portrait photographers
- **Context**: Requires a seamless shop-like experience to select customized print dimensions, calculate dynamic pricing, and monitor production status in real-time
- **Key Goals**:
  - Order complex combo sets
  - Configure pages (Regular, NTR, Velvet, Silky sheets)
  - Track order history with status timeline
  - Communicate special lab instructions
- **Access Level**: Photographer mobile app + web portal (requires approved status)

### 3.4 Lab Administrator / Production Manager (Desktop Operator)
- **Profile**: SD Colours business owners, print operators, and office managers
- **Context**: Operates on high-resolution widescreen displays, managing accounts, verifying studio credentials, and moving production queues quickly
- **Key Goals**:
  - Approve photographer sign-ups
  - Adjust product lines and pricing structures
  - Update order stages (pending → processing → shipped → delivered)
  - Broadcast status updates
  - Run financial audits
- **Access Level**: Full admin access (desktop app + web admin portal)

---

## 4. System Architecture

### 4.1 System Topology

```
┌─────────────────────────────────────────────────────────────────┐
│                     CLIENT APPLICATIONS                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────┐  │
│  │   Next.js 16     │  │  Flutter Mobile  │  │ Flutter Win  │  │
│  │  Marketing Site  │  │  Photographer    │  │ Desktop App  │  │
│  │  (Public Web)    │  │  App (B2B Shop)  │  │ (Admin)      │  │
│  └────────┬─────────┘  └────────┬─────────┘  └──────┬───────┘  │
│           │                     │                     │          │
│           │ HTTP/HTTPS          │ JSON REST API       │ JSON REST│
│           │                     │ Bearer Auth         │ Bearer   │
│           ▼                     ▼                     ▼          │
└───────────┼─────────────────────┼─────────────────────┼──────────┘
            │                     │                     │
┌───────────┼─────────────────────┼─────────────────────┼──────────┐
│           ▼                     ▼                     ▼          │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │              PHP 8.2 Backend Engine                        │  │
│  │  ┌────────────┐  ┌────────────┐  ┌──────────────┐       │  │
│  │  │   Router   │  │ REST API   │  │  Auth Guard  │       │  │
│  │  │  (router.  │  │  Handler   │  │  (auth.php)  │       │  │
│  │  │   php)     │  │ (api/*.php)│  │              │       │  │
│  │  └─────┬──────┘  └─────┬──────┘  └──────┬───────┘       │  │
│  │        │                │                 │                │  │
│  │        └────────────────┼─────────────────┘                │  │
│  │                         ▼                                  │  │
│  │              ┌──────────────────┐                          │  │
│  │              │  PDO Database    │                          │  │
│  │              │  Connection      │                          │  │
│  │              └────────┬─────────┘                          │  │
│  └───────────────────────┼────────────────────────────────────┘  │
│                          ▼                                       │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │         PostgreSQL / MySQL Database                      │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐  │   │
│  │  │  users   │ │ products │ │  orders  │ │api_tokens│  │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘  │   │
│  │  ┌──────────┐ ┌──────────┐                              │   │
│  │  │order_items│ │  (more)  │                              │   │
│  │  └──────────┘ └──────────┘                              │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘
```

### 4.2 Data Flow Architecture

1. **Public Access Flow**: Next.js marketing site serves static content and can optionally fetch product data from PHP API
2. **Photographer Flow**: Mobile app authenticates via PHP API, stores Bearer token locally, makes authenticated requests for catalog, cart, and orders
3. **Admin Flow**: Desktop app authenticates as admin, has full CRUD access to all resources
4. **Database Layer**: PHP backend uses PDO with prepared statements for all database operations, supporting both MySQL/MariaDB (local) and PostgreSQL (production)

---

## 5. Module-by-Module Feature Breakdown

### 5.1 Next.js 16 Marketing Website

**Technology Stack:**
- Next.js 16 (App Router)
- React 19
- TypeScript
- Tailwind CSS v4
- Lucide React Icons

**Directory Structure:**
```
src/
├── app/
│   ├── page.tsx                    # Homepage with hero, featured products
│   ├── layout.tsx                  # Root layout with header/footer
│   ├── globals.css                 # Global styles
│   ├── about/page.tsx              # About SD Colours Lab
│   ├── contact/page.tsx            # Contact form and info
│   ├── gallery/page.tsx            # Photo gallery showcase
│   ├── pricing/page.tsx            # Pricing tables
│   ├── products/page.tsx           # Product catalog
│   └── register/page.tsx          # Photographer registration
└── components/
    ├── layout/
    │   ├── Header.tsx              # Navigation header
    │   └── Footer.tsx              # Site footer
    └── ui/
        ├── FloatingWhatsApp.tsx     # WhatsApp chat widget
        └── ProductCard.tsx          # Product display card
```

**Features:**

#### 5.1.1 Homepage (page.tsx)
- **Hero Section**: 
  - Premium branding with gradient text
  - Background image overlay with gradient
  - Call-to-action buttons (WhatsApp, Pricing, Phone)
  - Responsive design for mobile/desktop
- **Trust Section**:
  - Shipping across India badge
  - High-quality printing (HP Indigo) badge
  - Premium wedding albums badge
- **Categories Highlight**:
  - 5 core offerings with icons:
    - Wedding Albums
    - Combo Photo Pads
    - Acrylic Prints
    - LED Frames
    - Wall Canvas
  - Circular icon design with hover effects
- **Featured Products**:
  - 6 featured product cards:
    - Leather Combo Photo Pad (₹1,550)
    - Acrylic Combo Photo Pad (₹1,250)
    - Inluxury Combo (₹4,100)
    - Platinum Series (₹3,150)
    - Royal Combo (₹2,250)
    - Special Album (Custom)
  - Each card shows: price, image, tag, sizes, features
- **CTA Section**:
  - WhatsApp integration for direct contact
  - Background image with overlay

#### 5.1.2 About Page (about/page.tsx)
- Company history and mission
- Rourkela printing standards
- Team information
- Quality assurance details

#### 5.1.3 Contact Page (contact/page.tsx)
- Contact form with validation
- Google Maps integration
- Contact information display
- WhatsApp direct link

#### 5.1.4 Gallery Page (gallery/page.tsx)
- Multi-category media grid
- Combos, Albums, Frames, Acrylics categories
- High-resolution image display
- Lightbox or zoom functionality

#### 5.1.5 Pricing Page (pricing/page.tsx)
- Responsive pricing tables
- Cost per page breakdown:
  - Velvet sheets
  - Silky sheets
  - NTR sheets
  - Lustre sheets
- Volume-tiered pricing
- PDF download links for price lists

#### 5.1.6 Products Page (products/page.tsx)
- Filterable product grid
- Category filters (Albums, Combos, Frames, Acrylics)
- Product cards with detailed info
- Size selection options
- Feature highlights

#### 5.1.7 Register Page (register/page.tsx)
- Photographer registration form
- Fields: name, email, password, phone, studio_name, city
- Form validation
- Submission to PHP API
- Pending approval notification

#### 5.1.8 Layout Components

**Header.tsx:**
- Responsive navigation
- Logo display
- Menu items: Home, Products, Gallery, Pricing, About, Contact
- Mobile hamburger menu
- Sticky positioning

**Footer.tsx:**
- Contact information
- Social media links
- Quick navigation links
- Copyright information

**FloatingWhatsApp.tsx:**
- Fixed position WhatsApp button
- Pre-filled message template
- Hover effects
- Mobile-friendly sizing

**ProductCard.tsx:**
- Product image display
- Price and tag badges
- Size list display
- Feature bullet points
- Hover animations
- Link to product details

---

### 5.2 PHP Backend & REST API

**Technology Stack:**
- PHP 8.2
- PDO (PHP Data Objects)
- MySQL/MariaDB (Local Development)
- PostgreSQL (Production)
- JSON REST API
- Bcrypt Password Hashing

**Directory Structure:**
```
sdcolorslab/
├── includes/
│   ├── db.php                      # PDO connection broker
│   ├── auth.php                    # Session & auth guard
│   ├── config.php                  # Database config
│   ├── config.local.php            # Local dev config
│   ├── header.php                  # Shared HTML header
│   └── footer.php                  # Shared HTML footer
├── api/
│   ├── index.php                   # API endpoint router
│   ├── docs.php                    # Live API documentation
│   ├── helpers.php                 # Helper functions
│   ├── check_users.php             # User verification
│   ├── verify_pass.php             # Password verification
│   ├── repair_database.php         # DB repair tool
│   └── setup_enhanced_tools.php    # Tools setup
├── admin/
│   ├── index.php                   # Admin dashboard
│   ├── orders.php                  # Order management
│   ├── products.php                # Product management
│   └── photographers.php           # Photographer verification
├── photographer/
│   ├── index.php                   # Photographer dashboard
│   ├── shop.php                    # Product catalog
│   ├── cart.php                    # Shopping cart
│   ├── checkout.php                # Checkout process
│   └── orders.php                  # Order history
├── [Public Pages]
│   ├── index.php / index.html
│   ├── about.php / about.html
│   ├── contact.php / contact.html
│   ├── gallery.php / gallery.html
│   ├── pricing.php / pricing.html
│   ├── products.php / products.html
│   ├── login.php
│   ├── register.php
│   └── logout.php
└── [Data Files]
    ├── products.json
    ├── orders.json
    ├── users.json
    └── order_items.json
```

**Features:**

#### 5.2.1 Database Connection (includes/db.php)
- PDO-based connection
- Multi-dialect support (MySQL/PostgreSQL)
- Prepared statement support
- Error handling
- Connection pooling

#### 5.2.2 Authentication System (includes/auth.php)
- Session management
- Cookie verification
- Bearer token validation
- Role-based access control
- Password verification with bcrypt

#### 5.2.3 REST API (api/index.php)

**Auth Endpoints:**
- `POST /auth/login` - User login, returns Bearer token
- `POST /auth/register` - New photographer registration
- `GET /auth/me` - Get current user profile
- `PATCH /auth/me` - Update user profile
- `POST /auth/logout` - Revoke token

**Public Products Endpoints:**
- `GET /products` - List all active products (with category filter)
- `GET /products/{id}` - Get single product details

**Photographer Endpoints:**
- `GET /photographer/dashboard` - Dashboard statistics
- `GET /photographer/orders` - List photographer's orders
- `GET /photographer/orders/{id}` - Get order details
- `POST /photographer/orders` - Submit new order

**Admin Endpoints:**
- `GET /admin/dashboard` - Admin dashboard metrics
- `GET /admin/orders` - List all orders (with filters)
- `GET /admin/orders/{id}` - Get order details (with photographer info)
- `PATCH /admin/orders/{id}` - Update order status
- `GET /admin/photographers` - List photographers (with status filter)
- `PATCH /admin/photographers/{id}` - Approve/reject photographer
- `GET /admin/products` - List all products (including inactive)
- `POST /admin/products` - Create new product
- `PUT /admin/products/{id}` - Update product (full replacement)
- `PATCH /admin/products/{id}/toggle` - Toggle product visibility
- `DELETE /admin/products/{id}` - Delete product

#### 5.2.4 Admin Web Portal (admin/)

**Dashboard (admin/index.php):**
- Overview statistics
- Recent orders list
- Pending photographers count
- Quick action buttons

**Orders Management (admin/orders.php):**
- Order list with filters
- Status update functionality
- Order detail view
- Photographer information
- Admin notes field

**Products Management (admin/products.php):**
- Product CRUD operations
- Image upload
- Price editing
- Category management
- Active/inactive toggle

**Photographer Verification (admin/photographers.php):**
- Pending photographer list
- Approve/reject actions
- Photographer details view
- Status management

#### 5.2.5 Photographer Web Portal (photographer/)

**Dashboard (photographer/index.php):**
- Order statistics
- Recent orders
- Quick actions

**Shop (photographer/shop.php):**
- Product catalog
- Category filters
- Size selection
- Add to cart functionality

**Cart (photographer/cart.php):**
- Cart item list
- Quantity adjustment
- Item removal
- Total calculation
- Proceed to checkout

**Checkout (photographer/checkout.php):**
- Order review
- Special instructions
- Order submission
- Confirmation display

**Orders (photographer/orders.php):**
- Order history list
- Status tracking
- Order detail view
- Timeline display

#### 5.2.6 Public Pages

**Legacy Pages (PHP & HTML versions):**
- Index page with hero section
- About page with company info
- Contact page with form
- Gallery page with image grid
- Pricing page with tables
- Products page with catalog
- Login page for authentication
- Register page for new photographers

#### 5.2.7 API Documentation (api/docs.php)
- Interactive API documentation
- Endpoint listing
- Request/response examples
- Authentication instructions
- Live testing interface

---

### 5.3 Flutter Photographer Mobile App

**Technology Stack:**
- Flutter 3.10.7+
- Dart
- Provider (State Management)
- HTTP (API calls)
- Shared Preferences (Local storage)
- Google Fonts
- Shimmer (Loading animations)
- File Picker (File uploads)

**Directory Structure:**
```
photographer_mobile_app/
├── lib/
│   ├── main.dart                   # App entry point
│   ├── models/
│   │   ├── user.dart              # User data model
│   │   ├── product.dart           # Product data model
│   │   ├── order.dart              # Order data model
│   │   └── cart_item.dart          # Cart item model
│   ├── services/
│   │   └── api_service.dart        # API service layer
│   ├── providers/
│   │   ├── auth_provider.dart      # Authentication state
│   │   ├── catalog_provider.dart   # Catalog state
│   │   ├── cart_provider.dart      # Cart state
│   │   └── order_provider.dart     # Order state
│   └── screens/
│       ├── login_screen.dart       # Login screen
│       ├── register_screen.dart   # Registration screen
│       ├── pending_screen.dart     # Pending approval screen
│       ├── home_screen.dart        # Home dashboard
│       ├── catalog_screen.dart     # Product catalog
│       ├── cart_screen.dart        # Shopping cart
│       ├── orders_screen.dart      # Order history
│       └── main_layout.dart        # Main layout wrapper
├── android/                       # Android configuration
├── ios/                           # iOS configuration
└── pubspec.yaml                   # Dependencies
```

**Features:**

#### 5.3.1 Authentication Flow

**Login Screen (login_screen.dart):**
- Email/password input
- Form validation
- API authentication
- Token storage
- Error handling
- Remember me option

**Register Screen (register_screen.dart):**
- Full registration form
- Fields: name, email, password, phone, studio_name, city
- Form validation
- API submission
- Success/error feedback

**Pending Screen (pending_screen.dart):**
- Displayed when account status is 'pending'
- Informative message
- Contact admin option
- Prevents access to other features

#### 5.3.2 Home Dashboard (home_screen.dart)
- Welcome message with user name
- Quick stats (total orders, total spent)
- Recent orders list
- Quick action buttons
- Navigation to other sections

#### 5.3.3 Product Catalog (catalog_screen.dart)
- Category tabs (Combos, Albums, Frames, Acrylics)
- Product grid with images
- Product details modal
- Size selection
- Price display
- Add to cart functionality
- Search and filter options
- Sorting options

#### 5.3.4 Shopping Cart (cart_screen.dart)
- Cart items list
- Quantity adjustment (+/-)
- Item removal
- Size modification
- Special notes per item
- Total calculation
- Proceed to checkout button
- Empty cart state

#### 5.3.5 Order History (orders_screen.dart)
- Order list with status badges
- Status timeline (pending → processing → shipped → delivered)
- Order detail view
- Item breakdown
- Order notes
- Admin notes display
- Refresh functionality

#### 5.3.6 State Management (Providers)

**AuthProvider:**
- Manages user authentication state
- Stores and retrieves token
- Login/logout methods
- User profile caching
- Auth status checks

**CatalogProvider:**
- Fetches product catalog
- Category filtering
- Product search
- Loading states
- Error handling

**CartProvider:**
- Manages cart items
- Add/remove items
- Update quantities
- Calculate totals
- Persist cart locally
- Clear cart on checkout

**OrderProvider:**
- Fetches order history
- Submits new orders
- Updates order status
- Order detail retrieval

#### 5.3.7 API Service (api_service.dart)
- Base URL configuration
- HTTP request handling
- Bearer token injection
- Response parsing
- Error handling
- Timeout management

---

### 5.4 Flutter Lab Desktop App

**Technology Stack:**
- Flutter 3.10.7+ (Windows)
- Dart
- Provider (State Management)
- HTTP (API calls)
- Shared Preferences (Local storage)
- Google Fonts
- Shimmer (Loading animations)
- FL Chart (Charts and graphs)

**Directory Structure:**
```
lab_desktop_app/
├── lib/
│   ├── main.dart                   # App entry point
│   ├── models/
│   │   ├── user.dart              # User data model
│   │   ├── product.dart           # Product data model
│   │   └── order.dart              # Order data model
│   ├── services/
│   │   └── api_service.dart        # API service layer
│   ├── providers/
│   │   ├── auth_provider.dart      # Authentication state
│   │   ├── product_provider.dart   # Product state
│   │   ├── order_provider.dart     # Order state
│   │   └── photographer_provider.dart # Photographer state
│   ├── screens/
│   │   ├── login_screen.dart       # Admin login
│   │   ├── dashboard_screen.dart   # Admin dashboard
│   │   ├── orders_screen.dart      # Order management
│   │   ├── photographers_screen.dart # Photographer verification
│   │   ├── products_screen.dart    # Product management
│   │   ├── reports_screen.dart     # Analytics & reports
│   │   ├── broadcast_screen.dart   # Broadcast messaging
│   │   ├── settings_screen.dart    # Settings
│   │   └── main_layout.dart        # Main layout wrapper
│   └── widgets/                    # Reusable widgets
├── windows/                        # Windows-specific config
└── pubspec.yaml                    # Dependencies
```

**Features:**

#### 5.4.1 Admin Authentication

**Login Screen (login_screen.dart):**
- Admin-specific login
- Email/password authentication
- Role verification (admin only)
- Token storage
- Remember credentials option

#### 5.4.2 Admin Dashboard (dashboard_screen.dart)
- Business metrics cards:
  - Total orders
  - Total revenue
  - Pending orders
  - Active photographers
  - Pending photographers
- Orders by status chart
- Recent orders table
- Quick action buttons
- Revenue trends graph

#### 5.4.3 Order Management (orders_screen.dart)
- Complete order list
- Filters by status (pending, processing, shipped, delivered, cancelled)
- Search by order ID, photographer name, studio name
- Order detail view with:
  - Photographer contact info
  - Order items breakdown
  - Special instructions
  - Admin notes field
- Status update workflow
- Bulk status update option
- Export orders functionality

#### 5.4.4 Photographer Verification (photographers_screen.dart)
- Photographer list with filters
- Pending photographers queue
- Photographer detail view:
  - Contact information
  - Studio details
  - Registration date
- Approve/Reject actions
- Status change confirmation
- Photographer search

#### 5.4.5 Product Management (products_screen.dart)
- Product catalog list
- Add new product form:
  - Name, category, description
  - Price, alternate price
  - Sizes (JSON array)
  - Features (JSON array)
  - Tag, image, sort order
- Edit existing product
- Toggle active/inactive status
- Delete product (with warning)
- Image upload functionality
- Product reordering

#### 5.4.6 Reports & Analytics (reports_screen.dart)
- Revenue reports:
  - Daily/weekly/monthly revenue
  - Revenue by product category
  - Revenue by photographer
- Order reports:
  - Order volume trends
  - Orders by status
  - Average order value
- Product reports:
  - Best-selling products
  - Product category distribution
- Photographer reports:
  - Active photographers
  - Top photographers by revenue
- Export to CSV/PDF functionality

#### 5.4.7 Broadcast Messaging (broadcast_screen.dart)
- Create broadcast message
- Target audience selection:
  - All photographers
  - Specific photographer
  - Photographers with pending orders
- Message templates
- Schedule broadcast
- Broadcast history
- Delivery status

#### 5.4.8 Settings (settings_screen.dart)
- API configuration
- Database connection settings
- Notification preferences
- Theme settings
- User profile management
- Logout functionality

#### 5.4.9 State Management (Providers)

**AuthProvider:**
- Admin authentication
- Token management
- Session persistence
- Auto-login on startup

**ProductProvider:**
- Product CRUD operations
- Catalog fetching
- Active/inactive filtering
- Product search

**OrderProvider:**
- Order fetching with filters
- Status updates
- Order detail retrieval
- Bulk operations

**PhotographerProvider:**
- Photographer list fetching
- Status filtering
- Approval/rejection actions
- Photographer search

---

## 6. Database Schema

### 6.1 Entity Relationship Diagram

```
┌─────────────────┐       ┌─────────────────┐
│     users       │       │   api_tokens    │
├─────────────────┤       ├─────────────────┤
│ id (PK)         │───┐   │ id (PK)         │
│ name            │   │   │ user_id (FK)    │◄──┐
│ email (UK)      │   │   │ token (UK)      │   │
│ password_hash   │   │   │ expires_at      │   │
│ role            │   │   │ created_at      │   │
│ phone           │   │   └─────────────────┘   │
│ studio_name     │   │                          │
│ city            │   │                          │
│ status          │   │                          │
│ created_at      │   │                          │
└─────────────────┘   │                          │
                      │                          │
                      │                          │
┌─────────────────┐   │                          │
│     orders      │   │                          │
├─────────────────┤   │                          │
│ id (PK)         │◄──┘                          │
│ photographer_id │◄─────────────────────────────┘
│ status          │                              │
│ total           │                              │
│ notes           │                              │
│ admin_notes     │                              │
│ created_at      │                              │
│ updated_at      │                              │
└─────────────────┘                              │
        │                                       │
        │                                       │
┌─────────────────┐                              │
│  order_items    │                              │
├─────────────────┤                              │
│ id (PK)         │                              │
│ order_id (FK)   │◄─────────────────────────────┘
│ product_id (FK) │───┐
│ product_name    │   │
│ size            │   │
│ quantity        │   │
│ unit_price      │   │
│ notes           │   │
└─────────────────┘   │
                      │
┌─────────────────┐   │
│    products     │   │
├─────────────────┤   │
│ id (PK)         │◄──┘
│ name            │
│ category        │
│ description     │
│ price           │
│ price_alt       │
│ sizes           │
│ features        │
│ tag             │
│ image           │
│ active          │
│ sort_order      │
│ created_at      │
└─────────────────┘
```

### 6.2 Table Definitions

#### 6.2.1 users
Stores administrative operators and professional B2B photographers.

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT UNSIGNED | PK, AUTO_INCREMENT | Unique user identifier |
| name | VARCHAR(150) | NOT NULL | User's full name |
| email | VARCHAR(200) | NOT NULL, UNIQUE | Unique email address |
| password_hash | VARCHAR(255) | NOT NULL | Bcrypt hashed password |
| role | ENUM | NOT NULL, DEFAULT 'photographer' | System access level: 'admin', 'photographer' |
| phone | VARCHAR(20) | NULLABLE | Mobile contact number |
| studio_name | VARCHAR(200) | NULLABLE | Registered photography business name |
| city | VARCHAR(100) | NULLABLE | Primary service city location |
| status | ENUM | NOT NULL, DEFAULT 'pending' | Verification status: 'pending', 'approved', 'rejected' |
| created_at | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Sign up date |

#### 6.2.2 api_tokens
Manages native mobile and desktop API authentication sessions.

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT UNSIGNED | PK, AUTO_INCREMENT | Unique token sequence |
| user_id | INT UNSIGNED | NOT NULL, FK → users.id | Owning user identifier |
| token | VARCHAR(100) | NOT NULL, UNIQUE | Cryptographically randomized token string |
| expires_at | DATETIME | NOT NULL | Token lifespan (default: 30 days) |
| created_at | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Creation date |

#### 6.2.3 products
The print catalog, including albums, acrylic frames, and customizable combo packages.

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT UNSIGNED | PK, AUTO_INCREMENT | Unique product number |
| name | VARCHAR(200) | NOT NULL | Product name |
| category | VARCHAR(100) | NULLABLE | Broad division: 'album', 'combo', 'led_frame', 'wall_acrylic' |
| description | TEXT | NULLABLE | Explanatory details |
| price | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00 | Base cost in INR |
| price_alt | DECIMAL(10,2) | NULLABLE | Secondary/bulk page cost in INR |
| sizes | LONGTEXT | NULLABLE | Available sizes list (JSON array) |
| features | LONGTEXT | NULLABLE | Bulleted details (JSON array) |
| tag | VARCHAR(100) | NULLABLE | Special label: 'Best Seller', 'Premium' |
| image | VARCHAR(300) | NULLABLE | Local asset path |
| active | TINYINT(1) | NOT NULL, DEFAULT 1 | Toggle for store display |
| sort_order | INT | NOT NULL, DEFAULT 0 | Sequential layout weight |
| created_at | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Creation timestamp |

#### 6.2.4 orders
Invoices submitted by approved photographers for production.

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT UNSIGNED | PK, AUTO_INCREMENT | Auto-incrementing order ID |
| photographer_id | INT UNSIGNED | NOT NULL, FK → users.id | Submitting user |
| status | ENUM | NOT NULL, DEFAULT 'pending' | Production step: 'pending', 'processing', 'shipped', 'delivered', 'cancelled' |
| total | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00 | Sum total of all items in INR |
| notes | TEXT | NULLABLE | Special printing instructions |
| admin_notes | TEXT | NULLABLE | Production details or comments |
| created_at | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Invoice creation date |
| updated_at | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Last state modification |

#### 6.2.5 order_items
Individual line items within a submitted order.

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | INT UNSIGNED | PK, AUTO_INCREMENT | Line item identifier |
| order_id | INT UNSIGNED | NOT NULL, FK → orders.id | Containing invoice |
| product_id | INT UNSIGNED | NULLABLE, FK → products.id | Linked catalog entry |
| product_name | VARCHAR(200) | NOT NULL | Snapshot of product name at time of order |
| size | VARCHAR(100) | NULLABLE | Configured size selection |
| quantity | INT | NOT NULL, DEFAULT 1 | Order count |
| unit_price | DECIMAL(10,2) | NOT NULL | Unit price in INR at time of order |
| notes | TEXT | NULLABLE | Additional customizations |

---

## 7. API Endpoints

### 7.1 Base URL
- **Local Development**: `http://localhost/sdcolorslab/api`
- **Production**: `https://your-domain.replit.app/api`

### 7.2 Authentication

All protected endpoints require:
```
Authorization: Bearer <token>
```

### 7.3 Endpoint Summary

#### Auth Endpoints (5)
- `POST /auth/login` - User login
- `POST /auth/register` - New registration
- `GET /auth/me` - Get current user
- `PATCH /auth/me` - Update user profile
- `POST /auth/logout` - Logout

#### Public Products (2)
- `GET /products` - List active products
- `GET /products/{id}` - Get single product

#### Photographer Endpoints (4)
- `GET /photographer/dashboard` - Dashboard stats
- `GET /photographer/orders` - List orders
- `GET /photographer/orders/{id}` - Get order details
- `POST /photographer/orders` - Submit new order

#### Admin Endpoints (11)
- `GET /admin/dashboard` - Admin dashboard metrics
- `GET /admin/orders` - List all orders
- `GET /admin/orders/{id}` - Get order details
- `PATCH /admin/orders/{id}` - Update order status
- `GET /admin/photographers` - List photographers
- `PATCH /admin/photographers/{id}` - Update photographer status
- `GET /admin/products` - List all products
- `POST /admin/products` - Create product
- `PUT /admin/products/{id}` - Update product
- `PATCH /admin/products/{id}/toggle` - Toggle product visibility
- `DELETE /admin/products/{id}` - Delete product

**Total: 22 API Endpoints**

---

## 8. Technology Stack

### 8.1 Frontend Technologies

#### Next.js Marketing Site
- **Framework**: Next.js 16.2.3 (App Router)
- **UI Library**: React 19.2.4
- **Language**: TypeScript 5
- **Styling**: Tailwind CSS v4
- **Icons**: Lucide React 1.8.0
- **Animations**: Tailwind CSS Animate 1.0.7
- **Build Tool**: Next.js built-in bundler
- **Deployment**: Static export (output: 'export')

#### Flutter Photographer Mobile App
- **Framework**: Flutter 3.10.7+
- **Language**: Dart
- **State Management**: Provider 6.0.5
- **Networking**: HTTP 1.1.0
- **Local Storage**: Shared Preferences 2.2.1
- **UI Components**: Material Design
- **Fonts**: Google Fonts 6.1.0
- **Loading**: Shimmer 3.0.0
- **File Handling**: File Picker 8.1.6
- **Platforms**: Android, iOS

#### Flutter Lab Desktop App
- **Framework**: Flutter 3.10.7+ (Windows)
- **Language**: Dart
- **State Management**: Provider 6.1.1
- **Networking**: HTTP 1.1.0
- **Local Storage**: Shared Preferences 2.2.2
- **UI Components**: Material Design
- **Fonts**: Google Fonts 6.1.0
- **Loading**: Shimmer 3.0.0
- **Charts**: FL Chart 0.70.0
- **Internationalization**: Intl 0.19.0
- **Platform**: Windows

### 8.2 Backend Technologies

#### PHP Backend
- **Language**: PHP 8.2
- **Database Abstraction**: PDO (PHP Data Objects)
- **Authentication**: Bcrypt password hashing
- **API Format**: JSON REST
- **Session Management**: Native PHP sessions
- **CORS**: Enabled for cross-origin requests

#### Database
- **Development**: MySQL/MariaDB (via Laragon)
- **Production**: PostgreSQL (via PDO)
- **Schema**: Relational with foreign keys
- **Character Set**: UTF-8 Unicode (utf8mb4)

### 8.3 Development Tools

#### Code Quality
- **Linting**: ESLint 9 (Next.js config)
- **Type Checking**: TypeScript 5
- **Code Style**: Prettier (implied)

#### Version Control
- **VCS**: Git
- **Platform**: Git (local repository)

#### Documentation
- **API Docs**: Self-hosted (api/docs.php)
- **Project Docs**: Markdown (doc/ folder)

---

## 9. Development Roadmap

### 9.1 Current Status (Phase 1 - Complete)

#### Next.js Marketing Site ✅
- [x] Responsive layout & navigation
- [x] Premium craftsmanship showcase
- [x] Combo pad catalog page
- [x] Interactive cost calculator
- [x] Contact form & lead routing
- [x] 1-click WhatsApp support
- [x] Server-side SEO meta headers

#### PHP Backend & APIs ✅
- [x] Router-based API routing
- [x] Standardized JSON response shapes
- [x] Secure password bcrypt hashing
- [x] Bearer token authentication guards
- [x] Relational DB integrations (PDO)
- [x] Public docs dashboard
- [x] Order queue status pipeline

#### Photographer Mobile App ✅
- [x] Secured splash/login onboarding
- [x] Photographer registration & verification
- [x] Interactive shop catalog dashboard
- [x] Dynamic sheet rate calculator
- [x] Local e-commerce cart manager
- [x] Order checkout & spec upload
- [x] Live progress milestones timeline

#### Lab Desktop App ✅
- [x] Administrative auth login panel
- [x] Operator dashboard metrics grid
- [x] User KYC account review console
- [x] Advanced live order search filter
- [x] Product catalog editor (CRUD)
- [x] Active product visibility switch

### 9.2 Phase 2: Mobile Engagement & Notifications (Planned)

#### Firebase Cloud Messaging Integration
- [ ] Setup Firebase in both Flutter apps
- [ ] Register device tokens to user accounts
- [ ] Implement push notification listeners
- [ ] Create notification templates

#### Automated Push Triggers
- [ ] Auto-push on order status changes
- [ ] Admin broadcast messaging system
- [ ] Photographer notification preferences
- [ ] Notification history tracking

#### Web Registration Channel
- [ ] Photographer signup on Next.js site
- [ ] Direct API integration
- [ ] Email verification workflow
- [ ] Pending approval notification

### 9.3 Phase 3: Advanced Features (Future)

#### Adobe Photoshop Integration
- [ ] JSX script development
- [ ] Automated album layout generation
- [ ] Database integration for order details
- [ ] 1-click print-ready PSD export

#### Offline-First Architecture
- [ ] SQLite local database for mobile app
- [ ] Offline cart state persistence
- [ ] Automatic sync on reconnection
- [ ] Conflict resolution strategy

#### Reporting Engine
- [ ] CSV export for admin reports
- [ ] PDF invoice generation
- [ ] Financial audit trails
- [ ] Custom report builder

#### Infrastructure Hardening
- [ ] Environment variable migration
- [ ] Rate limiting on auth endpoints
- [ ] Enterprise logging suite
- [ ] Automated database backups
- [ ] SSL/TLS certificate management

---

## 10. Non-Functional Requirements

### 10.1 Security & Authentication

#### Password Security
- All passwords must be hashed using bcrypt with minimum work factor of 10
- Passwords must never be stored in plain text
- Minimum password length: 6 characters

#### API Security
- All protected endpoints require Bearer token authentication
- Tokens have 30-day expiration
- Tokens are stored securely in api_tokens table
- SQL injection prevention via PDO prepared statements

#### Role-Based Access Control
- Strict RBAC enforcement on all endpoints
- Photographers cannot access admin endpoints
- Public endpoints are explicitly marked
- Pending photographers cannot place orders

#### Data Protection
- CORS policies configured appropriately
- Input validation on all endpoints
- Output encoding to prevent XSS
- Secure file upload handling

### 10.2 Performance & Availability

#### API Performance
- API endpoints must respond in < 300ms under normal load
- Database queries optimized with indexes
- Response caching where appropriate
- Connection pooling enabled

#### Web Performance
- Next.js site targets 95+ Lighthouse Performance score
- Static page pre-rendering enabled
- Responsive image loading with next/image
- Minimal blocking CSS
- Code splitting implemented

#### Mobile Performance
- App startup time < 3 seconds
- Smooth 60fps animations
- Efficient state management
- Image optimization and caching

#### Desktop Performance
- Fast data loading with pagination
- Efficient chart rendering
- Quick search and filter operations
- Background task handling

### 10.3 Reliability & Scalability

#### Data Persistence
- Shopping cart persists locally on mobile app
- Order history stored in database
- Product catalog cached appropriately
- Token refresh mechanism

#### Database Integrity
- Foreign key constraints enforced
- Logical deletion for products (active flag)
- Transaction support for complex operations
- Regular database backups

#### Error Handling
- Graceful error messages to users
- Detailed error logging for developers
- Automatic retry for transient failures
- User-friendly error recovery

#### Cross-Platform Compatibility
- Mobile app supports Android and iOS
- Desktop app supports Windows 10+
- Web app supports modern browsers
- API compatible with MySQL and PostgreSQL

### 10.4 Usability & Accessibility

#### User Experience
- Intuitive navigation across all platforms
- Consistent design language
- Clear feedback for user actions
- Loading states for async operations

#### Accessibility
- Semantic HTML in web app
- Keyboard navigation support
- Screen reader compatibility
- Color contrast compliance

#### Internationalization
- Support for multiple languages (planned)
- Currency formatting (INR)
- Date/time localization
- Phone number formatting

### 10.5 Maintainability & Documentation

#### Code Quality
- Consistent code style
- Comprehensive comments
- Modular architecture
- DRY principles followed

#### Documentation
- API documentation kept up-to-date
- Code documentation inline
- Architecture diagrams maintained
- Deployment guides available

#### Testing
- Unit tests for business logic
- Integration tests for API endpoints
- End-to-end tests for critical flows
- Manual testing procedures documented

---

## 11. Deployment & Operations

### 11.1 Development Environment

#### Local Development Setup
- **Next.js**: `npm run dev` on port 3000
- **PHP Backend**: Laragon (Apache + MySQL/MariaDB)
- **Flutter Mobile**: Flutter emulator or physical device
- **Flutter Desktop**: Windows development machine

#### Database Setup
- Import `database.sql` to create schema
- Configure `includes/config.local.php` for local DB
- Test connection with `test_db.php`

### 11.2 Production Deployment

#### Next.js Marketing Site
- Static export build: `npm run build`
- Deploy to Vercel, Netlify, or any static host
- Configure custom domain
- Enable HTTPS

#### PHP Backend
- Deploy to Replit, Heroku, or VPS
- Configure PostgreSQL database
- Set environment variables for DB credentials
- Enable SSL/TLS
- Configure CORS for production domain

#### Flutter Mobile App
- Build APK for Android: `flutter build apk`
- Build IPA for iOS: `flutter build ios`
- Publish to Google Play Store
- Publish to Apple App Store
- Configure production API base URL

#### Flutter Desktop App
- Build Windows executable: `flutter build windows`
- Create installer (NSIS or Inno Setup)
- Distribute via download link or installer
- Configure production API base URL

### 11.3 Monitoring & Maintenance

#### Application Monitoring
- API response time monitoring
- Error rate tracking
- User activity analytics
- Performance metrics

#### Database Maintenance
- Regular backups
- Index optimization
- Query performance analysis
- Storage capacity monitoring

#### Security Maintenance
- Regular dependency updates
- Security patch application
- Token expiration monitoring
- Access log review

---

## 12. Appendices

### Appendix A: File Size Summary

**Next.js Marketing Site:**
- Total files: ~20
- Main components: 7
- Pages: 8
- Estimated size: ~5MB (with dependencies)

**PHP Backend:**
- Total files: ~40
- API endpoints: 22
- Admin pages: 4
- Photographer pages: 5
- Estimated size: ~2MB

**Flutter Mobile App:**
- Total files: ~60
- Screens: 8
- Models: 4
- Providers: 4
- Estimated APK size: ~15MB

**Flutter Desktop App:**
- Total files: ~45
- Screens: 9
- Models: 3
- Providers: 4
- Estimated EXE size: ~25MB

### Appendix B: Key Configuration Files

**Next.js Configuration:**
- `next.config.ts` - Static export, image optimization
- `tsconfig.json` - TypeScript configuration
- `tailwind.config.ts` - Tailwind CSS setup
- `package.json` - Dependencies and scripts

**PHP Configuration:**
- `includes/config.php` - Database configuration
- `includes/config.local.php` - Local overrides
- `api/.htaccess` - API routing

**Flutter Configuration:**
- `pubspec.yaml` - Dependencies and metadata
- `analysis_options.yaml` - Linting rules

### Appendix C: External Dependencies

**Next.js Dependencies:**
- next: 16.2.3
- react: 19.2.4
- react-dom: 19.2.4
- lucide-react: 1.8.0
- tailwindcss-animate: 1.0.7

**Flutter Mobile Dependencies:**
- http: 1.1.0
- provider: 6.0.5
- shared_preferences: 2.2.1
- google_fonts: 6.1.0
- shimmer: 3.0.0
- file_picker: 8.1.6

**Flutter Desktop Dependencies:**
- http: 1.1.0
- provider: 6.1.1
- shared_preferences: 2.2.2
- google_fonts: 6.1.0
- shimmer: 3.0.0
- fl_chart: 0.70.0
- intl: 0.19.0

---

## Document Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 2.0 | May 23, 2026 | Cascade | Comprehensive PRD covering all modules, complete project structure, detailed feature breakdown |
| 1.0 | Earlier | Original | Initial PRD document |

---

**End of Comprehensive Product Requirements Document**
