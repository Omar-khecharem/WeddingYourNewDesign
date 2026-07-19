<?php
$product = $product ?? [];
$images = $product['images'] ?? [];
$reviews = $reviews ?? [];
$ratingStats = $ratingStats ?? [];
$relatedProducts = $relatedProducts ?? [];
$primaryImage = null;
foreach ($images as $img) { if (!empty($img['is_primary'])) { $primaryImage = $img; break; } }
if (!$primaryImage && !empty($images[0])) { $primaryImage = $images[0]; }
$catName = $product['category_name'] ?? '';
$subName = $product['subcategory_name'] ?? '';
?>
<div class="bg-premium-ivory">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-xs sm:text-sm text-premium-taupe mb-4 space-x-2">
      <a href="<?= url('') ?>" class="hover:text-premium-crimson">Home</a>
      <span>/</span>
      <a href="<?= url('products?category=' . e($product['category_slug'] ?? '')) ?>" class="hover:text-premium-crimson"><?= e($catName) ?: 'Products' ?></a>
      <?php if ($subName): ?><span>/</span><a href="<?= url('products?subcategory=' . e($product['subcategory_slug'] ?? '')) ?>" class="hover:text-premium-crimson"><?= e($subName) ?></a><?php endif; ?>
      <span>/</span>
      <span class="text-premium-charcoal font-medium"><?= e($product['name']) ?></span>
    </nav>

    <!-- Back link -->
    <a href="<?= url('products') ?>" class="inline-flex items-center text-sm text-premium-taupe hover:text-premium-crimson mb-6">&larr; Back to products</a>

    <!-- ==================== MAIN GRID ==================== -->
    <div class="lg:grid lg:grid-cols-12 lg:gap-8 xl:gap-12">

      <!-- LEFT: Thumbnails (vertical strip) -->
      <div class="hidden lg:flex lg:flex-col lg:col-span-1 space-y-2 order-1">
        <?php if (!empty($images)): ?>
        <?php foreach ($images as $i => $img): ?>
        <button class="thumbnail-btn w-full aspect-square rounded-lg overflow-hidden border-2 <?= ($i === 0) ? 'border-premium-crimson' : 'border-premium-warm-gray' ?> hover:border-premium-blush transition-colors"
                data-img="<?= uploadUrl($img['image']) ?>">
          <img src="<?= uploadUrl($img['image']) ?>" alt="" class="w-full h-full object-cover">
        </button>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- CENTER: Main Image -->
      <div class="lg:col-span-6 order-2 mt-4 lg:mt-0">
        <div class="relative bg-premium-ivory rounded-xl overflow-hidden border border-premium-warm-gray">
          <?php if (!empty($product['discount_percent'])): ?>
          <span class="absolute top-3 left-3 bg-premium-burgundy text-white text-xs font-black px-2.5 py-1 rounded-md z-10">-<?= (int)$product['discount_percent'] ?>%</span>
          <?php endif; ?>
          <img id="mainProductImage"
               src="<?= $primaryImage ? uploadUrl($primaryImage['image']) : 'https://placehold.co/600x600?text=No+Image' ?>"
               alt="<?= e($product['name']) ?>"
               class="w-full h-full object-contain" style="aspect-ratio:1/1; max-height:600px">
        </div>
        <!-- Mobile thumbnails (horizontal) -->
        <?php if (count($images) > 1): ?>
        <div class="flex lg:hidden gap-2 mt-3 overflow-x-auto pb-2">
          <?php foreach ($images as $i => $img): ?>
          <button class="thumbnail-btn shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 <?= ($i === 0) ? 'border-premium-crimson' : 'border-premium-warm-gray' ?>"
                  data-img="<?= uploadUrl($img['image']) ?>">
            <img src="<?= uploadUrl($img['image']) ?>" alt="" class="w-full h-full object-cover">
          </button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- RIGHT: Product Info -->
      <div class="lg:col-span-5 order-3 mt-6 lg:mt-0 space-y-5">

        <div>
          <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-premium-charcoal"><?= e($product['name']) ?></h1>
          <?php if ($catName): ?>
          <p class="text-sm text-premium-taupe mt-1"><?= e($catName) ?><?= $subName ? ' / ' . e($subName) : '' ?></p>
          <?php endif; ?>
        </div>

        <!-- Rating -->
        <?php if (!empty($ratingStats)): ?>
        <div class="flex items-center gap-2">
          <div class="flex">
            <?php $avg = round($ratingStats['average'] ?? 0); for ($s = 1; $s <= 5; $s++): ?>
            <svg class="w-4 h-4 <?= $s <= $avg ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <?php endfor; ?>
          </div>
          <span class="text-sm text-premium-taupe"><?= number_format($ratingStats['average'] ?? 0, 1) ?> (<?= (int)($ratingStats['total'] ?? 0) ?> reviews)</span>
        </div>
        <?php endif; ?>

        <!-- Price -->
        <div class="flex items-baseline gap-3" id="priceDisplay">
          <?php
          $unitPrice = !empty($product['sale_price']) && $product['sale_price'] < $product['regular_price'] ? (float)$product['sale_price'] : (float)$product['regular_price'];
          $regPrice = (float)$product['regular_price'];
          ?>
          <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['regular_price']): ?>
          <span class="text-3xl font-black text-premium-crimson" id="priceCurrent"><?= formatPrice($product['sale_price']) ?></span>
          <span class="text-xl text-premium-stone line-through" id="priceOriginal"><?= formatPrice($product['regular_price']) ?></span>
          <?php if (!empty($product['discount_percent'])): ?>
          <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-premium-blush text-premium-cabernet"><?= (int)$product['discount_percent'] ?>% OFF</span>
          <?php endif; ?>
          <?php else: ?>
          <span class="text-3xl font-black text-premium-charcoal" id="priceCurrent"><?= formatPrice($product['regular_price']) ?></span>
          <?php endif; ?>
        </div>

        <!-- Stock -->
        <div class="flex items-center gap-2 text-sm">
          <?php if ($product['stock_status'] === 'in_stock'): ?>
          <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
          <span class="text-green-700 font-medium"><?= (int)$product['stock_quantity'] ?> in stock</span>
          <?php elseif ($product['stock_status'] === 'out_of_stock'): ?>
          <span class="w-2.5 h-2.5 rounded-full bg-premium-crimson"></span>
          <span class="text-premium-crimson font-medium">Out of stock</span>
          <?php else: ?>
          <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
          <span class="text-yellow-700 font-medium">On backorder</span>
          <?php endif; ?>
        </div>

        <!-- Specifications Table -->
        <table class="w-full text-sm border border-premium-warm-gray rounded-lg overflow-hidden">
          <tbody class="divide-y divide-premium-warm-gray">
            <?php $specs = []; if ($product['sku'] ?? null) $specs[] = ['SKU', e($product['sku'])]; if ($catName) $specs[] = ['Category', e($catName)]; if ($subName) $specs[] = ['Subcategory', e($subName)]; if ($product['weight'] ?? null) $specs[] = ['Weight', e($product['weight'])]; if ($product['stock_quantity'] ?? null) $specs[] = ['In Box', e($product['stock_quantity'])]; ?>
            <?php foreach ($specs as $i => $spec): ?>
            <tr class="<?= $i % 2 === 0 ? 'bg-premium-ivory' : 'bg-white' ?>">
              <td class="px-4 py-2.5 font-medium text-premium-mink w-1/3"><?= $spec[0] ?></td>
              <td class="px-4 py-2.5 text-premium-mink"><?= $spec[1] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($specs)): ?>
            <tr><td class="px-4 py-3 text-premium-taupe text-center" colspan="2">No specifications available</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- Quantity + Add to Cart -->
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="flex items-center border border-premium-warm-gray rounded-lg">
            <button type="button" id="qtyMinus" class="px-3 py-2.5 text-premium-taupe hover:text-premium-mink"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg></button>
            <input type="number" id="qtyInput" value="1" min="1" max="999" class="w-14 text-center border-0 focus:ring-0 text-premium-charcoal font-medium text-sm">
            <button type="button" id="qtyPlus" class="px-3 py-2.5 text-premium-taupe hover:text-premium-mink"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></button>
          </div>
          <button onclick="addToCart(<?= $product['id'] ?>, parseInt(document.getElementById('qtyInput').value))"
                  class="flex-1 bg-premium-burgundy hover:bg-premium-cabernet text-white font-bold text-sm px-6 py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2" <?= $product['stock_status'] !== 'in_stock' ? 'disabled' : '' ?>>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg> Add to cart
          </button>
        </div>

        <button onclick="buyNow(<?= $product['id'] ?>)" class="w-full bg-premium-champagne hover:bg-premium-gold text-premium-charcoal font-bold text-sm px-6 py-2.5 rounded-lg transition-colors" <?= $product['stock_status'] !== 'in_stock' ? 'disabled' : '' ?>>Buy now</button>

        <!-- Pincode Checker -->
        <div class="border border-premium-warm-gray rounded-lg p-4 space-y-2">
          <label class="text-sm font-medium text-premium-mink">Check Availability At</label>
          <div class="flex gap-2">
            <input type="text" id="pincodeInput" placeholder="Enter Delivery Pincode" class="flex-1 border border-premium-warm-gray rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-premium-blush focus:border-premium-crimson outline-none">
            <button onclick="checkPincode()" class="bg-premium-charcoal hover:bg-premium-dark text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Check</button>
          </div>
          <p id="pincodeMsg" class="text-xs text-premium-taupe hidden"></p>
        </div>

        <!-- Action links -->
        <div class="flex flex-wrap gap-4 text-sm text-premium-mink">
          <button data-compare="<?= $product['id'] ?>" onclick="addToCompare(<?= $product['id'] ?>)" class="hover:text-premium-crimson flex items-center gap-1.5"><svg class="w-4 h-4" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg> Add to compare</button>
          <button data-wishlist="<?= $product['id'] ?>" onclick="toggleWishlist(<?= $product['id'] ?>)" class="hover:text-premium-crimson flex items-center gap-1.5"><svg class="w-4 h-4" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg> Add to wishlist</button>
          <button onclick="shareProduct()" class="hover:text-premium-crimson flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg> Share</button>
        </div>

      </div>
    </div>

    <!-- ==================== TABS ==================== -->
    <div class="mt-12 border-t border-premium-warm-gray">
      <nav class="flex gap-6 -mb-px">
        <button class="tab-btn text-premium-crimson border-premium-crimson border-b-2 py-4 text-sm font-bold" data-tab="description">Description</button>
        <button class="tab-btn text-premium-taupe border-transparent border-b-2 py-4 text-sm font-medium hover:text-premium-mink" data-tab="specification">Specification</button>
        <button class="tab-btn text-premium-taupe border-transparent border-b-2 py-4 text-sm font-medium hover:text-premium-mink" data-tab="reviews">Customer Reviews (<?= (int)($ratingStats['total'] ?? 0) ?>)</button>
      </nav>

      <!-- Description Tab -->
      <div id="tab-description" class="tab-content py-6">
        <div class="prose max-w-none text-premium-mink text-sm leading-relaxed">
          <?= $product['description'] ? nl2br(e($product['description'])) : '<p class="text-premium-taupe">No description available.</p>' ?>
        </div>
        <?php if ($product['meta_keywords'] ?? null): ?>
        <div class="mt-6 flex flex-wrap gap-2">
          <?php foreach (explode(',', $product['meta_keywords']) as $kw): $kw = trim($kw); if ($kw): ?>
          <span class="text-xs bg-premium-warm-gray text-premium-mink px-2.5 py-1 rounded-full"><?= e($kw) ?></span>
          <?php endif; endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Specification Tab -->
      <div id="tab-specification" class="tab-content hidden py-6">
        <table class="w-full text-sm border border-premium-warm-gray rounded-lg overflow-hidden">
          <tbody class="divide-y divide-premium-warm-gray">
            <?php $allSpecs = array_merge($specs, []); if (!empty($allSpecs)): ?>
            <?php foreach ($allSpecs as $i => $s): ?>
            <tr class="<?= $i % 2 === 0 ? 'bg-premium-ivory' : 'bg-white' ?>">
              <td class="px-4 py-2.5 font-medium text-premium-mink w-1/3"><?= $s[0] ?></td>
              <td class="px-4 py-2.5 text-premium-mink"><?= $s[1] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr><td class="px-4 py-6 text-premium-taupe text-center" colspan="2">General product specification details would be shown here.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Reviews Tab -->
      <div id="tab-reviews" class="tab-content hidden py-6">
        <?php if (!empty($ratingStats)): ?>
        <div class="bg-premium-ivory rounded-xl p-6 mb-8">
          <div class="sm:flex sm:items-center sm:gap-8">
            <div class="text-center sm:text-left sm:shrink-0">
              <p class="text-4xl font-black text-premium-charcoal"><?= number_format($ratingStats['average'] ?? 0, 1) ?></p>
              <div class="flex items-center justify-center sm:justify-start mt-1">
                <?php $a = round($ratingStats['average'] ?? 0); for ($s = 1; $s <= 5; $s++): ?>
                <svg class="w-4 h-4 <?= $s <= $a ? 'text-yellow-400' : 'text-premium-stone' ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <?php endfor; ?>
              </div>
              <p class="text-sm text-premium-taupe mt-1">Based on <?= (int)($ratingStats['total'] ?? 0) ?> reviews</p>
            </div>
            <?php if (!empty($ratingStats['distribution'])): ?>
            <div class="flex-1 mt-4 sm:mt-0 space-y-1.5">
              <?php for ($star = 5; $star >= 1; $star--): ?>
              <?php $cnt = $ratingStats['distribution'][$star] ?? 0; $total = $ratingStats['total'] ?? 1; $pct = $total > 0 ? round(($cnt / $total) * 100) : 0; ?>
              <div class="flex items-center text-sm gap-2">
                <span class="w-10 text-right text-premium-mink"><?= $star ?></span>
                <div class="flex-1 h-2 bg-premium-warm-gray rounded-full overflow-hidden"><div class="h-full bg-yellow-400 rounded-full" style="width:<?= $pct ?>%"></div></div>
                <span class="w-6 text-premium-taupe"><?= $cnt ?></span>
              </div>
              <?php endfor; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($reviews)): ?>
        <div class="space-y-6">
          <?php foreach ($reviews as $review): ?>
          <div class="border-b border-premium-warm-gray pb-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-premium-blush flex items-center justify-center text-sm font-bold text-premium-cabernet"><?= strtoupper(substr($review['name'] ?? 'A', 0, 1)) ?></div>
                <div>
                  <p class="text-sm font-semibold text-premium-charcoal"><?= e($review['name'] ?? 'Anonymous') ?></p>
                  <div class="flex items-center gap-1">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                    <svg class="w-3.5 h-3.5 <?= $s <= ($review['rating'] ?? 5) ? 'text-yellow-400' : 'text-premium-stone' ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php endfor; ?>
                  </div>
                </div>
              </div>
              <time class="text-xs text-premium-taupe"><?= date('d M Y', strtotime($review['created_at'] ?? 'now')) ?></time>
            </div>
            <p class="mt-2 text-sm text-premium-mink"><?= nl2br(e($review['comment'] ?? '')) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-8 text-premium-taupe">
          <p class="font-medium">There are no reviews yet.</p>
          <p class="text-sm mt-1">Be the first to review "<?= e($product['name']) ?>"</p>
        </div>
        <?php endif; ?>

        <!-- Review Form -->
        <div class="mt-8 border-t border-premium-warm-gray pt-8">
          <h3 class="text-lg font-bold text-premium-charcoal mb-4">Write a Review</h3>
          <form method="POST" action="<?= url('product/' . e($product['slug']) . '/review') ?>" class="max-w-2xl space-y-4">
            <?= \App\Helpers\Security::csrfField() ?>
            <div>
              <label class="block text-sm font-medium text-premium-mink mb-1">Rating</label>
              <div class="flex items-center gap-1 star-rating">
                <?php for ($s = 5; $s >= 1; $s--): ?>
                <input type="radio" name="rating" value="<?= $s ?>" id="star<?= $s ?>" class="hidden" required>
                <label for="star<?= $s ?>" class="cursor-pointer text-premium-stone hover:text-yellow-400 transition-colors">
                  <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </label>
                <?php endfor; ?>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-premium-mink mb-1">Title (optional)</label>
              <input type="text" name="title" class="w-full border border-premium-warm-gray rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-premium-blush focus:border-premium-crimson outline-none" placeholder="Summarize your review">
            </div>
            <div>
              <label class="block text-sm font-medium text-premium-mink mb-1">Review <span class="text-premium-crimson">*</span></label>
              <textarea name="comment" rows="4" class="w-full border border-premium-warm-gray rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-premium-blush focus:border-premium-crimson outline-none" placeholder="Share your experience with this product" required></textarea>
            </div>
            <div class="flex items-center gap-3">
              <input type="text" name="name" value="<?= e(\App\Helpers\Session::get('user')['name'] ?? '') ?>" class="flex-1 border border-premium-warm-gray rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-premium-blush focus:border-premium-crimson outline-none" placeholder="Your name" required>
              <input type="email" name="email" value="<?= e(\App\Helpers\Session::get('user')['email'] ?? '') ?>" class="flex-1 border border-premium-warm-gray rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-premium-blush focus:border-premium-crimson outline-none" placeholder="Your email">
            </div>
            <button type="submit" class="bg-premium-burgundy text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-premium-crimson transition-all">Submit Review</button>
          </form>
        </div>

        <script>
        document.querySelectorAll('.star-rating label').forEach(function(label) {
          label.addEventListener('click', function() {
            var all = document.querySelectorAll('.star-rating label svg');
            var clicked = Array.from(all).indexOf(this.querySelector('svg'));
            all.forEach(function(svg, i) {
              svg.classList.toggle('text-yellow-400', i >= clicked);
              svg.classList.toggle('text-gray-300', i < clicked);
            });
          });
        });
        </script>
      </div>
    </div>

    <!-- ==================== RELATED PRODUCTS ==================== -->
    <?php if (!empty($relatedProducts)): ?>
    <div class="mt-12">
      <h2 class="text-xl font-black text-premium-charcoal mb-6">Related Products</h2>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        <?php foreach ($relatedProducts as $p): ?>
        <?php $pImg = $p['image'] ?? ''; $pName = $p['name'] ?? ''; $pSlug = $p['slug'] ?? '#'; $pReg = $p['regular_price'] ?? 0; $pSale = $p['sale_price'] ?? null; $pDisc = $p['discount_percent'] ?? 0; $pId = $p['id'] ?? 0; ?>
        <div class="group bg-white border border-premium-warm-gray rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
          <a href="<?= url('product/' . e($pSlug)) ?>" class="block aspect-square bg-premium-ivory relative overflow-hidden">
            <?php if ($pDisc): ?><span class="absolute top-2 left-2 bg-premium-burgundy text-white text-xs font-black px-2 py-0.5 rounded-md z-10">-<?= (int)$pDisc ?>%</span><?php endif; ?>
            <img src="<?= e($pImg ?: 'https://placehold.co/300x300?text=No+Image') ?>" alt="<?= e($pName) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
          </a>
          <div class="p-3 space-y-1">
            <a href="<?= url('product/' . e($pSlug)) ?>" class="text-xs font-bold text-premium-charcoal uppercase tracking-wide block truncate"><?= e($pName) ?></a>
            <div class="flex items-center gap-2">
              <?php if ($pSale && $pSale < $pReg): ?><span class="text-sm font-black text-premium-crimson"><?= formatPrice($pSale) ?></span><span class="text-xs text-premium-stone line-through"><?= formatPrice($pReg) ?></span>
              <?php else: ?><span class="text-sm font-black text-premium-charcoal"><?= formatPrice($pReg) ?></span><?php endif; ?>
            </div>
            <div class="flex items-center gap-1">
              <button onclick="addToCart(<?= $pId ?>, 1)" class="flex-1 bg-premium-burgundy hover:bg-premium-cabernet text-white text-xs font-bold py-1.5 rounded transition-colors">Add to cart</button>
              <button data-wishlist="<?= $pId ?>" onclick="toggleWishlist(<?= $pId ?>)" class="w-8 h-8 bg-white border border-premium-warm-gray rounded-lg flex items-center justify-center text-premium-taupe hover:text-premium-crimson hover:border-premium-blush transition-colors" title="Wishlist">
                <svg class="w-4 h-4" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Recently Viewed -->
    <div class="mt-8 text-center">
      <a href="<?= url('products') ?>" class="inline-block bg-premium-charcoal hover:bg-premium-dark text-white font-bold text-sm px-8 py-3 rounded-full transition-colors">Load more products</a>
    </div>

  </div>
