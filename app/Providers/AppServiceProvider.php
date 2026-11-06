<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Category navigation used by the storefront layout. Only
        // subcategories that actually have visible products are listed.
        View::composer('layouts.shop', function ($view) {
            $view->with('navCategories', Category::active()
                ->with([
                    'subcategories' => fn ($q) => $q->active()
                        ->whereHas('products', fn ($p) => $p->active()),
                ])
                ->orderBy('name')
                ->get());
        });
    }
}
