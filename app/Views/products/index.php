<?php
$productList = $products ?? [];
$total = $total ?? 0;
$page = $page ?? 1;
$perPage = $perPage ?? 12;
$totalPages = $totalPages ?? 1;

$filterOptions = $filterOptions ?? [];
$categories = $categories ?? $filterOptions['categories'] ?? [];
$brands = $filterOptions['brands'] ?? [];
$priceRange = $filterOptions['priceRange'] ?? ['min_price' => 0, 'max_price' => 99999];

$filters = $filters ?? [];
$currentCategory = $currentCategory ?? null;
$searchQuery = $searchQuery ?? '';
$sortBy = $sortBy ?? 'newest';

$start = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$end = min($page * $perPage, $total);

?>

<!-- Breadcrumb -->
<div class="bg-premium-ivory border-b border-premium-warm-gray">
  <div class="max-w-[1400px] mx-auto px-4 py-3 flex items-center gap-2 text-xs font-medium text-premium-taupe">
    <a href="<?= url('') ?>" class="hover:text-premium-crimson transition-colors">Home</a>
    <svg class="w-3 h-3 text-premium-stone" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-premium-charcoal font-semibold"><?= $currentCategory ? e($currentCategory) : 'All Products' ?></span>
  </div>
</div>

<?php $subList = $subcategories ?? []; ?>
<?php if (!empty($subList)): ?>
<section class="bg-white border-b border-premium-warm-gray overflow-hidden select-none">
  <div class="max-w-[1400px] mx-auto px-4 py-5">
    <div id="subcatScrollProd" class="flex gap-3 md:gap-4 overflow-x-auto pb-2 scrollbar-hide" style="cursor:grab; scrollbar-width:none; -ms-overflow-style:none; user-select:none; -webkit-user-select:none; -webkit-overflow-scrolling:touch;">
      <?php foreach ($subList as $sub): ?>
      <?php $subSlug = $sub['slug'] ?? ''; ?>
      <?php $subName = $sub['name'] ?? ''; ?>
      <?php $subImg = $sub['image'] ?? ''; ?>
      <?php $subCatSlug = $sub['cat_slug'] ?? ''; ?>
      <a href="<?= url('products?category=' . e($subCatSlug) . '&subcategory=' . e($subSlug)) ?>" draggable="false" ondragstart="return false" class="flex flex-col items-center text-center space-y-2 w-[90px] min-[400px]:w-[100px] sm:w-[110px] md:w-[120px] lg:w-[130px] shrink-0 group">
        <div class="w-full aspect-square rounded-3xl overflow-hidden border-2 border-premium-warm-gray group-hover:border-premium-crimson group-hover:scale-105 transition-all duration-300 shadow-md group-hover:shadow-xl">
          <?php if ($subImg): ?>
            <img src="<?= uploadUrl($subImg, 'categories') ?>" alt="<?= e($subName) ?>" class="w-full h-full object-cover" loading="lazy" draggable="false">
          <?php else: ?>
          <div class="w-full h-full bg-gradient-to-br from-premium-blush to-premium-warm-gray flex items-center justify-center font-bold text-premium-taupe"><?= e(substr($subName, 0, 2)) ?></div>
          <?php endif; ?>
        </div>
        <span class="text-[10px] min-[400px]:text-[11px] sm:text-xs md:text-sm font-semibold leading-tight text-premium-mink group-hover:text-premium-crimson transition-colors"><?= e($subName) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<div class="max-w-[1400px] mx-auto px-4 py-6">
  <div class="flex gap-8">

    <!-- ============ SIDEBAR FILTERS ============ -->
    <aside id="filter-sidebar" class="w-[280px] shrink-0 hidden lg:block">
      <form method="GET" action="<?= url('products') ?>" id="filter-form">

        <!-- Price filter - range slider -->
        <div class="mb-6">
          <h3 class="text-sm font-bold text-premium-charcoal uppercase tracking-wider mb-3">Filter by price</h3>
          <?php $minP = (int)($priceRange['min_price'] ?? 0); $maxP = (int)($priceRange['max_price'] ?? 5000); ?>
          <?php $curMin = (int)($filters['min_price'] ?? $minP); $curMax = (int)($filters['max_price'] ?? $maxP); ?>
          <input type="hidden" name="min_price" id="priceMin" value="<?= $curMin ?>">
          <input type="hidden" name="max_price" id="priceMax" value="<?= $curMax ?>">
          <div class="relative h-2 bg-premium-warm-gray rounded-full mt-6 mb-4">
            <div id="priceTrack" class="absolute h-full bg-premium-crimson rounded-full" style="left:<?= max(0,($curMin-$minP)/($maxP-$minP?:1)*100) ?>%;width:<?= max(1,($curMax-$curMin)/($maxP-$minP?:1)*100) ?>%"></div>
            <input type="range" id="rangeMin" min="<?= $minP ?>" max="<?= $maxP ?>" value="<?= $curMin ?>" step="10" class="absolute inset-0 w-full h-full appearance-none bg-transparent pointer-events-none z-10 [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-premium-burgundy [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:cursor-grab [&::-webkit-slider-thumb]:shadow-md [&::-moz-range-thumb]:pointer-events-auto [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:bg-premium-burgundy [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:cursor-grab [&::-moz-range-thumb]:shadow-md">
            <input type="range" id="rangeMax" min="<?= $minP ?>" max="<?= $maxP ?>" value="<?= $curMax ?>" step="10" class="absolute inset-0 w-full h-full appearance-none bg-transparent pointer-events-none z-10 [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-premium-burgundy [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:cursor-grab [&::-webkit-slider-thumb]:shadow-md [&::-moz-range-thumb]:pointer-events-auto [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:bg-premium-burgundy [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:cursor-grab [&::-moz-range-thumb]:shadow-md">
          </div>
          <div class="flex items-center justify-between text-xs text-premium-taupe mb-4">
            <span id="priceLabelMin">₹<?= number_format($curMin) ?></span>
            <span id="priceLabelMax">₹<?= number_format($curMax) ?></span>
          </div>
          <button type="submit" class="w-full text-xs font-bold text-white bg-premium-burgundy hover:bg-premium-cabernet rounded-lg py-2 transition-colors">Filter</button>
        </div>

        <!-- Categories - navbar style -->
        <div class="mb-6">
          <h3 class="text-sm font-bold text-premium-charcoal uppercase tracking-wider mb-3">CATEGORIES</h3>
          <ul class="space-y-1">
            <?php
            $catMenu = [];
            foreach ($categories as $cat) {
                $catMenu[$cat['slug']] = ['label' => $cat['name'], 'sub' => []];
            }
            foreach ($subcategories as $sub) {
                $s = $sub['cat_slug'];
                if (isset($catMenu[$s])) {
                    $catMenu[$s]['sub'][] = ['label' => $sub['name'], 'slug' => $sub['slug']];
                }
            }
            ?>
            <?php foreach ($catMenu as $slug => $mc):
              $active = ($filters['category'] ?? '') === $slug;
              $catParams = $_GET;
              if ($active) {
                  unset($catParams['category'], $catParams['subcategory']);
              } else {
                  $catParams['category'] = $slug;
                  unset($catParams['subcategory']);
              }
              unset($catParams['page']);
              $catParams = array_filter($catParams, fn($v) => $v !== '');
              $catHref = $catParams ? url('products?' . http_build_query($catParams)) : url('products');
            ?>
            <li class="relative group">
              <a href="<?= $catHref ?>" class="flex items-center justify-between text-sm py-2 px-3 rounded transition-colors <?= $active ? 'text-premium-crimson font-bold bg-premium-blush' : 'text-premium-mink hover:text-premium-crimson hover:bg-premium-ivory' ?>">
                <span><?= $mc['label'] ?></span>
                <?php if (!empty($mc['sub'])): ?>
                <svg class="w-3 h-3 text-premium-taupe group-hover:text-premium-crimson transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <?php endif; ?>
              </a>
              <?php if (!empty($mc['sub'])): ?>
              <div class="absolute left-full top-0 w-48 bg-white border border-premium-warm-gray rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-20 ml-2">
                <ul>
                  <?php foreach ($mc['sub'] as $sub):
                    $subActive = ($filters['subcategory'] ?? '') === $sub['slug'];
                    $subParams = array_merge($_GET, ['category' => $slug]);
                    if ($subActive) {
                        unset($subParams['subcategory']);
                    } else {
                        $subParams['subcategory'] = $sub['slug'];
                    }
                    unset($subParams['page']);
                    $subParams = array_filter($subParams, fn($v) => $v !== '');
                    $subHref = url('products?' . http_build_query($subParams));
                  ?>
                  <li><a href="<?= $subHref ?>" class="block px-4 py-2 text-xs font-medium text-premium-mink hover:bg-premium-ivory hover:text-premium-crimson transition-colors <?= $subActive ? 'text-premium-crimson font-bold bg-premium-blush' : '' ?>"><?= $sub['label'] ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <?php endif; ?>
            </li>
            <?php endforeach; ?>
            <li class="border-t border-premium-warm-gray pt-1 mt-1">
              <a href="<?= url('products?sort=popular') ?>" class="block text-sm py-2 px-3 text-premium-mink hover:text-premium-crimson hover:bg-premium-ivory rounded transition-colors">Best Seller</a>
            </li>
            <li>
              <a href="<?= url('products?featured=1') ?>" class="block text-sm py-2 px-3 text-premium-mink hover:text-premium-crimson hover:bg-premium-ivory rounded transition-colors">Exclusive</a>
            </li>
          </ul>
        </div>

      </form>
    </aside>

    <!-- ============ MAIN CONTENT ============ -->
    <div class="flex-1 min-w-0">

      <!-- Results bar -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-4 border-b border-premium-warm-gray">
        <p class="text-sm text-premium-taupe">
          Showing <?= $start ?>–<?= $end ?> of <?= $total ?> results
        </p>
        <div class="flex items-center gap-3">
          <!-- View toggle -->
          <div class="flex items-center border border-premium-warm-gray rounded overflow-hidden">
            <button onclick="setView('grid')" id="view-grid" class="p-1.5 text-xs bg-white hover:bg-premium-ivory transition-colors border-r border-premium-warm-gray" title="Grid view">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </button>
            <button onclick="setView('list')" id="view-list" class="p-1.5 text-xs bg-white hover:bg-premium-ivory transition-colors" title="List view">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </button>
          </div>
          <!-- Per page -->
          <div class="flex items-center gap-1.5">
            <span class="text-xs text-premium-taupe">Show</span>
            <select name="per_page" onchange="var p=new URLSearchParams(location.search);p.set('per_page',this.value);p.delete('page');location.search=p.toString()" class="text-xs border border-premium-warm-gray rounded px-2 py-1.5 bg-white">
              <?php foreach ([9,12,18,24] as $pp): ?>
              <option value="<?= $pp ?>" <?= $perPage == $pp ? 'selected' : '' ?>><?= $pp ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- Sort -->
          <select name="sort" onchange="var p=new URLSearchParams(location.search);p.set('sort',this.value);p.delete('page');location.search=p.toString()" class="text-xs border border-premium-warm-gray rounded px-2 py-1.5 bg-white">
            <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Default sorting</option>
            <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Price low to high</option>
            <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Price high to low</option>
            <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name</option>
            <option value="popular" <?= $sortBy === 'popular' ? 'selected' : '' ?>>Popular</option>
            <option value="discount" <?= $sortBy === 'discount' ? 'selected' : '' ?>>Discount</option>
          </select>
        </div>
      </div>

      <!-- Products grid -->
      <?php if (!empty($productList)): ?>
      <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" id="product-grid">
        <?php foreach ($productList as $product):
          $pName = $product['name'] ?? '';
          $pSlug = $product['slug'] ?? '';
          $pImg = $product['image'] ?? $product['images'][0] ?? 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=300';
          $pCategory = $product['category_name'] ?? $product['category'] ?? 'BRIDE';
          $pSubcategory = $product['subcategory_name'] ?? $product['subcategory'] ?? 'Bridal Patashi / Mukut';
          $pTags = $product['tags'] ?? ($product['tag'] ?? '');
          $pRegular = $product['regular_price'] ?? $product['price'] ?? 0;
          $pSale = $product['sale_price'] ?? $pRegular;
          $pDiscount = $product['discount_percent'] ?? ($pRegular > 0 ? round((1 - $pSale / $pRegular) * 100) : 0);
          $pStock = $product['stock_status'] ?? 'in_stock';
          $pId = $product['id'] ?? 0;
          $rAvg = $productRatings[$pId]['average'] ?? 0;
          $rTot = $productRatings[$pId]['total'] ?? 0;
        ?>
        <a href="<?= url('product/' . e($pSlug)) ?>" class="bg-white border border-premium-warm-gray rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow flex flex-col group/card product-card">
          <div class="relative overflow-hidden bg-premium-ivory" style="padding-bottom:133.33%">
            <?php if ($pDiscount > 0): ?>
            <span class="absolute top-2 left-2 bg-premium-burgundy text-white text-[10px] font-black px-2 py-0.5 rounded-md z-10">-<?= $pDiscount ?>%</span>
            <?php endif; ?>
              <img src="<?= e($pImg) ?>" alt="<?= e($pName) ?>" class="absolute inset-0 w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-500" loading="lazy">
            <!-- Hover overlay buttons -->
            <div class="absolute bottom-0 left-0 right-0 flex items-center justify-center gap-1.5 p-2 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 product-card-actions">
              <button onclick="event.stopPropagation();addToCart(<?= $pId ?>, 1)" class="w-7 h-7 bg-white rounded-full flex items-center justify-center text-premium-mink hover:bg-premium-crimson hover:text-white transition-colors shadow" title="Cart">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
              </button>
              <button onclick="event.stopPropagation();" class="w-7 h-7 bg-white rounded-full flex items-center justify-center text-premium-mink hover:bg-premium-crimson hover:text-white transition-colors shadow hidden sm:flex" title="Quick view">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </button>
              <button data-compare="<?= $pId ?>" onclick="event.stopPropagation();addToCompare(<?= $pId ?>)" class="w-7 h-7 bg-white rounded-full flex items-center justify-center text-premium-mink hover:bg-premium-crimson hover:text-white transition-colors shadow hidden sm:flex" title="Compare">
                <svg class="w-3.5 h-3.5" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
              </button>
              <button data-wishlist="<?= $pId ?>" onclick="event.stopPropagation();toggleWishlist(<?= $pId ?>)" class="w-7 h-7 bg-white rounded-full flex items-center justify-center text-premium-mink hover:bg-premium-crimson hover:text-white transition-colors shadow" title="Wishlist">
                <svg class="w-3.5 h-3.5" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
            </div>
          </div>
          <div class="p-3 flex flex-col gap-1.5 flex-1 product-card-body">
            <div class="product-info-main flex flex-col flex-1 min-h-0 gap-1">
              <div class="space-y-1">
                <h3 class="text-xs font-bold text-premium-charcoal uppercase tracking-wide leading-tight line-clamp-2"><?= e($pName) ?></h3>
                <p class="text-[10px] text-premium-taupe font-medium flex flex-wrap gap-x-1">
                  <span><?= e($pCategory) ?></span><span class="text-premium-stone">,</span>
                  <span><?= e($pSubcategory) ?></span>
                  <?php if ($pTags): ?><span class="text-premium-stone">,</span><span><?= e(is_array($pTags) ? implode(', ', $pTags) : $pTags) ?></span><?php endif; ?>
                </p>
                <div class="flex items-center gap-1">
                  <?= renderStars($rAvg) ?>
                  <?php if ($rTot > 0): ?><span class="text-[10px] text-premium-taupe">(<?= $rTot ?>)</span><?php endif; ?>
                </div>
                <span class="text-[10px] text-premium-mink font-bold flex items-center gap-1">
                  <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> In stock
                </span>
              </div>
              <div class="flex items-center gap-2 mt-auto">
                <span class="text-sm font-black text-premium-crimson">₹<?= number_format($pSale, 2) ?></span>
                <?php if ($pDiscount > 0): ?>
                <span class="text-[10px] text-premium-stone line-through">₹<?= number_format($pRegular, 2) ?></span>
                <?php endif; ?>
              </div>
            </div>
            <div class="product-info-actions">
              <button onclick="event.stopPropagation();addToCart(<?= $pId ?>, 1)" class="list-cart-btn" title="Add to Cart">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5zM15.75 14.25a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5zM5.106 5.25H20.25l-2.614 7.276A1.5 1.5 0 0116.2 13.5H7.5a1.5 1.5 0 01-1.436-1.044L5.106 5.25z"/></svg>
                <span>Add to Cart</span>
              </button>
              <button onclick="event.stopPropagation();addToCart(<?= $pId ?>, 1);setTimeout(function(){window.location.href='<?= url('checkout') ?>'},500)" class="list-buy-btn" title="Buy Now">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                <span>Buy Now</span>
              </button>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>

      <!-- Load More -->
      <?php if ($page < $totalPages): ?>
      <div class="text-center mt-8">
        <a href="<?= url('products?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>" class="inline-flex items-center gap-2 bg-premium-burgundy hover:bg-premium-cabernet text-white font-bold text-sm px-8 py-3 rounded-full transition-colors shadow-md">
          Load more products
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </a>
      </div>
      <?php endif; ?>

      <?php else: ?>
      <div class="text-center py-20">
        <div class="text-5xl text-premium-stone mb-4">
          <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <h2 class="text-lg font-bold text-premium-mink mb-2">No products found</h2>
        <p class="text-sm text-premium-taupe mb-6">Try adjusting your search or filter criteria.</p>
        <a href="<?= url('products') ?>" class="inline-block bg-premium-burgundy hover:bg-premium-cabernet text-white text-sm font-bold px-6 py-2.5 rounded-full transition-colors">View All Products</a>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<style>
