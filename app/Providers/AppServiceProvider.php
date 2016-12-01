<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BlizzardApi\BlizzardClient;
use BlizzardApi\Service\WorldOfWarcraft;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBlizzardClient();
        $this->registerWOWService();
    }

    public function registerBlizzardClient()
    {
        $this->app->singleton('blizzard', function(){
            return new BlizzardClient(env('BLIZZARD_API_KEY'), env('BLIZZARD_API_REGION'), env('BLIZZARD_API_LOCALE'));
        });
    }

    public function registerWOWService()
    {
        $this->app->singleton('wow', function($app){
           return new WorldOfWarcraft($app->make('blizzard'));
        });
    }
}
