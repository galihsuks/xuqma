<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
        <span class="inline-flex items-center gap-2 rounded-full bg-secondary-100 px-4 py-2 text-sm font-semibold uppercase tracking-[0.22em] text-secondary-800">
            <i class="bi <?= esc($category['icon']) ?>"></i>
            Category Page
        </span>
        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-dark-900 lg:text-5xl">
            <?= esc($pageTitle) ?>
        </h1>
        <p class="mt-5 text-lg leading-8 text-dark-600">
            <?= esc($pageSubtitle) ?>
        </p>
    </div>

    <?php if (!empty($products)): ?>
        <div class="mt-10 grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($products as $product): ?>
                <?= view('web/partials/product_card', ['product' => $product]) ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mt-10">
            <?= view('web/partials/empty_state', [
                'icon' => 'bi-box-seam',
                'eyebrow' => 'Category Catalog',
                'title' => 'This category has no published products yet',
                'message' => 'Add products to this category from the admin catalog so visitors can explore relevant items here.',
                'primaryLabel' => 'Manage Products',
                'primaryUrl' => base_url('/app/admin/catalog/products'),
                'secondaryLabel' => 'Back to Shop',
                'secondaryUrl' => base_url('/shop'),
            ]) ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
