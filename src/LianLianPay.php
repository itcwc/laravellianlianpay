<?php

namespace Itcwc\LianLianPay;

use Itcwc\LianLianPay\Core\Http;
use Itcwc\LianLianPay\Support\Log;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\Cache as CacheInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application
 *
 * @property \Yoka\LianLianPay\Payment\Client payment
 * @property \Yoka\LianLianPay\Account\Client account
 * @property \Yoka\LianLianPay\Common\Client common
 *
 * @package Yoka\LianLianPay
 */
class LianLianPay extends Container
{
    protected $providers = [
        AccManage\ServiceProvider::class,
        Account\ServiceProvider::class,
        Common\ServiceProvider::class,
        Password\ServiceProvider::class,
        Payment\ServiceProvider::class,
        Refund\ServiceProvider::class,
        Replace\ServiceProvider::class,
        Secured\ServiceProvider::class,
        Withdrawal\ServiceProvider::class,
    ];

    public function __construct(array $config = array())
    {
        parent::__construct($config);

        $this['config'] = function () use ($config) {
            return new Core\Config($config);
        };

        $this->registerBase();
        $this->registerProviders();
        $this->initializeLogger();

        Http::setDefaultOptions($this['config']->get('guzzle', ['timeout' => 5.0]));

        $this->logConfiguration($config);
    }

    public function logConfiguration($config)
    {
        $config = new Core\Config($config);

        $keys = ['oid_partner', 'private_key', 'public_key', 'll_public_key'];
        foreach ($keys as $key) {
            !$config->has($key) || $config[$key] = '***' . substr($config[$key], -5);
        }

        Log::debug('Current config:', $config->toArray());
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    private function registerBase()
    {
        $this['request'] = function () {
            return Request::createFromGlobals();
        };

        if (!empty($this['config']['cache']) && $this['config']['cache'] instanceof CacheInterface) {
            $this['cache'] = $this['config']['cache'];
        } else {
            $this['cache'] = function () {
                return new FilesystemCache(sys_get_temp_dir());
            };
        }
    }

    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('lianlianpay');

        if (!$this['config']['debug'] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($this['config']['log.handler'] instanceof HandlerInterface) {
            $logger->pushHandler($this['config']['log.handler']);
        } elseif ($logFile = $this['config']['log.file']) {
            try {
                $logger->pushHandler(new StreamHandler(
                        $logFile,
                        $this['config']->get('log.level', Logger::WARNING),
                        true,
                        $this['config']->get('log.permission', null))
                );
            } catch (\Exception $e) {
            }
        }

        Log::setLogger($logger);
    }
//
//    /**
//     * @param $method
//     * @param $args
//     * @return mixed
//     * @throws \Exception
//     */
//    public function __call($method, $args)
//    {
//        if (is_callable([$this['fundamental.api'], $method])) {
//            return call_user_func_array([$this['fundamental.api'], $method], $args);
//        }
//
//        throw new \Exception("Call to undefined method {$method}()");
//    }
}