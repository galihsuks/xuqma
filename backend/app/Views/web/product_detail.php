<?php
$productId = (string) $product['id'];
$cartQty = (int) (($customerCartQtyMap[$productId] ?? 0));
?>
<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<article class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-24">
    <a href="<?= base_url('/category/' . $product['category_slug']) ?>" class="inline-flex items-center gap-2 rounded-full border border-dark-200 bg-white px-4 py-2 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
        <i class="bi bi-arrow-left"></i>
        Back to <?= esc($product['category_name']) ?>
    </a>

    <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_0.92fr]">
        <div class="rounded-[32px] border border-white/70 bg-gradient-to-br from-primary-100 via-white to-secondary-100 p-8 shadow-glow">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-primary-500 to-secondary-400 text-2xl text-white">
                <i class="bi bi-gpu-card"></i>
            </div>
            <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-dark-900 lg:text-5xl">
                <?= esc($product['name']) ?>
            </h1>
            <p class="mt-5 text-lg leading-8 text-dark-600"><?= esc($product['summary']) ?></p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <span class="inline-flex rounded-full bg-primary-50 px-4 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">
                    <?= esc($product['category_name']) ?>
                </span>
                <span class="inline-flex rounded-full bg-secondary-50 px-4 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-secondary-700">
                    <?= esc($product['stock_badge']) ?>
                </span>
            </div>
        </div>

        <div class="rounded-[32px] border border-white/70 bg-white/90 p-8 shadow-lg shadow-primary-100/40">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary-700">Price</p>
            <p class="mt-3 text-4xl font-extrabold tracking-tight text-dark-900"><?= esc($product['price']) ?></p>
            <p class="mt-3 text-sm leading-7 text-dark-600"><?= esc($product['highlight']) ?></p>

            <?php if (!empty($product['specs'])): ?>
                <div class="mt-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-dark-500">Highlighted specs</p>
                    <ul class="mt-4 space-y-3 text-sm leading-7 text-dark-700">
                        <?php foreach ($product['specs'] as $spec): ?>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check2-circle mt-0.5 text-primary-600"></i>
                                <span><?= esc($spec) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="mt-8 flex flex-wrap gap-3">
                <?php if (!$isLoggedIn): ?>
                    <a href="<?= base_url('/login') ?>" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Sign in to shop
                    </a>
                <?php elseif (!$isCustomerRole): ?>
                    <a href="<?= esc($appProfileUrl) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
                        <i class="bi bi-person-circle"></i>
                        Open profile
                    </a>
                <?php elseif ($cartQty > 0): ?>
                    <div class="inline-flex items-center gap-2 rounded-2xl border border-dark-200 bg-white px-3 py-3 text-sm font-semibold text-dark-700">
                        <form action="<?= base_url('/cart/items/' . urlencode($productId) . '/decrement') ?>" method="post">
                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full transition hover:bg-primary-50 hover:text-primary-700">
                                <i class="bi bi-dash text-lg"></i>
                            </button>
                        </form>
                        <span class="min-w-8 text-center text-base font-semibold text-dark-900"><?= esc((string) $cartQty) ?></span>
                        <form action="<?= base_url('/cart/items/' . urlencode($productId) . '/increment') ?>" method="post">
                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full transition hover:bg-primary-50 hover:text-primary-700">
                                <i class="bi bi-plus text-lg"></i>
                            </button>
                        </form>
                    </div>
                    <a href="<?= esc($appCartUrl) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-dark-200 bg-white px-6 py-3 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
                        <i class="bi bi-bag"></i>
                        View cart
                    </a>
                <?php else: ?>
                    <form action="<?= base_url('/cart/items/' . urlencode($productId) . '/add') ?>" method="post">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
                            <i class="bi bi-cart-plus"></i>
                            Add to cart
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($relatedProducts)): ?>
        <section class="mt-12">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700">Related picks</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-dark-900">More from this category</h2>
                </div>
                <a href="<?= base_url('/category/' . $product['category_slug']) ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
                    View category
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <?= view('web/partials/product_card', [
                        'product' => $relatedProduct,
                        'appProfileUrl' => $appProfileUrl,
                        'customerCartQtyMap' => $customerCartQtyMap,
                        'isCustomerRole' => $isCustomerRole,
                        'isLoggedIn' => $isLoggedIn,
                    ]) ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</article>
<?= $this->endSection() ?>
