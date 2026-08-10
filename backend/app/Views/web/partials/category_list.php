<?php $categoryItems = $categories ?? []; ?>

<?php if (!empty($categoryItems)): ?>
    <div class="mt-5 md:mt-8 pt-1 pb-4 overflow-x-auto px-8 md:px-0" style="scrollbar-width:none;-ms-overflow-style:none;">
        <div class="flex min-w-max gap-0 md:gap-4" style="-webkit-overflow-scrolling:touch;">
            <?php foreach ($categoryItems as $category): ?>
                <a
                    href="<?= base_url('/category/' . $category['slug']) ?>"
                    class="flex w-[85px] md:w-[118px] shrink-0 flex-col items-center justify-center rounded-sm md:rounded-[28px] px-3 py-0 md:px-4 md:py-5 text-center shadow-primary-100/35 transition hover:-translate-y-1 md:hover:shadow-lg"
                >
                    <div class="flex h-10 w-10 md:h-16 md:w-16 items-center justify-center rounded-[24px] text-3xl text-white">
                        <i class="bi <?= esc($category['icon']) ?> bg-gradient-to-br from-primary-500 to-secondary-400 bg-clip-text text-transparent"></i>
                    </div>
                    <p class="mt-4 text-[10px] md:text-sm font-light leading-3 md:leading-5 text-dark-600 md:text-dark-900"><?= esc($category['name']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
