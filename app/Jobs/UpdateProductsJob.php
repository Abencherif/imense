<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UpdateProductsJob implements ShouldQueue
{
    use Queueable;
    protected $productData;

    /**
     * Create a new job instance.
     */

    public function __construct($productData)
    {
        $this->productData = $productData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulate triggering events
        sleep(2);  // Simulate delay for each event

        //dd($this->productData);
        // Update or create the product in the database
        Product::updateOrCreate(
            ['sku' => $this->productData['sku']],
            [
                'name' => $this->productData['name'],
                'price' => $this->productData['price'],
                'currency' => $this->productData['currency'],
                'status' => $this->productData['status'],
                'sku' => $this->productData['sku'],
                'variations' => json_encode($this->productData['variations'] ?? []),
            ]
        );

        Log::info("Product with SKU {$this->productData['sku']} processed.");
    }
}
