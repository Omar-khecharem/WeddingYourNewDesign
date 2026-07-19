<?php
/**
 * Admin Middleware
 * 
 * Ensures the authenticated user has admin role before accessing
 * admin panel routes. Returns 403 for unauthorized access.
 *
 * @package App\Middleware
 */

namespace App\Middleware;

use App\Helpers\Session;

class AdminMiddleware
{
    /**
     * Handle the middleware check
     */
    public function handle(): void
    {
        $user = Session::get('user');

        if (!$user) {
            Session::flash('warning', 'Please login as admin to access this page.');
            header('Location: ' . APP_URL . '/13091998');
            exit;
        }

        if (!in_array($user['role'] ?? '', ['admin'])) {
            http_response_code(403);
            require VIEWS_DIR . DS . 'errors' . DS . '403.php';
            exit;
        }
    }
}
