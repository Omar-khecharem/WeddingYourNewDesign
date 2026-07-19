<?php
$logLines = $logs ?? [];
$logFile = $logFile ?? '';
$activityLogs = $activityLogs ?? [];
$couponTransactions = $couponTransactions ?? [];
$logCleanupEnabled = $logCleanupEnabled ?? '0';
$logCleanupPeriod = $logCleanupPeriod ?? 'weekly';
$lastCleanup = $lastCleanup ?? '';
$activeTab = $_GET['tab'] ?? 'error';
?>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">System Logs</h1>
                    <p class="text-sm text-gray-500">Monitor errors, activity, and coupon transactions</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="location.reload()" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                    <form method="POST" action="<?= url('13091998/logs/clean') ?>" onsubmit="return confirm('Clean all logs now? This will remove old activity logs and clear the error log file.')">
                        <?= \App\Helpers\Security::csrfField() ?>
                        <button type="submit" class="border border-red-300 text-red-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors inline-flex items-center gap-2">
                            <i class="fa-solid fa-broom"></i> Clean Logs Now
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex border-b border-gray-200 gap-1 mb-4">
                <a href="?tab=error" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors <?= $activeTab === 'error' ? 'border-primary-red text-primary-red' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                    <i class="fa-solid fa-bug mr-1.5"></i> Error Logs
                </a>
                <a href="?tab=activity" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors <?= $activeTab === 'activity' ? 'border-primary-red text-primary-red' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                    <i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Activity Logs
                </a>
                <a href="?tab=coupons" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors <?= $activeTab === 'coupons' ? 'border-primary-red text-primary-red' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                    <i class="fa-solid fa-tag mr-1.5"></i> Coupon Transactions
                </a>
            </div>
            <?php if (($logCleanupEnabled ?? '0') === '1' || !empty($lastCleanup)): ?>
            <div class="mb-4 px-4 py-2 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-3 text-gray-500">
                    <?php if (($logCleanupEnabled ?? '0') === '1'): ?>
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Auto-cleanup <strong class="text-gray-700"><?= e($logCleanupPeriod ?? 'weekly') ?></strong></span>
                    <?php endif; ?>
                    <?php if (!empty($lastCleanup)): ?>
                    <span>Last cleanup: <strong class="text-gray-700"><?= e(date('d M Y H:i', strtotime($lastCleanup))) ?></strong></span>
                    <?php endif; ?>
                </div>
                <a href="<?= url('13091998/settings#group-logs') ?>" class="text-primary-red hover:underline font-medium">Configure</a>
            </div>
            <?php endif; ?>

            <?php if ($activeTab === 'error'): ?>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        <span class="text-sm font-medium text-gray-700">Last <?= count($logLines) ?> lines</span>
                    </div>
                    <span class="text-xs text-gray-400">Auto-refresh: <button id="auto-refresh-btn" class="text-primary-red hover:underline font-medium" onclick="toggleAutoRefresh()">Enable</button></span>
                </div>
                <div class="overflow-x-auto">
                    <?php if (empty($logLines)): ?>
                    <div class="p-12 text-center">
                        <i class="fa-solid fa-check-circle text-4xl text-green-400 mb-3 block"></i>
                        <p class="text-gray-500 font-medium">No errors found</p>
                        <p class="text-sm text-gray-400 mt-1">The log file is empty or does not exist.</p>
                    </div>
                    <?php else: ?>
                    <div class="p-0">
                        <pre class="text-xs leading-6 font-mono text-gray-300 bg-gray-900 p-5 overflow-x-auto max-h-[600px] overflow-y-auto whitespace-pre-wrap break-all"><?php
                            $lines = array_slice($logLines, -200);
                            foreach ($lines as $index => $line):
                            $severity = '';
                            if (stripos($line, 'error') !== false || stripos($line, 'fatal') !== false) $severity = 'text-red-400';
                            elseif (stripos($line, 'warning') !== false) $severity = 'text-yellow-400';
                            elseif (stripos($line, 'notice') !== false) $severity = 'text-blue-400';
                            else $severity = 'text-gray-300';
                            ?><span class="<?= $severity ?>"><?= e($line) ?></span>
<?php endforeach; ?></pre>
                    </div>
                    <div class="bg-white border-t border-gray-200 px-5 py-3 text-xs text-gray-400 flex items-center justify-between">
                        <span>Showing <?= count($logLines) ?> last lines</span>
                        <span>Last refresh: <span id="last-refresh"><?= date('H:i:s') ?></span></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php elseif ($activeTab === 'activity'): ?>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Last <?= count($activityLogs) ?> activities</span>
                </div>
                <div class="overflow-x-auto">
                    <?php if (empty($activityLogs)): ?>
                    <div class="p-12 text-center">
                        <i class="fa-solid fa-clock text-4xl text-gray-300 mb-3 block"></i>
                        <p class="text-gray-500 font-medium">No activity logs found</p>
                    </div>
                    <?php else: ?>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Date</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">User</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Description</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($activityLogs as $log): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap text-xs"><?= e($log['created_at'] ?? '') ?></td>
                                <td class="px-5 py-3 text-gray-700"><?= e($log['user_name'] ?? 'System') ?></td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700"><?= e($log['type'] ?? '') ?></span>
                                </td>
                                <td class="px-5 py-3 text-gray-600"><?= e($log['description'] ?? '') ?></td>
                                <td class="px-5 py-3 text-gray-400 text-xs font-mono"><?= e($log['ip_address'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
            <?php elseif ($activeTab === 'coupons'): ?>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Last <?= count($couponTransactions) ?> coupon transactions</span>
                </div>
                <div class="overflow-x-auto">
                    <?php if (empty($couponTransactions)): ?>
                    <div class="p-12 text-center">
                        <i class="fa-solid fa-tag text-4xl text-gray-300 mb-3 block"></i>
                        <p class="text-gray-500 font-medium">No coupon transactions found</p>
                        <p class="text-sm text-gray-400 mt-1">Coupon usage will appear here when customers use coupons.</p>
                    </div>
                    <?php else: ?>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Date</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Order</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Customer</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Coupon Code</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Discount</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Order Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($couponTransactions as $tx): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap text-xs"><?= e($tx['created_at'] ?? '') ?></td>
                                <td class="px-5 py-3">
                                    <a href="<?= url('13091998/orders/' . e($tx['id'])) ?>" class="text-primary-red hover:underline font-medium">#<?= e($tx['order_number'] ?? $tx['id']) ?></a>
                                </td>
                                <td class="px-5 py-3 text-gray-700"><?= e($tx['user_name'] ?? 'Guest') ?></td>
                                <td class="px-5 py-3">
                                    <span class="font-mono font-bold text-primary-red"><?= e($tx['coupon_code'] ?? '') ?></span>
                                </td>
                                <td class="px-5 py-3 text-green-600 font-medium">-<?= formatPrice($tx['coupon_discount'] ?? 0) ?></td>
                                <td class="px-5 py-3 text-gray-800 font-medium"><?= formatPrice($tx['total'] ?? 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
<?php startSection('scripts') ?>
<script>
let autoRefreshInterval = null;

function toggleAutoRefresh() {
    const btn = document.getElementById('auto-refresh-btn');
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
        btn.textContent = 'Enable';
        btn.classList.remove('text-red-600');
        btn.classList.add('text-primary-red');
    } else {
        autoRefreshInterval = setInterval(function() {
            location.reload();
        }, 10000);
        btn.textContent = 'Disable (10s)';
        btn.classList.remove('text-primary-red');
        btn.classList.add('text-red-600');
    }
}
</script>
<?php endSection() ?>