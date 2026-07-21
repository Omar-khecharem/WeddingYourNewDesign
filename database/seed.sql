-- =============================================================================
-- WEDDINGYOUR — SEED DATA (Indian Bengali Wedding Context)
-- =============================================================================

-- Database: u514769775_wedding_your (select in phpMyAdmin before importing)

-- +----------------------------------------------------------------------------
-- 1. COUNTRIES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_countries` (`id`, `name`, `iso_code_2`, `iso_code_3`, `phone_code`, `currency_code`, `currency_symbol`, `locale`, `status`, `sort_order`) VALUES
(1, 'India',          'IN', 'IND', '+91', 'INR', '₹', 'en_IN', 1, 1),
(2, 'Bangladesh',     'BD', 'BGD', '+880','BDT', '৳', 'bn_BD', 1, 2),
(3, 'Nepal',          'NP', 'NPL', '+977','NPR', 'रू', 'ne_NP', 1, 3),
(4, 'United States',  'US', 'USA', '+1',  'USD', '$',  'en_US', 1, 4),
(5, 'United Kingdom', 'GB', 'GBR', '+44', 'GBP', '£',  'en_GB', 1, 5),
(6, 'Canada',         'CA', 'CAN', '+1',  'CAD', 'C$', 'en_CA', 1, 6),
(7, 'Australia',      'AU', 'AUS', '+61', 'AUD', 'A$', 'en_AU', 1, 7),
(8, 'UAE',            'AE', 'ARE', '+971','AED', 'د.إ','en_AE', 1, 8);

-- +----------------------------------------------------------------------------
-- 2. CURRENCIES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_currencies` (`id`, `name`, `code`, `symbol`, `symbol_position`, `decimal_places`, `decimal_sep`, `thousand_sep`, `exchange_rate`, `is_default`, `status`) VALUES
(1, 'Indian Rupee',  'INR', '₹', 'before', 2, '.', ',', 1.000000, 1, 1),
(2, 'Bangladeshi Taka','BDT','৳','before',2, '.', ',', 1.090000, 0, 1),
(3, 'US Dollar',     'USD', '$', 'before', 2, '.', ',', 0.012000, 0, 1),
(4, 'Pound Sterling','GBP', '£', 'before', 2, '.', ',', 0.009500, 0, 1);

-- +----------------------------------------------------------------------------
-- 3. LANGUAGES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_languages` (`id`, `name`, `code`, `locale`, `direction`, `is_default`, `status`, `sort_order`) VALUES
(1, 'English', 'en', 'en_IN', 'ltr', 1, 1, 1),
(2, 'বাংলা',   'bn', 'bn_IN', 'ltr', 0, 1, 2),
(3, 'हिन्दी',   'hi', 'hi_IN', 'ltr', 0, 1, 3);

-- +----------------------------------------------------------------------------
-- 4. ROLES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_roles` (`id`, `name`, `slug`, `description`, `is_system`) VALUES
(1, 'Admin',  'admin',  'Administrator – full backend access', 1),
(2, 'User',   'user',   'Registered customer – frontend access only', 1);

