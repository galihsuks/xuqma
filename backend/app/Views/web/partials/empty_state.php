<?php
$emptyIcon = $icon ?? 'bi-inbox';
$emptyEyebrow = $eyebrow ?? 'Coming Soon';
$emptyTitle = $title ?? 'No content available yet';
$emptyMessage = $message ?? 'Please check back again after the storefront content is published.';
$emptyPrimaryLabel = $primaryLabel ?? null;
$emptyPrimaryUrl = $primaryUrl ?? null;
$emptySecondaryLabel = $secondaryLabel ?? null;
$emptySecondaryUrl = $secondaryUrl ?? null;
?>

<article class="rounded-[30px] border border-dark-200 bg-white/92 p-8 text-center shadow-lg shadow-primary-100/30">
    <div class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-primary-500 to-secondary-400 text-2xl text-white">
        <i class="bi <?= esc($emptyIcon) ?>"></i>
    </div>
    <p class="mt-5 text-sm font-semibold uppercase tracking-[0.22em] text-primary-700"><?= esc($emptyEyebrow) ?></p>
    <h2 class="mt-3 text-3xl font-bold tracking-tight text-dark-900"><?= esc($emptyTitle) ?></h2>
    <p class="mx-auto mt-4 max-w-2xl text-base leading-8 text-dark-600"><?= esc($emptyMessage) ?></p>

    <?php if ($emptyPrimaryLabel || $emptySecondaryLabel): ?>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <?php if ($emptyPrimaryLabel && $emptyPrimaryUrl): ?>
                <a href="<?= esc($emptyPrimaryUrl) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
                    <i class="bi bi-arrow-right-circle"></i>
                    <?= esc($emptyPrimaryLabel) ?>
                </a>
            <?php endif; ?>

            <?php if ($emptySecondaryLabel && $emptySecondaryUrl): ?>
                <a href="<?= esc($emptySecondaryUrl) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-dark-200 bg-white px-6 py-3 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <?= esc($emptySecondaryLabel) ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</article>
