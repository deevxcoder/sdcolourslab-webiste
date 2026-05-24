# SD Colours Photobook Lab - Design Reference

This document serves as a design reference to reproduce the Next.js frontend design in the normal B2B HTML/CSS/JS PHP frontend.

---

## 1. Design System & Global Styles

### Theme Colors (HSL / Hex)
* **Background**: `#ffffff` (Pure White)
* **Foreground**: `#09090b` (Charcoal)
* **Primary (Premium Gold)**: `#cca353` (used for CTAs, active states, icons)
* **Primary Dark (Hover Gold)**: `#b58c42` (used for hover/focus on gold elements)
* **Secondary (Deep Black)**: `#171717` (used for dark sections, header backgrounds, footers)
* **Secondary Foreground**: `#ffffff`
* **Muted**: `#f4f4f5` (Light grey)
* **Muted Foreground**: `#71717a` (Medium grey)
* **Accent (Light Gold-Tinted White)**: `#f8f4eb` (subtle bg card frames and highlights)
* **Border**: `#e4e4e7` (Light line borders)

### Typography
* **Sans-serif Font**: `Inter` (Fallback: `ui-sans-serif, system-ui, sans-serif`) - used for body copy, controls, lists.
* **Serif Font**: `Playfair Display` (Fallback: `ui-serif, Georgia, serif`) - used for headers, page hero titles, category titles.

### Special Effects & Utility Classes (Tailwind & CSS)
* **Text Gradient** (`.text-gradient`):
  ```css
  background: linear-gradient(to right, #916c27, #cca353, #ebd494, #cca353);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  ```
* **Glassmorphism** (`.glass`):
  ```css
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  ```
* **Dark Glassmorphism** (`.glass-dark`):
  ```css
  background: rgba(23, 23, 23, 0.85);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  ```
* **Smooth Scroll**: `html { scroll-behavior: smooth; }`

---

## 2. Reusable Layouts & Components

### Global Header (`Header.tsx`)
* **State Behavior**:
  * Default state: `bg-secondary` (Deep Black), height padding `py-4`.
  * Scrolled state (`window.scrollY > 20`): `glass-dark` background, shadow-sm, height padding `py-2`.
* **Left**: Brand logo `/logo.png` (height: `h-12` on mobile, `h-20` on desktop, auto width).
* **Center (Navigation links)**:
  * Links: Home (`/`), Products (`/products`), Pricing (`/pricing`), Gallery (`/gallery`), About (`/about`), Contact (`/contact`).
  * Inactive style: `text-white/90 font-semibold hover:text-primary`.
  * Active style: `text-primary border-b-2 border-primary`.
* **Right**:
  * **Photographer Portal Dropdown**:
    * Styled button: `border border-primary/40 bg-white/5 backdrop-blur-md px-4 py-2 rounded-full hover:bg-primary/15 hover:border-primary`.
    * Options: "Register (Sign Up)" (`/register`) and "Portal Login" (resolves to `/login.php`).
  * **Order Now CTA**:
    * Styled button: `bg-primary text-primary-foreground px-5 py-2.5 rounded-full hover:bg-primary-dark`. Links to WhatsApp (`https://wa.me/918895838987?text=...`).
* **Mobile Drawer**:
  * Sliding drawer overlay (`bg-black/20 backdrop-blur-sm`).
  * Drawer panel: `bg-white px-6 py-6`. Uses dark text matching foreground.
  * Portal links displayed as vertical accordion list.

### Global Footer (`Footer.tsx`)
* **Background**: `bg-secondary` (Deep Black), top border `border-t border-white/10`.
* **Grid Layout** (3 Columns):
  * **Column 1**: Brand logo, short paragraph tagline, social links placeholder.
  * **Column 2 (Grid inside column)**:
    * *Solutions*: Wedding Albums, Combo Photo Pads, LED Frames, Wall Canvas.
    * *Company*: About Us, Gallery, Pricing, Contact.
  * **Column 3**: Contact Us (Phone numbers, Email, Street Address with icons).
* **Copyright Bar**: Bottom row showing `© [Year] SD Colours Photobook Lab. All rights reserved.`.

### Product Card Component (`ProductCard.tsx`)
* **Outer Structure**: `rounded-2xl bg-white shadow-md border border-border transition-all hover:shadow-xl overflow-hidden`.
* **Upper half (Image wrapper)**:
  * Ratio: `aspect-[4/3]`.
  * Tag badge (top-right absolute): `rounded-full px-3 py-1 text-xs font-semibold uppercase`. Background color depends on tag (e.g. `bg-secondary` for "Premium", `bg-primary` for "Best Seller").
  * Image: fills the space, zooms slightly on hover (`hover:scale-105 duration-500`).
