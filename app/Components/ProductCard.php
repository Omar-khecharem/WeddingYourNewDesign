<?php
/**
 * Product Card Component
 * 
 * Renders a product card matching the site's design system with
 * discount badge, image, name, price, rating, stock status,
 * and quick action buttons for wishlist, compare, and cart.
 *
 * @package App\Components
 */

namespace App\Components;

class ProductCard extends Component
{
    public function render(): string
    {
        $product = $this->prop('product', []);
        if (empty($product)) return '';

        $name = $product['name'] ?? '';
        $slug = $product['slug'] ?? '';
        $image = $product['image'] ?? $this->getProductImage($product['id'] ?? 0);
        $regularPrice = (float)($product['regular_price'] ?? 0);
        $salePrice = (float)($product['sale_price'] ?? 0);
        $discountPercent = (int)($product['discount_percent'] ?? 0);
        $stockStatus = $product['stock_status'] ?? 'in_stock';
        $category = $product['category_name'] ?? $product['prod_category'] ?? '';
        $rating = (int)($product['rating'] ?? 0);
        $productId = (int)($product['id'] ?? 0);
        $isNew = (bool)($product['is_new'] ?? false);

        $price = $salePrice ?: $regularPrice;
        $showOldPrice = $salePrice && $salePrice < $regularPrice;

        $html .= '<a href="' . url('product/' . $slug) . '" class="product-card group">';

        // Badges
        if ($discountPercent > 0) {
            $html .= '<span class="discount-badge">-' . $discountPercent . '%</span>';
        }
        if ($isNew) {
            $html .= '<span class="absolute top-12 left-12 bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full z-[2]">New</span>';
        }

        // Image
        $html .= '<div class="prod-img relative">';
        $html .= '<img src="' . $this->e($image) . '" alt="' . $this->e($name) . '" loading="lazy" class="w-full h-full object-cover">';
        $html .= '</div>';

        // Quick action overlay
        $html .= '<div class="absolute top-2 right-2 flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">';
        $html .= '<button data-wishlist="' . $productId . '" onclick="event.stopPropagation();toggleWishlist(' . $productId . ')" class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-gray-500 hover:text-primary-red transition-all" title="Add to Wishlist"><i class="fa-regular fa-heart text-xs"></i></button>';
        $html .= '<button onclick="event.stopPropagation();addToCompare(' . $productId . ')" class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-gray-500 hover:text-primary-red transition-all" title="Compare"><i class="fa-solid fa-arrows-rotate text-xs"></i></button>';
        $html .= '</div>';

        $html .= '</div>';

        // Name
        $html .= '<h4 class="prod-name">' . $this->e($name) . '</h4>';

        // Category
        if ($category) {
            $html .= '<p class="prod-category">' . $this->e($category) . '</p>';
        }

        // Stock status
        if ($stockStatus === 'in_stock') {
            $html .= '<div class="stock-status"><i class="fa-solid fa-circle-check text-green-500"></i> In stock</div>';
        } elseif ($stockStatus === 'out_of_stock') {
            $html .= '<div class="text-red-500 text-xs font-bold mb-1.5"><i class="fa-solid fa-circle-xmark"></i> Out of stock</div>';
        } else {
            $html .= '<div class="text-orange-500 text-xs font-bold mb-1.5"><i class="fa-solid fa-clock"></i> On backorder</div>';
        }

        // Rating
        $html .= '<div class="stars text-[#ffd54f] text-xs mb-2">';
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $rating
                ? '<i class="fa-solid fa-star"></i>'
                : '<i class="fa-regular fa-star"></i>';
        }
        $html .= '</div>';

        // Price
        $html .= '<div class="mb-3">';
        if ($showOldPrice) {
            $html .= '<span class="old-price">' . APP_CURRENCY . number_format($regularPrice, 2) . '</span>';
        }
        $html .= '<span class="new-price">' . APP_CURRENCY . number_format($price, 2) . '</span>';
        $html .= '</div>';

        // Add to cart button
        $html .= '<button onclick="event.stopPropagation();addToCart(' . $productId . ')" class="w-full bg-primary-red text-white text-xs font-bold py-2.5 px-4 rounded-lg hover:bg-opacity-90 transition-all flex items-center justify-center gap-2">';
        $html .= '<i class="fa-solid fa-cart-shopping"></i> Add to Cart';
        $html .= '</button>';

        $html .= '</a>';

        return $html;
    }

    /**
     * Get product primary image
     */
    private function getProductImage(int $productId): string
    {
        // In production, this would fetch from DB
        return asset('images/placeholder.png');
    }
}
