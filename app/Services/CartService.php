<?php
/**
 * Cart Service
 * 
 * Manages shopping cart operations: add, remove, update quantities,
 * apply coupons, calculate totals, and sync with database/user sessions.
 *
 * @package App\Services
 */

namespace App\Services;

use App\Core\Database;
use App\Helpers\Session;
use App\Helpers\Format;

class CartService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get total item count for the current cart (guest or logged-in).
     * Does NOT rely on PHP session — reads directly from DB.
     */
    public function getCount(): int
    {
        $cartId = $this->getCartId();
        if (!$cartId) return 0;
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM sg_cart_items WHERE cart_id = :cart_id");
        $stmt->execute([':cart_id' => $cartId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get current cart
     */
    public function getCart(): array
    {
        $cartId = $this->getCartId();
        if (!$cartId) return $this->emptyCart();

        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("
            SELECT c.*, ci.id as item_id, ci.product_id, ci.variant_id, ci.quantity, 
                   ci.unit_price, ci.total_price as item_total,
                   p.name, p.slug, p.stock_status, p.stock_quantity
            FROM sg_carts c
            LEFT JOIN sg_cart_items ci ON ci.cart_id = c.id
            LEFT JOIN sg_products p ON p.id = ci.product_id
            WHERE c.id = :cart_id
        ");
        $stmt->execute([':cart_id' => $cartId]);
        $rows = $stmt->fetchAll();

        if (empty($rows) || empty($rows[0]['item_id'])) {
            return $this->emptyCart();
        }

        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id' => $row['item_id'],
                'product_id' => $row['product_id'],
                'name' => $row['name'],
                'slug' => $row['slug'],
                'variant_id' => $row['variant_id'],
                'quantity' => (int)$row['quantity'],
                'unit_price' => (float)$row['unit_price'],
                'total' => (float)$row['item_total'],
                'stock_status' => $row['stock_status'],
                'image' => $this->getProductImage($row['product_id']),
            ];
        }

        return [
            'id' => $cartId,
            'items' => $items,
            'count' => count($items),
            'total_items' => array_sum(array_column($items, 'quantity')),
            'subtotal' => (float)$rows[0]['subtotal'],
            'discount' => (float)$rows[0]['discount'],
            'shipping' => (float)$rows[0]['shipping'],
            'tax' => (float)$rows[0]['tax'],
            'total' => (float)$rows[0]['total'],
            'coupon_code' => $this->getCouponCode((int)$rows[0]['coupon_id']),
        ];
    }

    /**
     * Add item to cart
     */
    public function add(int $productId, int $quantity = 1, ?int $variantId = null): array
    {
        $pdo = $this->db->getConnection();

        // Check product exists and is in stock
        $stmt = $pdo->prepare("SELECT * FROM sg_products WHERE id = :id AND status = 1");
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch();

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        $stockQty = $product['stock_quantity'];
        if ($product['stock_status'] === 'out_of_stock' || ($stockQty !== null && $stockQty <= 0)) {
            return ['success' => false, 'message' => 'Product is out of stock.'];
        }

        $price = (float)($product['sale_price'] ?? $product['regular_price'] ?? 0);
        $cartId = $this->getOrCreateCart();

        // Check if item already in cart
        if ($variantId !== null) {
            $stmt = $pdo->prepare("
                SELECT id, quantity FROM sg_cart_items 
                WHERE cart_id = :cart_id AND product_id = :product_id AND variant_id = :variant_id
            ");
            $stmt->execute([
                ':cart_id' => $cartId,
                ':product_id' => $productId,
                ':variant_id' => $variantId,
            ]);
        } else {
            $stmt = $pdo->prepare("
                SELECT id, quantity FROM sg_cart_items 
                WHERE cart_id = :cart_id AND product_id = :product_id AND variant_id IS NULL
            ");
            $stmt->execute([
                ':cart_id' => $cartId,
                ':product_id' => $productId,
            ]);
        }
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            if ($newQty > $product['stock_quantity']) {
                $newQty = $product['stock_quantity'];
            }
            $stmt = $pdo->prepare("UPDATE sg_cart_items SET quantity = :quantity, total_price = :total WHERE id = :id");
            $stmt->execute([
                ':quantity' => $newQty,
                ':total' => $price * $newQty,
                ':id' => $existing['id']
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO sg_cart_items (cart_id, product_id, variant_id, quantity, unit_price, total_price)
                VALUES (:cart_id, :product_id, :variant_id, :quantity, :unit_price, :total_price)
            ");
            $stmt->execute([
                ':cart_id' => $cartId,
                ':product_id' => $productId,
                ':variant_id' => $variantId,
                ':quantity' => $quantity,
                ':unit_price' => $price,
                ':total_price' => $price * $quantity,
            ]);
        }

        $this->recalculateCart($cartId);
        $this->updateSessionCount();

        $cart = $this->getCart();
        return [
            'success' => true,
            'message' => 'Product added to cart.',
            'cart_count' => $cart['total_items'],
            'cart_total' => $cart['total'],
            'cart_token' => $this->readGuestToken(),
            'cart_id' => $cartId,
        ];
    }

    /**
     * Update item quantity
     */
    public function update(int $itemId, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->remove($itemId);
        }

        $pdo = $this->db->getConnection();
        $cartId = $this->getCartId();

        if (!$cartId) {
            return ['success' => false, 'message' => 'Cart not found.'];
        }

        $stmt = $pdo->prepare("
            UPDATE sg_cart_items ci
            JOIN sg_products p ON p.id = ci.product_id
            SET ci.quantity = :qty, ci.total_price = (ci.unit_price * :qty2)
            WHERE ci.id = :id AND ci.cart_id = :cart_id
            AND (:qty3 <= p.stock_quantity OR p.stock_status = 'in_stock')
        ");
        $stmt->execute([
            ':qty' => $quantity,
            ':qty2' => $quantity,
            ':qty3' => $quantity,
            ':id' => $itemId,
            ':cart_id' => $cartId
        ]);

        $this->recalculateCart($cartId);
        $this->updateSessionCount();

        return ['success' => true, 'message' => 'Cart updated.'];
    }

    /**
     * Remove item from cart
     */
    public function remove(int $itemId): array
    {
        $pdo = $this->db->getConnection();
        $cartId = $this->getCartId();

        if (!$cartId) {
            return ['success' => false, 'message' => 'Cart not found.'];
        }

        $stmt = $pdo->prepare("DELETE FROM sg_cart_items WHERE id = :id AND cart_id = :cart_id");
        $stmt->execute([':id' => $itemId, ':cart_id' => $cartId]);

        $this->recalculateCart($cartId);
        $this->updateSessionCount();

        return ['success' => true, 'message' => 'Item removed from cart.'];
    }

    /**
     * Clear entire cart
     */
    public function clear(): void
    {
        $cartId = $this->getCartId();
        if (!$cartId) return;

        $pdo = $this->db->getConnection();
        $pdo->prepare("DELETE FROM sg_cart_items WHERE cart_id = :id")->execute([':id' => $cartId]);
        $pdo->prepare("DELETE FROM sg_carts WHERE id = :id")->execute([':id' => $cartId]);

        setcookie('cart_token', '', time() - 3600, '/', '', false, false);
        unset($_COOKIE['cart_token']);
        $this->updateSessionCount();
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(string $code): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_coupons WHERE code = :code AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW()) AND (starts_at IS NULL OR starts_at <= NOW())");
        $stmt->execute([':code' => $code]);
        $coupon = $stmt->fetch();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid or expired coupon.'];
        }

        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['success' => false, 'message' => 'Coupon usage limit reached.'];
        }

        $cartId = $this->getCartId();
        if (!$cartId) {
            return ['success' => false, 'message' => 'Cart is empty.'];
        }

        $cart = $this->getCart();
        if ($coupon['min_order_amount'] && $cart['subtotal'] < $coupon['min_order_amount']) {
            return ['success' => false, 'message' => 'Minimum order amount of ' . Format::currency($coupon['min_order_amount']) . ' required.'];
        }

        $stmt = $pdo->prepare("UPDATE sg_carts SET coupon_id = :coupon_id WHERE id = :id");
        $stmt->execute([':coupon_id' => $coupon['id'], ':id' => $cartId]);

        $this->recalculateCart($cartId);

        return ['success' => true, 'message' => 'Coupon applied!', 'discount' => $coupon['value']];
    }

    /**
     * Remove coupon from cart
     */
    public function removeCoupon(): void
    {
        $cartId = $this->getCartId();
        if ($cartId) {
            $pdo = $this->db->getConnection();
            $pdo->prepare("UPDATE sg_carts SET coupon_id = NULL WHERE id = :id")->execute([':id' => $cartId]);
            $this->recalculateCart($cartId);
        }
    }

    /**
     * Recalculate cart totals
     */
    private function recalculateCart(int $cartId): void
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare("SELECT SUM(total_price) as subtotal FROM sg_cart_items WHERE cart_id = :id");
        $stmt->execute([':id' => $cartId]);
        $result = $stmt->fetch();
        $subtotal = (float)($result['subtotal'] ?? 0);

        // Calculate discount from coupon
        $discount = 0;
        $stmt = $pdo->prepare("SELECT c.* FROM sg_carts ca JOIN sg_coupons c ON c.id = ca.coupon_id WHERE ca.id = :id");
        $stmt->execute([':id' => $cartId]);
        $coupon = $stmt->fetch();

        if ($coupon) {
            if ($coupon['type'] === 'percentage') {
                $discount = $subtotal * ($coupon['value'] / 100);
                if ($coupon['max_discount']) {
                    $discount = min($discount, (float)$coupon['max_discount']);
                }
            } else {
                $discount = min((float)$coupon['value'], $subtotal);
            }
        }

        // Calculate shipping from admin settings
        $shipping = 0;
        $freeMin = (float) \App\Models\Setting::get('free_shipping_min', FREE_SHIPPING_MIN);
        $stdCost = (float) \App\Models\Setting::get('shipping_cost', STANDARD_SHIPPING);
        if ($subtotal < $freeMin) {
            $shipping = $stdCost;
        }

        // Calculate tax (GST)
        $taxEnabled = \App\Models\Setting::get('tax_enabled', '1');
        $tax = $taxEnabled === '1' ? ($subtotal - $discount) * (TAX_RATE / 100) : 0;

        $total = $subtotal - $discount + $tax + $shipping;

        $stmt = $pdo->prepare("
            UPDATE sg_carts SET 
                subtotal = :subtotal, discount = :discount, 
                tax = :tax, shipping = :shipping, total = :total
            WHERE id = :id
        ");
        $stmt->execute([
            ':subtotal' => $subtotal,
            ':discount' => $discount,
            ':tax' => $tax,
            ':shipping' => $shipping,
            ':total' => $total,
            ':id' => $cartId
        ]);
    }

    /**
     * Read guest token from: POST > X-Cart-Token header > cookie
     */
    private function readGuestToken(): string
    {
        return $_POST['cart_token'] ?? $_SERVER['HTTP_X_CART_TOKEN'] ?? $_COOKIE['cart_token'] ?? $_GET['cart_token'] ?? '';
    }

    /**
     * Get unique guest token (from request/cookie, or generate new)
     */
    private function getGuestToken(): string
    {
        $token = $this->readGuestToken();
        if (strlen($token) !== 40) {
            $token = bin2hex(random_bytes(20));
        }
        $this->setGuestTokenCookie($token);
        return $token;
    }

    /**
     * Set guest token cookie
     */
    private function setGuestTokenCookie(string $token): void
    {
        setcookie('cart_token', $token, [
            'expires' => time() + 86400 * 30,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
        $_COOKIE['cart_token'] = $token;
    }

    /**
     * Get or create cart ID
     */
    private function getOrCreateCart(): int
    {
        $cartId = $this->getCartId();
        if ($cartId) return $cartId;

        $pdo = $this->db->getConnection();
        $userId = Session::get('user.id');

        $token = $this->getGuestToken();

        $stmt = $pdo->prepare("
            INSERT INTO sg_carts (user_id, session_id, subtotal, discount, tax, shipping, total)
            VALUES (:user_id, :session_id, 0, 0, 0, 0, 0)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':session_id' => $userId ? null : $token
        ]);

        $cartId = (int)$pdo->lastInsertId();

        return $cartId;
    }

    /**
     * Get current cart ID
     */
    private function getCartId(): ?int
    {
        $userId = Session::get('user.id');
        $pdo = $this->db->getConnection();

        if ($userId) {
            $stmt = $pdo->prepare("SELECT id FROM sg_carts WHERE user_id = :user_id ORDER BY id DESC LIMIT 1");
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch();
            if ($result) {
                return (int)$result['id'];
            }
        }

        $token = $this->readGuestToken();
        if ($token) {
            $stmt = $pdo->prepare("SELECT id FROM sg_carts WHERE session_id = :token ORDER BY id DESC LIMIT 1");
            $stmt->execute([':token' => $token]);
            $result = $stmt->fetch();
            if ($result) {
                $this->setGuestTokenCookie($token);
                return (int)$result['id'];
            }
        }

        return null;
    }

    /**
     * Update cart count in session
     */
    private function updateSessionCount(): void
    {
        $cart = $this->getCart();
        Session::set('cart.count', $cart['total_items'] ?? 0);
        Session::set('cart.total', $cart['total'] ?? 0.00);
    }

    /**
     * Get product image
     */
    private function getProductImage(int $productId): string
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT image FROM sg_product_images WHERE product_id = :id ORDER BY is_primary DESC, id ASC LIMIT 1");
        $stmt->execute([':id' => $productId]);
        $result = $stmt->fetch();
        if ($result && !empty($result['image'])) {
            return $this->resolveImageUrl($result['image']);
        }
        return asset('images/placeholder.png');
    }

    private function resolveImageUrl(string $imagePath): string
    {
        $filename = str_replace('\\', '/', $imagePath);
        $paths = [
            ['prefix' => 'products', 'check' => UPLOADS_DIR . DS . str_replace('/', DS, ltrim($filename, '/'))],
        ];
        if (!str_starts_with($filename, 'products/')) {
            $paths[] = ['prefix' => 'products', 'check' => PRODUCT_IMAGES_DIR . DS . $filename];
            $paths[] = ['prefix' => '', 'check' => UPLOADS_DIR . DS . $filename];
        }
        foreach ($paths as $p) {
            if (file_exists($p['check'])) {
                return $p['prefix'] ? uploadUrl($filename, $p['prefix']) : uploadUrl($filename);
            }
        }
        return asset('images/placeholder.png');
    }

    /**
     * Get coupon code by ID
     */
    private function getCouponCode(int $couponId): ?string
    {
        if (!$couponId) return null;
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT code FROM sg_coupons WHERE id = :id");
        $stmt->execute([':id' => $couponId]);
        $result = $stmt->fetch();
        return $result['code'] ?? null;
    }

    /**
     * Get empty cart structure
     */
    private function emptyCart(): array
    {
        return [
            'id' => null,
            'items' => [],
            'count' => 0,
            'total_items' => 0,
            'subtotal' => 0,
            'discount' => 0,
            'shipping' => 0,
            'tax' => 0,
            'total' => 0,
            'coupon_code' => null,
        ];
    }

    /**
     * Merge guest cart into user cart on login
     */
    public function mergeCartOnLogin(int $userId): void
    {
        $sessionCartId = Session::get('cart.id');
        if (!$sessionCartId) {
            $token = $_COOKIE['cart_token'] ?? '';
            if ($token) {
                $pdo = $this->db->getConnection();
                $stmt = $pdo->prepare("SELECT id FROM sg_carts WHERE session_id = :token ORDER BY id DESC LIMIT 1");
                $stmt->execute([':token' => $token]);
                $result = $stmt->fetch();
                $sessionCartId = $result ? (int)$result['id'] : null;
            }
        }
        if (!$sessionCartId) return;

        $pdo = $this->db->getConnection();

        // Find or create user cart
        $stmt = $pdo->prepare("SELECT id FROM sg_carts WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);
        $userCart = $stmt->fetch();

        if ($userCart) {
            // Move items from session cart to user cart
            $stmt = $pdo->prepare("
                UPDATE sg_cart_items SET cart_id = :user_cart_id 
                WHERE cart_id = :session_cart_id
                AND product_id NOT IN (
                    SELECT product_id FROM sg_cart_items WHERE cart_id = :user_cart_id2
                )
            ");
            $stmt->execute([
                ':user_cart_id' => $userCart['id'],
                ':session_cart_id' => $sessionCartId,
                ':user_cart_id2' => $userCart['id']
            ]);

            // Delete session cart
            $pdo->prepare("DELETE FROM sg_carts WHERE id = :id")->execute([':id' => $sessionCartId]);
            Session::set('cart.id', (int)$userCart['id']);
            $this->recalculateCart((int)$userCart['id']);
        } else {
            // Assign session cart to user
            $pdo->prepare("UPDATE sg_carts SET user_id = :user_id WHERE id = :id")
                ->execute([':user_id' => $userId, ':id' => $sessionCartId]);
        }
    }
}
