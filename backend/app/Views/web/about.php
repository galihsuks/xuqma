<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
        <span class="inline-flex items-center gap-2 rounded-full bg-secondary-100 px-4 py-2 text-sm font-semibold uppercase tracking-[0.22em] text-secondary-800">
            <i class="bi bi-stars"></i>
            About us
        </span>
        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-dark-900 lg:text-5xl">
            <?= esc($pageTitle) ?>
        </h1>
        <p class="mt-5 text-lg leading-8 text-dark-600">
            <?= esc($pageSubtitle) ?>
        </p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr] mt-10">
        <article class="rounded-[32px] border border-white/70 bg-white/90 p-7 shadow-lg shadow-primary-100/35 lg:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700">Why this storefront exists</p>
            <h2 class="mt-4 text-3xl font-bold tracking-tight text-dark-900">A better path from search, to research, to purchase</h2>
            <div class="mt-5 space-y-4 text-base leading-8 text-dark-600">
                <p>
                    Many shoppers arrive with questions before they are ready to buy. They may want to compare headphone styles,
                    understand whether a RAM upgrade makes sense, or check which accessories best fit their daily devices.
                </p>
                <p>
                    Xuqma is designed to support that journey. Public-facing pages help people discover products and articles through search,
                    while the customer area supports actions like saving preferences, managing orders, and moving through checkout.
                </p>
                <p>
                    The result is a storefront experience that feels easier to browse, easier to trust, and easier to return to when the next upgrade is due.
                </p>
            </div>
        </article>

        <article class="rounded-[32px] border border-primary-100 bg-gradient-to-br from-primary-50 via-white to-secondary-50 p-7 shadow-lg shadow-primary-100/30 lg:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700">What you can do next</p>
            <div class="mt-5 grid gap-4">
                <div class="rounded-[24px] border border-white/80 bg-white/90 p-5">
                    <p class="text-lg font-semibold text-dark-900">Browse the product catalog</p>
                    <p class="mt-2 text-sm leading-7 text-dark-600">
                        Explore categories, featured products, and detailed product pages built for discovery.
                    </p>
                </div>
                <div class="rounded-[24px] border border-white/80 bg-white/90 p-5">
                    <p class="text-lg font-semibold text-dark-900">Read practical buying guides</p>
                    <p class="mt-2 text-sm leading-7 text-dark-600">
                        Learn from comparison articles, setup tips, and explainers that make technical choices easier.
                    </p>
                </div>
                <div class="rounded-[24px] border border-white/80 bg-white/90 p-5">
                    <p class="text-lg font-semibold text-dark-900">Continue into your account flow</p>
                    <p class="mt-2 text-sm leading-7 text-dark-600">
                        Open the customer area to manage cart activity, orders, and saved details for faster repeat shopping.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="<?= base_url('/shop') ?>" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
                    <i class="bi bi-bag-check"></i>
                    Shop now
                </a>
                <a href="<?= base_url('/app/customer/profile') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-dark-200 bg-white px-5 py-3 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
                    <i class="bi bi-person"></i>
                    Open customer area
                </a>
            </div>
        </article>
    </div>

    <div class="grid gap-6 lg:grid-cols-3 mt-10">
        <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/35">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                <i class="bi bi-grid text-xl"></i>
            </div>
            <h2 class="mt-5 text-xl font-bold text-dark-900">Curated storefront structure</h2>
            <p class="mt-3 text-sm leading-7 text-dark-600">
                Categories, product detail pages, and featured selections are shaped to help shoppers move from discovery into comparison with less friction.
            </p>
        </article>

        <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/35">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-secondary-50 text-secondary-600">
                <i class="bi bi-headset text-xl"></i>
            </div>
            <h2 class="mt-5 text-xl font-bold text-dark-900">Built around real tech needs</h2>
            <p class="mt-3 text-sm leading-7 text-dark-600">
                From cables and headphones to monitors, RAM, and GPUs, the catalog is positioned around products people shop for to improve everyday setups.
            </p>
        </article>

        <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/35">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                <i class="bi bi-journal-check text-xl"></i>
            </div>
            <h2 class="mt-5 text-xl font-bold text-dark-900">Guides that support decisions</h2>
            <p class="mt-3 text-sm leading-7 text-dark-600">
                The article layer helps visitors learn, compare, and understand which products fit their budget, workflow, or upgrade plan.
            </p>
        </article>
    </div>
</section>
<?= $this->endSection() ?>
