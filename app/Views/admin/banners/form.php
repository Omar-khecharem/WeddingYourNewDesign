<?php $banner = $banner ?? null; $isEdit = !empty($banner); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= url('13091998/banners') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Banners</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Banner' : 'Add Banner' ?></h1>
    </div>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/banners/update/' . $banner['id']) : url('13091998/banners') ?>" enctype="multipart/form-data" class="max-w-2xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <select name="position" id="positionSelect" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="hero_left" <?= (old('position', $banner['position'] ?? '') === 'hero_left') ? 'selected' : '' ?>>Hero Left (Image)</option>
                        <option value="hero_right" <?= (old('position', $banner['position'] ?? '') === 'hero_right') ? 'selected' : '' ?>>Hero Right Carousel (Image)</option>
                        <option value="bride_video" <?= (old('position', $banner['position'] ?? '') === 'bride_video') ? 'selected' : '' ?>>Brand Story (Image)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="<?= old('title', $banner['title'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" placeholder="e.g. Wedding Season Special">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" placeholder="Short description for the banner overlay"><?= old('description', $banner['description'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                    <input type="text" name="link" value="<?= old('link', $banner['link'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" placeholder="e.g. /products?on_sale=1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="<?= old('sort_order', $banner['sort_order'] ?? 0) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="1" <?= (old('is_active', $banner['is_active'] ?? '1') == '1') ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (old('is_active', $banner['is_active'] ?? '1') == '0') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" id="uploadLabel">Upload Video / Image</label>
                    <p class="text-xs text-gray-400 mb-2" id="uploadHint">MP4, WebM, OGG, MOV or JPEG, PNG, WebP</p>
                    <input type="file" name="image" id="fileInput" accept="image/*,video/mp4,video/webm,video/ogg,video/quicktime" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-red/10 file:text-primary-red hover:file:bg-primary-red/20" <?= $isEdit ? '' : 'required' ?>>
                </div>
                <!-- Preview -->
                <div id="previewArea" class="relative rounded-lg border border-gray-200 overflow-hidden bg-gray-50 <?= ($isEdit && !empty($banner['image'])) ? '' : 'hidden' ?>" style="min-height:120px">
                    <?php if ($isEdit && !empty($banner['image'])): ?>
                    <?php $ext = strtolower(pathinfo($banner['image'], PATHINFO_EXTENSION)); $isVid = in_array($ext, ['mp4','webm','ogg','mov']); ?>
                    <?php if ($isVid): ?>
                    <video class="w-full object-contain" style="max-height:200px" controls><source src="<?= uploadUrl($banner['image']) ?>" type="video/<?= $ext === 'mov' ? 'quicktime' : $ext ?>"></video>
                    <?php else: ?>
                    <img src="<?= uploadUrl($banner['image']) ?>" class="w-full object-contain" style="max-height:200px">
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400"><i class="fa-solid fa-rotate mr-1"></i> Cache auto-cleared on save</p>
            <div class="flex gap-2">
                <a href="<?= url('13091998/banners') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
                <button type="submit" class="bg-primary-red text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
                    <i class="fa-solid fa-save"></i> <?= $isEdit ? 'Update' : 'Upload' ?>
                </button>
            </div>
        </div>
    </div>
</form>
<script>
var sel=document.getElementById('positionSelect'),hint=document.getElementById('uploadHint'),label=document.getElementById('uploadLabel'),fileInput=document.getElementById('fileInput'),preview=document.getElementById('previewArea');
function updateHint(){
    if(sel.value==='hero_left'||sel.value==='bride_video'){
        label.textContent='Upload Video';
        hint.textContent='MP4, WebM, OGG or MOV';
        fileInput.accept='video/mp4,video/webm,video/ogg,video/quicktime';
    }else{
        label.textContent='Upload Image';
        hint.textContent='JPEG, PNG or WebP';
        fileInput.accept='image/*';
    }
}
sel.addEventListener('change',updateHint);updateHint();
fileInput.addEventListener('change',function(e){
    var file=e.target.files[0];if(!file)return;
    var reader=new FileReader();
    reader.onload=function(ev){
        preview.classList.remove('hidden');
        if(file.type.startsWith('video/')){
            preview.innerHTML='<video class="w-full object-contain" style="max-height:200px" controls><source src="'+ev.target.result+'"></video>';
        }else{
            preview.innerHTML='<img src="'+ev.target.result+'" class="w-full object-contain" style="max-height:200px">';
        }
    };
    reader.readAsDataURL(file);
});
</script>
<?php startSection('scripts') ?>
<script>
<?php if ($isEdit): ?>
// Ensure edit preview shows if image exists
<?php endif; ?>
</script>
<?php endSection() ?>