.product-card.list-view-card { flex-direction: row !important; }
.product-card.list-view-card > .relative { width: 220px; min-width: 220px; height: 220px; flex-shrink: 0; border-radius: 0; padding-bottom: 0 !important; }
.product-card.list-view-card > .relative .product-card-actions { opacity: 1 !important; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent) !important; padding: 10px !important; gap: 8px !important; }
.product-card.list-view-card > .relative .product-card-actions button { width: 32px; height: 32px; }

.product-card.list-view-card .product-card-body { flex-direction: row !important; align-items: stretch; padding: 20px 24px; gap: 0; }

.product-card.list-view-card .product-info-main { flex: 1; display: flex; flex-direction: column; gap: 6px; padding-right: 24px; }
.product-card.list-view-card .product-info-main h3 { font-size: 15px; letter-spacing: 0.05em; line-height: 1.3; }
.product-card.list-view-card .product-info-main p { font-size: 12px; }
.product-card.list-view-card .product-info-main .text-sm { font-size: 18px; }

.product-card.list-view-card .product-info-actions { display: flex !important; flex-direction: column; gap: 10px; justify-content: center; min-width: 130px; border-left: 1px solid #E5E2DA; padding-left: 20px; }

.product-card.list-view-card .list-cart-btn,
.product-card.list-view-card .list-buy-btn { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 11px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; border: none; width: 100%; white-space: nowrap; }
.product-card.list-view-card .list-cart-btn { background: #B8845A; color: #FFF; }
.product-card.list-view-card .list-cart-btn:hover { background: #9E6F46; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(184,132,90,0.3); }
.product-card.list-view-card .list-buy-btn { background: #FFF; color: #1F2937; border: 1.5px solid #D1CCC4; }
.product-card.list-view-card .list-buy-btn:hover { border-color: #B8845A; color: #B8845A; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(184,132,90,0.12); }

.product-info-actions { display: none; }

@media (max-width: 768px) {
  .product-card.list-view-card { flex-direction: column !important; }
  .product-card.list-view-card > .relative { width: 100%; min-width: 100%; height: auto; }
  .product-card.list-view-card .product-card-body { flex-direction: column !important; padding: 16px; }
  .product-card.list-view-card .product-info-main { padding-right: 0; }
  .product-card.list-view-card .product-info-actions { flex-direction: row; flex-wrap: wrap; border-left: none; border-top: 1px solid #E5E2DA; padding-left: 0; padding-top: 16px; margin-top: 12px; min-width: 0; justify-content: stretch; }
  .product-card.list-view-card .list-cart-btn,
  .product-card.list-view-card .list-buy-btn { flex: 1; }
}
</style>
<script>
/* Category step carousel */
(function() {
  var track = document.getElementById('catTrackProd');
  var prevBtn = document.getElementById('catPrevBtn');
  var nextBtn = document.getElementById('catNextBtn');
  if (!track) return;
  var slides = track.children;
  if (slides.length < 2) return;
  var cur = 0;

  function goTo(idx) {
    cur = ((idx % slides.length) + slides.length) % slides.length;
    track.style.transform = 'translateX(-' + (cur * 100 / slides.length) + '%)';
    track.style.transition = 'transform 0.5s ease-in-out';
  }

  if (prevBtn) prevBtn.addEventListener('click', function() { goTo(cur - 1); });
  if (nextBtn) nextBtn.addEventListener('click', function() { goTo(cur + 1); });

  var interval = setInterval(function() { goTo(cur + 1); }, 4000);

  track.addEventListener('mouseenter', function() { clearInterval(interval); });
  track.addEventListener('mouseleave', function() {
    clearInterval(interval);
    interval = setInterval(function() { goTo(cur + 1); }, 4000);
  });
})();

/* Price range slider sync */
(function() {
  var minR = document.getElementById('rangeMin');
  var maxR = document.getElementById('rangeMax');
  var minH = document.getElementById('priceMin');
  var maxH = document.getElementById('priceMax');
  var track = document.getElementById('priceTrack');
  var labelMin = document.getElementById('priceLabelMin');
  var labelMax = document.getElementById('priceLabelMax');
  if (!minR || !maxR || !minH || !maxH || !track || !labelMin || !labelMax) return;
  function update() {
    var v1 = parseInt(minR.value), v2 = parseInt(maxR.value);
    if (v1 > v2) { if (this === minR) { minR.value = v2; v1 = v2; } else { maxR.value = v1; v2 = v1; } }
    minH.value = v1; maxH.value = v2;
    var min = parseInt(minR.min), max = parseInt(minR.max), range = (max - min) || 1;
    var l = (v1 - min) / range * 100, w = (v2 - v1) / range * 100;
    track.style.left = l + '%'; track.style.width = w + '%';
    labelMin.textContent = '₹' + v1.toLocaleString('en-IN');
    labelMax.textContent = '₹' + v2.toLocaleString('en-IN');
  }
  minR.addEventListener('input', update);
  maxR.addEventListener('input', update);
  update();
})();

/* Subcategory scroll drag */
(function(){var el=document.getElementById('subcatScrollProd');if(!el)return;var down=false,startX=0,scrollLeft=0,moved=false;el.addEventListener('mousedown',function(e){down=true;moved=false;startX=e.pageX;scrollLeft=el.scrollLeft;el.style.cursor='grabbing';});window.addEventListener('mousemove',function(e){if(!down)return;e.preventDefault();var walk=e.pageX-startX;if(Math.abs(walk)>5)moved=true;el.scrollLeft=scrollLeft-walk;});window.addEventListener('mouseup',function(){down=false;if(el)el.style.cursor='grab';});window.addEventListener('mouseleave',function(){down=false;});el.addEventListener('click',function(e){if(moved){e.stopPropagation();e.preventDefault();}},true);})();

/* View toggle */
function setView(mode) {
  var grid = document.getElementById('product-grid');
  if (!grid) return;
  localStorage.setItem('productView', mode);
  applyView(mode);
}
function applyView(mode) {
  var grid = document.getElementById('product-grid');
  if (!grid) return;
  document.getElementById('view-grid')?.classList.toggle('bg-premium-burgundy', mode === 'grid');
  document.getElementById('view-grid')?.classList.toggle('text-white', mode === 'grid');
  document.getElementById('view-grid')?.classList.toggle('bg-white', mode !== 'grid');
  document.getElementById('view-grid')?.classList.toggle('text-premium-mink', mode !== 'grid');
  document.getElementById('view-list')?.classList.toggle('bg-premium-burgundy', mode === 'list');
  document.getElementById('view-list')?.classList.toggle('text-white', mode === 'list');
  document.getElementById('view-list')?.classList.toggle('bg-white', mode !== 'list');
  document.getElementById('view-list')?.classList.toggle('text-premium-mink', mode !== 'list');
  var cards = grid.querySelectorAll('.product-card');
  if (mode === 'list') {
    grid.className = 'grid grid-cols-1 gap-4 max-w-5xl mx-auto';
    cards.forEach(function(c) { c.classList.add('list-view-card'); });
  } else {
    grid.className = 'grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4';
    cards.forEach(function(c) { c.classList.remove('list-view-card'); });
  }
}
(function() {
  var saved = localStorage.getItem('productView');
  if (saved) applyView(saved);
})();
</script>