# SEAPEDIA

Multi-role marketplace (Admin, Seller, Buyer, Driver) built with Laravel 13 + Livewire 4.
Built for the COMPFEST 18 Software Engineering Academy technical challenge — Level 1 through Level 7.

## 1. Tech Stack

- **Backend/Framework:** Laravel 13 (PHP 8.5)
- **Frontend:** Livewire 4 (server-rendered, full-page components) + Blade
- **API:** Laravel Sanctum (Bearer token) — `routes/api.php`
- **Database:** MySQL (or any Laravel-supported DB; SQLite also works for local dev)

## 2. Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

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

## 7. Testing Guide (end-to-end demo flow)

1. **Guest:** visit `/`, browse `/katalog`, open a product detail page — no login required.
2. **Register & role selection:** register a new account (gets Buyer + Seller roles) → redirected to `/select-role` → pick a role → lands on `/dashboard`.
3. **Seller flow:** switch to Seller → `/seller` → create store (unique name enforced) → add products.
4. **Buyer flow:** switch to Buyer (or login as `buyer1`) → top up wallet → add address → browse catalog → add product to cart → checkout with a delivery method and optionally `SEAPEDIA10` or `HEMAT20K` → see the order in "Riwayat Order".
5. **Seller processes the order:** login as the Seller who owns that store → `/seller` → Orders tab → "Proses Pesanan".
6. **Driver flow:** login as `driver1` → `/driver` → take the now-available job → confirm completed → check wallet for the earning.
7. **Admin flow:** login as `admin` → `/admin` → Monitoring tab (see counts) → Voucher/Promo tabs (create new codes) → Waktu & Overdue tab (simulate days forward, run overdue check on an unprocessed order to see the auto-refund).
8. **Security spot-check:** try submitting `<script>alert(1)</script>` in the public review form on `/` and confirm it renders as plain text.

## 8. Known Simplifications / Notes

- Product photo upload is supported (Seller dashboard, stored via `Storage::disk('public')`) — run `php artisan storage:link` after migrating, or images will 404.
- Styling uses the Tailwind CDN script (`cdn.tailwindcss.com`) for simplicity. `resources/css/app.css` and `resources/js/app.js` are prepared for a proper Vite build but are not currently wired into `layouts/app.blade.php` — harmless for local demo, but the CDN script is not recommended for production and should be swapped for a compiled Vite build before deploying.
- UI uses plain inline styles (no CSS framework) to keep the deliverable dependency-light; functionally complete but not visually polished — see "Additional Bonus Points" in the brief for where UI polish would be scored separately.
- Sanctum tokens do not expire by default in this setup (fine for a demo/evaluation environment). For production, set `'expiration' => <minutes>` in `config/sanctum.php` after publishing it.
- Deployment is not included in this deliverable; see the assignment's optional 15-pt deployment bonus.
