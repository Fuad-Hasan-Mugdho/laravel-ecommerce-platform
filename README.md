# BP Shop – Simplified E-commerce Platform (Laravel)

Technical assignment for the **Mid-Level Laravel Developer** position at Bangla Puzzle Limited.

A simplified e-commerce platform with full CRUD for **Categories → Subcategories → Products**, an admin panel, and a public storefront with SEO-friendly slug URLs.

| | |
|---|---|
| Framework | Laravel 13 (PHP 8.3) |
| Database | MySQL 8 (SQLite in-memory for tests) |
| Front-end | Blade + Bootstrap 5 (CDN, no Node build step required) |
| Tests | 34 feature tests, 118 assertions (`php artisan test`) |

---

## 1. Features

### Admin panel (`/admin`) – login required
- **Authentication** – session-based login (`/admin/login`) with "remember me", logout, and rate limiting (5 attempts/minute). Guests hitting any `/admin/*` URL are redirected to the login page; the storefront stays public.
  - Demo credentials (created by the seeder / included in the SQL export): **admin@example.com** / **password**
- **Dashboard** with counts and latest products.
- **Categories** – create, list (search + pagination), view, edit, delete.
- **Subcategories** – created under an existing category; list filterable by category; view, edit, delete. Name is unique *per category*.
- **Products** – category, subcategory (dependent dropdown loaded via AJAX), name, description, image upload with live preview, old price, new price, active flag. List with search/filter, view, edit (optional image replace), delete.
- Server-side validation everywhere via **Form Request** classes, with inline Bootstrap error messages and old input preserved.

### Storefront (`/`)
- **Home** – every active product, grouped by category and then by subcategory ("View all" when a subcategory has more than 8 products).
- **Category page** `/category/{slug}` and **Subcategory page** `/subcategory/{slug}` (paginated).
- **Product details** `/products/{slug}` with discount badge, savings, and related products from the same subcategory.
- Inactive categories/subcategories/products are hidden from the storefront (404 for direct product links).

### Data integrity
- `subcategories.category_id` and `products.category_id` / `products.subcategory_id` are **foreign keys** with `ON DELETE CASCADE` at the database level.
- Deletion is additionally performed through **Eloquent model events** (`Category` → `Subcategory` → `Product`) so that the product's image file is removed from storage as well. The admin confirm dialog tells the user how many child records will be deleted.
- A product's subcategory **must belong to the selected category** (`Rule::exists(...)->where('category_id', ...)`).
- Moving a subcategory to another category automatically re-points its products' `category_id` (inside a transaction).
- `old_price` must be `>= new_price` when supplied; prices are `DECIMAL(10,2)`.
- Slugs are generated automatically from the name, guaranteed unique (`t-shirt`, `t-shirt-2`, ...), and only regenerated when the name actually changes so existing URLs stay stable.

---

## 2. Project structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/LoginController.php              # admin login / logout
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── CategoryController.php            # resource CRUD
│   │   │   ├── SubcategoryController.php         # resource CRUD
│   │   │   ├── ProductController.php             # resource CRUD + image handling
│   │   │   └── CategorySubcategoryController.php # JSON: subcategories of a category (AJAX)
│   │   └── Shop/
│   │       └── ShopController.php                # public listing / category / subcategory / product
│   └── Requests/
│       ├── Auth/LoginRequest.php                 # credentials + throttling
│       └── Admin/
│           ├── CategoryRequest.php               # shared by store & update
│           ├── SubcategoryRequest.php
│           └── ProductRequest.php
├── Models/
│   ├── Concerns/HasSlug.php                      # unique slug generation + slug route binding
│   ├── Category.php                              # hasMany Subcategory, hasMany Product
│   ├── Subcategory.php                           # belongsTo Category, hasMany Product
│   └── Product.php                               # belongsTo Category & Subcategory, image/discount helpers
├── Providers/AppServiceProvider.php              # Bootstrap pagination, storefront nav composer
└── Services/ProductImageService.php              # store / replace product images

database/
├── migrations/2026_11_06_00000{1,2,3}_create_{categories,subcategories,products}_table.php
├── factories/                                    # used by tests and seeders
├── seeders/AdminUserSeeder.php                   # admin@example.com / password
├── seeders/CatalogSeeder.php                     # demo catalogue (images generated with GD)
└── bp_ecommerce.sql                              # MySQL export of the seeded database

