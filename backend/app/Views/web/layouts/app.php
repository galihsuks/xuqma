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
                        secondary: {
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
        :root {
            --color-light-50: #fafafa;
            --color-light-100: #f4f4f5;
            --color-light-200: #e4e4e7;
            --color-light-300: #d4d4d8;
            --color-light-400: #a1a1aa;
            --color-light-500: #71717a;
            --color-light-600: #52525b;
            --color-light-700: #3f3f46;
            --color-light-800: #27272a;
            --color-light-900: #18181b;
            --color-light-950: #09090b;
            --color-dark-50: #f8fafc;
            --color-dark-100: #f1f5f9;
            --color-dark-200: #e2e8f0;
            --color-dark-300: #cbd5e1;
            --color-dark-400: #94a3b8;
            --color-dark-500: #64748b;
            --color-dark-600: #475569;
            --color-dark-700: #334155;
            --color-dark-800: #1e293b;
            --color-dark-900: #0f172a;
            --color-dark-950: #020617;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: color-mix(in srgb, var(--color-dark-400) 42%, transparent)
                color-mix(in srgb, var(--color-light-200) 88%, transparent);
        }

        *::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        *::-webkit-scrollbar-track {
            border-radius: 9999px;
            background: color-mix(in srgb, var(--color-light-200) 82%, transparent);
        }

        *::-webkit-scrollbar-thumb {
            border: 2px solid transparent;
            border-radius: 9999px;
            background: linear-gradient(
                180deg,
                color-mix(in srgb, var(--color-dark-300) 78%, white),
                color-mix(in srgb, var(--color-dark-500) 72%, white)
        );
        background-clip: padding-box;
        }

        *::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(
                180deg,
                color-mix(in srgb, var(--color-dark-400) 84%, white),
                color-mix(in srgb, var(--color-dark-600) 78%, white)
        );
        background-clip: padding-box;
        }

        *::-webkit-scrollbar-corner {
            background: transparent;
        }

        *::-webkit-scrollbar-button {
            display: none;
            width: 0;
            height: 0;
        }
    </style>
</head>
<body class="text-dark-900 bg-[radial-gradient(circle_at_top_left,rgba(244,114,182,0.18),transparent_28%),radial-gradient(circle_at_top_right,rgba(167,139,250,0.22),transparent_24%),linear-gradient(180deg,var(--color-light-100),white)]">
    <div class="min-h-screen">
        <header class="sticky top-0 z-30 border-b border-white/70 bg-white/80 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-3 lg:px-8">
                <div class="flex-1">
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
                                    ? 'rounded-full px-4 py-2 text-sm font-semibold text-primary-600'
                                    : 'rounded-full px-4 py-2 text-sm font-semibold text-dark-600 transition hover:bg-primary-50 hover:text-primary-700' ?>"
                            >
                                <?= esc($item['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
                <a href="<?= base_url('/') ?>" class="flex items-center gap-1">
                    <div class="flex h-7 w-7 items-center justify-center rounded-2xl text-xl font-extrabold text-white shadow-glow">
                        <img src="/assets/app_logo/mark.png" alt="">
                    </div>
                    <div>
                        <p class="text-lg font-thin text-primary-700 tracking-[0.28em]">UQMA</p>
                    </div>
                </a>

                <div class="flex-1 flex flex justify-end">
                    <div class="flex flex-wrap items-center gap-2">
                        <?php $navItems = [
                            ['icon' => 'bi-cart2', 'href' => base_url('/app/customer/cart')],
                            ['icon' => 'bi-box-seam', 'href' => base_url('/app/customer/orders')],
                            ['icon' => 'bi-receipt', 'href' => base_url('/app/customer/history')],
                        ]; ?>
                        <?php foreach ($navItems as $item): ?>
                            <a
                                href="<?= esc($item['href']) ?>"
                                class="rounded-full px-3 py-3 text-sm font-semibold text-dark-600 transition hover:bg-primary-50 hover:text-primary-700"
                            >
                                <i class="block -translate-y-[3px] translate-x-[2px] h-4 w-4 bi <?= $item['icon']; ?>"></i>
                            </a>
                        <?php endforeach; ?>
                        <span class="w-[1px] h-[80%] bg-dark-200"></span>
                        <a
                            href="<?= base_url('/app/customer/profile'); ?>"
                            class="rounded-full px-3 py-3 text-sm font-semibold text-dark-600 transition hover:bg-primary-50 hover:text-primary-700"
                        >
                            <i class="block -translate-y-[3px] translate-x-[2px] h-4 w-4 bi bi-person"></i>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</body>
</html>
