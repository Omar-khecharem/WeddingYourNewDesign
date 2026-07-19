<?php
// Include header
require __DIR__ . DS . 'header.php';

// Navbar
echo \App\Core\View::include('layouts.navbar', [
    'cartCount' => $cartCount ?? 0,
    'wishlistCount' => $wishlistCount ?? 0,
    'compareCount' => $compareCount ?? 0,
    'isLoggedIn' => $isLoggedIn ?? false,
    'authUser' => $authUser ?? null,
    'appName' => $appName ?? APP_NAME,
    'appTagline' => $appTagline ?? '',
    'facebookUrl' => $facebookUrl ?? '#',
    'instagramUrl' => $instagramUrl ?? '#',
    'youtubeUrl' => $youtubeUrl ?? '#',
]);
?>

<?= \App\Components\Alert::renderFlash() ?>

<div class="max-w-[1200px] mx-auto my-4 sm:my-8 px-4">
    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

        <?php
        $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $links = [
            ['label' => 'Dashboard', 'url' => url('account'), 'icon' => 'fa-gauge-high'],
            ['label' => 'My Profile', 'url' => url('account/profile'), 'icon' => 'fa-user'],
            ['label' => 'My Orders', 'url' => url('account/orders'), 'icon' => 'fa-box'],
            ['label' => 'My Wishlist', 'url' => url('account/wishlist'), 'icon' => 'fa-heart'],
            ['label' => 'My Addresses', 'url' => url('account/addresses'), 'icon' => 'fa-location-dot'],
            ['label' => 'Password', 'url' => url('account/change-password'), 'icon' => 'fa-lock'],
            ['label' => 'Logout', 'url' => url('logout'), 'icon' => 'fa-right-from-bracket'],
        ];
        ?>

        <!-- Mobile nav -->
        <div class="lg:hidden -mx-4 px-4 overflow-x-auto scrollbar-hide">
            <div class="flex gap-2 pb-2 min-w-max">
                <?php foreach ($links as $link):
                    $active = strpos($currentUri, $link['url']) === 0;
                    $isLogout = $link['label'] === 'Logout';
                ?>
                <a href="<?= $link['url'] ?>"
                   class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-all
                           <?= $active ? 'bg-premium-burgundy/10 text-premium-burgundy font-bold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>
                          <?= $isLogout ? 'text-red-500 hover:bg-red-50' : '' ?>">
                    <i class="fa-solid <?= $link['icon'] ?>"></i>
                    <span><?= $link['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="hidden lg:block lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden sticky top-28">
                <div class="bg-premium-burgundy p-5 text-white text-center">
                    <div class="w-14 h-14 rounded-full bg-white/20 mx-auto flex items-center justify-center text-xl font-bold mb-2">
                        <?= e(strtoupper(mb_substr($authUser['name'] ?? 'U', 0, 1))) ?>
                    </div>
                    <h2 class="font-bold text-sm"><?= e($authUser['name'] ?? '') ?></h2>
                    <p class="text-xs text-white/80 mt-0.5"><?= e($authUser['email'] ?? '') ?></p>
                </div>
                <nav class="p-3 space-y-0.5">
                    <?php foreach ($links as $link):
                        $active = strpos($currentUri, $link['url']) === 0;
                        $isLogout = $link['label'] === 'Logout';
                    ?>
                    <a href="<?= $link['url'] ?>"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all
                               <?= $active ? 'bg-premium-burgundy/10 text-premium-burgundy font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>
                              <?= $isLogout ? 'mt-3 border-t border-gray-100 pt-3.5 text-red-500 hover:text-red-600' : '' ?>">
                        <i class="fa-solid <?= $link['icon'] ?> w-4 text-center text-xs"></i>
                        <span><?= $link['label'] ?></span>
                    </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 min-w-0">
            <?php
            echo $content ?? '';
            echo \App\Core\View::section('content');
            ?>
        </div>

    </div>
</div>

<?php
echo \App\Core\View::include('layouts.footer', [
    'appName' => $appName ?? APP_NAME,
    'contactEmail' => $contactEmail ?? CONTACT_EMAIL,
    'contactPhone' => $contactPhone ?? CONTACT_PHONE,
    'contactAddress' => $contactAddress ?? CONTACT_ADDRESS,
    'whatsappLink' => $whatsappLink ?? WHATSAPP_LINK,
    'currentYear' => $currentYear ?? date('Y'),
]);
?>
