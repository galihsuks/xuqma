<?php

namespace App\Database\Seeds;

use App\Models\ArticleModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Database\Seeder;

class EcommerceCatalogSeeder extends Seeder
{
    public function run()
    {
        $categoryModel = new ProductCategoryModel();
        $productModel = new ProductModel();
        $articleModel = new ArticleModel();

        $categories = [
            [
                'name' => 'Phone Accessories',
                'slug' => 'phone-accessories',
                'description' => 'Fast charging adapters, braided cables, magsafe gear, and premium daily-carry accessories for modern phones.',
                'icon' => 'bi-phone',
                'display' => 1,
                'sort' => 1,
            ],
            [
                'name' => 'Audio & Headphones',
                'slug' => 'audio-headphones',
                'description' => 'Wireless earbuds, ANC headphones, desk audio gear, and creator-friendly listening accessories.',
                'icon' => 'bi-headphones',
                'display' => 1,
                'sort' => 2,
            ],
            [
                'name' => 'PC Components',
                'slug' => 'pc-components',
                'description' => 'VGA, RAM, SSD, cooling, and builder-ready upgrade parts for gaming and workstation systems.',
                'icon' => 'bi-pc-display',
                'display' => 1,
                'sort' => 3,
            ],
            [
                'name' => 'Displays & Setup',
                'slug' => 'display-setup',
                'description' => 'Monitors, monitor arms, USB hubs, docks, and ergonomic setup pieces for clean desks.',
                'icon' => 'bi-display',
                'display' => 1,
                'sort' => 4,
            ],
        ];

        $savedCategories = [];
        foreach ($categories as $category) {
            $existing = $categoryModel->getCategoryBySlug($category['slug']);
            $saved = $existing
                ? $categoryModel->updateCategoryById($existing['id'], $category)
                : $categoryModel->insertCategory($category);
            $savedCategories[$category['slug']] = $saved;
        }

        $products = [
            [
                'category_slug' => 'phone-accessories',
                'sku' => 'ACC-GAN-140',
                'name' => 'VoltLink 140W GaN Charger',
                'slug' => 'voltlink-140w-gan-charger',
                'summary' => 'Fast, compact charging for phones, tablets, handheld consoles, and USB-C laptops from one premium GaN brick.',
                'description' => 'A premium multi-port charger built for fast charging across phone, tablet, and laptop workflows.',
                'highlight' => 'Three ports, travel-ready body, and reliable multi-device fast charging.',
                'price' => 899000,
                'stock' => 42,
                'stock_badge' => 'Ready Stock',
                'is_featured' => 1,
                'display' => 1,
                'sort' => 1,
            ],
            [
                'category_slug' => 'audio-headphones',
                'sku' => 'AUD-ANC-001',
                'name' => 'PulseWave ANC Headphones',
                'slug' => 'pulsewave-anc-headphones',
                'summary' => 'Wireless over-ear headphones with strong noise cancellation and long battery life for travel, coding, and focus sessions.',
                'description' => 'Balanced wireless audio with comfort and battery life tuned for long work sessions.',
                'highlight' => 'Balanced tuning, soft earcups, and battery that lasts through workdays.',
                'price' => 2199000,
                'stock' => 0,
                'stock_badge' => 'Pre Order',
                'is_featured' => 1,
                'display' => 1,
                'sort' => 2,
            ],
            [
                'category_slug' => 'pc-components',
                'sku' => 'VGA-NEB-4080',
                'name' => 'Nebula RTX Graphics Card',
                'slug' => 'nebula-rtx-graphics-card',
                'summary' => 'A capable graphics card for modern 1440p gaming, rendering workloads, and creator-focused desktop setups.',
                'description' => 'High-demand graphics card suitable for gaming and creator workloads.',
                'highlight' => 'Strong thermals, stable clocks, and room for demanding game libraries.',
                'price' => 8750000,
                'stock' => 7,
                'stock_badge' => 'Limited',
                'is_featured' => 1,
                'display' => 1,
                'sort' => 3,
            ],
            [
                'category_slug' => 'pc-components',
                'sku' => 'RAM-DDR5-32',
                'name' => 'Aurora DDR5 32GB Kit',
                'slug' => 'aurora-ddr5-32gb-kit',
                'summary' => 'High-speed DDR5 memory kit aimed at multitasking builds, coding rigs, and content-heavy workflows.',
                'description' => 'Fast dual-channel memory kit for balanced gaming and productivity builds.',
                'highlight' => 'Clean heat spreader, tuned profile support, and solid daily stability.',
                'price' => 1899000,
                'stock' => 18,
                'stock_badge' => 'Ready Stock',
                'is_featured' => 0,
                'display' => 1,
                'sort' => 4,
            ],
            [
                'category_slug' => 'display-setup',
                'sku' => 'MON-27-2K',
                'name' => 'TitanView 27" 2K Monitor',
                'slug' => 'titanview-27-2k-monitor',
                'summary' => 'A sharp 27-inch monitor for coding, content work, and high-refresh daily multitasking.',
                'description' => 'Sharp 1440p monitor designed for desk productivity and mixed entertainment use.',
                'highlight' => 'Crisp panel, clean design, and versatile desk-ready footprint.',
                'price' => 3250000,
                'stock' => 10,
                'stock_badge' => 'Ready Stock',
                'is_featured' => 0,
                'display' => 1,
                'sort' => 5,
            ],
            [
                'category_slug' => 'display-setup',
                'sku' => 'KEY-AET-X1',
                'name' => 'Aether X1 Mechanical Keyboard',
                'slug' => 'aether-x1-mechanical-keyboard',
                'summary' => 'Desk setup keyboard with hot-swappable switches and a refined layout built for long work sessions.',
                'description' => 'Premium mechanical keyboard for setup lovers and daily work comfort.',
                'highlight' => 'Compact footprint, satisfying typing, and strong starter appeal for premium setups.',
                'price' => 1499000,
                'stock' => 14,
                'stock_badge' => 'Ready Stock',
                'is_featured' => 1,
                'display' => 1,
                'sort' => 6,
            ],
        ];

        foreach ($products as $product) {
            $payload = $product;
            $payload['category_id'] = $savedCategories[$product['category_slug']]['id'];
            unset($payload['category_slug']);

            $existing = $productModel->getProductBySlug($payload['slug']);
            if ($existing) {
                $productModel->updateProductById($existing['id'], $payload);
                continue;
            }

            $productModel->insertProduct($payload);
        }

        $articles = [
            [
                'title' => 'How to choose a monitor for coding, editing, and gaming without overspending',
                'slug' => 'how-to-pick-a-monitor-for-coding-and-gaming',
                'category' => 'Buying Guide',
                'excerpt' => 'A practical buying guide for people who want one display to handle work focus, color-sensitive tasks, and play time.',
                'content' => 'For many buyers, a monitor has to serve more than one role. It may be your coding screen during the day, your editing canvas in the afternoon, and your gaming display at night.',
                'author_name' => 'Editorial Team',
                'status' => 'published',
                'published_at' => '2026-06-12 09:00:00',
                'read_time' => '5 min read',
            ],
            [
                'title' => 'DDR4 vs DDR5 for modern desktop builds',
                'slug' => 'ddr4-vs-ddr5-for-modern-desktop-builds',
                'category' => 'Components',
                'excerpt' => 'Memory standards can be confusing when prices move quickly. Here is a grounded way to decide which upgrade path makes sense.',
                'content' => 'DDR5 offers higher bandwidth and a stronger long-term platform story, but the value depends on the motherboard, CPU choice, and your actual workload.',
                'author_name' => 'Arga Putra',
                'status' => 'published',
                'published_at' => '2026-06-10 10:00:00',
                'read_time' => '4 min read',
            ],
            [
                'title' => 'Phone accessories worth bundling for a smarter daily carry setup',
                'slug' => 'must-have-phone-accessories-for-fast-daily-carry',
                'category' => 'Accessories',
                'excerpt' => 'Chargers, cables, stands, and compact accessories can change how practical your phone setup feels every day.',
                'content' => 'A powerful phone still feels incomplete when the accessories around it are weak. Slow chargers, fragile cables, and poor desk support create daily friction.',
                'author_name' => 'Editorial Team',
                'status' => 'draft',
                'published_at' => null,
                'read_time' => '3 min read',
            ],
        ];

        foreach ($articles as $article) {
            $existing = $articleModel->getArticleBySlug($article['slug']);
            if ($existing) {
                $articleModel->updateArticleById($existing['id'], $article);
                continue;
            }

            $articleModel->insertArticle($article);
        }
    }
}
