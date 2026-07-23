<style>
@keyframes marqueeTop {
  from { transform: translateX(0); }
  to   { transform: translateX(-33.333%); }
}
.marquee-top {
  animation: marqueeTop 18s linear infinite;
  display: flex;
  width: max-content;
}
</style>
<?php
$couponCode = \App\Models\Setting::get('header_coupon_code');
$couponText = \App\Models\Setting::get('header_coupon_text');
$cartCount = $cartCount ?? 0;
$wishlistCount = $wishlistCount ?? 0;
$compareCount = $compareCount ?? 0;
$isLoggedIn = $isLoggedIn ?? false;
$authUser = $authUser ?? null;
$isAdmin = $isLoggedIn && ($authUser['role'] ?? '') === 'admin';
$profileUrl = $isAdmin ? url('13091998') : url('account');
$appName = \App\Models\Setting::get('site_name', 'WeddingYour');
$appTagline = \App\Models\Setting::get('site_tagline', APP_TAGLINE);
$facebookUrl = \App\Models\Setting::get('social_facebook', SOCIAL_FACEBOOK);
$instagramUrl = \App\Models\Setting::get('social_instagram', SOCIAL_INSTAGRAM);
$youtubeUrl = \App\Models\Setting::get('social_youtube', SOCIAL_YOUTUBE);
$cartTotal = \App\Helpers\Session::get('cart.total', 0.00);

