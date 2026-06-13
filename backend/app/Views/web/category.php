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
                <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/40">
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-primary-700">
                            <?= esc($product['category_name']) ?>
                        </span>
                        <span class="inline-flex rounded-full bg-secondary-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-secondary-700">
                            <?= esc($product['stock_badge']) ?>
                        </span>
                    </div>
                    <h2 class="mt-5 text-2xl font-bold text-dark-900"><?= esc($product['name']) ?></h2>
                    <p class="mt-3 text-base leading-7 text-dark-600"><?= esc($product['highlight']) ?></p>
                    <?php if (!empty($product['specs'])): ?>
                        <ul class="mt-4 space-y-2 text-sm leading-6 text-dark-600">
                            <?php foreach ($product['specs'] as $spec): ?>
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-check2-circle mt-0.5 text-primary-600"></i>
                                    <span><?= esc($spec) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="mt-5 flex items-center justify-between gap-3">
                        <p class="text-lg font-semibold text-primary-700"><?= esc($product['price']) ?></p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="<?= base_url('/products/' . $product['slug']) ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
                                Detail
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            <a href="<?= base_url('/app/customer/cart?add_product=' . urlencode((string) $product['id']) . '&qty=1') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-secondary-700 hover:text-secondary-600">
                                Add to cart
                                <i class="bi bi-cart-plus"></i>
                            </a>
                        </div>
                    </div>
                </article>
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
