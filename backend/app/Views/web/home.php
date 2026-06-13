<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mx-auto grid max-w-7xl gap-10 px-5 py-16 lg:grid-cols-[1.15fr_0.85fr] lg:px-8 lg:py-24">
    <div class="rounded-[32px] border border-white/70 bg-white/85 p-8 shadow-glow backdrop-blur-xl lg:p-10">
        <span class="inline-flex items-center gap-2 rounded-full bg-secondary-100 px-4 py-2 text-sm font-semibold uppercase tracking-[0.22em] text-secondary-800">
            <i class="bi bi-stars"></i>
            <?= esc($heroBadge ?? 'Public Website') ?>
        </span>
        <h1 class="mt-6 max-w-3xl text-4xl font-extrabold tracking-tight text-dark-900 lg:text-6xl">
            <?= esc($pageTitle) ?>
        </h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-dark-600">
            <?= esc($pageSubtitle) ?>
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="<?= base_url('/shop') ?>" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
                <i class="bi bi-bag-check"></i>
                Explore catalog
            </a>
            <a href="<?= base_url('/app/login') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-dark-200 bg-white px-6 py-3 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
                <i class="bi bi-box-arrow-in-right"></i>
                Open customer or admin app
            </a>
        </div>
    </div>

    <div class="grid gap-4">
        <?php foreach (($stats ?? []) as $stat): ?>
            <article class="rounded-[28px] border border-white/70 bg-gradient-to-br from-white to-primary-50 p-6 shadow-lg shadow-primary-100/50">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700"><?= esc($stat['label']) ?></p>
                <p class="mt-3 text-2xl font-bold text-dark-900"><?= esc($stat['value']) ?></p>
            </article>
        <?php endforeach; ?>

        <article class="rounded-[28px] border border-primary-100 bg-dark-900 p-6 text-white shadow-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-300">Suggested flow</p>
            <ol class="mt-4 space-y-3 text-sm leading-7 text-slate-100">
                <li>1. Let search engines discover product and article pages rendered by CodeIgniter.</li>
                <li>2. Move transactional flows into React under <span class="font-semibold text-white">/app/customer</span>.</li>
                <li>3. Keep operations and management inside <span class="font-semibold text-white">/app/admin</span>.</li>
            </ol>
        </article>
    </div>
</section>

<section class="mx-auto max-w-7xl px-5 pb-6 lg:px-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700">Shop by category</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-dark-900">Curated sections for modern IT needs</h2>
        </div>
        <a href="<?= base_url('/shop') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
            View all products
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <?php if (!empty($categories)): ?>
        <div class="mt-8 grid gap-6 lg:grid-cols-2 xl:grid-cols-4">
            <?php foreach ($categories as $category): ?>
                <a href="<?= base_url('/category/' . $category['slug']) ?>" class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/40 transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-secondary-400 text-xl text-white">
                        <i class="bi <?= esc($category['icon']) ?>"></i>
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-dark-900"><?= esc($category['name']) ?></h3>
                    <p class="mt-3 text-sm leading-7 text-dark-600"><?= esc($category['description']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mt-8">
            <?= view('web/partials/empty_state', [
                'icon' => 'bi-grid-3x3-gap',
                'eyebrow' => 'Catalog Setup',
                'title' => 'Categories have not been published yet',
                'message' => 'Create and display product categories from the admin area so visitors can browse the storefront by section.',
                'primaryLabel' => 'Open Admin App',
                'primaryUrl' => base_url('/app/admin/catalog/categories'),
                'secondaryLabel' => 'Open Customer App',
                'secondaryUrl' => base_url('/app/customer/profile'),
            ]) ?>
        </div>
    <?php endif; ?>
</section>

<section class="mx-auto max-w-7xl px-5 py-10 lg:px-8 lg:py-14">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-secondary-700">Featured picks</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-dark-900">Popular products for gamers, creators, and everyday setups</h2>
        </div>
        <a href="<?= base_url('/shop') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
            Browse storefront
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <?php if (!empty($featuredProducts)): ?>
        <div class="mt-8 grid gap-6 lg:grid-cols-2 xl:grid-cols-4">
            <?php foreach ($featuredProducts as $product): ?>
                <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/40">
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-primary-700">
                            <?= esc($product['category_name']) ?>
                        </span>
                        <span class="inline-flex rounded-full bg-secondary-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-secondary-700">
                            <?= esc($product['stock_badge']) ?>
                        </span>
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-dark-900"><?= esc($product['name']) ?></h3>
                    <p class="mt-3 text-sm leading-7 text-dark-600"><?= esc($product['highlight']) ?></p>
                    <p class="mt-5 text-lg font-semibold text-primary-700"><?= esc($product['price']) ?></p>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <a href="<?= base_url('/products/' . $product['slug']) ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
                            View detail
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                        <a href="<?= base_url('/app/customer/cart?add_product=' . urlencode((string) $product['id']) . '&qty=1') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-secondary-700 hover:text-secondary-600">
                            Add to cart
                            <i class="bi bi-cart-plus"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mt-8">
            <?= view('web/partials/empty_state', [
                'icon' => 'bi-bag-x',
                'eyebrow' => 'Featured Picks',
                'title' => 'No featured products are live yet',
                'message' => 'Publish products and mark a few as featured from the admin catalog so the homepage can spotlight them here.',
                'primaryLabel' => 'Manage Products',
                'primaryUrl' => base_url('/app/admin/catalog/products'),
                'secondaryLabel' => 'Browse Customer App',
                'secondaryUrl' => base_url('/app/customer/cart'),
            ]) ?>
        </div>
    <?php endif; ?>
</section>

<section class="mx-auto max-w-7xl px-5 py-10 lg:px-8 lg:py-14">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700">Content that converts</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-dark-900">Support search traffic with educational IT articles</h2>
        </div>
        <a href="<?= base_url('/articles') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
            Read all articles
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <?php if (!empty($latestArticles)): ?>
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <?php foreach ($latestArticles as $article): ?>
                <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/40 transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-primary-700">
                        <span><?= esc($article['category']) ?></span>
                        <span class="h-1 w-1 rounded-full bg-primary-300"></span>
                        <span><?= esc($article['read_time']) ?></span>
                    </div>
                    <h3 class="mt-4 text-2xl font-bold text-dark-900">
                        <a href="<?= base_url('/articles/' . $article['slug']) ?>" class="transition hover:text-primary-700">
                            <?= esc($article['title']) ?>
                        </a>
                    </h3>
                    <p class="mt-4 text-base leading-7 text-dark-600"><?= esc($article['excerpt']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mt-8">
            <?= view('web/partials/empty_state', [
                'icon' => 'bi-journal-x',
                'eyebrow' => 'Content Hub',
                'title' => 'No articles have been published yet',
                'message' => 'Publish SEO articles from the admin content module to support discovery traffic and buying-guide journeys.',
                'primaryLabel' => 'Manage Articles',
                'primaryUrl' => base_url('/app/admin/content/articles'),
                'secondaryLabel' => 'Go to Articles Page',
                'secondaryUrl' => base_url('/articles'),
            ]) ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
