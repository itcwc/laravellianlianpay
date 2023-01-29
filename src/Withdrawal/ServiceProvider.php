<?php


namespace Yoka\LianLianPay\Withdrawal;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['withdrawal'] = function ($pimple) {
            return new Client($pimple['config']);
        };
    }
}