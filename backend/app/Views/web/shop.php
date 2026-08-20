<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<section class="mt-5 mx-auto max-w-7xl px-6 pb-6 lg:px-8 pt-6 lg:pt-16">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-primary-700">Category</p>
            
            <h2 class="hidden md:block mt-3 text-3xl font-bold tracking-tight text-dark-900">Choose the category that fits your setup best</h2>
            <h2 class="md:hidden block mt-3 text-3xl font-bold tracking-tight text-dark-900">Find your category</h2>
            
            <p class="hidden md:block mt-3 text-sm md:text-base leading-6 md:leading-8 text-dark-600">
                Explore the gear people shop for most, from daily phone essentials to serious desktop upgrades and performance-focused components.
            </p>
        </div>
    </div>
</section>
<section id="sticky-category" class="z-20 sticky top-[64px] mx-auto max-w-7xl px-0 pb-2 md:pb-6 lg:px-8 transition-all duration-300 bg-transparent">
    <?php if (!empty($categories)): ?>
        <?= view('web/partials/category_list', ['categories' => $categories]) ?>
    <?php else: ?>
        <div class="mt-8">
            <?= view('web/partials/empty_state', [
                'icon' => 'bi-grid-3x3-gap',
                'eyebrow' => 'Catalog Setup',
                'title' => 'Categories have not been published yet',
                'message' => 'Create and display product categories from the admin area so visitors can browse the storefront by section.',
                'primaryLabel' => 'Open Admin App',
                'primaryUrl' => base_url('/app/admin/catalog/categories'),
                'secondaryLabel' => 'Open Customer App',
                'secondaryUrl' => base_url('/app/customer/profile'),
            ]) ?>
        </div>
    <?php endif; ?>
</section>

<section class="relative z-1 mt-5 mx-auto max-w-7xl px-6 lg:px-8 pb-7 lg:pb-24">
    <?php if (!empty($products)): ?>
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs md:text-sm font-semibold uppercase tracking-[0.22em] text-secondary-700">Products</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-dark-900">Our Products</h2>
                <p class="mt-3 text-base leading-8 text-dark-600">
                    From compact mobile accessories to full desk and PC upgrades, each listing is here to help you compare faster and choose with confidence.
                </p>
            </div>
        </div>

        <div class="mt-10 grid gap-6 grid-cols-2 xl:grid-cols-3">
            <?php foreach ($products as $product): ?>
                <?= view('web/partials/product_card', [
                    'product' => $product,
                    'appProfileUrl' => $appProfileUrl,
                    'customerCartQtyMap' => $customerCartQtyMap,
                    'isCustomerRole' => $isCustomerRole,
                    'isLoggedIn' => $isLoggedIn,
                ]) ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mt-10">
            <?= view('web/partials/empty_state', [
                'icon' => 'bi-bag-dash',
                'eyebrow' => 'Shop Catalog',
                'title' => 'No products are available yet',
                'message' => 'Products will appear here once the catalog is published, giving shoppers a place to explore accessories, components, and setup gear.',
                'primaryLabel' => 'Manage Products',
                'primaryUrl' => base_url('/app/admin/catalog/products'),
                'secondaryLabel' => 'Back to Home',
                'secondaryUrl' => base_url('/'),
            ]) ?>
        </div>
    <?php endif; ?>
</section>
<script>
    const categoryContainer = document.getElementById('sticky-category');
    const isMobile = window.innerWidth < 600;

    window.addEventListener('scroll', () => {
        if (window.scrollY > (isMobile ? 133 : 290)) {
            // Background when scrolled/sticky
            categoryContainer.classList.add('bg-white/80', 'backdrop-blur-xl', 'shadow-lg');
            categoryContainer.classList.remove('bg-transparent');
        } else {
            // Default background at the top
            categoryContainer.classList.add('bg-transparent');
            categoryContainer.classList.remove('bg-white/80', 'backdrop-blur-xl', 'shadow-lg');
        }
    });
</script>
<?= $this->endSection() ?>
