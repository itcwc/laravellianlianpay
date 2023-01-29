<?php

namespace Itcwc\LianLianPay\Src;

use Illuminate\Support\ServiceProvider;

class BlogPackageServiceProvider extends ServiceProvider
{

    public function register()
    {
        // $this->mergeConfigFrom(__DIR__.'/../config/lianlianpay.php', 'lianlianpay'); 
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'blogpackage'); 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
              __DIR__.'/../config/config.php' => config_path('blogpackage.php'),
            ], 'config');
        }
    }
}