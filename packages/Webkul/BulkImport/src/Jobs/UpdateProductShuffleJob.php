<?php

namespace Webkul\BulkImport\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Batchable;

class UpdateProductShuffleJob implements ShouldQueue
{
    use Queueable, Batchable;

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
     *
     * @return void
     */
    public function handle(): void
    {
        \Log::info('UpdateProductShuffleJob started with value: ' . $this->id);
        $productRepository = app('Webkul\Product\Repositories\ProductRepository');
        $productRepository->update([
            'shuffle' => $this->value,
        ], $this->id);
        \Log::info('UpdateProductShuffleJob completed for products: ' . $this->id);
    }
}
