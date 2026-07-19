<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Security;
use App\Helpers\Image;

class CategoryController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Categories');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $categories = $pdo->query("
            SELECT c.*, (SELECT COUNT(*) FROM sg_products WHERE category_id = c.id) as product_count
            FROM sg_categories c ORDER BY c.sort_order, c.name
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $subcategories = $pdo->query("
            SELECT sc.*, c.name AS category_name
            FROM sg_subcategories sc
            JOIN sg_categories c ON c.id = sc.category_id
            ORDER BY c.sort_order, c.name, sc.sort_order, sc.name
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $subsByCat = [];
        foreach ($subcategories as $s) {
            $subsByCat[$s['category_id']][] = $s;
        }

        return $this->view('admin.categories.index', ['categories' => $categories, 'subsByCat' => $subsByCat]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->setMeta('Add Category');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $parents = $pdo->query("SELECT id, name FROM sg_categories WHERE parent_id IS NULL AND status = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        return $this->view('admin.categories.form', ['category' => null, 'parents' => $parents]);
    }

    public function store(Request $request, Response $response): void
    {
        $data = [
            'parent_id' => $request->input('parent_id') ? (int)$request->input('parent_id') : null,
            'name' => Security::sanitize($request->input('name', '')),
            'slug' => slugify($request->input('name', '')),
            'description' => Security::sanitize($request->input('description', '')),
            'sort_order' => (int)$request->input('sort_order', 0),
            'featured' => (int)(bool)$request->input('featured'),
            'status' => (int)$request->input('status', 1),
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO sg_categories (parent_id, name, slug, description, sort_order, featured, status, created_at) VALUES (:parent_id, :name, :slug, :description, :sort_order, :featured, :status, NOW())");
        $stmt->execute($data);
        $id = $pdo->lastInsertId();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Image::upload($file, CATEGORY_IMAGES_DIR, 'cat_' . $id . '_' . uniqid());
            if ($filename) {
                $pdo->prepare("UPDATE sg_categories SET image = :img WHERE id = :id")->execute([':img' => $filename, ':id' => $id]);
            }
        }
        $this->success('Category created successfully.', url('13091998/categories'));
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $category = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$category) {
            $this->flash('error', 'Category not found.');
            $this->redirect(url('13091998/categories'));
        }

        $parents = $pdo->query("SELECT id, name FROM sg_categories WHERE id != {$id} AND parent_id IS NULL AND status = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        $this->setMeta('Edit: ' . $category['name']);
        return $this->view('admin.categories.form', ['category' => $category, 'parents' => $parents]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $data = [
            'parent_id' => $request->input('parent_id') ? (int)$request->input('parent_id') : null,
            'name' => Security::sanitize($request->input('name', '')),
            'description' => Security::sanitize($request->input('description', '')),
            'sort_order' => (int)$request->input('sort_order', 0),
            'featured' => (int)(bool)$request->input('featured'),
            'status' => (int)$request->input('status', 1),
            ':id' => $id,
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("SELECT image FROM sg_categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $oldImage = $stmt->fetchColumn();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Image::upload($file, CATEGORY_IMAGES_DIR, 'cat_' . $id . '_' . uniqid());
            if ($filename) {
                if ($oldImage) {
                    $oldPath = UPLOADS_DIR . DS . str_replace('/', DS, $oldImage);
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $pdo->prepare("UPDATE sg_categories SET image = :img WHERE id = :id")->execute([':img' => $filename, ':id' => $id]);
            }
        } elseif ($request->input('remove_image') && $oldImage) {
            $oldPath = UPLOADS_DIR . DS . str_replace('/', DS, $oldImage);
            if (file_exists($oldPath)) @unlink($oldPath);
            $pdo->prepare("UPDATE sg_categories SET image = NULL WHERE id = :id")->execute([':id' => $id]);
        }
        $stmt = $pdo->prepare("UPDATE sg_categories SET parent_id = :parent_id, name = :name, description = :description, sort_order = :sort_order, featured = :featured, status = :status WHERE id = :id");
        $stmt->execute($data);

        $this->success('Category updated successfully.', url('13091998/categories'));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT image FROM sg_categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $oldImage = $stmt->fetchColumn();
        if ($oldImage) {
            $oldPath = UPLOADS_DIR . DS . str_replace('/', DS, $oldImage);
            if (file_exists($oldPath)) @unlink($oldPath);
        }
        $pdo->prepare("DELETE FROM sg_categories WHERE id = :id")->execute([':id' => $id]);
        $this->success('Category deleted.', url('13091998/categories'));
    }
}
