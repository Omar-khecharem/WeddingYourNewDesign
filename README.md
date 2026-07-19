# WeddingYour

A Bengali wedding accessories e-commerce platform built with PHP (custom MVC framework), MySQL, and Tailwind CSS.

## Features

- **Product Catalog** — Browse by category/subcategory with search, filter, sort
- **Cart & Checkout** — AJAX cart, coupon system, COD with order tracking
- **User Accounts** — Registration, login, wishlist, compare, order history
- **Admin Panel** — Secret URL (`/13091998`) with CRUD for products, categories, banners, orders, users, coupons, blog, gallery, pages, settings
- **Image Management** — Product images with primary/drag-drop reorder, category/subcategory image upload and delete
- **Homepage Builder** — Admin-controlled featured/trending products, banners (hero + brand story video), "Just Added" carousel, gallery, testimonials
- **Blog** — Posts with categories, publish/unpublish
- **SEO** — Per-page meta tags, canonical URLs, robots.txt, sitemap
- **Order Management** — Status updates, invoice generation, print, email notifications
- **Admin Notifications** — Badge counts for orders, contact messages, password reset requests

## Tech Stack

- **Backend:** PHP 8.x with custom MVC (Router, Controller, Model, View, Service layers)
- **Database:** MySQL with PDO
- **Frontend:** Tailwind CSS, vanilla JS (AJAX cart/wishlist/compare)
- **Deployment:** Hostinger shared hosting, FTP deployment

## Local Setup

1. Clone the repo
2. Point your web root to the project directory
3. Import `database/schema.sql` and `database/seed.sql`
4. Copy `config/.env.example` to `config/.env` and update credentials
5. Ensure `cache/`, `logs/`, `public/uploads/` are writable

## Admin Access

- URL: `https://yourdomain.com/13091998`
- Uses a secret path (not `/admin`) for security

## Deployment

All deployable files are under `public_html/`. This folder is gitignored — files are maintained at the repository root for versioning and copied to `public_html/` for deployment.

## License

Private — All rights reserved.
