<?php $faq = $faq ?? null; $isEdit = !empty($faq); ?>
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <a href="<?= url('13091998/faqs') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to FAQs</a>
        <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit FAQ' : 'Add FAQ' ?></h1>
    </div>
</div>
<form method="POST" action="<?= $isEdit ? url('13091998/faqs/update/' . $faq['id']) : url('13091998/faqs') ?>" class="max-w-3xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Question <span class="text-red-500">*</span></label>
                <input type="text" name="question" value="<?= $isEdit ? e($faq['question']) : '' ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red transition-all outline-none" placeholder="e.g. How long does shipping take?">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Answer <span class="text-red-500">*</span></label>
                <textarea name="answer" rows="5" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red transition-all outline-none" placeholder="Write the answer here..."><?= $isEdit ? e($faq['answer']) : '' ?></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="<?= $isEdit ? (int)$faq['sort_order'] : 0 ?>" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red transition-all outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red transition-all outline-none">
                        <option value="1" <?= $isEdit && $faq['status'] ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $isEdit && !$faq['status'] ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400"><i class="fa-solid fa-rotate mr-1"></i> Cache auto-cleared on save</p>
            <div class="flex gap-2">
                <a href="<?= url('13091998/faqs') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Cancel</a>
                <button type="submit" class="bg-primary-red text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
                    <i class="fa-solid fa-save"></i> <?= $isEdit ? 'Update' : 'Save' ?>
                </button>
            </div>
        </div>
    </div>
</form>
