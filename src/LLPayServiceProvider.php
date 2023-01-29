<?php

namespace Itcwc\LianLianPay;

use Illuminate\Support\ServiceProvider;

class LLPayServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/lianlianpay.php', 'lianlianpay'); 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/lianlianpay.php' => config_path('lianlianpay.php'),
            'config'
        ]);
    }
}