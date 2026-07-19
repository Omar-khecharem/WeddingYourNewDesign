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

\App\Core\Router::dispatch();
