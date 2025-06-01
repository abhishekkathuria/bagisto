<?php

namespace Webkul\OrderManagement\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\OrderManagement\Models\Admin\Order;
use Webkul\OrderManagement\Models\Admin\Invoice;
use Webkul\Sales\Contracts\Order as OrderContract;
use Webkul\Sales\Contracts\Invoice as InvoiceContract;
use Webkul\Core\Http\Middleware\PreventRequestsDuringMaintenance;
use Webkul\OrderManagement\DataGrids\Admin\Sales\OrderInvoiceDataGrid;
use Webkul\OrderManagement\Http\Controllers\Admin\Sales\OrderController;
use Webkul\Admin\DataGrids\Sales\OrderInvoiceDataGrid as AdminOrderInvoiceDataGrid;
use Webkul\Admin\Http\Controllers\Sales\OrderController as BaseOrderController;

class OrderManagementServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        Route::middleware(['web', PreventRequestsDuringMaintenance::class])->group(__DIR__.'/../Routes/admin-routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'order_management');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'order_management');

        $this->app->bind(AdminOrderInvoiceDataGrid::class, OrderInvoiceDataGrid::class);
        $this->app->bind(BaseOrderController::class, OrderController::class);

        $this->app->concord->registerModel(InvoiceContract::class, Invoice::class);
        $this->app->concord->registerModel(OrderContract::class, Order::class);

        $this->publishes([
            __DIR__.'/../Resources/views/admin/sales/orders' => resource_path('views/vendor/admin/sales/orders'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
    }
}