-- +----------------------------------------------------------------------------
-- 5. PERMISSIONS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_permissions` (`id`, `name`, `slug`, `group`) VALUES
(1,  'View Dashboard',         'dashboard.view',       'dashboard'),
(2,  'Manage Products',        'products.manage',      'products'),
(3,  'Create Products',        'products.create',      'products'),
(4,  'Edit Products',          'products.edit',        'products'),
(5,  'Delete Products',        'products.delete',      'products'),
(6,  'Manage Categories',      'categories.manage',    'categories'),
(7,  'Manage Orders',          'orders.manage',        'orders'),
(8,  'View Orders',            'orders.view',          'orders'),
(9,  'Edit Orders',            'orders.edit',          'orders'),
(10, 'Manage Customers',       'customers.manage',     'customers'),
(11, 'Manage Coupons',         'coupons.manage',       'marketing'),
(12, 'Manage Users',           'users.manage',         'users'),
(13, 'Manage Settings',        'settings.manage',      'settings'),
(14, 'View Reports',           'reports.view',         'reports'),
(15, 'Manage Content',         'content.manage',       'content'),
(16, 'Manage Reviews',         'reviews.manage',       'reviews'),
(17, 'Export Data',            'export.data',          'system'),
(18, 'View Logs',              'logs.view',            'system');

-- +----------------------------------------------------------------------------
-- 6. ROLE-PERMISSION
-- +----------------------------------------------------------------------------
INSERT INTO `sg_role_permission` (`role_id`, `permission_id`) VALUES
(1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18);

-- +----------------------------------------------------------------------------
-- 7. USERS (passwords: admin123 / user123)
-- +----------------------------------------------------------------------------
INSERT INTO `sg_users` (`id`, `role_id`, `name`, `email`, `phone`, `password`, `status`, `locale`) VALUES
(1, 1, 'Admin',     'admin@weddingyour.com', '+91 1234567890', '$2y$12$QKeit8Z1Rfxy2ijYUmFc6Ohx0S9J3EnxJ.ylznTLws0mowWDpKt2m', 1, 'en'),
(2, 2, 'Priya Sharma', 'priya@example.com', '+91 98765 43210', '$2y$12$7sP5g6o539hKbs6C./63Geky24/28kB7PrBLMh/KTM3.e5dUd/9/e', 1, 'en');

-- +----------------------------------------------------------------------------
-- 8. CUSTOMERS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_customers` (`id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `newsletter`, `status`) VALUES
(1, 2, 'Priya', 'Sharma', 'priya@example.com', '+91 98765 43210', 1, 1);

-- +----------------------------------------------------------------------------
-- 9. CATEGORIES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_categories` (`id`, `parent_id`, `name`, `slug`, `description`, `sort_order`, `featured`, `status`) VALUES
(1, NULL, 'BABY',              'baby',              'Traditional mukut and topor for babies',                           1, 1, 1),
(2, NULL, 'BRIDE',             'bride',             'Bridal mukut, patashi, crowns and wedding accessories for the bride',2, 1, 1),
(3, NULL, 'GROOM',             'groom',             'Topor, dorpon and traditional items for the groom',                  3, 1, 1),
(4, NULL, 'WEDDING ITEMS',     'wedding-items',     'Complete wedding accessories including panpata, piri and more',       4, 1, 1),
(5, NULL, "SHOLA'S JEWELLERY", 'sholas-jewellery',  'Handcrafted shola jewellery including matha patti and mini crowns',   5, 0, 1);

-- +----------------------------------------------------------------------------
-- 10. SUBCATEGORIES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_subcategories` (`id`, `category_id`, `name`, `slug`, `sort_order`, `status`) VALUES
-- BRIDE (category_id = 2)
(1,  2, 'Bridal Patashi / Mukut',  'bridal-patashi-mukut',  1, 1),
(2,  2, 'Sithi / Small Mukut',     'sithi-small-mukut',     2, 1),
(3,  2, 'Crown & 3 Pieces Set',    'crown-3-pieces-set',    3, 1),
(4,  2, 'Boron / Khoidan Kulo',    'boron-khoidan-kulo',    4, 1),
(5,  2, 'Gach kouto',              'gach-kouto',            5, 1),
(6,  2, 'Panpata',                 'bride-panpata',         6, 1),
(7,  2, 'Piri',                    'bride-piri',            7, 1),
-- GROOM (category_id = 3)
(8,  3, 'Topor Mukut Set',      'topor-mukut-set',      1, 1),
(9,  3, 'Topor (Without Mukut)', 'topor-without-mukut',  2, 1),
(10, 3, 'Dorpon',                'groom-dorpon',         3, 1),
(11, 3, 'Kunke',                 'groom-kunke',          4, 1),
(12, 3, 'Boron / Khoidan Kulo',  'groom-boron-khoidan',  5, 1),
-- BABY (category_id = 1)
(13, 1, 'Baby Topor (Boy)',   'baby-topor-boy',   1, 1),
(14, 1, 'Baby Mukut (Girl)',  'baby-mukut-girl',  2, 1),
(15, 1, 'Nitbor Topor (Boy)', 'nitbor-topor-boy', 3, 1),
-- WEDDING ITEMS (category_id = 4)
(16, 4, 'Panpata',              'wedding-panpata',       1, 1),
(17, 4, 'Piri',                 'wedding-piri',          2, 1),
(18, 4, 'Boron / Khoidan Kulo', 'wedding-boron-khoidan', 3, 1),
(19, 4, 'Tattwa Suchi',         'tattwa-suchi',          4, 1),
(20, 4, 'Tattwa Tray',          'tattwa-tray',           5, 1),
(21, 4, 'Haldi Platter',        'haldi-platter',         6, 1),
(22, 4, 'Haldi Jewellery',      'haldi-jewellery',       7, 1),
-- SHOLA'S JEWELLERY (category_id = 5)
(23, 5, 'Matha Patti',            'matha-patti',               1, 1),
(24, 5, 'Mini Crown',             'mini-crown',                2, 1),
(25, 5, 'Bridal Sithi / Small Mukut', 'shola-sithi-small',     3, 1),
(26, 5, 'Crown & 3 Pieces Set',   'shola-crown-3-pieces',     4, 1);

-- +----------------------------------------------------------------------------
-- 11. BRANDS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_brands` (`id`, `name`, `slug`, `description`, `featured`, `status`) VALUES
(1, 'WeddingYour',    'weddingyour',    'Premium WeddingYour brand',       1, 1),
(2, 'Artisan Bengal', 'artisan-bengal', 'Bengal artisans cooperative',   0, 1),
(3, 'Traditional Luxe', 'trad-luxe', 'Traditional luxury collection',   1, 1);

-- +----------------------------------------------------------------------------
-- 12. PRODUCTS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_products` (`id`, `category_id`, `subcategory_id`, `brand_id`, `name`, `slug`, `short_description`, `description`, `sku`, `regular_price`, `sale_price`, `discount_percent`, `stock_quantity`, `stock_status`, `is_featured`, `is_new`, `is_trending`, `is_bestseller`, `rating_avg`, `rating_count`, `status`) VALUES
(1, 2, 1, 1, 'Mukut Noyoni',         'mukut-noyoni',         'Handmade beautiful bridal Noyoni mukut',            '<p>Premium quality Noyoni Mukut crafted from traditional Sholapith. Perfect for brides who want to look elegant on their special day.</p>',         'NM-001', 1499.00, 799.00, 47, 25, 'in_stock', 1, 1, 1, 1, 4.80, 24, 1),
(2, 2, 2, 1, 'Mukut Poddosree',      'mukut-poddosree',      'Exclusive bridal Poddosree mukut',                  '<p>Poddosree Mukut with traditional shola work. A perfect blend of tradition and elegance for your wedding day.</p>',                              'PM-001', 1499.00, 799.00, 47, 20, 'in_stock', 1, 1, 1, 0, 4.50, 18, 1),
(3, 2, 3, 2, 'Mukut Purna',          'mukut-purna',          'Premium Purna mukut set',                           '<p>Complete Purna Mukut set for the bride. Traditional shola work with golden accents for a royal look.</p>',                                       'PM-002', 1299.00, 799.00, 38, 15, 'in_stock', 1, 0, 1, 0, 4.30, 12, 1),
(4, 2, 1, 1, 'Mukut Lili',           'mukut-lili',           'Elegant Lili mukut for the bride',                  '<p>Beautiful Lili Mukut with delicate shola work. Lightweight and comfortable for all-day wear.</p>',                                                'LM-001', 1399.00, 799.00, 43, 30, 'in_stock', 1, 1, 1, 1, 4.60, 15, 1),
(5, 3, 8, 1, 'Traditional Topor',    'traditional-topor',    'Classic traditional topor for groom',               '<p>Traditional Bengali Topor for the groom. Handcrafted using premium quality sholapith with intricate detailing.</p>',                              'TT-001', 999.00, 599.00, 40, 35, 'in_stock', 1, 0, 1, 1, 4.90, 30, 1),
(6, 3, 9, 3, 'Royal Topor Set',      'royal-topor-set',      'Premium royal topor set with accessories',          '<p>Complete royal Topor set for the groom with matching accessories. Perfect for the traditional Bengali wedding.</p>',                              'RT-001', 1999.00, 1299.00, 35, 10, 'in_stock', 1, 1, 0, 0, 5.00, 8,  1),
(7, 1, 13, 1, 'Baby Mukut',          'baby-mukut',           'Traditional mukut for baby boy',                    '<p>Beautiful baby Mukut crafted from premium sholapith. Perfect for traditional ceremonies and annaprashan.</p>',                                    'BM-001', 899.00, 499.00, 45, 50, 'in_stock', 1, 1, 1, 0, 4.70, 22, 1),
(8, 1, 14, 3, 'Premium Baby Mukut',  'premium-baby-mukut',   'Premium baby mukut with golden accents',            '<p>Premium baby Mukut with intricate shola work and golden detailing for special occasions.</p>',                                                   'BM-002', 1299.00, 799.00, 38, 20, 'in_stock', 0, 0, 0, 0, 4.40, 9,  1),
(9, 4, 16, 1, 'Wedding Mukut & Topor Set','wedding-mukut-topor-set','Complete wedding set with mukut and topor','<p>Complete wedding set including bridal Mukut and groom Topor. Perfect matching set for the couple on their big day.</p>',                       'WM-001', 2499.00, 1499.00, 40, 15, 'in_stock', 1, 1, 1, 1, 4.80, 16, 1),
(10,5, 23, 2, 'Gachhkouto Set',      'gachhkouto-set',       'Traditional Gachhkouto set',                        '<p>Traditional Gachhkouto set handcrafted from premium materials. Essential for Bengali wedding ceremonies.</p>',                                   'GS-001', 1799.00, 999.00, 44, 10, 'in_stock', 0, 0, 0, 0, 4.20, 5,  1);

-- +----------------------------------------------------------------------------
-- 12b. BEST DEALS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_deals` (`id`, `image`, `is_active`) VALUES
(1, '', 1),
(2, '', 1),
(3, '', 1);

-- +----------------------------------------------------------------------------
-- 13. PRODUCT IMAGES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_product_images` (`product_id`, `image`, `alt_text`, `is_primary`, `sort_order`) VALUES
(1, 'noyoni-mukut-1.jpg',    'Mukut Noyoni – Front View',  1, 0),
(2, 'poddosree-mukut-1.jpg', 'Mukut Poddosree – Front View',1, 0),
(3, 'purna-mukut-1.jpg',     'Mukut Purna – Front View',   1, 0),
(4, 'lili-mukut-1.jpg',      'Mukut Lili – Front View',    1, 0),
(5, 'topor-1.jpg',           'Topor – Front View',         1, 0),
(6, 'royal-topor-1.jpg',     'Royal Topor Set',            1, 0),
(7, 'baby-mukut-1.jpg',      'Baby Mukut – Front View',    1, 0),
(8, 'premium-baby-mukut-1.jpg','Premium Baby Mukut',        1, 0),
(9, 'wedding-set-1.jpg',     'Wedding Mukut & Topor Set',  1, 0),
(10,'gachhkouto-1.jpg',      'Gachhkouto Set',             1, 0);

-- +----------------------------------------------------------------------------
-- 14. TAGS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_tags` (`id`, `name`, `slug`) VALUES
(1, 'Best Seller',   'best-seller'),
(2, 'New Arrival',   'new-arrival'),
(3, 'Limited Edition','limited-edition'),
(4, 'Handmade',      'handmade'),
(5, 'Premium',       'premium');

-- +----------------------------------------------------------------------------
-- 15. PRODUCT-TAG
-- +----------------------------------------------------------------------------
INSERT INTO `sg_product_tag` (`product_id`, `tag_id`) VALUES
(1,1),(1,4),(2,1),(2,4),(3,1),(4,2),(4,4),(5,1),(5,4),
(6,3),(6,5),(7,2),(7,4),(8,5),(9,1),(9,5),(10,4);

-- +----------------------------------------------------------------------------
-- 16. COUPONS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_coupons` (`id`, `code`, `type`, `value`, `min_order_amount`, `max_discount`, `usage_limit`, `description`, `is_active`) VALUES
(1, 'WELCOME10', 'percentage', 10, 0,    300.00, 500, 'Welcome – 10% off for new customers',      1),
(2, 'WEDDING20', 'percentage', 20, 999.00, 500.00, 100, '20% OFF on Mukut & Topor sets',            1),
(3, 'FREESHIP',  'fixed',      49, 499.00, 49.00,  200, 'Free standard shipping on orders above ₹499',1);

-- +----------------------------------------------------------------------------
-- 17. ADDRESSES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_addresses` (`id`, `user_id`, `customer_id`, `type`, `label`, `full_name`, `phone`, `address_line1`, `city`, `state`, `postal_code`, `country_id`, `is_default`) VALUES
(1, 2, 1, 'both', 'Home', 'Priya Sharma', '+91 98765 43210', '45 Ballygunge Circular Road', 'Kolkata', 'West Bengal', '700019', 1, 1);

-- +----------------------------------------------------------------------------
-- 18. SHIPPING METHODS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_shipping_methods` (`id`, `name`, `slug`, `description`, `base_rate`, `free_above`, `estimated_days_min`, `estimated_days_max`, `has_tracking`, `status`, `sort_order`) VALUES
(1, 'Standard',   'standard',   'Standard shipping – 5 to 7 days',      49.00,  500.00, 5, 7,  0, 1, 1),
(2, 'Express',    'express',    'Express shipping – 2 to 3 days',      99.00,  999.00, 2, 3,  1, 1, 2),
(3, 'Free Shipping','free-shipping','Free shipping – 7 to 10 days',     0.00,  0.00,   7, 10, 0, 1, 3);

-- +----------------------------------------------------------------------------
-- 19. PAYMENT METHODS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_payment_methods` (`id`, `name`, `slug`, `description`, `is_online`, `fee_type`, `status`, `sort_order`) VALUES
(1, 'Cash on Delivery',   'cod',           'Pay when you receive your order',           0, 'fixed',      1, 1),
(2, 'UPI',                'upi',           'Pay via Google Pay, PhonePe, Paytm',         1, 'percentage', 1, 2),
(3, 'Net Banking',        'net-banking',   'Pay via your bank account',                  1, 'percentage', 1, 3),
(4, 'Debit / Credit Card','card',          'Pay via Visa, Mastercard, RuPay',            1, 'percentage', 1, 4);

-- +----------------------------------------------------------------------------
-- 20. REVIEWS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_reviews` (`product_id`, `user_id`, `customer_id`, `name`, `email`, `rating`, `comment`, `is_verified`, `is_approved`) VALUES
(1, 2, 1, 'Priya Sharma',  'priya@example.com',  5, 'The collections are beautiful! The staff was very helpful in choosing the perfect mukut. Highly recommended!', 1, 1),
(2, 2, 1, 'Priya Sharma',  'priya@example.com',  5, 'Wonderful experience. The traditional shola art is perfectly preserved here.',  1, 1),
(5, 2, 1, 'Priya Sharma',  'priya@example.com',  5, 'Best place to buy authentic Topor. Excellent finishing and reasonable prices.', 1, 1);

-- +----------------------------------------------------------------------------
-- 21. BLOG CATEGORIES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_blog_categories` (`id`, `name`, `slug`, `description`, `status`, `sort_order`) VALUES
(1, 'Wedding Tips',    'wedding-tips',    'Tips and advice for your wedding',      1, 1),
(2, 'Traditions',      'traditions',      'Articles about Bengali traditions',     1, 2),
(3, 'Trends',          'trends',          'Latest trends in wedding accessories',  1, 3);

-- +----------------------------------------------------------------------------
-- 22. BLOGS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_blogs` (`id`, `category_id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `is_published`, `published_at`) VALUES
(1, 1, 1, 'How to Choose Your Bridal Mukut', 'how-to-choose-bridal-mukut', 'Complete guide to choosing the perfect mukut for your wedding.', '<p>The mukut is the most important accessory for a Bengali bride. Here is how to choose the one that suits you best.</p>', 1, NOW()),
(2, 2, 1, 'The Art of Sholapith in Bengal', 'art-of-sholapith-bengal', 'Discover the ancient art of sholapith craftwork.', '<p>Sholapith is a traditional art form from West Bengal that dates back centuries. Our artisans preserve this heritage.</p>', 1, NOW()),
(3, 3, 1, '2026 Trends: Mukut and Topor', '2026-trends-mukut-topor', 'Current trends in traditional mukut and topor designs.', '<p>Discover this year\'s trending styles for traditional wedding accessories blending heritage with contemporary aesthetics.</p>', 1, NOW());