* **Lower half (Content)**:
  * Title: `font-serif text-xl font-semibold text-secondary`.
  * Price: `text-2xl font-bold text-primary` (e.g. `₹1550`).
  * Available Sizes: badge pill list in `bg-accent text-secondary text-xs ring-1 ring-border`.
  * Feature list: Checkmark icons (`text-primary`) next to features.
  * CTA Button: `block w-full bg-secondary hover:bg-black text-white text-center text-sm font-semibold rounded-md py-2`.

---

## 3. Page Layouts & Details

### Home Page (`page.tsx`)
1. **Hero Section**:
   * Minimum height: `min-h-[90vh]`.
   * Background image: Unsplash cover, `opacity-20 bg-cover bg-center`.
   * Overlay: `bg-gradient-to-r from-secondary via-secondary/90 to-transparent`.
   * Content:
     * Title: `Creativity Photobook Company in India` (utilizing text gradient on first words, font-serif, `sm:text-6xl`).
     * Subtext: `Your Fast & Professional Printing Partner...`.
     * Buttons: WhatsApp CTA (`bg-primary`), Price List outline button (`border border-white/40`), Call button (`border border-primary/60 bg-white/5`).
     * Right Side Monogram Image: displayed on desktop (`hidden lg:flex`), `/monogram.png` drop-shadow, brightness-200.
2. **Trust badging section**:
   * Layout: 3 cards.
   * Styling: `bg-accent border border-border p-6 rounded-2xl flex items-center gap-4`.
   * Badges: Shipping All Over India (Truck), High Quality Printing (CheckCircle), Premium Wedding Albums (ShieldCheck).
3. **Core Offerings (Categories)**:
   * Header: `Our Core Offerings` with subtext.
   * Buttons: 5 circular cards (`rounded-full bg-secondary border-4 border-white shadow-xl hover:scale-105 hover:border-primary`). Includes central primary gold icon.
4. **Featured Collections**:
   * Title and link to products. Shows 6 featured `ProductCard` components.
5. **CTA Section**:
   * Background: `bg-secondary`, Unsplash background overlay `opacity-10`.
   * Large headline: `Start Your Wedding Album Today` with a giant button leading to WhatsApp.

### Products Page (`products/page.tsx`)
* **Page Intro**: Large typography title (`Our Products` using text-gradient) over a light bg.
* **Layout**: 4 sections separated by a gold border header (`border-b-2 border-primary w-24 mb-6`).
  * **A. Photo Album Printing**: 4 cards highlighting sizes and paper options.
  * **B. Combo Photo Pad Products**: Grid of 9 cards.
  * **C. LED Frames**: 2 colored box layouts highlighting 12x18 (₹750) and 8x12 (₹370).
  * **D. Wall Acrylic & Canvas**: A clean data table (`bg-white border-b border-border`) detailing sizes (5x7 to 24x36) and prices.

### Pricing & Interactive Calculator Page (`pricing/page.tsx`)
* **Download Section**:
  * 2 large download cards for `price_list.pdf` and `combo_price_list.pdf` with gold icons, descriptions, and download tags.
* **Cost Estimator Tool**:
  * Interactive box: `bg-white/[0.02] border border-white/10 p-8 rounded-3xl shadow-2xl`.
  * Tabs: Wedding Albums, Wall Acrylics, LED Frames, Premium Combo Pads.
  * Left Column (Config Controls): selects category, size, paper type, page count, and quantity with plus/minus increment buttons.
  * Right Column (Summary Box):
    * Real-time calculation feedback.
    * Estimated Net Price card (`text-gradient` price text).
    * Custom WhatsApp Link compiler: Compiles a customized text template detailing the user's specific selections.
      * Example: `https://wa.me/918895838987?text=Hi%20SD%20Colours%20Lab!%20I'd%20like%20to%20place%20an%20order...`

### About Page (`about/page.tsx`)
* **Structure**: Two-column layout grid.
  * **Left Column**: Headline (`Most Rated Wedding Album Printing Press`), details text, checklist items.
  * **Right Column**: Aspect `4/3` box containing a centered monogram logo with white/5 borders.
* **Visit Our Lab Banner**: Centered bottom section with `bg-secondary` box.

### Contact Page (`contact/page.tsx`)
* **Cards Grid**: 4 columns (Rourkela HQ phone desk, Bhubaneswar phone desk, Email Desk, Lab Hours).
* **Inquiry Form (Left)**: Input elements with custom active borders (`focus:border-primary ring-1 ring-primary`). Redirects to WhatsApp text compilation upon submission.
* **Interactive Map Hub (Right)**:
  * Google Map embedded iframe. Styling is grayscaled and high contrast:
    * Class: `grayscale contrast-125 opacity-70 hover:grayscale-0 hover:opacity-100 transition-all duration-700`.
  * Includes address, timbers, instructions, and navigation trigger.
* **Fullscreen Map Modal**: Triggers overlay layout when clicking search icon.

### Register Page (`register/page.tsx`)
* **Layout**: Full-screen dark container (`bg-[#0d0d0f]`).
* **Form Inputs**: Glassmorphic dark card (`glass-dark border border-primary/30`).
  * Icon inputs (User, Mail, Phone, Lock, Building, MapPin).
  * Password toggle visibility eye-icons.
