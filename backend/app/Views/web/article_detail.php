<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<article class="mx-auto max-w-4xl px-5 py-16 lg:px-8 lg:py-24">
    <a href="<?= base_url('/articles') ?>" class="inline-flex items-center rounded-full border border-dark-200 bg-white px-4 py-2 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
        Back to articles
    </a>

    <div class="mt-8 rounded-[32px] border border-white/70 bg-white/90 p-8 shadow-glow lg:p-10">
        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.22em] text-primary-700">
            <span><?= esc($article['category']) ?></span>
            <span class="h-1 w-1 rounded-full bg-primary-300"></span>
            <span><?= esc($article['published_at']) ?></span>
            <span class="h-1 w-1 rounded-full bg-primary-300"></span>
            <span><?= esc($article['read_time']) ?></span>
        </div>

        <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-dark-900 lg:text-5xl">
            <?= esc($article['title']) ?>
        </h1>
        <p class="mt-5 text-lg leading-8 text-dark-600"><?= esc($article['excerpt']) ?></p>

        <div class="mt-8 space-y-5 text-base leading-8 text-dark-700">
            <?php foreach ($article['content'] as $paragraph): ?>
                <p><?= esc($paragraph) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</article>
<?= $this->endSection() ?>