-- +----------------------------------------------------------------------------
-- 23. PAGES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_pages` (`id`, `title`, `slug`, `content`, `status`, `sort_order`) VALUES
(1, 'About Us',          'about',           '<h2>Welcome to WeddingYour</h2><p>WeddingYour is India\'s premier destination for handcrafted Mukut, Topor and Sholapith wedding accessories. Based in Kolkata, West Bengal, we bring generations of artisanal craftsmanship to your special day.</p>', 1, 1),
(2, 'Shipping & Returns', 'shipping-returns','<h2>Shipping Policy</h2><p>We ship across India with free shipping on orders above ₹500.</p><h2>Returns</h2><p>Returns accepted within 7 days of delivery in original condition.</p>', 1, 2),
(3, 'Privacy Policy',    'privacy',         '<h2>Privacy Policy</h2><p>Your data is protected and never shared with third parties.</p>', 1, 3),
(4, 'Terms & Conditions','terms',           '<h2>Terms & Conditions</h2><p>Please read our terms and conditions carefully before placing an order.</p>', 1, 4);

-- +----------------------------------------------------------------------------
-- 25. BANNERS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_banners` (`id`, `name`, `title`, `description`, `image`, `link`, `position`, `is_active`) VALUES
(1, 'Summer Sale Banner',    'Flat 20% Off',     'Discount on all mukut', 'banner-sale.jpg',   '/products?on_sale=1', 'home_top', 1),
(2, 'Free Shipping Banner',  'Free Shipping',    'On orders above ₹500',  'banner-shipping.jpg','', 'sidebar', 1),
(3, 'Hero Left Wedding',     'Mukut Collection',    '', '', '/products?category=mukut-mariee', 'hero_left', 1),
(4, 'Hero Right Topor',      'Topor Sets',          '', '', '/products?category=topor-marie', 'hero_right', 1);

