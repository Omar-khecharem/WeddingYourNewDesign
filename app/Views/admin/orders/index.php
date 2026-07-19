<?php
$orders = $orders ?? [];
$pagination = $pagination ?? ['currentPage' => 1, 'totalPages' => 1, 'hasPrev' => false, 'hasNext' => false, 'prevPage' => 1, 'nextPage' => 1];
$statusFilter = $statusFilter ?? '';
$search = $_GET['search'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
?>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Orders</h1>
                    <p class="text-sm text-gray-500">Manage your customer orders</p>
                </div>
                <span class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg border border-gray-200"><?= $pagination['totalItems'] ?? count($orders) ?> orders</span>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
                <form method="GET" action="<?= url('13091998/orders') ?>" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                            <option value="">All Statuses</option>
                            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $statusFilter === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $statusFilter === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="delivered" <?= $statusFilter === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                        <input type="date" name="date_from" value="<?= e($dateFrom) ?>" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                        <input type="date" name="date_to" value="<?= e($dateTo) ?>" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="relative">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Order number..." class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        </div>
                    </div>
                    <button type="submit" class="bg-primary-red text-white px-5 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all">Filter</button>
                    <a href="<?= url('13091998/orders') ?>" class="border border-gray-300 text-gray-600 px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Reset</a>
                </form>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Order</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Customer</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Date</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Total</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Payment</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-box-open text-3xl mb-2 block text-gray-300"></i>
                                    No orders found
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <a href="<?= url('13091998/orders/' . e($order['id'])) ?>" class="font-medium text-primary-red hover:underline">#<?= e($order['order_number'] ?? $order['id']) ?></a>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="text-gray-800 font-medium"><?= e($order['billing_name'] ?? $order['customer_name'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-gray-400"><?= e($order['billing_email'] ?? '') ?></div>
                                </td>
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap"><?= formatDate($order['created_at'] ?? '') ?></td>
                                <td class="px-5 py-3 font-medium text-gray-800"><?= formatPrice($order['total'] ?? 0) ?></td>
                                <td class="px-5 py-3">
                                    <?php $pay = $order['payment_status'] ?? ''; ?>
                                    <?php if ($pay === 'paid' || $pay === 'completed'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Paid</span>
                                    <?php elseif ($pay === 'pending'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                                    <?php elseif ($pay === 'failed'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Failed</span>
                                    <?php elseif ($pay === 'refunded'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Refunded</span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600"><?= e($pay ?: 'N/A') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3">
                                    <?php $st = $order['order_status'] ?? ''; ?>
                                    <?php if (in_array($st, ['confirmed', 'completed', 'delivered'])): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700"><?= $st === 'delivered' ? 'Delivered' : 'Confirmed' ?></span>
                                    <?php elseif ($st === 'pending'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                                    <?php elseif ($st === 'processing'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Processing</span>
                                    <?php elseif ($st === 'shipped'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Shipped</span>
                                    <?php elseif ($st === 'cancelled'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Cancelled</span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600"><?= e($st ?: 'N/A') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="<?= url('13091998/orders/' . e($order['id'])) ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="View"><i class="fa-solid fa-eye"></i></a>
                                        <a href="<?= url('13091998/orders/' . e($order['id'])) ?>" class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors" title="View Details"><i class="fa-solid fa-pen"></i></a>
                                        <button onclick="confirmDelete(<?= e($order['id']) ?>)" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?= \App\Core\View::component('Pagination', [
                'currentPage' => $pagination['currentPage'],
                'totalPages' => $pagination['totalPages'],
                'baseUrl' => url('13091998/orders?') . ($statusFilter ? 'status=' . e($statusFilter) . '&' : '') . ($search ? 'search=' . e($search) . '&' : '') . ($dateFrom ? 'date_from=' . e($dateFrom) . '&' : '') . ($dateTo ? 'date_to=' . e($dateTo) . '&' : ''),
            ]) ?>
<?php startSection('scripts') ?>
<script>
function confirmDelete(id){if(confirm('Are you sure you want to delete this order?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/orders/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}
</script>
<?php endSection() ?>
