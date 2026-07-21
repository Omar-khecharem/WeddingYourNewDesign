<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Models\Setting;
use App\Services\OrderService;

class DashboardController extends BaseAdminController
{
    private OrderService $orderService;

    public function __construct()
    {
        parent::__construct();
        $this->orderService = new OrderService();
    }

    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Admin Dashboard');
        $pdo = \App\Core\Database::getInstance()->getConnection();

        $stats = $this->orderService->getStats();
        $stats['total_users'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_users")->fetchColumn();
        $stats['total_products'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_products WHERE status = 1")->fetchColumn();
        $stats['low_stock_count'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_products WHERE stock_status = 'in_stock' AND stock_quantity < 5")->fetchColumn();
        $stats['total_categories'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_categories WHERE status = 1")->fetchColumn();
        $stats['total_reviews'] = (int)$pdo->query("SELECT COUNT(*) FROM sg_reviews WHERE is_approved = 0")->fetchColumn();

        $lastMonthRevenue = (float)$pdo->query("SELECT COALESCE(SUM(total), 0) FROM sg_orders WHERE MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND order_status NOT IN ('cancelled', 'refunded') AND payment_status IN ('paid', 'completed')")->fetchColumn();
        $stats['revenue_growth'] = $lastMonthRevenue > 0 ? round((($stats['total_revenue'] - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;

        $recentOrders = $pdo->query("SELECT o.*, u.name AS customer_name FROM sg_orders o LEFT JOIN sg_users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 5")->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function clearCache(Request $request, Response $response): void
    {
        $cacheService = new \App\Services\CacheService();
        $cacheService->clear();
        array_map('unlink', glob(VIEW_CACHE_DIR . DS . '*'));
        $this->flash('success', 'System cache cleared successfully.');
        $this->redirectBack();
    }

    public function logs(Request $request, Response $response): string
    {
        $this->setMeta('System Logs');
        $pdo = \App\Core\Database::getInstance()->getConnection();

        $this->autoCleanupLogs($pdo);

        $logFile = LOGS_DIR . DS . 'error.log';
        $errorLogs = [];
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            if ($content !== false) {
                $lines = explode("\n", $content);
                $errorLogs = array_reverse(array_filter($lines));
                $errorLogs = array_slice($errorLogs, 0, 200);
            }
        }

        $activityLogs = $pdo->query("
            SELECT al.*, u.name AS user_name 
            FROM sg_activity_logs al 
            LEFT JOIN sg_users u ON u.id = al.user_id 
            ORDER BY al.created_at DESC 
            LIMIT 200
        ")->fetchAll();

        $couponTransactions = $pdo->query("
            SELECT o.id, o.order_number, o.coupon_code, o.coupon_discount, o.total, o.user_id, o.created_at, u.name AS user_name
            FROM sg_orders o 
            LEFT JOIN sg_users u ON u.id = o.user_id 
            WHERE o.coupon_code IS NOT NULL AND o.coupon_code != ''
            ORDER BY o.created_at DESC 
            LIMIT 200
        ")->fetchAll();

        $logCleanupEnabled = Setting::get('log_cleanup_enabled', '0');
        $logCleanupPeriod = Setting::get('log_cleanup_period', 'weekly');
        $lastCleanup = Setting::get('log_last_cleanup', '');

        return $this->view('admin.logs', [
            'logs' => $errorLogs,
            'logFile' => $logFile,
            'activityLogs' => $activityLogs,
            'couponTransactions' => $couponTransactions,
            'logCleanupEnabled' => $logCleanupEnabled,
            'logCleanupPeriod' => $logCleanupPeriod,
            'lastCleanup' => $lastCleanup,
        ]);
    }

    public function cleanLogs(Request $request, Response $response): void
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $counts = self::purgeOldLogs($pdo);

        if (file_exists(LOGS_DIR . DS . 'error.log')) {
            file_put_contents(LOGS_DIR . DS . 'error.log', '');
        }

        Setting::set('log_last_cleanup', date('Y-m-d H:i:s'));

        $parts = [];
        if ($counts['activity'] > 0) $parts[] = "{$counts['activity']} activity log(s)";
        if ($counts['admin'] > 0) $parts[] = "{$counts['admin']} admin log(s)";
        $msg = 'Logs cleaned successfully.';
        if (!empty($parts)) $msg .= ' Removed: ' . implode(', ', $parts) . '.';

        $this->flash('success', $msg);
        $this->redirect(url('13091998/logs'));
    }

    private function autoCleanupLogs(\PDO $pdo): void
    {
        $enabled = Setting::get('log_cleanup_enabled', '0');
        if ($enabled !== '1') return;

        $period = Setting::get('log_cleanup_period', 'weekly');
        $lastCleanup = Setting::get('log_last_cleanup', '');

        $intervals = [
            'daily' => '-1 day',
            'weekly' => '-7 days',
            'monthly' => '-30 days',
        ];

        $interval = $intervals[$period] ?? '-7 days';

        if ($lastCleanup) {
            $nextCleanup = date('Y-m-d H:i:s', strtotime($interval, strtotime($lastCleanup)));
            if (date('Y-m-d H:i:s') < $nextCleanup) return;
        }

        self::purgeOldLogs($pdo);

        if (file_exists(LOGS_DIR . DS . 'error.log')) {
            file_put_contents(LOGS_DIR . DS . 'error.log', '');
        }

        Setting::set('log_last_cleanup', date('Y-m-d H:i:s'));
    }

    private static function purgeOldLogs(\PDO $pdo): array
    {
        $counts = ['activity' => 0, 'admin' => 0];

        $stmt = $pdo->query("SELECT COUNT(*) FROM sg_activity_logs");
        $totalActivity = (int)$stmt->fetchColumn();

        if ($totalActivity > 500) {
            $stmt = $pdo->prepare("DELETE FROM sg_activity_logs ORDER BY created_at ASC LIMIT :limit");
            $limit = $totalActivity - 200;
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            $counts['activity'] = $stmt->rowCount();
        }

        $stmt = $pdo->query("SELECT COUNT(*) FROM sg_admin_logs");
        $totalAdmin = (int)$stmt->fetchColumn();

        if ($totalAdmin > 500) {
            $stmt = $pdo->prepare("DELETE FROM sg_admin_logs ORDER BY created_at ASC LIMIT :limit");
            $limit = $totalAdmin - 200;
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            $counts['admin'] = $stmt->rowCount();
        }

        return $counts;
    }
}