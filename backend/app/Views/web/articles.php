<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
        <span class="inline-flex rounded-full bg-secondary-100 px-4 py-2 text-sm font-semibold uppercase tracking-[0.22em] text-secondary-800">
            Content hub
        </span>
        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-dark-900 lg:text-5xl">
            <?= esc($pageTitle) ?>
        </h1>
        <p class="mt-5 text-lg leading-8 text-dark-600">
            <?= esc($pageSubtitle) ?>
        </p>
    </div>

    <div class="mt-10 grid gap-6 lg:grid-cols-3">
        <?php foreach (($articles ?? []) as $article): ?>
            <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/40 transition hover:-translate-y-1 hover:shadow-xl">
                <div class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-primary-700">
                    <span><?= esc($article['category']) ?></span>
                    <span class="h-1 w-1 rounded-full bg-primary-300"></span>
                    <span><?= esc($article['read_time']) ?></span>
                </div>
                <h2 class="mt-4 text-2xl font-bold text-dark-900">
                    <a href="<?= base_url('/articles/' . $article['slug']) ?>" class="transition hover:text-primary-700">
                        <?= esc($article['title']) ?>
                    </a>
                </h2>
                <p class="mt-4 text-base leading-7 text-dark-600"><?= esc($article['excerpt']) ?></p>
                <div class="mt-6 flex items-center justify-between">
                    <span class="text-sm text-dark-400"><?= esc($article['published_at']) ?></span>
                    <a href="<?= base_url('/articles/' . $article['slug']) ?>" class="text-sm font-semibold text-primary-700 hover:text-primary-600">
                        Read article
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?= $this->endSection() ?>
