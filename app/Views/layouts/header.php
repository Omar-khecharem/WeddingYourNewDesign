<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?= e($metaTitle ?? DEFAULT_META_TITLE) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? DEFAULT_META_DESCRIPTION) ?>">
    <meta name="keywords" content="<?= e($metaKeywords ?? DEFAULT_META_KEYWORDS) ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= e($canonicalUrl ?? APP_URL . $_SERVER['REQUEST_URI']) ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($metaTitle ?? DEFAULT_META_TITLE) ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? DEFAULT_META_DESCRIPTION) ?>">
    <meta property="og:url" content="<?= e(APP_URL . $_SERVER['REQUEST_URI']) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e(\App\Models\Setting::get('site_name', APP_NAME)) ?>">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($metaTitle ?? DEFAULT_META_TITLE) ?>">
    <meta name="twitter:description" content="<?= e($metaDescription ?? DEFAULT_META_DESCRIPTION) ?>">
    
    <!-- Favicon -->
    <?php $siteFavicon = \App\Models\Setting::get('site_favicon', '') ?: 'site_favicon.png'; ?>
    <?php if ($siteFavicon): ?>
    <link rel="icon" href="<?= uploadUrl($siteFavicon) ?>">
    <?php else: ?>
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    <?php endif; ?>
    
    <!-- Preconnect for external resources -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">
    
    <!-- Compiled CSS (Tailwind + Custom) -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>?v=2">

    <!-- Google Search Console -->
    <meta name="google-site-verification" content="YOUR_VERIFICATION_CODE_HERE">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "WeddingYour",
      "url": "<?= APP_URL ?>",
      "logo": "<?= url('assets/images/logo.png') ?>",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?= CONTACT_PHONE ?>",
        "contactType": "customer service",
        "availableLanguage": ["English", "Bengali", "Hindi"]
      },
      "address": {
        "@type": "PostalAddress",
        "addressCountry": "IN"
      },
      "sameAs": [
        "<?= SOCIAL_FACEBOOK ?>",
        "<?= SOCIAL_INSTAGRAM ?>",
        "<?= SOCIAL_YOUTUBE ?>"
      ]
    }
    </script>

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "WeddingYour",
      "url": "<?= APP_URL ?>",
      "potentialAction": {
        "@type": "SearchAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "<?= url('products') ?>?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    <?php if (!empty($breadcrumb)): ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        <?php foreach ($breadcrumb as $i => $crumb): ?>
        {"@type":"ListItem","position":<?= $i+1 ?>,"name":"<?= e($crumb['label']) ?>"<?php if (!empty($crumb['url'])): ?>,"item":"<?= e($crumb['url']) ?>"<?php endif; ?>},
        <?php endforeach; ?>
      ]
    }
    </script>
    <?php endif; ?>

    <script>window.CSRF_TOKEN = '<?= \App\Helpers\Session::csrfToken() ?>'; window.APP_CURRENCY = '<?= APP_CURRENCY ?>';</script>

    <style>
        @keyframes sk-shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        html.sk img:not(.sk-loaded) {
            background: linear-gradient(90deg, #ececec 25%, #f5f5f5 50%, #ececec 75%);
            background-size: 200% 100%;
            animation: sk-shimmer 1.5s ease-in-out infinite;
        }
        html { scroll-behavior: smooth; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
        }
        .star-rating label { transition: color 0.15s ease; }
        .star-rating label:hover ~ label { color: #D1D5DB; }
        .star-rating label:hover svg,
        .star-rating label:hover ~ label svg { color: #D1D5DB; }
        .star-rating input:checked ~ label svg { color: #FACC15; }
        .btn-buy-grid { transition: all 0.25s cubic-bezier(0.25, 0.1, 0.25, 1); }
        .btn-buy-grid:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(184, 132, 90, 0.3); }
        .product-card { transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1); }
        .product-card .btn-add-cart-grid { opacity: 0; transform: translateY(4px); transition: all 0.25s cubic-bezier(0.25, 0.1, 0.25, 1); }
        .product-card:hover .btn-add-cart-grid { opacity: 1; transform: translateY(0); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out both; }
        .review-card { animation: fadeInUp 0.5s ease-out both; }
        .review-card:nth-child(1) { animation-delay: 0.1s; }
        .review-card:nth-child(2) { animation-delay: 0.2s; }
        .review-card:nth-child(3) { animation-delay: 0.3s; }
    </style>
    <script>
        (function(){var d=document.documentElement;d.classList.add('sk');var imgs=document.querySelectorAll('img[src]'),n=imgs.length,c=0;if(!n){d.classList.remove('sk');return;}function done(i){i.classList.add('sk-loaded');if(++c>=n)d.classList.remove('sk');}for(var i=0;i<n;i++){(function(img){if(img.complete&&img.naturalWidth>0){done(img);return;}var loaded=false;img.addEventListener('load',function(){if(!loaded){loaded=true;done(img);}});img.addEventListener('error',function(){if(!loaded){loaded=true;done(img);}});})(imgs[i]);}setTimeout(function(){d.classList.remove('sk');},5000);})();
    </script>
    <script>
    (function(){if('scrollBehavior' in document.documentElement.style)return;var links=document.querySelectorAll('a[href^="#"]');for(var i=0;i<links.length;i++){links[i].addEventListener('click',function(e){var href=this.getAttribute('href');if(href==='#')return;var target=document.getElementById(href.slice(1));if(target){e.preventDefault();var top=target.getBoundingClientRect().top+window.pageYOffset-80;var start=window.pageYOffset,dist=top-start,dur=600,startTime=null;function step(t){if(!startTime)startTime=t;var p=Math.min(1,(t-startTime)/dur),ease=p<0.5?2*p*p:-1+(4-2*p)*p;window.scrollTo(0,start+dist*ease);if(p<1)requestAnimationFrame(step);}requestAnimationFrame(step);}});}})();
    </script>
</head>
<body>
