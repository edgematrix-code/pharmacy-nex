# Nexus Pharma – PHP store

A plain-PHP (8.1+) / SQLite rebuild of a WooCommerce-style shop, matching the layout and look of the original
(purple utility bars, navy header with category search, bordered product grid, tiered bulk pricing, FAQ accordion),
with a **non-prescription health-product catalog** (vitamins, OTC pain relief, cold & flu, first aid, home devices).

Now rebranded as **Nexus Pharma** with the Nexus X logo, Nexus-branded product images, and updated
site-wide content (contact email, about/quality copy, order tracking).

## Run locally
    php -S localhost:8000 -t public public/index.php
Open http://localhost:8000. The SQLite DB (`storage/store.sqlite`) is created and seeded on first request.
Requires the `pdo_sqlite` PHP extension.

## Configuration (.env)
All settings live in a `.env` file at the project root (DB credentials, admin login, site name, shipping,
links). Copy the template and fill it in:

    cp .env.example .env

`.env` is git-ignored. Real environment variables override the file (useful for Docker/CI),
and every key has a built-in default so the app still boots without a `.env`.

## Deploy (Apache/shared hosting)
Point the document root at `public/` (an `.htaccess` rewrite is included). Make `storage/` writable, keep it outside
the web root if you can, and **set a strong `ADMIN_PASS` in `.env`**.

## What's included
Home, Shop (category filter, search, sort, Load More), product pages with tier-price table, session cart,
wishlist, checkout -> saved orders, order tracking by number + email, FAQ, contact form, About, Quality & Testing,
and a small admin (`/admin`) to update order status/tracking and read messages.

## Before going live
- Checkout records "pay on delivery" only. Add a payment gateway in `app/views/checkout.php`.
- Replace placeholder brand, text and generated bottle images (`/img/<slug>.svg`) with your own.
- Selling medicines (even OTC) is regulated – make sure you hold the required licences for your country.
- Edit products in `app/data.php` (seed) or directly in the DB.
