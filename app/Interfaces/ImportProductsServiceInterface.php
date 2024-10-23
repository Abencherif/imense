<?php

namespace App\Interfaces;

interface ImportProductsServiceInterface
{
    public  function Csv(string $filePath): void;
}
