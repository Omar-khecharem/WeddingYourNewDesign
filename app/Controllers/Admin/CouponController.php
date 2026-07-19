<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Services\CouponService;

class CouponController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Coupons');
        $couponService = new CouponService();
        $coupons = $couponService->getAllCoupons();

        $editCoupon = null;
        if ($request->query('action') === 'edit' && $request->query('id')) {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM sg_coupons WHERE id = :id");
            $stmt->execute([':id' => (int)$request->query('id')]);
            $editCoupon = $stmt->fetch();
        }

        return $this->view('admin.coupons.index', [
            'coupons' => $coupons,
            'editCoupon' => $editCoupon,
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        try {
            $couponService = new CouponService();
            $result = $couponService->create([
                'code' => $request->input('code'),
                'type' => $request->input('type'),
                'value' => (float)$request->input('value'),
                'min_order_amount' => $request->input('min_amount') ? (float)$request->input('min_amount') : null,
                'max_discount' => null,
                'usage_limit' => $request->input('usage_limit') ? (int)$request->input('usage_limit') : null,
                'usage_per_user' => 1,
                'is_active' => $request->input('status') === 'active' ? 1 : 0,
                'starts_at' => $request->input('start_date') ?: null,
                'expires_at' => $request->input('expiry_date') ?: null,
                'description' => $request->input('description') ?: null,
            ]);

            if ($result['success']) {
                $this->flash('success', 'Coupon created successfully.');
                logActivity('coupon_created', "Coupon created: {$result['code']}");
            } else {
                $this->flash('error', $result['message'] ?? 'Failed to create coupon.');
            }
            $this->redirect(url('13091998/coupons'));
        } catch (\Exception $e) {
            error_log('Coupon creation failed: ' . $e->getMessage());
            $this->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
            $this->redirect(url('13091998/coupons'));
        }
    }

    public function update(Request $request, Response $response, array $params = []): void
    {
        try {
            $id = (int)($params['id'] ?? $request->input('id'));
            if (!$id) {
                $this->flash('error', 'Invalid coupon ID.');
                $this->redirect(url('13091998/coupons'));
                return;
            }

            $couponService = new CouponService();
            $result = $couponService->update($id, [
                'code' => $request->input('code'),
                'type' => $request->input('type'),
                'value' => (float)$request->input('value'),
                'min_order_amount' => $request->input('min_amount') ? (float)$request->input('min_amount') : null,
                'max_discount' => null,
                'usage_limit' => $request->input('usage_limit') ? (int)$request->input('usage_limit') : null,
                'is_active' => $request->input('status') === 'active' ? 1 : 0,
                'starts_at' => $request->input('start_date') ?: null,
                'expires_at' => $request->input('expiry_date') ?: null,
                'description' => $request->input('description') ?: null,
            ]);

            if ($result) {
                $this->flash('success', 'Coupon updated successfully.');
                logActivity('coupon_updated', "Coupon #{$id} updated");
            } else {
                $this->flash('error', 'Failed to update coupon.');
            }
            $this->redirect(url('13091998/coupons'));
        } catch (\Exception $e) {
            error_log('Coupon update failed: ' . $e->getMessage());
            $this->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
            $this->redirect(url('13091998/coupons'));
        }
    }

    public function destroy(Request $request, Response $response): void
    {
        try {
            $id = (int)$request->input('id');
            if (!$id) {
                $this->flash('error', 'Invalid coupon ID.');
                $this->redirect(url('13091998/coupons'));
                return;
            }

            $couponService = new CouponService();
            $success = $couponService->delete($id);

            if ($success) {
                $this->flash('success', 'Coupon deleted successfully.');
                logActivity('coupon_deleted', "Coupon #{$id} deleted");
            } else {
                $this->flash('error', 'Failed to delete coupon.');
            }
            $this->redirect(url('13091998/coupons'));
        } catch (\Exception $e) {
            error_log('Coupon deletion failed: ' . $e->getMessage());
            $this->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
            $this->redirect(url('13091998/coupons'));
        }
    }
}
