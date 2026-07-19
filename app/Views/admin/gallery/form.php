<?php $item = $item ?? null; $isEdit = !empty($item); ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <a href="<?= url('13091998/gallery') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Gallery</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Gallery Image' : 'Add Gallery Image' ?></h1>
    </div>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/gallery/update/' . $item['id']) : url('13091998/gallery') ?>" enctype="multipart/form-data" class="max-w-2xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                <p class="text-xs text-gray-400 mb-2">JPEG, PNG or WebP</p>
                <input type="file" name="image" id="fileInput" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-red/10 file:text-primary-red hover:file:bg-primary-red/20" <?= $isEdit ? '' : 'required' ?>>
            </div>
            <div id="previewArea" class="relative rounded-lg border border-gray-200 overflow-hidden bg-gray-50 <?= ($isEdit && !empty($item['image'])) ? '' : 'hidden' ?>" style="min-height:120px">
                <?php if ($isEdit && !empty($item['image'])): ?>
                <img src="<?= uploadUrl($item['image']) ?>" class="w-full object-contain" style="max-height:250px">
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400"><i class="fa-solid fa-rotate mr-1"></i> Cache auto-cleared on save</p>
            <div class="flex gap-2">
                <a href="<?= url('13091998/gallery') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
                <button type="submit" class="bg-primary-red text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
                    <i class="fa-solid fa-save"></i> <?= $isEdit ? 'Update' : 'Upload' ?>
                </button>
            </div>
        </div>
    </div>
</form>
<script>
document.getElementById('fileInput')?.addEventListener('change', function(e) {
    var file = e.target.files[0]; if (!file) return;
    var reader = new FileReader();
    reader.onload = function(ev) {
        var pa = document.getElementById('previewArea');
        pa.classList.remove('hidden');
        pa.innerHTML = '<img src="' + ev.target.result + '" class="w-full object-contain" style="max-height:250px">';
    };
    reader.readAsDataURL(file);
});
</script>
