<?php

namespace Tests\Feature;

use App\Services\ExternalProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SyncProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_sync_products_from_external_api()
    {
        // Arrange: Mock the ExternalProductService to return fake data
        $mockProductData = [
            [
                'sku' => 'SKU123',
                'name' => 'Product de test 1',
                'price' => 100.00,
                'currency' => 'SAR',
                'status' => 'sale',
                'variations' => [],
            ],
            [
                'sku' => 'SKU456',
                'name' => 'Product de test 2',
                'price' => 150.00,
                'currency' => 'SAR',
                'status' => 'sale',
                'variations' => [],
            ]
        ];

        // Mock the ExternalProductService
        $this->mock(ExternalProductService::class, function ($mock) use ($mockProductData) {
            $mock->shouldReceive('fetchProducts')
                ->once()
                ->andReturn($mockProductData);
        });

        // Artisan: Run the sync command
        Artisan::call('sync:products');

        // Assert: Ensure products are inserted into the database
        $this->assertDatabaseCount('products', 2);

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU123',
            'name' => 'Product de test 1',
            'price' => 100.00,
            'currency' => 'SAR',
            'status' => 'sale',
        ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU456',
            'name' => 'Product de test 2',
            'price' => 150.00,
            'currency' => 'SAR',
            'status' => 'sale',
        ]);
    }
}
