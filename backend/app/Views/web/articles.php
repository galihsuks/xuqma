<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
        <span class="inline-flex items-center gap-2 rounded-full bg-secondary-100 px-4 py-2 text-sm font-semibold uppercase tracking-[0.22em] text-secondary-800">
            <i class="bi bi-journal-richtext"></i>
            Content hub
        </span>
        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-dark-900 lg:text-5xl">
            <?= esc($pageTitle) ?>
        </h1>
        <p class="mt-5 text-lg leading-8 text-dark-600">
            <?= esc($pageSubtitle) ?>
        </p>
    </div>

    <?php if (!empty($articles)): ?>
        <?= view('web/partials/article_list', [
            'articles' => $articles,
            'gridClass' => 'mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3',
            'titleTag' => 'h2',
        ]) ?>
    <?php else: ?>
        <div class="mt-10">
            <?= view('web/partials/empty_state', [
                'icon' => 'bi-journal-text',
                'eyebrow' => 'Content Pipeline',
                'title' => 'No published articles are available yet',
                'message' => 'Publish buying guides and IT content from the admin area so this public article hub can support your SEO traffic.',
                'primaryLabel' => 'Manage Articles',
                'primaryUrl' => base_url('/app/admin/content/articles'),
                'secondaryLabel' => 'Back to Homepage',
                'secondaryUrl' => base_url('/'),
            ]) ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
