<?php
$wishlistItems = $wishlistItems ?? [];
?>

<?= component('Breadcrumb', ['crumbs' => [
    ['label' => 'My Account', 'url' => url('account')],
    ['label' => 'My Wishlist', 'url' => null],
]]) ?>

<?php startSection('content') ?>

<div class="bg-white rounded-xl border border-gray-200 p-6 lg:p-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
            <i class="fa-solid fa-heart"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Wishlist</h1>
            <p class="text-sm text-gray-500">All your favorite products in one place</p>
        </div>
    </div>

    <?php if (empty($wishlistItems)): ?>
    <div class="text-center py-16">
        <div class="text-5xl text-gray-300 mb-5">
            <i class="fa-regular fa-heart"></i>
        </div>
        <h2 class="text-lg font-bold text-gray-700 mb-2">Your wishlist is empty</h2>
        <p class="text-sm text-gray-500 mb-6">Browse our catalog and add items you love.</p>
        <a href="<?= url('products') ?>" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-6 py-3 rounded-lg transition-colors">
            <i class="fa-solid fa-bag-shopping"></i> Discover our products
        </a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        <?php foreach ($wishlistItems as $item):
            $pImg = $item['image'] ?? asset('images/placeholder.png');
            $pName = $item['name'] ?? '';
            $pSlug = $item['slug'] ?? '#';
            $pReg = $item['regular_price'] ?? 0;
            $pSale = $item['sale_price'] ?? 0;
            $pDisc = $item['discount_percent'] ?? 0;
            $pPrice = $pSale ?: $pReg;
            $pCat = $item['category_name'] ?? '';
            $pSub = $item['subcategory_name'] ?? '';
            $pId = $item['id'] ?? 0;
        ?>
        <a href="<?= url('product/' . e($pSlug)) ?>" class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow group block">
            <div class="relative">
                <?php if ($pDisc > 0): ?>
                <span class="absolute top-2 left-2 bg-red-600 text-white text-xs font-black px-2 py-0.5 rounded-md z-10">-<?= $pDisc ?>%</span>
                <?php endif; ?>
                    <img src="<?= e($pImg) ?>" alt="<?= e($pName) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 w-full aspect-square bg-gray-50" loading="lazy">
                <button data-wishlist="<?= $pId ?>" onclick="event.stopPropagation();toggleWishlist(<?= $pId ?>)" class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-red-500 hover:bg-red-50 transition-all z-10" title="Remove from wishlist">
                    <svg class="w-4 h-4" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide"><?= e($pName) ?></h3>
                <?php if ($pCat): ?>
                <p class="text-xs text-gray-400 mt-1"><?= e($pCat) ?><?= $pSub ? ' / ' . e($pSub) : '' ?></p>
                <?php endif; ?>
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-lg font-black text-red-600"><?= formatPrice($pPrice) ?></span>
                    <?php if ($pDisc > 0): ?>
                    <span class="text-xs text-gray-300 line-through"><?= formatPrice($pReg) ?></span>
                    <?php endif; ?>
                </div>
                <button onclick="event.stopPropagation();addToCart(<?= $pId ?>, 1)" class="mt-3 w-full bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg> Add to Cart
                </button>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php endSection() ?>
