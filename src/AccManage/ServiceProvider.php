<?php


namespace Yoka\LianLianPay\AccManage;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['accManage'] = function ($pimple) {
            return new Client($pimple['config']);
        };
    }
}