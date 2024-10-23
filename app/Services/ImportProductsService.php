<?php

namespace App\Services;

use App\Classes\CsvReader;
use App\Interfaces\ImportProductsServiceInterface;
use App\Jobs\ProductVariationsJob;
use App\Repositories\ProductRepository;

class ImportProductsService implements ImportProductsServiceInterface
{
    protected $productRepo;

    // Inject Product Repository in the construct
    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function Csv(string $filePath): void
    {

        // Open and read the CSV file
        $productData = CsvReader::read($filePath);

        if (!empty($productData)) {
            $data = $this->cleanDuplicates($productData);

            // Retrieve items where status is 'deleted'
            $deletedProductIds = collect($data)
                ->where('status', 'deleted')
                ->pluck('id')
                ->toArray();

            // Insert/update Products
            $this->productRepo->createOrUpdate($data);

            // Soft Delete Products
            $this->productRepo->softDeleteMissingProducts($deletedProductIds);

            // Dispatch Product Variations Job
            ProductVariationsJob::dispatch()->delay(10);

        }else{
            throw new \Exception('File is empty');
        }
    }

    private function cleanDuplicates($data)
    {
        $mergedData = collect();
        foreach ($data as $key => &$innerArray) {
            if ($key != 'no_sku') {
                $innerArray = collect($innerArray)
                    ->unique('sku')
                    ->values()
                    ->toArray();
            } else {
                $innerArray = collect($innerArray)
                    ->unique('name')
                    ->values()
                    ->toArray();
                foreach ($innerArray as $key => &$value) {
                    unset($value['sku']);
                }
            }
            $mergedData = $mergedData->merge($innerArray);
        }
        return $mergedData->toArray();
    }
}
