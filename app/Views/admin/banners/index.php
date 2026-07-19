<?php $banners = $banners ?? []; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Banners</h1>
        <p class="text-sm text-gray-500">Manage promotional banners and carousel images</p>
    </div>
    <a href="<?= url('13091998/banners/create') ?>" class="bg-primary-red text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Add Banner
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Image</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Position</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Link</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Sort</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($banners)): ?>
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">No banners found</td></tr>
                <?php else: ?>
                <?php foreach ($banners as $b): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <?php if (!empty($b['image'])): ?>
                        <?php $ext = strtolower(pathinfo($b['image'], PATHINFO_EXTENSION)); $isVid = in_array($ext, ['mp4','webm','ogg','mov']); ?>
                        <?php if ($isVid): ?>
                        <video class="w-20 h-12 rounded-lg object-cover border border-gray-200" muted><source src="<?= uploadUrl($b['image']) ?>" type="video/<?= $ext === 'mov' ? 'quicktime' : $ext ?>"></video>
                        <?php else: ?>
                        <img src="<?= uploadUrl($b['image']) ?>" class="w-20 h-12 rounded-lg object-cover border border-gray-200">
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="w-20 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No media</div>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 font-medium text-gray-800"><?= e($b['name']) ?></td>
                    <td class="px-5 py-3"><span class="text-xs font-medium bg-gray-100 px-2 py-1 rounded"><?= e($b['position']) ?></span></td>
                    <td class="px-5 py-3 text-gray-500 max-w-[200px] truncate"><?= e($b['link'] ?? '-') ?></td>
                    <td class="px-5 py-3 text-gray-500"><?= $b['sort_order'] ?></td>
                    <td class="px-5 py-3">
                        <?php if ($b['is_active']): ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="<?= url('13091998/banners/edit/' . $b['id']) ?>" class="p-1.5 text-gray-400 hover:text-primary-red" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <button onclick="confirmDelete(<?= $b['id'] ?>)" class="p-1.5 text-gray-400 hover:text-red-600" title="Delete"><i class="fa-solid fa-trash"></i></button>
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
<script>function confirmDelete(id){if(confirm('Delete this banner?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/banners/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}</script>
<?php endSection() ?>
