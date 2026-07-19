<?php $subcategories = $subcategories ?? []; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Subcategories</h1>
        <p class="text-sm text-gray-500">Manage subcategories with images</p>
    </div>
</div>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Image</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Category</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Slug</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($subcategories)): ?>
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">No subcategories found</td></tr>
                <?php else: ?>
                <?php foreach ($subcategories as $sub): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <?php if (!empty($sub['image'])): ?>
                        <img src="<?= uploadUrl($sub['image'], 'categories') ?>" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                        <?php else: ?>
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400"><i class="fa-solid fa-image"></i></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 font-medium text-gray-800"><?= e($sub['name']) ?></td>
                    <td class="px-5 py-3 text-gray-500"><?= e($sub['category_name'] ?? '') ?></td>
                    <td class="px-5 py-3 text-gray-500 text-xs font-mono"><?= e($sub['slug']) ?></td>
                    <td class="px-5 py-3">
                        <?php if ($sub['status']): ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="<?= url('13091998/subcategories/edit/' . $sub['id']) ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <button onclick="confirmDelete(<?= $sub['id'] ?>)" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php startSection('scripts') ?>
<script>function confirmDelete(id){if(confirm('Delete this subcategory?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/subcategories/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}</script>
<?php endSection() ?>
