<?php
/**
 * Order Service
 * 
 * Handles order creation, processing, status management,
 * invoice generation, and order history retrieval.
 *
 * @package App\Services
 */

namespace App\Services;

use App\Core\Database;
use App\Helpers\Session;
use App\Helpers\Security;

class OrderService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create order from cart
     */
    public function createOrder(array $billing, array $shipping, string $paymentMethod = 'cod', ?string $couponCode = null): array
    {
        $pdo = $this->db->getConnection();
        $cartService = new CartService();
        $cart = $cartService->getCart();

        if (empty($cart['items'])) {
            return ['success' => false, 'message' => 'Cart is empty.'];
        }

        try {
            $pdo->beginTransaction();

            $orderNumber = generateOrderNumber();
            $userId = Session::get('user.id');
            $userEmail = Session::get('user.email', $billing['email'] ?? '');

            // Create order
            $stmt = $pdo->prepare("
                INSERT INTO sg_orders (
                    order_number, user_id, email, phone,
                    billing_name, billing_address, billing_city, billing_state, billing_postal, billing_country,
                    shipping_name, shipping_address, shipping_city, shipping_state, shipping_postal, shipping_country,
                    subtotal, discount, tax, shipping_cost, total,
                    coupon_code, coupon_discount,
                    payment_method_name, payment_status, order_status,
                    currency_code, currency_rate,
                    is_paid, invoice_number, created_at
                ) VALUES (
                    :order_number, :user_id, :email, :phone,
                    :billing_name, :billing_address, :billing_city, :billing_state, :billing_postal, :billing_country,
                    :shipping_name, :shipping_address, :shipping_city, :shipping_state, :shipping_postal, :shipping_country,
                    :subtotal, :discount, :tax, :shipping_cost, :total,
                    :coupon_code, :coupon_discount,
                    :payment_method_name, :payment_status, :order_status,
                    :currency_code, :currency_rate,
                    :is_paid, :invoice_number, NOW()
                )
            ");

            $currency = defined('APP_CURRENCY') ? APP_CURRENCY : 'INR';

            $orderData = [
                ':order_number' => $orderNumber,
                ':user_id' => $userId,
                ':email' => $billing['email'] ?? $userEmail,
                ':phone' => $billing['phone'] ?? '',
                ':billing_name' => $billing['name'],
                ':billing_address' => $billing['address'] . ($billing['address2'] ? ', ' . $billing['address2'] : ''),
                ':billing_city' => $billing['city'],
                ':billing_state' => $billing['state'],
                ':billing_postal' => $billing['pincode'],
                ':billing_country' => $billing['country'] ?? '',
                ':shipping_name' => $shipping['name'],
                ':shipping_address' => $shipping['address'] . ($shipping['address2'] ? ', ' . $shipping['address2'] : ''),
                ':shipping_city' => $shipping['city'],
                ':shipping_state' => $shipping['state'],
                ':shipping_postal' => $shipping['pincode'],
                ':shipping_country' => $shipping['country'] ?? '',
                ':subtotal' => $cart['subtotal'],
                ':discount' => $cart['discount'],
                ':tax' => $cart['tax'],
                ':shipping_cost' => $cart['shipping'],
                ':total' => $cart['total'],
                ':coupon_code' => $couponCode ?? $cart['coupon_code'],
                ':coupon_discount' => $cart['discount'],
                ':payment_method_name' => $paymentMethod === 'cod' ? 'Cash on Delivery' : ($paymentMethod === 'razorpay' ? 'Online Payment' : $paymentMethod),
                ':payment_status' => $paymentMethod === 'cod' ? 'pending' : 'pending',
                ':order_status' => 'pending',
                ':currency_code' => $currency,
                ':currency_rate' => 1.000000,
                ':is_paid' => $paymentMethod === 'cod' ? 0 : 0,
                ':invoice_number' => generateInvoiceNumber(),
            ];

            $stmt->execute($orderData);
            $orderId = (int)$pdo->lastInsertId();

            // Create order items
            foreach ($cart['items'] as $item) {
                $itemImage = $item['image'] ?? '';
                $stmt = $pdo->prepare("
                    INSERT INTO sg_order_items (order_id, product_id, product_name, quantity, unit_price, total_price, image)
                    VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price, :total_price, :image)
                ");
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':product_name' => $item['name'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['unit_price'],
                    ':total_price' => $item['total'],
                    ':image' => $itemImage,
                ]);

                // Update stock
                $stmt = $pdo->prepare("UPDATE sg_products SET stock_quantity = stock_quantity - :qty WHERE id = :id AND stock_quantity >= :qty2");
                $stmt->execute([
                    ':qty' => $item['quantity'],
                    ':id' => $item['product_id'],
                    ':qty2' => $item['quantity']
                ]);
            }

            // Update coupon usage count
            $effectiveCode = $couponCode ?? ($cart['coupon_code'] ?? null);
            if ($effectiveCode) {
                $stmt = $pdo->prepare("SELECT id FROM sg_coupons WHERE code = :code");
                $stmt->execute([':code' => $effectiveCode]);
                $couponId = $stmt->fetchColumn();
                if ($couponId) {
                    $pdo->prepare("UPDATE sg_coupons SET used_count = used_count + 1 WHERE id = :id")
                        ->execute([':id' => $couponId]);
                }
            }

            $pdo->commit();

            // Clear the cart
            $cartService->clear();

            logActivity('order_placed', "Order placed: {$orderNumber}");

            Session::set('last_order_id', $orderId);

            return ['success' => true, 'order_id' => $orderId, 'order_number' => $orderNumber];
        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log('Order creation failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()];
        }
    }

    /**
     * Get order by ID
     */
    public function getOrder(int $orderId): ?array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_orders WHERE id = :id");
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) return null;

        // Get items
        $stmt = $pdo->prepare("SELECT * FROM sg_order_items WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        $items = $stmt->fetchAll();
        foreach ($items as &$item) {
            if (empty($item['image']) && !empty($item['product_id'])) {
                $imgs = \App\Models\Product::getImages((int)$item['product_id']);
                $item['image'] = $imgs[0]['url'] ?? '';
            }
        }
        $order['items'] = $items;

        return $order;
    }

    /**
     * Get order by order number
     */
    public function getOrderByNumber(string $orderNumber): ?array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_orders WHERE order_number = :number");
        $stmt->execute([':number' => $orderNumber]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    /**
     * Get user orders
     */
    public function getUserOrders(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $pdo = $this->db->getConnection();

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM sg_orders WHERE user_id = :user_id");
        $countStmt->execute([':user_id' => $userId]);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT * FROM sg_orders WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll();

        return [
            'orders' => $orders,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status, ?string $trackingNumber = null): bool
    {
        $pdo = $this->db->getConnection();
        $params = [':status' => $status, ':id' => $orderId];

        $sql = "UPDATE sg_orders SET order_status = :status";
        if ($trackingNumber) {
            $sql .= ", tracking_number = :tracking";
            $params[':tracking'] = $trackingNumber;
        }
        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            logActivity('order_status', "Order #{$orderId} status changed to: {$status}");
        }

        return $result;
    }

    /**
     * Cancel order
     */
    public function cancelOrder(int $orderId, int $userId): bool
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("UPDATE sg_orders SET order_status = 'cancelled' WHERE id = :id AND user_id = :user_id AND order_status = 'pending'");
        $result = $stmt->execute([':id' => $orderId, ':user_id' => $userId]);

        if ($result && $stmt->rowCount() > 0) {
            // Restore stock
            $items = $pdo->prepare("SELECT * FROM sg_order_items WHERE order_id = :id");
            $items->execute([':id' => $orderId]);
            foreach ($items->fetchAll() as $item) {
                $pdo->prepare("UPDATE sg_products SET stock_quantity = stock_quantity + :qty WHERE id = :id")
                    ->execute([':qty' => $item['quantity'], ':id' => $item['product_id']]);
            }

            logActivity('order_cancelled', "Order #{$orderId} cancelled by user #{$userId}");
        }

        return $result && $stmt->rowCount() > 0;
    }

    /**
     * Get all orders (admin)
     */
    public function getAllOrders(int $page = 1, int $perPage = 20, ?string $status = null, ?string $search = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $offset = ($page - 1) * $perPage;
        $pdo = $this->db->getConnection();

        $conditions = [];
        $params = [];
        if ($status) {
            $conditions[] = "order_status = :status";
            $params[':status'] = $status;
        }
        if ($search) {
            $conditions[] = "(order_number LIKE :search OR billing_name LIKE :search2 OR billing_email LIKE :search3)";
            $params[':search'] = "%{$search}%";
            $params[':search2'] = "%{$search}%";
            $params[':search3'] = "%{$search}%";
        }
        if ($dateFrom) {
            $conditions[] = "DATE(created_at) >= :date_from";
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $conditions[] = "DATE(created_at) <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM sg_orders{$where}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT * FROM sg_orders{$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'orders' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];
    }

    /**
     * Get order statistics
     */
    public function getStats(): array
    {
        $pdo = $this->db->getConnection();

        $stats = [];
        $stats['total_orders'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_orders")->fetchColumn();
        $stats['pending_orders'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_orders WHERE order_status = 'pending'")->fetchColumn();
        $stats['processing_orders'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_orders WHERE order_status = 'processing'")->fetchColumn();
        $stats['completed_orders'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_orders WHERE order_status = 'delivered'")->fetchColumn();
        $stats['cancelled_orders'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_orders WHERE order_status = 'cancelled'")->fetchColumn();
        $stats['total_revenue'] = (float)$pdo->query("SELECT COALESCE(SUM(total), 0) FROM sg_orders WHERE order_status NOT IN ('cancelled', 'refunded') AND payment_status IN ('paid', 'completed')")->fetchColumn();
        $stats['today_revenue'] = (float)$pdo->query("SELECT COALESCE(SUM(total), 0) FROM sg_orders WHERE DATE(created_at) = CURDATE() AND order_status NOT IN ('cancelled', 'refunded') AND payment_status IN ('paid', 'completed')")->fetchColumn();
        $stats['month_revenue'] = (float)$pdo->query("SELECT COALESCE(SUM(total), 0) FROM sg_orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND order_status NOT IN ('cancelled', 'refunded') AND payment_status IN ('paid', 'completed')")->fetchColumn();

        return $stats;
    }

    /**
     * Get order stats for a specific user
     */
    public function getUserStats(int $userId): array
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_orders WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        $totalOrders = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) FROM sg_orders WHERE user_id = :uid AND order_status NOT IN ('cancelled', 'refunded') AND payment_status IN ('paid', 'completed')");
        $stmt->execute([':uid' => $userId]);
        $totalSpent = (float)$stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_orders WHERE user_id = :uid AND order_status = 'pending'");
        $stmt->execute([':uid' => $userId]);
        $pendingOrders = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_orders WHERE user_id = :uid AND order_status = 'delivered'");
        $stmt->execute([':uid' => $userId]);
        $deliveredOrders = (int)$stmt->fetchColumn();

        return [
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent,
            'pending_orders' => $pendingOrders,
            'delivered_orders' => $deliveredOrders,
        ];
    }
}
