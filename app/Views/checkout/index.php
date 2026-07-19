<?php if (empty($cart['items'])): ?>
<div class="max-w-2xl mx-auto text-center py-16">
    <div class="text-gray-400 mb-6">
        <svg class="mx-auto h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
        </svg>
    </div>
    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Your cart is empty</h2>
    <p class="text-gray-500 mb-8">Add items before placing an order.</p>
    <a href="<?= url('products') ?>" class="inline-block bg-gray-900 text-white px-8 py-3 rounded-lg hover:bg-gray-800 transition">
        View Products
    </a>
</div>
<?php else: ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    <div class="lg:grid lg:grid-cols-5 lg:gap-10">
        <!-- Left column: Forms -->
        <div class="lg:col-span-3">
            <!-- Authentication -->
            <?php if (!$user): ?>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-700">Already have an account?</p>
                    <p class="text-xs text-gray-500">Log in for faster checkout.</p>
                </div>
                <a href="<?= url('login') ?>" class="bg-gray-900 text-white px-5 py-2 rounded-lg text-sm hover:bg-gray-800 transition">Log In</a>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('checkout/place-order') ?>" id="checkout-form">
                <!-- Billing address -->
                <div class="border border-gray-200 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Billing Address</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="billing_name" value="<?= $defaultAddress['full_name'] ?? ($user['name'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="billing_email" value="<?= $user['email'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="billing_phone" value="<?= $defaultAddress['phone'] ?? ($user['phone'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                            <input type="text" name="billing_pincode" value="<?= $defaultAddress['postal_code'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="billing_address" value="<?= $defaultAddress['address_line1'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                            <input type="text" name="billing_address2" value="<?= $defaultAddress['address_line2'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="billing_city" value="<?= $defaultAddress['city'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State / Region</label>
                            <input type="text" name="billing_state" value="<?= $defaultAddress['state'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none" required>
                        </div>
                    </div>
                </div>

                <!-- Same as shipping checkbox -->
                <div class="mb-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="same-shipping" name="different_shipping" value="1" class="w-4 h-4 border-gray-300 rounded focus:ring-gray-900">
                        <span class="text-sm font-medium text-gray-700">My shipping address is different from billing</span>
                    </label>
                </div>

                <!-- Shipping address -->
                <div id="shipping-address" class="border border-gray-200 rounded-lg p-6 mb-6 hidden">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="shipping_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="shipping_address" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                            <input type="text" name="shipping_address2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="shipping_city" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State / Region</label>
                            <input type="text" name="shipping_state" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                            <input type="text" name="shipping_pincode" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none">
                        </div>
                    </div>
                </div>

                <!-- Shipping method -->
                <?php if (!empty($shippingMethods)): ?>
                <div class="border border-gray-200 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Method</h2>
                    <div class="space-y-3">
                        <?php foreach ($shippingMethods as $method): ?>
                        <?php
                            $shippingAmount = $method['base_rate'];
                            $free = !empty($method['free_above']) && $cart['subtotal'] >= $method['free_above'];
                            if ($free) $shippingAmount = 0;
                        ?>
                        <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-gray-400 transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                            <input type="radio" name="shipping_method" value="<?= $method['id'] ?>" class="mt-0.5 w-4 h-4 text-gray-900 focus:ring-gray-900" <?= $loop->first ?? '' ? 'checked' : '' ?>>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900"><?= $method['name'] ?></span>
                                    <span class="font-semibold text-gray-900">
                                        <?php if ($free): ?>
                                        <span class="text-green-600">Free</span>
                                        <?php else: ?>
                                        <?= number_format($shippingAmount, 2) ?> <?= APP_CURRENCY ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1"><?= $method['description'] ?></p>
                                <p class="text-xs text-gray-400 mt-0.5">Estimated delivery: <?= $method['estimated_days_min'] ?>–<?= $method['estimated_days_max'] ?> business days</p>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Payment method -->
                <div class="border border-gray-200 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h2>
                    <div class="space-y-3">
                        <?php if (!empty($paymentMethods)): ?>
                        <?php foreach ($paymentMethods as $method): ?>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-gray-400 transition has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                            <input type="radio" name="payment_method" value="<?= $method['id'] ?>" class="w-4 h-4 text-gray-900 focus:ring-gray-900" <?= $loop->first ?? '' ? 'checked' : '' ?>>
                            <?php if (!empty($method['logo'])): ?>
                            <img src="<?= $method['logo'] ?>" alt="<?= $method['name'] ?>" class="h-6">
                            <?php endif; ?>
                            <div>
                                <span class="font-medium text-gray-900"><?= $method['name'] ?></span>
                                <?php if ($method['description']): ?>
                                <p class="text-xs text-gray-500"><?= $method['description'] ?></p>
                                <?php endif; ?>
                            </div>
                        </label>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg opacity-60 cursor-not-allowed bg-gray-50">
                            <input type="radio" name="payment_method" value="razorpay" class="w-4 h-4 text-gray-900" disabled>
                            <div>
                                <span class="font-medium text-gray-900">Pay Online (Card/UPI/Net Banking)</span>
                                <p class="text-xs text-gray-500">Secure payment via Razorpay</p>
                            </div>
                        </label>
                    </div>
                </div>

                <?= \App\Helpers\Security::csrfField() ?>
                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-lg font-medium text-base hover:bg-gray-800 transition">
                    Place Order
                </button>
            </form>
        </div>

        <!-- Right column: Order summary -->
        <div class="lg:col-span-2 mt-8 lg:mt-0">
            <div class="border border-gray-200 rounded-lg p-6 sticky top-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>

                <div class="space-y-4 mb-6">
                    <?php foreach ($cart['items'] as $item): ?>
                     <div class="flex items-start gap-3">
                        <a href="<?= url('product/' . $item['slug']) ?>">
                            <img src="<?= $item['image'] ?: asset('images/placeholder.png') ?>" alt="<?= $item['name'] ?>" class="w-16 h-16 object-cover rounded-lg flex-shrink-0" onerror="this.src='<?= asset('images/placeholder.png') ?>'">
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="<?= url('product/' . $item['slug']) ?>" class="text-sm font-medium text-gray-900 hover:text-premium-crimson truncate block"><?= $item['name'] ?></a>
                            <p class="text-xs text-gray-500">Qty : <?= $item['quantity'] ?></p>
                        </div>
                        <span class="text-sm font-medium text-gray-900 whitespace-nowrap"><?= number_format($item['total'] ?? ($item['unit_price'] * $item['quantity']), 2) ?> <?= APP_CURRENCY ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($cart['coupon_code'])): ?>
                <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-4">
                    <span class="text-sm font-medium text-green-800">Code : <?= $cart['coupon_code'] ?></span>
                    <span class="text-sm font-semibold text-green-600">−<?= number_format($cart['coupon_discount'] ?? 0, 2) ?> <?= APP_CURRENCY ?></span>
                </div>
                <?php endif; ?>

                <div class="space-y-3 text-sm border-t border-gray-200 pt-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-gray-900"><?= number_format($cart['subtotal'], 2) ?> <?= APP_CURRENCY ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium text-gray-900">
                            <?php if ($cart['shipping'] == 0): ?>
                            <span class="text-green-600">Free</span>
                            <?php else: ?>
                            <?= number_format($cart['shipping'], 2) ?> <?= APP_CURRENCY ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">TVA</span>
                        <span class="font-medium text-gray-900"><?= number_format($cart['tax'], 2) ?> <?= APP_CURRENCY ?></span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-3 text-base">
                        <span class="font-semibold text-gray-900">Total</span>
                        <span class="font-bold text-xl text-gray-900"><?= number_format($cart['total'], 2) ?> <?= APP_CURRENCY ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('same-shipping');
    const shippingDiv = document.getElementById('shipping-address');

    if (checkbox && shippingDiv) {
        function toggleShipping() {
            if (checkbox.checked) {
                shippingDiv.classList.add('hidden');
            } else {
                shippingDiv.classList.remove('hidden');
            }
        }
        checkbox.addEventListener('change', toggleShipping);
        toggleShipping();
    }

    // AJAX form submit — prevent raw JSON display
    var form = document.getElementById('checkout-form');
    var submitBtn = form ? form.querySelector('button[type="submit"]') : null;
    if (form && submitBtn) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...';

            var data = new URLSearchParams(new FormData(form));

            fetch(form.action, {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (res) { return res.json(); })
            .then(function (d) {
                if (d.success && d.redirect) {
                    window.location.href = d.redirect;
                } else {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Place Order';
                    checkoutToast(d.message || 'Something went wrong.', 'error');
                }
            })
            .catch(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Place Order';
                checkoutToast('Network error. Please try again.', 'error');
            });
        });
    }

    function checkoutToast(message, type) {
        type = type || 'error';
        var colors = { success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-yellow-500', info: 'bg-blue-500' };
        var toast = document.createElement('div');
        toast.className = 'fixed top-24 right-5 z-[9999] ' + (colors[type] || colors.error) + ' text-white px-5 py-3 rounded-lg shadow-lg';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(function () { toast.remove(); }, 300);
        }, 4000);
    }
});
</script>
