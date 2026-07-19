<?php
$requests = $requests ?? [];
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Password Reset Requests</h1>
        <p class="text-sm text-gray-500">Users requesting password reset — handle manually</p>
    </div>
    <span class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg border border-gray-200"><?= count($requests) ?> pending</span>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">User</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Email</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Requested</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Expires</th>
                    <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-inbox text-3xl mb-2 block text-gray-300"></i>
                        No pending password reset requests
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($requests as $req): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary-red/10 flex items-center justify-center text-primary-red font-bold text-sm">
                                <?= strtoupper(substr(e($req['user_name'] ?? '?'), 0, 1)) ?>
                            </div>
                            <span class="font-medium text-gray-800"><?= e($req['user_name'] ?? 'Unknown') ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-500"><?= e($req['email']) ?></td>
                    <td class="px-5 py-3">
                        <?php if (isset($req['user_status']) && (int)$req['user_status'] === 1): ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Banned</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-gray-500 whitespace-nowrap"><?= formatDate($req['created_at'] ?? '') ?></td>
                    <td class="px-5 py-3 text-gray-500 whitespace-nowrap"><?= formatDate($req['expires_at'] ?? '') ?></td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="<?= $req['user_id'] ? url('13091998/users/' . $req['user_id'] . '/edit') : '#' ?>" class="px-3 py-1.5 text-xs font-medium bg-primary-red/10 text-primary-red rounded-lg hover:bg-primary-red/20 transition-all <?= !$req['user_id'] ? 'opacity-50 pointer-events-none' : '' ?>">
                                <i class="fa-solid fa-key mr-1"></i> Reset Password
                            </a>
                            <form method="POST" action="<?= url('13091998/forgot-password-requests/' . $req['id'] . '/handle') ?>" class="inline" onsubmit="return confirm('Mark this request as handled?')">
                                <?= \App\Helpers\Security::csrfField() ?>
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-green-600 transition-colors" title="Mark as handled">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
