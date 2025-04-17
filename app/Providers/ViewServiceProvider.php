<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        // Share $cate with the header layout
        View::composer(['frontend.layouts.header', 'frontend.category', 'frontend.index'], function ($view) {
            $view->with('cate', Category::all());
        });
    }
}
