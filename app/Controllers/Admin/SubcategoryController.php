<?php
namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Security;
use App\Helpers\Image;

class SubcategoryController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Subcategories');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $subcategories = $pdo->query("
            SELECT sc.*, c.name AS category_name
            FROM sg_subcategories sc
            JOIN sg_categories c ON c.id = sc.category_id
            ORDER BY c.sort_order, c.name, sc.sort_order, sc.name
        ")->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('admin.subcategories.index', ['subcategories' => $subcategories]);
    }

    public function create(Request $request, Response $response): string
    {
        $categoryId = (int)$request->param('category_id');
        $this->setMeta('Add Subcategory');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT id, name FROM sg_categories WHERE id = :id AND status = 1");
        $stmt->execute([':id' => $categoryId]);
        $category = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $this->view('admin.subcategories.form', ['subcategory' => null, 'category' => $category, 'categories' => $category ? [] : $pdo->query("SELECT id, name FROM sg_categories WHERE status = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    public function store(Request $request, Response $response): void
    {
        $data = [
            'category_id' => (int)$request->input('category_id'),
            'name' => Security::sanitize($request->input('name', '')),
            'slug' => slugify($request->input('name', '')),
            'description' => Security::sanitize($request->input('description', '')),
            'sort_order' => (int)$request->input('sort_order', 0),
            'status' => (int)$request->input('status', 1),
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO sg_subcategories (category_id, name, slug, description, sort_order, status, created_at) VALUES (:category_id, :name, :slug, :description, :sort_order, :status, NOW())");
        $stmt->execute($data);
        $id = $pdo->lastInsertId();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Image::upload($file, CATEGORY_IMAGES_DIR, 'subcat_' . $id . '_' . uniqid());
            if ($filename) {
                $pdo->prepare("UPDATE sg_subcategories SET image = :img WHERE id = :id")->execute([':img' => $filename, ':id' => $id]);
            }
        }
        $this->success('Subcategory created successfully.', url('13091998/categories'));
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT sc.*, c.name AS category_name FROM sg_subcategories sc JOIN sg_categories c ON c.id = sc.category_id WHERE sc.id = :id");
        $stmt->execute([':id' => $id]);
        $subcategory = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$subcategory) {
            $this->flash('error', 'Subcategory not found.');
            $this->redirect(url('13091998/categories'));
        }

        $categories = $pdo->query("SELECT id, name FROM sg_categories WHERE status = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        $this->setMeta('Edit: ' . $subcategory['name']);
        return $this->view('admin.subcategories.form', ['subcategory' => $subcategory, 'category' => null, 'categories' => $categories]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $data = [
            'category_id' => (int)$request->input('category_id'),
            'name' => Security::sanitize($request->input('name', '')),
            'description' => Security::sanitize($request->input('description', '')),
            'sort_order' => (int)$request->input('sort_order', 0),
            'status' => (int)$request->input('status', 1),
            ':id' => $id,
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("SELECT image FROM sg_subcategories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $oldImage = $stmt->fetchColumn();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Image::upload($file, CATEGORY_IMAGES_DIR, 'subcat_' . $id . '_' . uniqid());
            if ($filename) {
                if ($oldImage) {
                    $oldPath = UPLOADS_DIR . DS . str_replace('/', DS, $oldImage);
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $pdo->prepare("UPDATE sg_subcategories SET image = :img WHERE id = :id")->execute([':img' => $filename, ':id' => $id]);
            }
        } elseif ($request->input('remove_image') && $oldImage) {
            $oldPath = UPLOADS_DIR . DS . str_replace('/', DS, $oldImage);
            if (file_exists($oldPath)) @unlink($oldPath);
            $pdo->prepare("UPDATE sg_subcategories SET image = NULL WHERE id = :id")->execute([':id' => $id]);
        }
        $stmt = $pdo->prepare("UPDATE sg_subcategories SET category_id = :category_id, name = :name, description = :description, sort_order = :sort_order, status = :status WHERE id = :id");
        $stmt->execute($data);

        $this->success('Subcategory updated successfully.', url('13091998/categories'));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT image FROM sg_subcategories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $oldImage = $stmt->fetchColumn();
        if ($oldImage) {
            $oldPath = UPLOADS_DIR . DS . str_replace('/', DS, $oldImage);
            if (file_exists($oldPath)) @unlink($oldPath);
        }
        $pdo->prepare("UPDATE sg_products SET subcategory_id = NULL WHERE subcategory_id = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM sg_subcategories WHERE id = :id")->execute([':id' => $id]);
        $this->success('Subcategory deleted.', url('13091998/categories'));
    }
}
