<?php $categoryItems = $categories ?? []; ?>

<?php if (!empty($categoryItems)): ?>
    <div class="mt-8 pt-1 pb-4 overflow-x-auto" style="scrollbar-width:none;-ms-overflow-style:none;">
        <div class="flex min-w-max gap-4" style="-webkit-overflow-scrolling:touch;">
            <?php foreach ($categoryItems as $category): ?>
                <a
                    href="<?= base_url('/category/' . $category['slug']) ?>"
                    class="flex w-[118px] shrink-0 flex-col items-center justify-center rounded-[28px] px-4 py-5 text-center shadow-primary-100/35 transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex h-16 w-16 items-center justify-center rounded-[24px] text-3xl text-white border border-white/70 bg-white/90">
                        <i class="bi <?= esc($category['icon']) ?> bg-gradient-to-br from-primary-500 to-secondary-400 bg-clip-text text-transparent"></i>
                    </div>
                    <p class="mt-4 text-sm font-light leading-5 text-dark-900"><?= esc($category['name']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
