<?php
/**
 * Account Controller
 * 
 * Manages the user account dashboard including profile management,
 * order history, wishlist management, and address management.
 *
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Services\OrderService;
use App\Services\InvoiceService;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Address;
use App\Helpers\Session;

class AccountController extends Controller
{
    private AuthService $authService;
    private OrderService $orderService;

    public function __construct()
    {
        parent::__construct();

        // Admin users should not access customer account pages
        if (($this->viewData['authUser']['role'] ?? '') === 'admin') {
            header('Location: ' . APP_URL . '/13091998/dashboard');
            exit;
        }

        $this->authService = new AuthService();
        $this->orderService = new OrderService();
    }

    /**
     * Render a view with the account layout
     */
    protected function view(string $view, array $data = [], string $layout = 'account'): string
    {
        return parent::view($view, $data, $layout);
    }

    /**
     * Account dashboard
     */
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('My Account');
        $user = $this->viewData['authUser'];

        $recentOrders = $this->orderService->getUserOrders($user['id'], 1, 5);
        $stats = $this->orderService->getUserStats($user['id']);
        $wishlistCount = Wishlist::getCount($user['id']);
        Session::set('wishlist.count', $wishlistCount);

        return $this->view('account.index', [
            'user' => $user,
            'recentOrders' => $recentOrders['orders'] ?? [],
            'stats' => $stats,
        ]);
    }

    /**
     * Edit profile
     */
    public function profile(Request $request, Response $response): string
    {
        $this->setMeta('Edit Profile');
        return $this->view('account.profile', [
            'user' => $this->viewData['authUser'],
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request, Response $response): void
    {
        $user = $this->viewData['authUser'];
        $data = [
            'name' => $request->input('name', ''),
            'email' => $request->input('email', ''),
            'phone' => $request->input('phone', ''),
        ];

        $result = $this->authService->updateProfile($user['id'], $data);

        if ($result['success']) {
            $this->flash('success', $result['message']);
        } else {
            $errors = $result['errors'] ?? [];
            \App\Helpers\Session::set('errors', $errors);
        }

        $this->redirectBack();
    }

    /**
     * Change password form
     */
    public function changePasswordForm(Request $request, Response $response): string
    {
        $this->setMeta('Change Password');
        return $this->view('account.change_password', [
            'user' => $this->viewData['authUser'],
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request, Response $response): void
    {
        $user = $this->viewData['authUser'];
        $currentPassword = $request->input('current_password', '');
        $newPassword = $request->input('new_password', '');

        if (strlen($newPassword) < 8) {
            $this->flash('error', 'New password must be at least 8 characters.');
            $this->redirectBack();
        }

        $result = $this->authService->changePassword($user['id'], $currentPassword, $newPassword);

        if ($result['success']) {
            $this->flash('success', $result['message']);
        } else {
            $this->flash('error', $result['message']);
        }

        $this->redirectBack();
    }

    /**
     * Order history
     */
    public function orders(Request $request, Response $response): string
    {
        $this->setMeta('My Orders');
        $user = $this->viewData['authUser'];
        $page = $this->getPage();

        $orders = $this->orderService->getUserOrders($user['id'], $page);

        return $this->view('account.orders', [
            'orders' => $orders['orders'],
            'pagination' => $orders,
        ]);
    }

    /**
     * View order detail
     */
    public function orderDetail(Request $request, Response $response): string
    {
        $orderId = (int)$request->param('id');
        $user = $this->viewData['authUser'];

        $order = $this->orderService->getOrder($orderId);

        if (!$order || $order['user_id'] != $user['id']) {
            $this->notFound('Order not found.');
        }

        $this->setMeta('Order #' . $order['order_number']);
        return $this->view('account.order_detail', ['order' => $order]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Request $request, Response $response): void
    {
        $orderId = (int)$request->input('order_id');
        $user = $this->viewData['authUser'];

        $result = $this->orderService->cancelOrder($orderId, $user['id']);

        if ($result) {
            $this->flash('success', 'Order cancelled successfully.');
        } else {
            $this->flash('error', 'Order could not be cancelled. Only pending orders can be cancelled.');
        }

        $this->redirectBack();
    }

    /**
     * Wishlist page
     */
    public function wishlist(Request $request, Response $response): string
    {
        $this->setMeta('My Wishlist');
        $user = $this->viewData['authUser'];

        $wishlistItems = Wishlist::getUserWishlist($user['id']);
        Session::set('wishlist.count', count($wishlistItems));

        return $this->view('account.wishlist', [
            'wishlistItems' => $wishlistItems,
        ]);
    }

    /**
     * Add to wishlist (AJAX)
     */
    public function addWishlist(Request $request, Response $response): void
    {
        $productId = (int)$request->input('product_id');
        $user = $this->viewData['authUser'];

        if (!$user) {
            $this->json(['success' => false, 'message' => 'Please login to add items to wishlist.']);
            return;
        }

        $result = Wishlist::toggle($user['id'], $productId);
        $result['count'] = Wishlist::getCount($user['id']);
        Session::set('wishlist.count', $result['count']);

        $this->json($result);
    }

    /**
     * Get wishlist product IDs for current user (AJAX)
     */
    public function wishlistIds(Request $request, Response $response): void
    {
        $user = $this->viewData['authUser'];
        $ids = [];
        if ($user) {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT product_id FROM sg_wishlist WHERE user_id = :uid");
            $stmt->execute([':uid' => $user['id']]);
            $ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            $ids = array_map('intval', $ids);
        }
        $this->json(['success' => true, 'ids' => $ids, 'count' => count($ids)]);
    }

    /**
     * Address list
     */
    public function addresses(Request $request, Response $response): string
    {
        $this->setMeta('My Addresses');
        $user = $this->viewData['authUser'];

        $addresses = Address::getUserAddresses($user['id']);

        return $this->view('account.addresses', [
            'addresses' => $addresses,
        ]);
    }

    /**
     * Save/update address
     */
    public function saveAddress(Request $request, Response $response): void
    {
        $user = $this->viewData['authUser'];
        $addressId = (int)$request->input('id', 0);

        $data = [
            'user_id' => $user['id'],
            'type' => $request->input('type', 'both'),
            'label' => $request->input('label', ''),
            'full_name' => $request->input('full_name', ''),
            'phone' => $request->input('phone', ''),
            'address_line1' => $request->input('address_line1', ''),
            'address_line2' => $request->input('address_line2', ''),
            'city' => $request->input('city', ''),
            'state' => $request->input('state', ''),
            'postal_code' => $request->input('postal_code', ''),
        ];

        $required = ['full_name', 'address_line1', 'city', 'state', 'postal_code'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                \App\Helpers\Session::set('errors', [$field => 'This field is required.']);
                $this->redirectBack();
            }
        }

        if ($addressId) {
            Address::update($addressId, $data);
        } else {
            Address::create($data);
        }

        $this->flash('success', 'Address saved successfully.');
        $this->redirectBack();
    }

    /**
     * Delete address
     */
    public function deleteAddress(Request $request, Response $response): void
    {
        $addressId = (int)$request->input('id', 0);
        $user = $this->viewData['authUser'];

        if ($addressId) {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("DELETE FROM sg_addresses WHERE id = :id AND user_id = :uid");
            $stmt->execute([':id' => $addressId, ':uid' => $user['id']]);
        }

        $this->flash('success', 'Address deleted successfully.');
        $this->redirectBack();
    }

    /**
     * Set default address
     */
    public function setDefaultAddress(Request $request, Response $response): void
    {
        $addressId = (int)$request->input('id', 0);
        $user = $this->viewData['authUser'];

        if ($addressId) {
            Address::setDefault($addressId, $user['id']);
        }

        $this->json(['success' => true]);
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(Request $request, Response $response): void
    {
        $orderId = (int)$request->param('id');
        $user = $this->viewData['authUser'];

        $order = $this->orderService->getOrder($orderId);

        if (!$order || $order['user_id'] != $user['id']) {
            $this->notFound('Order not found.');
        }

        InvoiceService::generate($order, true);
    }
}
