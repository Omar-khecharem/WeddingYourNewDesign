<?php
$reviews = $reviews ?? [];
$pagination = $pagination ?? ['currentPage' => 1, 'totalPages' => 1, 'totalItems' => 0, 'hasPrev' => false, 'hasNext' => false, 'prevPage' => 1, 'nextPage' => 1];
$currentStatus = $currentStatus ?? '';
$searchQuery = $searchQuery ?? '';
$tabs = [
    '' => 'All',
    'pending' => 'Pending',
    'approved' => 'Approved',
];
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Customer Reviews</h1>
            <p class="text-sm text-gray-500 mt-0.5">Moderate reviews left by your customers</p>
        </div>
        <span class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm"><?= $pagination['totalItems'] ?> review<?= $pagination['totalItems'] !== 1 ? 's' : '' ?></span>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        <div class="flex items-center gap-1 bg-white rounded-lg border border-gray-200 p-1 shadow-sm">
            <?php foreach ($tabs as $val => $label): ?>
            <a href="<?= url('13091998/reviews?' . http_build_query(array_merge($_GET, ['status' => $val, 'page' => 1]))) ?>"
               class="px-4 py-1.5 text-sm font-medium rounded-md transition-all <?= $currentStatus === $val ? 'bg-primary-red text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' ?>">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </div>
        <form method="GET" action="<?= url('13091998/reviews') ?>" class="flex gap-2">
            <?php if ($currentStatus): ?>
            <input type="hidden" name="status" value="<?= e($currentStatus) ?>">
            <?php endif; ?>
            <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Search reviews..." class="w-full sm:w-56 bg-white border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-red/20 focus:border-primary-red">
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 rounded-lg transition-colors"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-5 py-3.5 text-gray-500 font-medium text-xs uppercase tracking-wider">Product</th>
                        <th class="px-5 py-3.5 text-gray-500 font-medium text-xs uppercase tracking-wider">Customer</th>
                        <th class="px-5 py-3.5 text-gray-500 font-medium text-xs uppercase tracking-wider">Rating</th>
                        <th class="px-5 py-3.5 text-gray-500 font-medium text-xs uppercase tracking-wider">Review</th>
                        <th class="px-5 py-3.5 text-gray-500 font-medium text-xs uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-gray-500 font-medium text-xs uppercase tracking-wider">Date</th>
                        <th class="px-5 py-3.5 text-gray-500 font-medium text-xs uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="text-gray-300 text-4xl mb-3"><i class="fa-solid fa-star"></i></div>
                            <p class="text-gray-400 font-medium">No reviews found</p>
                            <p class="text-gray-400 text-xs mt-1">Try adjusting your search or filter</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($reviews as $review):
                        $isApproved = !empty($review['is_approved']);
                        $statusLabel = $isApproved ? 'Approved' : 'Pending';
                        $statusClasses = $isApproved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
                    ?>
                    <tr class="hover:bg-gray-50/80 transition-colors" data-review-id="<?= $review['id'] ?>">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-red/10 to-red-100 flex items-center justify-center text-primary-red text-xs font-bold flex-shrink-0">
                                    <?= e(substr($review['product_name'] ?? 'P', 0, 2)) ?>
                                </div>
                                <a href="<?= url('product/' . e($review['product_slug'] ?? '')) ?>" target="_blank" class="font-medium text-gray-800 hover:text-primary-red transition-colors">
                                    <?= e($review['product_name'] ?? 'N/A') ?>
                                </a>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="text-gray-800 font-medium"><?= e($review['name'] ?? 'N/A') ?></div>
                            <?php if (!empty($review['email'])): ?>
                            <div class="text-xs text-gray-400"><?= e($review['email']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-0.5">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                <svg class="w-4 h-4 <?= $s <= (int)($review['rating'] ?? 0) ? 'text-yellow-400' : 'text-gray-200' ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 max-w-xs">
                            <?php if (!empty($review['title'])): ?>
                            <p class="text-gray-800 text-xs font-semibold mb-0.5"><?= e($review['title']) ?></p>
                            <?php endif; ?>
                            <p class="text-gray-500 text-xs leading-relaxed line-clamp-2"><?= e($review['comment'] ?? '') ?></p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $statusClasses ?>"><?= $statusLabel ?></span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap text-xs"><?= formatDate($review['created_at'] ?? '') ?></td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-1">
                                <?php if (!$isApproved): ?>
                                <a href="<?= url('13091998/reviews/' . e($review['id']) . '/approve') ?>"
                                   class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Approve">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                                <?php else: ?>
                                <a href="<?= url('13091998/reviews/' . e($review['id']) . '/reject') ?>"
                                   class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all" title="Unapprove">
                                    <i class="fa-solid fa-ban"></i>
                                </a>
                                <?php endif; ?>
                                <button onclick="openDeleteModal(<?= e($review['id']) ?>, '<?= e(addslashes($review['name'] ?? '')) ?>')"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?= \App\Core\View::component('Pagination', [
        'currentPage' => $pagination['currentPage'],
        'totalPages' => $pagination['totalPages'],
        'baseUrl' => url('13091998/reviews?' . http_build_query(array_filter(['status' => $currentStatus, 'search' => $searchQuery]))),
    ]) ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" style="display:none" onclick="if(event.target===this)closeDeleteModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 animate-slideInUp">
        <div class="text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fa-solid fa-trash-can text-red-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Delete Review</h3>
            <p class="text-sm text-gray-500 mt-2">Are you sure you want to delete the review from <strong id="deleteCustomerName" class="text-gray-700"></strong>? This action cannot be undone.</p>
        </div>
        <div class="flex items-center gap-3 mt-6">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
            <a id="deleteConfirmBtn" href="#" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-500 hover:bg-red-600 rounded-xl transition-colors text-center">Delete</a>
        </div>
    </div>
</div>

<?php startSection('scripts') ?>
<script>
function openDeleteModal(id, name) {
    document.getElementById('deleteCustomerName').textContent = name;
    document.getElementById('deleteConfirmBtn').href = '<?= url('13091998/reviews') ?>/' + id + '/delete';
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>
<?php endSection() ?>