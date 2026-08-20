<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class WebController extends BaseController
{
    private const CART_FEEDBACK_FLASH_KEY = 'cart_feedback';

    public function home()
    {
        $categories = $this->getStorefrontCategories();
        $featuredProducts = $this->getStorefrontFeaturedProducts();
        $articles = $this->getStorefrontArticles();

        return view('web/home', $this->buildPageData([
            'activeNav' => 'home',
            'categories' => $categories,
            'description' => 'IT commerce storefront for accessories, headphones, monitors, VGA, RAM, and other computer components with SEO-friendly public pages.',
            'featuredProducts' => $featuredProducts,
            'metaType' => 'website',
            'latestArticles' => array_slice($articles, 0, 3),
            'seoTitle' => 'Xuqma | IT accessories, components, and setup gear',
        ]));
    }

    public function shop()
    {
        return view('web/shop', $this->buildPageData([
            'activeNav' => 'shop',
            'categories' => $this->getStorefrontCategories(),
            'description' => 'Shop phone accessories, headphones, monitors, RAM, GPUs, and everyday setup gear in one curated IT storefront.',
            'metaType' => 'website',
            'pageTitle' => '',
            'pageSubtitle' => 'Discover accessories, audio gear, and PC components selected to help you compare faster and shop with confidence.',
            'products' => $this->getStorefrontProducts(),
            'seoTitle' => 'Shop IT Products | Xuqma',
        ]));
    }

    public function about()
    {
        return view('web/about', $this->buildPageData([
            'activeNav' => 'about',
            'description' => 'Learn how Xuqma helps shoppers discover IT accessories, PC components, and practical buying guides through a storefront built for clarity and confidence.',
            'metaType' => 'website',
            'pageTitle' => 'About Xuqma',
            'pageSubtitle' => 'A modern IT storefront built to make buying tech accessories and components feel clearer, faster, and more confident.',
            'seoTitle' => 'About Xuqma | IT Storefront and Buying Guides',
        ]));
    }

    public function category(string $slug)
    {
        $categories = $this->getStorefrontCategories();
        $category = null;

        foreach ($categories as $item) {
            if ($item['slug'] === $slug) {
                $category = $item;
                break;
            }
        }

        if ($category === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $products = $this->getStorefrontProductsByCategorySlug($slug);

        return view('web/category', $this->buildPageData([
            'activeNav' => 'shop',
            'category' => $category,
            'description' => $category['description'],
            'metaType' => 'website',
            'pageTitle' => $category['name'],
            'pageSubtitle' => $category['description'],
            'products' => $products,
            'seoTitle' => $category['name'] . ' | Xuqma',
        ]));
    }

    public function productDetail(string $slug)
    {
        $product = $this->getStorefrontProductBySlug($slug);

        if ($product === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $relatedProducts = array_values(array_filter(
            $this->getStorefrontProducts(),
            static fn (array $item): bool => $item['category_slug'] === $product['category_slug'] && $item['slug'] !== $product['slug'],
        ));

        return view('web/product_detail', $this->buildPageData([
            'activeNav' => 'shop',
            'description' => $product['summary'],
            'metaType' => 'product',
            'product' => $product,
            'relatedProducts' => array_slice($relatedProducts, 0, 3),
            'seoTitle' => $product['name'] . ' | Xuqma',
        ]));
    }

    public function articles()
    {
        return view('web/articles', $this->buildPageData([
            'activeNav' => 'articles',
            'articles' => $this->getStorefrontArticles(),
            'description' => 'Explore IT buying guides, workspace setup notes, and component explainers built as SEO-friendly content pages.',
            'metaType' => 'website',
            'pageTitle' => 'Guides, buying notes, and IT commerce insights',
            'pageSubtitle' => 'Use articles to bring search traffic into product education, setup recommendations, and trust-building storefront content.',
            'seoTitle' => 'Articles | Xuqma',
        ]));
    }

    public function articleDetail(string $slug)
    {
        $article = $this->getStorefrontArticleBySlug($slug);

        if ($article === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('web/article_detail', $this->buildPageData([
            'activeNav' => 'articles',
            'article' => $article,
            'description' => $article['excerpt'],
            'metaType' => 'article',
            'seoTitle' => $article['title'] . ' | Xuqma',
        ]));
    }

    public function addProductToCart(string $productId)
    {
        if ($redirect = $this->guardStorefrontCartAccess()) {
            return $redirect;
        }

        $product = $this->productModel->getVisibleProductById($productId);
        if ($product === null) {
            return $this->redirectBackWithCartFeedback('error', 'Product unavailable', 'This product is no longer available in the storefront.');
        }

        $cart = $this->cartModel->getOrCreateCartByUserId((string) session('auth_user_id'));
        $existingItem = $this->cartItemModel->getItemByCartAndProductId((string) $cart['id'], $productId);

        if ($existingItem) {
            $this->cartItemModel->update((string) $existingItem['id'], [
                'qty' => (int) $existingItem['qty'] + 1,
            ]);
            $message = $product['name'] . ' quantity updated in your cart.';
        } else {
            $this->cartItemModel->insertCartItem([
                'cart_id' => (string) $cart['id'],
                'product_id' => $productId,
                'qty' => 1,
            ]);
            $message = $product['name'] . ' has been added to your cart.';
        }

        $this->syncStorefrontCartTotalQty((string) $cart['id']);

        return $this->redirectBackWithCartFeedback('success', 'Cart updated', $message);
    }

    public function incrementProductCartQty(string $productId)
    {
        if ($redirect = $this->guardStorefrontCartAccess()) {
            return $redirect;
        }

        $cart = $this->cartModel->getCartByUserId((string) session('auth_user_id'));
        if ($cart === null) {
            return $this->redirectBackWithCartFeedback('error', 'Cart not found', 'Your cart is currently empty.');
        }

        $item = $this->cartItemModel->getItemByCartAndProductId((string) $cart['id'], $productId);
        if ($item === null) {
            return $this->redirectBackWithCartFeedback('error', 'Cart item not found', 'This product is not in your cart yet.');
        }

        $this->cartItemModel->update((string) $item['id'], [
            'qty' => (int) $item['qty'] + 1,
        ]);
        $this->syncStorefrontCartTotalQty((string) $cart['id']);

        return $this->redirectBackWithCartFeedback('success', 'Cart updated', 'Product quantity increased.');
    }

    public function decrementProductCartQty(string $productId)
    {
        if ($redirect = $this->guardStorefrontCartAccess()) {
            return $redirect;
        }

        $cart = $this->cartModel->getCartByUserId((string) session('auth_user_id'));
        if ($cart === null) {
            return $this->redirectBackWithCartFeedback('error', 'Cart not found', 'Your cart is currently empty.');
        }

        $item = $this->cartItemModel->getItemByCartAndProductId((string) $cart['id'], $productId);
        if ($item === null) {
            return $this->redirectBackWithCartFeedback('error', 'Cart item not found', 'This product is not in your cart yet.');
        }

        $nextQty = (int) $item['qty'] - 1;
        if ($nextQty <= 0) {
            $this->cartItemModel->delete((string) $item['id']);
            $message = 'Product removed from your cart.';
        } else {
            $this->cartItemModel->update((string) $item['id'], [
                'qty' => $nextQty,
            ]);
            $message = 'Product quantity decreased.';
        }

        $this->syncStorefrontCartTotalQty((string) $cart['id']);

        return $this->redirectBackWithCartFeedback('success', 'Cart updated', $message);
    }

    private function buildPageData(array $data): array
    {
        $appName = getenv('app.name') ?: 'Xuqma';
        $path = '/' . ltrim((string) $this->request->getPath(), '/');
        $currentPath = $path === '//' ? '/' : $path;
        $baseUrl = rtrim(base_url(), '/');
        $canonicalUrl = $currentPath === '/' ? $baseUrl . '/' : $baseUrl . $currentPath;
        $isMobile = $this->request->getUserAgent()->isMobile();
        $isLoggedIn = (bool) session('is_authenticated');
        $loginRoleCode = (string) session('auth_role_code');
        $isCustomerRole = $loginRoleCode === 'C';
        $showCustomerAppNav = !$isLoggedIn || $isCustomerRole;
        $customerCartQtyMap = $isLoggedIn && $isCustomerRole
            ? $this->resolveCustomerCartQtyMap((string) session('auth_user_id'))
            : [];
        $customerCartCount = $isLoggedIn && $isCustomerRole
            ? $this->resolveCustomerCartCount((string) session('auth_user_id'))
            : 0;
        
        return array_merge([
            'activeNav' => '',
            'appName' => $appName,
            'appCartUrl' => $this->buildAppNavUrl('/app/customer/cart', $isLoggedIn),
            'appHistoryUrl' => $this->buildAppNavUrl('/app/customer/history', $isLoggedIn),
            'appOrdersUrl' => $this->buildAppNavUrl('/app/customer/orders', $isLoggedIn),
            'appProfileUrl' => $this->buildAppNavUrl('/app/customer/profile', $isLoggedIn),
            'cartFeedback' => session()->getFlashdata(self::CART_FEEDBACK_FLASH_KEY),
            'customerCartQtyMap' => $customerCartQtyMap,
            'canonicalUrl' => $canonicalUrl,
            'currentPath' => $currentPath,
            'customerCartCount' => $customerCartCount,
            'description' => $appName,
            'isMobile' => $isMobile,
            'isCustomerRole' => $isCustomerRole,
            'isLoggedIn' => $isLoggedIn,
            'loginRoleCode' => $loginRoleCode,
            'metaImage' => $baseUrl . '/favicon.ico',
            'metaType' => 'website',
            'seoTitle' => $appName,
            'showCustomerAppNav' => $showCustomerAppNav,
        ], $data);
    }

    private function buildAppNavUrl(string $targetPath, bool $isLoggedIn): string
    {
        if ($isLoggedIn) {
            return base_url($targetPath);
        }

        if (str_contains($targetPath, 'profile')) {
            return base_url('/login');
        }
        return base_url('/login?redirect=' . urlencode($targetPath));
    }

    private function resolveCustomerCartCount(string $userId): int
    {
        $resolvedUserId = trim($userId);
        if ($resolvedUserId === '') {
            return 0;
        }

        $cart = $this->cartModel->getCartByUserId($resolvedUserId);
        if ($cart === null) {
            return 0;
        }

        return (int) ($cart['total_qty'] ?? 0);
    }

    private function resolveCustomerCartQtyMap(string $userId): array
    {
        $resolvedUserId = trim($userId);
        if ($resolvedUserId === '') {
            return [];
        }

        $cart = $this->cartModel->getCartByUserId($resolvedUserId);
        if ($cart === null) {
            return [];
        }

        $items = $this->cartItemModel->getItemsByCartId((string) $cart['id']);
        $result = [];
        foreach ($items as $item) {
            $productId = (string) ($item['product_id'] ?? '');
            if ($productId === '') {
                continue;
            }

            $result[$productId] = (int) ($item['qty'] ?? 0);
        }

        return $result;
    }

    private function guardStorefrontCartAccess()
    {
        if (!(bool) session('is_authenticated')) {
            return redirect()->to(base_url('/login?redirect=' . urlencode($this->request->getServer('HTTP_REFERER') ? (string) parse_url((string) $this->request->getServer('HTTP_REFERER'), PHP_URL_PATH) : '/shop')));
        }

        if ((string) session('auth_role_code') !== 'C') {
            return $this->redirectBackWithCartFeedback('error', 'Customer access only', 'Only customer accounts can manage storefront cart items.');
        }

        return null;
    }

    private function syncStorefrontCartTotalQty(string $cartId): void
    {
        $items = $this->cartItemModel->getItemsByCartId($cartId);
        $totalQty = array_reduce($items, static function ($carry, array $item) {
            return $carry + (int) ($item['qty'] ?? 0);
        }, 0);

        $this->cartModel->updateTotalQty($cartId, $totalQty);
    }

    private function redirectBackWithCartFeedback(string $tone, string $title, string $message)
    {
        session()->setFlashdata(self::CART_FEEDBACK_FLASH_KEY, [
            'tone' => $tone,
            'title' => $title,
            'message' => $message,
        ]);

        return redirect()->back();
    }

    private function getStorefrontCategories(): array
    {
        $categories = $this->productCategoryModel->getVisibleCategories();
        return array_map(static function (array $category): array {
            return [
                'id' => $category['id'],
                'slug' => $category['slug'],
                'name' => $category['name'],
                'description' => $category['description'],
                'icon' => $category['icon'] ?: 'bi-grid',
            ];
        }, $categories);
    }

    private function getStorefrontProducts(): array
    {
        $products = $this->productModel->getVisibleProducts();
        return array_map(fn (array $product): array => $this->normalizeStorefrontProduct($product), $products);
    }

    private function getStorefrontFeaturedProducts(): array
    {
        $isMobile = $this->request->getUserAgent()->isMobile();
        $products = $this->productModel->getFeaturedVisibleProducts($isMobile ? 4 : 3);
        if ($products === []) {
            return [];
        }

        return array_map(fn (array $product): array => $this->normalizeStorefrontProduct($product), $products);
    }

    private function getStorefrontProductsByCategorySlug(string $slug): array
    {
        $products = $this->productModel->getVisibleProductsByCategorySlug($slug);
        return array_map(fn (array $product): array => $this->normalizeStorefrontProduct($product), $products);
    }

    private function getStorefrontProductBySlug(string $slug): ?array
    {
        $product = $this->productModel->getVisibleProductBySlug($slug);
        return $product ? $this->normalizeStorefrontProduct($product) : null;
    }

    private function getStorefrontArticles(): array
    {
        $articles = $this->articleModel->getPublishedArticles();
        return array_map(fn (array $article): array => $this->normalizeStorefrontArticle($article), $articles);
    }

    private function getStorefrontArticleBySlug(string $slug): ?array
    {
        $article = $this->articleModel->getPublishedArticleBySlug($slug);
        return $article ? $this->normalizeStorefrontArticle($article) : null;
    }

    private function normalizeStorefrontProduct(array $product): array
    {
        return [
            'id' => $product['id'],
            'slug' => $product['slug'],
            'name' => $product['name'],
            'category_slug' => $product['category_slug'],
            'category_name' => $product['category_name'],
            'price' => $this->formatRupiah($product['price']),
            'stock_badge' => $product['stock_badge'],
            'summary' => $product['summary'] ?? '',
            'highlight' => $product['highlight'] ?? ($product['summary'] ?? ''),
            'specs' => $this->extractSpecsFromDescription((string) ($product['description'] ?? '')),
        ];
    }

    private function normalizeStorefrontArticle(array $article): array
    {
        return [
            'id' => $article['id'],
            'slug' => $article['slug'],
            'title' => $article['title'],
            'excerpt' => $article['excerpt'] ?? '',
            'category' => $article['category'],
            'published_at' => $this->formatPublishedDate($article['published_at'] ?? null),
            'read_time' => $article['read_time'] ?? '5 min read',
            'content' => $this->normalizeArticleContent($article['content'] ?? null, $article['excerpt'] ?? ''),
        ];
    }

    private function extractSpecsFromDescription(string $description): array
    {
        $normalized = preg_replace("/\r\n|\r/", "\n", trim($description));
        if (!$normalized) {
            return [];
        }

        $parts = preg_split('/\n+|(?<=\.)\s+/', $normalized) ?: [];
        $specs = array_values(array_filter(array_map(
            static fn (string $item): string => trim($item),
            $parts,
        )));

        return array_slice($specs, 0, 4);
    }

    private function normalizeArticleContent(?string $content, string $excerpt): array
    {
        $normalized = preg_replace("/\r\n|\r/", "\n", trim((string) $content));
        if ($normalized) {
            $paragraphs = preg_split('/\n{2,}/', $normalized) ?: [];
            $result = array_values(array_filter(array_map(
                static fn (string $paragraph): string => trim($paragraph),
                $paragraphs,
            )));
            if ($result !== []) {
                return $result;
            }
        }

        return $excerpt !== '' ? [$excerpt] : [];
    }

    private function formatPublishedDate(?string $publishedAt): string
    {
        if (!$publishedAt) {
            return '';
        }

        $timestamp = strtotime($publishedAt);
        if ($timestamp === false) {
            return (string) $publishedAt;
        }

        return date('Y-m-d', $timestamp);
    }

    private function formatRupiah($amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }
}
