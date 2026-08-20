<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<article class="mx-auto max-w-4xl px-5 py-16 lg:px-8 lg:py-24">
    <a href="<?= base_url('/articles') ?>" class="inline-flex items-center gap-2 rounded-full border border-dark-200 bg-white px-4 py-2 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
        <i class="bi bi-arrow-left"></i>
        Back to articles
    </a>

    <div class="mt-8 rounded-[32px] border border-white/70 bg-white/90 p-8 shadow-glow lg:p-10">
        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.22em] text-primary-700">
            <span><?= esc($article['category']) ?></span>
            <span class="h-1 w-1 rounded-full bg-primary-300"></span>
            <span><?= esc($article['published_at']) ?></span>
            <span class="h-1 w-1 rounded-full bg-primary-300"></span>
            <span><?= esc($article['read_time']) ?></span>
        </div>

        <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-dark-900 lg:text-5xl">
            <?= esc($article['title']) ?>
        </h1>
        <p class="mt-5 text-lg leading-8 text-dark-600"><?= esc($article['excerpt']) ?></p>

        <div class="mt-8 space-y-5 text-base leading-8 text-dark-700">
            <?php foreach ($article['content'] as $paragraph): ?>
                <p><?= esc($paragraph) ?></p>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 rounded-[24px] border border-secondary-100 bg-secondary-50/80 p-6">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-secondary-700">Next step</p>
            <h2 class="mt-3 text-2xl font-bold text-dark-900">Turn discovery into action inside the app</h2>
            <p class="mt-3 text-sm leading-7 text-dark-600">
                Public SEO pages explain products and categories. When users are ready to buy, cart and order actions can continue inside the React application under <span class="font-semibold text-dark-800">/app</span>.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="<?= base_url('/shop') ?>" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
                    <i class="bi bi-bag"></i>
                    Browse products
                </a>
                <a href="<?= base_url('/login') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-dark-200 bg-white px-5 py-3 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Open app
                </a>
            </div>
        </div>
    </div>
</article>
<?= $this->endSection() ?>
