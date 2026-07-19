<?php
$product = $product ?? null;
$categories = $categories ?? [];
$isEdit = !empty($product);
$p = $isEdit ? $product : null;
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= url('13091998/products') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Products</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Product' : 'Add Product' ?></h1>
    </div>
    <?php if ($isEdit): ?>
    <a href="<?= url('product/' . e($p->slug ?? $p->id)) ?>" target="_blank" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors"><i class="fa-solid fa-external-link mr-1.5"></i> View on Site</a>
    <?php endif; ?>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/products/update/' . e($p->id)) : url('13091998/products') ?>" enctype="multipart/form-data" class="max-w-4xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">General Information</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?= e(old('name', $p->name ?? '')) ?>" id="product-name" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" value="<?= e(old('slug', $p->slug ?? '')) ?>" id="product-slug" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                    <p class="text-xs text-gray-400 mt-1">Auto-generated from name</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU <span class="text-red-500">*</span></label>
                        <input type="text" name="sku" value="<?= e(old('sku', $p->sku ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="product-category" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (old('category_id', $p->category_id ?? '') == $cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subcategory</label>
                        <select name="subcategory_id" id="product-subcategory" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                            <option value="">Select Subcategory</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                    <textarea name="short_description" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e(old('short_description', $p->short_description ?? '')) ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e(old('description', $p->description ?? '')) ?></textarea>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Pricing & Stock</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Regular Price <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><?= APP_CURRENCY ?></span>
                            <input type="number" step="0.01" name="regular_price" value="<?= e(old('regular_price', $p->regular_price ?? '')) ?>" class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><?= APP_CURRENCY ?></span>
                            <input type="number" step="0.01" name="sale_price" value="<?= e(old('sale_price', $p->sale_price ?? '')) ?>" class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                        <input type="number" name="stock_quantity" value="<?= e(old('stock_quantity', $p->stock_quantity ?? 0)) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                        <select name="stock_status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                            <option value="in_stock" <?= (old('stock_status', $p->stock_status ?? '') === 'in_stock') ? 'selected' : '' ?>>In Stock</option>
                            <option value="out_of_stock" <?= (old('stock_status', $p->stock_status ?? '') === 'out_of_stock') ? 'selected' : '' ?>>Out of Stock</option>
                            <option value="on_backorder" <?= (old('stock_status', $p->stock_status ?? '') === 'on_backorder') ? 'selected' : '' ?>>On Backorder</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                            <option value="1" <?= (old('status', $p->status ?? '1') == '1') ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= (old('status', $p->status ?? '1') == '0') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">SEO</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" value="<?= e(old('meta_title', $p->meta_title ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e(old('meta_description', $p->meta_description ?? '')) ?></textarea>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Images</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Add Images</label>
                    <input type="file" name="images[]" accept="image/*" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-red/10 file:text-primary-red hover:file:bg-primary-red/20" id="imageInput">
                    <p class="text-xs text-gray-400 mt-1">Select multiple images to upload (new images are added independently)</p>
                </div>
                <!-- Upload preview -->
                <div id="imagePreview" class="flex flex-wrap gap-2 hidden"></div>
                <?php if ($isEdit): $imgs = \App\Models\Product::getImages($p->id); ?>
                <?php if (!empty($imgs)): ?>
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Current Images</p>
                    <div class="grid grid-cols-3 gap-3">
                        <?php foreach ($imgs as $img): ?>
                        <div class="relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-50 shadow-sm hover:shadow-md transition-shadow <?= !empty($img['is_primary']) ? 'ring-2 ring-red-500' : '' ?>">
                            <img src="<?= e($img['url'] ?? '') ?>" alt="" class="w-full h-full object-cover" loading="lazy">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <?php if (empty($img['is_primary'])): ?>
                                <button type="button" onclick="setPrimaryImage(<?= $img['id'] ?>)" class="w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center text-sm hover:bg-yellow-600 transition-colors shadow-lg" title="Set as primary">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </button>
                                <?php endif; ?>
                                <button type="button" onclick="deleteProductImage(<?= $img['id'] ?>)" class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center text-sm hover:bg-red-700 transition-colors shadow-lg" title="Delete image">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <?php if (!empty($img['is_primary'])): ?>
                            <span class="absolute top-1.5 left-1.5 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">PRIMARY</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <p class="text-xs text-gray-400">No images yet. Upload some above.</p>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Delivery Pincodes</h2>
                <p class="text-xs text-gray-400">Enter pincodes where this product is available for delivery</p>
                <div id="pincodesWrapper">
                    <?php if ($isEdit): $pincodes = \App\Models\Product::getPincodes($p->id); ?>
                    <?php if (!empty($pincodes)): ?>
                    <?php foreach ($pincodes as $pc): ?>
                    <div class="flex gap-2 items-center mb-2">
                        <input type="text" name="pincodes[]" value="<?= e($pc) ?>" class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" maxlength="10" placeholder="Pincode">
                        <button type="button" class="remove-pincode w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xs hover:bg-red-200 transition-colors">&times;</button>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="flex gap-2 items-center mb-2">
                        <input type="text" name="pincodes[]" class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" maxlength="10" placeholder="Pincode">
                        <button type="button" class="remove-pincode w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xs hover:bg-red-200 transition-colors">&times;</button>
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="flex gap-2 items-center mb-2">
                        <input type="text" name="pincodes[]" class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" maxlength="10" placeholder="Pincode">
                        <button type="button" class="remove-pincode w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xs hover:bg-red-200 transition-colors">&times;</button>
                    </div>
                    <?php endif; ?>
                </div>
                <button type="button" id="addPincodeBtn" class="text-sm text-primary-red font-medium hover:underline inline-flex items-center gap-1"><i class="fa-solid fa-plus"></i> Add another pincode</button>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Flags</h2>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" <?= old('is_featured', $p->is_featured ?? false) ? 'checked' : '' ?> class="w-4 h-4 rounded border-gray-300 text-primary-red focus:ring-primary-red">
                    <span class="text-sm text-gray-700">Featured</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_new" value="1" <?= old('is_new', $p->is_new ?? false) ? 'checked' : '' ?> class="w-4 h-4 rounded border-gray-300 text-primary-red focus:ring-primary-red">
                    <span class="text-sm text-gray-700">New</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_trending" value="1" <?= old('is_trending', $p->is_trending ?? false) ? 'checked' : '' ?> class="w-4 h-4 rounded border-gray-300 text-primary-red focus:ring-primary-red">
                    <span class="text-sm text-gray-700">Trending</span>
                </label>
            </div>
        </div>
    </div>
    <div class="sticky bottom-0 z-10 mt-6">
        <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 shadow-lg">
            <p class="text-xs text-gray-400"><i class="fa-solid fa-rotate mr-1"></i> Cache auto-cleared on save</p>
            <div class="flex gap-2">
                <a href="<?= url('13091998/products') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
                <button type="submit" class="bg-primary-red text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
                    <i class="fa-solid fa-save"></i> <?= $isEdit ? 'Update Product' : 'Create Product' ?>
                </button>
            </div>
        </div>
    </div>
