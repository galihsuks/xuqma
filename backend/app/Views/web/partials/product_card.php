<?php
$productData = $product ?? [];
$description = (string) ($productData['highlight'] ?? $productData['summary'] ?? '');
$detailUrl = base_url('/products/' . ($productData['slug'] ?? ''));
$cartUrl = base_url('/app/customer/cart?add_product=' . urlencode((string) ($productData['id'] ?? '')) . '&qty=1');
?>

<a href="<?= esc($detailUrl) ?>" class="block rounded-[12px] md:rounded-[28px] bg-white/90 p-3 md:p-6 shadow-lg shadow-primary-100/40 transition md:hover:-translate-y-1 md:hover:shadow-xl">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-1 md:gap-3">
        <span class="block rounded-full bg-primary-50 px-2 md:px-3 py-1 text-[6px] md:text-xs font-semibold uppercase tracking-[0.18em] text-primary-700">
            <?= esc($productData['category_name'] ?? '') ?>
        </span>
        <?php if ($productData['stock_badge'] === 'Limited') {  ?>
            <span class="hidden md:inline-flex rounded-full bg-secondary-50 px-2 md:px-3 py-1 text-[6px] md:text-xs font-semibold uppercase tracking-[0.18em] text-secondary-700">
                <?= esc($productData['stock_badge']) ?>
            </span>
        <?php } ?>
    </div>
    <h3 class="mt-3 md:mt-5 text-sm md:text-2xl font-bold text-dark-900 max-w-[250px]"><?= esc($productData['name'] ?? '') ?></h3>
    <div class="mt-1 md:mt-3 flex justify-between items-center">
        <p class="text-xs md:text-lg font-semibold text-primary-700"><?= esc($productData['price'] ?? '') ?></p>
        <div class="flex w-[20px] h-[20px] md:w-auto md:h-auto items-center justify-center gap-2 rounded-full md:rounded-2xl border border-dark-50 md:border-dark-200 bg-white text-dark-700 hover:border-primary-200 hover:text-primary-700 px-0 py-0 md:px-5 md:py-3 text-sm font-semibold transition">
            <i class="bi bi-bag-plus hidden md:block"></i>
            <i class="bi bi-plus md:hidden block"></i>
            <p class="hidden md:block"><?= $productData['stock_badge'] === 'Pre Order' ? 'Pre Order' : "Buy" ?></p>
        </div>
    </div>
</a>
