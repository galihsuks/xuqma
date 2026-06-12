<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mx-auto grid max-w-7xl gap-10 px-5 py-16 lg:grid-cols-[1.15fr_0.85fr] lg:px-8 lg:py-24">
    <div class="rounded-[32px] border border-white/70 bg-white/85 p-8 shadow-glow backdrop-blur-xl lg:p-10">
        <span class="inline-flex rounded-full bg-secondary-100 px-4 py-2 text-sm font-semibold uppercase tracking-[0.22em] text-secondary-800">
            <?= esc($heroBadge ?? 'Public Website') ?>
        </span>
        <h1 class="mt-6 max-w-3xl text-4xl font-extrabold tracking-tight text-dark-900 lg:text-6xl">
            <?= esc($pageTitle) ?>
        </h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-dark-600">
            <?= esc($pageSubtitle) ?>
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="<?= base_url('/articles') ?>" class="inline-flex items-center rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500">
                Explore articles
            </a>
            <a href="<?= base_url('/about') ?>" class="inline-flex items-center rounded-2xl border border-dark-200 bg-white px-6 py-3 text-sm font-semibold text-dark-700 transition hover:border-primary-200 hover:text-primary-700">
                Learn the architecture
            </a>
        </div>
    </div>

    <div class="grid gap-4">
        <?php foreach (($stats ?? []) as $stat): ?>
            <article class="rounded-[28px] border border-white/70 bg-gradient-to-br from-white to-primary-50 p-6 shadow-lg shadow-primary-100/50">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700"><?= esc($stat['label']) ?></p>
                <p class="mt-3 text-2xl font-bold text-dark-900"><?= esc($stat['value']) ?></p>
            </article>
        <?php endforeach; ?>

        <article class="rounded-[28px] border border-primary-100 bg-dark-900 p-6 text-white shadow-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-300">Suggested flow</p>
            <ol class="mt-4 space-y-3 text-sm leading-7 text-slate-100">
                <li>1. Build SEO landing pages in CodeIgniter views.</li>
                <li>2. Keep application-heavy interactions inside React under <span class="font-semibold text-white">/admin</span>.</li>
                <li>3. Reuse one API backend for auth, data, logs, and permissions.</li>
            </ol>
        </article>
    </div>
</section>
<?= $this->endSection() ?>
