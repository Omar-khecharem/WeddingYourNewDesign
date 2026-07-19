<?php
$items = $products['items'] ?? [];
$cp = $products['currentPage'] ?? 1;
$tp = $products['totalPages'] ?? 1;
$hasPrev = $products['hasPrev'] ?? false;
$hasNext = $products['hasNext'] ?? false;
$prevPage = $products['prevPage'] ?? 1;
$nextPage = $products['nextPage'] ?? 1;
$search = $_GET['search'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$categories = $categories ?? [];
?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Products</h1>
        <p class="text-sm text-gray-500">Manage your product catalog</p>
    </div>
    <a href="<?= url('13091998/products/create') ?>" class="bg-primary-red text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add Product
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" action="<?= url('13091998/products') ?>" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="<?= e($search) ?>" placeholder="Name, SKU..." class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
            <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                <option value="">All</option>
                <option value="1" <?= $statusFilter === '1' ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= $statusFilter === '0' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-red text-white px-5 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all">Filter</button>
        <a href="<?= url('13091998/products') ?>" class="border border-gray-300 text-gray-600 px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Reset</a>
    </form>
</div>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Image</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Product</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">SKU</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Category</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Price</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Stock</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($items)): ?>
                <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">No products found</td></tr>
                <?php else: ?>
                <?php foreach ($items as $product):
                    $imgs = \App\Models\Product::getImages($product->id);
                    $img = $imgs[0]['url'] ?? '';
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <?php if ($img): ?>
                        <img src="<?= e($img) ?>" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                        <?php else: ?>
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400"><i class="fa-solid fa-image"></i></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <a href="<?= url('13091998/products/edit/' . $product->id) ?>" class="font-medium text-gray-800 hover:text-primary-red transition-colors"><?= e($product->name) ?></a>
                    </td>
                    <td class="px-5 py-3 text-gray-500 font-mono text-xs"><?= e($product->sku ?? 'N/A') ?></td>
                    <td class="px-5 py-3 text-gray-500"><?= e($product->category_name ?? '-') ?></td>
                    <td class="px-5 py-3">
                        <span class="font-medium text-gray-800"><?= formatPrice($product->regular_price) ?></span>
                        <?php if ($product->sale_price): ?>
                        <span class="text-xs text-red-500 line-through ml-1"><?= formatPrice($product->sale_price) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <?php if ($product->stock_quantity > 10): ?>
                        <span class="text-green-600 font-medium"><?= $product->stock_quantity ?></span>
                        <?php elseif ($product->stock_quantity > 0): ?>
                        <span class="text-yellow-600 font-medium"><?= $product->stock_quantity ?></span>
                        <?php else: ?>
                        <span class="text-red-600 font-medium">Out of stock</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <?php if ($product->status == 1): ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="<?= url('13091998/products/edit/' . $product->id) ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <button onclick="confirmDelete(<?= $product->id ?>)" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php if ($tp > 1): ?>
<?php $baseUrl = url('13091998/products?') . ($search ? 'search=' . e($search) . '&' : '') . ($categoryFilter ? 'category=' . e($categoryFilter) . '&' : '') . ($statusFilter !== '' ? 'status=' . e($statusFilter) . '&' : ''); ?>
<div class="flex items-center justify-between mt-4">
    <p class="text-sm text-gray-500">Page <?= $cp ?> / <?= $tp ?></p>
    <div class="flex gap-2">
        <?php if ($hasPrev): ?>
        <a href="<?= $baseUrl ?>page=<?= $prevPage ?>" class="border border-gray-300 text-gray-600 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-50">&laquo; Prev</a>
        <?php endif; ?>
        <?php if ($hasNext): ?>
        <a href="<?= $baseUrl ?>page=<?= $nextPage ?>" class="border border-gray-300 text-gray-600 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-50">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php startSection('scripts') ?>
<script>function confirmDelete(id){if(confirm('Delete this product?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/products/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}</script>
<?php endSection() ?>
