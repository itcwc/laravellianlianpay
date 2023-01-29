<?php
/*
 * @Author: Y95201 
 * @Date: 2022-03-18 14:10:48 
 * @Last Modified by: Y95201
 * @Last Modified time: 2022-03-18 14:15:18
 */
namespace Yoka\LianLianPay\Replace;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['Replace'] = function ($pimple) {
            return new Client($pimple['config']);
        };
    }
}