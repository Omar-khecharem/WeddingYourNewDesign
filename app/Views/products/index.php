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

      <!-- Sort bar (kept outside dynamic container) -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-4 border-b border-premium-warm-gray">
        <p class="text-sm text-premium-taupe" id="products-count-static">
          Showing <?= $start ?>–<?= $end ?> of <?= $total ?> results
        </p>
        <div class="flex items-center gap-3">
          <div class="flex items-center border border-premium-warm-gray rounded overflow-hidden">
            <button onclick="setView('grid')" id="view-grid" class="p-1.5 text-xs bg-white hover:bg-premium-ivory transition-colors border-r border-premium-warm-gray" title="Grid view">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </button>
            <button onclick="setView('list')" id="view-list" class="p-1.5 text-xs bg-white hover:bg-premium-ivory transition-colors" title="List view">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </button>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="text-xs text-premium-taupe">Show</span>
            <select name="per_page" onchange="var p=new URLSearchParams(location.search);p.set('per_page',this.value);p.delete('page');location.search=p.toString()" class="text-xs border border-premium-warm-gray rounded px-2 py-1.5 bg-white">
              <?php foreach ([9,12,18,24] as $pp): ?>
              <option value="<?= $pp ?>" <?= $perPage == $pp ? 'selected' : '' ?>><?= $pp ?></option>
              <?php endforeach; ?>
            </select>
          </div>
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

      <!-- Dynamic content container (replaced on AJAX pagination) -->
      <div id="products-container">
        <?= \App\Core\View::include('products._grid', [
          'products' => $products ?? [],
          'total' => $total ?? 0,
          'page' => $page ?? 1,
          'perPage' => $perPage ?? 12,
          'totalPages' => $totalPages ?? 1,
          'productRatings' => $productRatings ?? [],
          'filters' => $filters ?? [],
          'sortBy' => $sortBy ?? 'newest',
        ]) ?>
      </div>

    </div>

  </div>
</div>

<style>
/* Loading shimmer for AJAX pagination */
#products-container.is-loading { position: relative; min-height: 300px; }
#products-container.is-loading #product-grid { opacity: 0.3; pointer-events: none; }
#products-container.is-loading::after {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(255,255,255,0.5);
  z-index: 5;
}
#products-container.is-loading #pagination-nav { opacity: 0.4; pointer-events: none; }
.pag-loading {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #9E6F46;
  font-size: 13px;
  font-weight: 600;
}
.pag-loading .spinner {
  width: 18px; height: 18px;
  border: 2.5px solid #E8D5C8;
  border-top-color: #B8845A;
  border-radius: 50%;
  animation: pag-spin 0.6s linear infinite;
}
@keyframes pag-spin { to { transform: rotate(360deg); } }

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
/* ───────────────────────────────────────────
   Smooth AJAX Pagination
   ─────────────────────────────────────────── */
(function(){
  var container = document.getElementById('products-container');
  if (!container) return;

  container.addEventListener('click', function(e) {
    var link = e.target.closest('.page-link');
    if (!link) return;
    e.preventDefault();

    var url = link.getAttribute('href');
    if (!url) return;

    // Abort if already loading
    if (container.classList.contains('is-loading')) return;

    // Show loading state
    container.classList.add('is-loading');

    // Update URL without reload
    var pageParam = new URLSearchParams(link.search).get('page');
    var newParams = new URLSearchParams(location.search);
    if (pageParam) newParams.set('page', pageParam);
    var newUrl = location.pathname + '?' + newParams.toString();
    history.pushState({ page: pageParam }, '', newUrl);

    // Also update the static results count placeholder
    var countEl = document.getElementById('products-count-static');
    if (countEl) countEl.textContent = 'Loading…';

    // Fetch partial via AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
      container.classList.remove('is-loading');
      if (xhr.status >= 200 && xhr.status < 300) {
        // Replace container content
        container.innerHTML = xhr.responseText;

        // Restore view mode preference
        var saved = localStorage.getItem('productView');
        if (saved === 'list' && window.applyView) {
          window.applyView('list');
        }

        // Update static count from new results bar
        var newCount = container.querySelector('#results-count');
        if (newCount && countEl) {
          countEl.textContent = newCount.textContent;
        }

        // Scroll to top of products section smoothly
        var header = document.querySelector('.flex-1');
        if (header) header.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } else {
        // Fallback: full page load on error
        location.href = url;
      }
    };
    xhr.onerror = function() { location.href = url; };
    xhr.send();
  });

  // Handle back/forward browser navigation
  window.addEventListener('popstate', function(e) {
    location.reload();
  });
})();

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