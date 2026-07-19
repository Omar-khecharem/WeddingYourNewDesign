<?php
$message = $message ?? [];
?>
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <a href="<?= url('13091998/contacts') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Messages
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Message from <?= e($message['name'] ?? 'Unknown') ?></h1>
        <p class="text-sm text-gray-500"><?= e($message['email'] ?? '') ?><?= !empty($message['phone']) ? ' &middot; ' . e($message['phone']) : '' ?></p>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium <?= empty($message['is_read']) ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' ?>">
            <i class="fa-solid <?= empty($message['is_read']) ? 'fa-envelope' : 'fa-envelope-open' ?>"></i>
            <?= empty($message['is_read']) ? 'Unread' : 'Read' ?>
        </span>
        <?php if (!empty($message['is_replied'])): ?>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
            <i class="fa-solid fa-reply"></i> Replied
        </span>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-lg font-bold text-gray-800"><?= e($message['subject'] ?? '(No subject)') ?></h2>
                    <p class="text-xs text-gray-400 mt-1">Received <?= formatDate($message['created_at'] ?? '') ?></p>
                </div>
            </div>
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">
                <?= e($message['message'] ?? '') ?>
            </div>
        </div>

        
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Contact Details</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs">Name</dt>
                    <dd class="text-gray-800 font-medium"><?= e($message['name'] ?? '') ?></dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs">Email</dt>
                    <dd class="text-gray-800"><a href="mailto:<?= e($message['email'] ?? '') ?>" class="text-primary-red hover:underline"><?= e($message['email'] ?? '') ?></a></dd>
                </div>
                <?php if (!empty($message['phone'])): ?>
                <div>
                    <dt class="text-gray-400 text-xs">Phone</dt>
                    <dd class="text-gray-800"><?= e($message['phone']) ?></dd>
                </div>
                <?php endif; ?>
                <div>
                    <dt class="text-gray-400 text-xs">Date</dt>
                    <dd class="text-gray-800"><?= formatDate($message['created_at'] ?? '', 'M d, Y h:i A') ?></dd>
                </div>
                <?php if (!empty($message['read_at'])): ?>
                <div>
                    <dt class="text-gray-400 text-xs">Read at</dt>
                    <dd class="text-gray-800"><?= formatDate($message['read_at'], 'M d, Y h:i A') ?></dd>
                </div>
                <?php endif; ?>
                <?php if (!empty($message['replied_at'])): ?>
                <div>
                    <dt class="text-gray-400 text-xs">Replied at</dt>
                    <dd class="text-gray-800"><?= formatDate($message['replied_at'], 'M d, Y h:i A') ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Actions</h3>
            <div class="space-y-2">
                <a href="mailto:<?= e($message['email'] ?? '') ?>" class="flex items-center gap-2 px-3 py-2.5 bg-gray-50 rounded-lg text-sm text-gray-700 hover:bg-primary-red/5 hover:text-primary-red transition-all">
                    <i class="fa-solid fa-envelope w-5 text-center text-gray-400"></i> Send Email
                </a>
                <form method="POST" action="<?= url('13091998/contacts/delete') ?>" onsubmit="return confirm('Delete this message permanently?')">
                    <?= \App\Helpers\Security::csrfField() ?>
                    <input type="hidden" name="id" value="<?= e($message['id'] ?? '') ?>">
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 bg-gray-50 rounded-lg text-sm text-red-600 hover:bg-red-50 transition-all">
                        <i class="fa-solid fa-trash w-5 text-center text-red-400"></i> Delete Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
