<?php
$categories = $categories ?? [];
$products = $products ?? [];
$filter = $filter ?? 'featured';
$categorySlug = $categorySlug ?? '';
$subcategorySlug = $subcategorySlug ?? '';
$homepageSettings = $homepageSettings ?? [];
?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Home Page</h1>
        <p class="text-sm text-gray-500">Manage products and settings displayed on the homepage</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" action="<?= url('13091998/homepage') ?>" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Section</label>
            <select name="filter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                <option value="featured" <?= $filter === 'featured' ? 'selected' : '' ?>>Featured Products</option>
                <option value="trending" <?= $filter === 'trending' ? 'selected' : '' ?>>Trending Products</option>
                <option value="recent" <?= $filter === 'recent' ? 'selected' : '' ?>>Recent Products</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
            <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat['slug']) ?>" <?= $categorySlug === $cat['slug'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Subcategory</label>
            <input type="text" name="subcategory" value="<?= e($subcategorySlug) ?>" placeholder="subcategory-slug" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
        </div>
        <button type="submit" class="bg-primary-red text-white px-5 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all">Filter</button>
        <a href="<?= url('13091998/homepage') ?>" class="border border-gray-300 text-gray-600 px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Reset</a>
    </form>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
    <?php if (empty($products)): ?>
    <div class="col-span-full text-center py-12 text-gray-400">
        <i class="fa-solid fa-box-open text-4xl mb-3 text-gray-300"></i>
        <p class="text-sm">No products found</p>
    </div>
    <?php else: ?>
    <?php foreach ($products as $p): ?>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all group">
        <a href="<?= url('13091998/products/edit/' . e($p['id'])) ?>" class="block aspect-square bg-gray-50 overflow-hidden">
            <img src="<?= e($p['image'] ?? asset('images/placeholder.png')) ?>" alt="<?= e($p['name'] ?? '') ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </a>
        <div class="p-3 space-y-2">
            <a href="<?= url('13091998/products/edit/' . e($p['id'])) ?>" class="block text-sm font-semibold text-gray-800 hover:text-primary-red transition-colors leading-tight line-clamp-2"><?= e($p['name'] ?? '') ?></a>
            <?php if (!empty($p['category_name'])): ?>
            <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium"><?= e($p['category_name']) ?></p>
            <?php endif; ?>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-bold text-gray-800"><?= formatPrice($p['sale_price'] ?: $p['regular_price']) ?></span>
                    <?php if (!empty($p['sale_price'])): ?>
                    <span class="text-xs text-gray-400 line-through ml-1"><?= formatPrice($p['regular_price']) ?></span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($p['discount_percent'])): ?>
                <span class="text-[10px] font-bold text-green-600 bg-green-100 px-1.5 py-0.5 rounded">-<?= (int)$p['discount_percent'] ?>%</span>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-1.5 pt-1">
                <form method="POST" action="<?= url('13091998/homepage/toggle-featured') ?>" class="inline">
                    <input type="hidden" name="id" value="<?= e($p['id']) ?>">
                    <input type="hidden" name="redirect" value="<?= e(url('13091998/homepage?' . http_build_query(['filter' => $filter, 'category' => $categorySlug, 'subcategory' => $subcategorySlug]))) ?>">
                    <button type="submit" class="text-[11px] font-medium px-2 py-1 rounded transition-colors <?= ($p['is_featured'] ?? false) ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' ?>">
                        <i class="fa-solid fa-star <?= ($p['is_featured'] ?? false) ? 'text-yellow-500' : '' ?>"></i> Featured
                    </button>
                </form>
                <form method="POST" action="<?= url('13091998/homepage/toggle-trending') ?>" class="inline">
                    <input type="hidden" name="id" value="<?= e($p['id']) ?>">
                    <input type="hidden" name="redirect" value="<?= e(url('13091998/homepage?' . http_build_query(['filter' => $filter, 'category' => $categorySlug, 'subcategory' => $subcategorySlug]))) ?>">
                    <button type="submit" class="text-[11px] font-medium px-2 py-1 rounded transition-colors <?= ($p['is_trending'] ?? false) ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' ?>">
                        <i class="fa-solid fa-fire <?= ($p['is_trending'] ?? false) ? 'text-orange-500' : '' ?>"></i> Trending
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
        <div class="w-10 h-10 rounded-lg bg-primary-red/10 flex items-center justify-center text-primary-red flex-shrink-0"><i class="fa-solid fa-sliders"></i></div>
        <div>
            <h2 class="text-lg font-bold text-gray-800">Homepage Settings</h2>
            <p class="text-sm text-gray-500">Video showcase, WhatsApp, coupon banner &mdash; saved immediately</p>
        </div>
    </div>
    <form method="POST" action="<?= url('13091998/homepage/settings') ?>" enctype="multipart/form-data" class="space-y-4">
        <?php if (!empty($homepageSettings)): ?>
        <?php foreach ($homepageSettings as $key => $value): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"><?= e(ucwords(str_replace('_', ' ', $key))) ?></label>
            <?php if (str_contains($key, 'image') || str_contains($key, 'bg') || str_contains($key, 'banner')): ?>
            <div class="flex items-center gap-4">
                <?php if (!empty($value)): ?>
                <img src="<?= uploadUrl($value) ?>" class="w-20 h-20 rounded-lg object-cover border border-gray-200">
                <?php endif; ?>
                <div class="flex-1">
                    <input type="file" name="setting_file_<?= e($key) ?>" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-red/10 file:text-primary-red hover:file:bg-primary-red/20">
                    <input type="hidden" name="setting[<?= e($key) ?>]" value="<?= e($value ?? '') ?>">
                    <?php if (!empty($value)): ?>
                    <label class="text-xs text-red-600 cursor-pointer mt-1 inline-block hover:text-red-700"><input type="checkbox" name="remove_setting_<?= e($key) ?>" value="1"> Remove image</label>
                    <?php endif; ?>
                </div>
            </div>
            <?php elseif (str_contains($key, 'textarea') || str_contains($key, 'text')): ?>
            <textarea name="setting[<?= e($key) ?>]" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e($value ?? '') ?></textarea>
            <?php else: ?>
            <input type="text" name="setting[<?= e($key) ?>]" value="<?= e($value ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="text-sm text-gray-400">No homepage settings found.</p>
        <?php endif; ?>
        <div class="pt-3 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-primary-red text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
                <i class="fa-solid fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>
