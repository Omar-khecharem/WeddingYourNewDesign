<?php $gallery = $gallery ?? []; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Gallery</h1>
        <p class="text-sm text-gray-500">Manage "Have Some Memories" images</p>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Upload Image</h2>
            <form method="POST" action="<?= url('13091998/gallery') ?>" enctype="multipart/form-data" class="space-y-4">
                <?= \App\Helpers\Security::csrfField() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-primary-red transition-colors cursor-pointer mb-1" onclick="document.getElementById('gallery-image').click()">
                        <img id="gallery-image-preview" class="max-h-24 mx-auto mb-2 hidden rounded">
                        <i id="gallery-image-icon" class="fa-solid fa-cloud-upload-alt text-2xl text-gray-300 mb-1 block"></i>
                        <p class="text-xs text-gray-400">Click to upload</p>
                    </div>
                    <input type="file" name="image" id="gallery-image" accept="image/*" required class="hidden" onchange="previewGalleryImage(this)">
                </div>
                <button type="submit" class="w-full bg-primary-red text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all"><i class="fa-solid fa-upload mr-1.5"></i> Upload</button>
            </form>
        </div>
    </div>
    <div class="lg:col-span-3">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php if (empty($gallery)): ?>
            <div class="col-span-full text-center py-12 text-gray-400">No images in gallery</div>
            <?php else: ?>
            <?php foreach ($gallery as $img): ?>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden group relative">
                <img src="<?= uploadUrl($img['image']) ?>" alt="<?= e($img['title'] ?? 'Gallery') ?>" class="w-full h-40 object-cover">
                <div class="p-3">
                    <p class="text-sm font-medium text-gray-800 truncate"><?= e($img['title'] ?: 'Untitled') ?></p>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                    <a href="<?= url('13091998/gallery/edit/' . $img['id']) ?>" class="bg-blue-500 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:bg-blue-600" title="Edit"><i class="fa-solid fa-pen text-xs"></i></a>
                    <button onclick="confirmDelete(<?= $img['id'] ?>)" class="bg-red-500 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-600" title="Delete"><i class="fa-solid fa-trash text-xs"></i></button>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php startSection('scripts') ?>
<script>
function confirmDelete(id){if(confirm('Delete this image?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/gallery/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}
function previewGalleryImage(input){if(input.files&&input.files[0]){var r=new FileReader();r.onload=function(e){var p=document.getElementById('gallery-image-preview');p.src=e.target.result;p.classList.remove('hidden');document.getElementById('gallery-image-icon').classList.add('hidden');};r.readAsDataURL(input.files[0]);}}
</script>
<?php endSection() ?>
