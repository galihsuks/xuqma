<?php
$productData = $product ?? [];
$description = (string) ($productData['highlight'] ?? $productData['summary'] ?? '');
$detailUrl = base_url('/products/' . ($productData['slug'] ?? ''));
$productId = (string) ($productData['id'] ?? '');
$cartQty = (int) (($customerCartQtyMap[$productId] ?? 0));
$isLoggedInValue = (bool) ($isLoggedIn ?? false);
$isCustomerRoleValue = (bool) ($isCustomerRole ?? false);
$loginUrl = base_url('/login');
$profileUrl = $appProfileUrl ?? base_url('/app/customer/profile');
?>

<article class="rounded-[12px] md:rounded-[28px] bg-white/90 p-3 md:p-6 shadow-lg shadow-primary-100/40 transition md:hover:-translate-y-1 md:hover:shadow-xl">
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
    <a href="<?= esc($detailUrl) ?>" class="block">
        <h3 class="mt-3 md:mt-5 text-sm md:text-2xl font-bold text-dark-900 max-w-[250px]"><?= esc($productData['name'] ?? '') ?></h3>
    </a>
    <div class="mt-1 md:mt-3 flex justify-between items-center">
        <p class="text-xs md:text-lg font-semibold text-primary-700"><?= esc($productData['price'] ?? '') ?></p>
        <?php if (!$isLoggedInValue): ?>
            <a href="<?= esc($loginUrl) ?>" class="flex w-[20px] h-[20px] md:w-auto md:h-auto items-center justify-center gap-2 rounded-full md:rounded-2xl border border-dark-50 md:border-dark-200 bg-white text-dark-700 hover:border-primary-200 hover:text-primary-700 px-0 py-0 md:px-5 md:py-3 text-sm font-semibold transition">
                <i class="bi bi-box-arrow-in-right hidden md:block"></i>
                <i class="bi bi-plus md:hidden block"></i>
                <p class="hidden md:block">Login</p>
            </a>
        <?php elseif (!$isCustomerRoleValue): ?>
            <a href="<?= esc($profileUrl) ?>" class="flex w-[20px] h-[20px] md:w-auto md:h-auto items-center justify-center gap-2 rounded-full md:rounded-2xl border border-dark-50 md:border-dark-200 bg-white text-dark-700 hover:border-primary-200 hover:text-primary-700 px-0 py-0 md:px-5 md:py-3 text-sm font-semibold transition">
                <i class="bi bi-person hidden md:block"></i>
                <i class="bi bi-person md:hidden block"></i>
                <p class="hidden md:block">Profile</p>
            </a>
        <?php elseif ($cartQty > 0): ?>
            <div class="inline-flex items-center gap-1 rounded-full md:rounded-2xl border border-dark-50 md:border-dark-200 bg-white px-1 py-1 md:px-2 md:py-2">
                <form action="<?= base_url('/cart/items/' . urlencode($productId) . '/decrement') ?>" method="post">
                    <button type="submit" class="inline-flex h-5 w-5 md:h-9 md:w-9 items-center justify-center rounded-full text-dark-700 transition hover:bg-primary-50 hover:text-primary-700">
                        <i class="bi bi-dash text-sm md:text-base"></i>
                    </button>
                </form>
                <span class="min-w-4 md:min-w-8 text-center text-[10px] md:text-sm font-semibold text-dark-900"><?= esc((string) $cartQty) ?></span>
                <form action="<?= base_url('/cart/items/' . urlencode($productId) . '/increment') ?>" method="post">
                    <button type="submit" class="inline-flex h-5 w-5 md:h-9 md:w-9 items-center justify-center rounded-full text-dark-700 transition hover:bg-primary-50 hover:text-primary-700">
                        <i class="bi bi-plus text-sm md:text-base"></i>
                    </button>
                </form>
            </div>
        <?php else: ?>
            <form action="<?= base_url('/cart/items/' . urlencode($productId) . '/add') ?>" method="post">
                <button type="submit" class="flex w-[20px] h-[20px] md:w-auto md:h-auto items-center justify-center gap-2 rounded-full md:rounded-2xl border border-dark-50 md:border-dark-200 bg-white text-dark-700 hover:border-primary-200 hover:text-primary-700 px-0 py-0 md:px-5 md:py-3 text-sm font-semibold transition">
                    <i class="bi bi-bag-plus hidden md:block"></i>
                    <i class="bi bi-plus md:hidden block"></i>
                    <p class="hidden md:block"><?= $productData['stock_badge'] === 'Pre Order' ? 'Pre Order' : 'Buy' ?></p>
                </button>
            </form>
        <?php endif; ?>
    </div>
</article>
