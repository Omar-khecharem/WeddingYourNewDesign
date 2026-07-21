<?php
$order = $order ?? [];
$items = $order['items'] ?? [];
$timeline = $order['timeline'] ?? [];
?>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <a href="<?= url('13091998/orders') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Orders</a>
                    <h1 class="text-2xl font-bold text-gray-800">Order #<?= e($order['order_number'] ?? $order['id']) ?></h1>
                    <p class="text-sm text-gray-500">Placed on <?= formatDate($order['created_at'] ?? '', 'd M Y \a\t H:i') ?></p>
                </div>
                <div class="flex gap-2">
                    <a href="<?= url('13091998/orders/' . e($order['id']) . '/invoice') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors"><i class="fa-solid fa-file-pdf mr-1.5"></i> Download Invoice</a>
                    <a href="<?= url('13091998/orders/' . e($order['id']) . '/print') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors"><i class="fa-solid fa-print mr-1.5"></i> Print</a>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Ordered Items</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-left">
                                        <th class="px-4 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Product</th>
                                        <th class="px-4 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">SKU</th>
                                        <th class="px-4 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Price</th>
                                        <th class="px-4 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-200">
                                                    <?php
                                                    $img = $item['image'] ?? '';
                                                    if (empty($img) && !empty($item['product_id'])) {
                                                        $imgs = \App\Models\Product::getImages((int)$item['product_id']);
                                                        $img = $imgs[0]['url'] ?? '';
                                                    }
                                                    ?>
                                                    <?php if ($img): ?>
                                                    <img src="<?= e($img) ?>" alt="<?= e($item['product_name'] ?? '') ?>" class="w-full h-full object-cover" loading="lazy">
                                                    <?php else: ?>
                                                    <i class="fa-solid fa-image text-gray-400"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="font-medium text-gray-800"><?= e($item['product_name'] ?? '') ?></span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500"><?= e($item['product_sku'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-3 text-gray-700"><?= formatPrice($item['unit_price'] ?? 0) ?></td>
                                        <td class="px-4 py-3 text-gray-700"><?= (int)($item['quantity'] ?? 1) ?></td>
                                        <td class="px-4 py-3 font-medium text-gray-800"><?= formatPrice($item['total_price'] ?? 0) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <?php if (!empty($order['subtotal'])): ?>
                                    <tr><td colspan="4" class="px-4 py-2 text-right text-sm text-gray-500">Subtotal</td><td class="px-4 py-2 font-medium text-gray-700"><?= formatPrice($order['subtotal']) ?></td></tr>
                                    <?php endif; ?>
                                    <?php if (!empty($order['discount'])): ?>
                                    <tr><td colspan="4" class="px-4 py-2 text-right text-sm text-gray-500">Discount</td><td class="px-4 py-2 font-medium text-red-600">-<?= formatPrice($order['discount']) ?></td></tr>
                                    <?php endif; ?>
                                    <?php if (!empty($order['shipping_cost'])): ?>
                                    <tr><td colspan="4" class="px-4 py-2 text-right text-sm text-gray-500">Shipping</td><td class="px-4 py-2 font-medium text-gray-700"><?= formatPrice($order['shipping_cost']) ?></td></tr>
                                    <?php endif; ?>
                                    <?php if (!empty($order['tax'])): ?>
                                    <tr><td colspan="4" class="px-4 py-2 text-right text-sm text-gray-500">Tax</td><td class="px-4 py-2 font-medium text-gray-700"><?= formatPrice($order['tax']) ?></td></tr>
                                    <?php endif; ?>
                                    <tr><td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-gray-800">Total</td><td class="px-4 py-3 font-bold text-gray-800 text-base"><?= formatPrice($order['total'] ?? 0) ?></td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Status Update</h2>
                        <form method="POST" action="<?= url('13091998/orders/status') ?>" class="flex flex-wrap items-end gap-3">
                            <?= \App\Helpers\Security::csrfField() ?>
                            <input type="hidden" name="order_id" value="<?= (int)($order['id'] ?? 0) ?>">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Order Status</label>
                                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                                    <option value="pending" <?= ($order['order_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="processing" <?= ($order['order_status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="shipped" <?= ($order['order_status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= ($order['order_status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="cancelled" <?= ($order['order_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Payment Status</label>
                                <select name="payment_status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                                    <option value="pending" <?= ($order['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="completed" <?= in_array($order['payment_status'] ?? '', ['paid', 'completed']) ? 'selected' : '' ?>>Paid</option>
                                    <option value="failed" <?= ($order['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                                    <option value="refunded" <?= ($order['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                                </select>
                            </div>
                            <button type="submit" class="bg-primary-red text-white px-5 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all">Update</button>
                        </form>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Timeline</h2>
                        <div class="space-y-0">
                            <?php if (empty($timeline)): ?>
                            <p class="text-sm text-gray-400 text-center py-4">No events recorded</p>
                            <?php else: ?>
                            <?php foreach ($timeline as $event): ?>
                            <div class="flex gap-4 pb-4 relative">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 rounded-full border-2 border-primary-red bg-white z-10"></div>
                                    <div class="w-0.5 flex-1 bg-gray-200 mt-1"></div>
                                </div>
                                <div class="flex-1 -mt-1">
                                    <p class="text-sm font-medium text-gray-800"><?= e($event['status_label'] ?? $event['status']) ?></p>
                                    <p class="text-xs text-gray-400"><?= formatDate($event['created_at'] ?? $event['date'] ?? '', 'd M Y \a\t H:i') ?></p>
                                    <?php if (!empty($event['notes'])): ?>
                                    <p class="text-xs text-gray-500 mt-0.5"><?= e($event['notes']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 rounded-full bg-gray-300 z-10"></div>
                                </div>
                                <div class="flex-1 -mt-1">
                                    <p class="text-sm text-gray-400">Order Created</p>
                                    <p class="text-xs text-gray-400"><?= formatDate($order['created_at'] ?? '', 'd M Y \a\t H:i') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Billing Information</h2>
                        <div class="space-y-2 text-sm">
                            <p class="text-gray-800 font-medium"><?= e($order['billing_name'] ?? 'N/A') ?></p>
                            <p class="text-gray-500"><?= e($order['billing_email'] ?? '') ?></p>
                            <p class="text-gray-500"><?= e($order['billing_phone'] ?? '') ?></p>
                            <p class="text-gray-500"><?= e($order['billing_address'] ?? '') ?></p>
                            <p class="text-gray-500"><?= e($order['billing_city'] ?? '') ?><?= $order['billing_postal'] ? ' - ' . e($order['billing_postal']) : '' ?></p>
                            <p class="text-gray-500"><?= e($order['billing_state'] ?? '') ?><?= $order['billing_country'] ? ', ' . e($order['billing_country']) : '' ?></p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Shipping Address</h2>
                        <div class="space-y-2 text-sm">
                            <p class="text-gray-800 font-medium"><?= e($order['shipping_name'] ?? $order['billing_name'] ?? 'N/A') ?></p>
                            <p class="text-gray-500"><?= e($order['email'] ?? '') ?></p>
                            <p class="text-gray-500"><?= e($order['phone'] ?? '') ?></p>
                            <p class="text-gray-500"><?= e($order['shipping_address'] ?? $order['billing_address'] ?? '') ?></p>
                            <p class="text-gray-500"><?= e($order['shipping_city'] ?? $order['billing_city'] ?? '') ?><?= $order['shipping_postal'] ? ' - ' . e($order['shipping_postal']) : '' ?></p>
                            <p class="text-gray-500"><?= e($order['shipping_state'] ?? $order['billing_state'] ?? '') ?><?= $order['shipping_country'] ? ', ' . e($order['shipping_country']) : '' ?></p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Payment Information</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Method</span>
                                <span class="text-gray-800 font-medium"><?= e($order['payment_method_name'] ?? 'N/A') ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Transaction</span>
                                <span class="text-gray-800 font-medium"><?= e($order['payment_id'] ?? 'N/A') ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                <span>
                                    <?php $ps = $order['payment_status'] ?? ''; ?>
                                    <?php if (in_array($ps, ['paid', 'completed'])): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Paid</span>
                                    <?php elseif ($ps === 'pending'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                                    <?php elseif ($ps === 'failed'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Failed</span>
                                    <?php elseif ($ps === 'refunded'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Refunded</span>
                                    <?php else: ?>
                                    <span class="text-gray-500"><?= e($ps ?: 'N/A') ?></span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <span class="text-gray-800 font-bold">Total Paid</span>
                                <span class="text-gray-800 font-bold"><?= formatPrice($order['total'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($order['notes'])): ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Notes</h2>
                        <p class="text-sm text-gray-600"><?= nl2br(e($order['notes'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
<?php endSection() ?>
