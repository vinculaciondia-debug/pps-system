<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind(); // opcional
        Paginator::defaultView('components.pagination'); // aplica a todos los ->links()
        Paginator::defaultSimpleView('components.pagination'); // si usas ->simplePaginate()
    }
}
