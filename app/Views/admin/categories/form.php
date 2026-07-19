<?php $category = $category ?? null; $parents = $parents ?? []; $isEdit = !empty($category); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= url('13091998/categories') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Categories</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Category' : 'Add Category' ?></h1>
    </div>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/categories/update/' . $category['id']) : url('13091998/categories') ?>" enctype="multipart/form-data" class="max-w-4xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Details</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?= e(old('name', $category['name'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                    <select name="parent_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="">None (Top Level)</option>
                        <?php foreach ($parents as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= (old('parent_id', $category['parent_id'] ?? '') == $p['id']) ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e(old('description', $category['description'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Category Image</h2>
                <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-red/10 file:text-primary-red hover:file:bg-primary-red/20">
                <?php if ($isEdit && !empty($category['image'])): ?>
                <div class="relative">
                    <img src="<?= uploadUrl($category['image']) ?>" class="w-full h-28 object-cover rounded-lg border">
                    <label class="absolute top-2 right-2 bg-white/90 text-xs text-red-600 px-2 py-1 rounded cursor-pointer shadow"><input type="checkbox" name="remove_image" value="1"> Remove</label>
                </div>
                <?php endif; ?>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Settings</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="<?= old('sort_order', $category['sort_order'] ?? 0) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="featured" value="1" <?= old('featured', $category['featured'] ?? false) ? 'checked' : '' ?> class="w-4 h-4 rounded border-gray-300 text-primary-red focus:ring-primary-red">
                    <span class="text-sm text-gray-700">Featured on homepage</span>
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="1" <?= (old('status', $category['status'] ?? '1') == '1') ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (old('status', $category['status'] ?? '1') == '0') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary-red text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all"><i class="fa-solid fa-save mr-1.5"></i> <?= $isEdit ? 'Update Category' : 'Create Category' ?></button>
                <a href="<?= url('13091998/categories') ?>" class="block text-center border border-gray-300 text-gray-600 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 mt-6">
        <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400"><i class="fa-solid fa-rotate mr-1"></i> Cache auto-cleared on save</p>
            <div class="flex gap-2">
                <a href="<?= url('13091998/categories') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
                <button type="submit" class="bg-primary-red text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
                    <i class="fa-solid fa-save"></i> <?= $isEdit ? 'Update Category' : 'Create Category' ?>
                </button>
            </div>
        </div>
    </div>
</form>
