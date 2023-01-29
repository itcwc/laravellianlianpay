<?php

namespace Yoka\LianLianPay\Payment;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['payment'] = function ($pimple) {
            return new Client($pimple['config']);
        };
    }
}