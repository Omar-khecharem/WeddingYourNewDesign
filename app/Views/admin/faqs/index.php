<?php $faqs = $faqs ?? []; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">FAQ</h1>
        <p class="text-sm text-gray-500">Manage frequently asked questions</p>
    </div>
    <a href="<?= url('13091998/faqs/create') ?>" class="bg-primary-red text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
        <i class="fa-solid fa-plus"></i> Add FAQ
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <?php if (empty($faqs)): ?>
    <div class="text-center py-12 text-gray-400">No FAQs yet. <a href="<?= url('13091998/faqs/create') ?>" class="text-primary-red hover:underline">Add your first FAQ</a></div>
    <?php else: ?>
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3 w-16">#</th>
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Question</th>
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3 w-20">Order</th>
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3 w-20">Status</th>
                <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3 w-28">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($faqs as $faq): ?>
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <td class="px-5 py-4 text-sm text-gray-400 font-mono"><?= $faq['id'] ?></td>
                <td class="px-5 py-4">
                    <p class="text-sm font-medium text-gray-800 line-clamp-1"><?= e($faq['question']) ?></p>
                </td>
                <td class="px-5 py-4 text-sm text-gray-500"><?= (int)$faq['sort_order'] ?></td>
                <td class="px-5 py-4">
                    <?php if ($faq['status']): ?>
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">Active</span>
                    <?php else: ?>
                    <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2.5 py-1 rounded-full">Inactive</span>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="<?= url('13091998/faqs/edit/' . $faq['id']) ?>" class="text-blue-600 hover:bg-blue-50 w-8 h-8 rounded-lg flex items-center justify-center transition-colors" title="Edit"><i class="fa-solid fa-pen text-xs"></i></a>
                        <button onclick="confirmDelete(<?= $faq['id'] ?>)" class="text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center transition-colors" title="Delete"><i class="fa-solid fa-trash text-xs"></i></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<?php startSection('scripts') ?>
<script>
function confirmDelete(id) {
    if (confirm('Delete this FAQ?')) {
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = '<?= url('13091998/faqs/delete') ?>';
        f.innerHTML = '<?= \App\Helpers\Security::csrfField() ?>';
        var i = document.createElement('input');
        i.type = 'hidden';
        i.name = 'id';
        i.value = id;
        f.appendChild(i);
        document.body.appendChild(f);
        f.submit();
    }
}
</script>
<?php endSection() ?>
