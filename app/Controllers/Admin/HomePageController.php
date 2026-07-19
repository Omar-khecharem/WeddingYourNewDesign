<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Helpers\Security;
use App\Helpers\Image;

class HomePageController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Home Page Management');

        $categories = Category::getActive();
        $filter = $request->input('filter', 'featured');
        $categorySlug = $request->input('category', '');
        $subcategorySlug = $request->input('subcategory', '');

        $products = [];

        if ($categorySlug || $subcategorySlug) {
            $filters = [];
            if ($categorySlug) $filters['category'] = $categorySlug;
            if ($subcategorySlug) $filters['subcategory'] = $subcategorySlug;
            $result = Product::getFiltered($filters, 1, 50);
            $products = $result['products'] ?? [];
        } else {
            $products = match ($filter) {
                'featured' => Product::getFeatured(20),
                'trending' => Product::getTrending(20),
                'recent' => Product::getRecent(20),
                default => Product::getFeatured(20),
            };
        }

        $homepageSettings = Setting::getGroup('homepage');

        return $this->view('admin.homepage.index', compact(
            'categories', 'products', 'filter', 'categorySlug', 'subcategorySlug', 'homepageSettings'
        ));
    }

    public function toggleFeatured(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $redirect = $request->input('redirect', url('13091998/homepage'));
        $product = Product::find($id);
        if ($product) {
            $wasFeatured = (bool)$product->is_featured;
            $product->fill(['is_featured' => $wasFeatured ? 0 : 1]);
            $product->save();
            $this->flash('success', $wasFeatured ? 'Product removed from featured.' : 'Product added to featured.');
        }
        $this->redirect($redirect);
    }

    public function toggleTrending(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $redirect = $request->input('redirect', url('13091998/homepage'));
        $product = Product::find($id);
        if ($product) {
            $wasTrending = (bool)$product->is_trending;
            $product->fill(['is_trending' => $wasTrending ? 0 : 1]);
            $product->save();
            $this->flash('success', $wasTrending ? 'Product removed from trending.' : 'Product added to trending.');
        }
        $this->redirect($redirect);
    }

    public function saveSettings(Request $request, Response $response): void
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $settingValues = $request->input('setting', []);

        foreach ($settingValues as $key => $value) {
            $fileInputName = 'setting_file_' . $key;
            if ($request->hasFile($fileInputName)) {
                $file = $request->file($fileInputName);
                $filename = Image::upload($file, UPLOADS_DIR, $key);
                if ($filename) $value = $filename;
            }
            $removeInputName = 'remove_setting_' . $key;
            if ($request->input($removeInputName)) {
                $value = '';
            }
            $stmt = $pdo->prepare("UPDATE sg_settings SET value = :val WHERE `key` = :k");
            $stmt->execute([':val' => $value, ':k' => $key]);
        }

        clearSiteCache();
        $this->flash('success', 'Homepage settings saved.');
        $this->redirect(url('13091998/homepage'));
    }
}
