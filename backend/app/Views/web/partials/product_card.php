<?php
$productData = $product ?? [];
$description = (string) ($productData['highlight'] ?? $productData['summary'] ?? '');
$detailUrl = base_url('/products/' . ($productData['slug'] ?? ''));
$cartUrl = base_url('/app/customer/cart?add_product=' . urlencode((string) ($productData['id'] ?? '')) . '&qty=1');
$productBadge = $productData['stock_badge'] === 'Ready Stock' ? 'Ready' : ($productData['stock_badge'] === 'Limited' ? 'Limited' : "PO");
?>

<a href="<?= esc($detailUrl) ?>" class="block rounded-[12px] md:rounded-[28px] border border-white/70 bg-white/90 p-0 md:p-6 shadow-md md:shadow-lg shadow-primary-100/40">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-1 md:gap-3">
        <span class="inline-flex rounded-full bg-primary-50 px-2 md:px-3 py-1 text-[6px] md:text-xs font-semibold uppercase tracking-[0.18em] text-primary-700">
            <?= esc($productData['category_name'] ?? '') ?>
        </span>
        <span class="inline-flex rounded-full bg-secondary-50 px-2 md:px-3 py-1 text-[6px] md:text-xs font-semibold uppercase tracking-[0.18em] text-secondary-700">
            <?= esc($productBadge ?? '') ?>
        </span>
    </div>

    <h3 class="mt-3 md:mt-5 text-sm md:text-2xl font-bold text-dark-900"><?= esc($productData['name'] ?? '') ?></h3>
    <p class="mt-1 md:mt-3 overflow-hidden text-[10px] md:text-base leading-4 md:leading-7 text-dark-600" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
        <?= esc($description) ?>
    </p>
    <p class="mt-3 md:mt-5 text-xs md:text-lg font-semibold text-primary-700"><?= esc($productData['price'] ?? '') ?></p>

    <div class="mt-5 flex justify-end gap-3">
        <?php if ($productBadge === 'PO') { ?>
        <div class="inline-flex items-center gap-2 rounded-2xl border border-dark-200 bg-white text-dark-700 hover:border-primary-200 hover:text-primary-700 px-5 py-3 text-sm font-semibold transition">
            <i class="bi bi-bag-plus"></i>
            Buy
        </div>
        <?php } else { ?>
        <?php } ?>
    </div>
</a>
