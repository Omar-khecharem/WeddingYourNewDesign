<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Category;
use App\Helpers\Security;
use App\Helpers\Image;

class ProductController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Manage Products');
        $page = max(1, (int)$request->query('page', 1));
        $search = $request->query('search', '');
        $categoryId = (int)$request->query('category', 0);
        $statusFilter = $request->query('status', '');

        $pdo = \App\Core\Database::getInstance()->getConnection();
        $where = ' WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $like = '%' . $search . '%';
            $where .= ' AND (p.name LIKE :search OR p.sku LIKE :sku)';
            $params[':search'] = $like;
            $params[':sku'] = $like;
        }
        if ($categoryId > 0) {
            $where .= ' AND p.category_id = :cat';
            $params[':cat'] = $categoryId;
        }
        if ($statusFilter !== '') {
            $where .= ' AND p.status = :st';
            $params[':st'] = (int)$statusFilter;
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM sg_products p{$where}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $totalPages = max(1, ceil($total / PAGINATION_ADMIN_PER_PAGE));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * PAGINATION_ADMIN_PER_PAGE;

        $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM sg_products p LEFT JOIN sg_categories c ON c.id = p.category_id{$where} ORDER BY p.created_at DESC LIMIT :lim OFFSET :off");
        $stmt->bindValue(':lim', PAGINATION_ADMIN_PER_PAGE, \PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, \PDO::PARAM_INT);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $items = [];
        foreach ($rows as $r) {
            $p = new \App\Models\Product($r);
            $p->category_name = $r['category_name'] ?? '-';
            $items[] = $p;
        }

        $products = [
            'items' => $items,
            'total' => $total,
            'perPage' => PAGINATION_ADMIN_PER_PAGE,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'hasPrev' => $page > 1,
            'hasNext' => $page < $totalPages,
            'prevPage' => $page - 1,
            'nextPage' => $page + 1,
        ];

        $categories = Category::getActive();

        return $this->view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->setMeta('Add Product');
        $categories = Category::getActive();

        return $this->view('admin.products.form', [
            'product' => null,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        $data = [
            'category_id' => (int)$request->input('category_id'),
            'subcategory_id' => $request->input('subcategory_id') ? (int)$request->input('subcategory_id') : null,
            'name' => Security::sanitize($request->input('name', '')),
            'slug' => slugify($request->input('name', '')),
            'short_description' => Security::sanitize($request->input('short_description', '')),
            'description' => $request->input('description', ''),
            'sku' => strtoupper($request->input('sku', '')),
            'regular_price' => (float)$request->input('regular_price', 0),
            'sale_price' => $request->input('sale_price') ? (float)$request->input('sale_price') : null,
            'stock_quantity' => (int)$request->input('stock_quantity', 0),
            'stock_status' => $request->input('stock_status', 'in_stock'),
            'is_featured' => (int)(bool)$request->input('is_featured'),
            'is_new' => (int)(bool)$request->input('is_new'),
            'is_trending' => (int)(bool)$request->input('is_trending'),
            'status' => (int)$request->input('status', 1),
            'meta_title' => Security::sanitize($request->input('meta_title', '')),
            'meta_description' => Security::sanitize($request->input('meta_description', '')),
        ];

        if ($data['sale_price'] && $data['regular_price'] > 0) {
            $data['discount_percent'] = round((($data['regular_price'] - $data['sale_price']) / $data['regular_price']) * 100);
        }

        $product = Product::create($data);

        $this->handleImages($product->id, $request, true);

        $pincodes = $request->input('pincodes', []);
        if (is_array($pincodes) && !empty(array_filter($pincodes))) {
            Product::savePincodes($product->id, $pincodes);
        }

        $this->success('Product created successfully.', url('13091998/products'));
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $product = Product::find($id);

        if (!$product) {
            $this->notFound('Product not found.');
        }

        $this->setMeta('Edit Product: ' . $product->name);
        $categories = Category::getActive();

        return $this->view('admin.products.form', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $product = Product::find($id);

        if (!$product) {
            $this->notFound('Product not found.');
        }

        $data = [
            'category_id' => (int)$request->input('category_id'),
            'subcategory_id' => $request->input('subcategory_id') ? (int)$request->input('subcategory_id') : null,
            'name' => Security::sanitize($request->input('name', '')),
            'slug' => slugify($request->input('slug', $request->input('name', ''))),
            'short_description' => Security::sanitize($request->input('short_description', '')),
            'description' => $request->input('description', ''),
            'sku' => strtoupper($request->input('sku', '')),
            'regular_price' => (float)$request->input('regular_price', 0),
            'sale_price' => $request->input('sale_price') ? (float)$request->input('sale_price') : null,
            'stock_quantity' => (int)$request->input('stock_quantity', 0),
            'stock_status' => $request->input('stock_status', 'in_stock'),
            'is_featured' => (int)(bool)$request->input('is_featured'),
            'is_new' => (int)(bool)$request->input('is_new'),
            'is_trending' => (int)(bool)$request->input('is_trending'),
            'status' => (int)$request->input('status', 1),
            'meta_title' => Security::sanitize($request->input('meta_title', '')),
            'meta_description' => Security::sanitize($request->input('meta_description', '')),
        ];

        if ($data['sale_price'] && $data['regular_price'] > 0) {
            $data['discount_percent'] = round((($data['regular_price'] - $data['sale_price']) / $data['regular_price']) * 100);
        }

        $product->fill($data);
        $product->save();

        $this->handleImages($id, $request);

        $pincodes = $request->input('pincodes', []);
        if (is_array($pincodes)) {
            Product::savePincodes($id, $pincodes);
        }

        $this->success('Product updated successfully.', url('13091998/products'));
    }

    private function handleImages(int $productId, Request $request, bool $isNew = false): void
    {
        if (empty($_FILES['images']) || !is_array($_FILES['images']['name'])) return;

        $pdo = \App\Core\Database::getInstance()->getConnection();

        // If no existing primary image, the first uploaded image becomes primary
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_product_images WHERE product_id = :pid AND is_primary = 1");
        $stmt->execute([':pid' => $productId]);
        $hasPrimary = (int)$stmt->fetchColumn() > 0;

        $nameCount = count($_FILES['images']['name']);
        for ($i = 0; $i < $nameCount; $i++) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;

            $file = [
                'name' => $_FILES['images']['name'][$i],
                'type' => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i],
            ];

            $errors = Image::validate($file);
            if (!empty($errors)) continue;

            $filename = Image::upload($file, PRODUCT_IMAGES_DIR, 'product_' . $productId . '_' . uniqid());
            if (!$filename) continue;

            $isPrimary = ($isNew || !$hasPrimary) && $i === 0 ? 1 : 0;
            $pdo->prepare("INSERT INTO sg_product_images (product_id, image, is_primary) VALUES (:pid, :img, :prim)")
                ->execute([':pid' => $productId, ':img' => $filename, ':prim' => $isPrimary]);

            if (($isNew || !$hasPrimary) && $i === 0) {
                $pdo->prepare("UPDATE sg_product_images SET is_primary = 0 WHERE product_id = :pid AND id != LAST_INSERT_ID()")
                    ->execute([':pid' => $productId]);
                $hasPrimary = true;
            }
        }
    }

    public function setPrimaryImage(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("SELECT product_id FROM sg_product_images WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $productId = $stmt->fetchColumn();

        if (!$productId) {
            $this->flash('error', 'Image not found.');
            $this->redirectBack();
            return;
        }

        $pdo->prepare("UPDATE sg_product_images SET is_primary = 0 WHERE product_id = :pid")
            ->execute([':pid' => $productId]);
        $pdo->prepare("UPDATE sg_product_images SET is_primary = 1 WHERE id = :id")
            ->execute([':id' => $id]);

        clearSiteCache();
        $this->flash('success', 'Primary image updated.');
        $this->redirect(url('13091998/products/edit/' . $productId));
    }

    public function deleteImage(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT image FROM sg_product_images WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $img = $stmt->fetchColumn();
        if ($img) {
            $path = UPLOADS_DIR . DS . $img;
            if (file_exists($path)) @unlink($path);
        }
        $pdo->prepare("DELETE FROM sg_product_images WHERE id = :id")->execute([':id' => $id]);
        $this->success('Image deleted.', url('13091998/products/edit/' . $request->input('product_id')));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $product = Product::find($id);

        if ($product) {
            $product->delete();
            clearSiteCache();
            $this->flash('success', 'Product deleted successfully.');
        } else {
            $this->flash('error', 'Product not found.');
        }

        $this->redirect(url('13091998/products'));
    }
}
