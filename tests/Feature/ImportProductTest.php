<?php

namespace Tests\Feature;

use App\Jobs\ProductVariationsJob;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ImportProductTest extends TestCase
{
   use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_command_import_products_from_csv(): void
    {
        $this->artisan('import:products')->assertExitCode(0);
        $this->assertDatabaseHas('products', ['sku' => 'I3744AZ17WNBK27']);
    }

    public function test_variation_products_job(): void
    {
        $this->artisan('import:products')->assertExitCode(0);
        $count = ProductVariation::count();
        $this->assertGreaterThan(0, $count);
    }
}