-- +----------------------------------------------------------------------------
-- 26. FAQ CATEGORIES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_faq_categories` (`id`, `name`, `slug`, `sort_order`, `status`) VALUES
(1, 'Orders',     'orders',     1, 1),
(2, 'Shipping',   'shipping',   2, 1),
(3, 'Products',   'products',   3, 1),
(4, 'Returns',    'returns',    4, 1);

-- +----------------------------------------------------------------------------
-- 27. FAQ
-- +----------------------------------------------------------------------------
INSERT INTO `sg_faq` (`id`, `category_id`, `question`, `answer`, `sort_order`, `status`) VALUES
(1, 1, 'How do I place an order?',         'Add items to your cart and follow the checkout steps to complete your order.', 1, 1),
(2, 2, 'What are the delivery times?',     'Standard shipping: 5-7 days. Express shipping: 2-3 days across India.',      1, 1),
(3, 3, 'Are the mukut handmade?',          'Yes, 100% handmade by our skilled artisans in West Bengal.',                  1, 1),
(4, 4, 'Can I return an item?',            'Yes, within 7 days of delivery in unused condition.',                          1, 1);

-- +----------------------------------------------------------------------------
-- 28. GALLERY CATEGORIES
-- +----------------------------------------------------------------------------
INSERT INTO `sg_gallery_categories` (`id`, `name`, `slug`, `sort_order`, `status`) VALUES
(1, 'Weddings',    'weddings',    1, 1),
(2, 'Products',    'products',    2, 1),
(3, 'Craftsmanship','craftsmanship', 3, 1);

-- +----------------------------------------------------------------------------
-- 29. GALLERY
-- +----------------------------------------------------------------------------
INSERT INTO `sg_gallery` (`id`, `category_id`, `title`, `image`, `type`, `sort_order`, `status`) VALUES
(1, 1, 'Traditional Bengali Wedding',  'gallery-wedding-1.jpg',  'image', 1, 1),
(2, 1, 'Wedding Ceremony',             'gallery-wedding-2.jpg',  'image', 2, 1),
(3, 2, 'Mukut Collection',            'gallery-collection.jpg', 'image', 1, 1);

-- +----------------------------------------------------------------------------
-- 30. MENUS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_menus` (`id`, `name`, `slug`, `location`, `is_active`) VALUES
(1, 'Main Menu',        'main-menu',          'primary',            1),
(2, 'Footer Menu',      'footer-menu',        'footer',             1),
(3, 'Browse Categories','browse-categories',  'categories_dropdown', 1);

