<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class WebController extends BaseController
{
    public function home()
    {
        return view('web/home', $this->buildPageData([
            'activeNav' => 'home',
            'description' => 'Base App Galih is a clean starter for SEO-friendly public pages and a React-powered admin panel.',
            'heroBadge' => 'Public Website',
            'metaType' => 'website',
            'pageTitle' => 'Build fast public pages and admin apps from one foundation',
            'pageSubtitle' => 'Use CodeIgniter for SEO-focused pages, then hand off application-heavy flows to React under /admin.',
            'seoTitle' => 'Base App Galih | SEO-ready public pages with React admin',
            'stats' => [
                ['label' => 'SSR-friendly public pages', 'value' => 'CodeIgniter MVC'],
                ['label' => 'Admin application', 'value' => 'React + Vite'],
                ['label' => 'Shared API backend', 'value' => '/api/*'],
            ],
        ]));
    }

    public function about()
    {
        return view('web/about', $this->buildPageData([
            'activeNav' => 'about',
            'description' => 'Learn how Base App Galih splits SEO-friendly public pages and application dashboards without overloading the server.',
            'metaType' => 'article',
            'pageTitle' => 'A practical split between SEO pages and web app flows',
            'pageSubtitle' => 'Keep public marketing pages lightweight and crawlable, while the admin experience stays dynamic under React.',
            'seoTitle' => 'About Base App Galih | Public SEO pages + React admin',
            'highlights' => [
                'Public pages are rendered by CodeIgniter, which keeps HTML crawlable for search engines.',
                'Admin flows stay inside React, where richer state management and interaction patterns are easier to maintain.',
                'One API layer keeps auth, menu access, logs, and application data consistent across both sides.',
            ],
        ]));
    }

    public function articles()
    {
        return view('web/articles', $this->buildPageData([
            'activeNav' => 'articles',
            'articles' => $this->getArticles(),
            'description' => 'Browse starter articles that explain how to structure SEO-friendly CodeIgniter pages alongside a React admin application.',
            'metaType' => 'website',
            'pageTitle' => 'Articles and implementation notes',
            'pageSubtitle' => 'Simple content pages that search engines can read directly, while your application UI can stay separate.',
            'seoTitle' => 'Articles | Base App Galih',
        ]));
    }

    public function articleDetail(string $slug)
    {
        $article = null;

        foreach ($this->getArticles() as $item) {
            if ($item['slug'] === $slug) {
                $article = $item;
                break;
            }
        }

        if ($article === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('web/article_detail', $this->buildPageData([
            'activeNav' => 'articles',
            'article' => $article,
            'description' => $article['excerpt'],
            'metaType' => 'article',
            'seoTitle' => $article['title'] . ' | Base App Galih',
        ]));
    }

    private function buildPageData(array $data): array
    {
        $appName = getenv('app.name') ?: 'Base App Galih';
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

    private function getArticles(): array
    {
        return [
            [
                'slug' => 'seo-public-pages-and-react-admin',
                'title' => 'Combining SEO-friendly public pages with a React admin panel',
                'excerpt' => 'A practical baseline for splitting public content and dynamic application flows without adding unnecessary infrastructure.',
                'category' => 'Architecture',
                'published_at' => '2026-06-09',
                'read_time' => '5 min read',
                'content' => [
                    'When a project needs strong SEO, server-rendered public pages are still the simplest path. Search engines can crawl meaningful HTML immediately, and the server workload stays predictable.',
                    'For complex dashboards, forms, and state-heavy modules, React remains a better fit. The split lets each layer focus on what it does best without forcing one framework to solve every problem.',
                    'In this base app approach, CodeIgniter owns public routes and the API, while React focuses on /admin. That keeps deployment straightforward on smaller VPS instances.',
                ],
            ],
            [
                'slug' => 'why-shared-api-boundaries-matter',
                'title' => 'Why a shared API boundary makes the stack easier to scale',
                'excerpt' => 'A shared /api layer gives both public pages and app pages a single source of truth for business logic and permissions.',
                'category' => 'Backend',
                'published_at' => '2026-06-08',
                'read_time' => '4 min read',
                'content' => [
                    'Even if public pages are rendered on the server and admin pages run in React, both sides still benefit from the same backend contracts.',
                    'Validation, auth, role checks, menu access, and logging become easier to maintain because they are defined once.',
                    'That reduces duplication and makes future apps more consistent when you reuse this base project.',
                ],
            ],
            [
                'slug' => 'keeping-vps-usage-practical',
                'title' => 'Keeping VPS usage practical for multiple client projects',
                'excerpt' => 'A lighter deployment model can be a good tradeoff when the server needs to host several apps at once.',
                'category' => 'Operations',
                'published_at' => '2026-06-07',
                'read_time' => '3 min read',
                'content' => [
                    'Static frontend builds served directly by the web server are often cheaper operationally than keeping multiple Node processes alive.',
                    'CodeIgniter plus a built React admin can be a very practical stack when you want predictable memory usage.',
                    'The goal is not to avoid modern tooling, but to choose where runtime complexity actually adds value.',
                ],
            ],
        ];
    }
}
