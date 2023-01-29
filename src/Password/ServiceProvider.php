<?php


namespace Yoka\LianLianPay\Password;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['password'] = function ($pimple) {
            return new Client($pimple['config']);
        };
    }
}