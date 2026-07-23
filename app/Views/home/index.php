<?php
$categories = $categories ?? [];
$featuredProducts = $featuredProducts ?? [];
$trendingProducts = $trendingProducts ?? [];
$recentProducts = $recentProducts ?? [];
$reviews = $reviews ?? [];
$banners = $banners ?? [];
$deals = $deals ?? [];
$categoryCards = $categoryCards ?? [];
$categoryCardProducts = $categoryCardProducts ?? [];
$defaultCardFallback = $defaultCardFallback ?? [];
$galleryItems = $galleryItems ?? [];
$whatsappNumber = $whatsappNumber ?? '+919830136355';
$videoShowcaseBg = $videoShowcaseBg ?? '';
$heroSubtitle = $heroSubtitle ?? '';
$heroButtonText = $heroButtonText ?? 'Shop Now';
$heroButtonLink = $heroButtonLink ?? '';
?>
<style>
  [data-a] { opacity: 0; will-change: transform, opacity; }
  [data-a].a-done { opacity: 1; }
  .a-fade-up { transform: translateY(40px); }
  .a-fade-up.a-done { transform: translateY(0); transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
  .a-fade-down { transform: translateY(-30px); }
  .a-fade-down.a-done { transform: translateY(0); transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1); }
  .a-fade-left { transform: translateX(-40px); }
  .a-fade-left.a-done { transform: translateX(0); transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
  .a-fade-right { transform: translateX(40px); }
  .a-fade-right.a-done { transform: translateX(0); transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
  .a-scale { transform: scale(0.92); }
  .a-scale.a-done { transform: scale(1); transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1); }
  .a-stagger > * { opacity: 0; transform: translateY(25px); will-change: transform, opacity; }
  .a-stagger.a-done > * { opacity: 1; transform: translateY(0); }
  .a-stagger.a-done > *:nth-child(1) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0s; }
  .a-stagger.a-done > *:nth-child(2) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.08s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.08s; }
  .a-stagger.a-done > *:nth-child(3) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.16s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.16s; }
  .a-stagger.a-done > *:nth-child(4) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.24s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.24s; }
  .a-stagger.a-done > *:nth-child(5) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.32s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.32s; }
  .a-stagger.a-done > *:nth-child(6) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.4s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.4s; }
  .a-stagger.a-done > *:nth-child(7) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.48s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.48s; }
  .a-stagger.a-done > *:nth-child(8) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.56s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.56s; }
  .a-stagger.a-done > *:nth-child(9) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.64s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.64s; }
  .a-stagger.a-done > *:nth-child(10) { transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.72s, opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.72s; }
  @keyframes heroIn { 0% { opacity: 0; transform: scale(1.08); } 100% { opacity: 1; transform: scale(1); } }
  .hero-img { animation: heroIn 1.2s cubic-bezier(0.16, 1, 0.3, 1) both; }
  @keyframes heroContent { 0% { opacity: 0; transform: translateY(30px); } 100% { opacity: 1; transform: translateY(0); } }
  .hero-content > * { animation: heroContent 0.8s cubic-bezier(0.16, 1, 0.3, 1) both; }
  .hero-content > *:nth-child(1) { animation-delay: 0.1s; }
  .hero-content > *:nth-child(2) { animation-delay: 0.2s; }
  .hero-content > *:nth-child(3) { animation-delay: 0.3s; }
  .hero-content > *:nth-child(4) { animation-delay: 0.4s; }
  .hero-content > *:nth-child(5) { animation-delay: 0.5s; }
