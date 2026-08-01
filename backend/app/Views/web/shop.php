<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
        <span class="inline-flex items-center gap-2 rounded-full bg-primary-100 px-4 py-2 text-sm font-semibold uppercase tracking-[0.22em] text-primary-800">
            <i class="bi bi-bag-heart"></i>
            Curated tech store
        </span>
        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-dark-900 lg:text-5xl">
            <?= esc($pageTitle) ?>
        </h1>
        <p class="mt-5 text-lg leading-8 text-dark-600">
            <?= esc($pageSubtitle) ?>
        </p>
    </div>

    <?php if (!empty($categories)): ?>
        <div class="mt-10 grid gap-4 lg:grid-cols-4">
            <?php foreach ($categories as $category): ?>
                <a href="<?= base_url('/category/' . $category['slug']) ?>" class="rounded-[26px] border border-white/70 bg-white/90 p-5 shadow-lg shadow-primary-100/35 transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-secondary-400 text-lg text-white">
                        <i class="bi <?= esc($category['icon']) ?>"></i>
                    </div>
                    <h2 class="mt-4 text-lg font-bold text-dark-900"><?= esc($category['name']) ?></h2>
                    <p class="mt-2 text-sm leading-6 text-dark-600"><?= esc($category['description']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($products)): ?>
        <div class="mt-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-secondary-700">Popular product picks</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-dark-900">Browse products built for comfort, clarity, and performance</h2>
                <p class="mt-3 text-base leading-8 text-dark-600">
                    From compact mobile accessories to full desk and PC upgrades, each listing is here to help you compare faster and choose with confidence.
                </p>
            </div>
        </div>

        <div class="mt-10 grid gap-6 grid-cols-2 xl:grid-cols-3">
            <?php foreach ($products as $product): ?>
                <?= view('web/partials/product_card', ['product' => $product]) ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mt-10">
            <?= view('web/partials/empty_state', [
                'icon' => 'bi-bag-dash',
                'eyebrow' => 'Shop Catalog',
                'title' => 'No products are available yet',
                'message' => 'Products will appear here once the catalog is published, giving shoppers a place to explore accessories, components, and setup gear.',
                'primaryLabel' => 'Manage Products',
                'primaryUrl' => base_url('/app/admin/catalog/products'),
                'secondaryLabel' => 'Back to Home',
                'secondaryUrl' => base_url('/'),
            ]) ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
