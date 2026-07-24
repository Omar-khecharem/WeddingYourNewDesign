<?php
/**
 * Main Layout
 * 
 * Primary layout template that includes:
 *   - HTML head (header.php)
 *   - Navigation (navbar.php)
 *   - Flash message rendering
 *   - Page content block
 *   - Footer (footer.php)
 *
 * Variables expected:
 *   - $content: The rendered page content
 *   - $metaTitle, $metaDescription, $metaKeywords: SEO data
 *   - $appName, $appTagline, etc.: Application settings
 */

// Include header (all HTML head content)
require __DIR__ . DS . 'header.php';
?>

<!-- ============================================ -->
<!-- NAVBAR -->
<!-- ============================================ -->
<?php
$navbarData = [
    'cartCount' => $cartCount ?? 0,
    'cartTotal' => $cartTotal ?? 0,
    'wishlistCount' => $wishlistCount ?? 0,
    'compareCount' => $compareCount ?? 0,
    'isLoggedIn' => $isLoggedIn ?? false,
    'authUser' => $authUser ?? null,
    'appName' => $appName ?? APP_NAME,
    'appTagline' => $appTagline ?? '',
    'facebookUrl' => $facebookUrl ?? '#',
    'instagramUrl' => $instagramUrl ?? '#',
    'youtubeUrl' => $youtubeUrl ?? '#',
];
echo \App\Core\View::include('layouts.navbar', $navbarData);
?>

<!-- ============================================ -->
<!-- FLASH MESSAGES -->
<!-- ============================================ -->
<div id="flash-messages">
    <?= \App\Components\Alert::renderFlash() ?>
</div>

<!-- ============================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================ -->
<main>
    <?= $content ?? '' ?>
</main>

<!-- ============================================ -->
<!-- FOOTER -->
<!-- ============================================ -->
<?php
$footerData = [
    'appName' => $appName ?? APP_NAME,
    'contactEmail' => $contactEmail ?? CONTACT_EMAIL,
    'contactPhone' => $contactPhone ?? CONTACT_PHONE,
    'contactAddress' => $contactAddress ?? CONTACT_ADDRESS,
    'whatsappLink' => $whatsappLink ?? WHATSAPP_LINK,
    'facebookUrl' => $facebookUrl ?? SOCIAL_FACEBOOK,
    'instagramUrl' => $instagramUrl ?? SOCIAL_INSTAGRAM,
    'youtubeUrl' => $youtubeUrl ?? SOCIAL_YOUTUBE,
    'currentYear' => $currentYear ?? date('Y'),
];
echo \App\Core\View::include('layouts.footer', $footerData);
?>
