<?php
$coupons = $coupons ?? [];
$showForm = $_GET['action'] ?? '';
$editCoupon = $editCoupon ?? null;
?>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Coupons</h1>
                    <p class="text-sm text-gray-500">Manage your promotional codes</p>
                </div>
                <a href="<?= url('13091998/coupons?action=create') ?>" class="bg-primary-red text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Add Coupon
                </a>
            </div>
            <?php if ($showForm === 'create' || $editCoupon): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4"><?= $editCoupon ? 'Edit Coupon' : 'New Coupon' ?></h2>
                <form method="POST" action="<?= $editCoupon ? url('13091998/coupons/update/' . e($editCoupon['id'])) : url('13091998/coupons/store') ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?= \App\Helpers\Security::csrfField() ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="<?= e(old('code', $editCoupon['code'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                            <option value="fixed" <?= (old('type', $editCoupon['type'] ?? '') === 'fixed') ? 'selected' : '' ?>>Fixed Amount</option>
                            <option value="percentage" <?= (old('type', $editCoupon['type'] ?? '') === 'percentage') ? 'selected' : '' ?>>Percentage</option>
                            <option value="free_shipping" <?= (old('type', $editCoupon['type'] ?? '') === 'free_shipping') ? 'selected' : '' ?>>Free Shipping</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Value <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="value" value="<?= e(old('value', $editCoupon['value'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Amount</label>
                        <input type="number" step="0.01" name="min_amount" value="<?= e(old('min_amount', $editCoupon['min_order_amount'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="<?= e(old('start_date', $editCoupon['starts_at'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                        <input type="date" name="expiry_date" value="<?= e(old('expiry_date', $editCoupon['expires_at'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
                        <input type="number" name="usage_limit" value="<?= e(old('usage_limit', $editCoupon['usage_limit'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                            <?php $isActive = old('status', $editCoupon['is_active'] ?? '') == 1 || old('status', $editCoupon['is_active'] ?? '') === 'active'; ?>
                            <option value="active" <?= $isActive ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= !$isActive ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 lg:col-span-3 flex gap-3 pt-2">
                        <button type="submit" class="bg-primary-red text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all">
                            <i class="fa-solid fa-save mr-1.5"></i> <?= $editCoupon ? 'Update' : 'Create Coupon' ?>
                        </button>
                        <a href="<?= url('13091998/coupons') ?>" class="border border-gray-300 text-gray-600 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Code</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Value</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Min. Amount</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Uses</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Start</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Expiry</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($coupons)): ?>
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-tag text-3xl mb-2 block text-gray-300"></i>
                                    No coupons found
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($coupons as $coupon): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <span class="font-mono font-bold text-primary-red"><?= e($coupon['code']) ?></span>
                                </td>
                                <td class="px-5 py-3 text-gray-500">
                                    <?php if (($coupon['type'] ?? '') === 'fixed'): ?>
                                    Fixed Amount
                                    <?php elseif (($coupon['type'] ?? '') === 'percentage'): ?>
                                    Percentage
                                    <?php elseif (($coupon['type'] ?? '') === 'free_shipping'): ?>
                                    Free Shipping
                                    <?php else: ?>
                                    <?= e($coupon['type'] ?? 'N/A') ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    <?php if (($coupon['type'] ?? '') === 'percentage'): ?>
                                    <?= $coupon['value'] ?>%
                                    <?php else: ?>
                                    <?= formatPrice($coupon['value'] ?? 0) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3 text-gray-500"><?= !empty($coupon['min_order_amount']) ? formatPrice($coupon['min_order_amount']) : '-' ?></td>
                                <td class="px-5 py-3 text-gray-500"><?= $coupon['used_count'] ?? 0 ?><?= $coupon['usage_limit'] ? ' / ' . $coupon['usage_limit'] : '' ?></td>
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap"><?= !empty($coupon['starts_at']) ? formatDate($coupon['starts_at']) : '-' ?></td>
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap"><?= !empty($coupon['expires_at']) ? formatDate($coupon['expires_at']) : '-' ?></td>
                                <td class="px-5 py-3">
                                    <?php if (!empty($coupon['is_active'])): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="<?= url('13091998/coupons?action=edit&id=' . e($coupon['id'])) ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                        <form method="POST" action="<?= url('13091998/coupons/delete') ?>" onsubmit="return confirm('Are you sure you want to delete this coupon?')" class="inline">
                                            <?= \App\Helpers\Security::csrfField() ?>
                                            <input type="hidden" name="id" value="<?= e($coupon['id']) ?>">
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
