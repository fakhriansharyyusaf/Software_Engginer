# SEAPEDIA

<<<<<<< HEAD
SEAPEDIA is a multi-role marketplace built with Laravel 13, Livewire 4, and Blade. It supports Admin, Seller, Buyer, and Driver roles in one app.

## What is included

- Multi-role authentication and role switching
- Seller storefront and product management
- Buyer wallet, address, cart, checkout, and order history
- Driver delivery job flow
- Admin monitoring, vouchers, promos, and overdue handling
- REST API with Sanctum authentication

## Tech stack

- Backend: Laravel 13 (PHP)
- UI: Livewire 4 + Blade
- Architecture: Livewire for UI, service classes in app/Services for data access and business logic
- API: Laravel Sanctum
- Database: MySQL / SQLite / any Laravel-supported DB

## Quick start
=======
Multi-role marketplace (Admin, Seller, Buyer, Driver) built with Laravel 13 + Livewire 4.
Built for the COMPFEST 18 Software Engineering Academy technical challenge — Level 1 through Level 7.

## 1. Tech Stack

- **Backend/Framework:** Laravel 13 (PHP 8.5)
- **Frontend:** Livewire 4 (server-rendered, full-page components) + Blade
- **API:** Laravel Sanctum (Bearer token) — `routes/api.php`
- **Database:** MySQL (or any Laravel-supported DB; SQLite also works for local dev)

## 2. Setup
>>>>>>> 9b52be7943c7368f25a8508c5ac76ceeddfa2c94

```bash
composer install
cp .env.example .env
php artisan key:generate
<<<<<<< HEAD
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

## Demo accounts

Default password for all demo users: `password123`

| Username | Roles | Notes |
|---|---|---|
| `admin` | Admin | Monitoring, vouchers, promos, overdue handling |
| `seller1` | Seller | Owns a demo store and products |
| `buyer1` | Buyer | Has wallet balance and order history |
| `driver1` | Driver | Can take and complete delivery jobs |
| `multi1` | Buyer + Seller | Must choose an active role after login |

## Architecture

The project uses a simple layered structure:

- Livewire components handle the web UI and user interaction
- Service classes in app/Services handle data loading, calculations, and reusable business logic
- REST API routes in routes/api.php expose the same domain behavior for external clients

## API documentation

API docs are available in:

- docs/openapi.yaml
- docs/seapedia-postman-collection.json

You can also inspect the API routes with:

```bash
php artisan route:list --path=api
```

## Testing

```bash
php artisan test
```

## Notes

- Run `php artisan storage:link` after setup so uploaded product images work correctly.
- Sanctum tokens are used for API authentication.
- The app includes simulated time and overdue processing for demo purposes.
=======

# Make sure Sanctum is installed (skip if already in composer.json):
composer require laravel/sanctum

# Configure DB credentials in .env, then:
php artisan migrate
php artisan db:seed
php artisan storage:link
```

`php artisan storage:link` is required so product photos uploaded by Sellers (stored under `storage/app/public/products`) are reachable at `/storage/products/...` via `asset('storage/...')`.

`php artisan db:seed` runs, in order:
1. `RoleSeeder` — creates the 4 roles (Admin, Seller, Buyer, Driver).
2. `DemoSeeder` — creates demo accounts (see below), a demo store with products, and starting wallet balances.
3. `VoucherPromoSeeder` — creates sample Voucher/Promo codes (including one intentionally-expired voucher for testing).

Run the app:
```bash
php artisan serve
```

## 3. Demo Accounts

All passwords: `password123`

| Username  | Roles           | Notes                                   |
|-----------|-----------------|------------------------------------------|
| `admin`   | Admin           | Admin dashboard, monitoring, discounts, overdue trigger |
| `seller1` | Seller          | Has a store ("Toko Lari Cepat") with 3 demo products |
| `buyer1`  | Buyer           | Starting wallet balance Rp 5.000.000     |
| `driver1` | Driver          | No active job initially                  |
| `multi1`  | Buyer + Seller  | Must choose active role after login (has its own second store "TechGear ID" — useful to demo single-store checkout conflicts) |

