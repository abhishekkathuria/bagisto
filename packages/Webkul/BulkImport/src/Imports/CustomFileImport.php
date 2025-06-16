<?php

namespace Webkul\BulkImport\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Concerns\ToCollection;
use Throwable;
use Webkul\Attribute\Models\Attribute;
use Webkul\BulkImport\Jobs\FileImport as JobsFileImport;
use Webkul\BulkImport\Jobs\UpdateProductShuffleJob;
use Webkul\Product\Models\Product;

class CustomFileImport implements ToCollection
{
    protected $locale;

    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    public function collection(Collection $collection)
    {
        $rows = $collection
            ->values() // Reset keys
            ->filter(function ($row) {
                return collect($row)->filter()->isNotEmpty();
            });
        $rows = $rows->slice(1)->values(); // Remove header and reset keys
        $totalRows = $rows->count();
        $chunks = 1;
        $chunkSize = ceil($totalRows / $chunks); // Should be 500
        $jobs = [];

        for ($i = 0; $i < $chunks; $i++) {
            $chunk = $rows->slice($i * $chunkSize, $chunkSize)->values()->all();
            $jobs[] = new JobsFileImport($chunk, $this->locale);
        }

        Bus::batch($jobs)
            ->then(function () {
                $this->updateProductShuffleAttribute();
            })
            ->catch(function (Throwable $e) {
                \Log::error('Bulk import failed: '.$e->getMessage());
            })
            ->dispatch();
    }

    public function updateProductShuffleAttribute()
    {
        // Dispatch the update logic as a job batch for each season
        $seasonAttribute = Attribute::where('code', 'season')->first();

        if ($seasonAttribute) {
            $seasons = $seasonAttribute->options()->pluck('id')->toArray();

            $jobs = [];

            foreach ($seasons as $season) {
                $products = \DB::table('products')
                    ->join('product_attribute_values', 'products.id', '=', 'product_attribute_values.product_id')
                    ->where('product_attribute_values.attribute_id', $seasonAttribute->id)
                    ->where('product_attribute_values.integer_value', $season)
                    ->inRandomOrder()
                    ->select('products.*')
                    ->get()
                    ->map(
                        function ($item) {
                            return Product::find($item->id);
                        }
                    );

                foreach ($products as $key => $product) {
                    $jobs[] = new UpdateProductShuffleJob($product->id, $key + 1);
                }
            }

            if (! empty($jobs)) {
                Bus::batch($jobs)->dispatch();
            }
        }
    }
}
