<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        view()->composer(
            'layouts.admin.sidebar', 'App\Http\ViewComposers\MenuComposer'
        );
        view()->composer(
            'layouts.admin', 'App\Http\ViewComposers\TipComposer'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