There is no separate "admin setup" UI — the Admin account is created via `DemoSeeder`. To promote any other user to Admin manually:
```bash
php artisan tinker
>>> $u = App\Models\User::where('username', 'someuser')->first();
>>> $u->roles()->attach(App\Models\Role::where('name', 'Admin')->first());
```

## 4. Core Business Rules (as implemented)

### 4.1 Single-store checkout
One cart may only contain products from **one store** at a time (`carts.store_id`). If a Buyer tries to add a product from a different store, `CartService::addItem()` throws and the UI shows an option to clear the cart first. See `app/Services/CartService.php`.

### 4.2 Discount rule (Voucher vs Promo)
- A checkout may apply **either one Voucher OR one Promo — never both** (not combinable). This keeps the calculation predictable and is enforced by `DiscountService::validate()`, which looks up the code against Vouchers first, then Promos.
- Discount is calculated on the **subtotal**, before delivery fee and PPN.
- Vouchers have `expiry_date` **and** `usage_limit`/`used_count`. Promos only have `expiry_date` (unlimited use until expired).

### 4.3 PPN (tax) calculation
```
ppn   = 12% * (subtotal - discount)
total = subtotal - discount + delivery_fee + ppn
```
Delivery fee is **not** taxed. See `CheckoutService::PPN_RATE` / `checkout()`.

### 4.4 Delivery fee & SLA per method
| Method    | Fee        | SLA (from checkout time) |
|-----------|-----------|---------------------------|
| Instant   | Rp 20.000 | 3 hours                   |
| Next Day  | Rp 12.000 | 24 hours                  |
| Regular   | Rp 8.000  | 72 hours                  |

`sla_due_at` is stored on the order at checkout time (`CheckoutService::SLA_HOURS`).

### 4.5 Order lifecycle
```
Sedang Dikemas → Menunggu Pengirim → Sedang Dikirim → Pesanan Selesai
                                                       ↘ Dikembalikan (overdue)
```
Every transition is recorded in `order_status_histories` with a timestamp (`app/Models/OrderStatusHistory.php`).

### 4.6 Driver earning
A Driver earns **80% of the order's delivery_fee**, credited to their wallet when they confirm the job as completed (`OrderService::DRIVER_EARNING_RATE`, in `driverCompleteJob()`).

### 4.7 Seller income
Seller income (`subtotal - discount`) is credited to the Seller's wallet when the order reaches **"Pesanan Selesai"** (i.e., after Driver confirms delivery) — not at checkout time. This makes the overdue-reversal logic simple: most overdue orders never had income recorded in the first place.

### 4.8 Overdue auto-refund/return
`OverdueService::run()` (triggered by Admin button, `php artisan seapedia:check-overdue`, or the hourly scheduler entry in `routes/console.php`):
1. Finds orders still in `Sedang Dikemas` / `Menunggu Pengirim` / `Sedang Dikirim` whose `sla_due_at` has passed (using the **simulated clock**, see below).
2. Restores product stock for each order item.
3. Refunds `order.total` to the Buyer's wallet (recorded as a `refund` wallet transaction).
4. If Seller income was already recorded for that order, creates a `seller_income_reversal` transaction (negative) so income reports stay accurate.
5. Sets order status to `Dikembalikan` and stores `overdue_processed_at`.

**Idempotency:** every order is guarded by `overdue_processed_at` (only ever processed once) and by checking existing wallet transactions before creating reversals — running the check twice never double-refunds.

### 4.9 Time simulation
Because a real server clock can't be fast-forwarded, SEAPEDIA keeps a "simulated day offset" in the `settings` table (`app/Services/TimeService.php`). **All business logic that needs "now" (SLA calculation, overdue checks) uses `TimeService::now()`, never `now()`/`Carbon::now()` directly.**

To demo the overdue flow:
1. Login as Buyer, checkout an order (any delivery method).
2. Login as `admin`, go to **Admin → Waktu & Overdue**, click **"Simulasikan Hari Berikutnya"** a few times (or run `php artisan seapedia:simulate-next-day`) until you're past the SLA window.
3. Click **"Jalankan Pengecekan Overdue"** (or `php artisan seapedia:check-overdue`).
4. The order becomes `Dikembalikan`, the Buyer's wallet is refunded, and stock is restored — check the Buyer's wallet history and order detail to confirm.

