<?php $cards = $cards ?? []; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Category Cards</h1>
        <p class="text-sm text-gray-500">Manage section cards (Mukut & Topor, Wedding Items, etc.)</p>
    </div>
    <a href="<?= url('13091998/category-cards/create') ?>" class="bg-primary-red text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add Card
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-5 py-3 text-xs uppercase tracking-wider text-gray-500 font-medium">Image</th>
                <th class="px-5 py-3 text-xs uppercase tracking-wider text-gray-500 font-medium">Title</th>
                <th class="px-5 py-3 text-xs uppercase tracking-wider text-gray-500 font-medium">Section</th>
                <th class="px-5 py-3 text-xs uppercase tracking-wider text-gray-500 font-medium">Category</th>
                <th class="px-5 py-3 text-xs uppercase tracking-wider text-gray-500 font-medium">Sort</th>
                <th class="px-5 py-3 text-xs uppercase tracking-wider text-gray-500 font-medium">Status</th>
                <th class="px-5 py-3 text-xs uppercase tracking-wider text-gray-500 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (empty($cards)): ?>
            <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">No cards found</td></tr>
            <?php else: ?>
            <?php foreach ($cards as $c): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3">
                    <?php if (!empty($c['image'])): ?>
                    <img src="<?= uploadUrl($c['image']) ?>" class="w-16 h-10 rounded-lg object-cover border">
                    <?php else: ?>
                    <div class="w-16 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No img</div>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-3 font-medium text-gray-800"><?= e($c['title']) ?></td>
                <td class="px-5 py-3"><span class="text-xs font-medium bg-gray-100 px-2 py-1 rounded"><?= e($c['section_key']) ?></span></td>
                <td class="px-5 py-3 text-gray-500"><?= e($c['category_name'] ?: $c['subcategory_name'] ?: '-') ?></td>
                <td class="px-5 py-3 text-gray-500"><?= $c['sort_order'] ?></td>
                <td class="px-5 py-3">
                    <?php if ($c['is_active']): ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                    <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-1">
                        <a href="<?= url('13091998/category-cards/edit/' . $c['id']) ?>" class="p-1.5 text-gray-400 hover:text-primary-red" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <button onclick="confirmDelete(<?= $c['id'] ?>)" class="p-1.5 text-gray-400 hover:text-red-600" title="Delete"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php startSection('scripts') ?>
<script>function confirmDelete(id){if(confirm('Delete this card?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/category-cards/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}</script>
<?php endSection() ?>
