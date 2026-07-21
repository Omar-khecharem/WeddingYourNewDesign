-- =============================================================================
-- WEDDINGYOUR — MASTER DATABASE SCHEMA
-- Engine: InnoDB  |  Charset: utf8mb4  |  Collation: utf8mb4_unicode_ci
-- Version: 2.0
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `shola_ghar`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `shola_ghar`;

-- +----------------------------------------------------------------------------
-- | SECTION 1 – ACCESS CONTROL (roles, permissions, users)
-- +----------------------------------------------------------------------------

-- 1.1 ROLES
CREATE TABLE `sg_roles` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(50)  NOT NULL UNIQUE,
    `slug`        VARCHAR(50)  NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `is_system`   TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_roles_slug` (`slug`),
    INDEX `idx_roles_system` (`is_system`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.2 PERMISSIONS
CREATE TABLE `sg_permissions` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL UNIQUE,
    `slug`        VARCHAR(100) NOT NULL UNIQUE,
    `group`       VARCHAR(50)  NOT NULL DEFAULT 'general',
    `description` VARCHAR(255) NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_perm_slug` (`slug`),
    INDEX `idx_perm_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.3 ROLE-PERMISSION PIVOT
CREATE TABLE `sg_role_permission` (
    `role_id`       INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`role_id`, `permission_id`),
    INDEX `idx_rp_permission` (`permission_id`),
    CONSTRAINT `fk_rp_role`       FOREIGN KEY (`role_id`)       REFERENCES `sg_roles`(`id`)       ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `sg_permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.4 USERS
CREATE TABLE `sg_users` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_id`           INT UNSIGNED NULL,
    `name`              VARCHAR(100) NOT NULL,
    `email`             VARCHAR(191) NOT NULL UNIQUE,
    `phone`             VARCHAR(20)  NULL,
    `password`          VARCHAR(255) NOT NULL,
    `avatar`            VARCHAR(255) NULL,
    `status`            TINYINT(1)   NOT NULL DEFAULT 1,
    `email_verified_at` DATETIME     NULL,
    `remember_token`    VARCHAR(100) NULL,
    `last_login_at`     DATETIME     NULL,
    `last_login_ip`     VARCHAR(45)  NULL,
    `locale`            VARCHAR(10)  NOT NULL DEFAULT 'fr',
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_role` (`role_id`),
    INDEX `idx_users_status` (`status`),
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `sg_roles`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.5 CUSTOMERS (rich customer profile, separate from auth users)
CREATE TABLE `sg_customers` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`         INT UNSIGNED NULL,
    `first_name`      VARCHAR(100) NOT NULL,
    `last_name`       VARCHAR(100) NULL,
    `email`           VARCHAR(191) NOT NULL UNIQUE,
    `phone`           VARCHAR(20)  NULL,
    `gender`          ENUM('male','female','other') NULL,
    `date_of_birth`   DATE         NULL,
    `avatar`          VARCHAR(255) NULL,
    `company`         VARCHAR(100) NULL,
    `tax_number`      VARCHAR(50)  NULL,
    `notes`           TEXT         NULL,
    `newsletter`      TINYINT(1)   NOT NULL DEFAULT 1,
    `total_orders`    INT UNSIGNED NOT NULL DEFAULT 0,
    `total_spent`     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `last_order_at`   DATETIME     NULL,
    `status`          TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cust_user` (`user_id`),
    INDEX `idx_cust_email` (`email`),
    INDEX `idx_cust_phone` (`phone`),
    INDEX `idx_cust_status` (`status`),
    CONSTRAINT `fk_cust_user` FOREIGN KEY (`user_id`) REFERENCES `sg_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- +----------------------------------------------------------------------------
-- | SECTION 2 – LOCALISATION (countries, currencies, languages)
-- +----------------------------------------------------------------------------

-- 2.1 COUNTRIES
CREATE TABLE `sg_countries` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`          VARCHAR(100) NOT NULL,
    `iso_code_2`    CHAR(2)      NOT NULL,
    `iso_code_3`    CHAR(3)      NOT NULL,
    `phone_code`    VARCHAR(10)  NOT NULL,
    `currency_code` CHAR(3)      NOT NULL DEFAULT 'EUR',
    `currency_symbol` VARCHAR(5) NOT NULL DEFAULT '€',
    `locale`        VARCHAR(10)  NOT NULL DEFAULT 'fr_FR',
    `status`        TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`    INT          NOT NULL DEFAULT 0,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_country_iso2` (`iso_code_2`),
    UNIQUE KEY `uk_country_iso3` (`iso_code_3`),
    INDEX `idx_country_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.2 CURRENCIES
CREATE TABLE `sg_currencies` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`           VARCHAR(50)  NOT NULL,
    `code`           CHAR(3)      NOT NULL UNIQUE,
    `symbol`         VARCHAR(10)  NOT NULL,
    `symbol_position` ENUM('before','after') NOT NULL DEFAULT 'before',
    `decimal_places` TINYINT UNSIGNED NOT NULL DEFAULT 2,
    `decimal_sep`    CHAR(1)      NOT NULL DEFAULT '.',
    `thousand_sep`   CHAR(1)      NOT NULL DEFAULT ',',
    `exchange_rate`  DECIMAL(12,6) NOT NULL DEFAULT 1.000000,
    `is_default`     TINYINT(1)   NOT NULL DEFAULT 0,
    `status`         TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_curr_code` (`code`),
    INDEX `idx_curr_default` (`is_default`),
    INDEX `idx_curr_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.3 LANGUAGES
