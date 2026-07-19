<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Image;

class DealController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Best Deals');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $deals = $pdo->query("SELECT * FROM sg_deals ORDER BY sort_order")->fetchAll(\PDO::FETCH_ASSOC);
        return $this->view('admin.deals.index', ['deals' => $deals]);
    }

    public function store(Request $request, Response $response): void
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Image::upload($file, UPLOADS_DIR . DS . 'deals', 'deal-' . uniqid());
            if ($filename) {
                $stmt = $pdo->prepare("INSERT INTO sg_deals (image, sort_order, is_active, created_at) VALUES (:image, 0, 1, NOW())");
                $stmt->execute([':image' => $filename]);
                $this->success('Deal added.', url('13091998/deals'));
                return;
            }
        }

        $this->flash('error', 'Image is required.');
        $this->redirectBack();
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $pdo->prepare("DELETE FROM sg_deals WHERE id = :id")->execute([':id' => $id]);
        $this->success('Deal deleted.', url('13091998/deals'));
    }
}
