<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
        <span class="inline-flex rounded-full bg-primary-100 px-4 py-2 text-sm font-semibold uppercase tracking-[0.22em] text-primary-800">
            Architecture overview
        </span>
        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-dark-900 lg:text-5xl">
            <?= esc($pageTitle) ?>
        </h1>
        <p class="mt-5 text-lg leading-8 text-dark-600">
            <?= esc($pageSubtitle) ?>
        </p>
    </div>

    <div class="mt-10 grid gap-6 lg:grid-cols-3">
        <?php foreach (($highlights ?? []) as $highlight): ?>
            <article class="rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-lg shadow-primary-100/40">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-secondary-400 text-lg font-extrabold text-white">
                    <?= esc(substr($highlight, 0, 1)) ?>
                </div>
                <p class="mt-5 text-base leading-7 text-dark-700"><?= esc($highlight) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?= $this->endSection() ?>
