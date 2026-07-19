<?php $subcategory = $subcategory ?? null; $category = $category ?? null; $categories = $categories ?? []; $isEdit = !empty($subcategory); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= url('13091998/categories') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Categories</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Subcategory' : 'Add Subcategory' ?></h1>
    </div>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/subcategories/update/' . $subcategory['id']) : url('13091998/subcategories') ?>" enctype="multipart/form-data" class="max-w-4xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="<?= e(old('name', $subcategory['name'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category <span class="text-red-500">*</span></label>
                        <?php if ($category): ?>
                        <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                        <input type="text" value="<?= e($category['name']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm bg-gray-50 text-gray-500" readonly>
                        <?php else: ?>
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (old('category_id', $subcategory['category_id'] ?? '') == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e(old('description', $subcategory['description'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Subcategory Image</h2>
                <input type="file" name="image" id="subcatImageInput" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-red/10 file:text-primary-red hover:file:bg-primary-red/20">
                <div id="subcatImagePreview" class="<?= ($isEdit && !empty($subcategory['image'])) ? '' : 'hidden' ?>">
                    <img src="<?= $isEdit ? uploadUrl($subcategory['image'] ?? '', 'categories') : '' ?>" class="w-full h-28 object-cover rounded-lg border">
                </div>
                <?php if ($isEdit && !empty($subcategory['image'])): ?>
                <label class="flex items-center gap-2 text-xs text-red-600 cursor-pointer"><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
                <?php endif; ?>
                <script>document.getElementById('subcatImageInput')?.addEventListener('change',function(){var p=document.getElementById('subcatImagePreview');if(!p)return;var img=p.querySelector('img');if(this.files&&this.files[0]){var r=new FileReader();r.onload=function(e){img.src=e.target.result;p.classList.remove('hidden');};r.readAsDataURL(this.files[0]);}else{p.classList.add('hidden');}});</script>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Settings</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="<?= old('sort_order', $subcategory['sort_order'] ?? 0) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="1" <?= (old('status', $subcategory['status'] ?? '1') == '1') ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (old('status', $subcategory['status'] ?? '1') == '0') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary-red text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all"><i class="fa-solid fa-save mr-1.5"></i> <?= $isEdit ? 'Update Subcategory' : 'Create Subcategory' ?></button>
                <a href="<?= url('13091998/categories') ?>" class="block text-center border border-gray-300 text-gray-600 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
            </div>
        </div>
    </div>
</form>
