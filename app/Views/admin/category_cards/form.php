<?php $card = $card ?? null; $categories = $categories ?? []; $subcategories = $subcategories ?? []; $isEdit = !empty($card); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= url('13091998/category-cards') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Cards</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Card' : 'Add Card' ?></h1>
    </div>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/category-cards/update/' . $card['id']) : url('13091998/category-cards') ?>" enctype="multipart/form-data" class="max-w-4xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="<?= e(old('title', $card['title'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Key <span class="text-red-500">*</span></label>
                        <select name="section_key" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                            <option value="wedding_crowns" <?= (old('section_key', $card['section_key'] ?? '') === 'wedding_crowns') ? 'selected' : '' ?>>Mukut & Topor</option>
                            <option value="wedding_items" <?= (old('section_key', $card['section_key'] ?? '') === 'wedding_items') ? 'selected' : '' ?>>Wedding Items</option>
                            <option value="traditional" <?= (old('section_key', $card['section_key'] ?? '') === 'traditional') ? 'selected' : '' ?>>Gachhkouto & Dorpon</option>
                            <option value="bestsellers" <?= (old('section_key', $card['section_key'] ?? '') === 'bestsellers') ? 'selected' : '' ?>>Best Sellers</option>
                            <option value="custom" <?= (old('section_key', $card['section_key'] ?? '') === 'custom') ? 'selected' : '' ?>>Custom</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                    <input type="text" name="subtitle" value="<?= e(old('subtitle', $card['subtitle'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Linked Category</label>
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                            <option value="">None</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (old('category_id', $card['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Linked Subcategory</label>
                        <select name="subcategory_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                            <option value="">None</option>
                            <?php foreach ($subcategories as $sc): ?>
                            <option value="<?= $sc['id'] ?>" <?= (old('subcategory_id', $card['subcategory_id'] ?? '') == $sc['id']) ? 'selected' : '' ?>><?= e($sc['name']) ?> (<?= e($sc['cat_name']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Link (overrides category)</label>
                    <input type="url" name="link" value="<?= e(old('link', $card['link'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Image</h2>
                <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-red/10 file:text-primary-red" <?= $isEdit ? '' : 'required' ?>>
                <?php if ($isEdit && !empty($card['image'])): ?>
                <img src="<?= uploadUrl($card['image']) ?>" class="w-full h-32 object-cover rounded-lg border">
                <?php endif; ?>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Settings</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="<?= old('sort_order', $card['sort_order'] ?? 0) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="1" <?= (old('is_active', $card['is_active'] ?? '1') == '1') ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (old('is_active', $card['is_active'] ?? '1') == '0') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary-red text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all"><i class="fa-solid fa-save mr-1.5"></i> <?= $isEdit ? 'Update Card' : 'Create Card' ?></button>
                <a href="<?= url('13091998/category-cards') ?>" class="block text-center border border-gray-300 text-gray-600 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
            </div>
        </div>
    </div>
    <div class="sticky bottom-0 z-10 mt-6">
        <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 shadow-lg">
            <p class="text-xs text-gray-400"><i class="fa-solid fa-rotate mr-1"></i> Cache auto-cleared on save</p>
            <div class="flex gap-2">
                <a href="<?= url('13091998/category-cards') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
                <button type="submit" class="bg-primary-red text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
                    <i class="fa-solid fa-save"></i> <?= $isEdit ? 'Update Card' : 'Create Card' ?>
                </button>
            </div>
        </div>
    </div>
</form>