CREATE TABLE `sg_languages` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(50)  NOT NULL,
    `code`        VARCHAR(10)  NOT NULL UNIQUE,
    `locale`      VARCHAR(10)  NOT NULL,
    `direction`   ENUM('ltr','rtl') NOT NULL DEFAULT 'ltr',
    `icon`        VARCHAR(50)  NULL,
    `is_default`  TINYINT(1)   NOT NULL DEFAULT 0,
    `status`      TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_lang_code` (`code`),
    INDEX `idx_lang_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- +----------------------------------------------------------------------------
-- | SECTION 3 – CATALOGUE (categories, subcategories, brands, products)
-- +----------------------------------------------------------------------------

-- 3.1 CATEGORIES (self-referencing parent for unlimited depth)
CREATE TABLE `sg_categories` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `parent_id`        INT UNSIGNED NULL,
    `name`             VARCHAR(100) NOT NULL,
    `slug`             VARCHAR(120) NOT NULL UNIQUE,
    `description`      TEXT         NULL,
    `image`            VARCHAR(255) NULL,
    `icon`             VARCHAR(50)  NULL,
    `banner_image`     VARCHAR(255) NULL,
    `meta_title`       VARCHAR(191) NULL,
    `meta_description` VARCHAR(255) NULL,
    `sort_order`       INT          NOT NULL DEFAULT 0,
    `featured`         TINYINT(1)   NOT NULL DEFAULT 0,
    `status`           TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cat_parent` (`parent_id`),
    INDEX `idx_cat_slug` (`slug`),
    INDEX `idx_cat_featured` (`featured`),
    INDEX `idx_cat_status` (`status`),
    CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `sg_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.2 SUBCATEGORIES (dedicated table for simpler queries)
CREATE TABLE `sg_subcategories` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id`      INT UNSIGNED NOT NULL,
    `name`             VARCHAR(100) NOT NULL,
    `slug`             VARCHAR(120) NOT NULL UNIQUE,
    `description`      TEXT         NULL,
    `image`            VARCHAR(255) NULL,
    `meta_title`       VARCHAR(191) NULL,
    `meta_description` VARCHAR(255) NULL,
    `sort_order`       INT          NOT NULL DEFAULT 0,
    `status`           TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_subcat_category` (`category_id`),
    INDEX `idx_subcat_slug` (`slug`),
    INDEX `idx_subcat_status` (`status`),
    CONSTRAINT `fk_subcat_category` FOREIGN KEY (`category_id`) REFERENCES `sg_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.3 BRANDS
CREATE TABLE `sg_brands` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`             VARCHAR(100) NOT NULL,
    `slug`             VARCHAR(120) NOT NULL UNIQUE,
    `description`      TEXT         NULL,
    `logo`             VARCHAR(255) NULL,
    `website`          VARCHAR(255) NULL,
    `meta_title`       VARCHAR(191) NULL,
    `meta_description` VARCHAR(255) NULL,
    `sort_order`       INT          NOT NULL DEFAULT 0,
    `featured`         TINYINT(1)   NOT NULL DEFAULT 0,
    `status`           TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_brand_slug` (`slug`),
    INDEX `idx_brand_featured` (`featured`),
    INDEX `idx_brand_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.4 PRODUCTS
CREATE TABLE `sg_products` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id`       INT UNSIGNED NULL,
    `subcategory_id`    INT UNSIGNED NULL,
    `brand_id`          INT UNSIGNED NULL,
    `name`              VARCHAR(200) NOT NULL,
    `slug`              VARCHAR(220) NOT NULL UNIQUE,
    `short_description` VARCHAR(500) NULL,
    `description`       LONGTEXT     NULL,
    `sku`               VARCHAR(50)  NULL UNIQUE,
    `barcode`           VARCHAR(100) NULL,
    `regular_price`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `sale_price`        DECIMAL(10,2) NULL,
    `discount_percent`  TINYINT UNSIGNED NULL,
    `cost_price`        DECIMAL(10,2) NULL,
    `margin`            DECIMAL(5,2) NULL,
    `stock_quantity`    INT          NOT NULL DEFAULT 0,
    `low_stock_threshold` INT       NOT NULL DEFAULT 5,
    `stock_status`      ENUM('in_stock','out_of_stock','on_backorder') NOT NULL DEFAULT 'in_stock',
    `weight`            DECIMAL(8,2) NULL,
    `length`            DECIMAL(8,2) NULL,
    `width`             DECIMAL(8,2) NULL,
    `height`            DECIMAL(8,2) NULL,
    `weight_unit`       VARCHAR(5)   NOT NULL DEFAULT 'kg',
    `dimension_unit`    VARCHAR(5)   NOT NULL DEFAULT 'cm',
    `is_featured`       TINYINT(1)   NOT NULL DEFAULT 0,
    `is_new`            TINYINT(1)   NOT NULL DEFAULT 0,
    `is_trending`       TINYINT(1)   NOT NULL DEFAULT 0,
    `is_bestseller`     TINYINT(1)   NOT NULL DEFAULT 0,
    `taxable`           TINYINT(1)   NOT NULL DEFAULT 1,
    `tax_class`         VARCHAR(50)  NULL,
    `views_count`       INT UNSIGNED NOT NULL DEFAULT 0,
    `sales_count`       INT UNSIGNED NOT NULL DEFAULT 0,
    `rating_avg`        DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    `rating_count`      INT UNSIGNED NOT NULL DEFAULT 0,
    `min_order_qty`     INT UNSIGNED NOT NULL DEFAULT 1,
    `max_order_qty`     INT UNSIGNED NULL,
    `status`            TINYINT(1)   NOT NULL DEFAULT 1,
    `meta_title`        VARCHAR(191) NULL,
    `meta_description`  VARCHAR(255) NULL,
    `meta_keywords`     VARCHAR(255) NULL,
    `published_at`      DATETIME     NULL,
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_prod_category` (`category_id`),
    INDEX `idx_prod_subcategory` (`subcategory_id`),
    INDEX `idx_prod_brand` (`brand_id`),
    INDEX `idx_prod_slug` (`slug`),
    INDEX `idx_prod_sku` (`sku`),
    INDEX `idx_prod_status` (`status`),
    INDEX `idx_prod_featured` (`is_featured`),
    INDEX `idx_prod_trending` (`is_trending`),
    INDEX `idx_prod_bestseller` (`is_bestseller`),
    INDEX `idx_prod_stock` (`stock_status`),
    INDEX `idx_prod_price` (`regular_price`, `sale_price`),
    INDEX `idx_prod_rating` (`rating_avg`),
    INDEX `idx_prod_created` (`created_at`),
    FULLTEXT `ft_prod_search` (`name`, `short_description`, `description`),
    CONSTRAINT `fk_prod_category`    FOREIGN KEY (`category_id`)    REFERENCES `sg_categories`(`id`)    ON DELETE SET NULL,
    CONSTRAINT `fk_prod_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `sg_subcategories`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_prod_brand`       FOREIGN KEY (`brand_id`)       REFERENCES `sg_brands`(`id`)       ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.5 PRODUCT IMAGES