-- +----------------------------------------------------------------------------
-- 31. MENU ITEMS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `type`, `reference_id`, `sort_order`, `is_active`) VALUES
(1,  1, NULL, 'Home',           '/',                  'custom',   NULL, 1, 1),
(2,  1, NULL, 'Shop',           '/products',          'custom',   NULL, 2, 1),
(3,  1, 2,    'Bridal Mukut',   NULL,                 'category', 2,   1, 1),
(4,  1, 2,    'Groom Topor',    NULL,                 'category', 3,   2, 1),
(5,  1, 2,    'Baby Mukut',     NULL,                 'category', 1,   3, 1),
(6,  1, NULL, 'About',          '/about',             'page',     NULL, 3, 1),
(7,  1, NULL, 'Contact',        '/contact',           'custom',   NULL, 4, 1),
(8,  2, NULL, 'Terms & Conditions','/terms',          'page',     NULL, 1, 1),
(9,  2, NULL, 'Privacy Policy', '/privacy',           'page',     NULL, 2, 1),
(10, 2, NULL, 'Shipping',       '/shipping-returns',  'page',     NULL, 3, 1);

-- Browse Categories menu (menu_id = 3) - Parent items
INSERT INTO `sg_menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `type`, `reference_id`, `sort_order`, `is_active`) VALUES
(11, 3, NULL, 'BRIDE',             NULL, 'category', 2,  1, 1),
(12, 3, NULL, 'GROOM',             NULL, 'category', 3,  2, 1),
(13, 3, NULL, 'BABY',              NULL, 'category', 1,  3, 1),
(14, 3, NULL, 'WEDDING ITEMS',     NULL, 'category', 4,  4, 1),
(15, 3, NULL, "SHOLA'S JEWELLERY", NULL, 'category', 5,  5, 1),
(16, 3, NULL, 'Best Seller',       '/products?sort=popular', 'custom', NULL, 6, 1),
(17, 3, NULL, 'Exclusive',         '/products?featured=1',   'custom', NULL, 7, 1);

-- BRIDE sub-items (parent_id = 11)
INSERT INTO `sg_menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `type`, `reference_id`, `sort_order`, `is_active`) VALUES
(18, 3, 11, 'Bridal Patashi / Mukut',  NULL, 'category', 6,  1, 1),
(19, 3, 11, 'Sithi / Small Mukut',     NULL, 'category', 7,  2, 1),
(20, 3, 11, 'Crown & 3 Pieces Set',    NULL, 'category', 8,  3, 1),
(21, 3, 11, 'Boron / Khoidan Kulo',    NULL, 'category', 19, 4, 1),
(22, 3, 11, 'Gach kouto',              NULL, 'category', 24, 5, 1),
(23, 3, 11, 'Panpata',                 NULL, 'category', 17, 6, 1),
(24, 3, 11, 'Piri',                    NULL, 'category', 18, 7, 1);

