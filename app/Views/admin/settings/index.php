<?php $groups = $groups ?? [];
$groupLabels = [
    'general' => ['General', 'Store information and branding', 'fa-globe'],
    'social' => ['Social Media', 'Facebook, Instagram, YouTube, Maps links', 'fa-share-nodes'],
    'contact' => ['Contact', 'Contact information and address', 'fa-address-book'],
    'shipping' => ['Shipping', 'Shipping settings', 'fa-truck'],
    'tax' => ['Tax', 'Tax rate and name settings', 'fa-percent'],
    'seo' => ['SEO', 'Meta tags and keywords for SEO', 'fa-magnifying-glass'],
    'logs' => ['Logs', 'Activity log cleanup settings', 'fa-list'],
];
// unset($groups['tax'], $groups['seo'], $groups['homepage']);
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Settings</h1>
        <p class="text-sm text-gray-500">Configure your store &mdash; changes appear immediately on the frontend</p>
    </div>
    <a href="<?= url('') ?>" target="_blank" class="inline-flex items-center gap-1.5 border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">
        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Visit Site
    </a>
</div>
<div class="flex gap-6 flex-col lg:flex-row">
    <div class="lg:w-56 flex-shrink-0">
        <nav class="bg-white rounded-xl border border-gray-200 p-3 space-y-1 sticky top-6">
            <?php foreach ($groups as $groupKey => $groupSettings): ?>
            <?php $label = $groupLabels[$groupKey] ?? [ucfirst($groupKey), '', 'fa-gear']; ?>
            <a href="#group-<?= e($groupKey) ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-all sidebar-link">
                <i class="fa-solid <?= e($label[2]) ?> w-5 text-center"></i> <?= e($label[0]) ?>
            </a>
            <?php endforeach; ?>
            <?php if (empty($groups)): ?>
            <p class="text-sm text-gray-400 px-3 py-2">No settings groups</p>
            <?php endif; ?>
        </nav>
    </div>
    <div class="flex-1 space-y-6">
        <form id="settings-form" method="POST" action="<?= url('13091998/settings') ?>" enctype="multipart/form-data">
            <?= \App\Helpers\Security::csrfField() ?>
            <input type="hidden" name="clear_cache" value="1">
            <?php foreach ($groups as $groupKey => $groupSettings): ?>
            <?php $label = $groupLabels[$groupKey] ?? [ucfirst($groupKey), '', 'fa-gear']; ?>
            <div id="group-<?= e($groupKey) ?>" class="bg-white rounded-xl border border-gray-200 p-5 scroll-mt-6">
                <div class="flex items-center justify-between mb-5 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-red/10 flex items-center justify-center text-primary-red flex-shrink-0"><i class="fa-solid <?= e($label[2]) ?>"></i></div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800"><?= e($label[0]) ?></h2>
                            <p class="text-sm text-gray-500"><?= e($label[1] ?? '') ?></p>
                        </div>
                    </div>
                    <button type="submit" class="bg-primary-red text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-1.5 shadow-sm shadow-red-200 save-group-btn" data-group="<?= e($groupKey) ?>">
                        <i class="fa-solid fa-save text-xs"></i> Save
                    </button>
                </div>
                <div class="space-y-4">
                    <?php foreach ($groupSettings as $setting): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?= e($setting['label'] ?? $setting['key']) ?></label>
                        <?php if ($setting['type'] === 'image'): ?>
                            <div class="flex items-center gap-4">
                                <?php if (!empty($setting['value'])): ?>
                                <img src="<?= uploadUrl($setting['value']) ?>" class="w-16 h-16 rounded-lg object-cover border border-gray-200">
                                <?php endif; ?>
                                <div class="flex-1">
                                    <input type="file" name="setting_file_<?= e($setting['key']) ?>" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-red/10 file:text-primary-red hover:file:bg-primary-red/20">
                                    <input type="hidden" name="setting[<?= e($setting['key']) ?>]" value="<?= e($setting['value'] ?? '') ?>">
                                    <?php if (!empty($setting['value'])): ?>
                                    <label class="text-xs text-red-600 cursor-pointer mt-1 inline-block hover:text-red-700"><input type="checkbox" name="remove_setting_<?= e($setting['key']) ?>" value="1"> Remove image</label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif ($setting['type'] === 'textarea'): ?>
                            <textarea name="setting[<?= e($setting['key']) ?>]" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none"><?= e($setting['value'] ?? '') ?></textarea>
                        <?php elseif ($setting['type'] === 'boolean'): ?>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="setting[<?= e($setting['key']) ?>]" value="0">
                                <input type="checkbox" name="setting[<?= e($setting['key']) ?>]" value="1" <?= ($setting['value'] ?? '') === '1' ? 'checked' : '' ?> class="w-4 h-4 rounded border-gray-300 text-primary-red focus:ring-primary-red">
                                <span class="text-sm text-gray-500">Enabled</span>
                            </label>
                        <?php elseif ($setting['type'] === 'select'): ?>
                            <select name="setting[<?= e($setting['key']) ?>]" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                                <?php $options = is_string($setting['options'] ?? '') ? json_decode($setting['options'], true) : ($setting['options'] ?? []); ?>
                                <?php if (is_array($options)): foreach ($options as $optVal => $optLabel): ?>
                                <option value="<?= e($optVal) ?>" <?= ($setting['value'] ?? '') === $optVal ? 'selected' : '' ?>><?= e($optLabel) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        <?php else: ?>
                            <input type="<?= $setting['type'] === 'url' ? 'url' : ($setting['type'] === 'email' ? 'email' : 'text') ?>" name="setting[<?= e($setting['key']) ?>]" value="<?= e($setting['value'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <?php endif; ?>
                        <?php if (!empty($setting['description'])): ?>
                        <p class="text-xs text-gray-400 mt-1"><?= e($setting['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (!empty($groups)): ?>
            <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 sticky bottom-0 shadow-lg">
                <p class="text-xs text-gray-400"><i class="fa-solid fa-rotate mr-1"></i> Cache auto-cleared on save &mdash; frontend updates instantly</p>
                <div class="flex gap-2">
                    <a href="<?= url('13091998/clear-cache') ?>" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate text-xs"></i> Clear Cache
                    </a>
                    <button type="submit" class="bg-primary-red text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-all inline-flex items-center gap-2 shadow-sm shadow-red-200">
                        <i class="fa-solid fa-save"></i> Save All Settings
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<script>
document.querySelectorAll('.save-group-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        document.getElementById('settings-form').submit();
    });
});
</script>
