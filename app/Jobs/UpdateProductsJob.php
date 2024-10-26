<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateProductsJob implements ShouldQueue
{
    use Queueable,Batchable;
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

        // Update or create the product in the database
        foreach ($this->productData as $item){
            $product = Product::updateOrCreate(
                ['external_id' => $item['id']],
                [
                    'name' =>  $item['name'],
                    'price' =>  $item['price'],
                    'image' => $item['image'],
                    'quantity' =>  $item['variations'][0]['quantity'] ?? 0,
                    'external_id' => $item['id'],
                ]
            );
            foreach ($item['variations'] as $variation) {

                $product->variations()->updateOrCreate([
                    'external_id' => $variation['id'],
                ],[
                    'color'=> $variation['color'],
                    'material' => $variation['material'],
                    'quantity' => $variation['quantity'],
                    'additional_price' => $variation['additional_price'],
                    'external_id' => $variation['id'],
                ]);
            }
        }

    }
}
