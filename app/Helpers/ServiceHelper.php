<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('get_services')) {
    function get_services()
    {
        $apiKey = env('DAISYSMS_API_KEY');

        $response = Http::get('https://daisysms.com/stubs/handler_api.php', [
            'api_key' => $apiKey,
            'action' => 'getPrices'
        ]);

        if (!$response->ok()) {
            return [];
        }

        $data = $response->json();

        return $data[187] ?? []; // Only USA services (country ID 187)
    }
}
