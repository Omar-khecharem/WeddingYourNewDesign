<?php
$users = $users ?? [];
$pagination = $pagination ?? ['currentPage' => 1, 'totalPages' => 1, 'hasPrev' => false, 'hasNext' => false, 'prevPage' => 1, 'nextPage' => 1];
$search = $search ?? '';
$role = $role ?? '';
$status = $status ?? '';
?>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Customers</h1>
                    <p class="text-sm text-gray-500">Manage your customer accounts</p>
                </div>
                <span class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg border border-gray-200"><?= $pagination['totalItems'] ?? count($users) ?> customers</span>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 mb-6">
                <form method="GET" class="p-4 flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search by name, email or phone..." class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                    </div>
                    <select name="role" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="">All Roles</option>
                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>Customer</option>
                    </select>
                    <select name="status" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red outline-none">
                        <option value="">All Status</option>
                        <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Banned</option>
                    </select>
                    <button type="submit" class="bg-primary-red text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-all">
                        <i class="fa-solid fa-search mr-1.5"></i> Filter
                    </button>
                    <?php if ($search || $role || $status !== ''): ?>
                    <a href="<?= url('13091998/users') ?>" class="border border-gray-300 text-gray-600 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Customer</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Email</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Phone</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Role</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Orders</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Registered on</th>
                                <th class="px-5 py-3 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-users text-3xl mb-2 block text-gray-300"></i>
                                    No customers found
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-primary-red/10 flex items-center justify-center text-primary-red font-bold text-sm">
                                            <?= strtoupper(substr(e($user['name'] ?? '?'), 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-800"><?= e($user['name']) ?></span>
                                            <?php if (!empty($user['username'])): ?>
                                            <p class="text-xs text-gray-400">@<?= e($user['username']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-500"><?= e($user['email']) ?></td>
                                <td class="px-5 py-3 text-gray-500"><?= e($user['phone'] ?? '-') ?></td>
                                <td class="px-5 py-3">
                                    <?php $urole = $user['role'] ?? 'customer'; ?>
                                    <?php if ($urole === 'admin'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Admin</span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Customer</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3">
                                    <?php $ustat = $user['status'] ?? 1; ?>
                                    <?php if ((int)$ustat === 1): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Banned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3 text-gray-500 font-medium"><?= $user['orders_count'] ?? 0 ?></td>
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap"><?= formatDate($user['created_at'] ?? '') ?></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="<?= url('13091998/users/' . e($user['id']) . '/edit') ?>" class="p-1.5 text-gray-400 hover:text-primary-red transition-colors" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                        <?php if ((int)($user['status'] ?? 1) === 1): ?>
                                        <a href="<?= url('13091998/users/' . e($user['id']) . '/ban') ?>" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Ban"><i class="fa-solid fa-ban"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            $baseUrl = url('13091998/users?');
            $queryParams = [];
            if ($search) $queryParams[] = 'search=' . urlencode($search);
            if ($role) $queryParams[] = 'role=' . urlencode($role);
            if ($status !== '') $queryParams[] = 'status=' . urlencode($status);
            if (!empty($queryParams)) $baseUrl .= implode('&', $queryParams) . '&';
            echo \App\Core\View::component('Pagination', [
                'currentPage' => $pagination['currentPage'],
                'totalPages' => $pagination['totalPages'],
                'baseUrl' => $baseUrl,
            ]);
            ?>
<?php endSection() ?>
