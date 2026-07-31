<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title><?= esc($pageTitle ?? 'Error') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary-50: #f5f3ff;
            --color-primary-100: #ede9fe;
            --color-primary-200: #ddd6fe;
            --color-primary-300: #c4b5fd;
            --color-primary-400: #a78bfa;
            --color-primary-500: #8b5cf6;
            --color-primary-600: #7c3aed;
            --color-primary-700: #6d28d9;
            --color-primary-800: #5b21b6;
            --color-primary-900: #4c1d95;
            --color-secondary-100: #fce7f3;
            --color-secondary-300: #f9a8d4;
            --color-secondary-400: #f472b6;
            --color-secondary-500: #ec4899;
            --color-secondary-600: #db2777;
            --color-warning-100: #fef3c7;
            --color-warning-500: #f59e0b;
            --color-warning-700: #b45309;
            --color-danger-100: #ffe4e6;
            --color-danger-500: #f43f5e;
            --color-danger-700: #be123c;
            --color-info-100: #cffafe;
            --color-info-500: #06b6d4;
            --color-info-700: #0e7490;
            --color-light-100: #f4f4f5;
            --color-light-200: #e4e4e7;
            --color-dark-300: #cbd5e1;
            --color-dark-500: #64748b;
            --color-dark-700: #334155;
            --color-dark-900: #0f172a;
            --color-white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", sans-serif;
            color: var(--color-dark-900);
            background:
                radial-gradient(circle at top left, rgba(244, 114, 182, 0.18), transparent 28%),
                radial-gradient(circle at top right, rgba(167, 139, 250, 0.22), transparent 24%),
                linear-gradient(180deg, var(--color-light-100), var(--color-white));
        }

        .page {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            padding: 2rem 1rem;
        }

        .page::before {
            content: "";
            position: absolute;
            inset: 0;
            background: <?= esc($glowBackground ?? 'linear-gradient(135deg, rgba(124,58,237,0.16), rgba(244,114,182,0.08), transparent)') ?>;
            pointer-events: none;
        }

        .page::after {
            content: "";
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 18rem;
            background: radial-gradient(circle at top, rgba(167, 139, 250, 0.16), transparent 60%);
            pointer-events: none;
        }

        .shell-wrap {
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 4rem);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .shell {
            width: 100%;
            max-width: 1152px;
            display: grid;
            overflow: hidden;
            border-radius: 32px;
            border: 1px solid rgba(221, 214, 254, 0.6);
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 30px 80px -45px rgba(124, 58, 237, 0.28);
            backdrop-filter: blur(18px);
        }

        .hero {
            display: none;
            overflow: hidden;
            position: relative;
            padding: 3rem;
            color: var(--color-white);
            background: linear-gradient(135deg, var(--color-primary-900), var(--color-primary-800), var(--color-secondary-500));
        }

        .hero-inner,
        .hero-status {
            position: relative;
            z-index: 1;
        }

        .hero-inner {
            margin-bottom: 2rem;
        }

        .hero-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
        }

        .hero-logo img {
            width: 2.5rem;
            height: 2.5rem;
            object-fit: contain;
        }

        .hero-brand {
            margin: 0.4rem 0 0;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.22em;
            color: rgba(255, 255, 255, 0.8);
        }

        .hero-title {
            margin: 1rem 0 0;
            max-width: 350px;
            font-size: 2.5rem;
            line-height: 1.15;
            font-weight: 700;
        }

        .hero-copy {
            margin: 1rem 0 0;
            max-width: 24rem;
            font-size: 0.95rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.84);
        }

        .hero-status {
            margin-top: auto;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.1);
            padding: 1.25rem;
            backdrop-filter: blur(12px);
        }

        .hero-status-label {
            margin: 0;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.72);
        }

        .hero-status-row {
            margin-top: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(255, 255, 255, 0.86);
            font-size: 0.95rem;
            line-height: 1.75;
        }

        .hero-status-dot {
            width: 0.75rem;
            height: 0.75rem;
            flex: 0 0 auto;
            border-radius: 999px;
            background: var(--color-secondary-300);
            box-shadow: 0 0 20px rgba(249, 168, 212, 0.95);
        }

        .content {
            padding: 1.75rem;
        }

        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .meta-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
        }

        .badge,
        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 0.4rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge {
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .badge-primary {
            background: var(--color-primary-100);
            color: var(--color-primary-700);
        }

        .badge-warning {
            background: var(--color-warning-100);
            color: var(--color-warning-700);
        }

        .badge-danger {
            background: var(--color-danger-100);
            color: var(--color-danger-700);
        }

        .badge-info {
            background: var(--color-info-100);
            color: var(--color-info-700);
        }

        .pill {
            gap: 0.5rem;
            border: 1px solid var(--color-light-200);
            background: var(--color-white);
            color: var(--color-dark-500);
        }

        .pill-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 999px;
            background: var(--color-secondary-400);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .brand img {
            width: 1.75rem;
            height: 1.75rem;
            object-fit: contain;
        }

        .brand-text {
            font-size: 1.125rem;
            font-weight: 300;
            letter-spacing: 0.28em;
            color: var(--color-primary-700);
            display: none;
        }

        .icon-box {
            width: 4rem;
            height: 4rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 22px;
            background: <?= esc($iconBackground ?? '#7c3aed') ?>;
            color: var(--color-white);
            box-shadow: 0 16px 28px -18px rgba(15, 23, 42, 0.25);
        }

        .icon-box svg {
            width: 2rem;
            height: 2rem;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .title {
            margin: 1.5rem 0 0;
            font-size: clamp(2.3rem, 4vw, 4rem);
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--color-dark-900);
        }

        .description {
            margin: 1rem 0 0;
            max-width: 38rem;
            font-size: 1rem;
            line-height: 1.95;
            color: var(--color-dark-500);
        }

        .actions {
            margin-top: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            border-radius: 14px;
            padding: 0.9rem 1.15rem;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--color-dark-900);
            color: var(--color-white);
        }

        .btn-secondary {
            border: 1px solid var(--color-primary-500);
            background: rgba(255, 255, 255, 0.95);
            color: var(--color-primary-700);
        }

        .hint-card {
            margin-top: 2rem;
            border-radius: 24px;
            border: 1px solid rgba(221, 214, 254, 0.7);
            background: linear-gradient(135deg, rgba(255,255,255,0.96), rgba(245,243,255,0.82));
            padding: 1.1rem 1.25rem;
        }

        .hint-title {
            margin: 0;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--color-primary-700);
        }

        .hint-copy {
            margin: 0.75rem 0 0;
            font-size: 0.95rem;
            line-height: 1.85;
            color: var(--color-dark-700);
        }

        @media (min-width: 500px) {
            .brand-text {
                display: block;
            }
        }

        @media (min-width: 1024px) {
            .shell {
                grid-template-columns: 0.92fr 1.08fr;
            }

            .hero {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                min-height: 40rem;
            }

            .content {
                padding: 3rem;
            }

            .brand {
                display: none;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="shell-wrap">
            <section class="shell">
                <aside class="hero">
                    <div class="hero-inner">
                        <div class="hero-logo">
                            <img src="/assets/app_logo/mark.png" alt="UQMA">
                        </div>
                        <p class="hero-brand"><?= esc($heroBrand ?? 'XUQMA') ?></p>
                        <h2 class="hero-title"><?= esc($heroTitle ?? 'The latest software and hardware are here') ?></h2>
                        <p class="hero-copy">
                            <?= esc($heroDescription ?? 'SEO storefront pages can stay on CodeIgniter, while transactional customer and admin experiences live under React.') ?>
                        </p>
                    </div>

                    <div class="hero-status">
                        <p class="hero-status-label"><?= esc($statusLabel ?? 'System status') ?></p>
                        <div class="hero-status-row">
                            <span class="hero-status-dot"></span>
                            <span><?= esc($statusCopy ?? 'The app is still active. You can safely return or navigate elsewhere.') ?></span>
                        </div>
                    </div>
                </aside>

                <div class="content">
                    <div class="header-row">
                        <div class="meta-group">
                            <span class="badge <?= esc($badgeClass ?? 'badge-primary') ?>"><?= esc($statusCode ?? 'ERROR') ?></span>
                            <span class="pill">
                                <span class="pill-dot"></span>
                                <?= esc($contextLabel ?? 'Public Website') ?>
                            </span>
                        </div>
                        <div class="brand">
                            <img src="/assets/app_logo/mark.png" alt="UQMA">
                            <span class="brand-text"><?= esc($brandText ?? 'UQMA') ?></span>
                        </div>
                    </div>

                    <div class="icon-box">
                        <?= $iconSvg ?? '<span>' . esc($iconText ?? '!') . '</span>' ?>
                    </div>
                    <h1 class="title"><?= esc($title ?? 'Something went wrong') ?></h1>
                    <p class="description"><?= esc($description ?? 'An unexpected error occurred while loading this page.') ?></p>

                    <div class="actions">
                        <a href="<?= esc($primaryActionUrl ?? base_url('/')) ?>" class="btn btn-primary">
                            <?= esc($primaryActionLabel ?? 'Go to Home') ?>
                        </a>
                        <a href="<?= esc($secondaryActionUrl ?? base_url('/articles')) ?>" class="btn btn-secondary">
                            <?= esc($secondaryActionLabel ?? 'Browse Articles') ?>
                        </a>
                    </div>

                    <div class="hint-card">
                        <p class="hint-title"><?= esc($hintTitle ?? 'Helpful note') ?></p>
                        <p class="hint-copy"><?= esc($hintCopy ?? 'If the problem keeps happening, verify the URL or review the related route and server configuration.') ?></p>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
