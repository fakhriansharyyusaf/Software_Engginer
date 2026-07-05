# SEAPEDIA

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

```bash
composer install
cp .env.example .env
php artisan key:generate
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