</form>
<?php startSection('scripts') ?>
<script>
// Load subcategories on category change
document.addEventListener('DOMContentLoaded', function() {
    var catSelect = document.getElementById('product-category');
    var subcatSelect = document.getElementById('product-subcategory');
    if (!catSelect || !subcatSelect) return;

    function loadSubcategories(categoryId, selectedSubcatId) {
        subcatSelect.innerHTML = '<option value="">Loading...</option>';
        subcatSelect.disabled = true;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= url('13091998/subcategories/by-category') ?>?category_id=' + categoryId, true);
        xhr.onload = function() {
            subcatSelect.innerHTML = '<option value="">Select Subcategory</option>';
            subcatSelect.disabled = false;
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success && data.subcategories) {
                        data.subcategories.forEach(function(sub) {
                            var opt = document.createElement('option');
                            opt.value = sub.id;
                            opt.textContent = sub.name;
                            if (selectedSubcatId && parseInt(selectedSubcatId) === parseInt(sub.id)) {
                                opt.selected = true;
                            }
                            subcatSelect.appendChild(opt);
                        });
                    }
                } catch(e) {}
            }
        };
        xhr.onerror = function() {
            subcatSelect.innerHTML = '<option value="">Select Subcategory</option>';
            subcatSelect.disabled = false;
        };
        xhr.send();
    }

    catSelect.addEventListener('change', function() {
        var catId = this.value;
        if (catId) {
            loadSubcategories(catId, '<?= $p->subcategory_id ?? '' ?>');
        } else {
            subcatSelect.innerHTML = '<option value="">Select Subcategory</option>';
        }
    });

    // Load on page load if category is already selected
    var initialCat = catSelect.value;
    var initialSubcat = '<?= $p->subcategory_id ?? '' ?>';
    if (initialCat && initialSubcat) {
        loadSubcategories(initialCat, initialSubcat);
    } else if (initialCat) {
                loadSubcategories(initialCat);
    }
});

