<?php
$productData = $product ?? [];
$description = (string) ($productData['highlight'] ?? $productData['summary'] ?? '');
$detailUrl = base_url('/products/' . ($productData['slug'] ?? ''));
$cartUrl = base_url('/app/customer/cart?add_product=' . urlencode((string) ($productData['id'] ?? '')) . '&qty=1');
?>

<article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/40">
    <div class="flex items-center justify-between gap-3">
        <span class="inline-flex rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-primary-700">
            <?= esc($productData['category_name'] ?? '') ?>
        </span>
        <span class="inline-flex rounded-full bg-secondary-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-secondary-700">
            <?= esc($productData['stock_badge'] ?? '') ?>
        </span>
    </div>

    <h3 class="mt-5 text-2xl font-bold text-dark-900"><?= esc($productData['name'] ?? '') ?></h3>
    <p class="mt-3 overflow-hidden text-base leading-7 text-dark-600" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
        <?= esc($description) ?>
    </p>
    <p class="mt-5 text-lg font-semibold text-primary-700"><?= esc($productData['price'] ?? '') ?></p>

    <div class="mt-5 flex flex-wrap gap-3">
        <a href="<?= esc($detailUrl) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
            <i class="bi bi-eye"></i>
            View details
        </a>
        <a href="<?= esc($cartUrl) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-dark-200 bg-white text-dark-700 hover:border-primary-200 hover:text-primary-700 px-5 py-3 text-sm font-semibold transition">
            <i class="bi bi-cart-plus"></i>
            Add to cart
        </a>
    </div>
</article>
