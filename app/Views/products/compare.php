<?php
/**
 * Product Comparison Page
 *
 * Compare products side by side with key attributes.
 *
 * Variables:
 *   - $products: array of product arrays
 *   - $count: int
 */

$products = $products ?? [];
$count = $count ?? 0;
?>

<div class="max-w-[1200px] mx-auto my-8 px-4">
    <?= \App\Core\View::component('Breadcrumb', ['crumbs' => $breadcrumb ?? []]) ?>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="section-title">Product Comparison</h1>
            <p class="section-subtitle"><?= $count ?> product<?= $count > 1 ? 's' : '' ?> selected</p>
        </div>
        <?php if ($count > 0): ?>
                <a href="<?= url('compare/clear') ?>"
               class="bg-premium-burgundy hover:bg-premium-cabernet text-white text-sm font-bold px-5 py-2.5 rounded-lg transition-all"
               onclick="return confirm('Are you sure you want to clear all products from comparison?')">
                <i class="fa-solid fa-trash-can mr-1.5"></i> Clear All
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-20 bg-white rounded-xl border border-premium-warm-gray">
            <div class="text-6xl text-premium-stone mb-6">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <h2 class="text-xl font-bold text-premium-mink mb-3">No products to compare</h2>
            <p class="text-premium-taupe text-sm mb-6">
                Add products to compare to see their differences.
            </p>
            <a href="<?= url('products') ?>" class="maroon-btn">
                View Shop <i class="fa-solid fa-angles-right ml-1.5"></i>
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto bg-white rounded-xl border border-premium-warm-gray shadow-sm">
            <table class="w-full text-sm">
                <tbody class="divide-y divide-premium-warm-gray">
                    <!-- Remove -->
                    <tr>
                        <td class="w-48 py-3 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Actions
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-3 text-center align-middle">
                                <a href="<?= url('compare/remove/' . e($product['slug'] ?? '')) ?>"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-premium-crimson hover:text-premium-cabernet transition-colors"
                                   onclick="return confirm('Remove this product from comparison?')">
                                    <i class="fa-solid fa-xmark"></i> Remove
                                </a>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Image -->
                    <tr>
                        <td class="w-48 py-4 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Image
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center min-w-[200px] align-middle">
                                <div class="w-32 h-32 mx-auto rounded-lg overflow-hidden bg-premium-warm-gray">
                                    <img src="<?= e(!empty($product['primary_image']) ? uploadUrl($product['primary_image'], 'products') : asset('images/placeholder.png')) ?>"
                                         alt="<?= e($product['name']) ?>"
                                         class="w-full h-full object-contain p-1"
                                         loading="lazy">
                                </div>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Name -->
                    <tr>
                        <td class="py-4 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Name
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center align-middle">
                                <a href="<?= url('product/' . $product['slug']) ?>"
                                   class="font-bold text-premium-charcoal hover:text-premium-crimson transition-colors">
                                    <?= e($product['name']) ?>
                                </a>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Price -->
                    <tr>
                        <td class="py-4 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Price
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center align-middle">
                                <?php if (!empty($product['sale_price'])): ?>
                                    <span class="text-premium-stone line-through text-xs"><?= formatPrice($product['regular_price']) ?></span>
                                    <span class="text-premium-crimson font-bold text-base block"><?= formatPrice($product['sale_price']) ?></span>
                                    <?php if (!empty($product['discount_percent'])): ?>
                                        <span class="inline-block bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full mt-1">
                                            -<?= (int) $product['discount_percent'] ?>%
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="font-bold text-premium-charcoal"><?= formatPrice($product['regular_price']) ?></span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Rating -->
                    <tr>
                        <td class="py-4 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Reviews
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center align-middle">
                                <?php $rating = round($product['rating_avg'] ?? 0); ?>
                                <div class="text-[#ffd54f] text-sm mb-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="<?= $i <= $rating ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-premium-taupe text-xs">
                                    <?= number_format($product['rating_avg'] ?? 0, 1) ?>
                                    (<?= (int) ($product['rating_count'] ?? 0) ?> reviews)
                                </span>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Stock -->
                    <tr>
                        <td class="py-4 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Availability
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center align-middle">
                                <?php if (($product['stock_status'] ?? '') === 'in_stock'): ?>
                                    <span class="inline-flex items-center gap-1 text-green-600 font-bold text-xs">
                                        <i class="fa-solid fa-circle-check"></i> In Stock
                                    </span>
                                <?php elseif (($product['stock_status'] ?? '') === 'out_of_stock'): ?>
                                    <span class="inline-flex items-center gap-1 text-premium-crimson font-bold text-xs">
                                        <i class="fa-solid fa-circle-xmark"></i> Out of Stock
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-orange-500 font-bold text-xs">
                                        <i class="fa-solid fa-clock"></i> On Order
                                    </span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Category -->
                    <tr>
                        <td class="py-4 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Category
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center align-middle">
                                <span class="text-premium-mink text-xs font-medium bg-premium-warm-gray px-3 py-1.5 rounded-full">
                                    <?= e($product['category_name'] ?? '—') ?>
                                </span>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Brand -->
                    <tr>
                        <td class="py-4 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Brand
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center align-middle">
                                <span class="text-premium-mink font-medium">
                                    <?= e($product['brand_name'] ?? '—') ?>
                                </span>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Description -->
                    <tr>
                        <td class="py-4 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Description
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center align-middle">
                                <p class="text-premium-mink text-xs leading-relaxed line-clamp-3">
                                    <?= e($product['short_description'] ?? '—') ?>
                                </p>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <!-- Actions -->
                    <tr>
                        <td class="py-4 px-5 font-bold text-premium-mink bg-premium-ivory align-middle">
                            Action
                        </td>
                        <?php foreach ($products as $product): ?>
                            <td class="p-4 text-center align-middle">
                                <a href="<?= url('compare/remove/' . $product['slug']) ?>"
                                   class="inline-flex items-center gap-1.5 text-premium-crimson hover:text-premium-cabernet text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-xmark"></i> Remove
                                </a>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
