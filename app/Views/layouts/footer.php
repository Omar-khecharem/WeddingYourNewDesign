<?php
$currentYear = $currentYear ?? date('Y');
$footerName = \App\Models\Setting::get('site_name', 'WeddingYour');
$footerTagline = \App\Models\Setting::get('site_tagline', '');
$siteLogo = \App\Models\Setting::get('site_logo', '');
if (empty($siteLogo)) $siteLogo = 'site_logo.png';
$isAdmin = \App\Helpers\Session::has('user') && (\App\Helpers\Session::get('user')['role'] ?? '') === 'admin';
?>
<!-- ============================================ -->
<!-- FOOTER - PREMIUM LIGHT -->
<!-- ============================================ -->
<footer class="footer-premium">
  <div class="max-w-[1200px] mx-auto px-6 py-16">

    <!-- Main Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-12">

      <!-- Column 1: Brand (spans 2 cols on lg) -->
      <div class="lg:col-span-2 space-y-5">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-full overflow-hidden border border-premium-warm-gray flex items-center justify-center bg-white flex-shrink-0">
            <?php if ($siteLogo): ?>
            <img src="<?= uploadUrl($siteLogo) ?>" alt="<?= e($footerName) ?>" class="w-full h-full object-contain">
            <?php else: ?>
            <span class="font-playfair font-bold text-premium-burgundy text-lg"><?= e(substr($footerName, 0, 2)) ?></span>
            <?php endif; ?>
          </div>
          <div>
            <h2 class="font-playfair text-xl font-semibold "><?= e($footerName) ?></h2>
            <?php if ($footerTagline): ?>
            <p class="text-[10px] text-premium-taupe font-medium tracking-wider uppercase mt-0.5"><?= e($footerTagline) ?></p>
            <?php endif; ?>
          </div>
        </div>
        <p class="text-sm text-premium-mink leading-relaxed">Your premier destination for authentic Bengali wedding accessories. Handcrafted with love by skilled artisans since generations.</p>
        <div class="flex items-center gap-3 pt-1">
          <a href="<?= e(\App\Models\Setting::get('social_facebook', SOCIAL_FACEBOOK)) ?>" target="_blank" rel="noopener noreferrer" class="social-circle" title="Facebook">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1V12h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/></svg>
          </a>
          <a href="<?= e(\App\Models\Setting::get('social_instagram', SOCIAL_INSTAGRAM)) ?>" target="_blank" rel="noopener noreferrer" class="social-circle" title="Instagram">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
          </a>
          <a href="<?= e(\App\Models\Setting::get('social_youtube', SOCIAL_YOUTUBE)) ?>" target="_blank" rel="noopener noreferrer" class="social-circle" title="YouTube">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.517 3.545 12 3.545 12 3.545s-7.517 0-9.388.508a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.871.508 9.388.508 9.388.508s7.517 0 9.388-.508a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          </a>
        </div>
      </div>

      <!-- Column 2: Quick Links -->
      <div>
        <h3>Quick Links</h3>
        <ul class="space-y-3 text-sm">
          <li><a href="<?= url('') ?>" class="text-premium-mink">Home</a></li>
          <li><a href="<?= url('about') ?>" class="text-premium-mink">About Us</a></li>
          <li><a href="<?= url('products') ?>" class="text-premium-mink">Shop</a></li>
          <li><a href="<?= url('blog') ?>" class="text-premium-mink">Blog</a></li>
          <li><a href="<?= url('contact') ?>" class="text-premium-mink">Contact</a></li>
        </ul>
      </div>

      <!-- Column 3: Account -->
      <div>
        <h3>Account</h3>
        <ul class="space-y-3 text-sm">
          <li><a href="<?= url('account') ?>" class="text-premium-mink">My Account</a></li>
          <li><a href="<?= url('account/wishlist') ?>" class="text-premium-mink">Wishlist</a></li>
          <li><a href="<?= url('account/addresses') ?>" class="text-premium-mink">Addresses</a></li>
        </ul>
      </div>

      <!-- Column 4: Policies -->
      <div>
        <h3>Policies</h3>
        <ul class="space-y-3 text-sm">
          <li><a href="<?= url('privacy-policy') ?>" class="text-premium-mink">Privacy Policy</a></li>
          <li><a href="<?= url('terms-of-use') ?>" class="text-premium-mink">Terms of Use</a></li>
          <li><a href="<?= url('about') ?>" class="text-premium-mink">About Us</a></li>
          <li><a href="<?= url('contact') ?>" class="text-premium-mink">Contact</a></li>
        </ul>
      </div>

    </div>

    <!-- Divider -->
    <div class="divider-gold my-10"></div>

    <!-- Bottom row -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
      <p class="text-xs text-premium-taupe">&copy; <?= $currentYear ?> <?= e($footerName) ?>. All rights reserved.</p>
      <div class="flex items-center gap-4">
        <span class="text-xs text-premium-mink font-medium tracking-wide uppercase">Secure payments</span>
        <img src="<?= asset('images/payments.png') ?>" alt="Payment methods" class="h-6 opacity-40">
      </div>
    </div>

  </div>