CREATE TABLE `sg_product_images` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `image`      VARCHAR(255) NOT NULL,
    `alt_text`   VARCHAR(191) NULL,
    `caption`    VARCHAR(255) NULL,
    `sort_order` INT          NOT NULL DEFAULT 0,
    `is_primary` TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pimg_product` (`product_id`),
    INDEX `idx_pimg_primary` (`is_primary`),
    CONSTRAINT `fk_pimg_product` FOREIGN KEY (`product_id`) REFERENCES `sg_products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.6 PRODUCT VARIANTS
CREATE TABLE `sg_product_variants` (
    `id`                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id`         INT UNSIGNED NOT NULL,
    `name`               VARCHAR(100) NOT NULL,
    `sku`                VARCHAR(50)  NULL,
    `price_adjustment`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `stock_quantity`     INT          NOT NULL DEFAULT 0,
    `weight`             DECIMAL(8,2) NULL,
    `is_default`         TINYINT(1)   NOT NULL DEFAULT 0,
    `attributes`         JSON         NULL,
    `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pvar_product` (`product_id`),
    CONSTRAINT `fk_pvar_product` FOREIGN KEY (`product_id`) REFERENCES `sg_products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.7 TAGS
CREATE TABLE `sg_tags` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(50)  NOT NULL,
    `slug`       VARCHAR(60)  NOT NULL UNIQUE,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_tag_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.8 PRODUCT-TAG PIVOT
CREATE TABLE `sg_product_tag` (
    `product_id` INT UNSIGNED NOT NULL,
    `tag_id`     INT UNSIGNED NOT NULL,
    PRIMARY KEY (`product_id`, `tag_id`),
    CONSTRAINT `fk_ptag_product` FOREIGN KEY (`product_id`) REFERENCES `sg_products`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ptag_tag`     FOREIGN KEY (`tag_id`)     REFERENCES `sg_tags`(`id`)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- +----------------------------------------------------------------------------
-- | SECTION 4 – SHOPPING & PROMOTIONS (carts, wishlist, compare, coupons)
-- +----------------------------------------------------------------------------

-- 4.1 CARTS
CREATE TABLE `sg_carts` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED NULL,
    `session_id` VARCHAR(100) NULL,
    `coupon_id`  INT UNSIGNED NULL,
    `subtotal`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tax`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `shipping`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cart_user` (`user_id`),
    INDEX `idx_cart_session` (`session_id`),
    CONSTRAINT `fk_cart_user`   FOREIGN KEY (`user_id`)  REFERENCES `sg_users`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.2 CART ITEMS
CREATE TABLE `sg_cart_items` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cart_id`     INT UNSIGNED NOT NULL,
    `product_id`  INT UNSIGNED NOT NULL,
    `variant_id`  INT UNSIGNED NULL,
    `quantity`    INT UNSIGNED NOT NULL DEFAULT 1,
    `unit_price`  DECIMAL(10,2) NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_citem_cart` (`cart_id`),
    INDEX `idx_citem_product` (`product_id`),
    CONSTRAINT `fk_citem_cart`    FOREIGN KEY (`cart_id`)    REFERENCES `sg_carts`(`id`)    ON DELETE CASCADE,
    CONSTRAINT `fk_citem_product` FOREIGN KEY (`product_id`) REFERENCES `sg_products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.3 WISHLIST
CREATE TABLE `sg_wishlist` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_wishlist_user_product` (`user_id`, `product_id`),
    INDEX `idx_wish_user` (`user_id`),
    CONSTRAINT `fk_wish_user`    FOREIGN KEY (`user_id`)    REFERENCES `sg_users`(`id`)    ON DELETE CASCADE,
    CONSTRAINT `fk_wish_product` FOREIGN KEY (`product_id`) REFERENCES `sg_products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.4 COMPARE
CREATE TABLE `sg_compare` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED NULL,
    `session_id` VARCHAR(100) NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_comp_user` (`user_id`),
    INDEX `idx_comp_session` (`session_id`),
    CONSTRAINT `fk_comp_product` FOREIGN KEY (`product_id`) REFERENCES `sg_products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.5 COUPONS
CREATE TABLE `sg_coupons` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code`            VARCHAR(50)  NOT NULL UNIQUE,
    `type`            ENUM('percentage','fixed','free_shipping') NOT NULL DEFAULT 'percentage',
    `value`           DECIMAL(10,2) NOT NULL,
    `min_order_amount` DECIMAL(10,2) NULL,
    `max_discount`    DECIMAL(10,2) NULL,
    `usage_limit`     INT UNSIGNED NULL,
    `usage_per_user`  INT UNSIGNED NULL DEFAULT 1,
    `used_count`      INT UNSIGNED NOT NULL DEFAULT 0,
    `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
    `starts_at`       DATETIME     NULL,
    `expires_at`      DATETIME     NULL,
    `description`     VARCHAR(255) NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_coup_code` (`code`),
    INDEX `idx_coup_active` (`is_active`),
    INDEX `idx_coup_dates` (`starts_at`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- +----------------------------------------------------------------------------
-- | SECTION 5 – ORDERS & PAYMENTS
-- +----------------------------------------------------------------------------

-- 5.1 ADDRESSES (user/customer addresses)
CREATE TABLE `sg_addresses` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`       INT UNSIGNED NULL,
    `customer_id`   INT UNSIGNED NULL,
    `type`          ENUM('billing','shipping','both') NOT NULL DEFAULT 'shipping',
    `label`         VARCHAR(50)  NULL,
    `full_name`     VARCHAR(100) NOT NULL,
    `phone`         VARCHAR(20)  NOT NULL,
    `address_line1` VARCHAR(255) NOT NULL,
    `address_line2` VARCHAR(255) NULL,
    `city`          VARCHAR(100) NOT NULL,
    `state`         VARCHAR(100) NOT NULL,
    `postal_code`   VARCHAR(20)  NOT NULL,
    `country_id`    INT UNSIGNED NULL,
    `latitude`      DECIMAL(10,8) NULL,
    `longitude`     DECIMAL(11,8) NULL,
    `is_default`    TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_addr_user` (`user_id`),
    INDEX `idx_addr_customer` (`customer_id`),
    INDEX `idx_addr_country` (`country_id`),
    CONSTRAINT `fk_addr_user`     FOREIGN KEY (`user_id`)     REFERENCES `sg_users`(`id`)         ON DELETE CASCADE,
    CONSTRAINT `fk_addr_customer` FOREIGN KEY (`customer_id`) REFERENCES `sg_customers`(`id`)      ON DELETE SET NULL,
    CONSTRAINT `fk_addr_country`  FOREIGN KEY (`country_id`)  REFERENCES `sg_countries`(`id`)      ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.2 SHIPPING METHODS
CREATE TABLE `sg_shipping_methods` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`         VARCHAR(100) NOT NULL,
    `slug`         VARCHAR(100) NOT NULL UNIQUE,
    `description`  TEXT         NULL,
    `base_rate`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `rate_per_kg`  DECIMAL(10,2) NULL,
    `free_above`   DECIMAL(10,2) NULL,
    `estimated_days_min` INT UNSIGNED NULL,
    `estimated_days_max` INT UNSIGNED NULL,
    `has_tracking` TINYINT(1)   NOT NULL DEFAULT 0,
    `status`       TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`   INT          NOT NULL DEFAULT 0,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ship_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.3 PAYMENT METHODS
CREATE TABLE `sg_payment_methods` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`           VARCHAR(100) NOT NULL,
    `slug`           VARCHAR(100) NOT NULL UNIQUE,
    `description`    TEXT         NULL,
    `instructions`   TEXT         NULL,
    `logo`           VARCHAR(255) NULL,
    `is_online`      TINYINT(1)   NOT NULL DEFAULT 0,
    `fee_type`       ENUM('none','fixed','percentage') NOT NULL DEFAULT 'none',
    `fee_value`      DECIMAL(10,2) NULL,
    `min_amount`     DECIMAL(10,2) NULL,
    `max_amount`     DECIMAL(10,2) NULL,
    `api_key`        VARCHAR(255) NULL,
    `api_secret`     VARCHAR(255) NULL,
    `is_default`     TINYINT(1)   NOT NULL DEFAULT 0,
    `status`         TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`     INT          NOT NULL DEFAULT 0,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pay_status` (`status`),
    INDEX `idx_pay_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.4 ORDERS
CREATE TABLE `sg_orders` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_number`    VARCHAR(30)  NOT NULL UNIQUE,
    `user_id`         INT UNSIGNED NULL,
    `customer_id`     INT UNSIGNED NULL,
    `email`           VARCHAR(191) NOT NULL,
    `phone`           VARCHAR(20)  NULL,
    `billing_address_id`  INT UNSIGNED NULL,
    `shipping_address_id` INT UNSIGNED NULL,
    `billing_name`    VARCHAR(100) NOT NULL,
    `billing_company` VARCHAR(100) NULL,
    `billing_address` TEXT         NOT NULL,
    `billing_city`    VARCHAR(100) NOT NULL,
    `billing_state`   VARCHAR(100) NOT NULL,
    `billing_postal`  VARCHAR(20)  NOT NULL,
    `billing_country` VARCHAR(100) NOT NULL,
    `shipping_name`     VARCHAR(100) NOT NULL,
    `shipping_company`  VARCHAR(100) NULL,
    `shipping_address`  TEXT         NOT NULL,
    `shipping_city`     VARCHAR(100) NOT NULL,
    `shipping_state`    VARCHAR(100) NOT NULL,
    `shipping_postal`   VARCHAR(20)  NOT NULL,
    `shipping_country`  VARCHAR(100) NOT NULL,
    `subtotal`        DECIMAL(10,2) NOT NULL,
    `discount`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tax`             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `shipping_cost`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total`           DECIMAL(10,2) NOT NULL,
    `coupon_code`     VARCHAR(50)  NULL,
    `coupon_discount` DECIMAL(10,2) NULL,
    `shipping_method_id`   INT UNSIGNED NULL,
    `shipping_method_name` VARCHAR(100) NULL,
    `tracking_number` VARCHAR(100) NULL,
    `payment_method_id`   INT UNSIGNED NULL,
    `payment_method_name` VARCHAR(100) NULL,
    `payment_status`  ENUM('pending','pending_online','completed','failed','refunded','partially_refunded') NOT NULL DEFAULT 'pending',
    `payment_id`      VARCHAR(100) NULL,
    `order_status`    ENUM('pending','confirmed','processing','shipped','delivered','cancelled','refunded','partially_shipped','on_hold') NOT NULL DEFAULT 'pending',
    `currency_code`   CHAR(3)      NOT NULL DEFAULT 'EUR',
    `currency_rate`   DECIMAL(12,6) NOT NULL DEFAULT 1.000000,
    `notes`           TEXT         NULL,
    `staff_notes`     TEXT         NULL,
    `is_paid`         TINYINT(1)   NOT NULL DEFAULT 0,
    `is_archived`     TINYINT(1)   NOT NULL DEFAULT 0,
    `invoice_number`  VARCHAR(30)  NULL,
    `delivered_at`    DATETIME     NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_order_user` (`user_id`),
    INDEX `idx_order_customer` (`customer_id`),
    INDEX `idx_order_number` (`order_number`),
    INDEX `idx_order_status` (`order_status`),
    INDEX `idx_order_payment_status` (`payment_status`),
    INDEX `idx_order_payment_method` (`payment_method_id`),
    INDEX `idx_order_shipping` (`shipping_method_id`),
    INDEX `idx_order_created` (`created_at`),
    INDEX `idx_order_billing` (`billing_address_id`),
    INDEX `idx_order_shipping_addr` (`shipping_address_id`),
    CONSTRAINT `fk_order_user`     FOREIGN KEY (`user_id`)            REFERENCES `sg_users`(`id`)             ON DELETE SET NULL,
    CONSTRAINT `fk_order_customer`  FOREIGN KEY (`customer_id`)       REFERENCES `sg_customers`(`id`)         ON DELETE SET NULL,
    CONSTRAINT `fk_order_payment`   FOREIGN KEY (`payment_method_id`) REFERENCES `sg_payment_methods`(`id`)   ON DELETE SET NULL,
    CONSTRAINT `fk_order_shipping`  FOREIGN KEY (`shipping_method_id`)REFERENCES `sg_shipping_methods`(`id`)  ON DELETE SET NULL,
    CONSTRAINT `fk_order_billing_addr`  FOREIGN KEY (`billing_address_id`)  REFERENCES `sg_addresses`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_order_shipping_addr` FOREIGN KEY (`shipping_address_id`) REFERENCES `sg_addresses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.5 ORDER ITEMS
CREATE TABLE `sg_order_items` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id`       INT UNSIGNED NOT NULL,
    `product_id`     INT UNSIGNED NULL,
    `product_name`   VARCHAR(200) NOT NULL,
    `product_sku`    VARCHAR(50)  NULL,
    `variant_name`   VARCHAR(100) NULL,
    `quantity`       INT UNSIGNED NOT NULL,
    `unit_price`     DECIMAL(10,2) NOT NULL,
    `total_price`    DECIMAL(10,2) NOT NULL,
    `tax_amount`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `image`          VARCHAR(255) NULL,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_oitm_order` (`order_id`),
    INDEX `idx_oitm_product` (`product_id`),
    CONSTRAINT `fk_oitm_order`   FOREIGN KEY (`order_id`)   REFERENCES `sg_orders`(`id`)   ON DELETE CASCADE,
    CONSTRAINT `fk_oitm_product` FOREIGN KEY (`product_id`) REFERENCES `sg_products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.6 TRANSACTIONS (payment gateway log)
CREATE TABLE `sg_transactions` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id`          INT UNSIGNED NOT NULL,
    `transaction_id`    VARCHAR(100) NULL,
    `payment_method_id` INT UNSIGNED NULL,
    `type`              ENUM('sale','refund','partial_refund','authorization','capture','void') NOT NULL DEFAULT 'sale',
    `amount`            DECIMAL(10,2) NOT NULL,
    `currency_code`     CHAR(3)      NOT NULL DEFAULT 'EUR',
    `status`            ENUM('pending','success','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
    `gateway_response`  JSON         NULL,
    `request_data`      JSON         NULL,
    `notes`             TEXT         NULL,
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_txn_order` (`order_id`),
    INDEX `idx_txn_method` (`payment_method_id`),
    INDEX `idx_txn_status` (`status`),
    INDEX `idx_txn_id` (`transaction_id`),
    CONSTRAINT `fk_txn_order`  FOREIGN KEY (`order_id`)          REFERENCES `sg_orders`(`id`)          ON DELETE CASCADE,
    CONSTRAINT `fk_txn_method` FOREIGN KEY (`payment_method_id`) REFERENCES `sg_payment_methods`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- +----------------------------------------------------------------------------
-- | SECTION 6 – ENGAGEMENT (reviews, notifications, contact)
-- +----------------------------------------------------------------------------

-- 6.1 REVIEWS
CREATE TABLE `sg_reviews` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id`  INT UNSIGNED NOT NULL,
    `user_id`     INT UNSIGNED NULL,
    `customer_id` INT UNSIGNED NULL,
    `name`        VARCHAR(100) NOT NULL,
    `email`       VARCHAR(191) NULL,
    `rating`      TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `title`       VARCHAR(200) NULL,
    `comment`     TEXT         NOT NULL,
    `images`      JSON         NULL,
    `is_verified` TINYINT(1)   NOT NULL DEFAULT 0,
    `is_approved` TINYINT(1)   NOT NULL DEFAULT 0,
    `helpful_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_rev_product` (`product_id`),
    INDEX `idx_rev_user` (`user_id`),
    INDEX `idx_rev_customer` (`customer_id`),
    INDEX `idx_rev_rating` (`rating`),
    INDEX `idx_rev_approved` (`is_approved`),
    CONSTRAINT `fk_rev_product`  FOREIGN KEY (`product_id`)  REFERENCES `sg_products`(`id`)  ON DELETE CASCADE,
    CONSTRAINT `fk_rev_user`     FOREIGN KEY (`user_id`)     REFERENCES `sg_users`(`id`)     ON DELETE SET NULL,
    CONSTRAINT `fk_rev_customer` FOREIGN KEY (`customer_id`) REFERENCES `sg_customers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.2 NOTIFICATIONS
CREATE TABLE `sg_notifications` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED NULL,
    `customer_id` INT UNSIGNED NULL,
    `type`        VARCHAR(50)  NOT NULL,
    `title`       VARCHAR(200) NOT NULL,
    `message`     TEXT         NOT NULL,
    `link`        VARCHAR(255) NULL,
    `icon`        VARCHAR(50)  NULL,
    `is_read`     TINYINT(1)   NOT NULL DEFAULT 0,
    `read_at`     DATETIME     NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_user` (`user_id`),
    INDEX `idx_notif_customer` (`customer_id`),
    INDEX `idx_notif_read` (`is_read`),
    INDEX `idx_notif_created` (`created_at`),
    CONSTRAINT `fk_notif_user`     FOREIGN KEY (`user_id`)     REFERENCES `sg_users`(`id`)     ON DELETE CASCADE,
    CONSTRAINT `fk_notif_customer` FOREIGN KEY (`customer_id`) REFERENCES `sg_customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.3 CONTACT MESSAGES
CREATE TABLE `sg_contact_messages` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`         VARCHAR(100) NOT NULL,
    `email`        VARCHAR(191) NOT NULL,
    `phone`        VARCHAR(20)  NULL,
    `subject`      VARCHAR(200) NULL,
    `message`      TEXT         NOT NULL,
    `department`   VARCHAR(50)  NULL,
    `order_number` VARCHAR(30)  NULL,
    `is_read`      TINYINT(1)   NOT NULL DEFAULT 0,
    `read_by`      INT UNSIGNED NULL,
    `read_at`      DATETIME     NULL,
    `is_replied`   TINYINT(1)   NOT NULL DEFAULT 0,
    `replied_at`   DATETIME     NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cmsg_read` (`is_read`),
    INDEX `idx_cmsg_email` (`email`),
    INDEX `idx_cmsg_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.4 NEWSLETTER SUBSCRIBERS
CREATE TABLE `sg_newsletter` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email`           VARCHAR(191) NOT NULL UNIQUE,
    `name`            VARCHAR(100) NULL,
    `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
    `subscribed_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `unsubscribed_at` DATETIME     NULL,
    INDEX `idx_nl_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- +----------------------------------------------------------------------------
-- | SECTION 7 – CONTENT (blogs, pages, sliders, banners, gallery, faq)
-- +----------------------------------------------------------------------------

-- 7.1 BLOG CATEGORIES
CREATE TABLE `sg_blog_categories` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(120) NOT NULL UNIQUE,
    `description` TEXT         NULL,
    `status`      TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_bcat_slug` (`slug`),
    INDEX `idx_bcat_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.2 BLOGS
CREATE TABLE `sg_blogs` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id`     INT UNSIGNED NULL,
    `author_id`       INT UNSIGNED NULL,
    `title`           VARCHAR(200) NOT NULL,
    `slug`            VARCHAR(220) NOT NULL UNIQUE,
    `excerpt`         TEXT         NULL,
    `content`         LONGTEXT     NULL,
    `featured_image`  VARCHAR(255) NULL,
    `images`          JSON         NULL,
    `sg_tags`            TEXT         NULL,
    `meta_title`      VARCHAR(191) NULL,
    `meta_description` VARCHAR(255) NULL,
    `meta_keywords`   VARCHAR(255) NULL,
    `is_featured`     TINYINT(1)   NOT NULL DEFAULT 0,
    `is_published`    TINYINT(1)   NOT NULL DEFAULT 0,
    `published_at`    DATETIME     NULL,
    `views_count`     INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_blog_category` (`category_id`),
    INDEX `idx_blog_author` (`author_id`),
    INDEX `idx_blog_slug` (`slug`),
    INDEX `idx_blog_featured` (`is_featured`),
    INDEX `idx_blog_published` (`is_published`, `published_at`),
    FULLTEXT `ft_blog_search` (`title`, `excerpt`, `content`),
    CONSTRAINT `fk_blog_category` FOREIGN KEY (`category_id`) REFERENCES `sg_blog_categories`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_blog_author`   FOREIGN KEY (`author_id`)   REFERENCES `sg_users`(`id`)          ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.3 PAGES (static CMS pages)
CREATE TABLE `sg_pages` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`           VARCHAR(200) NOT NULL,
    `slug`            VARCHAR(220) NOT NULL UNIQUE,
    `content`         LONGTEXT     NULL,
    `meta_title`      VARCHAR(191) NULL,
    `meta_description` VARCHAR(255) NULL,
    `meta_keywords`   VARCHAR(255) NULL,
    `status`          TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`      INT          NOT NULL DEFAULT 0,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_page_slug` (`slug`),
    INDEX `idx_page_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.4 SLIDERS (hero/homepage banners)
CREATE TABLE `sg_sliders` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`           VARCHAR(200) NULL,
    `subtitle`        VARCHAR(500) NULL,
    `description`     TEXT         NULL,
    `image`           VARCHAR(255) NOT NULL,
    `image_mobile`    VARCHAR(255) NULL,
    `link`            VARCHAR(255) NULL,
    `link_text`       VARCHAR(100) NULL,
    `button_color`    VARCHAR(20)  NULL,
    `text_color`      VARCHAR(20)  NULL,
    `overlay_color`   VARCHAR(20)  NULL,
    `overlay_opacity` DECIMAL(3,2) NULL DEFAULT 0.00,
    `sort_order`      INT          NOT NULL DEFAULT 0,
    `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
    `start_at`        DATETIME     NULL,
    `end_at`          DATETIME     NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slider_active` (`is_active`),
    INDEX `idx_slider_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.5 BANNERS (promotional banners anywhere on site)
CREATE TABLE `sg_banners` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`          VARCHAR(100) NOT NULL,
    `title`         VARCHAR(200) NULL,
    `description`   TEXT         NULL,
    `image`         VARCHAR(255) NOT NULL,
    `link`          VARCHAR(255) NULL,
    `position`      VARCHAR(50)  NOT NULL DEFAULT 'sidebar',
    `target`        ENUM('_self','_blank') NOT NULL DEFAULT '_self',
    `sort_order`    INT          NOT NULL DEFAULT 0,
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `start_at`      DATETIME     NULL,
    `end_at`        DATETIME     NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_banner_position` (`position`),
    INDEX `idx_banner_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.6 FAQ CATEGORIES
CREATE TABLE `sg_faq_categories` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(120) NOT NULL UNIQUE,
    `description` TEXT         NULL,
    `icon`        VARCHAR(50)  NULL,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `status`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_faqcat_slug` (`slug`),
    INDEX `idx_faqcat_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.7 FAQS
CREATE TABLE `sg_faq` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NULL,
    `question`    TEXT         NOT NULL,
    `answer`      LONGTEXT     NOT NULL,
    `is_featured` TINYINT(1)   NOT NULL DEFAULT 0,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `status`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_faq_category` (`category_id`),
    INDEX `idx_faq_featured` (`is_featured`),
    INDEX `idx_faq_status` (`status`),
    FULLTEXT `ft_faq_search` (`question`, `answer`),
    CONSTRAINT `fk_faq_category` FOREIGN KEY (`category_id`) REFERENCES `sg_faq_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.8 GALLERY CATEGORIES
CREATE TABLE `sg_gallery_categories` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(120) NOT NULL UNIQUE,
    `description` TEXT         NULL,
    `cover_image` VARCHAR(255) NULL,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `status`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_galcat_slug` (`slug`),
    INDEX `idx_galcat_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.9 GALLERY
CREATE TABLE `sg_gallery` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id`  INT UNSIGNED NULL,
    `title`        VARCHAR(200) NULL,
    `description`  TEXT         NULL,
    `image`        VARCHAR(255) NOT NULL,
    `thumbnail`    VARCHAR(255) NULL,
    `alt_text`     VARCHAR(191) NULL,
    `type`         ENUM('image','video','youtube','vimeo') NOT NULL DEFAULT 'image',
    `video_url`    VARCHAR(255) NULL,
    `filesize`     INT UNSIGNED NULL,
    `width`        INT UNSIGNED NULL,
    `height`       INT UNSIGNED NULL,
    `is_featured`  TINYINT(1)   NOT NULL DEFAULT 0,
    `sort_order`   INT          NOT NULL DEFAULT 0,
    `status`       TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_gallery_category` (`category_id`),
    INDEX `idx_gallery_type` (`type`),
    INDEX `idx_gallery_featured` (`is_featured`),
    INDEX `idx_gallery_status` (`status`),
    CONSTRAINT `fk_gallery_category` FOREIGN KEY (`category_id`) REFERENCES `sg_gallery_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- +----------------------------------------------------------------------------
-- | SECTION 8 – NAVIGATION & SEO
-- +----------------------------------------------------------------------------

-- 8.1 MENUS
CREATE TABLE `sg_menus` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(100) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `location`    VARCHAR(50)  NOT NULL DEFAULT 'primary',
    `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_menu_location` (`location`),
    INDEX `idx_menu_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8.2 MENU ITEMS (recursive for nested menus)
CREATE TABLE `sg_menu_items` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `menu_id`         INT UNSIGNED NOT NULL,
    `parent_id`       INT UNSIGNED NULL,
    `title`           VARCHAR(100) NOT NULL,
    `url`             VARCHAR(255) NULL,
    `route`           VARCHAR(100) NULL,
    `icon`            VARCHAR(50)  NULL,
    `target`          ENUM('_self','_blank') NOT NULL DEFAULT '_self',
    `type`            ENUM('link','page','category','product','custom') NOT NULL DEFAULT 'link',
    `reference_id`    INT UNSIGNED NULL,
    `sort_order`      INT          NOT NULL DEFAULT 0,
    `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_mitem_menu` (`menu_id`),
    INDEX `idx_mitem_parent` (`parent_id`),
    INDEX `idx_mitem_active` (`is_active`),
    CONSTRAINT `fk_mitem_menu`   FOREIGN KEY (`menu_id`)   REFERENCES `sg_menus`(`id`)       ON DELETE CASCADE,
    CONSTRAINT `fk_mitem_parent` FOREIGN KEY (`parent_id`) REFERENCES `sg_menu_items`(`id`)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8.3 SEO (flexible metadata for any entity)
CREATE TABLE `sg_seo` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `entity_type`     VARCHAR(50)  NOT NULL,
    `entity_id`       INT UNSIGNED NOT NULL,
    `title`           VARCHAR(191) NULL,
    `description`     VARCHAR(255) NULL,
    `keywords`        TEXT         NULL,
    `og_title`        VARCHAR(191) NULL,
    `og_description`  VARCHAR(255) NULL,
    `og_image`        VARCHAR(255) NULL,
    `og_type`         VARCHAR(50)  NULL DEFAULT 'website',
    `twitter_title`   VARCHAR(191) NULL,
    `twitter_description` VARCHAR(255) NULL,
    `twitter_image`   VARCHAR(255) NULL,
    `canonical_url`   VARCHAR(255) NULL,
    `no_index`        TINYINT(1)   NOT NULL DEFAULT 0,
    `no_follow`       TINYINT(1)   NOT NULL DEFAULT 0,
    `structured_data` JSON         NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_seo_entity` (`entity_type`, `entity_id`),
    INDEX `idx_seo_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- +----------------------------------------------------------------------------
-- | SECTION 9 – SYSTEM & SETTINGS
-- +----------------------------------------------------------------------------

-- 9.1 SETTINGS (key-value with groups)
CREATE TABLE `sg_settings` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`         VARCHAR(100) NOT NULL UNIQUE,
    `value`       LONGTEXT     NULL,
    `group`       VARCHAR(50)  NOT NULL DEFAULT 'general',
    `type`        ENUM('text','textarea','number','boolean','email','url','image','color','select','json') NOT NULL DEFAULT 'text',
    `options`     JSON         NULL,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `is_public`   TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_setting_key` (`key`),
    INDEX `idx_setting_group` (`group`),
    INDEX `idx_setting_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9.2 ACTIVITY LOGS (user activity)
CREATE TABLE `sg_activity_logs` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED NULL,
    `customer_id` INT UNSIGNED NULL,
    `type`        VARCHAR(50)  NOT NULL,
    `action`      VARCHAR(100) NOT NULL,
    `description` TEXT         NOT NULL,
    `entity_type` VARCHAR(50)  NULL,
    `entity_id`   INT UNSIGNED NULL,
    `old_values`  JSON         NULL,
    `new_values`  JSON         NULL,
    `ip_address`  VARCHAR(45)  NULL,
    `user_agent`  TEXT         NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_actlog_user` (`user_id`),
    INDEX `idx_actlog_customer` (`customer_id`),
    INDEX `idx_actlog_type` (`type`),
    INDEX `idx_actlog_action` (`action`),
    INDEX `idx_actlog_entity` (`entity_type`, `entity_id`),
    INDEX `idx_actlog_created` (`created_at`),
    CONSTRAINT `fk_actlog_user`     FOREIGN KEY (`user_id`)     REFERENCES `sg_users`(`id`)     ON DELETE SET NULL,
    CONSTRAINT `fk_actlog_customer` FOREIGN KEY (`customer_id`) REFERENCES `sg_customers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9.3 ADMIN LOGS (dedicated admin audit trail)
CREATE TABLE `sg_admin_logs` (
    `id`            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id`      INT UNSIGNED NOT NULL,
    `action`        VARCHAR(100) NOT NULL,
    `description`   TEXT         NOT NULL,
    `entity_type`   VARCHAR(50)  NULL,
    `entity_id`     INT UNSIGNED NULL,
    `old_values`    JSON         NULL,
    `new_values`    JSON         NULL,
    `ip_address`    VARCHAR(45)  NULL,
    `user_agent`    TEXT         NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_adminlog_admin` (`admin_id`),
    INDEX `idx_adminlog_action` (`action`),
    INDEX `idx_adminlog_entity` (`entity_type`, `entity_id`),
    INDEX `idx_adminlog_created` (`created_at`),
    CONSTRAINT `fk_adminlog_admin` FOREIGN KEY (`admin_id`) REFERENCES `sg_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9.4 PASSWORD RESETS
CREATE TABLE `sg_password_resets` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email`      VARCHAR(191) NOT NULL,
    `token`      VARCHAR(255) NOT NULL,
    `expires_at` DATETIME     NOT NULL,
    `used`       TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pwr_email` (`email`),
    INDEX `idx_pwr_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9.5 SESSIONS (for database session driver)
CREATE TABLE `sg_sessions` (
    `id`            VARCHAR(128) NOT NULL PRIMARY KEY,
    `user_id`       INT UNSIGNED NULL,
    `ip_address`    VARCHAR(45)  NULL,
    `user_agent`    TEXT         NULL,
    `payload`       LONGTEXT     NOT NULL,
    `last_activity` INT UNSIGNED NOT NULL,
    INDEX `idx_sess_user` (`user_id`),
    INDEX `idx_sess_last` (`last_activity`),
    CONSTRAINT `fk_sess_user` FOREIGN KEY (`user_id`) REFERENCES `sg_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- END OF SCHEMA
-- =============================================================================
