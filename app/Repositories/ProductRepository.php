<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Jobs\SoftDeleteProductJob;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    public function createOrUpdate(array $data)
    {
        foreach ($data as $key => $value) {
            DB::table('products')->updateOrInsert(['id' => $value['id']],$value);
        }
        return true;
    }

    public function softDeleteMissingProducts(array $deletedIds): void
    {
        SoftDeleteProductJob::dispatch($deletedIds)->delay(10);
    }
}
