## Transportation Management App

A Laravel 12 + Filament v4 application for managing companies, drivers, vehicles, and trips. It includes KPIs, availability tooling, and a clean Filament admin panel.

This README documents a Windows-friendly setup using:
- Herd (free) for PHP + Nginx web serving
- Laragon for MySQL (since Herd free does not bundle MySQL)

---

## Tech Stack
- Laravel 12 (PHP 8.2+)
- Filament v4
- Pest (Testing)
- MySQL 8 (via Laragon)
- Vite (assets), Node 18+/20+
- Tailwind V4 (Automatic installed via Laravel 12)

---

## Prerequisites
- PHP 8.2+ (managed by Herd)
- Composer 2.x
- Node.js 18+ (20+ recommended) and npm
- Herd (free): https://herd.laravel.com/
- Laragon (for MySQL): https://laragon.org/

Optional but recommended:
- Redis (for queues/cache) – not required for local setup

---

## Local Environment Setup (Herd + Laragon)

1) Clone the repository
```bash
git clone https://github.com/MohamedAshour119/Transportation-Management-App.git
cd Transportation-Management-App
```

2) Install PHP dependencies
```bash
composer install
```

3) Install JS dependencies and build assets
```bash
npm install
# Development (HMR)
npm run dev
# or Production build
npm run build
```

4) Create and configure your environment
```bash
cp .env.example .env
php artisan key:generate
```
Edit the following env keys for Herd + Laragon:
```env
APP_NAME="Transportation Management App"
APP_ENV=local
APP_DEBUG=true
APP_URL=https://your-app.test             # Herd site URL

# Point to Laragon's MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=transportation_management
DB_USERNAME=root                         # Laragon default
DB_PASSWORD=                             # Laragon default is empty

# Queue / Cache (defaults are fine for local)
QUEUE_CONNECTION=sync
CACHE_DRIVER=file
SESSION_DRIVER=file
FILESYSTEM_DISK=public
```

5) Start services
- Start Laragon and ensure MySQL is running (port 3306).
- In Herd, add this project as a site and note the domain (e.g., `https://transportation-management-app.test`).

6) Database migration & seed
```bash
php artisan migrate
php artisan db:seed
```

7) Storage link (it's not required for the local app because there is not files or images used)
```bash
php artisan storage:link
```

Now open your Herd site URL in a browser. The Filament admin panel will be available according to your panel configuration.

---

## Filament Admin
- Version: Filament v4
- Location: Admin pages, resources, and widgets are under `app/Filament/`
- KPIs Page & Widgets:
  - Page: `app/Filament/Pages/KPIs.php`
  - Stats widget: `app/Filament/Widgets/KPIsWidget.php`
  - Line chart widget: `app/Filament/Widgets/KPIsLineChartWidget.php`
  - KPIs Blade view: `resources/views/filament/pages/k-p-is.blade.php`

### KPI Caching
To improve performance, the KPIs are cached:
- Keys:
  - `kpis.widget.stats`
  - `kpis.line_chart.stats`
- TTL: 60 seconds (configurable in the widget classes)
- Automatic invalidation: when a `Trip` is created/updated/deleted (`app/Models/Trip.php::booted()`), both cache keys are forgotten so the KPIs refresh promptly.
- Manual clears:
```bash
php artisan cache:clear
php artisan optimize:clear
```

---

## Availability Overview Page
- Page class: `app/Filament/Pages/AvailabilityOverview.php`
- Blade view: `resources/views/filament/pages/availability-overview.blade.php`
- Functionality: pick a date-time range and view available drivers and vehicles in that period using helpers on `Trip`.

Trip helpers used:
- `Trip::getAvailableDrivers($start, $end, ?$companyId)`
- `Trip::getAvailableVehicles($start, $end, ?$companyId)`

---

## Running Tests
We use Pest.

Run the full suite:
```bash
php artisan test
```
Run a specific file:
```bash
php artisan test --filter=AvailabilityOverviewPageTest
```
Generate coverage (requires Xdebug or PCOV):
```bash
php artisan test --coverage
```

Included feature tests cover:
- CRUD resources (Company, Driver, Vehicle, Trip)
- KPI helpers (active trips, available drivers/vehicles, completed trips this month)
- Availability Overview scenarios
- Overlapping trip validation

---

## Common Troubleshooting
- Database connection errors
  - Confirm Laragon MySQL is running on `127.0.0.1:3306` and the `.env` matches your DB name/user/pass.
- URL / asset issues
  - Ensure `APP_URL` matches your Herd site domain, rebuild assets if needed: `npm run build`.
- Cache not updating
  - KPI caches auto-clear on Trip create/update/delete. Manually clear with `php artisan cache:clear` if needed.
- Filament/Schemas vs Forms API
  - This project uses Filament v4 and leverages the new Schemas API where applicable.

---

## Useful Artisan Commands
```bash
# Database
php artisan migrate              # Run migrations
php artisan migrate:fresh --seed # Rebuild database with seeders

# Cache / Config
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan optimize:clear

# Assets
npm run dev
npm run build
```

---

## Project Structure (high level)
```
app/
  Filament/
    Pages/
    Widgets/
  Models/
config/
database/
resources/
  views/
  css/js/
routes/
```

---
