<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Facades\Http;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $apiKey = env('DAISYSMS_API_KEY');

        $response = Http::get('https://daisysms.com/stubs/handler_api.php', [
            'api_key' => $apiKey,
            'action' => 'getPrices'
        ]);

        $data = $response->json();

        if (!isset($data[187])) {
            dd('Error: USA services not found.');
        }

        $usaServices = $data[187];

        foreach ($usaServices as $code => $details) {
            Service::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $details['Name'] ?? strtoupper($code),
                    'cost' => $details['cost'] ?? null
                ]
            );
        }
    }
}
