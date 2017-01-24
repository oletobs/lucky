<?php

namespace App\Providers;

use App\Services\BlizzardApiScraper;
use App\Services\WarcraftLogsApiClient;
use App\Services\WarcraftLogsApiScraper;
use Illuminate\Support\ServiceProvider;
use App\Services\BlizzardApiClient;
use GuzzleHttp\Client;

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
        $this->registerBlizzardApiClient();
        $this->registerWarcraftLogsApiClient();
        $this->registerBlizzardApiScraper();
        $this->registerWarcraftLogsApiScraper();
    }

    public function registerBlizzardApiClient()
    {
        $this->app->bind('blizzapi', function(){
            return new BlizzardApiClient(env('BLIZZARD_API_KEY'), new Client());
        });
    }

    public function registerWarcraftLogsApiClient()
    {
        $this->app->bind('wowlogsapi', function(){
            return new WarcraftLogsApiClient(env('WARCRAFT_LOGS_API_KEY'), new Client());
        });
    }

    public function registerBlizzardApiScraper()
    {
        $this->app->bind('blizzscraper', function(){
            return new BlizzardApiScraper();
        });
    }

    public function registerWarcraftLogsApiScraper()
    {
        $this->app->bind('wowlogscraper', function(){
            return new WarcraftLogsApiScraper();
        });
    }
}
