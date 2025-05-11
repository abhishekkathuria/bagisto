<?php

namespace Webkul\BulkImport\Http\Controllers;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\BulkImport\Imports\CustomFileImport;

class BulkImportController extends Controller
{   
    /**
     * Display the bulk import form.
     */
    public function importfile()
    {
        return view('bulk_import::bulk-import.import');
    }

    /**
     * Bulk Import
     */
    public function customFileUpload()
    {
        $file = request()->file('file');

        $import = new CustomFileImport();

        Excel::import($import, $file);

        return redirect()->back()->with('success', 'File imported successfully');
    }
}
