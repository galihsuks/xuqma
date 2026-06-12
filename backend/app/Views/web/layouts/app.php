<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($seoTitle) ?></title>
    <meta name="description" content="<?= esc($description) ?>">
    <link rel="canonical" href="<?= esc($canonicalUrl) ?>">
    <meta property="og:title" content="<?= esc($seoTitle) ?>">
    <meta property="og:description" content="<?= esc($description) ?>">
    <meta property="og:url" content="<?= esc($canonicalUrl) ?>">
    <meta property="og:type" content="<?= esc($metaType) ?>">
    <meta property="og:image" content="<?= esc($metaImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($seoTitle) ?>">
    <meta name="twitter:description" content="<?= esc($description) ?>">
    <meta name="twitter:image" content="<?= esc($metaImage) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f7fee7',
                            100: '#ecfccb',
                            200: '#d9f99d',
                            300: '#bef264',
                            400: '#a3e635',
                            500: '#84cc16',
                            600: '#65a30d',
                            700: '#4d7c0f',
                            800: '#3f6212',
                            900: '#365314',
                        },
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    boxShadow: {
                        glow: '0 30px 90px rgba(14, 165, 233, 0.14)',
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.18), transparent 30%),
                radial-gradient(circle at top right, rgba(132, 204, 22, 0.15), transparent 26%),
                linear-gradient(180deg, #f8fafc 0%, #eff6ff 42%, #ffffff 100%);
        }
    </style>
</head>
<body class="text-dark-900">
    <div class="min-h-screen">
        <header class="sticky top-0 z-30 border-b border-white/70 bg-white/80 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
                <a href="<?= base_url('/') ?>" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 via-primary-400 to-secondary-400 text-xl font-extrabold text-white shadow-glow">
                        G
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-primary-700">Base App</p>
                        <p class="text-lg font-bold text-dark-900"><?= esc($appName) ?></p>
                    </div>
                </a>

                <nav class="hidden items-center gap-2 md:flex">
                    <?php $navItems = [
                        ['label' => 'Home', 'href' => base_url('/'), 'key' => 'home'],
                        ['label' => 'About', 'href' => base_url('/about'), 'key' => 'about'],
                        ['label' => 'Articles', 'href' => base_url('/articles'), 'key' => 'articles'],
                    ]; ?>
                    <?php foreach ($navItems as $item): ?>
                        <?php $isActive = ($activeNav ?? '') === $item['key']; ?>
                        <a
                            href="<?= esc($item['href']) ?>"
                            class="<?= $isActive
                                ? 'rounded-full bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-primary-200/70'
                                : 'rounded-full px-4 py-2 text-sm font-semibold text-dark-600 transition hover:bg-primary-50 hover:text-primary-700' ?>"
                        >
                            <?= esc($item['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <a
                    href="<?= base_url('/admin') ?>"
                    class="inline-flex items-center justify-center rounded-full border border-primary-200 bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700 transition hover:bg-primary-100"
                >
                    Open Admin
                </a>
            </div>
        </header>

        <main>
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</body>
</html>