</div>

<script>
(function(){
  // Thumbnail switching
  const mainImg = document.getElementById('mainProductImage');
  document.querySelectorAll('.thumbnail-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      document.querySelectorAll('.thumbnail-btn').forEach(b => b.classList.replace('border-premium-crimson','border-premium-warm-gray'));
      this.classList.replace('border-premium-warm-gray','border-premium-crimson');
      mainImg.src = this.dataset.img;
    });
  });

  // Quantity
  const qty = document.getElementById('qtyInput');
  const priceEl = document.getElementById('priceCurrent');
  var unitPrice = <?= json_encode($unitPrice) ?>;
  var currency = window.APP_CURRENCY || '<?= APP_CURRENCY ?>';
  function updatePrice() {
    if (!priceEl) return;
    var v = parseInt(qty.value) || 1;
    priceEl.textContent = currency + ' ' + Number(v * unitPrice).toFixed(2);
  }
  document.getElementById('qtyMinus')?.addEventListener('click', function(){ const v = parseInt(qty.value)||1; if(v>1) qty.value = v-1; updatePrice(); });
  document.getElementById('qtyPlus')?.addEventListener('click', function(){ const v = parseInt(qty.value)||1; if(v<999) qty.value = v+1; updatePrice(); });
  qty.addEventListener('input', updatePrice);

  // Tabs
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      document.querySelectorAll('.tab-btn').forEach(b => { b.classList.replace('text-premium-crimson','text-premium-taupe'); b.classList.replace('border-premium-crimson','border-transparent'); b.classList.remove('font-bold'); b.classList.add('font-medium'); });
      this.classList.replace('text-premium-taupe','text-premium-crimson'); this.classList.replace('border-transparent','border-premium-crimson'); this.classList.remove('font-medium'); this.classList.add('font-bold');
      document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
      document.getElementById('tab-' + this.dataset.tab)?.classList.remove('hidden');
    });
  });

})();

