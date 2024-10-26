<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Repositories\CSVProductUpdater;
use App\Services\ImportProductsService;
use Illuminate\Console\Command;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {filePath?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =  'Imports products into database';
    protected $importService;

    public function __construct(ImportProductsService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            ini_set('memory_limit', '-1');
            $startTime = microtime(true);
            $filePath = $this->argument('filePath') ? storage_path($this->argument('filePath')) : storage_path('csv/products.csv');
            // Check if file exist
            $this->info('Checking if "products.csv" File Exist');
            if (!file_exists($filePath)) {
                $this->error('File not found!');
                return;
            }
            $this->info('File Exist !, Start Importing');
            // Call import service to handle import
            $this->importService->Csv($filePath);


            $endTime = microtime(true);
            $this->info("Total execution time: " . round($endTime - $startTime, 4) . " seconds\n");
            $this->info('Product import completed successfully.');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }



}
