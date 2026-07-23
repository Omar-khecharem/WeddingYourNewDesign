<?php if (empty($cart['items'])): ?>
<div class="max-w-2xl mx-auto text-center py-16">
    <div class="text-gray-400 mb-6">
        <svg class="mx-auto h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
        </svg>
    </div>
    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Your cart is empty</h2>
    <p class="text-gray-500 mb-8">Browse our collection and add items to your cart.</p>
    <a href="<?= url('products') ?>" class="inline-block bg-gray-900 text-white px-8 py-3 rounded-lg hover:bg-gray-800 transition">
        Browse Products
    </a>
</div>
<?php else: ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Cart</h1>

    <div class="lg:grid lg:grid-cols-3 lg:gap-10">
        <div class="lg:col-span-2">
            <div class="overflow-hidden border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left py-4 px-4 font-medium text-gray-600">Product</th>
                            <th class="text-center py-4 px-4 font-medium text-gray-600">Unit Price</th>
                            <th class="text-center py-4 px-4 font-medium text-gray-600">Quantity</th>
                            <th class="text-right py-4 px-4 font-medium text-gray-600">Total</th>
                            <th class="py-4 px-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($cart['items'] as $item): ?>
                        <tr class="cart-item" data-item-id="<?= $item['id'] ?>">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-4">
                                    <img src="<?= $item['image'] ?: asset('images/placeholder.png') ?>" alt="<?= $item['name'] ?>" class="w-20 h-20 object-cover rounded-lg" onerror="this.src='<?= asset('images/placeholder.png') ?>'">
                                    <div>
                                        <a href="<?= url('product/' . $item['slug']) ?>" class="font-medium text-gray-900 hover:text-gray-600 transition">
                                            <?= $item['name'] ?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="unit-price font-medium text-gray-900"><?= number_format($item['unit_price'], 2) ?> <?= APP_CURRENCY ?></span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" class="qty-btn qty-down w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 transition" data-item-id="<?= $item['id'] ?>">−</button>
                                    <input type="number" value="<?= $item['quantity'] ?>" min="1" class="qty-input w-14 h-8 text-center border border-gray-300 rounded-lg text-sm" data-item-id="<?= $item['id'] ?>" data-price="<?= $item['unit_price'] ?>">
                                    <button type="button" class="qty-btn qty-up w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 transition" data-item-id="<?= $item['id'] ?>">+</button>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="line-total font-semibold text-gray-900"><?= number_format($item['line_total'] ?? ($item['unit_price'] * $item['quantity']), 2) ?> <?= APP_CURRENCY ?></span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <button type="button" class="remove-btn text-gray-400 hover:text-red-500 transition" data-item-id="<?= $item['id'] ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="<?= url('products') ?>" class="text-sm text-gray-600 hover:text-gray-900 transition inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>

        <div class="mt-8 lg:mt-0">
            <div class="border border-gray-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Cart Summary</h2>

                <!-- Coupon -->
                <div class="mb-6">
                    <?php if (!empty($cart['coupon_code'])): ?>
                    <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                        <div>
                            <span class="text-sm font-medium text-green-800">Code : <?= $cart['coupon_code'] ?></span>
                            <span class="block text-xs text-green-600">−<?= number_format($cart['discount'] ?? 0, 2) ?> <?= APP_CURRENCY ?></span>
                        </div>
                        <button type="button" id="remove-coupon" class="text-green-600 hover:text-green-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="flex gap-2">
                        <input type="text" id="coupon-input" placeholder="Coupon Code" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none">
                        <button type="button" id="apply-coupon" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition">Apply</button>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="cart-summary-subtotal font-medium text-gray-900"><?= number_format($cart['subtotal'], 2) ?> <?= APP_CURRENCY ?></span>
                    </div>

                    <?php if (($cart['discount'] ?? 0) > 0): ?>
                    <div class="flex justify-between text-green-600">
                        <span>Discount</span>
                        <span class="cart-summary-discount font-medium">−<?= number_format($cart['discount'], 2) ?> <?= APP_CURRENCY ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="flex justify-between">
                        <span class="text-gray-600">TVA</span>
                        <span class="cart-summary-tax font-medium text-gray-900"><?= number_format($cart['tax'], 2) ?> <?= APP_CURRENCY ?></span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="cart-summary-shipping font-medium text-green-600"><?= $cart['shipping'] == 0 ? 'Free' : number_format($cart['shipping'], 2) . ' ' . APP_CURRENCY ?></span>
                    </div>

                    <?php if (($cart['free_shipping_min'] ?? 0) > 0 && $cart['shipping'] == 0): ?>
                    <p class="text-xs text-green-600">Free shipping for orders above <?= number_format($cart['free_shipping_min'], 2) ?> <?= APP_CURRENCY ?></p>
                    <?php endif; ?>

                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between text-base">
                            <span class="font-semibold text-gray-900">Total</span>
                            <span class="cart-summary-total font-bold text-xl text-gray-900"><?= number_format($cart['total'], 2) ?> <?= APP_CURRENCY ?></span>
                        </div>
                    </div>
                </div>

                <a href="<?= url('checkout') ?>" class="mt-6 block w-full text-center bg-gray-900 text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition">
                    Checkout
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div id="confirm-modal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40" style="display:none">
  <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 p-6 text-center" onclick="event.stopPropagation()">
    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
      <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
      </svg>
    </div>
    <h3 class="text-lg font-bold text-gray-900 mb-2">Remove item?</h3>
    <p class="text-sm text-gray-500 mb-1">Are you sure you want to remove</p>
    <p id="confirm-product-name" class="text-sm font-semibold text-gray-800 mb-6 truncate"></p>
    <div class="flex gap-3">
      <button id="confirm-no" class="flex-1 border border-gray-300 text-gray-700 font-medium px-4 py-2.5 rounded-xl hover:bg-gray-50 transition">Cancel</button>
      <button id="confirm-yes" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        Remove
      </button>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Guest cart recovery: sync localStorage token to cookie, reload if empty
    (function() {
        var token = localStorage.getItem('cart_token');
        if (token) {
            document.cookie = 'cart_token=' + encodeURIComponent(token) + '; path=/; max-age=' + (30*24*60*60) + '; samesite=Lax';
        }
        <?php if (empty($cart['items'])): ?>
        // Cart is empty server-side — try recovering from localStorage token
        if (token && window.location.href.indexOf('cart_token') === -1) {
            var sep = window.location.href.indexOf('?') === -1 ? '?' : '&';
            window.location.href = window.location.href + sep + 'cart_token=' + encodeURIComponent(token);
            return;
        }
        <?php endif; ?>
    })();
    <?php if (!empty($cart['items'])): ?>
    var csrfToken = window.CSRF_TOKEN || '<?= \App\Helpers\Session::csrfToken() ?>';

    function postForm(url, data) {
        var formData = new URLSearchParams();
        formData.append('_csrf_token', csrfToken);
        Object.keys(data).forEach(function (k) { formData.append(k, data[k]); });
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData.toString()
        }).then(function (res) {
            return res.text().then(function (text) {
                try {
                    var d = JSON.parse(text);
                    if (!res.ok) {
                        showToast(d.message || 'Error (' + res.status + ')', 'error');
                        return null;
                    }
                    return d;
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    showToast('Request failed (' + res.status + ')', 'error');
                    return null;
                }
            });
        }).catch(function (e) {
            console.error('Fetch error:', e);
            showToast('Network error', 'error');
            return null;
        });
    }

    function updateRowPrices(row, d) {
        if (d.cart && d.cart.items) {
            d.cart.items.forEach(function (item) {
                if (item.id == row.dataset.itemId) {
                    row.querySelector('.line-total').textContent = item.total.toFixed(2) + ' ' + window.APP_CURRENCY;
                    row.querySelector('.qty-input').value = item.quantity;
                }
            });
            document.querySelector('.cart-summary-subtotal').textContent = d.cart.subtotal.toFixed(2) + ' ' + window.APP_CURRENCY;
            var discEl = document.querySelector('.cart-summary-discount');
            if (discEl) {
                discEl.textContent = '\u2212' + d.cart.discount.toFixed(2) + ' ' + window.APP_CURRENCY;
            }
            document.querySelector('.cart-summary-tax').textContent = d.cart.tax.toFixed(2) + ' ' + window.APP_CURRENCY;
            document.querySelector('.cart-summary-shipping').textContent = d.cart.shipping === 0 ? 'Free' : d.cart.shipping.toFixed(2) + ' ' + window.APP_CURRENCY;
            document.querySelector('.cart-summary-total').textContent = d.cart.total.toFixed(2) + ' ' + window.APP_CURRENCY;
            var countBadge = document.querySelector('.cart-count');
            if (countBadge) {
                countBadge.textContent = d.cart.total_items;
                countBadge.style.display = d.cart.total_items > 0 ? 'flex' : 'none';
            }
            var countBadgeMobile = document.querySelector('.cart-count-mobile');
            if (countBadgeMobile) {
                countBadgeMobile.textContent = d.cart.total_items;
                countBadgeMobile.style.display = d.cart.total_items > 0 ? 'flex' : 'none';
            }
            if (typeof updateCartTotal === 'function') {
                updateCartTotal(d.cart.total);
            }
        }
    }

    document.querySelectorAll('.qty-down').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.dataset.itemId;
            var row = this.closest('.cart-item');
            var input = row.querySelector('.qty-input');
            var qty = parseInt(input.value);
            if (qty > 1) {
                postForm('<?= url('cart/update') ?>', { item_id: id, quantity: qty - 1 }).then(function (d) {
                    if (d && d.success) {
                        updateRowPrices(row, d);
                    }
                });
            }
        });
    });

    document.querySelectorAll('.qty-up').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.dataset.itemId;
            var row = this.closest('.cart-item');
            var input = row.querySelector('.qty-input');
            var qty = parseInt(input.value);
            postForm('<?= url('cart/update') ?>', { item_id: id, quantity: qty + 1 }).then(function (d) {
                if (d && d.success) {
                    updateRowPrices(row, d);
                }
            });
        });
    });

    document.querySelectorAll('.qty-input').forEach(function (input) {
        input.addEventListener('change', function () {
            var id = this.dataset.itemId;
            var row = this.closest('.cart-item');
            var qty = parseInt(this.value);
            if (qty < 1) qty = 1;
            postForm('<?= url('cart/update') ?>', { item_id: id, quantity: qty }).then(function (d) {
                if (d && d.success) {
                    updateRowPrices(row, d);
                }
            });
        });
    });

    var _removeItemId = null;
    var _removeRow = null;
    var modalOverlay = document.getElementById('confirm-modal');
    function showRemoveModal(itemId) {
        _removeItemId = itemId;
        _removeRow = document.querySelector('.cart-item[data-item-id="' + itemId + '"]');
        if (_removeRow) {
            var name = _removeRow.querySelector('.font-medium.text-gray-900')?.textContent || '';
            document.getElementById('confirm-product-name').textContent = name;
        }
        if (modalOverlay) modalOverlay.style.display = 'flex';
    }
    function closeConfirmModal() {
        if (modalOverlay) modalOverlay.style.display = 'none';
        _removeItemId = null;
        _removeRow = null;
    }
    document.querySelectorAll('.remove-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            showRemoveModal(this.dataset.itemId);
        });
    });
    document.getElementById('confirm-yes')?.addEventListener('click', function () {
        if (_removeItemId) {
            postForm('<?= url('cart/remove') ?>', { item_id: _removeItemId }).then(function (d) {
                if (d && d.success) {
                    location.reload();
                }
            });
        }
        closeConfirmModal();
    });
    document.getElementById('confirm-no')?.addEventListener('click', closeConfirmModal);
    if (modalOverlay) modalOverlay.addEventListener('click', function (e) {
        if (e.target === this) closeConfirmModal();
    });

    <?php if (empty($cart['coupon_code'])): ?>
    document.getElementById('apply-coupon').addEventListener('click', function () {
        var code = document.getElementById('coupon-input').value.trim();
        if (!code) return;
        postForm('<?= url('cart/coupon') ?>', { code: code }).then(function (d) {
            if (d && d.success) location.reload();
        });
    });
    <?php else: ?>
    document.getElementById('remove-coupon').addEventListener('click', function () {
        postForm('<?= url('cart/coupon/remove') ?>', {}).then(function (d) {
            if (d && d.success) location.reload();
        });
    });
    <?php endif; ?>

    <?php endif; ?>
});
</script>
