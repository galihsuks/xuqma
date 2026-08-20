<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="">
    <div class="relative overflow-hidden bg-dark-900 shadow-[0_40px_120px_-45px_rgba(124,58,237,0.55)]">
        <div class="absolute inset-0">
            <img
                src="<?= base_url('/assets/hero-img-light.png') ?>"
                alt="Featured IT accessories and computer components"
                class="h-full w-full object-cover object-center"
            >
        </div>

        <div class="relative mx-auto max-w-7xl grid py-20 lg:py-[140px] px-6 lg:px-8 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="max-w-3xl md:mb-20">
                <span class="inline-flex items-center gap-2 rounded-full bg-secondary-600/10 px-4 py-2 text-xs md:text-sm font-semibold uppercase tracking-[0.22em] text-secondary-600 backdrop-blur">
                    <i class="bi bi-lightning-charge-fill"></i>
                    Trusted IT Commerce
                </span>

                <h1 class="hidden md:block mt-6 max-w-[200px] md:max-w-3xl text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                    Shopping is
                    <span class="bg-gradient-to-r from-primary-700 via-secondary-500 to-primary-600 bg-clip-text text-transparent">
                        more focused,
                    </span>
                    not more complicated.
                </h1>
                <h1 class="md:hidden block mt-6 max-w-[220px] md:max-w-3xl font-extrabold tracking-tight sm:text-5xl text-4xl lg:text-6xl">
                    Less
                    <span class="bg-gradient-to-r from-primary-700 via-secondary-500 to-primary-600 bg-clip-text text-transparent">
                        complexity.
                    </span>
                    More
                    <span class="bg-gradient-to-r from-primary-700 via-secondary-500 to-primary-600 bg-clip-text text-transparent">
                        focus.
                    </span>
                </h1>

                <p class="hidden md:block mt-5 max-w-[240px] md:max-w-2xl text-sm md:text-base leading-6 md:leading-8 text-dark-600 sm:text-lg">
                    XUQMA brings together headphones, phone accessories, monitors, RAM, GPUs, and everyday tech gear
                    in one storefront with clear product highlights, helpful buying guides, and a fast checkout flow.
                </p>
                <p class="md:hidden block mt-5 max-w-[170px] md:max-w-2xl text-sm md:text-base leading-6 md:leading-8 text-dark-600 sm:text-lg">
                    XUQMA makes everyday tech shopping simple, clear, and fast.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="<?= base_url('/shop') ?>" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-900/30 transition hover:bg-primary-500">
                        <i class="bi bi-bag-check"></i>
                        Shop now
                    </a>
                    <a href="<?= base_url('/articles') ?>" class="hidden md:block inline-flex items-center gap-2 rounded-2xl border border-dark-200 text-dark-700 hover:border-primary-200 hover:text-primary-700 bg-white/90 md:bg-white/10 px-6 py-3 text-sm font-semibold backdrop-blur transition hover:bg-white/100 md:hover:bg-white/15">
                        <i class="bi bi-journal-richtext"></i>
                        Read buying guides
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mt-5 mx-auto max-w-7xl px-6 pb-6 lg:px-8 pt-10 lg:pt-14">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700">Category</p>
            
            <h2 class="hidden md:block mt-3 text-3xl font-bold tracking-tight text-dark-900">Choose the category that fits your setup best</h2>
            <h2 class="md:hidden block mt-3 text-3xl font-bold tracking-tight text-dark-900">Find your category</h2>
            
            <p class="hidden md:block mt-3 text-sm md:text-base leading-6 md:leading-8 text-dark-600">
                Explore the gear people shop for most, from daily phone essentials to serious desktop upgrades and performance-focused components.
            </p>
        </div>
        <a href="<?= base_url('/shop') ?>" class="hidden md:inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
            View all products
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</section>
<section class="mx-auto max-w-7xl px-0 pb-2 md:pb-6 lg:px-8">
    <?php if (!empty($categories)): ?>
        <?= view('web/partials/category_list', ['categories' => $categories]) ?>
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

<section class="mx-auto max-w-7xl px-6 py-10 lg:px-8 lg:py-14">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-secondary-700">Featured</p>
            
            <h2 class="hidden md:block mt-3 text-3xl font-bold tracking-tight text-dark-900">Featured picks for gaming, work, streaming, and daily upgrades</h2>
            <h2 class="md:hidden block mt-3 text-3xl font-bold tracking-tight text-dark-900">Top picks for gaming, work, and more</h2>
            
            <p class="hidden md:block mt-3 text-sm md:text-base leading-6 md:leading-8 text-dark-600">
                Discover standout products that are worth a closer look, whether you are shopping for better comfort, cleaner audio, or more power.
            </p>
        </div>
        <a href="<?= base_url('/shop') ?>" class="hidden md:inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
            Browse storefront
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <?php if (!empty($featuredProducts)): ?>
        <div class="mt-8 grid gap-3 md:gap-6 grid-cols-2 lg:grid-cols-3">
            <?php foreach ($featuredProducts as $product): ?>
                <?= view('web/partials/product_card', [
                    'product' => $product,
                    'appProfileUrl' => $appProfileUrl,
                    'customerCartQtyMap' => $customerCartQtyMap,
                    'isCustomerRole' => $isCustomerRole,
                    'isLoggedIn' => $isLoggedIn,
                ]) ?>
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

<section class="mx-auto max-w-7xl px-6 py-10 lg:px-8 lg:py-14">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700">Content that converts</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-dark-900">Helpful articles for smarter tech buying decisions</h2>
            <p class="mt-3 text-sm md:text-base leading-6 md:leading-8 text-dark-600">
                Read practical guides, comparisons, and upgrade tips that make it easier to choose the right product before you spend.
            </p>
        </div>
        <a href="<?= base_url('/articles') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
            Read all articles
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <?php if (!empty($latestArticles)): ?>
        <?= view('web/partials/article_list', [
            'articles' => $latestArticles,
            'gridClass' => 'mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3',
            'showPublishedAt' => false,
            'showReadMore' => false,
        ]) ?>
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

<section class="mx-auto max-w-7xl py-10 lg:py-14 px-2 lg:px-8">
    <div class="grid gap-5 md:grid-cols-3">
        <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/35">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                <i class="bi bi-search text-xl"></i>
            </div>
            <h2 class="mt-3 md:mt-5 text-xl font-bold text-dark-900">Find the right products faster</h2>
            <p class="mt-1 md:mt-3 text-xs md:text-sm leading-5 md:leading-7 text-dark-600">
                Browse popular categories, scan key highlights, and get to the products that match your setup without wasting time.
            </p>
        </article>
        <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/35">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-secondary-50 text-secondary-600">
                <i class="bi bi-journal-check text-xl"></i>
            </div>
            <h2 class="mt-3 md:mt-5 text-xl font-bold text-dark-900">Research while you shop</h2>
            <p class="mt-1 md:mt-3 text-xs md:text-sm leading-5 md:leading-7 text-dark-600">
                Read practical buying guides, understand the specs that matter, and shop with more confidence from the start.
            </p>
        </article>
        <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/35">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                <i class="bi bi-lightning-charge text-xl"></i>
            </div>
            <h2 class="mt-3 md:mt-5 text-xl font-bold text-dark-900">Enjoy a smoother buying journey</h2>
            <p class="mt-1 md:mt-3 text-xs md:text-sm leading-5 md:leading-7 text-dark-600">
                Save your details, keep delivery preferences ready, and reorder faster whenever you come back for your next upgrade.
            </p>
        </article>
    </div>
</section>
<?= $this->endSection() ?>
