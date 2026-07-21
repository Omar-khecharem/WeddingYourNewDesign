<?php
/**
 * Global Helper Functions
 * 
 * Utility functions available globally throughout the application.
 * Includes formatting, URL generation, asset loading, and security helpers.
 */

use App\Helpers\Session;
use App\Core\View;

/**
 * Generate a safe URL
 */
function url(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Generate asset URL
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Generate upload URL
 */
function uploadUrl(string $path, string $subdir = ''): string
{
    if (empty($path)) return '';
    $path = str_replace('\\', '/', $path);
    $subdir = trim($subdir, '/');
    if ($subdir && str_starts_with($path, $subdir . '/')) {
        return url('uploads/' . ltrim($path, '/'));
    }
    $prefix = 'uploads';
    if ($subdir) {
        $prefix .= '/' . $subdir;
    }
    return url($prefix . '/' . ltrim($path, '/'));
}

/**
 * Generate route URL
 */
function route(string $name, array $params = []): string
{
    return \App\Core\Router::route($name, $params);
}

/**
 * Escape HTML output
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
}

/**
 * Format currency
 */
function formatPrice(float $amount): string
{
    return APP_CURRENCY . ' ' . number_format($amount, 2);
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 100, string $ending = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length - mb_strlen($ending)) . $ending;
}

/**
 * Generate slug from string
 */
