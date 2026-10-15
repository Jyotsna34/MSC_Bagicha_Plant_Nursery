# Project Memory

# Project

Bagicha

---

# Current Status

Complete - Production Ready (Security Vulnerabilities, Business Logic, Payment Signature, and Inventory Integrity Resolved)

---

# Completed

✓ **New Botanical Pages, Interactive Features & System-Wide UI/UX Enhancements**

- **Contact Us & Support Hub ([`pages/contact.php`](file:///c:/xampp/htdocs/bagicha/pages/contact.php)):** Direct WhatsApp Botanist hotline, nursery visiting hours/location map, and interactive categorized inquiry submission desk.
- **Public Live Order Tracking Portal ([`pages/track-order.php`](file:///c:/xampp/htdocs/bagicha/pages/track-order.php)):** Instant tracking by Order Number (`BAG-...`) with 5-stage milestone transit timeline and package contents preview.
- **7-Day Guarantee & Shipping Policy ([`pages/guarantee.php`](file:///c:/xampp/htdocs/bagicha/pages/guarantee.php)):** Arrive-Alive guarantee terms, 3-step claim procedure, and Eco-Shield packaging engineering details.
- **Live Search Autocomplete with Thumbnails ([`api/products/search.php`](file:///c:/xampp/htdocs/bagicha/api/products/search.php), [`navbar.php`](file:///c:/xampp/htdocs/bagicha/includes/navbar.php), [`app.js`](file:///c:/xampp/htdocs/bagicha/assets/js/app.js)):** Debounced instant search dropdown with thumbnails, prices, and `⌘K` / `Ctrl+K` keyboard shortcut.
- **Smart Botanical Badge Filter Pills ([`pages/products.php`](file:///c:/xampp/htdocs/bagicha/pages/products.php)):** Quick filters for Pet-Safe, Low-Light Survivors, Easy Care, and Succulents.
- **Pincode Delivery Estimator & Bundle & Save ([`pages/product-detail.php`](file:///c:/xampp/htdocs/bagicha/pages/product-detail.php), [`assets/js/product-detail.js`](file:///c:/xampp/htdocs/bagicha/assets/js/product-detail.js)):** Pincode check widget, "Frequently Potted Together" bundle discount, and mobile bottom buy bar.
- **Floating WhatsApp Botanist Consultation Bubble ([`includes/footer.php`](file:///c:/xampp/htdocs/bagicha/includes/footer.php), [`assets/css/features.css`](file:///c:/xampp/htdocs/bagicha/assets/css/features.css)):** Unobtrusive floating green chat bubble.
- **Admin Support Inquiries Management ([`admin/messages.php`](file:///c:/xampp/htdocs/bagicha/admin/messages.php), [`admin/includes/sidebar.php`](file:///c:/xampp/htdocs/bagicha/admin/includes/sidebar.php)):** View, filter by category/status, mark as replied, reply via mailto, and delete customer messages.
- **Database & Backend APIs ([`api/contact/index.php`](file:///c:/xampp/htdocs/bagicha/api/contact/index.php), [`api/orders/track.php`](file:///c:/xampp/htdocs/bagicha/api/orders/track.php), `contact_messages` table):** Fully prepared statements, input validation, and admin notifications.

✓ **Admin Collapsible Sidebar & Universal Show/Hide Password UI**

- **Collapsible/Expandable Admin Sidebar & Secure Logout ([`admin/includes/sidebar.php`](file:///c:/xampp/htdocs/bagicha/admin/includes/sidebar.php), [`admin/assets/css/admin-sidebar.css`](file:///c:/xampp/htdocs/bagicha/admin/assets/css/admin-sidebar.css), [`admin/assets/js/admin-ui.js`](file:///c:/xampp/htdocs/bagicha/admin/assets/js/admin-ui.js)):**
  - Smooth desktop toggle button (`#sidebarCollapseBtn`) collapsing sidebar from 260px to 76px compact icon view.
  - Interactive tooltip support when collapsed via Bootstrap Tooltips.
  - Keyboard shortcut `Ctrl+B` / `Cmd+B` to toggle sidebar anytime.
  - State persistence via `localStorage.getItem('bagicha_admin_sidebar_collapsed')` with flash-prevention script in `header.php`.
  - Mobile & tablet off-canvas sliding drawer (< 992px) with sticky top header bar (`.admin-mobile-topbar`), hamburger toggle button (`#sidebarMobileOpenBtn`), and backdrop overlay (`.admin-sidebar-backdrop`).
  - **Prominent Admin Logout Controls**: Clearly visible red outline Logout button (`.btn-admin-logout`) in the desktop sidebar footer (icon-only in collapsed view) and topbar quick-logout icon on mobile.
  - **Secure Server Session Invalidation ([`api/auth/logout.php`](file:///c:/xampp/htdocs/bagicha/api/auth/logout.php))**: Invalidates PHP session, deletes cookies (`bagicha_token`), clears `localStorage`, and securely redirects to [`pages/login.php?logged_out=1`](file:///c:/xampp/htdocs/bagicha/pages/login.php) with a confirmation alert.
- **Universal Show/Hide Password Feature Across Entire System ([`assets/js/password-toggle.js`](file:///c:/xampp/htdocs/bagicha/assets/js/password-toggle.js), [`admin/assets/css/admin-sidebar.css`](file:///c:/xampp/htdocs/bagicha/admin/assets/css/admin-sidebar.css), [`assets/css/features.css`](file:///c:/xampp/htdocs/bagicha/assets/css/features.css)):**
  - Dedicated modular utility (`password-toggle.js`) automatically decorating every password field (`input[type="password"]`) across the whole system: Customer Login, Customer Registration, Forgot/Reset Password, Customer Profile Security, and Admin Website Settings.
  - Seamlessly toggles field between masked password (`••••••••`) and plain text, swapping between `bi-eye` and `bi-eye-slash` with accessible ARIA labels.
  - Automatically hooks into `shown.bs.modal` for all existing and dynamically loaded Bootstrap modals.
- **Customer & Admin Password Management ([`admin/settings.php`](file:///c:/xampp/htdocs/bagicha/admin/settings.php), [`includes/change-password-modal.php`](file:///c:/xampp/htdocs/bagicha/includes/change-password-modal.php), [`pages/profile.php`](file:///c:/xampp/htdocs/bagicha/pages/profile.php), [`api/admin/change-password.php`](file:///c:/xampp/htdocs/bagicha/api/admin/change-password.php), [`api/auth/change-password.php`](file:///c:/xampp/htdocs/bagicha/api/auth/change-password.php)):**
  - Added dedicated Security Card to Admin Website Settings and modal to Customer Garden Profile.
  - Secure backend endpoints verifying current password with `password_verify` and updating hash with `password_hash`.

✓ **Unified Home & About Us Single-Page Architecture with Smooth Scrolling**

- Combined Home and About Us into a unified, seamless single-page experience on [`index.php`](file:///c:/xampp/htdocs/bagicha/index.php) using a dedicated, modular section component [`includes/about-section.php`](file:///c:/xampp/htdocs/bagicha/includes/about-section.php) (respecting the strict 300-line limit per PHP file in [`rules.md`](file:///c:/xampp/htdocs/bagicha/rules.md)).
- Configured 301 redirection in [`pages/about.php`](file:///c:/xampp/htdocs/bagicha/pages/about.php) to `index.php#about`, ensuring existing bookmarks and direct URL requests seamlessly land on the About section.
- Updated navigation links in [`includes/navbar.php`](file:///c:/xampp/htdocs/bagicha/includes/navbar.php) and [`includes/footer.php`](file:///c:/xampp/htdocs/bagicha/includes/footer.php) to target `index.php#about`.
- Added sticky navbar clearance with `scroll-padding-top: 85px` on `html` and `scroll-margin-top: 85px` on `#about` in [`assets/css/about.css`](file:///c:/xampp/htdocs/bagicha/assets/css/about.css).
- Enhanced [`assets/js/app.js`](file:///c:/xampp/htdocs/bagicha/assets/js/app.js) with smooth internal hash scrolling and dynamic scrollspy highlighting on the navbar (toggling `active` between "Home" and "About Us" as the user scrolls past sections).

✓ **Home & About Us Full Scrollability & Back-to-Top Navigation**

- Swapped `overflow-x: hidden` for modern standard `overflow-x: clip` on `html` and `body` in [`assets/css/variables.css`](file:///c:/xampp/htdocs/bagicha/assets/css/variables.css), eliminating horizontal scroll-trapping while maintaining strict boundary clipping.
- Added `min-height: 100%`, `overflow-y: auto`, `overscroll-behavior-y: auto`, and safety guard `body:not(.modal-open):not(.offcanvas-open) { overflow-y: auto !important; }` to prevent modal/offcanvas backdrop scroll locks.
- Enhanced horizontal scroll tracks (`.horizontal-scroll-track`) in [`assets/css/style.css`](file:///c:/xampp/htdocs/bagicha/assets/css/style.css) with `touch-action: pan-y`, `overscroll-behavior-x: contain`, and `overscroll-behavior-y: auto` so vertical mouse-wheel and swipe actions naturally propagate without jamming.
- Implemented floating Biophilic Back-to-Top button in [`includes/footer.php`](file:///c:/xampp/htdocs/bagicha/includes/footer.php) and scroll trigger in [`assets/js/app.js`](file:///c:/xampp/htdocs/bagicha/assets/js/app.js).

✓ **About Us Page Design & Content Overhaul**

- Redesigned [`pages/about.php`](file:///c:/xampp/htdocs/bagicha/pages/about.php) (under 300-line strict limit, semantic HTML5, zero inline styling).
- Added modular styles in [`assets/css/about.css`](file:///c:/xampp/htdocs/bagicha/assets/css/about.css) (under 250-line limit) imported via [`assets/css/style.css`](file:///c:/xampp/htdocs/bagicha/assets/css/style.css).
- Features 8 biophilic sections:
  1. Biophilic Hero Showcase with trust badge and high-resolution imagery.
  2. Nursery Highlights & Impact Counter (50,000+ plants shipped, 99.4% transit rate, 7-day guarantee).
  3. Origin Story & Timeline (from 400 sq.ft balcony greenhouse to 180+ cities).
  4. Core Values: Mission & Vision cards.
  5. 4-Step Botanical Care Cycle (Hardened Acclimatization, Organic Living Soil, Eco-Shield Transit Pods, Lifetime Botanist Support).
  6. The Bagicha Difference: Visual Comparison Table (Bagicha vs Traditional Street Nurseries).
  7. Meet Our Lead Botanists (Senior Horticulturist, Soil Scientist, Packaging Engineer).
  8. Interactive FAQ Accordion and Inspiring Botanical CTA Banner.

# Completed

✓ **Testimonial Carousel Nav Button Alignment & Avatar Fix**

- Replaced failing external image tags in [`assets/js/reviews.js`](file:///c:/xampp/htdocs/Bagicha/assets/js/reviews.js) with bulletproof biophilic gradient initial badges (`linear-gradient(...)`), eliminating broken image icons and text overflow.
- Adjusted testimonial section wrapper padding (`px-4 px-md-5`) in [`index.php`](file:///c:/xampp/htdocs/Bagicha/index.php) and button positioning in [`assets/css/style.css`](file:///c:/xampp/htdocs/Bagicha/assets/css/style.css), ensuring `<` and `>` arrows sit comfortably outside cards without overlapping.

# Completed

✓ **Admin Panel Data Loading & Axios BaseURL Path Resolution**

- Updated `getApiBaseUrl()` in [app.js](file:///c:/xampp/htdocs/Bagicha/assets/js/app.js) and [admin.js](file:///c:/xampp/htdocs/Bagicha/admin/assets/js/admin.js) to dynamically calculate base API paths across subfolders (`/Bagicha/api`).
- Cleared `axios.defaults.baseURL` to prevent Axios 1.x prepending bugs on leading-slash requests (`/admin/products/index.php`), resolving the 404 infinite loading spinner across all admin pages.
- Added empty data checks and clean table fallback rendering for all admin tables.

✓ **Theme Switcher & Theme Consistency Across Application**

- Standardized `[data-bs-theme]` application across `document.documentElement` and `document.body` in [header.php](file:///c:/xampp/htdocs/Bagicha/admin/includes/header.php) and [admin.js](file:///c:/xampp/htdocs/Bagicha/admin/assets/js/admin.js).
- Added CSS rule overrides in [admin.css](file:///c:/xampp/htdocs/Bagicha/admin/assets/css/admin.css) to enforce theme-aware backgrounds (`--admin-bg-card`) over Bootstrap `!important` classes (`.bg-white`, `.table-light`, `.text-dark`).
- Updated all admin HTML page templates (`products.php`, `categories.php`, `orders.php`, `customers.php`, `coupons.php`, `reports.php`, `reviews.php`) to use responsive `admin-card` and `table-admin-custom` elements.

---

# Completed

✓ **Single Sidebar Theme Toggle Button for Overall Admin Dashboard**

- Toggle button situated at the bottom of the Admin Sidebar ([sidebar.php](file:///c:/xampp/htdocs/Bagicha/admin/includes/sidebar.php)).
- Single click toggles overall admin dashboard theme between **Light Mode** (`#F1F5F9` body, `#FFFFFF` cards) and **Dark Mode** (`#0F172A` body, `#1E293B` cards).
- Updates both `documentElement` and `body` `data-bs-theme` attributes, dynamically switches icon (`bi-sun-fill` / `bi-moon-stars-fill`) and button text (`Light Mode` / `Dark Mode`), and saves choice in `localStorage`.
- Automatically updates Chart.js grid line contrast when toggled.

✓ **Admin Dashboard UI/UX Redesign**

- Sleek administrative executive theme with Light & Dark Mode support.
- Interactive Chart.js analytics for Revenue Trends & Category Share.
- Preserved user logo (`<img src="<?= BASE_URL ?>/img/logo-3.png" ...>`) in `sidebar.php` and `navbar.php`.
- Modern metric cards with growth badges (`+14.2% mo/mo`) & table theme overrides.

✓ **Role-Based Access Control (RBAC)**

- Server-side `requireAdminPage()` PHP guard protecting `/admin/*.php` pages.
- Role-based login redirection: Admins redirect to `/admin/index.php`, Customers to `/index.php`.
- Dynamic navbar dropdown item displaying "Admin Dashboard" for Admin accounts.
- Server-side `requireAdmin()` middleware protecting `/api/admin/*` endpoints.

---

# Current Tech Stack

Frontend: HTML5, CSS3, JavaScript (ES6+), Bootstrap 5, Chart.js, Axios, Razorpay SDK, Google Fonts (Outfit & Source Sans 3)
Backend: PHP 8+, PDO (Prepared Statements), REST API JSON, Native JWT Auth, Role-Based Access Control (`requireAdmin()`, `requireAdminPage()`)
Database: MySQL (`bagicha_db`)
Server Environment: XAMPP (Apache + MySQL)
