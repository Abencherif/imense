<?php

namespace App\Services;

use App\Contracts\ImportProductsServiceInterface;
use App\Repositories\CSVProductUpdater;
use App\Repositories\ProductRepository;

class ImportProductsService implements ImportProductsServiceInterface
{

    public function Csv(string $filePath): void
    {
        (new CSVProductUpdater($filePath))->updateProducts();
    }
}
