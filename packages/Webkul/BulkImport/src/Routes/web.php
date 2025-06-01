<?php

use Illuminate\Support\Facades\Route;
use Webkul\BulkImport\Http\Controllers\BulkImportController;

Route::group(['middleware' => ['admin'], 'prefix' => config('app.admin_url')], function () {
    Route::get('bulk/import', [BulkImportController::class, 'importfile'])
        ->name('admin.settings.data_transfer.imports');
    
    Route::post('bulk/import', [BulkImportController::class, 'customFileUpload'])
        ->name('admin.settings.data_transfer.imports.custom.file');
    
    Route::post('upload-images', [BulkImportController::class, 'uploadProductImages']);
});