function slugify(string $string): string
{
    $map = [
        'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a','æ'=>'ae',
        'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
        'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
        'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'o',
        'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
        'ý'=>'y','ÿ'=>'y',
        'ñ'=>'n','ç'=>'c','š'=>'s','ž'=>'z',
        'À'=>'A','Á'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A','Å'=>'A','Æ'=>'AE',
        'È'=>'E','É'=>'E','Ê'=>'E','Ë'=>'E',
        'Ì'=>'I','Í'=>'I','Î'=>'I','Ï'=>'I',
        'Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O','Ø'=>'O',
        'Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'U',
        'Ý'=>'Y','Ÿ'=>'Y',
        'Ñ'=>'N','Ç'=>'C','Š'=>'S','Ž'=>'Z',
    ];
    $string = strtr($string, $map);
    $string = preg_replace('/[^a-zA-Z0-9\s_-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = preg_replace('/^-+|-+$/', '', $string);
    return strtolower($string);
}

/**
 * Generate random string
 */
function strRandom(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

/**
 * Generate order number
 */
function generateOrderNumber(): string
{
    return 'WY-' . date('Ymd') . '-' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
}

/**
 * Generate invoice number
 */
function generateInvoiceNumber(): string
{
    return 'WY-INV-' . date('Ymd') . '-' . strtoupper(substr(str_shuffle('0123456789'), 0, 6));
}

/**
 * Check if current page matches route
 */
function isActive(string $path, string $class = 'active'): string
{
    $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $basePath = parse_url(APP_URL, PHP_URL_PATH);
    $currentUri = $basePath ? substr($currentUri, strlen($basePath)) : $currentUri;
    $currentUri = rtrim($currentUri, '/') ?: '/';

    if ($path === $currentUri || strpos($currentUri, $path) === 0 && $path !== '/') {
        return $class;
    }
    return '';
}

/**
 * Old input helper
 */
function old(string $field, string $default = ''): string
{
    return View::old($field, $default);
}

/**
 * Error display helper
 */
function error(string $field = ''): string
{
    return View::errors($field);
}

/**
 * Flash message display
 */
function flashMessage(): string
{
    $types = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700'
    ];

    $html = '';
    foreach ($types as $type => $classes) {
        if (Session::hasFlash($type)) {
            $message = Session::flash($type);
            $html .= '<div class="fixed top-24 right-5 z-[9999] animate-slideInRight">';
            $html .= "<div class=\"{$classes} border-l-4 px-4 py-3 rounded shadow-lg max-w-md\">";
            $html .= '<div class="flex items-center justify-between">';
            $html .= '<p class="text-sm font-medium">' . e($message) . '</p>';
            $html .= '<button onclick="this.parentElement.parentElement.parentElement.remove()" class="ml-4 text-current opacity-50 hover:opacity-100">&times;</button>';
            $html .= '</div></div></div>';
        }
    }
    return $html;
}

/**
 * Format date
 */
function formatDate(string $date, string $format = 'd M Y'): string
{
    if (!$date || $date === '0000-00-00 00:00:00') return '';
    return date($format, strtotime($date));
}

/**
 * Time ago
 */
function timeAgo(string $date): string
{
    $timestamp = strtotime($date);
    $diff = time() - $timestamp;

    $intervals = [
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    ];

    foreach ($intervals as $seconds => $label) {
        $count = floor($diff / $seconds);
        if ($count >= 1) {
            $plural = $count > 1 ? 's' : '';
            return "{$count} {$label}{$plural} ago";
        }
    }
    return 'just now';
}

/**
 * Pagination links HTML
 */
function paginationLinks(array $pagination, string $baseUrl = ''): string
{
    if ($pagination['totalPages'] <= 1) return '';

    $html = '<div class="flex items-center justify-center gap-2 mt-8">';

    // Previous
    $prevClass = $pagination['hasPrev'] ? 'bg-white hover:bg-gray-50 text-gray-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed';
    $html .= "<a href=\"{$baseUrl}?page={$pagination['prevPage']}\" class=\"{$prevClass} border border-gray-300 px-3 py-2 rounded-lg text-sm font-medium transition\">← Prev</a>";

    // Pages
    $start = max(1, $pagination['currentPage'] - 2);
    $end = min($pagination['totalPages'], $pagination['currentPage'] + 2);

    if ($start > 1) {
        $html .= "<a href=\"{$baseUrl}?page=1\" class=\"bg-white border border-gray-300 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-50\">1</a>";
        if ($start > 2) $html .= '<span class="px-2 text-gray-400">...</span>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $pagination['currentPage'] ? 'bg-primary-red text-white border-primary-red' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
        $html .= "<a href=\"{$baseUrl}?page={$i}\" class=\"{$active} border px-3 py-2 rounded-lg text-sm font-medium transition\">{$i}</a>";
    }

    if ($end < $pagination['totalPages']) {
        if ($end < $pagination['totalPages'] - 1) $html .= '<span class="px-2 text-gray-400">...</span>';
        $html .= "<a href=\"{$baseUrl}?page={$pagination['totalPages']}\" class=\"bg-white border border-gray-300 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-50\">{$pagination['totalPages']}</a>";
    }

    // Next
    $nextClass = $pagination['hasNext'] ? 'bg-white hover:bg-gray-50 text-gray-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed';
    $html .= "<a href=\"{$baseUrl}?page={$pagination['nextPage']}\" class=\"{$nextClass} border border-gray-300 px-3 py-2 rounded-lg text-sm font-medium transition\">Next →</a>";

    $html .= '</div>';
    return $html;
}

/**
 * Render SVG star rating
 */
function renderStars(float $rating, int $max = 5): string
{
    $html = '<div class="flex items-center gap-0.5">';
    for ($i = 1; $i <= $max; $i++) {
        if ($i <= round($rating)) {
            $html .= '<svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
        } else {
            $html .= '<svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
        }
    }
    $html .= '</div>';
    return $html;
}

/**
 * Get star rating HTML (FontAwesome)
 */
function starRating(int $rating, int $max = 5): string
{
    $html = '<div class="stars text-[#ffd54f] text-xs">';
    for ($i = 1; $i <= $max; $i++) {
        if ($i <= $rating) {
            $html .= '<i class="fa-solid fa-star"></i>';
        } elseif ($i - 0.5 <= $rating) {
            $html .= '<i class="fa-solid fa-star-half-stroke"></i>';
        } else {
            $html .= '<i class="fa-regular fa-star"></i>';
        }
    }
    $html .= '</div>';
    return $html;
}

/**
 * Render a component
 */
function component(string $component, array $props = []): string
{
    return View::component($component, $props);
}

/**
 * Clear the entire site cache (view cache + cache service)
 */
function clearSiteCache(): void
{
    $cacheService = new \App\Services\CacheService();
    $cacheService->clear();
    array_map('unlink', glob(VIEW_CACHE_DIR . DS . '*'));
}

/**
 * Start a content section (for layouts)
 */
function startSection(string $name): void
{
    View::startSection($name);
}

/**
 * Get rendered section content
 */
function section(string $name, string $default = ''): string
{
    return View::section($name, $default);
}

/**
 * End the current section
 */
function endSection(): void
{
    View::endSection();
}

/**
 * Extend a layout (delegates to View::render)
 */
function extend(string $layout): void
{
    // placeholder — actual layout extension handled by View::render
}

/**
 * Include a sub-view
 */
function includeView(string $view, array $data = []): string
{
    return View::include($view, $data);
}

/**
 * Log activity
 */
function logActivity(string $type, string $description): void
{
    $userId = Session::get('user.id', null);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    try {
        $stmt = \App\Core\Database::getInstance()->getConnection()->prepare(
            "INSERT INTO sg_activity_logs (user_id, type, description, ip_address, user_agent) VALUES (:user_id, :type, :description, :ip, :ua)"
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':type' => $type,
            ':description' => $description,
            ':ip' => $ip,
            ':ua' => $ua
        ]);
    } catch (\Exception $e) {
        error_log('Activity log failed: ' . $e->getMessage());
    }
}

/**
 * Debug helper
 */
function dd(...$vars): void
{
    echo '<pre style="background:#1a1a1a;color:#f8f8f8;padding:20px;margin:20px;border-radius:8px;font-size:14px;overflow:auto;">';
    foreach ($vars as $var) {
        echo '<hr style="border-color:#444;margin:10px 0;">';
        if (is_array($var) || is_object($var)) {
            print_r($var);
        } else {
            var_dump($var);
        }
    }
    echo '</pre>';
    exit;
}
