<?php

namespace App\Repositories;

use App\Contracts\ProductUpdaterInterface;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use Illuminate\Support\Facades\Log;
class CSVProductUpdater implements ProductUpdaterInterface
{
    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }
    public function updateProducts(): void
    {
        // Get all current product external IDs in the database
        $existingProductIds = Product::pluck('external_id')->toArray();
        // Track products that are still present in the CSV
        $processedProductIds = [];

        $csv = Reader::createFromPath($this->filePath, 'r');

        $csv->setHeaderOffset(0);
        //dump($csv->getHeader());
        foreach ($csv as $record) {
            // Validate each row
                $validator = Validator::make($record, [
                    'id' => 'required|integer',
                    'name' => 'required|string|max:255',
                    'sku' => 'nullable|string|max:100',
                    'price' => 'nullable|numeric|min:0',
                    'currency' => 'required|string|in:SAR,USD',
                    'quantity' => 'nullable|integer|min:0',
                    'status' => 'required|string|in:sale,hidden,out,deleted',
                    'variations' => 'nullable|json'
                ]);

                if ($validator->fails()) {
                    Log::error('Invalid CSV data', [
                        'row' => $record,
                        'errors' => $validator->errors()
                    ]);
                    // Skip invalid rows
                    continue;
                }

                // Track the product data if valid
                $productData = [
                    'external_id' => $record['id'],
                    'name' => $record['name'],
                    'sku' =>  empty($record['sku'])  ? null : $record['sku'],
                    'price' => $record['price'] ?? null,
                    'currency' => $record['currency'],
                    'quantity' => $record['quantity'] ?? 0,
                    'status' => $record['status'],
                    // soft delete product that has status deleted
                    'deleted_at' => $record['status'] === 'deleted' ? now() : null,
                    'reason' => $record['status'] === 'deleted' ?  'Deleted due to synchronization' : null,
                    'variations' => $record['variations']
                ];
                $insertData[] = $productData;
                if(count($insertData) > 1000){
                    try {
                        // update or insert the product
                        Product::updateOrCreate(
                            ['external_id' => $productData['external_id']],
                            $productData
                        );
                        $processedProductIds[] = $productData['external_id'];
                    } catch (QueryException $e) {
                        // Check if the exception is a unique constraint violation
                        if ($e->getCode() == 23000) {
                            // Unique constraint violation occurred, skip the row
                            Log::warning("Product with SKU {$productData['sku']} and status {$productData['status']} could not be inserted due to unique constraint.");
                        } else {
                            // Re-throw the exception if it's not a unique constraint issue
                            throw $e;
                        }
                    }
                }
        }
        // Soft delete products missing from CSV
        $missingProductIds = array_diff($existingProductIds, $processedProductIds);
        foreach(array_chunk($missingProductIds, 1000) as $chunk) {
            Product::whereIn('external_id', $chunk)->delete();
        }
    }
}