-- GROOM sub-items (parent_id = 12)
INSERT INTO `sg_menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `type`, `reference_id`, `sort_order`, `is_active`) VALUES
(25, 3, 12, 'Topor Mukut Set',      NULL, 'category', 9,  1, 1),
(26, 3, 12, 'Topor (Without Mukut)', NULL, 'category', 10, 2, 1),
(27, 3, 12, 'Dorpon',               NULL, 'category', 11, 3, 1),
(28, 3, 12, 'Kunke',                NULL, 'category', 12, 4, 1),
(29, 3, 12, 'Boron / Khoidan Kulo', NULL, 'category', 13, 5, 1);

-- BABY sub-items (parent_id = 13)
INSERT INTO `sg_menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `type`, `reference_id`, `sort_order`, `is_active`) VALUES
(30, 3, 13, 'Baby Topor (Boy)',   NULL, 'category', 14, 1, 1),
(31, 3, 13, 'Baby Mukut (Girl)',  NULL, 'category', 15, 2, 1),
(32, 3, 13, 'Nitbor Topor (Boy)', NULL, 'category', 16, 3, 1);

-- WEDDING ITEMS sub-items (parent_id = 14)
INSERT INTO `sg_menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `type`, `reference_id`, `sort_order`, `is_active`) VALUES
(33, 3, 14, 'Panpata',              NULL, 'category', 17, 1, 1),
(34, 3, 14, 'Piri',                 NULL, 'category', 18, 2, 1),
(35, 3, 14, 'Boron / Khoidan Kulo', NULL, 'category', 19, 3, 1),
(36, 3, 14, 'Tattwa Suchi',         NULL, 'category', 20, 4, 1),
(37, 3, 14, 'Tattwa Tray',          NULL, 'category', 21, 5, 1),
(38, 3, 14, 'Haldi Platter',        NULL, 'category', 22, 6, 1),
(39, 3, 14, 'Haldi Jewellery',      NULL, 'category', 23, 7, 1);

-- SHOLA'S JEWELLERY sub-items (parent_id = 15)
INSERT INTO `sg_menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `type`, `reference_id`, `sort_order`, `is_active`) VALUES
(40, 3, 15, 'Matha Patti',            NULL, 'category', 24, 1, 1),
(41, 3, 15, 'Mini Crown',             NULL, 'category', 25, 2, 1),
(42, 3, 15, 'Bridal Sithi / Small Mukut', NULL, 'category', 26, 3, 1),
(43, 3, 15, 'Crown & 3 Pieces Set',   NULL, 'category', 27, 4, 1);

