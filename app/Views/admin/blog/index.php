<?php $posts = $posts ?? []; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Blog Posts</h1>
        <p class="text-sm text-gray-500">Manage blog posts and articles</p>
    </div>
    <a href="<?= url('13091998/blog/create') ?>" class="bg-primary-red text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition-all"><i class="fa-solid fa-plus mr-1.5"></i> Add Post</a>
</div>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Title</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Category</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Published</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($posts)): ?>
                <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400"><i class="fa-solid fa-newspaper text-3xl mb-2 block text-gray-300"></i>No blog posts found</td></tr>
                <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <?php if (!empty($post['featured_image'])): ?>
                            <img src="<?= uploadUrl($post['featured_image'], 'blogs') ?>" class="w-10 h-10 rounded object-cover" loading="lazy">
                            <?php endif; ?>
                            <span class="font-medium text-gray-800"><?= e($post['title'] ?? '') ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-500"><?= e($post['category_name'] ?? '-') ?></td>
                    <td class="px-5 py-3"><?= ($post['is_published'] ?? 0) ? '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Published</span>' : '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Draft</span>' ?></td>
                    <td class="px-5 py-3 text-gray-500 whitespace-nowrap"><?= !empty($post['published_at']) ? formatDate($post['published_at'], 'd/m/Y') : '-' ?></td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="<?= url('13091998/blog/edit/' . $post['id']) ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="<?= url('13091998/blog/delete') ?>" class="inline" onsubmit="return confirm('Delete this post?')">
                                <?= \App\Helpers\Security::csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
