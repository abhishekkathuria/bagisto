<?php

namespace Webkul\BulkImport\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Concerns\ToCollection;
use Webkul\BulkImport\Jobs\FileImport as JobsFileImport;


class CustomFileImport implements ToCollection
{
    protected $locale;

    /**
     * CustomFileImport constructor.
     *
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $rows = $collection
            ->values() // Reset keys
            ->filter(function ($row) {
                return collect($row)->filter()->isNotEmpty();
            });

        $totalRows = $rows->count();
        $chunks = 1;
        $chunkSize = ceil($totalRows / $chunks); // Should be 500
        $jobs = [];

        for ($i = 0; $i < $chunks; $i++) {
            $chunk = $rows->slice($i * $chunkSize, $chunkSize)->values()->all();
            $jobs[] = new JobsFileImport($chunk, $this->locale);
            // dump($chunk);
            // JobsFileImport::dispatch($chunk, $this->locale);
        }

        Bus::batch($jobs)->dispatch();
    }
}
