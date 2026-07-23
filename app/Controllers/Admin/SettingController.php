<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Image;

class SettingController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Store Settings');
        $pdo = \App\Core\Database::getInstance()->getConnection();

        // Ensure logo/favicon files on disk have DB entries
        $this->ensureFileSetting($pdo, 'site_logo', 'site_logo.png');
        $this->ensureFileSetting($pdo, 'site_favicon', 'site_favicon.png');
        $this->ensureFileSetting($pdo, 'header_coupon_code', 'SGTM20');
        $this->ensureFileSetting($pdo, 'header_coupon_text', 'Get FLAT 20% OFF On Topor Mukut set. Use Code -');
        $this->ensureSetting($pdo, 'log_cleanup_enabled', '0', 'logs', 'boolean', 1, 1);
        $this->ensureSetting($pdo, 'log_cleanup_period', 'weekly', 'logs', 'select', 2, 1, json_encode(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly']));
        $this->ensureSetting($pdo, 'tax_enabled', '1', 'tax', 'boolean', 9, 1);
        $this->ensureSetting($pdo, 'maintenance_mode', '0', 'general', 'boolean', 10, 1);
        $this->ensureSetting($pdo, 'store_open', '1', 'general', 'boolean', 11, 1);
        $this->ensureSetting($pdo, 'shipping_label', 'Standard Shipping', 'shipping', 'text', 1, 1);
        $this->ensureSetting($pdo, 'shipping_cost', '49', 'shipping', 'text', 2, 1);
        $this->ensureSetting($pdo, 'free_shipping_min', '500', 'shipping', 'text', 3, 1);
        $this->ensureSetting($pdo, 'express_shipping_label', 'Express Shipping', 'shipping', 'text', 4, 1);
        $this->ensureSetting($pdo, 'express_shipping_cost', '99', 'shipping', 'text', 5, 1);

        $stmt = $pdo->query("SELECT * FROM sg_settings ORDER BY `group`, sort_order");
        $settings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $groups = [];
        foreach ($settings as $s) {
            if (str_starts_with($s['key'], 'hero_')) continue;
            $g = $s['group'];
            if (!isset($groups[$g])) $groups[$g] = [];
            $groups[$g][] = $s;
        }

        return $this->view('admin.settings.index', ['groups' => $groups]);
    }

    private function ensureFileSetting(\PDO $pdo, string $key, string $defaultValue): void
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_settings WHERE `key` = :k");
        $stmt->execute([':k' => $key]);
        if (!$stmt->fetchColumn()) {
            $stmt = $pdo->prepare("INSERT INTO sg_settings (`key`, `value`, `group`, `type`, `sort_order`, `is_public`) VALUES (:k, :v, 'general', 'text', 99, 1)");
            $stmt->execute([':k' => $key, ':v' => $defaultValue]);
        }
    }

    private function ensureSetting(\PDO $pdo, string $key, string $defaultValue, string $group, string $type, int $sortOrder, int $isPublic = 1, ?string $options = null): void
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_settings WHERE `key` = :k");
        $stmt->execute([':k' => $key]);
        if (!$stmt->fetchColumn()) {
            $stmt = $pdo->prepare("INSERT INTO sg_settings (`key`, `value`, `group`, `type`, `sort_order`, `is_public`, `options`) VALUES (:k, :v, :g, :t, :s, :p, :o)");
            $stmt->execute([':k' => $key, ':v' => $defaultValue, ':g' => $group, ':t' => $type, ':s' => $sortOrder, ':p' => $isPublic, ':o' => $options]);
        }
    }

    public function update(Request $request, Response $response): void
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $settingValues = $request->input('setting', []);

        foreach ($settingValues as $key => $value) {
            // Check for file upload
            $fileInputName = 'setting_file_' . $key;
            if ($request->hasFile($fileInputName)) {
                $file = $request->file($fileInputName);
                $filename = Image::upload($file, UPLOADS_DIR, $key);
                if ($filename) $value = $filename;
            }

            // Check for remove checkbox
            $removeInputName = 'remove_setting_' . $key;
            if ($request->input($removeInputName)) {
                $value = '';
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_settings WHERE `key` = :k");
            $stmt->execute([':k' => $key]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                $stmt = $pdo->prepare("UPDATE sg_settings SET value = :val WHERE `key` = :k");
                $stmt->execute([':val' => $value, ':k' => $key]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO sg_settings (`key`, `value`, `group`, `type`, `sort_order`, `is_public`) VALUES (:k, :val, 'general', 'image', 99, 1)");
                $stmt->execute([':k' => $key, ':val' => $value]);
            }
        }

        // Auto-clear cache so frontend updates immediately
        if ($request->input('clear_cache')) {
            $cacheService = new \App\Services\CacheService();
            $cacheService->clear();
            array_map('unlink', glob(VIEW_CACHE_DIR . DS . '*'));
        }

        $this->flash('success', 'Settings saved successfully &mdash; frontend is now updated.');
        $this->redirect(url('13091998/settings'));
    }
}
