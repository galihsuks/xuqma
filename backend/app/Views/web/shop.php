<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
        <span class="inline-flex items-center gap-2 rounded-full bg-primary-100 px-4 py-2 text-sm font-semibold uppercase tracking-[0.22em] text-primary-800">
            <i class="bi bi-bag-heart"></i>
            SEO Storefront
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
                    <p class="mt-3 text-base leading-7 text-dark-600"><?= esc($product['summary']) ?></p>
                    <p class="mt-5 text-lg font-semibold text-primary-700"><?= esc($product['price']) ?></p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="<?= base_url('/products/' . $product['slug']) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
                            <i class="bi bi-eye"></i>
                            View detail
                        </a>
                        <a href="<?= base_url('/app/customer/cart?add_product=' . urlencode((string) $product['id']) . '&qty=1') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-dark-200 bg-white px-5 py-3 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
                            <i class="bi bi-cart-plus"></i>
                            Add to cart flow
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mt-10">
            <?= view('web/partials/empty_state', [
                'icon' => 'bi-bag-dash',
                'eyebrow' => 'Storefront Catalog',
                'title' => 'No products are available yet',
                'message' => 'Publish products from the admin catalog so the storefront can show real IT gear, accessories, and component listings here.',
                'primaryLabel' => 'Manage Products',
                'primaryUrl' => base_url('/app/admin/catalog/products'),
                'secondaryLabel' => 'Open Admin Dashboard',
                'secondaryUrl' => base_url('/app/admin/dashboard'),
            ]) ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