</style>
<div class="min-h-screen bg-premium-ivory font-sans text-premium-charcoal antialiased selection:bg-premium-burgundy selection:text-white">

  <main class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">

    <!-- ============================================================ -->
    <!-- SUBCATEGORIES STRIP                                           -->
    <!-- ============================================================ -->
    <?php $subList = $subcategories; if (empty($subList)): $subList = [['slug'=>'bridal-patashi-mukut','name'=>'Bridal Patashi','cat_slug'=>'bride'],['slug'=>'sithi-small-mukut','name'=>'Sithi Mukut','cat_slug'=>'bride'],['slug'=>'topor-mukut-set','name'=>'Topor Set','cat_slug'=>'groom'],['slug'=>'baby-topor-boy','name'=>'Baby Topor','cat_slug'=>'baby'],['slug'=>'groom-dorpon','name'=>'Dorpon','cat_slug'=>'groom'],['slug'=>'matha-patti','name'=>'Matha Patti','cat_slug'=>'sholas-jewellery'],['slug'=>'wedding-panpata','name'=>'Panpata','cat_slug'=>'wedding-items'],['slug'=>'mini-crown','name'=>'Mini Crown','cat_slug'=>'sholas-jewellery']]; endif; ?>
    <section class="py-6 md:py-8 overflow-hidden select-none" id="subcatSectionHome" data-a="fade-up">
      <div id="subcatScrollHome" class="flex gap-3 md:gap-5 overflow-x-auto pb-3 scrollbar-hide" style="cursor:grab; scrollbar-width:none; -ms-overflow-style:none; user-select:none; -webkit-user-select:none; -webkit-overflow-scrolling:touch;">
        <?php foreach ($subList as $sub): ?>
        <?php $subSlug = is_array($sub) ? ($sub['slug'] ?? '') : $sub; ?>
        <?php $subName = is_array($sub) ? ($sub['name'] ?? '') : $sub; ?>
        <?php $subImg = is_array($sub) ? ($sub['image'] ?? '') : ''; ?>
        <?php $subCatSlug = is_array($sub) ? ($sub['cat_slug'] ?? '') : ''; ?>
        <a href="<?= url('products?category=' . e($subCatSlug) . '&subcategory=' . e($subSlug)) ?>" draggable="false" ondragstart="return false" class="flex flex-col items-center text-center space-y-2.5 w-[85px] min-[400px]:w-[95px] sm:w-[105px] md:w-[115px] shrink-0 group">
          <div class="w-full aspect-square rounded-2xl overflow-hidden border-2 border-premium-warm-gray group-hover:border-premium-champagne group-hover:scale-[1.04] transition-all duration-400 shadow-md group-hover:shadow-xl">
            <?php if ($subImg): ?>
            <img src="<?= uploadUrl($subImg, 'categories') ?>" alt="<?= e($subName) ?>" class="w-full h-full object-cover" loading="lazy" draggable="false">
            <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-premium-blush to-premium-cream flex items-center justify-center font-bold text-premium-champagne"><?= e(substr($subName, 0, 2)) ?></div>
            <?php endif; ?>
          </div>
          <span class="text-[10px] min-[400px]:text-[11px] sm:text-xs font-semibold leading-tight text-premium-mink group-hover:text-premium-burgundy transition-colors"><?= e($subName) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- ============================================================ -->
    <!-- HERO SECTION - Left Image + Right Carousel                   -->
    <!-- ============================================================ -->
    <?php
      $heroLeftBanners = array_filter($banners, fn($b) => ($b['position'] ?? '') === 'hero_left');
      $heroRightBanners = array_values(array_filter($banners, fn($b) => ($b['position'] ?? '') === 'hero_right'));
      $heroLeft = $heroLeftBanners ? current($heroLeftBanners) : null;
      $heroLeftImg = $heroLeft['image'] ?? '';
      $heroTitle = $heroLeft['title'] ?? 'Where Heritage Meets<br><span class="text-premium-champagne">Elegance</span>';
      $heroDesc = $heroDesc ?? ($heroLeft['description'] ?? 'Discover handcrafted Bengali wedding treasures, made by master artisans.');
      $heroButtonLink = $heroButtonLink ?: ($heroLeft['link'] ?? url('products'));
    ?>
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-0 lg:gap-6 mb-8">
      <!-- Left Main Image -->
      <div class="lg:col-span-7 relative overflow-hidden rounded-2xl bg-premium-charcoal min-h-[320px] sm:min-h-[400px] lg:min-h-[480px] group hero-img">
        <?php if ($heroLeftImg): ?>
        <img src="<?= uploadUrl($heroLeftImg) ?>" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700 hero-img">
        <?php else: ?>
        <div class="absolute inset-0 bg-gradient-to-br from-premium-burgundy to-premium-cabernet"></div>
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=60 height=60 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M30 0L60 30L30 60L0 30Z%22 fill=%22white%22 opacity=%220.1%22/%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8 lg:p-10 hero-content">
          <span class="inline-block bg-premium-champagne text-premium-charcoal text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest mb-3">WeddingYour Collection</span>
          <?php if ($heroSubtitle): ?>
          <p class="text-premium-champagne/90 text-xs sm:text-sm font-semibold tracking-widest uppercase mb-2"><?= e($heroSubtitle) ?></p>
          <?php endif; ?>
          <h1 class="font-playfair text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-bold text-white leading-tight max-w-xl"><?= $heroTitle ?></h1>
          <p class="text-white/70 text-sm sm:text-base mt-3 max-w-md leading-relaxed"><?= e($heroDesc) ?></p>
          <a href="<?= e($heroButtonLink) ?>" class="inline-flex items-center gap-2 mt-5 bg-premium-champagne hover:bg-premium-gold text-premium-charcoal font-bold text-xs sm:text-sm px-6 sm:px-8 py-3 sm:py-3.5 rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-premium-champagne/25">
            <?= e($heroButtonText) ?>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
          </a>
        </div>
      </div>

      <!-- Right Carousel -->
      <div class="lg:col-span-5 mt-3 lg:mt-0 relative" x-data="heroCarousel()">
        <?php if (!empty($heroRightBanners)): ?>
        <div class="relative overflow-hidden rounded-2xl min-h-[320px] sm:min-h-[400px] lg:min-h-[480px]">
          <?php foreach ($heroRightBanners as $i => $hb):
            $hbTitle = $hb['title'] ?? 'Collection';
            $hbDesc = $hb['description'] ?? '';
            $hbLink = $hb['link'] ?? url('products');
            $hbImg = $hb['image'] ?? '';
          ?>
          <div x-show="slide === <?= $i ?>" x-cloak x-transition:enter="transition-all duration-700" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition-all duration-500" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute inset-0 rounded-2xl overflow-hidden <?= $hbImg ? '' : 'bg-gradient-to-br from-premium-burgundy to-premium-navy' ?>">
            <?php if ($hbImg): ?>
            <img src="<?= uploadUrl($hbImg) ?>" alt="" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
            <?php endif; ?>
            <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8 z-10">
              <p class="font-playfair text-white text-xl sm:text-2xl font-bold leading-snug"><?= nl2br(e($hbTitle)) ?></p>
              <?php if ($hbDesc): ?>
              <p class="text-white/60 text-xs sm:text-sm mt-1 max-w-xs"><?= e($hbDesc) ?></p>
              <?php endif; ?>
              <a href="<?= e($hbLink) ?>" class="mt-3 inline-flex items-center gap-1.5 text-premium-champagne text-xs font-bold tracking-wider uppercase hover:gap-2.5 transition-all">
                Shop Now <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
              </a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <!-- Dots -->
        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex gap-2">
          <?php foreach ($heroRightBanners as $i => $hb): ?>
          <button @click="goTo(<?= $i ?>)" :class="slide === <?= $i ?> ? 'bg-premium-champagne w-6' : 'bg-white/40 w-2'" class="h-2 rounded-full transition-all duration-300 hover:bg-premium-champagne/70"></button>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- Fallback single panel -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-premium-burgundy to-premium-navy min-h-[320px] sm:min-h-[400px] lg:min-h-[480px] p-6 sm:p-8 flex flex-col justify-center group">
          <div class="absolute top-5 right-5 w-24 h-24 bg-white/5 rounded-full"></div>
          <p class="font-playfair text-white text-2xl sm:text-3xl font-bold leading-snug">Bridal<br>Mukut & Crowns</p>
          <p class="text-white/60 text-sm sm:text-base mt-2 max-w-xs">Handcrafted elegance for your special day</p>
          <a href="<?= url('products?category=bride') ?>" class="mt-4 inline-flex items-center gap-2 text-premium-champagne text-sm font-bold tracking-wider uppercase group-hover:gap-3 transition-all">
            Shop Bride <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
          </a>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <?php if (!empty($heroRightBanners)): ?>
    <script>
    function heroCarousel() {
      return {
        slide: 0,
        timer: null,
        total: <?= count($heroRightBanners) ?>,
        init() { this.start(); },
        start() { this.timer = setInterval(() => { this.next(); }, 4000); },
        stop() { clearInterval(this.timer); this.timer = null; },
        next() { this.slide = (this.slide + 1) % this.total; },
        goTo(i) { this.slide = i; this.stop(); this.start(); }
      }
    }
    </script>
    <?php endif; ?>

    <!-- ============================================================ -->
    <!-- FEATURED PRODUCTS - Boutique Grid                            -->
    <!-- ============================================================ -->
    <section class="py-8 sm:py-12" data-a="fade-up">
      <div class="flex items-end justify-between mb-8" data-a="fade-up">
        <div>
          <span class="text-premium-burgundy text-xs font-bold tracking-[0.2em] uppercase">Curated Collection</span>
          <h2 class="font-playfair text-2xl sm:text-3xl lg:text-4xl font-bold text-premium-charcoal mt-1">Our Finest Creations</h2>
        </div>
        <a href="<?= url('products') ?>" class="hidden sm:inline-flex items-center gap-2 text-premium-burgundy font-semibold text-sm hover:text-premium-cabernet transition-colors border-b-2 border-premium-burgundy/20 hover:border-premium-burgundy pb-0.5">
          View All
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4 lg:gap-5 a-stagger" data-a="stagger">
        <?php $displayProducts = array_slice(array_merge($featuredProducts, $trendingProducts, $recentProducts), 0, 10); ?>
        <?php foreach ($displayProducts as $product): ?>
        <?php $pImg = is_array($product) ? ($product['image'] ?? '') : ($product->image ?? ''); ?>
        <?php $pName = is_array($product) ? ($product['name'] ?? '') : ($product->name ?? ''); ?>
        <?php $pSlug = is_array($product) ? ($product['slug'] ?? '#') : ($product->slug ?? '#'); ?>
        <?php $pReg = is_array($product) ? ($product['regular_price'] ?? 0) : ($product->regular_price ?? 0); ?>
        <?php $pSale = is_array($product) ? ($product['sale_price'] ?? null) : ($product->sale_price ?? null); ?>
        <?php $pDisc = is_array($product) ? ($product['discount_percent'] ?? 0) : ($product->discount_percent ?? 0); ?>
        <?php $pCat = is_array($product) ? ($product['category_name'] ?? '') : ($product->category_name ?? ''); ?>
        <?php $pSub = is_array($product) ? ($product['subcategory_name'] ?? '') : ($product->subcategory_name ?? ''); ?>
        <?php $pId = is_array($product) ? ($product['id'] ?? 0) : ($product->id ?? 0); ?>
        <?php $rAvg = $homeRatings[$pId]['average'] ?? 0; $rTot = $homeRatings[$pId]['total'] ?? 0; ?>
        <a href="<?= url('product/' . e($pSlug)) ?>" class="group bg-white rounded-2xl overflow-hidden border border-premium-warm-gray hover:border-premium-champagne/40 hover:shadow-xl hover:shadow-premium-champagne/5 transition-all duration-400">
          <div class="relative aspect-square overflow-hidden bg-premium-cream">
            <?php if ($pDisc > 0): ?>
            <span class="absolute top-2.5 left-2.5 bg-premium-burgundy text-white text-[10px] font-bold px-2.5 py-1 rounded-full z-10 shadow-md">-<?= (int)$pDisc ?>%</span>
            <?php endif; ?>
            <div class="absolute top-2.5 right-2.5 z-10 flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0">
              <button data-wishlist="<?= $pId ?>" onclick="event.preventDefault();event.stopPropagation();toggleWishlist(<?= $pId ?>)" class="w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-premium-mink hover:text-premium-burgundy hover:shadow-lg transition-all" title="Wishlist">
                <svg class="w-4 h-4" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
              <button data-compare="<?= $pId ?>" onclick="event.preventDefault();event.stopPropagation();addToCompare(<?= $pId ?>)" class="w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-premium-mink hover:text-premium-champagne hover:shadow-lg transition-all" title="Compare">
                <svg class="w-4 h-4" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
              </button>
            </div>
            <img class="w-full h-full object-contain p-2 transition-transform duration-700 group-hover:scale-110" src="<?= e($pImg ?: 'https://placehold.co/400x400/FEF3C7/D4A847?text=WeddingYour') ?>" alt="<?= e($pName) ?>" loading="lazy">
            <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-white/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-400"></div>
          </div>
          <div class="p-4">
            <p class="text-[10px] text-premium-taupe font-semibold uppercase tracking-wider"><?= e($pCat ?: 'Wedding') ?></p>
            <h3 class="text-sm font-bold text-premium-charcoal mt-0.5 leading-tight line-clamp-2 group-hover:text-premium-burgundy transition-colors"><?= e($pName) ?></h3>
            <?= renderStars($rAvg) ?>
            <div class="flex items-center gap-2 mt-2">
              <span class="text-base font-bold text-premium-burgundy"><?= formatPrice($pSale ?: $pReg) ?></span>
              <?php if ($pDisc > 0): ?>
              <span class="text-xs text-premium-taupe line-through"><?= formatPrice($pReg) ?></span>
              <?php endif; ?>
            </div>
            <button onclick="event.preventDefault();event.stopPropagation();addToCart(<?= $pId ?>, 1)" class="w-full mt-2.5 bg-premium-charcoal hover:bg-premium-burgundy text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
              Add to Cart
            </button>
          </div>
        </a>
        <?php endforeach; ?>

        <?php if (empty($displayProducts)): ?>
        <?php $demoProducts = [['name'=>'Crown','category_name'=>'BRIDE','regular_price'=>3000,'sale_price'=>1790,'discount_percent'=>40],['name'=>'Panpata','category_name'=>'BRIDE','regular_price'=>2500,'sale_price'=>1450,'discount_percent'=>42],['name'=>'Piri','category_name'=>'BRIDE','regular_price'=>2200,'sale_price'=>1342,'discount_percent'=>39],['name'=>'Tattwasuchi','category_name'=>'BRIDE','regular_price'=>3500,'sale_price'=>1400,'discount_percent'=>60],['name'=>'Topor Mukut','category_name'=>'GROOM','regular_price'=>1800,'sale_price'=>1188,'discount_percent'=>34],['name'=>'Gachhkouto','category_name'=>'WEDDING','regular_price'=>2800,'sale_price'=>1876,'discount_percent'=>33],['name'=>'Khoidan Kulo','category_name'=>'WEDDING','regular_price'=>3200,'sale_price'=>1600,'discount_percent'=>50],['name'=>'Matha Patti','category_name'=>'JEWELLERY','regular_price'=>1500,'sale_price'=>899,'discount_percent'=>40],['name'=>'Dorpon','category_name'=>'GROOM','regular_price'=>2000,'sale_price'=>1299,'discount_percent'=>35],['name'=>'Sithi Mukut','category_name'=>'BRIDE','regular_price'=>2800,'sale_price'=>1699,'discount_percent'=>39]]; foreach ($demoProducts as $product): ?>
        <div class="bg-white rounded-2xl overflow-hidden border border-premium-warm-gray group">
          <div class="relative aspect-square overflow-hidden bg-premium-cream">
            <span class="absolute top-2.5 left-2.5 bg-premium-burgundy text-white text-[10px] font-bold px-2.5 py-1 rounded-full z-10 shadow-md">-<?= $product['discount_percent'] ?>%</span>
            <img class="w-full h-full object-contain p-2 transition-transform duration-700 group-hover:scale-110" src="https://placehold.co/400x400/FEF3C7/D4A847?text=WeddingYour" alt="<?= $product['name'] ?>" loading="lazy">
          </div>
          <div class="p-4">
            <p class="text-[10px] text-premium-taupe font-semibold uppercase tracking-wider"><?= $product['category_name'] ?></p>
            <h3 class="text-sm font-bold text-premium-charcoal mt-0.5"><?= $product['name'] ?></h3>
            <div class="flex items-center gap-2 mt-2">
              <span class="text-base font-bold text-premium-burgundy"><?= formatPrice($product['sale_price']) ?></span>
              <span class="text-xs text-premium-taupe line-through"><?= formatPrice($product['regular_price']) ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="mt-8 text-center sm:hidden">
        <a href="<?= url('products') ?>" class="inline-flex items-center gap-2 bg-premium-burgundy text-white font-bold text-sm px-6 py-3 rounded-full transition-all hover:bg-premium-cabernet shadow-md">View All Products <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>
      </div>
    </section>

    <!-- ============================================================ -->
    <!-- CATEGORY CARDS - Luxury Grid                                 -->
    <!-- ============================================================ -->
    <section class="py-8 sm:py-12" data-a="fade-up">
      <div class="text-center mb-8 sm:mb-10" data-a="fade-up">
        <span class="text-premium-burgundy text-xs font-bold tracking-[0.2em] uppercase">Shop by</span>
        <h2 class="font-playfair text-2xl sm:text-3xl lg:text-4xl font-bold text-premium-charcoal mt-1">Categories</h2>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 lg:gap-5 a-stagger" data-a="stagger">
        <?php $trendingCats = array_slice($categories, 0, 4); ?>
        <?php if (!empty($trendingCats)): ?>
        <?php foreach ($trendingCats as $cat): $cs = is_array($cat) ? ($cat['slug']??'') : $cat; $cn = is_array($cat) ? ($cat['name']??'') : $cat; $ci = is_array($cat) ? ($cat['image']??'') : ''; ?>
        <a href="<?= url('products?category=' . e($cs)) ?>" class="group relative rounded-2xl overflow-hidden min-h-[220px] sm:min-h-[280px] lg:min-h-[320px] bg-premium-charcoal">
          <?php if ($ci): ?>
          <img src="<?= uploadUrl($ci, 'categories') ?>" alt="" class="w-full h-full absolute inset-0 object-cover transition-transform duration-700 group-hover:scale-110">
          <?php else: ?>
          <div class="w-full h-full absolute inset-0 bg-gradient-to-br from-premium-burgundy/80 to-premium-navy/80"></div>
          <?php endif; ?>
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
          <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-5 lg:p-6">
            <h3 class="font-playfair text-white text-lg sm:text-xl lg:text-2xl font-bold"><?= e($cn ?: 'Category') ?></h3>
            <span class="inline-flex items-center gap-1.5 text-premium-champagne text-xs font-bold tracking-wide mt-1.5 opacity-0 group-hover:opacity-100 transition-all translate-y-1 group-hover:translate-y-0">
              Shop Now <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </span>
          </div>
        </a>
        <?php endforeach; ?>
        <?php else: ?>
        <?php $fallbackCats = [['name'=>'Bride','slug'=>'bride'],['name'=>'Groom','slug'=>'groom'],['name'=>'Baby','slug'=>'baby'],['name'=>'Wedding Items','slug'=>'wedding-items']]; ?>
        <?php foreach ($fallbackCats as $cat): ?>
        <a href="<?= url('products?category=' . e($cat['slug'])) ?>" class="group relative rounded-2xl overflow-hidden min-h-[220px] sm:min-h-[280px] lg:min-h-[320px] bg-gradient-to-br from-premium-burgundy/90 to-premium-navy/90">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
          <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-5 lg:p-6">
            <h3 class="font-playfair text-white text-lg sm:text-xl lg:text-2xl font-bold"><?= e($cat['name']) ?></h3>
            <span class="inline-flex items-center gap-1.5 text-premium-champagne text-xs font-bold tracking-wide mt-1.5 opacity-0 group-hover:opacity-100 transition-all translate-y-1 group-hover:translate-y-0">
              Shop Now <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </span>
          </div>
        </a>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <!-- ============================================================ -->
    <!-- RECENT PRODUCTS - Horizontal Carousel                        -->
    <!-- ============================================================ -->
    <section class="py-8 sm:py-12" data-a="fade-up">
      <div class="flex items-end justify-between mb-8" data-a="fade-up">
        <div>
          <span class="text-premium-burgundy text-xs font-bold tracking-[0.2em] uppercase">New Arrivals</span>
          <h2 class="font-playfair text-2xl sm:text-3xl lg:text-4xl font-bold text-premium-charcoal mt-1">Just Added</h2>
        </div>
        <a href="<?= url('products?sort=newest') ?>" class="hidden sm:inline-flex items-center gap-2 text-premium-burgundy font-semibold text-sm hover:text-premium-cabernet transition-colors border-b-2 border-premium-burgundy/20 hover:border-premium-burgundy pb-0.5">
          View All
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
      </div>

      <div class="relative">
        <button onclick="recentScroll(-1)" class="absolute left-0 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-white/90 hover:bg-white rounded-full shadow-lg flex items-center justify-center text-premium-mink hover:text-premium-burgundy transition-all -ml-4 hidden sm:flex" aria-label="Previous">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
              </button>
              <button onclick="recentScroll(1)" class="absolute right-0 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-white/90 hover:bg-white rounded-full shadow-lg flex items-center justify-center text-premium-mink hover:text-premium-burgundy transition-all -mr-4 hidden sm:flex" aria-label="Next">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </button>
        <div id="recent-viewport" class="overflow-hidden">
        <div id="recent-track" class="flex transition-transform duration-500 ease-in-out">
          <?php $rp = array_slice($recentProducts, 0, 12); if (empty($rp)): $rp = array_fill(0,8,['name'=>'Product','category_name'=>'BRIDE','subcategory_name'=>'Wedding Item','tags'=>'Exclusive','regular_price'=>1499,'sale_price'=>799,'discount_percent'=>47]); endif; ?>
          <?php foreach ($rp as $p): ?>
          <?php $pn = is_array($p) ? ($p['name']??'') : ($p->name??''); $pc = is_array($p) ? ($p['category_name']??'') : ($p->category_name??''); $ps = is_array($p) ? ($p['subcategory_name']??'') : ($p->subcategory_name??''); $pt = is_array($p) ? ($p['tags']??'') : ($p->tags??''); ?>
          <?php $pr = is_array($p) ? ($p['regular_price']??0) : ($p->regular_price??0); $psl = is_array($p) ? ($p['sale_price']??0) : ($p->sale_price??0); $pd = is_array($p) ? ($p['discount_percent']??0) : ($p->discount_percent??0); ?>
          <?php $pimg = is_array($p) ? ($p['image']??'') : ($p->image??''); $pSlug = is_array($p) ? ($p['slug']??'#') : ($p->slug??'#'); $pId = is_array($p) ? ($p['id']??0) : ($p->id??0); $rAvg = $homeRatings[$pId]['average'] ?? 0; $rTot = $homeRatings[$pId]['total'] ?? 0; ?>
          <div class="w-1/2 md:w-1/3 lg:w-1/5 xl:w-1/6 flex-shrink-0 px-2">
          <a href="<?= url('product/' . e($pSlug)) ?>" class="bg-white rounded-2xl overflow-hidden border border-premium-warm-gray hover:border-premium-champagne/40 hover:shadow-xl hover:shadow-premium-champagne/5 transition-all duration-400 group flex flex-col h-full">
            <div class="relative aspect-[4/5] overflow-hidden bg-premium-cream">
              <?php if ($pd): ?>
              <span class="absolute top-2.5 left-2.5 bg-premium-burgundy text-white text-[10px] font-bold px-2.5 py-1 rounded-full z-10 shadow-md">-<?= (int)$pd ?>%</span>
              <?php endif; ?>
              <div class="absolute top-2.5 right-2.5 z-10 flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0">
                <button data-wishlist="<?= $pId ?>" onclick="event.preventDefault();event.stopPropagation();toggleWishlist(<?= $pId ?>)" class="w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-premium-mink hover:text-premium-burgundy hover:shadow-lg transition-all" title="Wishlist">
                  <svg class="w-4 h-4" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
                <button data-compare="<?= $pId ?>" onclick="event.preventDefault();event.stopPropagation();addToCompare(<?= $pId ?>)" class="w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-premium-mink hover:text-premium-champagne hover:shadow-lg transition-all" title="Compare">
                  <svg class="w-4 h-4" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                </button>
              </div>
              <?php if ($pimg): ?>
              <img src="<?= e($pimg) ?>" alt="<?= e($pn) ?>" class="w-full h-full object-contain p-2 transition-transform duration-700 group-hover:scale-110" loading="lazy">
              <?php else: ?>
              <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-premium-blush to-premium-cream text-premium-champagne font-bold"><?= e(substr($pn,0,2)) ?></div>
              <?php endif; ?>
              <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-white/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
            <div class="p-3 flex flex-col flex-1">
              <p class="text-[9px] text-premium-taupe font-semibold uppercase tracking-wider"><?= e($pc) ?></p>
              <h4 class="text-xs font-bold text-premium-charcoal leading-tight line-clamp-2 group-hover:text-premium-burgundy transition-colors"><?= e($pn) ?></h4>
              <?= renderStars($rAvg) ?>
              <div class="flex items-center gap-2 mt-1">
                <span class="text-sm font-bold text-premium-burgundy"><?= formatPrice($psl ?: $pr) ?></span>
                <?php if ($psl): ?>
                <span class="text-[10px] text-premium-taupe line-through"><?= formatPrice($pr) ?></span>
                <?php endif; ?>
              </div>
              <button onclick="event.preventDefault();event.stopPropagation();addToCart(<?= $pId ?>, 1)" class="w-full mt-auto bg-premium-charcoal hover:bg-premium-burgundy text-white text-xs font-bold py-2 rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                Add to Cart
              </button>
            </div>
          </a>
          </div>
          <?php endforeach; ?>
        </div>
        </div>
      </div>
      <div class="flex items-center justify-center gap-2 pt-3" id="recent-dots"></div>
    </section>

    <!-- ============================================================ -->
    <!-- BRAND STORY - Video/Image Section                            -->
    <!-- ============================================================ -->
    <?php
    $brideBanner = current(array_filter($banners, fn($b)=>($b['position']??'')==='bride_video')) ?: null;
    $bvImg = $brideBanner['image'] ?? '';
    $bvExt = $bvImg ? strtolower(pathinfo($bvImg, PATHINFO_EXTENSION)) : '';
    $bvIsVideo = in_array($bvExt, ['mp4','webm','ogg','mov']);
    ?>
    <section class="relative overflow-hidden rounded-2xl bg-premium-charcoal min-h-[280px] sm:min-h-[360px] lg:min-h-[440px] flex items-center justify-center my-6 sm:my-8" data-a="scale">
      <?php if ($bvImg): ?>
      <?php if ($bvIsVideo): ?>
      <video src="<?= uploadUrl($bvImg) ?>" autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover"></video>
      <?php else: ?>
      <img src="<?= uploadUrl($bvImg) ?>" alt="" class="absolute inset-0 w-full h-full object-cover">
      <?php endif; ?>
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-black/40"></div>
      <?php else: ?>
      <div class="absolute inset-0 bg-gradient-to-r from-premium-burgundy to-premium-navy"></div>
      <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=60 height=60 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M30 0L60 30L30 60L0 30Z%22 fill=white/%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
      <?php endif; ?>
      <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-black/30"></div>
      <div class="relative z-10 text-center px-6 max-w-2xl">
        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20 mb-5 sm:mb-6">
          <svg class="w-7 h-7 sm:w-8 sm:h-8 text-premium-champagne ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </div>
        <h2 class="font-playfair text-white text-2xl sm:text-3xl lg:text-4xl font-bold">Crafted with Love,<br><span class="text-premium-champagne">Rooted in Tradition</span></h2>
        <p class="text-white/60 text-sm sm:text-base mt-3 max-w-lg mx-auto">Every piece tells a story of Bengali heritage, meticulously handcrafted by generations of skilled artisans.</p>
        <a href="<?= url('about') ?>" class="inline-flex items-center gap-2 mt-5 sm:mt-6 bg-premium-champagne hover:bg-premium-gold text-premium-charcoal font-bold text-xs sm:text-sm px-6 sm:px-8 py-2.5 sm:py-3 rounded-full transition-all">
          Discover Our Story
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
      </div>
    </section>

    <!-- ============================================================ -->
    <!-- CUSTOMER REVIEWS / TESTIMONIALS                              -->
    <!-- ============================================================ -->
    <?php if (!empty($reviews)): ?>
    <section class="py-8 sm:py-12" data-a="fade-up">
      <div class="text-center mb-8 sm:mb-10" data-a="fade-up">
        <span class="text-premium-burgundy text-xs font-bold tracking-[0.2em] uppercase">Testimonials</span>
        <h2 class="font-playfair text-2xl sm:text-3xl lg:text-4xl font-bold text-premium-charcoal mt-1">What Our Customers Say</h2>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 a-stagger" data-a="stagger">
        <?php foreach ($reviews as $review):
          $rName = $review['name'] ?? 'Anonymous';
          $rRating = $review['rating'] ?? 5;
          $rComment = $review['comment'] ?? '';
          $rTitle = $review['title'] ?? '';
          $rProduct = $review['product_name'] ?? '';
          $rDate = $review['created_at'] ?? '';
          $rAvatar = $review['avatar'] ?? '';
        ?>
        <div class="bg-white rounded-2xl border border-premium-warm-gray p-6 shadow-sm hover:shadow-lg transition-shadow duration-300 flex flex-col">
          <div class="flex items-center gap-1 mb-3">
            <?php for ($s = 1; $s <= 5; $s++): ?>
            <?php if ($s <= $rRating): ?>
            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <?php else: ?>
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <?php endif; ?>
            <?php endfor; ?>
          </div>
          <?php if ($rTitle): ?>
          <h4 class="text-sm font-bold text-premium-charcoal mb-1"><?= e($rTitle) ?></h4>
          <?php endif; ?>
          <p class="text-sm text-premium-mink leading-relaxed flex-1">"<?= e(truncate($rComment, 200)) ?>"</p>
          <div class="flex items-center gap-3 mt-4 pt-4 border-t border-premium-warm-gray">
            <div class="w-10 h-10 rounded-full bg-premium-blush flex items-center justify-center text-sm font-bold text-premium-cabernet shrink-0"><?= strtoupper(substr($rName, 0, 1)) ?></div>
            <div>
              <p class="text-sm font-semibold text-premium-charcoal"><?= e($rName) ?></p>
              <?php if ($rProduct): ?>
              <p class="text-[11px] text-premium-taupe"><?= e($rProduct) ?></p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-8">
        <a href="<?= url('products') ?>" class="inline-flex items-center gap-2 bg-premium-burgundy hover:bg-premium-cabernet text-white font-bold text-sm px-8 py-3 rounded-full transition-all shadow-md hover:shadow-lg">Shop Now & Leave Your Review</a>
      </div>
    </section>
    <?php endif; ?>

    <!-- ============================================================ -->
    <!-- GALLERY / MEMORIES SECTION                                   -->
    <!-- ============================================================ -->
    <section class="py-8 sm:py-12 my-6 sm:my-8 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-premium-burgundy to-premium-cabernet rounded-none sm:rounded-2xl" data-a="fade-up">
      <div class="text-center mb-8" data-a="fade-up">
        <h2 class="font-playfair text-2xl sm:text-3xl lg:text-4xl font-bold text-white">Moments from Our World</h2>
        <p class="text-white/60 text-sm mt-2">Glimpses of Bengali wedding traditions</p>
      </div>
      <div id="memories-scroll" class="flex gap-4 overflow-x-auto scrollbar-hide pb-2 select-none" style="cursor:grab; scrollbar-width:none; -ms-overflow-style:none; user-select:none; -webkit-user-select:none;">
        <?php $memItems = array_slice($galleryItems, 0, 6); if (empty($memItems)): $memItems = array_fill(0,4,['image'=>'']); endif; ?>
        <?php foreach ($memItems as $mi): $miImg = is_array($mi) ? ($mi['image']??'') : ($mi->image??''); ?>
        <div class="shrink-0 w-[55vw] sm:w-[30vw] md:w-[22vw] lg:w-[16vw] rounded-2xl overflow-hidden border-2 border-white/20 shadow-xl bg-white/5">
          <?php if ($miImg): ?>
          <img src="<?= uploadUrl($miImg, 'gallery') ?>" alt="Memory" class="w-full h-auto object-cover pointer-events-none" loading="lazy" draggable="false" style="aspect-ratio:2/3">
          <?php else: ?>
          <div class="w-full bg-white/5 flex items-center justify-center font-bold text-white/30 text-sm" style="aspect-ratio:2/3">Gallery</div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

  </main>
