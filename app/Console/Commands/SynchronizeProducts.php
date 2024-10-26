<?php

namespace App\Console\Commands;

use App\Jobs\UpdateProductsJob;
use App\Services\ExternalProductService;
use Illuminate\Console\Command;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

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
        $batches = array_chunk($products, 200); // Adjust batch size as needed

        // Dispatch each product batch as a job batch
        Bus::batch(
            array_map(fn($batch) => new UpdateProductsJob($batch), $batches)
        )->dispatch();
        $this->info('Product synchronization has been successfully dispatched.');

    }
}
