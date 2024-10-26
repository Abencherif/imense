<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportProductTest extends TestCase
{
   use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_command_import_products_from_csv(): void
    {
        $filePath = 'csv/test_products.csv';
        $this->artisan('import:products',['filePath' => $filePath])
            ->assertExitCode(0);
        $this->assertDatabaseHas('products', ['sku' => 'I1431AZ17WNAR53']);
    }
}