resources/views/
├── layouts/{admin,shop}.blade.php
├── auth/login.blade.php
├── components/{flash,delete-button,status-badge,price}.blade.php
├── admin/{dashboard, categories/*, subcategories/*, products/*}.blade.php
└── shop/{index,category,subcategory,product}.blade.php + partials/

routes/web.php                                    # storefront routes + /admin resource routes
tests/Feature/{Auth,Admin,Shop}/                  # feature tests
docker-compose.yml, docker/php/Dockerfile         # optional Docker setup
```

### Database schema

```
categories        subcategories                 products
---------------   ---------------------------   ------------------------------------
id                id                            id
name (unique)     category_id  FK → categories  category_id     FK → categories
slug (unique)     name  (unique per category)   subcategory_id  FK → subcategories
description       slug (unique)                 name
is_active         description                   slug (unique)
timestamps        is_active                     description
                  timestamps                    image (path on "public" disk, nullable)
                                                old_price  DECIMAL(10,2) nullable
                                                new_price  DECIMAL(10,2)
                                                is_active
                                                timestamps
```

All foreign keys are `ON UPDATE CASCADE ON DELETE CASCADE`.

### Routes

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/` | `shop.index` | All products grouped by subcategory |
| GET | `/category/{slug}` | `shop.category` | Products of one category, grouped by subcategory |
| GET | `/subcategory/{slug}` | `shop.subcategory` | Paginated products of one subcategory |
| GET | `/products/{slug}` | `shop.product` | Product details |
| GET/POST | `/admin/login` | `admin.login`, `admin.login.store` | Admin login (guests only) |
| POST | `/admin/logout` | `admin.logout` | Log out |
| GET | `/admin` | `admin.dashboard` | Admin dashboard (auth) |
| * | `/admin/categories` | `admin.categories.*` | Category resource (index/create/store/show/edit/update/destroy) |
| * | `/admin/subcategories` | `admin.subcategories.*` | Subcategory resource |
| * | `/admin/products` | `admin.products.*` | Product resource |
| GET | `/admin/categories/{id}/subcategories` | `admin.categories.subcategories` | JSON for the dependent dropdown |

Run `php artisan route:list` for the complete list.

---

## 3. Setup instructions

### Option A: Local PHP (XAMPP / Laragon / Herd / native)

Requirements: PHP ≥ 8.3 with `pdo_mysql`, `gd`, `fileinfo`, `mbstring`; Composer; MySQL 8 (or MariaDB 10.6+).

```bash
# 1. Install dependencies
composer install

# 2. Environment
cp .env.example .env          # skip if .env already exists
php artisan key:generate

# 3. Create the database, then set DB_* in .env
#    DB_DATABASE=bp_ecommerce  DB_USERNAME=root  DB_PASSWORD=
mysql -u root -p -e "CREATE DATABASE bp_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4a. EITHER run migrations + demo seed (generates demo product images)
php artisan migrate --seed

# 4b. OR import the provided SQL export instead of migrating
mysql -u root -p bp_ecommerce < database/bp_ecommerce.sql

# 5. Expose uploaded images
php artisan storage:link

# 6. Run
php artisan serve             # http://localhost:8000
# Admin: http://localhost:8000/admin  ->  admin@example.com / password
```

> If you import `database/bp_ecommerce.sql`, the demo product images referenced by it are already included under `storage/app/public/products/`.

### Option B: Docker (no local PHP needed)

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app cp -n .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
# open http://localhost:8000
```

The `app` service already has `DB_HOST=mysql` etc. set as environment variables, which override `.env`.

### Running the tests

```bash
php artisan test
```

Tests run against an in-memory SQLite database (configured in `phpunit.xml`) and a fake storage disk, so they never touch your MySQL data or real uploads.

---

## 4. Implementation approach

**Authentication.** The admin area is protected by Laravel's built-in session guard rather than a starter kit, to keep the footprint small: a `LoginRequest` validates credentials and throttles attempts, `redirectGuestsTo()` in `bootstrap/app.php` sends unauthenticated users to `/admin/login`, and all CRUD routes sit in one `auth` middleware group. Admin tests extend `AdminTestCase`, which logs a user in before each test.

**Routing.** Public routes use the slug directly (`/products/{slug}`) and are resolved with explicit `active()` queries so inactive items 404 instead of leaking. Admin routes use `Route::resource` with implicit model binding; the models' `getRouteKeyName()` returns `slug`, so admin URLs are readable too (`/admin/products/galaxy-edge-12/edit`). The AJAX endpoint binds by `{category:id}` because the front-end only knows the selected option's id.

**Eloquent relationships.** `Category hasMany Subcategory hasMany Product`, plus `Category hasMany Product` and the inverse `belongsTo`s. Listing pages use eager loading with constrained relations (`with(['subcategories' => fn ($q) => $q->active()->whereHas(...)->withCount(...)])`) to avoid N+1 queries while still grouping products under their subcategory.

**Validation.** One Form Request per entity, shared between store and update. `prepareForValidation()` normalises input (trim, checkbox → boolean, empty old price → `null`). Uniqueness rules ignore the current record on update; the subcategory rule is scoped to the parent category; the product rule enforces that the subcategory belongs to the chosen category.

**Images.** Uploads go to the `public` disk under `products/` through a small `ProductImageService`. Replacing an image deletes the old file; deleting a product (directly or via cascade) deletes its file through the model's `deleted` event. The UI falls back to `public/images/placeholder.svg` when a product has no image.

**Data integrity on delete.** Both layers are used on purpose: database-level cascades guarantee no orphan rows even if something bypasses Eloquent, and Eloquent-level cascades make sure application-level side effects (file cleanup) run. Deletes are wrapped in transactions.

**Front-end.** Bootstrap 5 and Bootstrap Icons from CDN keep the project runnable without Node/Vite. A few small Blade components (`<x-price>`, `<x-status-badge>`, `<x-delete-button>`, `<x-flash>`) keep the views DRY. The product form uses ~40 lines of vanilla JS for the dependent subcategory dropdown and image preview.

**Out of scope (by design).** Cart/checkout, user registration and product image resizing were intentionally left out to keep the assignment focused. The admin login was added on top of the brief because an unprotected admin panel is not realistic for any deployment.

---

## 5. Deployment notes (VPS)

The application is a standard Laravel app and deploys like any other:

1. Ubuntu 22.04/24.04 with Nginx, PHP 8.3-FPM (`php8.3-{fpm,mysql,gd,mbstring,xml,curl,zip,bcmath}`), MySQL 8, Composer.
2. Clone/upload to `/var/www/bp-shop`, `composer install --no-dev --optimize-autoloader`.
3. `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`, DB credentials.
4. `php artisan key:generate && php artisan migrate --force && php artisan storage:link`, then `php artisan db:seed --class=AdminUserSeeder --force` and change the password (or create your own user with `php artisan tinker`).
5. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
6. Nginx server block pointing `root` to `/var/www/bp-shop/public`, `chown -R www-data:www-data storage bootstrap/cache`.
7. HTTPS via Certbot. Optionally run with the provided `docker-compose.yml` behind a reverse proxy instead.
