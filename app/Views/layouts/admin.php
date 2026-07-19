<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? 'Admin - ' . \App\Models\Setting::get('site_name', 'WeddingYour')) ?></title>
    <?php $adminFavicon = \App\Models\Setting::get('site_favicon', ''); if (empty($adminFavicon) && file_exists(PUBLIC_DIR . DS . 'uploads' . DS . 'site_favicon.png')) $adminFavicon = 'site_favicon.png'; ?>
    <?php if ($adminFavicon): ?>
    <link rel="icon" href="<?= uploadUrl($adminFavicon) ?>">
    <?php else: ?>
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-red': '#B8845A',
                        'accent-orange': '#9E6F46',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { scrollbar-width: thin; scrollbar-color: #d1d5db transparent; }
        *::-webkit-scrollbar { width: 5px; }
        *::-webkit-scrollbar-track { background: transparent; }
        *::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .sidebar-link { transition: all 0.2s ease; position: relative; }
        .sidebar-link:hover { transform: translateX(3px); }
        .sidebar-link.active::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 3px; height: 60%; border-radius: 0 3px 3px 0; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 12px 30px -8px rgba(0,0,0,0.12); }
        .sidebar-overlay { transition: opacity 0.3s ease; }
        .sidebar-panel { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideInUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .animate-slideInRight { animation: slideInRight 0.3s ease-out; }
        .animate-slideInUp { animation: slideInUp 0.3s ease-out; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" x-init="() => { const mq = window.matchMedia('(min-width: 1024px)'); mq.addEventListener('change', e => sidebarOpen = e.matches); window.addEventListener('resize', () => { if(window.innerWidth >= 1024) sidebarOpen = true; }); }" class="flex h-screen overflow-hidden">

        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-20 lg:hidden sidebar-overlay"></div>

        <aside x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 flex-shrink-0 overflow-y-auto sidebar-panel lg:translate-x-0">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <?php $adminLogo = \App\Models\Setting::get('site_logo', ''); if ($adminLogo && !file_exists(PUBLIC_DIR . DS . 'uploads' . DS . $adminLogo)) $adminLogo = ''; if (empty($adminLogo) && file_exists(PUBLIC_DIR . DS . 'uploads' . DS . 'site_logo.png')) $adminLogo = 'site_logo.png'; ?>
                        <?php if ($adminLogo): ?>
                        <img src="<?= uploadUrl($adminLogo) ?>" alt="Logo" class="h-8 w-auto object-contain">
                        <?php else: ?>
                        <span class="font-bold text-primary-red text-lg"><?= e(substr(\App\Models\Setting::get('site_name', 'WeddingYour'), 0, 2)) ?></span>
                        <?php endif; ?>
                        <span class="font-semibold text-gray-800 text-sm"><?= e(\App\Models\Setting::get('site_name', 'WeddingYour')) ?></span>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400">
                <?php if ($authUser ?? null): ?>
                <div class="mt-4 flex items-center gap-3 px-3 py-2 bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-red to-red-500 flex items-center justify-center text-white font-bold text-xs uppercase shadow-sm"><?= e(substr($authUser['name'] ?? 'Admin', 0, 2)) ?></div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-gray-800 truncate"><?= e($authUser['name'] ?? 'Admin') ?></p>
                        <p class="text-[10px] text-gray-400 truncate"><?= e($authUser['email'] ?? '') ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <nav class="p-3 space-y-0.5">
                <?php
                $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
                $basePath = rtrim(dirname(parse_url(url('13091998'), PHP_URL_PATH)), '/');
                $isActive = function($path) use ($currentUri, $basePath) {
                    if ($path === $basePath . '/13091998') return $currentUri === $basePath . '/13091998';
                    return strpos($currentUri, $path) === 0;
                };
                $pdo = \App\Core\Database::getInstance()->getConnection();
                $viewedAt = \App\Helpers\Session::get('forgot_requests_viewed_at', 0);
                $pendingResetStmt = $pdo->prepare("SELECT COUNT(*) FROM sg_password_resets WHERE admin_request = 1 AND used = 0 AND created_at > :viewed");
                $pendingResetStmt->execute([':viewed' => $viewedAt ? date('Y-m-d H:i:s', $viewedAt) : '1970-01-01 00:00:00']);
                $pendingResetCount = (int)$pendingResetStmt->fetchColumn();

                $contactsViewedAt = \App\Helpers\Session::get('contacts_viewed_at', 0);
                $unreadContactsStmt = $pdo->prepare("SELECT COUNT(*) FROM sg_contact_messages WHERE is_read = 0 AND created_at > :viewed");
                $unreadContactsStmt->execute([':viewed' => $contactsViewedAt ? date('Y-m-d H:i:s', $contactsViewedAt) : '1970-01-01 00:00:00']);
                $unreadContactsCount = (int)$unreadContactsStmt->fetchColumn();
                $navItems = [
                    ['url' => url('13091998'), 'label' => 'Dashboard', 'icon' => 'fa-chart-pie'],
                    ['url' => url('13091998/products'), 'label' => 'Products', 'icon' => 'fa-box'],
                    ['url' => url('13091998/categories'), 'label' => 'Categories', 'icon' => 'fa-folder'],
                    ['url' => url('13091998/orders'), 'label' => 'Orders', 'icon' => 'fa-truck'],
                    ['url' => url('13091998/users'), 'label' => 'Users', 'icon' => 'fa-users'],
                    ['url' => url('13091998/reviews'), 'label' => 'Reviews', 'icon' => 'fa-star'],
                    ['url' => url('13091998/coupons'), 'label' => 'Coupons', 'icon' => 'fa-tag'],
                    ['url' => url('13091998/pages'), 'label' => 'Pages', 'icon' => 'fa-file-lines'],
                    ['url' => url('13091998/blog'), 'label' => 'Blog', 'icon' => 'fa-newspaper'],
                    ['url' => url('13091998/contacts'), 'label' => 'Contact Messages', 'icon' => 'fa-envelope', 'badge' => $unreadContactsCount],
                    ['url' => url('13091998/forgot-password-requests'), 'label' => 'Password Reset' . ($pendingResetCount > 0 ? ' (' . $pendingResetCount . ')' : ''), 'icon' => 'fa-key', 'badge' => $pendingResetCount],

                    ['url' => url('13091998/banners'), 'label' => 'Banners', 'icon' => 'fa-images'],
                    ['url' => url('13091998/deals'), 'label' => 'Best Deals', 'icon' => 'fa-percent'],
                    ['url' => url('13091998/category-cards'), 'label' => 'Section Cards', 'icon' => 'fa-layer-group'],
                    ['url' => url('13091998/gallery'), 'label' => 'Gallery', 'icon' => 'fa-camera'],

                ];
                ?>
                <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">Management</div>
                <?php foreach ($navItems as $item): $active = $isActive($item['url']); ?>
                    <a href="<?= $item['url'] ?>"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all <?= $active ? 'bg-primary-red/10 text-primary-red font-semibold active' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800' ?>"
                       @click="if(window.innerWidth < 1024) sidebarOpen = false">
                        <i class="fa-solid <?= $item['icon'] ?> w-5 text-center text-xs <?= $active ? 'text-primary-red' : 'text-gray-400' ?>"></i>
                        <span class="flex-1"><?= $item['label'] ?></span>
                        <?php if (!empty($item['badge'])): ?>
                        <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center"><?= $item['badge'] > 99 ? '99+' : $item['badge'] ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
                <hr class="my-3 border-gray-100">
                <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">System</div>
                <a href="<?= url('13091998/settings') ?>"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all <?= $isActive(url('13091998/settings')) ? 'bg-primary-red/10 text-primary-red font-semibold active' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800' ?>"
                   @click="if(window.innerWidth < 1024) sidebarOpen = false">
                    <i class="fa-solid fa-gear w-5 text-center text-xs <?= $isActive(url('13091998/settings')) ? 'text-primary-red' : 'text-gray-400' ?>"></i> Settings
                </a>
                <a href="<?= url('13091998/logs') ?>"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all <?= $isActive(url('13091998/logs')) ? 'bg-primary-red/10 text-primary-red font-semibold active' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800' ?>"
                   @click="if(window.innerWidth < 1024) sidebarOpen = false">
                    <i class="fa-solid fa-list w-5 text-center text-xs <?= $isActive(url('13091998/logs')) ? 'text-primary-red' : 'text-gray-400' ?>"></i> Logs
                </a>
                <hr class="my-3 border-gray-100">
                <a href="<?= url('logout') ?>"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 hover:text-red-700 transition-all"
                   @click="if(window.innerWidth < 1024) sidebarOpen = false">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center text-xs text-red-400"></i> Logout
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-4 lg:px-6 py-3 flex items-center justify-between flex-shrink-0 sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-500 transition-all -ml-1.5">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div class="hidden sm:flex items-center gap-2 text-sm text-gray-400">
                        <a href="<?= url('/') ?>" target="_blank" class="hover:text-primary-red transition-colors"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Visit Site</a>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?= url('13091998/logs') ?>" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-all" title="System Logs">
                        <i class="fa-solid fa-terminal text-sm"></i>
                    </a>
                    <a href="<?= url('13091998/settings') ?>" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-all" title="Settings">
                        <i class="fa-solid fa-sliders text-sm"></i>
                    </a>
                    <div class="h-6 w-px bg-gray-200 mx-1"></div>
<div class="flex items-center gap-2 pl-1">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-red to-red-500 flex items-center justify-center text-white font-bold text-xs uppercase shadow-sm"><?= e(substr(($authUser['name'] ?? 'A'), 0, 2)) ?></div>
                        <span class="hidden sm:block text-sm font-medium text-gray-700"><?= e($authUser['name'] ?? 'Admin') ?></span>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto bg-gray-50/80">
                <div class="max-w-[1440px] mx-auto py-6 px-4 lg:px-6">
                    <?= \App\Components\Alert::renderFlash() ?>
                    <?= $content ?? '' ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>if (location.hash) setTimeout(() => document.getElementById(location.hash.slice(1))?.scrollIntoView({ behavior: 'smooth' }), 100);</script>
    <?= \App\Components\Modal::renderScripts() ?>
    <?= section('scripts') ?>
    <?php if (isset($exceptionModal)): ?>
    <?= $exceptionModal ?>
    <script>document.addEventListener('DOMContentLoaded', function() { var m = document.querySelector('[role="dialog"]'); if(m) openModal(m.id); });</script>
    <?php endif; ?>
</body>
</html>
