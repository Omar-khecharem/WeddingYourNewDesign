<?php
/**
 * Checkout Controller
 * 
 * Manages the checkout process: display checkout form,
 * process orders, handle payment integration, and
 * show order confirmation.
 *
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\InvoiceService;
use App\Helpers\Security;
use App\Helpers\Session;

class CheckoutController extends Controller
{
    private CartService $cartService;
    private OrderService $orderService;
    private PaymentService $paymentService;

    public function __construct()
    {
        parent::__construct();
        $this->cartService = new CartService();
        $this->orderService = new OrderService();
        $this->paymentService = new PaymentService();
    }

    /**
     * Display checkout page
     */
    public function index(Request $request, Response $response): string
    {
        $cart = $this->cartService->getCart();

        if (empty($cart['items'])) {
            $this->flash('warning', 'Your cart is empty.');
            $this->redirect(url('cart'));
        }

        $this->setMeta('Checkout');
        $this->setBreadcrumb([
            ['label' => 'Home', 'url' => url('')],
            ['label' => 'Cart', 'url' => url('cart')],
            ['label' => 'Checkout'],
        ]);

        $user = $this->viewData['authUser'];

        // Load default address for logged-in users
        $defaultAddress = [];
        if ($user) {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM sg_addresses WHERE user_id = :uid AND is_default = 1 LIMIT 1");
            $stmt->execute([':uid' => $user['id']]);
            $defaultAddress = $stmt->fetch() ?: [];
        }

        $paymentMethods = $this->paymentService->getPaymentMethods();
        $shippingMethods = $this->getShippingMethods();

        return $this->view('checkout.index', [
            'cart' => $cart,
            'user' => $user,
            'defaultAddress' => $defaultAddress,
            'paymentMethods' => $paymentMethods,
            'shippingMethods' => $shippingMethods,
        ]);
    }

    /**
     * Process order placement
     */
    public function placeOrder(Request $request, Response $response): void
    {
        $cart = $this->cartService->getCart();

        if (empty($cart['items'])) {
            $this->json(['success' => false, 'message' => 'Cart is empty.']);
        }

        // Validate required fields
        $required = ['billing_name', 'billing_address', 'billing_city', 'billing_state', 'billing_pincode', 'billing_phone', 'billing_email'];
        foreach ($required as $field) {
            if (empty($request->input($field))) {
                $this->json(['success' => false, 'message' => 'Please fill in all required billing fields.']);
            }
        }

        $country = $request->input('billing_country', '');
        $billing = [
            'name' => Security::sanitize($request->input('billing_name', '')),
            'email' => Security::sanitizeEmail($request->input('billing_email', '')),
            'phone' => $request->input('billing_phone', ''),
            'address' => Security::sanitize($request->input('billing_address', '')),
            'address2' => Security::sanitize($request->input('billing_address2', '')),
            'city' => Security::sanitize($request->input('billing_city', '')),
            'state' => Security::sanitize($request->input('billing_state', '')),
            'pincode' => Security::sanitize($request->input('billing_pincode', '')),
            'country' => $country,
        ];

        // Use billing for shipping if same
        $useDifferentShipping = (bool)$request->input('different_shipping');
        if ($useDifferentShipping) {
            $shipping = [
                'name' => Security::sanitize($request->input('shipping_name', $billing['name'])),
                'address' => Security::sanitize($request->input('shipping_address', $billing['address'])),
                'address2' => Security::sanitize($request->input('shipping_address2', '')),
                'city' => Security::sanitize($request->input('shipping_city', $billing['city'])),
                'state' => Security::sanitize($request->input('shipping_state', $billing['state'])),
                'pincode' => Security::sanitize($request->input('shipping_pincode', $billing['pincode'])),
                'country' => $request->input('shipping_country', $country),
            ];
        } else {
            $shipping = $billing;
        }

        $paymentMethod = $request->input('payment_method', 'cod');
        $shippingMethodId = (int)$request->input('shipping_method', 0);

        // Validate shipping method exists in DB (FK constraint)
        $validMethodId = null;
        $shippingMethodName = 'Standard Shipping';
        if ($shippingMethodId > 0) {
            try {
                $pdo = Database::getInstance()->getConnection();
                $stmt = $pdo->prepare("SELECT id, name, base_rate, free_above FROM shipping_methods WHERE id = :id AND status = 1 LIMIT 1");
                $stmt->execute([':id' => $shippingMethodId]);
                $method = $stmt->fetch();
                if ($method) {
                    $validMethodId = (int)$method['id'];
                    $shippingMethodName = $method['name'];
                }
            } catch (\Exception $e) {
            }
        }

        // Calculate TVA (20% VAT)
        $subtotal = $cart['subtotal'] ?? 0;
        $discount = $cart['discount'] ?? 0;
        $tvaBase = $subtotal - $discount;
        $tva = round($tvaBase * 0.20, 2);

        // Get selected shipping cost
        $shippingCost = $this->getShippingCost($shippingMethodId, $subtotal);
        $totalFinal = $tvaBase + $tva + $shippingCost;

        // Generate invoice number
        $invoiceNumber = generateInvoiceNumber();

        // Create the order
        $result = $this->orderService->createOrder($billing, $shipping, $paymentMethod, $cart['coupon_code'] ?? null);

        if ($result['success']) {
            // Update order with correct totals, TVA, shipping, invoice
            $pdo = Database::getInstance()->getConnection();
            $pdo->prepare("UPDATE sg_orders SET tax = :tax, shipping_cost = :shipping, shipping_method_id = :ship_meth_id, shipping_method_name = :ship_meth_name, invoice_number = :inv, total = :total WHERE id = :id")->execute([
                ':tax' => $tva,
                ':shipping' => $shippingCost,
                ':ship_meth_id' => $validMethodId,
                ':ship_meth_name' => $shippingMethodName,
                ':inv' => $invoiceNumber,
                ':total' => $totalFinal,
                ':id' => $result['order_id'],
            ]);

            // Process payment
            if ($paymentMethod === 'cod') {
                $this->paymentService->processCOD($result['order_id']);
            }

            // Set WhatsApp order link in session (from admin settings)
            $orderNumber = $result['order_number'];
            $waNumber = \App\Models\Setting::get('whatsapp_number', WHATSAPP_NUMBER);
            $waMessage = "🛒 *New Order #{$orderNumber}*\n💵 Total: " . APP_CURRENCY . ' ' . number_format($cart['total'], 2) . "\n👤 Customer: " . $billing['name'] . "\n📞 Phone: " . $billing['phone'] . "\n📍 Address: " . $billing['address'] . ", " . $billing['city'] . ", " . $billing['state'] . " - " . $billing['pincode'];
            $waLink = "https://wa.me/" . $waNumber . "?text=" . urlencode($waMessage);
            Session::set('whatsapp_order_link', $waLink);

            // Send confirmation email
            try {
                $emailService = new \App\Services\EmailService();
                $order = $this->orderService->getOrder($result['order_id']);
                if ($order) {
                    $order['tax'] = $tva;
                    $order['shipping_cost'] = $shippingCost;
                    $order['invoice_number'] = $invoiceNumber;
                    $emailService->sendOrderConfirmation($billing['email'], $order);
                }
            } catch (\Exception $e) {
                error_log('Failed to send confirmation email: ' . $e->getMessage());
            }

            $this->json([
                'success' => true,
                'order_id' => $result['order_id'],
                'order_number' => $orderNumber,
                'redirect' => url('order/confirmation/' . $orderNumber),
            ]);
        } else {
            $this->json($result);
        }
    }

    /**
     * Order confirmation page
     */
    public function confirmation(Request $request, Response $response): string
    {
        $orderNumber = $request->param('order_number');
        $order = $this->orderService->getOrderByNumber($orderNumber);

        if (!$order) {
            $this->notFound('Order not found.');
        }

        $this->setMeta('Order Confirmation - ' . $orderNumber);
        return $this->view('checkout.confirmation', ['order' => $order]);
    }

    /**
     * Get available shipping methods from DB
     */
    private function getShippingMethods(): array
    {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->query("SELECT * FROM shipping_methods WHERE status = 1 ORDER BY sort_order ASC");
            $methods = $stmt->fetchAll();

            if (empty($methods)) {
                return $this->getDefaultShippingMethods();
            }

            return $methods;
        } catch (\Exception $e) {
            return $this->getDefaultShippingMethods();
        }
    }

    /**
     * Fallback shipping methods when DB table is empty
     */
    private function getDefaultShippingMethods(): array
    {
        return [
            [
                'id' => 0,
                'name' => \App\Models\Setting::get('shipping_label', 'Standard Shipping'),
                'slug' => 'standard',
                'description' => 'Estimated delivery in 5-7 business days.',
                'base_rate' => (float) \App\Models\Setting::get('shipping_cost', STANDARD_SHIPPING),
                'free_above' => (float) \App\Models\Setting::get('free_shipping_min', FREE_SHIPPING_MIN),
                'estimated_days_min' => 5,
                'estimated_days_max' => 7,
                'has_tracking' => 0,
            ],
            [
                'id' => 0,
                'name' => \App\Models\Setting::get('express_shipping_label', 'Express Shipping'),
                'slug' => 'express',
                'description' => 'Estimated delivery in 2-3 business days.',
                'base_rate' => (float) \App\Models\Setting::get('express_shipping_cost', EXPRESS_SHIPPING),
                'free_above' => null,
                'estimated_days_min' => 2,
                'estimated_days_max' => 3,
                'has_tracking' => 1,
            ],
        ];
    }

    /**
     * Get shipping method name by ID
     */
    private function getShippingMethodName(int $methodId): string
    {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT name FROM shipping_methods WHERE id = :id AND status = 1 LIMIT 1");
            $stmt->execute([':id' => $methodId]);
            $method = $stmt->fetch();
            if ($method) {
                return $method['name'];
            }
        } catch (\Exception $e) {
        }
        $defaults = $this->getDefaultShippingMethods();
        foreach ($defaults as $d) {
            if ($d['id'] === $methodId) return $d['name'];
        }
        return 'Standard Shipping';
    }

    /**
     * Calculate shipping cost for selected method
     */
    private function getShippingCost(int $methodId, float $subtotal): float
    {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM shipping_methods WHERE id = :id AND status = 1 LIMIT 1");
            $stmt->execute([':id' => $methodId]);
            $method = $stmt->fetch();

            if ($method) {
                $freeAbove = (float)($method['free_above'] ?? 0);
                if ($freeAbove > 0 && $subtotal >= $freeAbove) {
                    return 0;
                }
                return (float)($method['base_rate'] ?? 0);
            }
        } catch (\Exception $e) {
        }

        $freeMin = (float) \App\Models\Setting::get('free_shipping_min', FREE_SHIPPING_MIN);
        $stdCost = (float) \App\Models\Setting::get('shipping_cost', STANDARD_SHIPPING);
        if ($subtotal >= $freeMin) {
            return 0;
        }

        return $stdCost;
    }
}
