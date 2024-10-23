<?php

namespace App\Console\Commands;

use App\Jobs\UpdateProductsJob;
use App\Services\ExternalProductService;
use Illuminate\Console\Command;

class SynchronizeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $externalProductService;
    public function __construct(ExternalProductService $externalProductService)
    {
        parent::__construct();
        $this->externalProductService = $externalProductService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = $this->externalProductService->fetchProducts();

        if (empty($products)) {
            $this->error('No products retrieved from the external API.');
            return;
        }

        // Process products in chunks (for better performance)
        foreach (array_chunk($products, 100) as $batch) {
            foreach ($batch as $productData) {
                // Dispatch a job to process each product update
                UpdateProductsJob::dispatch($productData);
            }
        }

        $this->info('Product synchronization has been successfully dispatched.');
    }
}
