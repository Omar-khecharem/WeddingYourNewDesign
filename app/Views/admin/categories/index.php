<?php $categories = $categories ?? []; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
        <p class="text-sm text-gray-500">Manage product categories & subcategories</p>
    </div>
    <a href="<?= url('13091998/categories/create') ?>" class="bg-primary-red text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add Category
    </a>
</div>
<?php $subsByCat = $subsByCat ?? []; ?>
<div class="space-y-4">
    <?php if (empty($categories)): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-400">No categories found</div>
    <?php else: ?>
    <?php foreach ($categories as $cat): ?>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <?php if (!empty($cat['image'])): ?>
                <img src="<?= uploadUrl($cat['image']) ?>" class="w-8 h-8 rounded-lg object-cover border border-gray-200">
                <?php else: ?>
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400"><i class="fa-solid fa-folder"></i></div>
                <?php endif; ?>
                <div>
                    <span class="font-bold text-gray-800"><?= e($cat['name']) ?></span>
                    <span class="text-xs text-gray-400 ml-2"><?= $cat['product_count'] ?? 0 ?> products</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <?php if ($cat['featured']): ?><span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Featured</span><?php endif; ?>
                <?php if (!$cat['status']): ?><span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Inactive</span><?php endif; ?>
                <a href="<?= url('13091998/subcategories/create/' . $cat['id']) ?>" class="p-1.5 text-gray-400 hover:text-green-600 transition-colors" title="Add Subcategory"><i class="fa-solid fa-plus"></i></a>
                <a href="<?= url('13091998/categories/edit/' . $cat['id']) ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="Edit Category"><i class="fa-solid fa-pen"></i></a>
                <button onclick="confirmDeleteCat(<?= $cat['id'] ?>)" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete Category"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
        <?php if (!empty($subsByCat[$cat['id']])): ?>
        <div class="divide-y divide-gray-50">
            <?php foreach ($subsByCat[$cat['id']] as $sub): ?>
            <div class="flex items-center justify-between px-8 py-2.5 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <?php if (!empty($sub['image'])): ?>
                    <img src="<?= uploadUrl($sub['image'], 'categories') ?>" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                    <?php else: ?>
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300"><i class="fa-solid fa-image text-sm"></i></div>
                    <?php endif; ?>
                    <span class="text-sm text-gray-700"><?= e($sub['name']) ?></span>
                    <span class="text-xs text-gray-400 font-mono">/<?= e($sub['slug']) ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <?php if (!$sub['status']): ?><span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Inactive</span><?php endif; ?>
                    <a href="<?= url('13091998/subcategories/edit/' . $sub['id']) ?>" class="p-1 text-gray-400 hover:text-primary-red transition-colors" title="Edit Subcategory"><i class="fa-solid fa-pen text-xs"></i></a>
                    <button onclick="confirmDeleteSub(<?= $sub['id'] ?>)" class="p-1 text-gray-400 hover:text-red-600 transition-colors" title="Delete Subcategory"><i class="fa-solid fa-trash text-xs"></i></button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="px-8 py-3 text-xs text-gray-400 italic">No subcategories — <a href="<?= url('13091998/subcategories/create/' . $cat['id']) ?>" class="text-primary-red hover:underline">add one</a></div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php startSection('scripts') ?>
<script>
function confirmDeleteCat(id){if(confirm('Delete this category and all its subcategories?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/categories/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}
function confirmDeleteSub(id){if(confirm('Delete this subcategory?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/subcategories/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}
</script>
<?php endSection() ?>
