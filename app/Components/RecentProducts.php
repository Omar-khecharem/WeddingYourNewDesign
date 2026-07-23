<?php
/**
 * RecentProducts Component
 * 
 * Recent products grid with discount badges and pricing.
 * ALL data (products, prices, images, discounts) comes from MySQL.
 *
 * @package App\Components
 */

namespace App\Components;

class RecentProducts extends Component
{
    protected function fetchData(): array
    {
        $stmt = $this->db()->query("
            SELECT p.*, c.name as category_name,
                   (SELECT image FROM sg_product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
            FROM sg_products p
            LEFT JOIN sg_categories c ON c.id = p.category_id
            WHERE p.status = 1
            ORDER BY p.created_at DESC LIMIT 8
        ");
        return ['products' => $stmt->fetchAll()];
    }

    public static function css(): string
    {
        return '.product-card{background:white;border:1px solid #e6e6e6;border-radius:12px;padding:15px;text-align:center;transition:all .3s;position:relative}.product-card:hover{transform:translateY(-5px);border-color:#b30d1b;box-shadow:0 8px 25px rgba(0,0,0,.08)}.product-card .discount-badge{position:absolute;top:12px;left:12px;background:#b30d1b;color:white;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;z-index:2}.product-card .prod-img{height:160px;border-radius:8px;overflow:hidden;margin-bottom:12px;background:#fafafa}.product-card .prod-img img{width:100%;height:100%;object-fit:contain;transition:transform .3s}.product-card:hover .prod-img img{transform:scale(1.06)}.product-card .prod-name{font-size:13.5px;font-weight:700;color:#222;margin-bottom:4px;text-transform:capitalize}.product-card .prod-category{font-size:10px;font-weight:600;color:#666;text-transform:uppercase;letter-spacing:.3px;margin-bottom:8px}.product-card .stock-status{color:#2e7d32;font-size:11px;font-weight:700;margin-bottom:6px}.product-card .stars{color:#ffd54f;font-size:12px;margin-bottom:10px}.product-card .old-price{text-decoration:line-through;color:#999;font-size:12px;margin-right:6px}.product-card .new-price{color:#b30d1b;font-size:14px;font-weight:700}';
    }

    public function render(): string
    {
        return $this->renderWithTitle('Checkout Our Recent Products', $this->getData('products', []));
    }

    protected function renderWithTitle(string $title, array $products): string
    {
        $items = '';
        foreach ($products as $p) {
            $img = $p['image'] ? $this->imageUrl($p['image'], 'products') : asset('images/placeholder.png');
            $regular = (float)($p['regular_price'] ?? 0);
            $sale = (float)($p['sale_price'] ?? 0);
            $discount = (int)($p['discount_percent'] ?? 0);
            $price = $sale ?: $regular;
            $category = $this->e($p['category_name'] ?? '');
            $name = $this->e($p['name'] ?? '');

            $items .= '<a href="' . url('product/' . $p['slug']) . '" class="product-card">';
            if ($discount > 0) $items .= '<span class="discount-badge">-' . $discount . '%</span>';
            $items .= '<div class="prod-img"><img src="' . $this->e($img) . '" alt="' . $name . '" loading="lazy"></div>';
            $items .= '<h4 class="prod-name">' . $name . '</h4>';
            if ($category) $items .= '<p class="prod-category">' . $category . '</p>';
            $items .= '<div class="stock-status"><i class="fa-solid fa-circle-check"></i> In stock</div>';
            $items .= '<div class="stars"><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i></div>';
            $items .= '<div>';
            if ($sale && $sale < $regular) $items .= '<span class="old-price">' . $this->price($regular) . '</span>';
            $items .= '<span class="new-price">' . $this->price($price) . '</span>';
            $items .= '</div></a>';
        }

        if (empty($products)) {
            $items = '<div class="col-span-full text-center py-8 text-gray-400">No products yet.</div>';
        }

        return '
<section class="max-w-[1200px] mx-auto my-12 p-6 border-2 border-primary-red rounded-xl bg-white">
    <h2 class="text-center font-playfair text-2xl md:text-3xl text-primary-red font-bold mb-6">' . $this->e($title) . '</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">' . $items . '</div>
    <div class="text-center mt-8"><a href="' . url('products') . '" class="maroon-btn">View All Products <i class="fa-solid fa-angles-right ml-1.5"></i></a></div>
</section>';
    }
}
