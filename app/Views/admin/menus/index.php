<?php $menus = $menus ?? []; $items = $items ?? []; $currentMenuId = $currentMenuId ?? 0; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Menu Items</h1>
        <p class="text-sm text-gray-500">Manage navigation menu items</p>
    </div>
    <a href="<?= url('13091998/menus/create?menu_id=' . $currentMenuId) ?>" class="bg-primary-red text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add Menu Item
    </a>
</div>

<!-- Menu selector tabs -->
<?php if (!empty($menus)): ?>
<div class="flex gap-2 mb-5 overflow-x-auto">
    <?php foreach ($menus as $menu): ?>
    <a href="<?= url('13091998/menus?menu_id=' . $menu['id']) ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentMenuId == $menu['id'] ? 'bg-primary-red text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>">
        <?= e($menu['name']) ?>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Title</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">URL</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Order</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($items)): ?>
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">No menu items found</td></tr>
                <?php else: ?>
                <?php foreach ($items as $item): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 font-medium text-gray-800">
                        <?php if (!empty($item['depth'])): ?>
                        <span class="inline-block w-4" style="margin-left:<?= $item['depth'] * 20 ?>px">└</span>
                        <?php endif; ?>
                        <?= e($item['title']) ?>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs font-mono max-w-[200px] truncate"><?= e($item['url'] ?? '') ?></td>
                    <td class="px-5 py-3 text-gray-500"><?= e($item['type']) ?></td>
                    <td class="px-5 py-3 text-gray-500"><?= $item['sort_order'] ?></td>
                    <td class="px-5 py-3">
                        <?php if ($item['is_active']): ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="<?= url('13091998/menus/edit/' . $item['id']) ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <button onclick="confirmDelete(<?= $item['id'] ?>)" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete"><i class="fa-solid fa-trash"></i></button>
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
<script>function confirmDelete(id){if(confirm('Delete this menu item?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/menus/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}</script>
<?php endSection() ?>
