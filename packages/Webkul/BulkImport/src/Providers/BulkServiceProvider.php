<?php

namespace Webkul\BulkImport\Providers;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BulkServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'bulk_import');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'bulk_import');
        
        Route::middleware(['web', PreventRequestsDuringMaintenance::class])->group(__DIR__.'/../Routes/web.php');
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php',
            'menu.admin'
        );
    }
}
