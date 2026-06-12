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
            --primary-100: #e0f2fe;
            --primary-500: #0ea5e9;
            --primary-600: #0284c7;
            --secondary-300: #bef264;
            --secondary-400: #a3e635;
            --warning-100: #fef3c7;
            --warning-600: #d97706;
            --danger-100: #ffe4e6;
            --danger-600: #e11d48;
            --dark-100: #e2e8f0;
            --dark-300: #94a3b8;
            --dark-500: #64748b;
            --dark-700: #334155;
            --dark-900: #0f172a;
            --light-50: #fafafa;
            --light-100: #f4f4f5;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", sans-serif;
            color: var(--dark-900);
            background:
                radial-gradient(circle at top, rgba(14, 165, 233, 0.16), transparent 42%),
                linear-gradient(180deg, #f8fafc 0%, #eef6ff 45%, #ffffff 100%);
        }

        .page {
            min-height: 100vh;
            padding: 2rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .shell {
            width: 100%;
            max-width: 1120px;
            display: grid;
            overflow: hidden;
            border-radius: 32px;
            border: 1px solid rgba(224, 242, 254, 0.85);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 30px 80px -45px rgba(14, 165, 233, 0.55);
            backdrop-filter: blur(18px);
        }

        .hero {
            display: none;
            position: relative;
            overflow: hidden;
            min-height: 580px;
            padding: 2rem;
            color: var(--white);
            background: linear-gradient(135deg, var(--primary-600), var(--primary-500), var(--secondary-400));
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.35), transparent 32%),
                radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.16), transparent 32%);
        }

        .hero-content,
        .hero-status {
            position: relative;
            z-index: 1;
        }

        .hero-top {
            width: 64px;
            height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            font-size: 1.5rem;
            font-weight: 800;
            backdrop-filter: blur(10px);
        }

        .hero-label {
            margin: 2rem 0 0;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.26em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.78);
        }

        .hero-title {
            margin: 1rem 0 0;
            max-width: 360px;
            font-size: 3rem;
            line-height: 1.15;
            font-weight: 800;
        }

        .hero-copy {
            margin: 1rem 0 0;
            max-width: 420px;
            font-size: 0.97rem;
            line-height: 1.9;
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
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.74);
        }

        .hero-status-row {
            margin-top: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.88);
        }

        .hero-status-dot {
            width: 12px;
            height: 12px;
            flex: 0 0 auto;
            border-radius: 999px;
            background: var(--secondary-300);
            box-shadow: 0 0 22px rgba(163, 230, 53, 0.95);
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
            padding: 0.4rem 0.8rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .badge-primary {
            background: var(--primary-100);
            color: var(--primary-600);
        }

        .badge-warning {
            background: var(--warning-100);
            color: var(--warning-600);
        }

        .badge-danger {
            background: var(--danger-100);
            color: var(--danger-600);
        }

        .pill {
            gap: 0.5rem;
            border: 1px solid var(--dark-100);
            background: var(--white);
            color: var(--dark-500);
            letter-spacing: 0;
            text-transform: none;
            font-weight: 600;
        }

        .pill-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--secondary-400);
        }

        .brand {
            display: none;
            height: 28px;
            font-size: 1rem;
            font-weight: 800;
            color: var(--dark-900);
        }

        .icon-box {
            width: 64px;
            height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 22px;
            background: <?= esc($iconBackground ?? '#0284c7') ?>;
            color: var(--white);
            box-shadow: 0 16px 28px -18px rgba(15, 23, 42, 0.25);
        }

        .icon-box svg {
            width: 32px;
            height: 32px;
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
            color: var(--dark-900);
        }

        .description {
            margin: 1rem 0 0;
            max-width: 620px;
            font-size: 1rem;
            line-height: 1.95;
            color: var(--dark-500);
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
            background: var(--dark-900);
            color: var(--white);
        }

        .btn-secondary {
            border: 1px solid var(--primary-500);
            background: rgba(255, 255, 255, 0.95);
            color: var(--primary-600);
        }

        .hint-card {
            margin-top: 2rem;
            border-radius: 24px;
            border: 1px solid var(--dark-100);
            background: linear-gradient(135deg, rgba(255,255,255,0.96), rgba(240,249,255,0.75));
            padding: 1.1rem 1.25rem;
        }

        .hint-title {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--primary-600);
        }

        .hint-copy {
            margin: 0.75rem 0 0;
            font-size: 0.95rem;
            line-height: 1.85;
            color: var(--dark-700);
        }

        @media (min-width: 1024px) {
            .shell {
                grid-template-columns: 0.92fr 1.08fr;
            }

            .hero {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .content {
                padding: 3rem;
            }

            .brand {
                display: block;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="shell">
            <aside class="hero">
                <div class="hero-content">
                    <div class="hero-top">G</div>
                    <p class="hero-label">Base App</p>
                    <h2 class="hero-title"><?= esc($heroTitle ?? 'Graceful error pages for public routes.') ?></h2>
                    <p class="hero-copy">
                        <?= esc($heroDescription ?? 'The public side stays polished and predictable even when a page cannot be found or something unexpected happens on the server.') ?>
                    </p>
                </div>

                <div class="hero-status">
                    <p class="hero-status-label">System status</p>
                    <div class="hero-status-row">
                        <span class="hero-status-dot"></span>
                        <span>The website is still active. You can safely go back or open another page.</span>
                    </div>
                </div>
            </aside>

            <div class="content">
                <div class="header-row">
                    <div class="meta-group">
                        <span class="badge <?= esc($badgeClass ?? 'badge-primary') ?>"><?= esc($statusCode ?? 'ERROR') ?></span>
                        <span class="pill">
                            <span class="pill-dot"></span>
                            Public Website
                        </span>
                    </div>
                    <div class="brand"><?= esc($brandText ?? 'Base App Galih') ?></div>
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
    </main>
</body>
</html>
