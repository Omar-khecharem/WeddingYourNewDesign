<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Banner;
use App\Models\Deal;
use App\Models\CategoryCard;
use App\Models\GalleryItem;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta(
            'WeddingYour - Premium Bengali Wedding Mukut, Topor & Wedding Accessories',
            'India\'s leading Bengali wedding marketplace for premium handmade Topor, Mukut, bridal accessories, and traditional Bengali wedding items.',
            'WeddingYour, Bengali Wedding, Bengali Wedding Planner, Bengali Wedding Photography, Bengali Wedding Dresses, topor, mukut, sholapith, Bengali bride, Bengali groom'
        );

        $categories = Category::getActiveWithProductCount();
        $recentProducts = Product::getRecent(8);
        $featuredProducts = Product::getFeatured(4);
        $trendingProducts = Product::getTrending(4);
        $reviews = Review::getApproved(3);
        $banners = Banner::getAllActive();
        $deals = Deal::getActive();
        $categoryCards = CategoryCard::getActive();
        $categoryCardProducts = [];
        $fallbackPool = Product::getTrending(8) ?: Product::getFeatured(8) ?: Product::getRecent(8);
        $fallbackCount = max(count($fallbackPool), 1);
        foreach ($categoryCards as $i => $card) {
            if (!empty($card['category_id'])) {
                $products = Product::getByCategoryId((int)$card['category_id'], 4);
            } else {
                $products = [];
            }
            if (count($products) < 4) {
                $needed = 4 - count($products);
                for ($j = 0; $j < $needed; $j++) {
                    $idx = ($i * 4 + count($products) + $j) % $fallbackCount;
                    $products[] = $fallbackPool[$idx] ?? [];
                }
            }
            $categoryCardProducts[$i] = $products;
        }
        // Also build fallback for default (no-DB) cards
        $defaultCardFallback = [];
        for ($i = 0; $i < 4; $i++) {
            $slice = [];
            for ($j = 0; $j < 4; $j++) {
                $idx = ($i * 4 + $j) % $fallbackCount;
                $slice[] = $fallbackPool[$idx] ?? [];
            }
            $defaultCardFallback[$i] = $slice;
        }

        $galleryItems = GalleryItem::getActive();
        $whatsappNumber = Setting::get('whatsapp_number', '+919830136355');

        $subcategories = [];
        try {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $subcategories = $pdo->query("SELECT sc.id, sc.name, sc.slug, sc.image, sc.category_id, c.slug AS cat_slug FROM sg_subcategories sc JOIN sg_categories c ON c.id = sc.category_id WHERE sc.status = 1 ORDER BY c.sort_order, sc.sort_order, sc.name")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Hero text from settings
        $heroTitle = Setting::get('hero_title', 'Premium Wedding Mukut');
        $heroSubtitle = Setting::get('hero_subtitle', 'New Collection 2026');
        $heroDescription = Setting::get('hero_description', '');
        $heroButtonText = Setting::get('hero_button_text', 'Shop Now');
        $heroButtonLink = Setting::get('hero_button_link', '/products');

        $allProductIds = array_merge(
            array_column($recentProducts, 'id'),
            array_column($featuredProducts, 'id'),
            array_column($trendingProducts, 'id')
        );
        $homeRatings = \App\Models\Review::getBatchRatings($allProductIds);

        return $this->view('home.index', compact(
            'categories', 'subcategories', 'recentProducts', 'featuredProducts', 'trendingProducts', 'reviews',
            'banners', 'deals', 'categoryCards', 'categoryCardProducts', 'defaultCardFallback',
            'galleryItems',
            'whatsappNumber', 'heroTitle', 'heroSubtitle', 'heroDescription', 'heroButtonText', 'heroButtonLink',
            'homeRatings'
        ));
    }
}