* **Submit States**: Includes a spinning loader (`Loader2 animate-spin`) during submission.
* **Success View**: Reveals application status checklist banner and custom WhatsApp button.

---

## 4. B2B Pricing Calculation Logic

### 1. Albums (Regular, Special, Metallic, Mini, Emboss)
$$\text{Price} = \text{Paper Rate} \times \text{Number of Pages (Sides)} \times \text{Quantity}$$
* *Regular Album*:
  * Regular Glossy: ₹38/page
  * Regular Heavy Glossy: ₹46/page
  * Regular Matt: ₹51/page
  * Regular Heavy Matt: ₹61/page
* *Special Album*:
  * NTR Glossy Slim: ₹52/page
  * NTR Heavy Glossy: ₹62/page
  * NTR Matt Slim: ₹62/page
  * NTR Heavy Matt: ₹66/page
  * Luster: ₹70/page
* *Metallic Album*:
  * Regular Velvet Sheet: ₹60/page
  * NTR Velvet Sheet: ₹72/page
  * Transparent Sheet: ₹90/page
  * Silky Metallic: ₹90/page
  * Ultra Metallic: ₹90/page
  * Sparkle: ₹90/page
  * Pearl Metallic: ₹110/page
  * 3D: ₹110/page
* *Mini Book*:
  * Regular Glossy: ₹28/page | Regular Matt: ₹30/page | NTR Glossy: ₹38/page | NTR Matt: ₹40/page
* *Emboss / Foil*:
  * Emboss Simplex 12x18: ₹190/page | Emboss + Gold Foil 12x18: ₹250/page

### 2. Wall Acrylics
$$\text{Price} = \text{Base Price} \times \text{Quantity}$$
* Sizes & Rates:
  * 5x7: ₹350 | 6x8: ₹500 | 8x12: ₹650 | 12x18: ₹750 | 16x20: ₹1550 | 20x24: ₹2250 | 20x30: ₹2750 | 24x36: ₹3150

### 3. LED Frames (Tiered Pricing)
$$\text{Price} = \text{Volume Rate} \times \text{Quantity}$$
* Volume Rates:
  * **6x8**: Qty 1-14: ₹380 | Qty 15-24: ₹295 | Qty 25-49: ₹230 | Qty 50+: ₹190
  * **8x12**: Qty 1-14: ₹412 | Qty 15-24: ₹380 | Qty 25-49: ₹360 | Qty 50+: ₹310
  * **12x18**: Qty 1-14: ₹570 | Qty 15-24: ₹530 | Qty 25-49: ₹480 | Qty 50+: ₹452
  * **12x36**: Qty 1-14: ₹1050 | Qty 15-24: ₹1015 | Qty 25-49: ₹895 | Qty 50+: ₹750
  * **16x20**: Qty 1-14: ₹1115 | Qty 15-24: ₹1052 | Qty 25-49: ₹1010 | Qty 50+: ₹923
  * **18x24**: Qty 1-14: ₹1290 | Qty 15-24: ₹1210 | Qty 25-49: ₹1170 | Qty 50+: ₹1050
  * **24x36**: Qty 1-14: ₹1910 | Qty 15-24: ₹1830 | Qty 25-49: ₹1650 | Qty 50+: ₹1540

### 4. Premium Combo Pads
$$\text{Price} = \text{Base Price} \times \text{Quantity}$$
* Packages:
  * **Leather Combo**: ₹1550 (Leather 2 IN 1 - Cover Leather Pad, Photo Bag, LED Frame, Calendar)
  * **Acrylic Combo**: ₹1250 (Acrylic 2 IN 1 - Leather Cover Pad, Full Acrylic Layout)
  * **Wooden Combo**: ₹1850 (LAWood 4 IN 1 - Wooden Cover, Leather Bag, LED Frame, Calendar)
  * **Royal Combo**: ₹2250 (Royal 4 IN 1 - Leather Cover, Leather Bag & Box, LED Frame)
  * **Superior Silver (3 in 1)**: ₹1750 (Acrylic Cover pad, Leather Bag, Calendar)
  * **Superior Silver (4 in 1)**: ₹2100 (Wooden + Acrylic cover pad, Leather Bag, LED Frame, Calendar)
  * **Superior Gold+ (6 in 1)**: ₹2550 (Leather Cover, Briefcase, Bag, LED, Calendar, Mini Book)
  * **Superior Platinum (6 in 1)**: ₹3150 (Premium Leather, Briefcase, Bag, LED, Calendar, Mini Book)
  * **Inluxury Combo**: ₹4100 (Proluxury 5 in 1 - Square Briefcase, Window Acrylic Cover, Bag)
  * **Leatherism Combo**: ₹4500 (Furio 7 in 1 - Double Door, 2 Cover Pads, Briefcase, Bag, Stand, USB Box, Calendar)
