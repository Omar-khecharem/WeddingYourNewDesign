<?php
/**
 * SEO Helper
 * 
 * Generates SEO-friendly meta tags, Open Graph tags,
 * Twitter Cards, structured data (JSON-LD), and sitemap generation.
 *
 * @package App\Helpers
 */

namespace App\Helpers;

class SEO
{
    private static array $tags = [];

    /**
     * Set meta title
     */
    public static function setTitle(string $title): void
    {
        self::$tags['title'] = $title;
    }

    /**
     * Set meta description
     */
    public static function setDescription(string $description): void
    {
        self::$tags['description'] = $description;
    }

    /**
     * Set meta keywords
     */
    public static function setKeywords(string $keywords): void
    {
        self::$tags['keywords'] = $keywords;
    }

    /**
     * Set canonical URL
     */
    public static function setCanonical(string $url): void
    {
        self::$tags['canonical'] = $url;
    }

    /**
     * Set Open Graph tags
     */
    public static function setOG(string $property, string $content): void
    {
        self::$tags['og:' . $property] = $content;
    }

    /**
     * Set Twitter Card tags
     */
    public static function setTwitter(string $name, string $content): void
    {
        self::$tags['twitter:' . $name] = $content;
    }

    /**
     * Set all SEO tags from an array
     */
    public static function setTags(array $tags): void
    {
        foreach ($tags as $key => $value) {
            self::$tags[$key] = $value;
        }
    }

    /**
     * Generate all meta tags HTML
     */
    public static function render(): string
    {
        $html = '';

        // Title
        $title = self::$tags['title'] ?? DEFAULT_META_TITLE;
        $html .= "<title>{$title}</title>\n";
        $html .= "<meta name=\"title\" content=\"{$title}\">\n";

        // Description
        $description = self::$tags['description'] ?? DEFAULT_META_DESCRIPTION;
        $html .= "<meta name=\"description\" content=\"{$description}\">\n";

        // Keywords
        $keywords = self::$tags['keywords'] ?? DEFAULT_META_KEYWORDS;
        $html .= "<meta name=\"keywords\" content=\"{$keywords}\">\n";

        // Canonical
        $canonical = self::$tags['canonical'] ?? APP_URL . $_SERVER['REQUEST_URI'];
        $html .= "<link rel=\"canonical\" href=\"{$canonical}\">\n";

        // Robots
        $robots = APP_ENV === 'production' ? 'index, follow' : 'noindex, nofollow';
        $html .= "<meta name=\"robots\" content=\"{$robots}\">\n";

        // Open Graph
        $ogDefaults = [
            'og:title' => $title,
            'og:description' => $description,
            'og:url' => $canonical,
            'og:type' => 'website',
            'og:site_name' => APP_NAME,
            'og:locale' => 'en_IN',
        ];

        foreach ($ogDefaults as $property => $content) {
            $value = self::$tags[$property] ?? $content;
            if ($value) {
                $html .= "<meta property=\"{$property}\" content=\"{$value}\">\n";
            }
        }

        // Twitter Card
        $twitterDefaults = [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $title,
            'twitter:description' => $description,
        ];

        foreach ($twitterDefaults as $name => $content) {
            $value = self::$tags[$name] ?? $content;
            if ($value) {
                $html .= "<meta name=\"{$name}\" content=\"{$value}\">\n";
            }
        }

        return $html;
    }

    /**
     * Generate JSON-LD structured data for organization
     */
    public static function organizationSchema(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => APP_NAME,
            'url' => APP_URL,
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => CONTACT_PHONE,
                'contactType' => 'customer service',
                'availableLanguage' => ['English', 'Bengali', 'Hindi']
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => CONTACT_ADDRESS,
                'addressCountry' => 'IN'
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Generate JSON-LD for product
     */
    public static function productSchema(array $product): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'description' => $product['short_description'] ?? '',
            'sku' => $product['sku'] ?? '',
            'offers' => [
                '@type' => 'Offer',
                'url' => url('product/' . ($product['slug'] ?? '')),
                'priceCurrency' => APP_CURRENCY_CODE,
                'price' => $product['sale_price'] ?? $product['regular_price'],
                'availability' => $product['stock_status'] === 'in_stock'
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition'
            ],
            'image' => isset($product['image']) ? url($product['image']) : ''
        ];

        if (!empty($product['rating'] ?? 0)) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product['rating'],
                'reviewCount' => $product['review_count'] ?? 0
            ];
        }

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Generate JSON-LD breadcrumbs
     */
    public static function breadcrumbSchema(array $crumbs): string
    {
        $items = [];
        foreach ($crumbs as $i => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb['label'],
                'item' => $crumb['url'] ?? ''
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Generate sitemap index
     */
    public static function generateSitemap(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $xml .= '<lastmod>' . ($url['lastmod'] ?? date('Y-m-d')) . '</lastmod>';
            $xml .= '<changefreq>' . ($url['changefreq'] ?? 'weekly') . '</changefreq>';
            $xml .= '<priority>' . ($url['priority'] ?? '0.5') . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';
        return $xml;
    }

    /**
     * Generate robots.txt content
     */
    public static function robotsTxt(): string
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /13091998/\n";
        $content .= "Disallow: /cart/\n";
        $content .= "Disallow: /checkout/\n";
        $content .= "Disallow: /account/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Sitemap: " . APP_URL . "/sitemap.xml\n";
        return $content;
    }

    /**
     * Get current page URL for SEO
     */
    public static function currentUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
