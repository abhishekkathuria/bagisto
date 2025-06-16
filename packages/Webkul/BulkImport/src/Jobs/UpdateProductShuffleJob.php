<?php

namespace Webkul\BulkImport\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateProductShuffleJob implements ShouldQueue
{
    use Batchable, Queueable;

    protected $id;

    protected $value;

    /**
     * Create a new job instance.
     */
    public function __construct($id, string $value)
    {
        $this->value = $value;
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('UpdateProductShuffleJob started with value: '.$this->id);
        $productRepository = app('Webkul\Product\Repositories\ProductRepository');
        $productRepository->update([
            'shuffle' => $this->value,
        ], $this->id);
        \Log::info('UpdateProductShuffleJob completed for products: '.$this->id);
    }
}
