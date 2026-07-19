<?php $post = $post ?? null; $isEdit = !empty($post); $categoryName = old('category_name', $post['category_name'] ?? ''); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= url('13091998/blog') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Blog</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Post' : 'Add Post' ?></h1>
    </div>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/blog/update/' . $post['id']) : url('13091998/blog') ?>" class="max-w-4xl" enctype="multipart/form-data">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Content</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?= e(old('title', $post['title'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="<?= e(old('slug', $post['slug'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" placeholder="Leave empty to auto-generate">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                    <textarea name="excerpt" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e(old('excerpt', $post['excerpt'] ?? '')) ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <textarea name="content" rows="15" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none font-mono"><?= e(old('content', $post['content'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Settings</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <input type="text" name="category_name" value="<?= e($categoryName) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" placeholder="e.g. Conseils Mariage">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                    <?php if ($isEdit && !empty($post['featured_image'])): ?>
                    <div class="mb-2">
                        <img src="<?= uploadUrl($post['featured_image'], 'blogs') ?>" class="w-32 h-24 object-cover rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-400 mt-1">Current image</p>
                    </div>
                    <input type="hidden" name="existing_image" value="<?= e($post['featured_image']) ?>">
                    <?php endif; ?>
                    <input type="file" name="featured_image" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-red file:text-white hover:file:opacity-90">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_published" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="1" <?= ($post['is_published'] ?? '') == 1 ? 'selected' : '' ?>>Published</option>
                        <option value="0" <?= ($post['is_published'] ?? '') == 0 ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Published At</label>
                    <input type="datetime-local" name="published_at" value="<?= e(old('published_at', !empty($post['published_at']) ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : date('Y-m-d\TH:i'))) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">SEO</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" value="<?= e(old('meta_title', $post['meta_title'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e(old('meta_description', $post['meta_description'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="bg-primary-red text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition-all"><i class="fa-solid fa-floppy-disk mr-1.5"></i> <?= $isEdit ? 'Update' : 'Create' ?></button>
        <a href="<?= url('13091998/blog') ?>" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</a>
    </div>
</form>
