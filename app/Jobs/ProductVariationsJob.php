<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductVariationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Product::whereNotNull('variations')
            ->chunk(100, function ($products) {
                foreach ($products as $product) {
                    // Parse the variations
                    $variations = json_decode($product->variations, true);
                    if (is_array($variations)) {
                        $variationData = [];

                        foreach ($variations as $variation) {
                            $variationValue = explode(',', $variation['value']);
                            if(count($variationValue) > 1) {
                                foreach ($variationValue as $variationValueItem) {
                                    $ExitingVariation = ProductVariation::where('product_id', $product->id)
                                        ->where('attribute_name', $variation['name'])
                                        ->where('attribute_value', $variationValueItem)
                                        ->first();
                                    $variationData[] = [
                                        'id' => $ExitingVariation->id ?? null,
                                        'product_id' => $product->id,
                                        'attribute_name' => $variation['name'] ?? null,
                                        'attribute_value' => $variationValueItem ?? null,
                                        'price' => $variation['price'] ?? $product->price,  // Default to product's price if not set
                                        'quantity' => $variation['quantity'] ?? 0,  // Default stock to 0 if not set
                                    ];
                                }

                            }else{
                                $ExitingVariation = ProductVariation::where('product_id', $product->id)
                                    ->where('attribute_name', $variation['name'])
                                    ->where('attribute_value', $variation['value'])
                                    ->first();
                                $variationData[] = [
                                    'id' => $ExitingVariation->id ?? null,
                                    'product_id' => $product->id,
                                    'attribute_name' => $variation['name'] ?? null,
                                    'attribute_value' => $variation['value'] ?? null,
                                    'price' => $variation['price'] ?? $product->price,  // Default to product's price if not set
                                    'quantity' => $variation['quantity'] ?? 0,  // Default stock to 0 if not set
                                ];
                            }
                        }

                        // Bulk upsert variations for this product
                        DB::table('product_variations')->upsert(
                            $variationData,
                            ['id'],
                            ['product_id','price', 'attribute_name', 'attribute_value', 'quantity']
                        );
                    }
                }
            });
    }
}
