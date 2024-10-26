<?php

namespace App\Contracts;

interface ImportProductsServiceInterface
{
    public  function Csv(string $filePath): void;
}
