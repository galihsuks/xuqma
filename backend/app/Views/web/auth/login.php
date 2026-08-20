<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Xuqma</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                        secondary: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            200: '#fbcfe8',
                            300: '#f9a8d4',
                            400: '#f472b6',
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                            800: '#9d174d',
                            900: '#831843',
                        },
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    boxShadow: {
                        glow: '0 20px 60px -30px rgba(236,72,153,0.45)',
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-100 font-sans text-slate-900">
    <main class="relative grid min-h-screen place-items-center overflow-hidden px-4 py-8">
        <div class="pointer-events-none absolute -top-20 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-primary-300/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 right-0 h-72 w-72 rounded-full bg-secondary-300/40 blur-3xl"></div>

        <form
            action="<?= base_url('/login') ?>"
            method="post"
            class="relative z-10 grid w-full max-w-5xl overflow-hidden rounded-3xl border border-primary-100 bg-white shadow-glow md:grid-cols-2"
        >
            <input type="hidden" name="redirect" value="<?= esc($redirectTo ?? old('redirect') ?? '') ?>">

            <aside class="hidden h-full flex-col justify-between bg-gradient-to-br from-primary-900 via-primary-800 to-secondary-400 p-12 text-white md:flex">
                <div class="mb-3">
                    <div class="inline-flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl">
                        <img src="/assets/app_logo/mark.png" alt="UQMA" class="h-10 w-10 object-contain">
                    </div>
                    <p class="mt-1 text-sm uppercase tracking-[0.22em] text-white/80">XUQMA</p>
                </div>

                <div>
                    <h1 class="max-w-[360px] text-4xl font-semibold">
                        Sign in to continue your next tech upgrade.
                    </h1>
                    <p class="mt-4 max-w-sm text-sm leading-2 text-white/85">
                        Shop accessories, audio gear, and PC components with a faster checkout flow
                        across customer and admin experiences.
                    </p>
                </div>
            </aside>

            <section class="flex flex-col justify-center p-7 sm:p-12">
                <a href="<?= base_url('/') ?>" class="mb-2 flex items-center gap-2 md:hidden">
                    <span class="flex h-7 w-7 items-center justify-center overflow-hidden rounded-2xl shadow-glow">
                        <img src="/assets/app_logo/mark.png" alt="UQMA" class="h-7 w-7 object-contain">
                    </span>
                    <span class="text-lg font-light tracking-[0.28em] text-primary-700">UQMA</span>
                </a>

                <h2 class="text-3xl font-semibold text-slate-800">Welcome back</h2>
                <p class="mt-1 text-sm text-slate-500">Sign in to continue shopping.</p>

                <?php if (session('success')): ?>
                    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        <?= esc(session('success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                    <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <?= esc(session('error')) ?>
                    </div>
                <?php endif; ?>

                <?php $errors = session('errors') ?? []; ?>

                <div class="mt-6">
                    <label for="username" class="mb-2 block text-sm font-semibold text-slate-700">
                        Username / Email
                    </label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        value="<?= esc(old('username', '')) ?>"
                        placeholder="Enter your username or email"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-primary-300 focus:ring-4 focus:ring-primary-100"
                    >
                    <?php if (isset($errors['username'])): ?>
                        <p class="mt-2 text-sm text-rose-600"><?= esc($errors['username']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                        Password
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Enter your password"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-primary-300 focus:ring-4 focus:ring-primary-100"
                    >
                    <?php if (isset($errors['password'])): ?>
                        <p class="mt-2 text-sm text-rose-600"><?= esc($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <button
                    type="submit"
                    class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-200/70 transition hover:bg-primary-500"
                >
                    Sign in
                </button>

                <a
                    href="<?= base_url('/') ?>"
                    class="mt-4 inline-flex items-center justify-center text-sm font-semibold text-primary-700 transition hover:text-primary-600"
                >
                    View all products
                </a>
            </section>
        </form>
    </main>
</body>
</html>
