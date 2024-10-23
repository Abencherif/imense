<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalProductService
{

    protected $apiUrl;

    public function __construct()
    {
        // this link is not working 'https://670fb936a85f4164ef2ba7ad.mockapi.io/api/v5'

        $this->apiUrl = config('services.mock_api_url');
    }

    public function fetchProducts()
    {
        try {
            return Http::get($this->apiUrl)->json();
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error fetching products from external API: ' . $e->getMessage());
            return [];
        }
    }
}
