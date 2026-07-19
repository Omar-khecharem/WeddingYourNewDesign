<?php
/**
 * Auth Controller
 * 
 * Handles user authentication, registration, login, logout,
 * password management, and profile management.
 *
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Helpers\Security;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }

    /**
     * Show login form
     */
    /**
     * Show admin login form (at /13091998)
     */
    public function adminLoginForm(Request $request, Response $response): string
    {
        if ($this->viewData['isLoggedIn'] && ($_SESSION['user']['role'] ?? '') === 'admin') {
            $this->redirect(url('13091998/dashboard'));
        }
        return $this->partial('auth.admin-login');
    }

    /**
     * Process admin login
     */
    public function adminLogin(Request $request, Response $response): void
    {
        $email = Security::sanitizeEmail($request->input('email', ''));
        $password = $request->input('password', '');

        if (empty($email) || empty($password)) {
            \App\Helpers\Session::set('old', ['email' => $email]);
            $this->flash('error', 'Please enter email and password.');
            $this->redirect(url('13091998'));
            return;
        }

        $result = $this->authService->login($email, $password, true);

        if ($result['success']) {
            $role = $result['user']['role'] ?? '';
            if ($role !== 'admin') {
                $this->authService->logout();
                $this->flash('error', 'Access denied. Admin credentials required.');
                $this->redirect(url('13091998'));
                return;
            }

            $cartService = new \App\Services\CartService();
            $cartService->mergeCartOnLogin($result['user']['id']);

            $this->flash('success', 'Welcome back, ' . $result['user']['name'] . '!');
            $this->redirect(url('13091998/dashboard'));
        } else {
            $this->flash('error', $result['message']);
            $this->redirect(url('13091998'));
        }
    }

    /**
     * Show login form
     */
    public function loginForm(Request $request, Response $response): string
    {
        if ($this->viewData['isLoggedIn']) {
            $role = $_SESSION['user']['role'] ?? '';
            $this->redirect($role === 'admin' ? url('13091998/dashboard') : url('account'));
        }

        $this->setMeta('Login');
        return $this->view('auth.login');
    }

    /**
     * Process login
     */
    public function login(Request $request, Response $response): void
    {
        $email = Security::sanitizeEmail($request->input('email', ''));
        $password = $request->input('password', '');
        $remember = (bool)$request->input('remember');

        if (empty($email) || empty($password)) {
            \App\Helpers\Session::set('old', ['email' => $email]);
            $this->flash('error', 'Please enter email and password.');
            $this->redirectBack();
            return;
        }

        $result = $this->authService->login($email, $password);

        if ($result['success']) {
            $this->flash('success', 'Welcome back, ' . $result['user']['name'] . '!');

            // Merge cart if needed
            $cartService = new \App\Services\CartService();
            $cartService->mergeCartOnLogin($result['user']['id']);

            $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
            $redirect = $isAdmin ? url('13091998/dashboard') : ($_SESSION['redirect_after_login'] ?? url('account'));
            unset($_SESSION['redirect_after_login']);
            $this->redirect($redirect);
        } else {
            $this->flash('error', $result['message']);
            $this->redirectBack();
        }
    }

    /**
     * Show registration form
     */
    public function registerForm(Request $request, Response $response): string
    {
        if ($this->viewData['isLoggedIn']) {
            $role = $_SESSION['user']['role'] ?? '';
            $this->redirect($role === 'admin' ? url('13091998/dashboard') : url('account'));
        }

        $this->setMeta('Create Account');
        return $this->view('auth.register');
    }

    /**
     * Process registration
     */
    public function register(Request $request, Response $response): void
    {
        $data = [
            'name' => $request->input('name', ''),
            'email' => $request->input('email', ''),
            'phone' => $request->input('phone', ''),
            'password' => $request->input('password', ''),
            'password_confirmation' => $request->input('password_confirmation', ''),
        ];

        $result = $this->authService->register($data);

        if ($result['success']) {
            $this->flash('success', 'Account created successfully! Welcome to ' . APP_NAME . '.');
            $this->redirect(url('account'));
        } else {
            $errors = $result['errors'] ?? [];
            \App\Helpers\Session::set('errors', $errors);
            \App\Helpers\Session::set('old', $data);
            $this->redirectBack();
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request, Response $response): void
    {
        $this->authService->logout();
        $this->flash('success', 'You have been logged out successfully.');
        $this->redirect(url(''));
    }

    /**
     * Show forgot password form
     */
    public function forgotForm(Request $request, Response $response): string
    {
        $this->setMeta('Forgot Password');
        return $this->view('auth.forgot');
    }

    /**
     * Process forgot password
     */
    public function forgot(Request $request, Response $response): void
    {
        $email = Security::sanitizeEmail($request->input('email', ''));
        if (empty($email)) {
            $this->flash('error', 'Please enter your email address.');
            $this->redirectBack();
        }

        $result = $this->authService->forgotPassword($email);
        $this->flash('success', $result['message']);
        $this->redirect(url('login'));
    }

    /**
     * Show reset password form
     */
    public function resetForm(Request $request, Response $response): string
    {
        $token = $request->param('token');
        $this->setMeta('Reset Password');
        return $this->view('auth.reset', ['token' => $token]);
    }

    /**
     * Process password reset
     */
    public function reset(Request $request, Response $response): void
    {
        $token = $request->input('token', '');
        $password = $request->input('password', '');

        if (strlen($password) < 8) {
            $this->flash('error', 'Password must be at least 8 characters.');
            $this->redirectBack();
        }

        $result = $this->authService->resetPassword($token, $password);

        if ($result['success']) {
            $this->flash('success', $result['message']);
            $this->redirect(url('login'));
        } else {
            $this->flash('error', $result['message']);
            $this->redirect(url('forgot-password'));
        }
    }
}
