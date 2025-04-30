<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\Product;

class SyncProducts extends Command
{
    protected $signature = 'sync:products';
    protected $description = 'Fetch and save products from API to database';

    protected $apiKey = 'aada198608ece1ebb755cd878415fd18';
    protected $baseUrl = 'https://shopviaclone22.com/api';

    public function handle()
    {
        $this->info('Starting Product Sync...');

        $productsResponse = Http::get($this->baseUrl . '/products.php', [
            'api_key' => $this->apiKey,
        ]);

        $productsData = $productsResponse->json();

        if (isset($productsData['categories']) && is_array($productsData['categories'])) {
            foreach ($productsData['categories'] as $categoryData) {
                if (!isset($categoryData['name'])) {
                    continue;
                }

                $category = Category::firstOrCreate(
                    ['name' => $categoryData['name']],
                    ['created_at' => now(), 'updated_at' => now()]
                );

                if (isset($categoryData['products']) && is_array($categoryData['products'])) {
                    foreach ($categoryData['products'] as $productData) {
                        if (!isset($productData['id'], $productData['name'])) {
                            continue;
                        }

                        Product::updateOrCreate(
                            ['api_product_id' => $productData['id']],
                            [
                                'name' => $productData['name'],
                                'description' => $productData['description'] ?? null,
                                'category_id' => $category->id,
                                'price' => isset($productData['price']) ? (float) $productData['price'] : 0,
                                'stock' => isset($productData['amount']) ? (int) $productData['amount'] : 0, // 🔥 FIXED!
                                'updated_at' => now(),
                            ]
                        );
                    }
                }
            }

            $this->info('Products synced successfully!');
        } else {
            $this->error('Failed to fetch products.');
        }
    }
}
