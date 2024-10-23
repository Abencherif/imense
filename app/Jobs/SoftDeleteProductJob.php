<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class SoftDeleteProductJob implements ShouldQueue
{
    use Queueable;

    private array $deletedProducts;
    /**
     * Create a new job instance.
     */
    public function __construct(array $deletedProducts)
    {
        $this->deletedProducts = $deletedProducts;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::table('products')
            ->whereIn('id', $this->deletedProducts)
            ->update([
                'reason' => 'Deleted due to synchronization',
                'deleted_at' => now()  //rather than using delete() we can update deleted_at
            ]);
        //restore product that are deleted using old imports
        DB::table('products')
            ->where('status','!=','deleted')
            ->where('reason','Deleted due to synchronization')
            ->update([
                'reason' => null,
                'deleted_at' => null
            ]);
    }
}
