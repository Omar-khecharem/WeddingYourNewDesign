<?php

namespace App\Middleware;

use App\Helpers\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        $user = Session::get('user');

        if (!$user) {
            $isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Please login to add items to your wishlist.'
                ]);
                exit;
            }
            Session::flash('warning', 'Please login to access this page.');
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? APP_URL;
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        // Admin users should not access customer account pages
        if (($user['role'] ?? '') === 'admin') {
            header('Location: ' . APP_URL . '/13091998/dashboard');
            exit;
        }
    }
}
