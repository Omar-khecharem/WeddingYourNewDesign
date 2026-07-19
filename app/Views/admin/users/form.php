<?php $user = $user ?? null; if (!$user) { http_response_code(404); exit; } ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= url('13091998/users') ?>" class="text-sm text-gray-500 hover:text-primary-red transition-colors mb-1 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Users</a>
        <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
    </div>
</div>
<form method="POST" action="<?= url('13091998/users/update/' . $user['id']) ?>" class="max-w-4xl">
    <?= \App\Helpers\Security::csrfField() ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Account Details</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?= e(old('name', $user['name'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="<?= e(old('email', $user['email'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="<?= e(old('phone', $user['phone'] ?? '')) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none" placeholder="Leave blank to keep current">
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-bold text-gray-800">Permissions</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="customer" <?= ($user['role'] ?? '') === 'customer' ? 'selected' : '' ?>>Customer</option>
                        <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="manager" <?= ($user['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="1" <?= ($user['status'] ?? '') == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ($user['status'] ?? '') == 0 ? 'selected' : '' ?>>Banned</option>
                    </select>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-lg font-bold text-gray-800 mb-3">Account Info</h2>
                <div class="text-sm text-gray-600 space-y-1">
                    <p>Registered: <?= formatDate($user['created_at'] ?? '') ?></p>
                    <p>Last login: <?= !empty($user['last_login_at']) ? formatDate($user['last_login_at']) : 'Never' ?></p>
                    <?php if (!empty($user['last_login_ip'])): ?>
                    <p>Last IP: <?= e($user['last_login_ip']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="bg-primary-red text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition-all"><i class="fa-solid fa-floppy-disk mr-1.5"></i> Save Changes</button>
        <a href="<?= url('13091998/users') ?>" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</a>
    </div>
</form>
