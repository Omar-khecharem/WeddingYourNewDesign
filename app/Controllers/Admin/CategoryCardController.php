<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Security;
use App\Helpers\Image;

class CategoryCardController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Category Cards');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $cards = $pdo->query("SELECT cc.*, c.name as category_name, sc.name as subcategory_name FROM sg_category_cards cc LEFT JOIN sg_categories c ON c.id = cc.category_id LEFT JOIN sg_subcategories sc ON sc.id = cc.subcategory_id ORDER BY cc.section_key, cc.sort_order")->fetchAll(\PDO::FETCH_ASSOC);
        return $this->view('admin.category_cards.index', ['cards' => $cards]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->setMeta('Add Category Card');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $categories = $pdo->query("SELECT id, name FROM sg_categories WHERE status = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        $subcategories = $pdo->query("SELECT sc.id, sc.name, c.name as cat_name FROM sg_subcategories sc JOIN sg_categories c ON c.id = sc.category_id WHERE sc.status = 1 ORDER BY c.name, sc.name")->fetchAll(\PDO::FETCH_ASSOC);
        return $this->view('admin.category_cards.form', ['card' => null, 'categories' => $categories, 'subcategories' => $subcategories]);
    }

    public function store(Request $request, Response $response): void
    {
        $data = [
            'title' => Security::sanitize($request->input('title', '')),
            'subtitle' => Security::sanitize($request->input('subtitle', '')),
            'section_key' => $request->input('section_key', 'custom'),
            'category_id' => $request->input('category_id') ? (int)$request->input('category_id') : null,
            'subcategory_id' => $request->input('subcategory_id') ? (int)$request->input('subcategory_id') : null,
            'link' => $request->input('link', ''),
            'sort_order' => (int)$request->input('sort_order', 0),
            'is_active' => (int)(bool)$request->input('is_active', 1),
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Image::upload($file, UPLOADS_DIR . DS . 'category-cards', $data['title']);
            if ($filename) $data['image'] = $filename;
        }

        if (empty($data['image'])) {
            $this->flash('error', 'Image is required.');
            $this->redirectBack();
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO sg_category_cards (title, subtitle, section_key, category_id, subcategory_id, image, link, sort_order, is_active, created_at) VALUES (:title, :subtitle, :section_key, :category_id, :subcategory_id, :image, :link, :sort_order, :is_active, NOW())");
        $stmt->execute($data);

        $this->success('Category card created.', url('13091998/category-cards'));
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_category_cards WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $card = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$card) {
            $this->flash('error', 'Card not found.');
            $this->redirect(url('13091998/category-cards'));
        }

        $categories = $pdo->query("SELECT id, name FROM sg_categories WHERE status = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        $subcategories = $pdo->query("SELECT sc.id, sc.name, c.name as cat_name FROM sg_subcategories sc JOIN sg_categories c ON c.id = sc.category_id WHERE sc.status = 1 ORDER BY c.name, sc.name")->fetchAll(\PDO::FETCH_ASSOC);
        $this->setMeta('Edit: ' . $card['title']);
        return $this->view('admin.category_cards.form', ['card' => $card, 'categories' => $categories, 'subcategories' => $subcategories]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $data = [
            'title' => Security::sanitize($request->input('title', '')),
            'subtitle' => Security::sanitize($request->input('subtitle', '')),
            'section_key' => $request->input('section_key', 'custom'),
            'category_id' => $request->input('category_id') ? (int)$request->input('category_id') : null,
            'subcategory_id' => $request->input('subcategory_id') ? (int)$request->input('subcategory_id') : null,
            'link' => $request->input('link', ''),
            'sort_order' => (int)$request->input('sort_order', 0),
            'is_active' => (int)(bool)$request->input('is_active', 1),
            ':id' => $id,
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Image::upload($file, UPLOADS_DIR . DS . 'category-cards', $data['title']);
            if ($filename) {
                $pdo->prepare("UPDATE sg_category_cards SET image = :img WHERE id = :id")->execute([':img' => $filename, ':id' => $id]);
            }
        }

        $stmt = $pdo->prepare("UPDATE sg_category_cards SET title = :title, subtitle = :subtitle, section_key = :section_key, category_id = :category_id, subcategory_id = :subcategory_id, link = :link, sort_order = :sort_order, is_active = :is_active WHERE id = :id");
        $stmt->execute($data);

        $this->success('Category card updated.', url('13091998/category-cards'));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $pdo->prepare("DELETE FROM sg_category_cards WHERE id = :id")->execute([':id' => $id]);
        $this->success('Card deleted.', url('13091998/category-cards'));
    }
}
