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
        // Arrange: Mock the ExternalProductService to return the JSON structure
        $mockProductData = [
            [
                'id' => '25',
                'created_at' => '2020-12-15T21:28:26.899Z',
                'name' => 'Handcrafted Concrete Cheese',
                'image' => 'http://lorempixel.com/640/480/technics',
                'price' => 34,
                'variations' => [
                    [
                        'id' => '25',
                        'productId' => '25',
                        'color' => 'pink',
                        'material' => 'Metal',
                        'quantity' => 38,
                        'additional_price' => 41
                    ]
                ],
            ]
        ];

        // Mock the ExternalProductService
        $this->mock(ExternalProductService::class, function ($mock) use ($mockProductData) {
            $mock->shouldReceive('fetchProducts')
                ->once()
                ->andReturn($mockProductData);
        });

        // Act: Run the sync command
        Artisan::call('sync:products');

        $this->assertDatabaseCount('products', 1);

        $this->assertDatabaseHas('products', [
            'external_id' => '25',
            'name' => 'Handcrafted Concrete Cheese',
            'price' => 34,
        ]);

        // Assert the variation is also inserted correctly
        $this->assertDatabaseCount('product_variations', 1);

        $this->assertDatabaseHas('product_variations', [
            'external_id' => '25',
            'color' => 'pink',
            'material' => 'Metal',
            'quantity' => 38,
            'additional_price' => 41,
        ]);
    }

}
