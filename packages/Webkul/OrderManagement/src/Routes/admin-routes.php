<?php

use Illuminate\Support\Facades\Route;
use Webkul\OrderManagement\Http\Controllers\Admin\InvoiceController;
use Webkul\OrderManagement\Http\Controllers\Admin\OrderController;

Route::group(['middleware' => ['admin'], 'prefix' => config('app.admin_url')], function () {
    /**
     * Sales routes.
     */
    Route::prefix('sales')->group(function () {

        /**
         * Invoices routes.
         */
        Route::controller(InvoiceController::class)->prefix('invoices')->group(function () {
            Route::delete('cancel/{id}', 'cancel')->name('order_management.admin.sales.invoices.cancel');
        });

        /**
         * Invoices routes.
         */
        Route::controller(OrderController::class)->prefix('orders')->group(function () {
            Route::get('edit/{id}', 'edit')->name('order_management.admin.sales.orders.edit');

            Route::put('update/{id}', 'update')->name('order_management.admin.sales.orders.update');
        });
    });
});