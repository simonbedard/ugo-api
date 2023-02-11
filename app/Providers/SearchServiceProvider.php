<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Search\Search;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('search',function(){       
            return new Search();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
