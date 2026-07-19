<?php

use App\Core\Router;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\SettingController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\BannerController;
use App\Controllers\Admin\GalleryController;
use App\Controllers\Admin\DealController;
use App\Controllers\Admin\CategoryCardController;
use App\Controllers\Admin\SubcategoryController;
use App\Controllers\Admin\CouponController;
use App\Controllers\Admin\PageController;
use App\Controllers\Admin\BlogController;
use App\Controllers\Admin\ContactController;
use App\Controllers\Admin\HomePageController;
use App\Middleware\AdminMiddleware;

// Admin login (outside middleware group - no auth required)
Router::get('/13091998', [\App\Controllers\AuthController::class, 'adminLoginForm']);
Router::post('/13091998', [\App\Controllers\AuthController::class, 'adminLogin']);

// Admin protected routes
Router::group('/13091998', ['middleware' => [AdminMiddleware::class]], function () {

    // Dashboard
    Router::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Router::get('/clear-cache', [DashboardController::class, 'clearCache'])->name('admin.cache');
    Router::get('/logs', [DashboardController::class, 'logs'])->name('admin.logs');
    Router::post('/logs/clean', [DashboardController::class, 'cleanLogs'])->name('admin.logs.clean');

    // Products
    Router::get('/products', [AdminProductController::class, 'index'])->name('admin.products');
    Router::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Router::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Router::get('/products/edit/{id}', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Router::post('/products/update/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Router::post('/products/delete', [AdminProductController::class, 'destroy'])->name('admin.products.delete');
    Router::post('/products/delete-image', [AdminProductController::class, 'deleteImage'])->name('admin.products.delete-image');
    Router::post('/products/set-primary-image', [AdminProductController::class, 'setPrimaryImage'])->name('admin.products.set-primary-image');

    // Categories
    Router::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
    Router::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Router::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Router::get('/categories/edit/{id}', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Router::post('/categories/update/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Router::post('/categories/delete', [CategoryController::class, 'destroy'])->name('admin.categories.delete');

    // Orders
    Router::get('/orders', function () {
        $orderService = new \App\Services\OrderService();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $status = $_GET['status'] ?? null;
        $search = $_GET['search'] ?? null;
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;
        $result = $orderService->getAllOrders($page, 20, $status, $search, $dateFrom, $dateTo);
        return \App\Core\View::render('admin.orders.index', [
            'orders' => $result['orders'],
            'pagination' => [
                'currentPage' => $result['page'],
                'totalPages' => $result['totalPages'],
                'totalItems' => $result['total'],
                'hasPrev' => $result['page'] > 1,
                'hasNext' => $result['page'] < $result['totalPages'],
                'prevPage' => $result['page'] - 1,
                'nextPage' => $result['page'] + 1,
            ],
        ], 'admin');
    })->name('admin.orders');

    Router::get('/orders/{id}', function ($params) {
        $orderService = new \App\Services\OrderService();
        $order = $orderService->getOrder((int)$params['id']);
        if (!$order) { http_response_code(404); exit; }
        return \App\Core\View::render('admin.orders.show', ['order' => $order], 'admin');
    })->name('admin.orders.show');

    Router::post('/orders/status', function () {
        $orderService = new \App\Services\OrderService();
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $tracking = $_POST['tracking_number'] ?? null;
        $orderService->updateStatus($orderId, $status, $tracking);

        // Update payment status if provided
        $paymentStatus = $_POST['payment_status'] ?? '';
        if ($paymentStatus) {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("UPDATE sg_orders SET payment_status = :ps WHERE id = :id");
            $stmt->execute([':ps' => $paymentStatus, ':id' => $orderId]);
        }

        \App\Helpers\Session::flash('success', 'Order status updated.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('13091998/orders')));
        exit;
    })->name('admin.orders.status');

    Router::get('/orders/{id}/invoice', function ($params) {
        $orderService = new \App\Services\OrderService();
        $order = $orderService->getOrder((int)$params['id']);
        if (!$order) { http_response_code(404); exit; }
        \App\Services\InvoiceService::generate($order, true);
        exit;
    })->name('admin.orders.invoice');

    Router::get('/orders/{id}/print', function ($params) {
        $orderService = new \App\Services\OrderService();
        $order = $orderService->getOrder((int)$params['id']);
        if (!$order) { http_response_code(404); exit; }
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Order #' . e($order['order_number'] ?? $order['id']) . '</title>';
        echo '<style>body{font-family:Arial,sans-serif;padding:30px;color:#333}table{width:100%;border-collapse:collapse;margin:15px 0}th,td{padding:10px 12px;text-align:left;border-bottom:1px solid #ddd}th{background:#f5f5f5}.totals{width:300px;margin-left:auto}.totals td{padding:6px 12px}.grand-total td{font-weight:bold;font-size:16px;border-top:2px solid #333}.header{display:flex;justify-content:space-between;align-items:start;margin-bottom:20px;border-bottom:2px solid #333;padding-bottom:15px}.address-box{padding:15px;background:#f9f9f9;border:1px solid #ddd;border-radius:6px}.grid{display:flex;gap:20px;margin-bottom:20px}.grid>div{flex:1}@media print{body{-webkit-print-color-adjust:exact;print-color-adjust:exact}.no-print{display:none}}</style>';
        echo '</head><body><div class="no-print" style="text-align:right;margin-bottom:20px"><button onclick="window.print()" style="padding:8px 20px;background:#1e293b;color:#fff;border:none;border-radius:4px;cursor:pointer">Print</button></div>';
        echo \App\Services\InvoiceService::renderHtml($order);
        echo '</body></html>';
        exit;
    })->name('admin.orders.print');

    Router::post('/orders/delete', function () {
        $orderId = (int)($_POST['id'] ?? 0);
        if ($orderId) {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $pdo->prepare("DELETE FROM sg_order_items WHERE order_id = :id")->execute([':id' => $orderId]);
            $pdo->prepare("DELETE FROM sg_orders WHERE id = :id")->execute([':id' => $orderId]);
            \App\Helpers\Session::flash('success', 'Order deleted successfully.');
        }
        header('Location: ' . url('13091998/orders'));
        exit;
    })->name('admin.orders.delete');

    // Users
    Router::get('/users', function () {
        $pdo = \App\Core\Database::getInstance()->getConnection();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';

        $conditions = [];
        $params = [];

        if ($search) {
            $conditions[] = "(u.name LIKE :search OR u.email LIKE :search2 OR u.phone LIKE :search3)";
            $searchTerm = "%$search%";
            $params[':search'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
        }

        if ($role) {
            $conditions[] = "r.slug = :role";
            $params[':role'] = $role;
        }

        if ($status !== '') {
            $conditions[] = "u.status = :status";
            $params[':status'] = (int)$status;
        }

        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM sg_users u LEFT JOIN sg_roles r ON u.role_id = r.id $whereClause");
        $countStmt->execute($params);
        $totalItems = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($totalItems / $perPage));
        if ($page > $totalPages) $page = $totalPages;
        $offset = ($page - 1) * $perPage;

        $stmt = $pdo->prepare("SELECT u.*, r.name as role, r.slug as role_slug FROM sg_users u LEFT JOIN sg_roles r ON u.role_id = r.id $whereClause ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $users = $stmt->fetchAll();

        return \App\Core\View::render('admin.users.index', [
            'users' => $users,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'hasPrev' => $page > 1,
                'hasNext' => $page < $totalPages,
                'prevPage' => $page - 1,
                'nextPage' => $page + 1,
                'totalItems' => $totalItems,
            ],
            'search' => $search,
            'role' => $role,
            'status' => $status,
        ], 'admin');
    })->name('admin.users');

    Router::get('/users/{id}/edit', function ($params) {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int)$params['id']]);
        $user = $stmt->fetch();
        if (!$user) { http_response_code(404); exit; }
        return \App\Core\View::render('admin.users.form', ['user' => $user], 'admin');
    })->name('admin.users.edit');

    Router::post('/users/update/{id}', function ($params) {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $id = (int)$params['id'];
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $roleSlug = $_POST['role'] ?? 'user';
        $status = (int)($_POST['status'] ?? 1);
        $phone = $_POST['phone'] ?? '';

        $roleStmt = $pdo->prepare("SELECT id FROM sg_roles WHERE slug = :slug LIMIT 1");
        $roleStmt->execute([':slug' => $roleSlug]);
        $roleRow = $roleStmt->fetch();
        $roleId = $roleRow ? (int)$roleRow['id'] : null;

        $stmt = $pdo->prepare("UPDATE sg_users SET name = :n, email = :e, role_id = :r, status = :s, phone = :p WHERE id = :id");
        $stmt->execute([':n' => $name, ':e' => $email, ':r' => $roleId, ':s' => $status, ':p' => $phone, ':id' => $id]);

        if (!empty($_POST['password'])) {
            $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE sg_users SET password = :p WHERE id = :id")->execute([':p' => $hashed, ':id' => $id]);
        }

        \App\Helpers\Session::flash('success', 'User updated successfully.');
        header('Location: ' . url('13091998/users'));
        exit;
    })->name('admin.users.update');

    Router::match(['GET', 'POST'], '/users/{id}/ban', function ($params) {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $id = (int)$params['id'];
        $stmt = $pdo->prepare("SELECT status FROM sg_users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        if (!$user) { http_response_code(404); exit; }

        if ($user['status'] == 1) {
            $pdo->prepare("UPDATE sg_users SET status = 0 WHERE id = :id")->execute([':id' => $id]);
            \App\Helpers\Session::flash('success', 'User banned successfully.');
        } else {
            $pdo->prepare("UPDATE sg_users SET status = 1 WHERE id = :id")->execute([':id' => $id]);
            \App\Helpers\Session::flash('success', 'User unbanned successfully.');
        }
        header('Location: ' . url('13091998/users'));
        exit;
    })->name('admin.users.ban');

    // Reviews
    Router::get('/reviews', function () {
        $reviews = \App\Models\Review::getPending();
        return \App\Core\View::render('admin.reviews.index', ['reviews' => $reviews], 'admin');
    })->name('admin.reviews');

    Router::get('/reviews/{id}/approve', function ($params) {
        \App\Models\Review::approve((int)($params['id'] ?? 0));
        \App\Helpers\Session::flash('success', 'Review approved.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('13091998/reviews')));
        exit;
    })->name('admin.reviews.approve');

    Router::get('/reviews/{id}/reject', function ($params) {
        \App\Models\Review::reject((int)($params['id'] ?? 0));
        \App\Helpers\Session::flash('success', 'Review rejected.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('13091998/reviews')));
        exit;
    })->name('admin.reviews.reject');

    Router::get('/reviews/{id}/delete', function ($params) {
        \App\Models\Review::deleteReview((int)($params['id'] ?? 0));
        \App\Helpers\Session::flash('success', 'Review deleted.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('13091998/reviews')));
        exit;
    })->name('admin.reviews.delete');

    // Password Reset Requests
    Router::get('/forgot-password-requests', function () {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT pr.*, u.id AS user_id, u.name AS user_name, u.status AS user_status
            FROM sg_password_resets pr
            LEFT JOIN sg_users u ON u.email = pr.email
            WHERE pr.admin_request = 1 AND pr.used = 0
            ORDER BY pr.created_at DESC
        ");
        $stmt->execute();
        $requests = $stmt->fetchAll();

        // Mark as viewed: store current timestamp so badge only shows NEW requests
        \App\Helpers\Session::set('forgot_requests_viewed_at', time());

        return \App\Core\View::render('admin.forgot-password-requests.index', ['requests' => $requests], 'admin');
    })->name('admin.forgot-password-requests');

    Router::post('/forgot-password-requests/{id}/handle', function ($params) {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $id = (int)$params['id'];
        $stmt = $pdo->prepare("UPDATE sg_password_resets SET used = 1 WHERE id = :id AND admin_request = 1");
        $stmt->execute([':id' => $id]);
        \App\Helpers\Session::flash('success', 'Password reset request marked as handled.');
        header('Location: ' . url('13091998/forgot-password-requests'));
        exit;
    })->name('admin.forgot-password-requests.handle');

    // Settings
    Router::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
    Router::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    // Homepage
    Router::get('/homepage', [HomePageController::class, 'index'])->name('admin.homepage');
    Router::post('/homepage/toggle-featured', [HomePageController::class, 'toggleFeatured'])->name('admin.homepage.toggle-featured');
    Router::post('/homepage/toggle-trending', [HomePageController::class, 'toggleTrending'])->name('admin.homepage.toggle-trending');
    Router::post('/homepage/settings', [HomePageController::class, 'saveSettings'])->name('admin.homepage.settings');

    // Banners
    Router::get('/banners', [BannerController::class, 'index'])->name('admin.banners');
    Router::get('/banners/create', [BannerController::class, 'create'])->name('admin.banners.create');
    Router::post('/banners', [BannerController::class, 'store'])->name('admin.banners.store');
    Router::get('/banners/edit/{id}', [BannerController::class, 'edit'])->name('admin.banners.edit');
    Router::post('/banners/update/{id}', [BannerController::class, 'update'])->name('admin.banners.update');
    Router::post('/banners/delete', [BannerController::class, 'destroy'])->name('admin.banners.delete');

    // Gallery
    Router::get('/gallery', [GalleryController::class, 'index'])->name('admin.gallery');
    Router::get('/gallery/create', [GalleryController::class, 'create'])->name('admin.gallery.create');
    Router::post('/gallery', [GalleryController::class, 'store'])->name('admin.gallery.store');
    Router::get('/gallery/edit/{id}', [GalleryController::class, 'edit'])->name('admin.gallery.edit');
    Router::post('/gallery/update/{id}', [GalleryController::class, 'update'])->name('admin.gallery.update');
    Router::post('/gallery/delete', [GalleryController::class, 'destroy'])->name('admin.gallery.delete');

    // Deals
    Router::get('/deals', [DealController::class, 'index'])->name('admin.deals');
    Router::post('/deals', [DealController::class, 'store'])->name('admin.deals.store');
    Router::post('/deals/delete', [DealController::class, 'destroy'])->name('admin.deals.delete');

    // Category Cards
    Router::get('/category-cards', [CategoryCardController::class, 'index'])->name('admin.category-cards');
    Router::get('/category-cards/create', [CategoryCardController::class, 'create'])->name('admin.category-cards.create');
    Router::post('/category-cards', [CategoryCardController::class, 'store'])->name('admin.category-cards.store');
    Router::get('/category-cards/edit/{id}', [CategoryCardController::class, 'edit'])->name('admin.category-cards.edit');
    Router::post('/category-cards/update/{id}', [CategoryCardController::class, 'update'])->name('admin.category-cards.update');
    Router::post('/category-cards/delete', [CategoryCardController::class, 'destroy'])->name('admin.category-cards.delete');

    // Subcategories (with image support)
    Router::get('/subcategories', [SubcategoryController::class, 'index'])->name('admin.subcategories');
    Router::get('/subcategories/by-category', function () {
        $categoryId = (int)($_GET['category_id'] ?? 0);
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT id, name, slug FROM sg_subcategories WHERE category_id = :cid AND status = 1 ORDER BY sort_order, name");
        $stmt->execute([':cid' => $categoryId]);
        $subcategories = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'subcategories' => $subcategories]);
        exit;
    })->name('admin.subcategories.by-category');
    Router::get('/subcategories/create/{category_id}', [SubcategoryController::class, 'create'])->name('admin.subcategories.create');
    Router::post('/subcategories', [SubcategoryController::class, 'store'])->name('admin.subcategories.store');
    Router::get('/subcategories/edit/{id}', [SubcategoryController::class, 'edit'])->name('admin.subcategories.edit');
    Router::post('/subcategories/update/{id}', [SubcategoryController::class, 'update'])->name('admin.subcategories.update');
    Router::post('/subcategories/delete', [SubcategoryController::class, 'destroy'])->name('admin.subcategories.delete');

    // Coupons
    Router::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons');
    Router::post('/coupons/store', [CouponController::class, 'store'])->name('admin.coupons.store');
    Router::post('/coupons/update/{id}', [CouponController::class, 'update'])->name('admin.coupons.update');
    Router::post('/coupons/delete', [CouponController::class, 'destroy'])->name('admin.coupons.delete');

    // Pages
    Router::get('/pages', [PageController::class, 'index'])->name('admin.pages');
    Router::get('/pages/create', [PageController::class, 'create'])->name('admin.pages.create');
    Router::post('/pages', [PageController::class, 'store'])->name('admin.pages.store');
    Router::get('/pages/edit/{id}', [PageController::class, 'edit'])->name('admin.pages.edit');
    Router::post('/pages/update/{id}', [PageController::class, 'update'])->name('admin.pages.update');
    Router::post('/pages/delete', [PageController::class, 'destroy'])->name('admin.pages.delete');

    // Blog
    Router::get('/blog', [BlogController::class, 'index'])->name('admin.blog');
    Router::get('/blog/create', [BlogController::class, 'create'])->name('admin.blog.create');
    Router::post('/blog', [BlogController::class, 'store'])->name('admin.blog.store');
    Router::get('/blog/edit/{id}', [BlogController::class, 'edit'])->name('admin.blog.edit');
    Router::post('/blog/update/{id}', [BlogController::class, 'update'])->name('admin.blog.update');
    Router::post('/blog/delete', [BlogController::class, 'destroy'])->name('admin.blog.delete');

    // Contact Messages
    Router::get('/contacts', [ContactController::class, 'index'])->name('admin.contacts');
    Router::get('/contacts/{id}', [ContactController::class, 'show'])->name('admin.contacts.show');
    Router::post('/contacts/{id}/reply', [ContactController::class, 'reply'])->name('admin.contacts.reply');
    Router::post('/contacts/delete', [ContactController::class, 'destroy'])->name('admin.contacts.delete');
});