</footer>

<!-- ============================================ -->
<!-- FLOATING WHATSAPP BUTTON -->
<!-- ============================================ -->
<?php $waNumber = \App\Models\Setting::get('whatsapp_number', WHATSAPP_NUMBER); $waLink = \App\Models\Setting::get('whatsapp_link', 'https://wa.me/' . $waNumber); ?>
<a href="<?= e($waLink) ?>" class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
</a>

<!-- ============================================ -->
<!-- GLOBAL JAVASCRIPT -->
<!-- ============================================ -->
<script>
<?php
$wishlistProductIds = [];
if (\App\Helpers\Session::has('user')) {
    $uid = \App\Helpers\Session::get('user')['id'] ?? 0;
    if ($uid) {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT product_id FROM sg_wishlist WHERE user_id = :uid");
        $stmt->execute([':uid' => $uid]);
        $wishlistProductIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
?>
window._wishlistIds = <?= json_encode(array_map('intval', $wishlistProductIds)) ?>;
function toggleMobileMenu() {
    document.querySelector('.nav-links .flex.items-center.gap-6').classList.toggle('hidden');
}
function addToCart(productId, quantity, variantId) {
    quantity = quantity || 1;
    var params = 'action=add_to_cart&product_id=' + productId + '&quantity=' + quantity + '&_csrf_token=<?= \App\Helpers\Session::csrfToken() ?>';
    if (variantId) params += '&variant_id=' + variantId;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= url('cart/add') ?>', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        console.log('addToCart response:', xhr.status, xhr.responseText.substring(0, 500));
        try {
            var d = JSON.parse(xhr.responseText);
            if (d.success) { showToast(d.message, 'success'); updateCartCount(d.cart_count); updateCartTotal(d.cart_total); }
            else { showToast(d.message || 'Error', 'error'); }
        } catch(e) {
            console.error('addToCart JSON parse error:', e.message, 'Status:', xhr.status);
            console.error('Raw response:', xhr.responseText.substring(0, 1000));
            showToast('Failed to add to cart.', 'error');
        }
    };
    xhr.onerror = function() { console.error('addToCart network error'); showToast('Failed to add to cart.', 'error'); };
    xhr.send(params);
}
function addToWishlist(productId) {
    var formData = new FormData();
    formData.append('action', 'add_to_wishlist');
    formData.append('product_id', productId);
    formData.append('_csrf_token', '<?= \App\Helpers\Session::csrfToken() ?>');
    fetch('<?= url('wishlist/add') ?>', {
        method: 'POST', body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        showToast(d.message, d.success ? 'success' : 'error');
        if (d.count !== undefined) updateWishlistCount(d.count);
    })
    .catch(function() { showToast('Failed to add to wishlist.', 'error'); });
}
function toggleCompare(productId) { addToCompare(productId); }
function addToCompare(productId) {
    var btn = document.querySelector('[data-compare="' + productId + '"]');
    var formData = new FormData();
    formData.append('product_id', productId);
    formData.append('_csrf_token', '<?= \App\Helpers\Session::csrfToken() ?>');
    fetch('<?= url('compare/toggle') ?>', {
        method: 'POST', body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            var compare = JSON.parse(localStorage.getItem('compare') || '[]');
            if (d.action === 'added') {
                if (compare.indexOf(productId) === -1) compare.push(productId);
            } else {
                compare = compare.filter(function(id) { return id !== productId; });
            }
            localStorage.setItem('compare', JSON.stringify(compare));
            if (btn) {
                btn.classList.toggle('is-active', d.action === 'added');
                btn.classList.toggle('is-inactive', d.action === 'removed');
            }
            if (typeof d.count === 'number') updateCompareCount(d.count);
            showToast(d.action === 'added' ? 'Added to compare' : 'Removed from compare', 'success');
        } else {
            showToast(d.message || 'Error', 'error');
        }
    })
    .catch(function() { showToast('Failed to update compare.', 'error'); });
}
function updateCompareCount(count) {
    var badges = document.querySelectorAll('.compare-count');
    badges.forEach(function(b) { b.textContent = count; b.style.display = count > 0 ? 'flex' : 'none'; });
}
function updateCartCount(count) {
    var badges = document.querySelectorAll('.cart-count, .cart-count-mobile');
    badges.forEach(function(b) { b.textContent = count; b.style.display = count > 0 ? 'flex' : 'none'; });
}
function updateCartTotal(total) {
    if (total === undefined) return;
    var currency = window.APP_CURRENCY || '<?= APP_CURRENCY ?>';
    var pills = document.querySelectorAll('.cart-total');
    pills.forEach(function(el) { el.textContent = currency + ' ' + Number(total).toFixed(2); });
}
function toggleWishlist(productId) {
    var btn = document.querySelector('[data-wishlist="' + productId + '"]');
    var formData = new FormData();
    formData.append('product_id', productId);
    formData.append('_csrf_token', '<?= \App\Helpers\Session::csrfToken() ?>');
    fetch('<?= url('wishlist/add') ?>', {
        method: 'POST', body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) {
        return r.json().catch(function(e) {
            return r.text().then(function(t) { console.error('Response text:', t.substring(0, 500)); throw e; });
        });
    })
    .then(function(data) {
        if (data.success) {
            var wishlist = window._wishlistIds || [];
            if (data.action === 'added') {
                if (wishlist.indexOf(productId) === -1) wishlist.push(productId);
            } else {
                wishlist = wishlist.filter(function(id) { return id !== productId; });
            }
            window._wishlistIds = wishlist;
            if (btn) {
                btn.classList.toggle('is-active', data.action === 'added');
                btn.classList.toggle('is-inactive', data.action === 'removed');
            }
            if (typeof data.count === 'number') updateWishlistCount(data.count);
            showToast(data.action === 'added' ? 'Added to wishlist' : 'Removed from wishlist', 'success');
        } else {
            showToast(data.message || 'Error', 'error');
        }
    })
    .catch(function(e) { console.error('toggleWishlist caught:', e); showToast('Network error', 'error'); });
}
document.addEventListener('DOMContentLoaded', function() {
    // Load wishlist badges
    var wishlist = window._wishlistIds || [];
    wishlist.forEach(function(id) {
        var btn = document.querySelector('[data-wishlist="' + id + '"]');
        if (btn) {
            btn.classList.add('is-active');
            btn.classList.remove('is-inactive');
        }
    });
    // Load compare IDs from server and sync with localStorage
    fetch('<?= url('compare/ids') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success && d.ids) {
            localStorage.setItem('compare', JSON.stringify(d.ids));
            d.ids.forEach(function(id) {
                var btn = document.querySelector('[data-compare="' + id + '"]');
                if (btn) {
                    btn.classList.add('is-active');
                    btn.classList.remove('is-inactive');
                }
            });
            if (typeof d.count === 'number') updateCompareCount(d.count);
        }
    })
    .catch(function() {
        // Fallback to localStorage
        var compare = JSON.parse(localStorage.getItem('compare') || '[]');
        compare.forEach(function(id) {
            var btn = document.querySelector('[data-compare="' + id + '"]');
            if (btn) {
                btn.classList.add('is-active');
                btn.classList.remove('is-inactive');
            }
        });
    });
});
function updateWishlistCount(count) {
    var badges = document.querySelectorAll('.wishlist-count');
    badges.forEach(function(b) { b.textContent = count; b.style.display = count > 0 ? 'flex' : 'none'; });
}
function buyNow(productId) {
    var qty = 1;
    var qtyInput = document.getElementById('qtyInput');
    if (qtyInput) qty = parseInt(qtyInput.value) || 1;
    var params = 'action=add_to_cart&product_id=' + productId + '&quantity=' + qty + '&_csrf_token=<?= \App\Helpers\Session::csrfToken() ?>';
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= url('cart/add') ?>', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        try {
            var d = JSON.parse(xhr.responseText);
            if (d.success) {
                window.location.href = '<?= url('checkout') ?>';
            } else {
                showToast(d.message || 'Error', 'error');
            }
        } catch(e) {
            showToast('Failed to process. Please try again.', 'error');
        }
    };
    xhr.onerror = function() { showToast('Network error. Please try again.', 'error'); };
    xhr.send(params);
}
function showToast(message, type) {
    type = type || 'info';
    var colors = { success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-yellow-500', info: 'bg-blue-500' };
    var toast = document.createElement('div');
    toast.className = 'fixed bottom-5 right-5 z-[9999] ' + (colors[type] || colors.info) + ' text-white px-5 py-3 rounded-lg shadow-lg animate-slideInUp';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(function() {
        toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s';
        setTimeout(function() { toast.remove(); }, 300);
    }, 3000);
}
document.addEventListener('click', function(e) {
    if (e.target.closest('[onclick*="remove"]')) e.target.closest('.fixed.top-24').remove();
});
</script>

<div id="privacy-banner" class="fixed bottom-0 left-0 right-0 z-[99999] bg-white border-t border-premium-warm-gray shadow-lg" style="display:none">
  <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4 flex flex-col sm:flex-row items-center gap-3 sm:gap-6">
    <div class="flex-1 text-center sm:text-left">
      <p class="text-xs sm:text-sm text-premium-mink">
        This site uses cookies to improve your experience. Read our 
        <a href="<?= url('privacy-policy') ?>" class="text-premium-burgundy underline font-medium hover:text-premium-crimson" target="_blank">Privacy Policy</a>.
      </p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
      <button onclick="acceptPrivacy()" class="bg-premium-burgundy hover:bg-premium-crimson text-white text-xs font-bold px-5 py-2 rounded-lg transition-all hover:shadow-md">
        Accept
      </button>
      <button onclick="declinePrivacy()" class="text-xs font-medium text-premium-taupe hover:text-premium-charcoal px-3 py-2 transition-colors">
        Decline
      </button>
    </div>
  </div>
</div>

<script>
(function(){
  var accepted = localStorage.getItem('privacy_accepted');
  if (accepted === '1') return;
  if (document.cookie.indexOf('privacy_accepted=1') !== -1) { localStorage.setItem('privacy_accepted', '1'); return; }
  setTimeout(function(){
    document.getElementById('privacy-banner').style.display = 'block';
  }, 4000);
})();
function acceptPrivacy(){
  localStorage.setItem('privacy_accepted', '1');
  document.cookie = 'privacy_accepted=1; path=/; max-age=' + (365*24*60*60);
  document.getElementById('privacy-banner').style.display = 'none';
}
function declinePrivacy(){
  document.getElementById('privacy-banner').style.display = 'none';
}
</script>

</body>
</html>