## 5. Security Notes (Level 7)

- **SQL Injection:** all database access goes through Eloquent ORM / Laravel's query builder with bound parameters. No raw string-concatenated SQL is used anywhere in the codebase.
- **XSS:** all user-generated content (application reviews, product names/descriptions, addresses, etc.) is rendered with Blade's `{{ }}` syntax, which auto-escapes HTML. The codebase does not use `{!! !!}` anywhere for user input. `AppReview` (public reviews) is mass-assignment-protected to only `reviewer_name`, `rating`, `comment`.
- **Input validation:** every write path (Livewire `validate()` calls and API `Request::validate()` calls) validates required fields, types, and ranges (email format, numeric price/stock, rating 1–5, discount values ≥ 0, etc.) before touching the database.
- **Authorization / RBAC:**
  - Active role is persisted on `users.active_role` (not only in the web session), so the **same rule** protects both the Livewire web app (session-based) and the JSON API (Sanctum token-based, stateless).
  - Web routes are protected by the `active_role:{Role}` middleware (`app/Http/Middleware/CheckActiveRole.php`); API routes by the equivalent `api_role:{Role}` middleware (`CheckActiveRoleApi.php`).
  - Ownership is additionally enforced via Policies (`app/Policies/*`) and by scoping queries through the authenticated user's own relations (e.g., `$store->products()->findOrFail($id)`), so a Seller can never edit another Seller's product even if they guess the ID — both the authorization check and the query itself would fail.
  - Admin-only pages/endpoints require `active_role:Admin` — a Buyer/Seller/Driver token can never reach them, even by hitting the URL directly.
- **Session/token hardening:**
  - Logout (`Dashboard::logout()`) calls `Auth::logout()`, `session()->invalidate()`, and `session()->regenerateToken()`.
  - API logout deletes the specific Sanctum token used for that request (`$request->user()->currentAccessToken()->delete()`), not all of the user's tokens.
  - Login regenerates the session ID (`session()->regenerate()`) to prevent session fixation, and clears any stale `active_role` left over from a previous user on a shared browser.
  - Auth endpoints (`/api/auth/register`, `/api/auth/login`) are rate-limited (`throttle:10,1`).

### Suggested test cases (from the assignment brief)
- Submit `<script>alert(1)</script>` as an application review comment → it is stored as plain text and rendered harmlessly (visible as literal text, not executed) because of Blade auto-escaping.
- Submit `' OR '1'='1` in the login username/password fields → `Auth::attempt()` uses parameterized queries under the hood, so this is treated as a literal (failing) credential, not a SQL injection.
- Log in as `buyer1`, then try to `GET /api/seller/products` with the Buyer's token → `403 Forbidden` (wrong active role).
- Try to update a product that belongs to `multi1`'s store while authenticated as `seller1` → `403` from the `ProductPolicy`.

## 6. API Documentation