</div>

<script>
/* Recent Products slider */
(function(){var t=document.getElementById('recent-track'),v=document.getElementById('recent-viewport'),dc=document.getElementById('recent-dots');if(!t||!v||!dc)return;var c=0,ch=t.children,ni=ch.length;if(!ni)return;
function gpp(){return window.innerWidth<768?2:window.innerWidth<1024?3:window.innerWidth<1280?5:6;}
function upd(){var p=gpp(),tp=Math.ceil(ni/p);if(c>=tp)c=tp-1;if(c<0)c=0;dc.innerHTML='';for(var i=0;i<tp;i++){var d=document.createElement('button');d.className='w-2 h-2 rounded-full '+(i===c?'bg-premium-burgundy':'bg-premium-stone');d.onclick=function(p){return function(){go(p);};}(i);dc.appendChild(d);}
var sw=v.offsetWidth;t.style.transform='translateX(-'+(c*sw)+'px)';}
function go(p){c=p;upd();}
window.recentScroll=function(d){go(c+d);};
upd();window.addEventListener('resize',upd);})();

/* Subcategory scroll drag */
(function(){var el=document.getElementById('subcatScrollHome');if(!el)return;var down=false,startX=0,scrollLeft=0,moved=false;el.addEventListener('mousedown',function(e){down=true;moved=false;startX=e.pageX;scrollLeft=el.scrollLeft;el.style.cursor='grabbing';});window.addEventListener('mousemove',function(e){if(!down)return;e.preventDefault();var walk=e.pageX-startX;if(Math.abs(walk)>5)moved=true;el.scrollLeft=scrollLeft-walk;});window.addEventListener('mouseup',function(){down=false;if(el)el.style.cursor='grab';});window.addEventListener('mouseleave',function(){down=false;});el.addEventListener('click',function(e){if(moved){e.stopPropagation();e.preventDefault();}},true);})();

/* Memories scroll drag */
(function(){var el=document.getElementById('memories-scroll');if(!el)return;var down=false,startX=0,scrollLeft=0;el.addEventListener('mousedown',function(e){down=true;startX=e.pageX;scrollLeft=el.scrollLeft;el.style.cursor='grabbing';});window.addEventListener('mousemove',function(e){if(!down)return;e.preventDefault();var walk=e.pageX-startX;el.scrollLeft=scrollLeft-walk;});window.addEventListener('mouseup',function(){down=false;if(el)el.style.cursor='grab';});})();

/* High-quality scroll animations */
(function(){
  if (!window.IntersectionObserver) {
    document.querySelectorAll('[data-a]').forEach(function(el){ el.classList.add('a-done'); });
    return;
  }
  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (!entry.isIntersecting) return;
      var el = entry.target;
      var type = el.getAttribute('data-a');
      el.classList.remove('a-' + type);
      requestAnimationFrame(function(){ el.classList.add('a-done'); });
      observer.unobserve(el);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('[data-a]').forEach(function(el){ observer.observe(el); });
})();
</script>
