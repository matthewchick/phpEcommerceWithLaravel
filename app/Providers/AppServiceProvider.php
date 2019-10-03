<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use View;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        // Section 6-40 https://laravel.com/docs/5.8/views
        /*
        Occasionally, you may need to share a piece of data with all views that are rendered by your application.
        You may do so using the view facade's share method. Typically,
        you should place calls to share within a service provider's boot method.
        You are free to add them to the AppServiceProvider or generate a separate service provider to house them
        */
        View::share('Name',"Matthew");  // share user data on the all view
        /*
        View composers are callbacks or class methods that are called when a view is rendered.
        If you have data that you want to be bound to a view each time that view is rendered,
        a view composer can help you organize that logic into a single location.
        */
        View::composer('*', function($view){
            $view->with('userData',Auth::user());
        });
    }
}
