<?php $item = $item ?? null; $menus = $menus ?? []; $parents = $parents ?? []; $selectedMenuId = $selectedMenuId ?? 0; $isEdit = !empty($item); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= url('13091998/menus') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Menu Items</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Menu Item' : 'Add Menu Item' ?></h1>
    </div>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/menus/update/' . $item['id']) : url('13091998/menus') ?>" class="max-w-3xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
            <h2 class="text-lg font-bold text-gray-800">Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Menu <span class="text-red-500">*</span></label>
                    <select name="menu_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                        <?php foreach ($menus as $menu): ?>
                        <option value="<?= $menu['id'] ?>" <?= ($selectedMenuId == $menu['id']) ? 'selected' : '' ?>><?= e($menu['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent Item</label>
                    <select name="parent_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="">None (Top Level)</option>
                        <?php foreach ($parents as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= (old('parent_id', $item['parent_id'] ?? '') == $p['id']) ? 'selected' : '' ?>><?= e($p['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="<?= e(old('title', $item['title'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                <input type="text" name="url" value="<?= e(old('url', $item['url'] ?? '')) ?>" placeholder="/products?category=..." class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="custom" <?= (old('type', $item['type'] ?? 'custom') == 'custom') ? 'selected' : '' ?>>Custom Link</option>
                        <option value="category" <?= (old('type', $item['type'] ?? '') == 'category') ? 'selected' : '' ?>>Category</option>
                        <option value="page" <?= (old('type', $item['type'] ?? '') == 'page') ? 'selected' : '' ?>>Page</option>
                        <option value="product" <?= (old('type', $item['type'] ?? '') == 'product') ? 'selected' : '' ?>>Product</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="<?= old('sort_order', $item['sort_order'] ?? 0) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                    <option value="1" <?= (old('is_active', $item['is_active'] ?? '1') == '1') ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= (old('is_active', $item['is_active'] ?? '1') == '0') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="sticky bottom-0 z-10">
            <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 shadow-lg">
                <p class="text-xs text-gray-400"><i class="fa-solid fa-rotate mr-1"></i> Cache auto-cleared on save</p>
                <div class="flex gap-2">
                    <a href="<?= url('13091998/menus') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
                    <button type="submit" class="bg-primary-red text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
                        <i class="fa-solid fa-save"></i> <?= $isEdit ? 'Update Menu Item' : 'Create Menu Item' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
