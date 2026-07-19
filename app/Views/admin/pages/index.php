<?php $pages = $pages ?? []; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pages</h1>
        <p class="text-sm text-gray-500">Manage static pages content</p>
    </div>
    <a href="<?= url('13091998/pages/create') ?>" class="bg-primary-red text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition-all"><i class="fa-solid fa-plus mr-1.5"></i> Add Page</a>
</div>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Title</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Slug</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Order</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($pages)): ?>
                <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400"><i class="fa-solid fa-file text-3xl mb-2 block text-gray-300"></i>No pages found</td></tr>
                <?php else: ?>
                <?php foreach ($pages as $page): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 font-medium text-gray-800"><?= e($page['title'] ?? '') ?></td>
                    <td class="px-5 py-3 text-gray-500">/<?= e($page['slug'] ?? '') ?></td>
                    <td class="px-5 py-3"><?= ($page['status'] ?? 0) ? '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>' : '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>' ?></td>
                    <td class="px-5 py-3 text-gray-500"><?= (int)($page['sort_order'] ?? 0) ?></td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="<?= url('13091998/pages/edit/' . $page['id']) ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="<?= url('13091998/pages/delete') ?>" class="inline" onsubmit="return confirm('Delete this page?')">
                                <?= \App\Helpers\Security::csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int)$page['id'] ?>">
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
