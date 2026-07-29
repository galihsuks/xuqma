<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class WebController extends BaseController
{
    public function home()
    {
        $categories = $this->getStorefrontCategories();
        $products = $this->getStorefrontProducts();
        $featuredProducts = $this->getStorefrontFeaturedProducts();
        $articles = $this->getStorefrontArticles();

        return view('web/home', $this->buildPageData([
            'activeNav' => 'home',
            'categories' => array_slice($categories, 0, 4),
            'description' => 'IT commerce storefront for accessories, headphones, monitors, VGA, RAM, and other computer components with SEO-friendly public pages.',
            'featuredProducts' => $featuredProducts !== [] ? $featuredProducts : array_slice($products, 0, 4),
            'heroBadge' => 'IT Commerce Storefront',
            'metaType' => 'website',
            'latestArticles' => array_slice($articles, 0, 3),
            'pageTitle' => 'Build your setup with curated IT gear, accessories, and upgrade-ready components',
            'pageSubtitle' => 'Use CodeIgniter for SEO-focused storefront pages, and hand off cart, order, and admin workflows to React under /app.',
            'seoTitle' => 'Xuqma | IT accessories, components, and setup gear',
            'stats' => [
                ['label' => 'Top categories', 'value' => 'Phone, audio, PC, display'],
                ['label' => 'Operational split', 'value' => 'Storefront + React app'],
                ['label' => 'Launch path', 'value' => '/app/customer and /app/admin'],
            ],
        ]));
    }

    public function shop()
    {
        return view('web/shop', $this->buildPageData([
            'activeNav' => 'shop',
            'categories' => $this->getStorefrontCategories(),
            'description' => 'Browse SEO-friendly product discovery pages for phone accessories, headphones, VGA, RAM, monitor, and desk setup gear.',
            'metaType' => 'website',
            'pageTitle' => 'Shop modern IT gear for work, play, and content creation',
            'pageSubtitle' => 'A storefront layer designed for search visibility, product discovery, and category landing pages.',
            'products' => $this->getStorefrontProducts(),
            'seoTitle' => 'Shop IT Products | Xuqma',
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

    private function buildPageData(array $data): array
    {
        $appName = getenv('app.name') ?: 'Xuqma';
        $path = '/' . ltrim((string) $this->request->getPath(), '/');
        $currentPath = $path === '//' ? '/' : $path;
        $baseUrl = rtrim(base_url(), '/');
        $canonicalUrl = $currentPath === '/' ? $baseUrl . '/' : $baseUrl . $currentPath;

        return array_merge([
            'activeNav' => '',
            'appName' => $appName,
            'canonicalUrl' => $canonicalUrl,
            'currentPath' => $currentPath,
            'description' => $appName,
            'metaImage' => $baseUrl . '/favicon.ico',
            'metaType' => 'website',
            'seoTitle' => $appName,
        ], $data);
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
        $products = $this->productModel->getFeaturedVisibleProducts(4);
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
