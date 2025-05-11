<?php

namespace Webkul\BulkImport\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Webkul\BulkImport\Jobs\FileImport as JobsFileImport;

class CustomFileImport implements ToCollection
{
    public function collection(Collection $collection)
    {
        $rows = $collection->values(); // Optional: ->slice(1) if first row is header
        $totalRows = $rows->count();
        $chunks = 12;
        $chunkSize = ceil($totalRows / $chunks); // Should be 500

        for ($i = 0; $i < $chunks; $i++) {
            $chunk = $rows->slice($i * $chunkSize, $chunkSize)->values()->all();
            JobsFileImport::dispatch($chunk);
        }
    }
}
