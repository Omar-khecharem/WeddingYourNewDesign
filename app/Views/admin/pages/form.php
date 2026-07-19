<?php $page = $page ?? null; $isEdit = !empty($page); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= url('13091998/pages') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Pages</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Page' : 'Add Page' ?></h1>
    </div>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/pages/update/' . $page['id']) : url('13091998/pages') ?>" class="max-w-4xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Content</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?= e(old('title', $page['title'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="<?= e(old('slug', $page['slug'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" placeholder="Leave empty to auto-generate">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <textarea name="content" rows="15" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none font-mono"><?= e(old('content', $page['content'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Settings</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="1" <?= ($page['status'] ?? '') == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ($page['status'] ?? '') == 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="<?= (int)($page['sort_order'] ?? 0) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">SEO</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" value="<?= e(old('meta_title', $page['meta_title'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e(old('meta_description', $page['meta_description'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="bg-primary-red text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition-all"><i class="fa-solid fa-floppy-disk mr-1.5"></i> <?= $isEdit ? 'Update' : 'Create' ?></button>
        <a href="<?= url('13091998/pages') ?>" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</a>
    </div>
</form>
