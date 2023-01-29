<?php


namespace Yoka\LianLianPay\Common;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Yoka\LianLianPay\Core\AbstractAPI;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['common'] = function ($pimple) {
            return new Client($pimple['config']);
        };
    }
}