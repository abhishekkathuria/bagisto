<?php

use Illuminate\Support\Facades\Route;
use Webkul\BulkImport\Http\Controllers\BulkImportController;

Route::get('/admin/bulk/import', [BulkImportController::class, 'importfile'])
    ->name('admin.settings.data_transfer.imports');

Route::post('bulk/import', [BulkImportController::class, 'customFileUpload'])
    ->name('admin.settings.data_transfer.imports.custom.file');
