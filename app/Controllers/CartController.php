<?php
/**
 * Cart Controller
 * 
 * Handles shopping cart operations: view cart, add/remove items,
 * update quantities, apply/remove coupons, and AJAX endpoints
 * for a seamless cart experience.
 *
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\CartService;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct()
    {
        parent::__construct();
        $this->cartService = new CartService();
    }

    /**
     * Display cart page
     */
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Shopping Cart');
        $this->setBreadcrumb([
            ['label' => 'Home', 'url' => url('')],
            ['label' => 'Cart'],
        ]);

        $cart = $this->cartService->getCart();

        return $this->view('cart.index', [
            'cart' => $cart,
            'freeShippingMin' => FREE_SHIPPING_MIN,
            'standardShipping' => STANDARD_SHIPPING,
            'expressShipping' => EXPRESS_SHIPPING,
            'taxRate' => TAX_RATE,
        ]);
    }

    /**
     * Add item to cart (AJAX)
     */
    public function add(Request $request, Response $response): void
    {
        $productId = (int)$request->input('product_id');
        $quantity = max(1, (int)$request->input('quantity', 1));
        $variantId = $request->input('variant_id') ? (int)$request->input('variant_id') : null;

        $result = $this->cartService->add($productId, $quantity, $variantId);

        if ($result['success']) {
            $cart = $this->cartService->getCart();
            $result['cart_count'] = $cart['total_items'];
            $result['cart_total'] = $cart['total'];
        } else {
            $result['cart_count'] = 0;
            $result['cart_total'] = 0;
        }

        $this->json($result);
    }

    /**
     * Update cart item quantity (AJAX)
     */
    public function update(Request $request, Response $response): void
    {
        $itemId = (int)$request->input('item_id');
        $quantity = (int)$request->input('quantity');

        $result = $this->cartService->update($itemId, $quantity);

        if ($result['success']) {
            $cart = $this->cartService->getCart();
            $result['cart'] = $cart;
        }

        $this->json($result);
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function remove(Request $request, Response $response): void
    {
        $itemId = (int)$request->input('item_id');

        $result = $this->cartService->remove($itemId);

        if ($result['success']) {
            $cart = $this->cartService->getCart();
            $result['cart'] = $cart;
        }

        $this->json($result);
    }

    /**
     * Apply coupon (AJAX)
     */
    public function applyCoupon(Request $request, Response $response): void
    {
        $code = $request->input('code', '');
        $result = $this->cartService->applyCoupon($code);

        if ($result['success']) {
            $cart = $this->cartService->getCart();
            $result['cart'] = $cart;
        }

        $this->json($result);
    }

    /**
     * Remove coupon (AJAX)
     */
    public function removeCoupon(Request $request, Response $response): void
    {
        $this->cartService->removeCoupon();
        $cart = $this->cartService->getCart();
        $this->json(['success' => true, 'cart' => $cart]);
    }

    /**
     * Get cart count (AJAX)
     */
    public function getCount(Request $request, Response $response): void
    {
        $cart = $this->cartService->getCart();
        $this->json([
            'count' => $cart['total_items'],
            'total' => $cart['total'],
        ]);
    }
}