$categoriesMenu = [];
$subcategoriesBar = [];
try {
    $pdoCat = \App\Core\Database::getInstance()->getConnection();
    $cats = $pdoCat->query("SELECT id, name, slug FROM sg_categories WHERE status = 1 ORDER BY sort_order, name")->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($cats as $cat) {
        $item = ['id' => $cat['id'], 'title' => $cat['name'], 'url' => '/category/' . $cat['slug'], 'children' => []];
        $subs = $pdoCat->prepare("SELECT id, name, slug, image FROM sg_subcategories WHERE category_id = :cid AND status = 1 ORDER BY sort_order");
        $subs->execute([':cid' => $cat['id']]);
        foreach ($subs as $sub) {
            $item['children'][] = ['id' => $sub['id'], 'title' => $sub['name'], 'url' => '/products?category=' . $cat['slug'] . '&subcategory=' . $sub['slug']];
            $subcategoriesBar[] = [
                'name' => $sub['name'],
                'slug' => $sub['slug'],
                'image' => $sub['image'],
                'url' => '/products?category=' . $cat['slug'] . '&subcategory=' . $sub['slug'],
            ];
        }
        $categoriesMenu[] = $item;
    }
} catch (\Exception $e) {}
?>
<div class="w-full font-sans antialiased">

  <!-- TOP BANNER - Scrolling marquee -->
  <?php if ($couponCode && $couponText): ?>
  <div class="bg-premium-burgundy bg-opacity-5 text-premium-charcoal text-xs md:text-sm font-medium py-2.5 border-b border-premium-warm-gray overflow-hidden">
    <div class="whitespace-nowrap marquee-top">
      <span class="inline-block px-4"><?= e($couponText) ?> <span class="bg-premium-champagne bg-opacity-20 text-premium-cabernet px-2.5 py-0.5 rounded font-mono font-bold"><?= e($couponCode) ?></span></span>
      <span class="inline-block px-4"><?= e($couponText) ?> <span class="bg-premium-champagne bg-opacity-20 text-premium-cabernet px-2.5 py-0.5 rounded font-mono font-bold"><?= e($couponCode) ?></span></span>
      <span class="inline-block px-4"><?= e($couponText) ?> <span class="bg-premium-champagne bg-opacity-20 text-premium-cabernet px-2.5 py-0.5 rounded font-mono font-bold"><?= e($couponCode) ?></span></span>
    </div>
  </div>
  <?php endif; ?>

  <?php $siteLogo = \App\Models\Setting::get('site_logo', '') ?: 'site_logo.png'; ?>

  <!-- HEADER - premium minimal (sticky on scroll) -->
  <header id="main-header" class="w-full bg-premium-ivory border-b border-premium-warm-gray px-5 md:px-10 lg:px-16">
    <div class="max-w-[1440px] mx-auto flex items-center justify-between h-[72px] lg:h-[80px]">

      <!-- Mobile left: hamburger -->
      <button class="p-2 -ml-2 hover:bg-premium-warm-gray rounded-lg transition-colors lg:hidden shrink-0" id="mobile-menu-btn" aria-label="Menu">
        <svg class="w-5 h-5 text-premium-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
      </button>

      <!-- Logo -->
      <a href="<?= url('') ?>" class="flex items-center gap-3 shrink-0">
        <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full overflow-hidden border border-premium-warm-gray flex items-center justify-center bg-white">
          <?php if ($siteLogo): ?>
          <img src="<?= uploadUrl($siteLogo) ?>" alt="<?= e($appName) ?>" class="w-full h-full object-contain">
          <?php else: ?>
          <span class="font-playfair font-bold text-premium-cabernet text-lg lg:text-xl"><?= e(substr($appName, 0, 2)) ?></span>
          <?php endif; ?>
        </div>
        <div class="flex flex-col">
          <h1 class="font-playfair font-bold text-premium-charcoal text-lg lg:text-xl tracking-tight leading-none"><?= e($appName) ?></h1>
          <p class="text-[9px] lg:text-[10px] text-premium-taupe font-medium tracking-wider uppercase mt-0.5"><?= e($appTagline) ?></p>
        </div>
      </a>

      <!-- Search (desktop) -->
      <div class="hidden lg:block flex-1 max-w-[480px] mx-8">
        <form action="<?= url('products') ?>" method="GET" class="search-premium">
          <div class="flex items-center">
            <input type="text" name="q" placeholder="Search products..." value="<?= e($_GET['q'] ?? '') ?>" class="flex-1" id="search-input" autocomplete="off">
            <button type="submit" class="search-btn-premium" aria-label="Search">
              <svg class="w-4 h-4 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
          </div>
          <div id="search-results" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-premium-warm-gray hidden z-50 overflow-hidden"></div>
        </form>
      </div>

      <!-- Right: Icons -->
      <div class="flex items-center gap-1.5 lg:gap-2 shrink-0">
        <!-- Search (mobile) -->
        <button class="p-2 hover:bg-premium-warm-gray rounded-lg transition-colors lg:hidden" onclick="document.getElementById('mobile-search').classList.toggle('hidden');document.getElementById('mobile-search-input').focus()" aria-label="Search">
          <svg class="w-5 h-5 text-premium-dark" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </button>

        <!-- Profile -->
        <a href="<?= $isLoggedIn ? $profileUrl : url('login') ?>" class="icon-btn-premium hidden lg:flex" aria-label="Profile">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/>
          </svg>
        </a>

        <?php if (!$isAdmin): ?>
        <!-- Wishlist -->
        <a href="<?= url('account/wishlist') ?>" class="icon-btn-premium" aria-label="Wishlist">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
          </svg>
          <span class="badge-premium wishlist-count"><?= $wishlistCount ?></span>
        </a>
        <?php endif; ?>

        <!-- Compare -->
        <a href="<?= url('compare') ?>" class="icon-btn-premium hidden lg:flex" aria-label="Compare">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
          </svg>
          <span class="badge-premium compare-count"><?= $compareCount ?></span>
        </a>

        <!-- Cart -->
        <a href="<?= url('cart') ?>" class="relative flex items-center gap-2 py-2 pl-1 pr-3 hover:bg-premium-warm-gray rounded-lg transition-colors" aria-label="Cart">
          <span class="relative w-[40px] h-[40px] flex items-center justify-center flex-shrink-0 rounded-full">
            <svg class="w-5 h-5 text-premium-dark" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
            </svg>
            <span class="badge-premium cart-count"><?= $cartCount ?></span>
          </span>
          <span class="cart-total text-xs font-semibold text-premium-charcoal tracking-tight hidden sm:inline"><?= APP_CURRENCY ?> <?= number_format($cartTotal, 2) ?></span>
        </a>
      </div>
    </div>

    <!-- Mobile search (hidden by default) -->
    <div id="mobile-search" class="hidden pb-4 lg:hidden">
      <form action="<?= url('products') ?>" method="GET" class="search-premium">
        <div class="flex items-center">
          <input type="text" name="q" id="mobile-search-input" placeholder="Search products..." value="<?= e($_GET['q'] ?? '') ?>" class="flex-1">
          <button type="submit" class="search-btn-premium" aria-label="Search">
            <svg class="w-4 h-4 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </button>
        </div>
      </form>
    </div>
  </header>
  <div id="header-spacer" class="hidden"></div>

  <!-- DESKTOP NAVIGATION -->
  <nav id="desktop-nav" class="w-full bg-white border-b border-premium-warm-gray hidden lg:block relative">
    <div class="max-w-[1440px] mx-auto flex items-center justify-between px-8">

      <!-- Browse Categories Dropdown -->
      <div class="relative group z-50 shrink-0">
        <button class="category-trigger">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
          </svg>
          <span>Browse Categories</span>
          <svg class="w-3.5 h-3.5 ml-2 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path>
          </svg>
        </button>

        <div class="absolute left-0 w-64 dropdown-panel opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[60]">
          <ul class="py-2">
            <?php foreach ($categoriesMenu as $parent): ?>
            <li class="relative group/sub">
              <?php if (!empty($parent['children'])): ?>
              <a href="<?= url(ltrim($parent['url'], '/')) ?>" class="dropdown-item flex items-center justify-between">
                <span><?= e($parent['title']) ?></span>
                <svg class="w-3 h-3 text-premium-taupe group-hover/sub:text-premium-cabernet" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                </svg>
              </a>
              <div class="absolute left-full top-0 w-56 bg-white border border-premium-warm-gray shadow-2xl py-2 opacity-0 invisible group-hover/sub:opacity-100 group-hover/sub:visible transition-all duration-200 z-[70] rounded-r-lg">
                <ul>
                  <?php foreach ($parent['children'] as $child): ?>
                  <li><a href="<?= url(ltrim($child['url'], '/')) ?>" class="block px-5 py-2.5 text-sm font-medium text-premium-mink hover:text-premium-crimson hover:bg-premium-ivory transition-colors"><?= e($child['title']) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <?php else: ?>
              <a href="<?= url(ltrim($parent['url'], '/')) ?>" class="dropdown-item"><?= e($parent['title']) ?></a>
              <?php endif; ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <!-- Page Links -->
      <div class="flex items-center gap-1">
        <a href="<?= url('') ?>" class="nav-link px-4 <?= ($_SERVER['REQUEST_URI'] ?? '/') === '/' ? 'active' : '' ?>">Home</a>
        <a href="<?= url('about') ?>" class="nav-link px-4">About Us</a>
        <a href="<?= url('contact') ?>" class="nav-link px-4">Contact</a>
        <a href="<?= url('blog') ?>" class="nav-link px-4">Blog</a>
      </div>

      <!-- Social Icons -->
      <div class="flex items-center gap-2">
        <a href="<?= e($facebookUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full flex items-center justify-center text-premium-taupe hover:text-premium-burgundy hover:bg-premium-blush transition-all" title="Facebook">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1V12h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/></svg>
        </a>
        <a href="<?= e($instagramUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full flex items-center justify-center text-premium-taupe hover:text-premium-burgundy hover:bg-premium-blush transition-all" title="Instagram">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
        </a>
        <a href="<?= e($youtubeUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full flex items-center justify-center text-premium-taupe hover:text-premium-burgundy hover:bg-premium-blush transition-all" title="YouTube">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.517 3.545 12 3.545 12 3.545s-7.517 0-9.388.508a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.871.508 9.388.508 9.388.508s7.517 0 9.388-.508a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
        </a>
      </div>

    </div>
  </nav>

  <!-- MOBILE SIDE MENU -->
  <div class="mobile-menu fixed inset-0 z-[100] lg:hidden">
    <div class="mobile-overlay absolute inset-0 bg-black/50 opacity-0 transition-opacity duration-300"></div>
    <div class="mobile-panel absolute top-0 left-0 bottom-0 w-[300px] max-w-[85vw] bg-white shadow-2xl overflow-y-auto">
      <div class="sticky top-0 bg-white z-10 flex items-center justify-between px-4 py-3 border-b border-premium-warm-gray">
        <span class="text-sm font-black text-premium-charcoal uppercase tracking-wide">Menu</span>
        <button class="p-2 hover:bg-premium-warm-gray rounded-lg transition-colors" aria-label="Close" onclick="document.querySelector('.mobile-menu').classList.remove('is-open')">
          <svg class="w-5 h-5 text-premium-mink" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <div class="py-2">
        <a href="<?= url('') ?>" class="flex items-center gap-3 px-5 py-3 text-sm font-bold text-premium-charcoal hover:bg-premium-blush hover:text-premium-burgundy transition-colors border-b border-premium-warm-gray">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          Home
        </a>

        <?php foreach ($categoriesMenu as $parent): ?>
        <div class="border-b border-premium-warm-gray">
          <?php if (!empty($parent['children'])): ?>
          <button onclick="this.nextElementSibling.classList.toggle('hidden');this.querySelector('svg').classList.toggle('rotate-90')" class="flex items-center justify-between w-full px-5 py-3 text-sm font-bold text-premium-charcoal hover:bg-premium-blush hover:text-premium-burgundy transition-colors">
            <span><?= e($parent['title']) ?></span>
            <svg class="w-3.5 h-3.5 text-premium-taupe transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
          <div class="hidden bg-premium-ivory">
            <?php foreach ($parent['children'] as $child): ?>
            <a href="<?= url(ltrim($child['url'], '/')) ?>" class="block px-8 py-2.5 text-xs font-medium text-premium-mink hover:text-premium-burgundy hover:bg-white transition-colors"><?= e($child['title']) ?></a>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <a href="<?= url(ltrim($parent['url'], '/')) ?>" class="flex items-center gap-3 px-5 py-3 text-sm font-bold text-premium-charcoal hover:bg-premium-blush hover:text-premium-burgundy transition-colors">
            <?= e($parent['title']) ?>
          </a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <a href="<?= url('blog') ?>" class="flex items-center gap-3 px-5 py-3 text-sm font-bold text-premium-charcoal hover:bg-premium-blush hover:text-premium-burgundy transition-colors border-b border-premium-warm-gray">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
          Our Blogs
        </a>

        <a href="<?= url('contact') ?>" class="flex items-center gap-3 px-5 py-3 text-sm font-bold text-premium-charcoal hover:bg-premium-blush hover:text-premium-burgundy transition-colors border-b border-premium-warm-gray">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          Our Contacts
        </a>

        <?php if ($isAdmin): ?>
        <a href="<?= url('13091998') ?>" class="flex items-center gap-3 px-5 py-3 text-sm font-bold text-premium-charcoal hover:bg-premium-blush hover:text-premium-burgundy transition-colors border-b border-premium-warm-gray">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
          Admin Dashboard
        </a>
        <?php else: ?>
        <a href="<?= url('account/wishlist') ?>" class="flex items-center gap-3 px-5 py-3 text-sm font-bold text-premium-charcoal hover:bg-premium-blush hover:text-premium-burgundy transition-colors border-b border-premium-warm-gray">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          Wishlist
        </a>
        <?php endif; ?>

        <a href="<?= url('compare') ?>" class="flex items-center gap-3 px-5 py-3 text-sm font-bold text-premium-charcoal hover:bg-premium-blush hover:text-premium-burgundy transition-colors border-b border-premium-warm-gray">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          Compare
        </a>

        <a href="<?= $isLoggedIn ? $profileUrl : url('login') ?>" class="flex items-center gap-3 px-5 py-3 text-sm font-bold text-premium-burgundy hover:bg-premium-blush transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <?= $isAdmin ? 'Admin Dashboard' : ($isLoggedIn ? 'My Account' : 'Login / Register') ?>
        </a>
      </div>
    </div>
  </div>

  <style>
  .mobile-menu { pointer-events: none; visibility: hidden; }
  .mobile-menu.is-open { pointer-events: all; visibility: visible; }
  .mobile-menu .mobile-overlay { opacity: 0; transition: opacity 0.3s ease; }
  .mobile-menu.is-open .mobile-overlay { opacity: 1; }
  .mobile-menu .mobile-panel { transform: translateX(-100%); transition: transform 0.3s ease; }
  .mobile-menu.is-open .mobile-panel { transform: translateX(0); }
  .rotate-90 { transform: rotate(90deg) !important; }
  </style>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('mobile-menu-btn');
    var menu = document.querySelector('.mobile-menu');
    if (!btn || !menu) return;
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      menu.classList.toggle('is-open');
      document.body.classList.toggle('overflow-hidden');
    });
    var overlay = menu.querySelector('.mobile-overlay');
    if (overlay) {
      overlay.addEventListener('click', function() {
        menu.classList.remove('is-open');
        document.body.classList.remove('overflow-hidden');
      });
    }

    // Sticky header + desktop nav scroll hide/show
    var nav = document.getElementById('desktop-nav');
    var headerEl = document.getElementById('main-header');
    var spacer = document.getElementById('header-spacer');
    var topBanner = document.querySelector('.marquee-top') ? document.querySelector('.marquee-top').closest('div') : null;
    var lastScroll = 0;
    var navHeight = nav ? nav.offsetHeight : 0;
    var ticking = false;
    var headerSticky = false;

    if (nav) {
      nav.style.transition = 'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.45s cubic-bezier(0.4, 0, 0.2, 1)';
    }
    if (headerEl) {
      headerEl.style.transition = 'box-shadow 0.3s ease';
    }

    function updateHeaderPosition() {
      if (!headerEl) return;
      var currentScroll = window.pageYOffset || document.documentElement.scrollTop;
      var bannerH = topBanner ? topBanner.offsetHeight : 0;
      var threshold = bannerH + 20;
      var headerH = headerEl.offsetHeight;

      if (currentScroll > threshold && !headerSticky) {
        headerSticky = true;
        headerEl.style.position = 'fixed';
        headerEl.style.top = '0';
        headerEl.style.left = '0';
        headerEl.style.right = '0';
        headerEl.style.zIndex = '60';
        headerEl.style.boxShadow = '0 4px 16px rgba(0,0,0,0.08)';
        if (spacer) {
          spacer.style.display = 'block';
          spacer.style.height = headerH + 'px';
        }
      } else if (currentScroll <= threshold && headerSticky) {
        headerSticky = false;
        headerEl.style.position = '';
        headerEl.style.top = '';
        headerEl.style.left = '';
        headerEl.style.right = '';
        headerEl.style.zIndex = '';
        headerEl.style.boxShadow = 'none';
        if (spacer) {
          spacer.style.display = 'none';
          spacer.style.height = '0';
        }
      }
    }

    window.addEventListener('scroll', function() {
      if (!ticking) {
        window.requestAnimationFrame(function() {
          var currentScroll = window.pageYOffset || document.documentElement.scrollTop;
          var headerH = headerEl ? headerEl.offsetHeight : 80;

          // Update header sticky state
          updateHeaderPosition();

          // Re-get header height after potential sticky change
          headerH = headerEl ? headerEl.offsetHeight : 80;

          // Desktop nav show/hide
          if (nav && navHeight > 0) {
            if (currentScroll > navHeight) {
              if (currentScroll > lastScroll) {
                if (nav.style.transform !== 'translateY(-100%)') {
                  nav.style.transform = 'translateY(-100%)';
                  nav.style.boxShadow = 'none';
                }
              } else {
                nav.style.position = 'fixed';
                nav.style.top = headerH + 'px';
                nav.style.left = '0';
                nav.style.right = '0';
                nav.style.zIndex = '50';
                nav.style.transform = 'translateY(0)';
                nav.style.boxShadow = '0 4px 16px rgba(0,0,0,0.12)';
              }
            } else {
              nav.style.transform = 'translateY(0)';
              nav.style.position = '';
              nav.style.top = '';
              nav.style.left = '';
              nav.style.right = '';
              nav.style.zIndex = '';
              nav.style.boxShadow = '';
            }
          }

          lastScroll = currentScroll;
          ticking = false;
        });
        ticking = true;
      }
    });
  });

  </script>
</div>
