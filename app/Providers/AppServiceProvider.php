<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ResetPassword::createUrlUsing(function($notifiable, $token){
            return env('SPA_URL') . "/reset-password/{$token}?email={$notifiable->getEmailForPasswordReset()}";
        });

        Request::macro('shouldBeCache', function () {
            if(config('ugo.api.skip_cache') == false){
                return false;
            }else{
                return true;
            }
            
        });
    }
}