function shareProduct(){
  var url = window.location.href;
  if (navigator.share) {
    navigator.share({ title: document.title, url: url });
  } else {
    navigator.clipboard.writeText(url).then(function(){
      var btn = event.target.closest('button');
      var orig = btn.innerHTML;
      btn.innerHTML = 'Copied!';
      setTimeout(function(){ btn.innerHTML = orig; }, 2000);
    });
  }
}
function buyNow(id){
  addToCart(id, parseInt(document.getElementById('qtyInput').value));
  setTimeout(function(){ window.location.href='<?= url('checkout') ?>'; }, 500);
}
function checkPincode(){
  var input = document.getElementById('pincodeInput'), msg = document.getElementById('pincodeMsg');
  var pincode = input.value.trim();
  if(!pincode){ msg.textContent='Please enter a pincode.'; msg.classList.remove('hidden','text-green-600'); msg.classList.add('text-premium-crimson'); return; }
  msg.textContent='Checking...'; msg.classList.remove('hidden','text-premium-crimson','text-green-600'); msg.classList.add('text-premium-taupe');
  fetch('<?= url('api/check-pincode') ?>?product_id=<?= $product['id'] ?>&pincode='+encodeURIComponent(pincode))
    .then(function(r){ return r.json(); })
    .then(function(d){
      msg.textContent=d.message;
      msg.classList.remove('hidden','text-premium-taupe');
      msg.classList.add(d.available ? 'text-green-600' : 'text-premium-crimson');
    })
    .catch(function(){
      msg.textContent='Error checking availability. Try again.';
      msg.classList.remove('hidden','text-premium-taupe','text-green-600');
      msg.classList.add('text-premium-crimson');
    });
}
</script>