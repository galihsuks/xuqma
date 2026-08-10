<?php
$articleItems = $articles ?? [];
$articleGridClass = $gridClass ?? 'mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3';
$articleShowPublishedAt = $showPublishedAt ?? true;
$articleShowReadMore = $showReadMore ?? true;
$articleCardClass = $cardClass ?? 'block group rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/40 transition hover:-translate-y-1 hover:shadow-xl';
?>

<?php if (!empty($articleItems)): ?>
    <div class="<?= esc($articleGridClass, 'attr') ?>">
        <?php foreach ($articleItems as $article): ?>
            <a href="<?= base_url('/articles/' . ($article['slug'] ?? '')) ?>" class="<?= esc($articleCardClass, 'attr') ?>">
                <div class="flex flex-wrap items-center gap-3 text-[10px] md:text-xs font-semibold uppercase tracking-[0.2em] text-primary-700">
                    <span><?= esc($article['category'] ?? '') ?></span>
                    <span class="h-1 w-1 rounded-full bg-primary-300"></span>
                    <span><?= esc($article['read_time'] ?? '') ?></span>
                </div>

                <h3 class="mt-2 md:mt-4 text-sm md:text-2xl font-bold text-dark-900 transition group-hover:text-primary-700">
                    <?= esc($article['title'] ?? '') ?>
                </h3>

                <p class="mt-1 md:mt-4 text-xs md:text-base leading-5 md:leading-7 text-dark-600"><?= esc($article['excerpt'] ?? '') ?></p>

                <?php if ($articleShowPublishedAt || $articleShowReadMore): ?>
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                        <?php if ($articleShowPublishedAt): ?>
                            <span class="text-sm text-dark-400"><?= esc($article['published_at'] ?? '') ?></span>
                        <?php endif; ?>

                        <?php if ($articleShowReadMore): ?>
                            <a href="<?= base_url('/articles/' . ($article['slug'] ?? '')) ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-600">
                                Read article
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