// Auto-slug
document.getElementById('product-name')?.addEventListener('input', function() {
    const slug = this.value.toLowerCase().replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-').replace(/^-+|-+$/g, '');
    document.getElementById('product-slug').value = slug;
});

// Image preview before upload
document.getElementById('imageInput')?.addEventListener('change', function() {
    var preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    if (this.files && this.files.length > 0) {
        preview.classList.remove('hidden');
        for (var i = 0; i < this.files.length; i++) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var wrap = document.createElement('div');
                wrap.className = 'relative w-20 h-20 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 shadow-sm';
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-full object-cover';
                wrap.appendChild(img);
                preview.appendChild(wrap);
            };
            reader.readAsDataURL(this.files[i]);
        }
    } else {
        preview.classList.add('hidden');
    }
});

// Delete product image
function deleteProductImage(id){
  if(!confirm('Delete this image?')) return;
  var f=document.createElement('form'); f.method='POST';
  f.action='<?= url('13091998/products/delete-image') ?>';
  f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';
  var inp=document.createElement('input'); inp.type='hidden'; inp.name='id'; inp.value=id;
  f.appendChild(inp);
  var pid=document.createElement('input'); pid.type='hidden'; pid.name='product_id'; pid.value='<?= $p->id ?? 0 ?>';
  f.appendChild(pid);
  document.body.appendChild(f); f.submit();
}

// Set image as primary
function setPrimaryImage(id) {
  if(!confirm('Set this image as the primary thumbnail?')) return;
  var f=document.createElement('form'); f.method='POST';
  f.action='<?= url('13091998/products/set-primary-image') ?>';
  f.innerHTML='<?= \App\Helpers\Security::csrfField() ?>';
  var inp=document.createElement('input'); inp.type='hidden'; inp.name='id'; inp.value=id;
  f.appendChild(inp);
  document.body.appendChild(f); f.submit();
}

// Pincodes management
(function() {
    var wrapper = document.getElementById('pincodesWrapper');
    var addBtn = document.getElementById('addPincodeBtn');
    if (!wrapper) return;

    function createPincodeRow(value) {
        var div = document.createElement('div');
        div.className = 'flex gap-2 items-center mb-2';
        div.innerHTML = '<input type="text" name="pincodes[]" value="' + (value || '') + '" class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" maxlength="10" placeholder="Pincode">' +
                       '<button type="button" class="remove-pincode w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xs hover:bg-red-200 transition-colors">&times;</button>';
        div.querySelector('.remove-pincode').addEventListener('click', function() { div.remove(); });
        return div;
    }

    wrapper.querySelectorAll('.remove-pincode').forEach(function(btn) {
        btn.addEventListener('click', function() { btn.parentElement.remove(); });
    });

    if (addBtn) {
        addBtn.addEventListener('click', function() {
            wrapper.appendChild(createPincodeRow(''));
        });
    }
})();
</script>
<?php endSection() ?>
