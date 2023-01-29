<?php

namespace Itcwc\LianLianPay\Src;

use Illuminate\Support\ServiceProvider;

class LLPayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
                __DIR__.'/../config/lianlianpay.php' => config_path('lianlianpay.php'),
            ], 
            'config'
        );
    }
}