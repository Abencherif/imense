<?php

namespace App\Interfaces;

interface ProductRepositoryInterface
{
    public function createOrUpdate(array $data);
    public function softDeleteMissingProducts(array $deletedIds): void;
}