-- +----------------------------------------------------------------------------
-- 32. SEO
-- +----------------------------------------------------------------------------
INSERT INTO `sg_seo` (`entity_type`, `entity_id`, `title`, `description`, `og_title`, `og_description`) VALUES
('home',     0, 'WeddingYour – Bengali Wedding Marketplace | Topor, Mukut & Accessories', 'India\'s leading Bengali wedding marketplace for premium handmade Topor, Mukut, bridal accessories, and traditional Bengali wedding items.', 'WeddingYour – Wedding Mukut & Topor', 'Discover our exceptional collection of traditional mukut and topor, handcrafted by Bengal\'s finest artisans.'),
('page',    1, 'About Us – WeddingYour', 'Learn about WeddingYour\u2019s heritage in traditional sholapith craftsmanship', NULL, NULL),
('category',2, 'Bridal Mukut – WeddingYour', 'Collection of premium bridal mukut and patashi', NULL, NULL);

-- +----------------------------------------------------------------------------
-- 33. SETTINGS
-- +----------------------------------------------------------------------------
INSERT INTO `sg_settings` (`key`, `value`, `group`, `type`, `sort_order`, `is_public`) VALUES
('site_name',             'WeddingYour',                                                    'general',  'text',     1, 1),
('site_tagline',          'Your Complete Bengali Wedding Destination ❤️',                    'general',  'text',     2, 1),
('site_email',            'contact@weddingyour.com',                                         'general',  'email',    3, 1),
('site_phone',            '+91 1234567890',                                                  'general',  'text',     4, 1),
('site_address',          'Kolkata, West Bengal, India',                                     'general',  'textarea', 5, 1),
('contact_email',         'contact@weddingyour.com',                                         'general',  'email',    3, 1),
('contact_phone',         '+91 1234567890',                                                  'general',  'text',     4, 1),
('contact_address',       'Kolkata, West Bengal, India',                                     'general',  'textarea', 5, 1),
('whatsapp_number',       '911234567890',                                                    'social',   'text',     6, 1),
('whatsapp_link',         'https://wa.me/911234567890',                                      'social',   'url',      7, 1),
('social_facebook',       'https://facebook.com/weddingyour',                                'social',   'url',      8, 1),
('social_instagram',      'https://instagram.com/weddingyour',                               'social',   'url',      9, 1),
('social_youtube',        'https://youtube.com/@weddingyour',                                'social',   'url',      10,1),
('tax_rate',              '18',                                                              'tax',      'number',   11,0),
('tax_name',              'GST',                                                             'tax',      'text',     12,0),
('free_shipping_min',     '500',                                                             'shipping', 'number',   13,1),
('standard_shipping',     '49',                                                              'shipping', 'number',   14,1),
('express_shipping',      '99',                                                              'shipping', 'number',   15,1),
('meta_title',            'WeddingYour – Bengali Wedding Marketplace | Topor, Mukut & Accessories', 'seo', 'text', 16,1),
('meta_description',      'India\'s leading Bengali wedding marketplace for premium handmade Topor, Mukut, bridal accessories, and traditional Bengali wedding items.', 'seo', 'textarea', 17,1),
('meta_keywords',         'WeddingYour, Bengali Wedding, Bengali Wedding Planner, Bengali Wedding Photography, Bengali Wedding Dresses, Bengali Wedding Decor, Bengali Wedding Invitation, Bengali Wedding Venue, Bengali Bride, Bengali Groom, Bengali Marriage, topor, mukut, sholapith', 'seo', 'text', 18,1),
('currency',              '₹',                                                               'general',  'text',     19,1),
('currency_code',         'INR',                                                             'general',  'text',     20,1),
('site_logo',             'site_logo.png',                                                    'general',  'image',    21,1),
('site_favicon',          'site_favicon.png',                                                 'general',  'image',    22,1),
('header_coupon_code',    'WEDDING20',                                                       'general',  'text',     30,1),
('header_coupon_text',    'Get FLAT 20% OFF On Topor Mukut set. Use Code -',                 'general',  'text',     31,1),
('hero_title',            'Premium Bengali Wedding Mukut',                                   'general',  'text',     23,1),
('hero_subtitle',         'New Collection 2026',                                             'general',  'text',     24,1),
('hero_description',      'Discover our handcrafted bridal mukut collection for your Bengali wedding', 'general', 'textarea', 25,1),
('hero_button_text',      'Shop Now',                                                        'general',  'text',     26,1),
('hero_button_link',      '/products',                                                       'general',  'url',      27,1),
('maintenance_mode',      '0',                                                               'system',   'boolean',  28,0),
('store_open',            '1',                                                               'system',   'boolean',  29,0);

-- +----------------------------------------------------------------------------
-- 34. PASSWORD RESETS (none by default)
-- +----------------------------------------------------------------------------

-- +----------------------------------------------------------------------------
-- 35. NEWSLETTER
-- +----------------------------------------------------------------------------
INSERT INTO `sg_newsletter` (`id`, `email`, `name`, `is_active`) VALUES
(1, 'priya@example.com', 'Priya Sharma', 1);

-- +----------------------------------------------------------------------------
-- 36. CONTACT MESSAGES (sample)
-- +----------------------------------------------------------------------------
INSERT INTO `sg_contact_messages` (`id`, `name`, `email`, `subject`, `message`, `is_read`) VALUES
(1, 'Rahul Das', 'rahul@example.com', 'Question about delivery', 'Hi, what are the delivery times for a custom mukut? Thank you.', 0);

-- +----------------------------------------------------------------------------
-- END OF SEED DATA
-- +----------------------------------------------------------------------------