Base URL: `/api`. Authenticated routes require header `Authorization: Bearer {token}` (token returned by `/auth/login` or `/auth/register`).

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/auth/register` | — | Register (auto-assigned Buyer + Seller roles) |
| POST | `/auth/login` | — | Login, returns token |
| POST | `/auth/logout` | any | Revoke current token |
| GET  | `/auth/me` | any | Current user profile + roles + active role |
| POST | `/auth/active-role` | any | Body: `{ "role": "Buyer" }` |
| GET  | `/catalog/products` | — | Paginated public product list (`?search=`) |
| GET  | `/catalog/products/{id}` | — | Product detail + store |
| GET  | `/catalog/stores/{id}` | — | Store detail + its products |
| GET/POST | `/seller/store` | Seller | View/create/update own store |
| GET/POST | `/seller/products` | Seller | List / create own products |
| PUT/DELETE | `/seller/products/{id}` | Seller | Update / delete own product |
| GET | `/seller/orders` | Seller | Incoming orders for own store |
| POST | `/seller/orders/{id}/process` | Seller | Sedang Dikemas → Menunggu Pengirim |
| GET | `/buyer/wallet` | Buyer | Balance + transaction history |
| POST | `/buyer/wallet/topup` | Buyer | Body: `{ "amount": 100000 }` |
| GET/POST | `/buyer/addresses` | Buyer | List / create address |
| PUT/DELETE | `/buyer/addresses/{id}` | Buyer | Update / delete address |
| GET | `/buyer/cart` | Buyer | Current cart |
| POST | `/buyer/cart/items` | Buyer | Body: `{ "product_id", "quantity" }` |
| PUT | `/buyer/cart/items/{id}` | Buyer | Body: `{ "quantity" }` (0 = remove) |
| POST | `/buyer/cart/clear` | Buyer | Empty cart |
| POST | `/buyer/checkout` | Buyer | Body: `{ "delivery_method", "discount_code"? }` |
| GET | `/buyer/orders` | Buyer | Order history |
| GET | `/buyer/orders/{id}` | Buyer | Order detail + status history |
| GET | `/driver/jobs` | Driver | Available delivery jobs |
| GET | `/driver/jobs/{id}` | Driver | Job detail |
| POST | `/driver/jobs/{id}/take` | Driver | Take a job |
| POST | `/driver/jobs/{id}/complete` | Driver | Confirm completed |
| GET | `/driver/my-jobs` | Driver | Active + history + earnings |
| GET | `/admin/monitoring` | Admin | Marketplace-wide counters |
| GET/POST | `/admin/vouchers` | Admin | List / create vouchers |
| GET/POST | `/admin/promos` | Admin | List / create promos |
| POST | `/admin/time/simulate-next-day` | Admin | Advance simulated clock by 1 day |
| POST | `/admin/overdue/run` | Admin | Run overdue auto-refund/return now |

A Postman collection can be generated quickly from this table, or the routes can be introspected with `php artisan route:list --path=api`.

## 7. Demo Script (for judges / evaluators — everything is pre-seeded, no setup needed)

`DemoOrderSeeder` (runs automatically as part of `php artisan db:seed`) uses the real checkout/order/driver services to create **four orders in four different lifecycle stages** against `buyer1` + `seller1`'s store, so the marketplace looks alive the moment you log in — nothing needs to happen "live" first before you can show it off:

| Order | Status at seed time | What to show |
|---|---|---|
| #1 | **Pesanan Selesai** (fully completed) | Login `seller1` → Laporan tab → non-zero income. Login `driver1` → Riwayat & Earnings → non-zero earning. |
| #2 | **Menunggu Pengirim** | Login `driver1` → Job Tersedia → take it live, confirm delivered → watch status flip in real time. |
| #3 | **Sedang Dikemas** | Login `seller1` → Order Masuk → click "Proses Pesanan" live. |
| #4 | **Already past its SLA** (backdated) | Login `admin` → Waktu & Overdue → click "Jalankan Pengecekan Overdue" → watch it flip to "Dikembalikan" and `buyer1`'s wallet get refunded, live. |

Suggested walkthrough order:
1. **Guest:** `/` → `/katalog` → open a product — no login required.
2. **Register a brand-new account** → gets Buyer + Seller roles → forced to `/select-role` → shows the multi-role requirement is real, not just claimed.
3. **Login `admin`** → Monitoring tab (counts already non-zero) → Voucher/Promo tabs (click a code to copy it, click "Detail" to show the read-only detail view) → **Waktu & Overdue tab → run the overdue check on order #4 live.**
4. **Login `seller1`** → Produk tab (upload a photo on a product to show the upload feature) → Order Masuk tab → **process order #3 live** → Laporan tab (income from order #1 already there).
5. **Login `driver1`** → Job Tersedia → **take order #2's job live** → confirm delivered → Riwayat & Earnings (now two completed jobs, two payouts).
6. **Login `buyer1`** → Keranjang/Checkout a *new* order with voucher `SEAPEDIA10` to show the discount + PPN math working end-to-end → Riwayat Order to see all 5 orders (4 seeded + 1 just made) with full status timelines.
7. **Security spot-check:** submit `<script>alert(1)</script>` in the public review form on `/` and show it renders as inert plain text, not executed.

## 8. Full Feature Test Guide (for a slower, from-scratch walkthrough)

1. **Guest:** visit `/`, browse `/katalog`, open a product detail page — no login required.
2. **Register & role selection:** register a new account (gets Buyer + Seller roles) → redirected to `/select-role` → pick a role → lands on `/dashboard`.
3. **Seller flow:** switch to Seller → `/seller` → create store (unique name enforced) → add products (with a photo).
4. **Buyer flow:** switch to Buyer (or login as `buyer1`) → top up wallet → add address → browse catalog → add product to cart → checkout with a delivery method and optionally `SEAPEDIA10` or `HEMAT20K` → see the order in "Riwayat Order".
5. **Seller processes the order:** login as the Seller who owns that store → `/seller` → Orders tab → "Proses Pesanan".
6. **Driver flow:** login as `driver1` → `/driver` → take the now-available job → confirm completed → check wallet for the earning.
7. **Admin flow:** login as `admin` → `/admin` → Monitoring tab (see counts) → Voucher/Promo tabs (create new codes) → Waktu & Overdue tab (simulate days forward, run overdue check on an unprocessed order to see the auto-refund).
8. **Security spot-check:** try submitting `<script>alert(1)</script>` in the public review form on `/` and confirm it renders as plain text.

## 8. UX Layer (Reusable Components & Polish)

To close the "reusable UI foundations" and "creative, intuitive UI" scoring criteria, a shared UX layer lives in `resources/views/components/ui/` and is wired into **one** place — `resources/views/layouts/app.blade.php` — so every Livewire full-page component gets it automatically without per-page boilerplate:

- **`<x-ui.navbar>`** — one navbar for the whole app. Adapts to guest vs. logged-in state, shows the active role + a one-click role switcher (for multi-role accounts), a live wallet balance chip, and collapses into a mobile hamburger menu. Role-switch and logout are implemented as plain POST routes (`SessionController`) rather than Livewire methods, specifically so the same navbar works correctly no matter which Livewire component is currently mounted.
- **`<x-ui.footer>`** — one footer, everywhere.
- **`<x-ui.flash>`** — a generic toast system. It introspects `session()->all()` for any key ending in `_message` (or `message`/`error`) and renders it as an auto-dismissing toast (Alpine.js) — every existing Livewire component's `session()->flash(...)` calls "just work" with zero extra wiring.
- **`<x-ui.confirm-modal>`** — a reusable Alpine-driven confirm dialog that replaces the browser's native `confirm()` for the most consequential actions (deleting a product, clearing the cart, deleting an address), giving a branded, on-page confirmation instead of a jarring native popup. Usage pattern: `x-on:click="$store.confirmModal.open('message', () => $wire.someMethod())"`.
- **`<x-ui.status-badge :status="$order->status">`** — consistent color-coded order status pill (amber/blue/indigo/green/red) reused across Buyer, Seller, and Admin views instead of every page inventing its own status styling.
- **`<x-ui.empty-state>`** — consistent "nothing here yet" placeholder.
- Loading states: primary actions that hit the network (checkout, add to cart, process order, take/complete delivery job) now disable themselves and swap their label via `wire:loading`/`wire:target` while the request is in flight, instead of looking unresponsive.
- Click-to-copy voucher/promo codes in the Admin panel (Alpine + Clipboard API).
- A custom branded 404 page (`resources/views/errors/404.blade.php`) and a data-URI favicon replace Laravel's defaults.

## 9. Known Simplifications / Notes

- Product photo upload is supported (Seller dashboard, stored via `Storage::disk('public')`) — run `php artisan storage:link` after migrating, or images will 404.
- Styling uses the Tailwind CDN script (`cdn.tailwindcss.com`) for simplicity. `resources/css/app.css` and `resources/js/app.js` are prepared for a proper Vite build but are not currently wired into `layouts/app.blade.php` — harmless for local demo, but the CDN script is not recommended for production and should be swapped for a compiled Vite build before deploying.
- Sanctum tokens do not expire by default in this setup (fine for a demo/evaluation environment). For production, set `'expiration' => <minutes>` in `config/sanctum.php` after publishing it.
- Deployment is not included in this deliverable; see the assignment's optional 15-pt deployment bonus.
>>>>>>> 9b52be7943c7368f25a8508c5ac76ceeddfa2c94
