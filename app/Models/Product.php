<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Product extends Model
{
    protected static string $table = 'sg_products';
    protected static array $fillable = [
        'category_id', 'subcategory_id', 'brand_id', 'name', 'slug',
        'short_description', 'description', 'sku', 'barcode',
        'regular_price', 'sale_price', 'discount_percent', 'cost_price',
        'stock_quantity', 'low_stock_threshold', 'stock_status',
        'weight', 'length', 'width', 'height',
        'is_featured', 'is_new', 'is_trending', 'is_bestseller',
        'taxable', 'tax_class', 'status',
        'meta_title', 'meta_description', 'meta_keywords'
    ];

    public static function findBySlug(string $slug): ?array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                   sc.name AS subcategory_name, sc.slug AS subcategory_slug, b.name AS brand_name, b.slug AS brand_slug
            FROM sg_products p
            LEFT JOIN sg_categories c ON c.id = p.category_id
            LEFT JOIN sg_subcategories sc ON sc.id = p.subcategory_id
            LEFT JOIN sg_brands b ON b.id = p.brand_id
            WHERE p.slug = :slug AND p.status = 1 LIMIT 1
        ");
        $stmt->execute([':slug' => $slug]);
        $product = $stmt->fetch();
        if ($product) {
            $imgStmt = $pdo->prepare("SELECT image FROM sg_product_images WHERE product_id = :pid ORDER BY is_primary DESC, sort_order ASC LIMIT 1");
            $imgStmt->execute([':pid' => $product['id']]);
            $img = $imgStmt->fetch();
            $product['image'] = $img ? self::resolveImageUrl($img['image'], 'products') : asset('images/placeholder.png');
            $product['images'] = self::getImages($product['id']);
            $product['variants'] = self::getVariants($product['id']);
            $product['tags'] = self::getTags($product['id']);
        }
        return $product ?: null;
    }

    public static function getImages(int $productId): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_product_images WHERE product_id = :pid ORDER BY sort_order ASC, is_primary DESC");
        $stmt->execute([':pid' => $productId]);
        $images = $stmt->fetchAll();
        foreach ($images as &$img) {
            $img['url'] = self::resolveImageUrl($img['image'], 'products');
        }
        return $images;
    }

    public static function getVariants(int $productId): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_product_variants WHERE product_id = :pid ORDER BY is_default DESC, price_adjustment ASC");
        $stmt->execute([':pid' => $productId]);
        return $stmt->fetchAll();
    }

    public static function getTags(int $productId): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT t.* FROM sg_tags t JOIN sg_product_tag pt ON t.id = pt.tag_id WHERE pt.product_id = :pid");
        $stmt->execute([':pid' => $productId]);
        return $stmt->fetchAll();
    }

    public static function getRecent(int $limit = 8): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name, sc.name AS subcategory_name
            FROM sg_products p
            LEFT JOIN sg_categories c ON c.id = p.category_id
            LEFT JOIN sg_subcategories sc ON sc.id = p.subcategory_id
            WHERE p.status = 1
            ORDER BY p.created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return self::attachImages($stmt->fetchAll());
    }

    public static function getFeatured(int $limit = 4): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name, sc.name AS subcategory_name
            FROM sg_products p
            LEFT JOIN sg_categories c ON c.id = p.category_id
            LEFT JOIN sg_subcategories sc ON sc.id = p.subcategory_id
            WHERE p.is_featured = 1 AND p.status = 1
            ORDER BY p.created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return self::attachImages($stmt->fetchAll());
    }

    public static function getTrending(int $limit = 4): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name, sc.name AS subcategory_name
            FROM sg_products p
            LEFT JOIN sg_categories c ON c.id = p.category_id
            LEFT JOIN sg_subcategories sc ON sc.id = p.subcategory_id
            WHERE p.is_trending = 1 AND p.status = 1
            ORDER BY p.created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return self::attachImages($stmt->fetchAll());
    }

    public static function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name, sc.name AS subcategory_name
            FROM sg_products p
            LEFT JOIN sg_categories c ON c.id = p.category_id
            LEFT JOIN sg_subcategories sc ON sc.id = p.subcategory_id
            WHERE p.category_id = :cat_id AND p.id != :prod_id AND p.status = 1
            ORDER BY RAND() LIMIT :limit
        ");
        $stmt->bindValue(':cat_id', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(':prod_id', $productId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return self::attachImages($stmt->fetchAll());
    }

    public static function search(string $query, int $limit = 10): array
    {
        $pdo = Database::getInstance()->getConnection();
        $searchTerm = '%' . $query . '%';
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name,
                   MATCH(p.name, p.short_description, p.description) AGAINST(:q IN BOOLEAN MODE) AS relevance
            FROM sg_products p
            LEFT JOIN sg_categories c ON c.id = p.category_id
            WHERE p.status = 1
            AND (p.name LIKE :s OR p.short_description LIKE :s2 OR p.sku LIKE :s3)
            ORDER BY relevance DESC, p.created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':q', $query . '*');
        $stmt->bindValue(':s', $searchTerm);
        $stmt->bindValue(':s2', $searchTerm);
        $stmt->bindValue(':s3', $searchTerm);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return self::attachImages($stmt->fetchAll());
    }

    public static function searchAjax(string $query, int $limit = 5): array
    {
        $pdo = Database::getInstance()->getConnection();
        $searchTerm = '%' . $query . '%';
        $stmt = $pdo->prepare("
            SELECT p.id, p.name, p.slug, p.regular_price, p.sale_price, p.discount_percent,
                   pi.image AS primary_image
            FROM sg_products p
            LEFT JOIN sg_product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE p.status = 1 AND p.name LIKE :s
            ORDER BY p.created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':s', $searchTerm);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as &$r) {
            $r['image_url'] = $r['primary_image'] ? self::resolveImageUrl($r['primary_image'], 'products') : asset('images/placeholder.png');
            $r['price'] = $r['sale_price'] ?: $r['regular_price'];
            $r['url'] = url('product/' . $r['slug']);
        }
        return $results;
    }

    public static function getFiltered(array $filters, int $page = 1, int $perPage = 12): array
    {
        $pdo = Database::getInstance()->getConnection();
        $where = ['p.status = 1'];
        $params = [];

        if (!empty($filters['category'])) {
            $where[] = '(c.slug = :cat OR c.id = :cat2)';
            $params[':cat'] = $filters['category'];
            $params[':cat2'] = is_numeric($filters['category']) ? (int)$filters['category'] : 0;
        }
        if (!empty($filters['subcategory'])) {
            $where[] = 'sc.slug = :subcat';
            $params[':subcat'] = $filters['subcategory'];
        }
        if (!empty($filters['brand'])) {
            $where[] = '(b.slug = :brand OR b.id = :brand2)';
            $params[':brand'] = $filters['brand'];
            $params[':brand2'] = is_numeric($filters['brand']) ? (int)$filters['brand'] : 0;
        }
        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE :search OR p.short_description LIKE :search2)';
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['min_price'])) {
            $where[] = 'COALESCE(p.sale_price, p.regular_price) >= :min_price';
            $params[':min_price'] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = 'COALESCE(p.sale_price, p.regular_price) <= :max_price';
            $params[':max_price'] = (float)$filters['max_price'];
        }
        if (!empty($filters['rating'])) {
            $where[] = 'p.rating_avg >= :rating';
            $params[':rating'] = (float)$filters['rating'];
        }
        if (!empty($filters['in_stock'])) {
            $where[] = "p.stock_status = 'in_stock'";
        }
        if (!empty($filters['featured'])) {
            $where[] = 'p.is_featured = 1';
        }
        if (!empty($filters['on_sale'])) {
            $where[] = 'p.sale_price IS NOT NULL AND p.sale_price < p.regular_price';
        }

        $whereClause = implode(' AND ', $where);

        $orderMap = [
            'newest' => 'p.created_at DESC',
            'oldest' => 'p.created_at ASC',
            'price_asc' => 'COALESCE(p.sale_price, p.regular_price) ASC',
            'price_desc' => 'COALESCE(p.sale_price, p.regular_price) DESC',
            'name_asc' => 'p.name ASC',
            'name_desc' => 'p.name DESC',
            'rating' => 'p.rating_avg DESC',
            'popular' => 'p.sales_count DESC',
            'discount' => 'p.discount_percent DESC',
        ];
        $order = $orderMap[$filters['sort'] ?? ''] ?? 'p.created_at DESC';

        $countSql = "SELECT COUNT(*) FROM sg_products p
                     LEFT JOIN sg_categories c ON p.category_id = c.id
                     LEFT JOIN sg_subcategories sc ON p.subcategory_id = sc.id
                     LEFT JOIN sg_brands b ON p.brand_id = b.id
                     WHERE $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                       sc.name AS subcategory_name, sc.slug AS subcategory_slug,
                       b.name AS brand_name, b.slug AS brand_slug
                FROM sg_products p
                LEFT JOIN sg_categories c ON p.category_id = c.id
                LEFT JOIN sg_subcategories sc ON p.subcategory_id = sc.id
                LEFT JOIN sg_brands b ON p.brand_id = b.id
                WHERE $whereClause
                ORDER BY $order LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $products = self::attachImages($stmt->fetchAll());

        $totalPages = (int)ceil($total / $perPage);
        return compact('products', 'total', 'page', 'perPage', 'totalPages');
    }

    public static function getFilterOptions(): array
    {
        $pdo = Database::getInstance()->getConnection();

        $stmt = $pdo->query("SELECT id, name, slug, image FROM sg_categories WHERE status = 1 ORDER BY sort_order ASC");
        $categories = $stmt->fetchAll();

        $stmt = $pdo->query("SELECT id, name, slug FROM sg_brands WHERE status = 1 ORDER BY name ASC");
        $brands = $stmt->fetchAll();

        $stmt = $pdo->query("SELECT MIN(COALESCE(sale_price, regular_price)) AS min_price, MAX(COALESCE(sale_price, regular_price)) AS max_price FROM sg_products WHERE status = 1");
        $priceRange = $stmt->fetch();

        return compact('categories', 'brands', 'priceRange');
    }

    public static function getByCategoryId(int $categoryId, int $limit = 4): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name, sc.name AS subcategory_name
            FROM sg_products p
            LEFT JOIN sg_categories c ON c.id = p.category_id
            LEFT JOIN sg_subcategories sc ON sc.id = p.subcategory_id
            WHERE p.category_id = :cat_id AND p.status = 1
            ORDER BY p.is_featured DESC, p.created_at DESC LIMIT :limit
        ");
        $stmt->bindValue(':cat_id', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return self::attachImages($stmt->fetchAll());
    }

    public static function getByCategory(int $categoryId, int $page = 1, int $perPage = 12): array
    {
        return self::paginate($page, $perPage, ['category_id' => $categoryId, 'status' => STATUS_ACTIVE]);
    }

    public static function updateStock(int $productId, int $quantity): bool
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE sg_products SET stock_quantity = :qty, stock_status = CASE WHEN :qty <= 0 THEN 'out_of_stock' WHEN :qty <= low_stock_threshold THEN 'on_backorder' ELSE 'in_stock' END WHERE id = :id");
        return $stmt->execute([':qty' => $quantity, ':id' => $productId]);
    }

    public static function decrementStock(int $productId, int $quantity): bool
    {
        $pdo = Database::getInstance()->getConnection();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT stock_quantity FROM sg_products WHERE id = :id FOR UPDATE");
        $stmt->execute([':id' => $productId]);
        $current = (int)$stmt->fetchColumn();
        if ($current < $quantity) {
            $pdo->rollBack();
            return false;
        }
        $newQty = $current - $quantity;
        $status = $newQty <= 0 ? 'out_of_stock' : ($newQty <= 5 ? 'on_backorder' : 'in_stock');
        $pdo->prepare("UPDATE sg_products SET stock_quantity = :qty, stock_status = :status WHERE id = :id")
            ->execute([':qty' => $newQty, ':status' => $status, ':id' => $productId]);
        $pdo->commit();
        return true;
    }

    public static function getOutOfStock(): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM sg_products WHERE stock_status = 'out_of_stock' OR stock_quantity <= 0 ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    private static function resolveImageUrl(string $path, string $subdir = 'products'): string
    {
        if (empty($path)) return asset('images/placeholder.png');
        $path = str_replace('\\', '/', $path);
        $checkPath = UPLOADS_DIR . DS . str_replace('/', DS, $path);
        if (!file_exists($checkPath)) {
            $altPath = ROOT_DIR . DS . 'uploads' . DS . str_replace('/', DS, $path);
            if (!file_exists($altPath)) return uploadUrl($path, $subdir);
        }
        return uploadUrl($path, $subdir);
    }

    private static function attachImages(array $products): array
    {
        if (empty($products)) return [];
        $ids = array_column($products, 'id');
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT id, product_id, image FROM sg_product_images WHERE product_id IN ($ph) ORDER BY is_primary DESC, sort_order ASC");
        $stmt->execute($ids);
        $allRows = $stmt->fetchAll();
        $images = [];
        foreach ($allRows as $row) {
            if (!isset($images[$row['product_id']])) {
                $images[$row['product_id']] = $row['image'];
            }
        }
        foreach ($products as &$product) {
            $img = $images[$product['id']] ?? '';
            $product['image'] = self::resolveImageUrl($img, 'products');
        }
        return $products;
    }

    public static function getPincodes(int $productId): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT pincode FROM sg_product_pincodes WHERE product_id = :pid ORDER BY pincode ASC");
        $stmt->execute([':pid' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function savePincodes(int $productId, array $pincodes): void
    {
        $pdo = Database::getInstance()->getConnection();
        $pdo->prepare("DELETE FROM sg_product_pincodes WHERE product_id = :pid")->execute([':pid' => $productId]);
        $stmt = $pdo->prepare("INSERT INTO sg_product_pincodes (product_id, pincode) VALUES (:pid, :pc)");
        foreach ($pincodes as $pc) {
            $pc = trim($pc);
            if ($pc !== '') {
                $stmt->execute([':pid' => $productId, ':pc' => $pc]);
            }
        }
    }

    public static function checkPincode(int $productId, string $pincode): bool
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sg_product_pincodes WHERE product_id = :pid AND pincode = :pc");
        $stmt->execute([':pid' => $productId, ':pc' => trim($pincode)]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
