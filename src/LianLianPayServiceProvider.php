<?php

namespace Itcwc\LianLianPay;

use Illuminate\Support\ServiceProvider;

class LLPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/lianlianpay.php' => config_path('config/lianlianpay.php'),
        ]);
    }

    public function register()
    {
          
    }

}