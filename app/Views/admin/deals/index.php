<?php $deals = $deals ?? []; ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Best Deals</h1>
        <p class="text-sm text-gray-500">Manage "Best Deal For You" promotional images</p>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Add Deal Image</h2>
            <form method="POST" action="<?= url('13091998/deals') ?>" enctype="multipart/form-data" class="space-y-4">
                <?= \App\Helpers\Security::csrfField() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-primary-red transition-colors cursor-pointer mb-1" onclick="document.getElementById('deal-image').click()">
                        <img id="deal-image-preview" class="max-h-24 mx-auto mb-2 hidden rounded">
                        <i id="deal-image-icon" class="fa-solid fa-cloud-upload-alt text-2xl text-gray-300 mb-1 block"></i>
                        <p class="text-xs text-gray-400">Click to upload image</p>
                    </div>
                    <input type="file" name="image" id="deal-image" accept="image/*" required class="hidden" onchange="previewDealImage(this)">
                </div>
                <button type="submit" class="w-full bg-primary-red text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all"><i class="fa-solid fa-plus mr-1.5"></i> Add Deal</button>
            </form>
        </div>
    </div>
    <div class="lg:col-span-3">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <?php if (empty($deals)): ?>
            <div class="col-span-full text-center py-12 text-gray-400">No deals yet</div>
            <?php else: ?>
            <?php foreach ($deals as $d): ?>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden group relative">
                <img src="<?= uploadUrl($d['image'], 'deals') ?>" class="w-full object-contain bg-gray-50" style="max-height:280px">
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="confirmDelete(<?= $d['id'] ?>)" class="bg-red-500 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-600"><i class="fa-solid fa-trash text-xs"></i></button>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php startSection('scripts') ?>
<script>
function confirmDelete(id){if(confirm('Delete this deal?')){var f=document.createElement('form');f.method='POST';f.action='<?= url('13091998/deals/delete') ?>';f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';var i=document.createElement('input');i.type='hidden';i.name='id';i.value=id;f.appendChild(i);document.body.appendChild(f);f.submit();}}
function previewDealImage(input){if(input.files&&input.files[0]){var r=new FileReader();r.onload=function(e){var p=document.getElementById('deal-image-preview');p.src=e.target.result;p.classList.remove('hidden');document.getElementById('deal-image-icon').classList.add('hidden');};r.readAsDataURL(input.files[0]);}}
</script>
<?php endSection() ?>
