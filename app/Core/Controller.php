<?php
/**
 * Base Controller
 * 
 * Provides shared functionality for all controllers including
 * view rendering, JSON responses, redirects, and input validation.
 *
 * @package App\Core
 */

namespace App\Core;

use App\Helpers\Security;
use App\Helpers\Session;

abstract class Controller
{
    /**
     * Data passed to all views
     */
    protected array $viewData = [];

    /**
     * Constructor - sets common view data
     */
    public function __construct()
    {
        $this->viewData['appName'] = APP_NAME;
        $this->viewData['appTagline'] = APP_TAGLINE;
        $this->viewData['appUrl'] = APP_URL;
        $this->viewData['currency'] = APP_CURRENCY;
        $this->viewData['contactEmail'] = CONTACT_EMAIL;
        $this->viewData['contactPhone'] = CONTACT_PHONE;
        $this->viewData['contactAddress'] = CONTACT_ADDRESS;
        $this->viewData['whatsappLink'] = WHATSAPP_LINK;
        $this->viewData['currentYear'] = date('Y');
        $this->viewData['metaTitle'] = DEFAULT_META_TITLE;
        $this->viewData['metaDescription'] = DEFAULT_META_DESCRIPTION;
        $this->viewData['metaKeywords'] = DEFAULT_META_KEYWORDS;
        $this->viewData['facebookUrl'] = SOCIAL_FACEBOOK;
        $this->viewData['instagramUrl'] = SOCIAL_INSTAGRAM;
        $this->viewData['youtubeUrl'] = SOCIAL_YOUTUBE;

        // Cart count — read from DB (not session) for reliable guest cart
        try {
            $cartService = new \App\Services\CartService();
            $this->viewData['cartCount'] = $cartService->getCount();
        } catch (\Exception $e) {
            $this->viewData['cartCount'] = Session::get('cart.count', 0);
        }
        $this->viewData['wishlistCount'] = Session::get('wishlist.count', 0);
        // Compute compare count from DB
        try {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $userId = Session::get('user.id');
            if ($userId) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_compare WHERE user_id = :u");
                $stmt->execute([':u' => $userId]);
            } else {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_compare WHERE session_id = :s");
                $stmt->execute([':s' => session_id()]);
            }
            $compareCount = (int)$stmt->fetchColumn();
        } catch (\Exception $e) {
            $compareCount = 0;
        }
        $this->viewData['compareCount'] = $compareCount;

        // Auth user
        $this->viewData['authUser'] = Session::get('user');
        $this->viewData['isLoggedIn'] = Session::has('user');
    }

    /**
     * Render a view with layout
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): string
    {
        $data = array_merge($this->viewData, $data);
        return View::render($view, $data, $layout);
    }

    /**
     * Render a view without layout (for AJAX/partials)
     */
    protected function partial(string $view, array $data = []): string
    {
        $data = array_merge($this->viewData, $data);
        return View::renderPartial($view, $data);
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect to a URL
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Redirect back to previous page
     */
    protected function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL;
        $this->redirect($referer);
    }

    /**
     * Redirect to a named route
     */
    protected function redirectRoute(string $name, array $params = []): void
    {
        $this->redirect(Router::route($name, $params));
    }

    /**
     * Set flash message
     */
    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    /**
     * Validate request data against rules
     */
    protected function validate(Request $request, array $rules): array
    {
        $validation = new \App\Helpers\Validation();
        $validated = $validation->validate($request->all(), $rules);

        if ($validation->fails()) {
            Session::set('errors', $validation->errors());
            Session::set('old', $request->all());
            $this->redirectBack();
        }

        return $validated;
    }

    /**
     * Set SEO meta data
     */
    protected function setMeta(string $title, string $description = '', string $keywords = ''): void
    {
        $this->viewData['metaTitle'] = $title ? $title . ' | ' . APP_NAME : DEFAULT_META_TITLE;
        $this->viewData['metaDescription'] = $description ?: DEFAULT_META_DESCRIPTION;
        $this->viewData['metaKeywords'] = $keywords ?: DEFAULT_META_KEYWORDS;
    }

    /**
     * Set breadcrumb data
     */
    protected function setBreadcrumb(array $breadcrumb): void
    {
        $this->viewData['breadcrumb'] = $breadcrumb;
    }

    /**
     * Get current page number from query string
     */
    protected function getPage(): int
    {
        return max(1, (int)($_GET['page'] ?? 1));
    }

    /**
     * Not found response
     */
    protected function notFound(string $message = 'Page not found'): void
    {
        http_response_code(404);
        if (self::isAjax()) {
            $this->json(['error' => $message], 404);
        }
        $this->view('errors.404', ['message' => $message]);
        exit;
    }

    /**
     * Forbidden response
     */
    protected function forbidden(string $message = 'Access denied'): void
    {
        http_response_code(403);
        if (self::isAjax()) {
            $this->json(['error' => $message], 403);
        }
        $this->view('errors.403', ['message' => $message]);
        exit;
    }

    /**
     * Check if request is AJAX
     */
    protected static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get sanitized input
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return Security::sanitize($_POST[$key] ?? $_GET[$key] ?? $default);
    }
}
