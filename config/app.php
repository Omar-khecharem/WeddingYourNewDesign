<?php

define('APP_NAME', EnvLoader::get('APP_NAME', 'WeddingYour'));
define('APP_TAGLINE', EnvLoader::get('APP_TAGLINE', "It's your day you are the celebrity..."));
define('APP_URL', EnvLoader::get('APP_URL', 'http://localhost:8080'));
define('APP_ENV', EnvLoader::get('APP_ENV', 'development'));
define('APP_DEBUG', EnvLoader::get('APP_DEBUG', 'true') === 'true' || EnvLoader::get('APP_DEBUG', 'true') === '1');
define('APP_TIMEZONE', EnvLoader::get('APP_TIMEZONE', 'Asia/Kolkata'));
define('APP_CURRENCY', EnvLoader::get('APP_CURRENCY', '₹'));
define('APP_CURRENCY_CODE', EnvLoader::get('APP_CURRENCY_CODE', 'INR'));
define('APP_LOCALE', EnvLoader::get('APP_LOCALE', 'en'));

define('CONTACT_EMAIL', EnvLoader::get('CONTACT_EMAIL', 'contact@weddingyour.com'));
define('CONTACT_PHONE', EnvLoader::get('CONTACT_PHONE', '+91 1234567890'));
define('CONTACT_ADDRESS', EnvLoader::get('CONTACT_ADDRESS', 'Kolkata, West Bengal, India.'));
define('WHATSAPP_NUMBER', EnvLoader::get('WHATSAPP_NUMBER', '911234567890'));
define('WHATSAPP_LINK', EnvLoader::get('WHATSAPP_LINK', 'https://wa.me/911234567890'));

define('SOCIAL_FACEBOOK', EnvLoader::get('SOCIAL_FACEBOOK', '#'));
define('SOCIAL_INSTAGRAM', EnvLoader::get('SOCIAL_INSTAGRAM', '#'));
define('SOCIAL_YOUTUBE', EnvLoader::get('SOCIAL_YOUTUBE', '#'));

define('CSRF_TOKEN_NAME', EnvLoader::get('CSRF_TOKEN_NAME', '_csrf_token'));
define('SESSION_LIFETIME', (int) EnvLoader::get('SESSION_LIFETIME', 480));
define('PASSWORD_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_COST', 12);
define('MAX_LOGIN_ATTEMPTS', (int) EnvLoader::get('MAX_LOGIN_ATTEMPTS', 50));
define('LOGIN_LOCKOUT_TIME', (int) EnvLoader::get('LOGIN_LOCKOUT_TIME', 1));

define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
define('IMAGE_QUALITY', 80);

define('PAGINATION_PER_PAGE', 12);
define('PAGINATION_ADMIN_PER_PAGE', 20);

define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600);

define('TAX_RATE', (int) EnvLoader::get('TAX_RATE', 18));
define('TAX_NAME', EnvLoader::get('TAX_NAME', 'GST'));

define('FREE_SHIPPING_MIN', (int) EnvLoader::get('FREE_SHIPPING_MIN', 500));
define('STANDARD_SHIPPING', (int) EnvLoader::get('STANDARD_SHIPPING', 49));
define('EXPRESS_SHIPPING', (int) EnvLoader::get('EXPRESS_SHIPPING', 99));

define('DEFAULT_META_TITLE', 'WeddingYour — Premium Bengali Wedding Accessories | Topor, Mukut & More');
define('DEFAULT_META_DESCRIPTION', 'India\'s leading Bengali wedding marketplace for premium handmade Topor, Mukut, bridal accessories, and traditional wedding items. Your complete Bengali wedding destination.');
define('DEFAULT_META_KEYWORDS', 'WeddingYour, Bengali Wedding, Bengali Wedding Planner, Bengali Wedding Photography, Bengali Wedding Dresses, Bengali Wedding Decor, Bengali Wedding Invitation, Bengali Wedding Venue, Bengali Bride, Bengali Groom, Bengali Marriage, topor, mukut, sholapith');

define('INVOICES_DIR', ROOT_DIR . DS . 'invoices');
define('INVOICE_PREFIX', 'INV');
