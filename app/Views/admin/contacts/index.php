<?php
$messages = $messages ?? [];
$pagination = $pagination ?? [];
$search = $search ?? '';
$status = $status ?? '';
$unreadCount = \App\Models\ContactMessage::getUnreadCount();
?>
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Contact Messages</h1>
        <p class="text-sm text-gray-500">Manage customer inquiries and messages</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-sm <?= $unreadCount > 0 ? 'text-amber-600 font-semibold' : 'text-gray-400' ?>">
            <i class="fa-solid fa-envelope mr-1"></i> <?= $unreadCount ?> unread
        </span>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" class="p-4 flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search by name, email or message..." class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
        </div>
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
            <option value="">All Status</option>
            <option value="unread" <?= $status === 'unread' ? 'selected' : '' ?>>Unread</option>
            <option value="read" <?= $status === 'read' ? 'selected' : '' ?>>Read</option>
        </select>
        <button type="submit" class="bg-primary-red text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all">
            <i class="fa-solid fa-search mr-1.5"></i> Filter
        </button>
        <?php if ($search || $status): ?>
        <a href="<?= url('13091998/contacts') ?>" class="border border-gray-300 text-gray-600 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider hidden sm:table-cell">Email</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider hidden md:table-cell">Subject</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider hidden lg:table-cell">Date</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-inbox text-3xl mb-2 block text-gray-300"></i>
                        No messages found
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                <tr class="hover:bg-gray-50 transition-colors <?= empty($msg['is_read']) ? 'bg-primary-red/5' : '' ?>">
                    <td class="px-5 py-3">
                        <?php if (empty($msg['is_read'])): ?>
                        <span class="inline-flex items-center justify-center w-3 h-3 rounded-full bg-primary-red" title="Unread"></span>
                        <?php else: ?>
                        <span class="inline-flex items-center justify-center w-3 h-3 rounded-full border border-gray-300 bg-white" title="Read"></span>
                        <?php endif; ?>
                        <?php if (!empty($msg['is_replied'])): ?>
                        <span class="inline-flex items-center justify-center ml-1.5 w-3 h-3 rounded-full bg-green-500" title="Replied"></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <span class="font-medium text-gray-800 <?= empty($msg['is_read']) ? 'font-semibold' : '' ?>"><?= e($msg['name']) ?></span>
                        <?php if (!empty($msg['phone'])): ?>
                        <p class="text-xs text-gray-400"><?= e($msg['phone']) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-gray-600 hidden sm:table-cell"><?= e($msg['email']) ?></td>
                    <td class="px-5 py-3 text-gray-600 hidden md:table-cell max-w-[200px] truncate"><?= e($msg['subject'] ?? '(No subject)') ?></td>
                    <td class="px-5 py-3 text-gray-500 whitespace-nowrap hidden lg:table-cell"><?= formatDate($msg['created_at'] ?? '') ?></td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="<?= url('13091998/contacts/' . e($msg['id'])) ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="View"><i class="fa-solid fa-eye"></i></a>
                            <form method="POST" action="<?= url('13091998/contacts/delete') ?>" onsubmit="return confirm('Delete this message?')" class="inline">
                                <?= \App\Helpers\Security::csrfField() ?>
                                <input type="hidden" name="id" value="<?= e($msg['id']) ?>">
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($pagination) && ($pagination['totalPages'] ?? 0) > 1): ?>
    <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50">
        <p class="text-xs text-gray-500"><?= $pagination['total'] ?? 0 ?> total messages</p>
        <div class="flex items-center gap-1">
            <?php if ($pagination['hasPrev'] ?? false): ?>
            <a href="<?= url('13091998/contacts?page=' . ($pagination['prevPage'] ?? 1) . ($search ? '&search=' . urlencode($search) : '') . ($status ? '&status=' . $status : '')) ?>" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200 rounded transition-colors"><i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>
            <span class="px-3 py-1.5 text-xs font-medium text-gray-700"><?= $pagination['page'] ?? 1 ?> / <?= $pagination['totalPages'] ?? 1 ?></span>
            <?php if ($pagination['hasNext'] ?? false): ?>
            <a href="<?= url('13091998/contacts?page=' . ($pagination['nextPage'] ?? 1) . ($search ? '&search=' . urlencode($search) : '') . ($status ? '&status=' . $status : '')) ?>" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200 rounded transition-colors"><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
