<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Category;

class SyncCategories extends Command
{
    protected $signature = 'sync:categories';
    protected $description = 'Fetch and save categories from API to database';

    protected $apiKey = 'aada198608ece1ebb755cd878415fd18'; // your API key
    protected $baseUrl = 'https://shopviaclone22.com/api'; // your API base URL

    public function handle()
    {
        $this->info('Starting Category Sync...');

        $productsResponse = Http::get($this->baseUrl . '/products.php', [
            'api_key' => $this->apiKey,
        ]);

        $productsData = $productsResponse->json();

        if (isset($productsData['categories']) && is_array($productsData['categories'])) {
            foreach ($productsData['categories'] as $category) {
                if (isset($category['name'])) {
                    Category::firstOrCreate(
                        ['name' => $category['name']],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
            $this->info('Categories synced successfully!');
        } else {
            $this->error('Failed to fetch categories.');
        }
    }
}
