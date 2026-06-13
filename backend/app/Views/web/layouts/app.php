<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            200: '#fbcfe8',
                            300: '#f9a8d4',
                            400: '#f472b6',
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                            800: '#9d174d',
                            900: '#831843',
                        },
                        secondary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
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
                        glow: '0 30px 90px rgba(236, 72, 153, 0.18)',
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(236, 72, 153, 0.18), transparent 30%),
                radial-gradient(circle at top right, rgba(139, 92, 246, 0.16), transparent 26%),
                linear-gradient(180deg, #fdf2f8 0%, #f5f3ff 42%, #ffffff 100%);
        }
    </style>
</head>
<body class="text-dark-900">
    <div class="min-h-screen">
        <header class="sticky top-0 z-30 border-b border-white/70 bg-white/80 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
                <a href="<?= base_url('/') ?>" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 via-primary-400 to-secondary-400 text-xl font-extrabold text-white shadow-glow">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-primary-700">IT Commerce</p>
                        <p class="text-lg font-bold text-dark-900"><?= esc($appName) ?></p>
                    </div>
                </a>

                <nav class="hidden items-center gap-2 md:flex">
                    <?php $navItems = [
                        ['label' => 'Home', 'href' => base_url('/'), 'key' => 'home'],
                        ['label' => 'Shop', 'href' => base_url('/shop'), 'key' => 'shop'],
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
                    href="<?= base_url('/app/login') ?>"
                    class="inline-flex items-center justify-center rounded-full border border-primary-200 bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700 transition hover:bg-primary-100"
                >
                    Open App
                </a>
            </div>
        </header>

        <main>
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</body>
</html>
