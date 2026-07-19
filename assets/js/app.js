/**
 * Wedding Your — Main Frontend JavaScript
 * E-commerce site — vanilla JS (ES6+)
 *
 * UI labels are in French, code comments in English.
 */

(function () {
  'use strict';

  /* ─────────────────────────────────────
     Configuration
     ───────────────────────────────────── */

  const BASE_URL = window.BASE_URL || '';
  const CSRF_TOKEN =
    window.CSRF_TOKEN ||
    (document.querySelector('meta[name="csrf-token"]') &&
      document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

  /* ─────────────────────────────────────
     Helpers
     ───────────────────────────────────── */

  function debounce(fn, delay) {
    let timer;
    return function (...args) {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  function csrfHeaders() {
    return {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': CSRF_TOKEN,
    };
  }

  function csrfBody() {
    return { _token: CSRF_TOKEN };
  }

  function apiPost(url, data) {
    return fetch(BASE_URL + url, {
      method: 'POST',
      headers: csrfHeaders(),
      body: JSON.stringify({ ...data, ...csrfBody() }),
    }).then((r) => r.json());
  }

  function apiGet(url) {
    return fetch(BASE_URL + url, {
      method: 'GET',
      headers: { Accept: 'application/json' },
    }).then((r) => r.json());
  }

  /* ─────────────────────────────────────
     Flash Messages Toast
     ───────────────────────────────────── */

  function showFlash(message, type) {
    type = type || 'success';
    var toast = document.createElement('div');
    toast.className = 'flash-toast flash-toast--' + type;
    toast.textContent = message;
    toast.style.cssText =
      'position:fixed;bottom:24px;right:24px;z-index:9999;padding:14px 24px;border-radius:6px;color:#fff;font-weight:600;box-shadow:0 4px 14px rgba(0,0,0,0.15);transition:all 0.35s ease;opacity:0;transform:translateY(20px);';
    if (type === 'success') toast.style.background = '#28a745';
    else if (type === 'error') toast.style.background = '#dc3545';
    else toast.style.background = '#ffc107';
    document.body.appendChild(toast);
    requestAnimationFrame(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    });
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(20px)';
      setTimeout(() => toast.remove(), 350);
    }, 4000);
  }

  /* ─────────────────────────────────────
     Instant AJAX Search
     ───────────────────────────────────── */

  (function initSearch() {
    var input = document.getElementById('search-input');
    if (!input) return;

    var dropdown = document.createElement('div');
    dropdown.className = 'search-dropdown';
    dropdown.style.cssText =
      'position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #ddd;border-top:0;z-index:1000;max-height:400px;overflow-y:auto;display:none;box-shadow:0 4px 12px rgba(0,0,0,0.1);';
    input.parentNode.style.position = 'relative';
    input.parentNode.appendChild(dropdown);

    function closeDropdown() {
      dropdown.style.display = 'none';
    }

    var doSearch = debounce(function () {
      var q = input.value.trim();
      if (q.length < 2) {
        closeDropdown();
        return;
      }
      apiGet('/search?q=' + encodeURIComponent(q))
        .then(function (data) {
          dropdown.innerHTML = '';
          if (!data.results || data.results.length === 0) {
            var empty = document.createElement('div');
            empty.style.cssText = 'padding:12px 16px;color:#999;';
            empty.textContent = 'No results found';
            dropdown.appendChild(empty);
            dropdown.style.display = 'block';
            return;
          }
          data.results.forEach(function (item) {
            var el = document.createElement('a');
            el.href = item.url || BASE_URL + '/product/' + item.slug;
            el.style.cssText =
              'display:flex;align-items:center;gap:12px;padding:10px 16px;text-decoration:none;color:#333;border-bottom:1px solid #f0f0f0;transition:background 0.15s;';
            el.addEventListener('mouseenter', function () {
              el.style.background = '#f7f7f7';
            });
            el.addEventListener('mouseleave', function () {
              el.style.background = '';
            });
            if (item.image_url) {
              var img = document.createElement('img');
              img.src = item.image_url;
              img.alt = item.name;
              img.style.cssText = 'width:44px;height:44px;object-fit:cover;border-radius:4px;';
              el.appendChild(img);
            }
            var info = document.createElement('div');
            info.style.cssText = 'flex:1;min-width:0;';
            var name = document.createElement('div');
            name.style.cssText = 'font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
            name.textContent = item.name;
            info.appendChild(name);
            if (item.price) {
              var price = document.createElement('div');
              price.style.cssText = 'font-size:13px;color:#28a745;';
              price.textContent = item.price + ' €';
              info.appendChild(price);
            }
            el.appendChild(info);
            el.addEventListener('click', function (e) {
              closeDropdown();
            });
            dropdown.appendChild(el);
          });
          dropdown.style.display = 'block';
        })
        .catch(function () {
          closeDropdown();
        });
    }, 300);

    input.addEventListener('input', doSearch);

    document.addEventListener('click', function (e) {
      if (!input.parentNode.contains(e.target)) closeDropdown();
    });

    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeDropdown();
    });
  })();

  /* ─────────────────────────────────────
     Cart AJAX Operations
     ───────────────────────────────────── */

  function updateCartBadge(count) {
    var badges = document.querySelectorAll('.cart-count');
    badges.forEach(function (el) {
      el.textContent = count;
      el.style.display = count > 0 ? '' : 'none';
    });
  }

  function getCartCount() {
    apiGet('/cart/count')
      .then(function (data) {
        if (typeof data.count === 'number') updateCartBadge(data.count);
      })
      .catch(function () {});
  }

  // — Add to cart
  window.addToCart = function (productId, quantity, variantId) {
    quantity = quantity || 1;
    var payload = { product_id: productId, quantity: quantity };
    if (variantId) payload.variant_id = variantId;
    return apiPost('/cart/add', payload)
      .then(function (data) {
        if (data.success) {
          showFlash('Product added to cart', 'success');
          if (typeof data.count === 'number') updateCartBadge(data.count);
          else getCartCount();
        } else {
          showFlash(data.message || 'Error adding to cart', 'error');
        }
        return data;
      })
      .catch(function () {
        showFlash('Network error', 'error');
      });
  };

  // — Update cart item quantity
  window.updateCart = function (itemId, quantity) {
    return apiPost('/cart/update', { item_id: itemId, quantity: quantity })
      .then(function (data) {
        if (data.success) {
          if (typeof data.count === 'number') updateCartBadge(data.count);
          if (data.cartHtml) {
            var container = document.querySelector('.cart-container');
            if (container) container.innerHTML = data.cartHtml;
          }
        } else {
          showFlash(data.message || 'Update error', 'error');
        }
        return data;
      })
      .catch(function () {
        showFlash('Network error', 'error');
      });
  };

  // — Remove from cart
  window.removeFromCart = function (itemId) {
    return apiPost('/cart/remove', { item_id: itemId })
      .then(function (data) {
        if (data.success) {
          showFlash('Item removed from cart', 'success');
          if (typeof data.count === 'number') updateCartBadge(data.count);
          if (data.cartHtml) {
            var container = document.querySelector('.cart-container');
            if (container) container.innerHTML = data.cartHtml;
          } else {
            var row = document.querySelector('[data-cart-item="' + itemId + '"]');
            if (row) row.remove();
          }
        } else {
          showFlash(data.message || 'Error removing item', 'error');
        }
        return data;
      })
      .catch(function () {
        showFlash('Network error', 'error');
      });
  };

  // — Apply coupon
  window.applyCoupon = function (code) {
    if (!code || !code.trim()) {
      showFlash('Veuillez entrer un code promo', 'error');
      return Promise.resolve();
    }
    return apiPost('/cart/coupon', { code: code.trim() })
      .then(function (data) {
        if (data.success) {
          showFlash('Code promo appliqué', 'success');
          if (data.cartHtml) {
            var container = document.querySelector('.cart-container');
            if (container) container.innerHTML = data.cartHtml;
          } else if (data.refresh) {
            location.reload();
          }
        } else {
          showFlash(data.message || 'Code promo invalide', 'error');
        }
        return data;
      })
      .catch(function () {
        showFlash('Network error', 'error');
      });
  };

  // — Remove coupon
  window.removeCoupon = function () {
    return apiPost('/cart/coupon/remove', {})
      .then(function (data) {
        if (data.success) {
          showFlash('Code promo retiré', 'success');
          if (data.cartHtml) {
            var container = document.querySelector('.cart-container');
            if (container) container.innerHTML = data.cartHtml;
          } else if (data.refresh) {
            location.reload();
          }
        } else {
          showFlash(data.message || 'Error', 'error');
        }
        return data;
      })
      .catch(function () {
        showFlash('Network error', 'error');
      });
  };

  /* ─────────────────────────────────────
     Wishlist AJAX
     ───────────────────────────────────── */

  window.toggleWishlist = function (productId) {
    var btn = document.querySelector('[data-wishlist="' + productId + '"]');
    return apiPost('/account/wishlist/add', { product_id: productId })
      .then(function (data) {
        if (data.success) {
          if (btn) {
            btn.classList.toggle('is-active', data.action === 'added');
            btn.classList.toggle('is-inactive', data.action === 'removed');
          }
          if (typeof data.count === 'number') {
            var badges = document.querySelectorAll('.wishlist-count');
            badges.forEach(function (el) {
              el.textContent = data.count;
              el.style.display = data.count > 0 ? '' : 'none';
            });
          }
          var msg =
            data.action === 'added'
              ? 'Added to wishlist'
              : 'Removed from wishlist';
          showFlash(msg, 'success');
        } else {
          showFlash(data.message || 'Error', 'error');
        }
        return data;
      })
      .catch(function () {
        showFlash('Network error', 'error');
      });
  };

  /* ─────────────────────────────────────
     Compare AJAX
     ───────────────────────────────────── */

  window.toggleCompare = function (productId) {
    var btn = document.querySelector('[data-compare="' + productId + '"]');
    return apiPost('/compare/toggle', { product_id: productId })
      .then(function (data) {
        if (data.success) {
          if (btn) btn.classList.toggle('is-active', data.action === 'added');
          if (typeof data.count === 'number') {
            var badges = document.querySelectorAll('.compare-count');
            badges.forEach(function (el) {
              el.textContent = data.count;
              el.style.display = data.count > 0 ? '' : 'none';
            });
          }
          var msg =
            data.action === 'added'
              ? 'Added to comparison'
              : 'Removed from comparison';
          showFlash(msg, 'success');
        } else {
          showFlash(data.message || 'Error', 'error');
        }
        return data;
      })
      .catch(function () {
        showFlash('Network error', 'error');
      });
  };

  /* ─────────────────────────────────────
     Quantity Selector (+ / -)
     ───────────────────────────────────── */

  document.addEventListener('click', function (e) {
    var minus = e.target.closest('.qty-minus');
    var plus = e.target.closest('.qty-plus');

    if (!minus && !plus) return;

    var ctrl = (minus || plus).closest('.qty-selector');
    if (!ctrl) return;
    var input = ctrl.querySelector('.qty-input');
    if (!input) return;

    var min = parseInt(input.getAttribute('min'), 10) || 1;
    var max = parseInt(input.getAttribute('max'), 10) || 9999;
    var step = parseInt(input.getAttribute('step'), 10) || 1;
    var val = parseInt(input.value, 10) || min;

    if (minus) val = Math.max(min, val - step);
    if (plus) val = Math.min(max, val + step);

    input.value = val;

    // If we're on the cart page, auto-update via AJAX
    var cartItem = ctrl.closest('[data-cart-item]');
    if (cartItem) {
      var itemId = cartItem.getAttribute('data-cart-item');
      if (typeof window.updateCart === 'function') {
        window.updateCart(itemId, val);
      }
    }
  });

  /* ─────────────────────────────────────
     Image Gallery (product detail)
     ───────────────────────────────────── */

  document.addEventListener('click', function (e) {
    var thumb = e.target.closest('.gallery-thumb');
    if (!thumb) return;

    var mainImg = document.getElementById('main-image');
    if (!mainImg) return;

    var fullSrc = thumb.getAttribute('data-full') || thumb.getAttribute('src');
    if (fullSrc) {
      mainImg.src = fullSrc;
    }
    // Remove active class from all thumbs, add to clicked
    var thumbs = thumb.parentNode.querySelectorAll('.gallery-thumb');
    thumbs.forEach(function (t) {
      t.classList.remove('active');
    });
    thumb.classList.add('active');
  });

  /* ─────────────────────────────────────
     Mobile Menu Toggle
     ───────────────────────────────────── */

  document.addEventListener('click', function (e) {
    var hamburger = e.target.closest('.hamburger, .hamburger-btn, [data-toggle="mobile-menu"]');
    if (!hamburger) return;

    var menu = document.querySelector('.mobile-menu');
    if (menu) {
      menu.classList.toggle('is-open');
      document.body.classList.toggle('mobile-menu-open');
    }
  });

  /* ─────────────────────────────────────
     Filter Auto-Submit
     ───────────────────────────────────── */

  document.addEventListener('change', function (e) {
    var sort = e.target.closest('.filter-sort, [data-filter="sort"]');
    if (sort) {
      var form = sort.closest('form');
      if (form) {
        form.submit();
      }
    }
  });

  document.addEventListener('click', function (e) {
    var apply = e.target.closest('.filter-apply, [data-filter="apply"]');
    if (!apply) return;
    var form = apply.closest('form');
    if (form) form.submit();
  });

  /* ─────────────────────────────────────
     Init on DOM ready
     ───────────────────────────────────── */

  document.addEventListener('DOMContentLoaded', function () {
    getCartCount();

    // Read initial count from server for wishlist & compare badges
    if (document.querySelector('.wishlist-count')) {
      apiGet('/account/wishlist/ids')
        .then(function (d) {
          if (d.success && typeof d.count === 'number') {
            window._wishlistIds = d.ids || [];
            var el = document.querySelector('.wishlist-count');
            if (el) {
              el.textContent = d.count;
              el.style.display = d.count > 0 ? '' : 'none';
            }
            (d.ids || []).forEach(function (id) {
              var btn = document.querySelector('[data-wishlist="' + id + '"]');
              if (btn) {
                btn.classList.add('is-active');
                btn.classList.remove('is-inactive');
              }
            });
          }
        })
        .catch(function () {});
    }

    if (document.querySelector('.compare-count')) {
      apiGet('/compare/count')
        .then(function (d) {
          if (typeof d.count === 'number') {
            var el = document.querySelector('.compare-count');
            if (el) {
              el.textContent = d.count;
              el.style.display = d.count > 0 ? '' : 'none';
            }
          }
        })
        .catch(function () {});
    }
  });
})();
