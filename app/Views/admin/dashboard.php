<?php
$stats = $stats ?? [];
$recentOrders = $recentOrders ?? [];
$greeting = date('H') < 12 ? 'Good morning' : (date('H') < 18 ? 'Good afternoon' : 'Good evening');
?>
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5"><?= $greeting ?>, <?= e($authUser['name'] ?? 'Admin') ?> — here's what's happening today.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= url('') ?>" target="_blank" class="inline-flex items-center gap-1.5 border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 hover:text-gray-800 transition-all">
            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Visit Site
        </a>
        <a href="<?= url('13091998/clear-cache') ?>" class="inline-flex items-center gap-1.5 bg-primary-red text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all shadow-sm shadow-red-200">
            <i class="fa-solid fa-rotate text-xs"></i> Clear Cache
        </a>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 md:gap-4 mb-8">
    <?php
    $cards = [
        ['label' => 'Total Revenue', 'value' => formatPrice($stats['total_revenue'] ?? 0), 'sub' => ($stats['revenue_growth'] ?? 0) . '% vs last month', 'icon' => 'fa-indian-rupee-sign', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'subColor' => 'text-emerald-600', 'subIcon' => 'fa-arrow-up'],
        ['label' => 'Orders', 'value' => $stats['total_orders'] ?? 0, 'sub' => 'Total orders placed', 'icon' => 'fa-box', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'subColor' => 'text-gray-500', 'subIcon' => ''],
        ['label' => 'Customers', 'value' => $stats['total_users'] ?? 0, 'sub' => 'Registered accounts', 'icon' => 'fa-users', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'subColor' => 'text-gray-500', 'subIcon' => ''],
        ['label' => 'Products', 'value' => $stats['total_products'] ?? 0, 'sub' => 'Active in catalog', 'icon' => 'fa-cube', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'subColor' => 'text-gray-500', 'subIcon' => ''],
        ['label' => 'Pending Orders', 'value' => $stats['pending_orders'] ?? 0, 'sub' => 'Awaiting processing', 'icon' => 'fa-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'subColor' => 'text-amber-600', 'subIcon' => ''],
        ['label' => 'Low Stock', 'value' => $stats['low_stock_count'] ?? 0, 'sub' => 'Need reordering', 'icon' => 'fa-exclamation-triangle', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'subColor' => 'text-rose-600', 'subIcon' => ''],
    ];
    ?>
    <?php foreach ($cards as $card): ?>
    <div class="card-hover bg-white rounded-xl border border-gray-200 p-4 md:p-5 transition-all">
        <div class="flex items-center justify-between mb-2 md:mb-3">
            <p class="text-xs md:text-sm text-gray-500 font-medium truncate"><?= $card['label'] ?></p>
            <div class="w-8 h-8 md:w-10 md:h-10 <?= $card['bg'] ?> rounded-lg flex items-center justify-center <?= $card['text'] ?> flex-shrink-0"><i class="fa-solid <?= $card['icon'] ?> text-xs md:text-sm"></i></div>
        </div>
        <p class="text-lg md:text-2xl font-extrabold text-gray-800 truncate"><?= $card['value'] ?></p>
        <p class="text-[10px] md:text-xs <?= $card['subColor'] ?> mt-1 truncate"><?php if ($card['subIcon']): ?><i class="fa-solid <?= $card['subIcon'] ?> mr-0.5"></i><?php endif; ?><?= $card['sub'] ?></p>
    </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-100">
            <div>
                <h2 class="text-base md:text-lg font-bold text-gray-800">Recent Orders</h2>
                <p class="text-xs text-gray-400 mt-0.5">Latest 5 transactions</p>
            </div>
            <a href="<?= url('13091998/orders') ?>" class="text-sm text-primary-red font-medium hover:underline whitespace-nowrap">View All <i class="fa-solid fa-arrow-right ml-1 text-xs"></i></a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 text-left">
                        <th class="px-4 md:px-5 py-3 text-gray-500 font-semibold text-[10px] md:text-xs uppercase tracking-wider">Order</th>
                        <th class="px-4 md:px-5 py-3 text-gray-500 font-semibold text-[10px] md:text-xs uppercase tracking-wider hidden sm:table-cell">Customer</th>
                        <th class="px-4 md:px-5 py-3 text-gray-500 font-semibold text-[10px] md:text-xs uppercase tracking-wider hidden md:table-cell">Date</th>
                        <th class="px-4 md:px-5 py-3 text-gray-500 font-semibold text-[10px] md:text-xs uppercase tracking-wider">Total</th>
                        <th class="px-4 md:px-5 py-3 text-gray-500 font-semibold text-[10px] md:text-xs uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400"><i class="fa-solid fa-receipt text-2xl mb-2 block text-gray-300"></i>No recent orders</td></tr>
                    <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 md:px-5 py-3">
                            <a href="<?= url('13091998/orders/' . e($order['id'])) ?>" class="font-semibold text-primary-red hover:underline text-xs md:text-sm">#<?= e($order['order_number'] ?? $order['id']) ?></a>
                        </td>
                        <td class="px-4 md:px-5 py-3 text-gray-700 hidden sm:table-cell text-xs md:text-sm"><?= e($order['customer_name'] ?? $order['billing_name'] ?? 'N/A') ?></td>
                        <td class="px-4 md:px-5 py-3 text-gray-500 hidden md:table-cell text-xs"><?= formatDate($order['created_at'] ?? '') ?></td>
                        <td class="px-4 md:px-5 py-3 font-semibold text-gray-800 text-xs md:text-sm"><?= formatPrice($order['total'] ?? 0) ?></td>
                        <td class="px-4 md:px-5 py-3">
                            <?php $s = $order['order_status'] ?? ''; $badges = ['confirmed'=>'bg-green-100 text-green-700','completed'=>'bg-green-100 text-green-700','pending'=>'bg-amber-100 text-amber-700','processing'=>'bg-blue-100 text-blue-700','shipped'=>'bg-indigo-100 text-indigo-700','cancelled'=>'bg-rose-100 text-rose-700','refunded'=>'bg-gray-100 text-gray-600']; $class = $badges[$s] ?? 'bg-gray-100 text-gray-600'; ?>
                            <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-1 rounded-full text-[10px] md:text-xs font-medium <?= $class ?> capitalize"><?= e($s ?: 'N/A') ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
        <div>
            <h2 class="text-base md:text-lg font-bold text-gray-800">Quick Actions</h2>
            <p class="text-xs text-gray-400 mt-0.5 mb-4">Common admin tasks</p>
        </div>
        <div class="space-y-2">
            <?php
            $actions = [
                ['url' => url('13091998/products/create'), 'icon' => 'fa-plus', 'icBg' => 'bg-emerald-100', 'icColor' => 'text-emerald-600', 'title' => 'Add Product', 'desc' => 'New catalog item'],
                ['url' => url('13091998/orders'), 'icon' => 'fa-truck', 'icBg' => 'bg-blue-100', 'icColor' => 'text-blue-600', 'title' => 'View Orders', 'desc' => 'Manage transactions'],
                ['url' => url('13091998/coupons?action=create'), 'icon' => 'fa-tag', 'icBg' => 'bg-amber-100', 'icColor' => 'text-amber-600', 'title' => 'Create Coupon', 'desc' => 'New promotion'],
                ['url' => url('13091998/banners'), 'icon' => 'fa-images', 'icBg' => 'bg-violet-100', 'icColor' => 'text-violet-600', 'title' => 'Manage Banners', 'desc' => 'Homepage slides'],
                ['url' => url('13091998/settings'), 'icon' => 'fa-gear', 'icBg' => 'bg-gray-200', 'icColor' => 'text-gray-600', 'title' => 'Settings', 'desc' => 'Site configuration'],
            ];
            ?>
            <?php foreach ($actions as $a): ?>
            <a href="<?= $a['url'] ?>" class="flex items-center gap-3 px-3 md:px-4 py-2.5 md:py-3 bg-gray-50/50 rounded-lg hover:bg-primary-red/5 hover:border-primary-red border border-gray-100 transition-all group">
                <div class="w-8 h-8 md:w-9 md:h-9 rounded-lg <?= $a['icBg'] ?> flex items-center justify-center <?= $a['icColor'] ?> group-hover:scale-110 transition-transform flex-shrink-0"><i class="fa-solid <?= $a['icon'] ?> text-xs"></i></div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate"><?= $a['title'] ?></p>
                    <p class="text-[10px] md:text-xs text-gray-400 truncate"><?= $a['desc'] ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
