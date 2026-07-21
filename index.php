<?php

define('BASE_PATH', __DIR__);

require_once BASE_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'autoload.php';

// Redirect old /admin/* URLs to /13091998/*
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($uri, '/admin') === 0 && strpos($uri, '/13091998') !== 0) {
    header('Location: ' . rtrim(APP_URL, '/') . '/13091998' . substr($uri, 6), true, 301);
    exit;
}

require_once ROUTES_DIR . DS . 'web.php';
require_once ROUTES_DIR . DS . 'api.php';
require_once ROUTES_DIR . DS . 'admin.php';

\App\Helpers\Security::setSecureHeaders();

// Maintenance / Store Closed check (skip for admin & assets)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($path, '/13091998') !== 0 && strpos($path, '/assets/') !== 0) {
    try {
        $mm = \App\Models\Setting::get('maintenance_mode', '0');
        if ($mm === '1') {
            http_response_code(503);
            require VIEWS_DIR . DS . 'maintenance.php';
            exit;
        }
        $so = \App\Models\Setting::get('store_open', '1');
        if ($so !== '1') {
            http_response_code(503);
            require VIEWS_DIR . DS . 'store_closed.php';
            exit;
        }
    } catch (\Throwable $e) {
        // DB unavailable – let the app handle normally
    }
}

\App\Core\Router::dispatch();
