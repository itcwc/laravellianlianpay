<?php

namespace Yoka\LianLianPay\Core;

use Yoka\LianLianPay\Exceptions\HttpException;
use Yoka\LianLianPay\Support\Arr;
use Yoka\LianLianPay\Support\Collection;
use Yoka\LianLianPay\Support\Log;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

abstract class AbstractAPI
{
    /**
     * Http instance.
     *
     * @var Http
     */
    protected $http;

    /**
     * @var Config
     */
    protected $config;

    const SIGN_TYPE_RSA = 'RSA';
    const GET = 'get';
    const POST = 'post';
    const JSON = 'json';
    const PUT = 'put';
    const DELETE = 'delete';

    protected $baseUrl;

    protected $production = false;

    protected $timestamp;
    /**
     * @var int
     */
    protected static $maxRetries = 0;

    /**
     * Constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->timestamp = date('YmdHis');
        $this->setConfig($config);
    }

    /**
     * 根据测试环境和生产环境选择 BaseUrl
     * @return string
     */
    protected function getBaseUrl(): string
    {
        
        if (empty($this->baseUrl)) {
            $this->production = $this->getConfig()->get('production');
            if ($this->production) {
                $this->baseUrl = 'https://accpapi.lianlianpay.com/v1/';
            } else {
                $this->baseUrl = 'https://accpapi-ste.lianlianpay-inc.com/v1/';
            }
        }

        return $this->baseUrl;
    }

    /**
     * Return the http instance.
     *
     * @return Http
     */
    public function getHttp()
    {
        if (is_null($this->http)) {
            $this->http = new Http();
        }

        if (0 === count($this->http->getMiddlewares())) {
            $this->registerHttpMiddlewares();
        }

        return $this->http;
    }

    /**
     * Set the http instance.
     *
     * @param Http $http
     *
     * @return $this
     */
    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Return the current config.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the config.
     *
     * @param Config $config
     *
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @param int $retries
     */
    public static function maxRetries($retries)
    {
        self::$maxRetries = abs($retries);
    }

    /**
     * 返回json相应，及封装请求参数为json字符串格式.
     *
     * @param $url
     * @param $params
     * @param string $method
     * @return Collection|null
     * @throws HttpException
     */
    protected function parse($url, $params, string $method = 'post')
    {
        // 获取http实例
        $http = $this->getHttp();
        
        $params = $this->filterNull($params);
        // 
        $sign = $this->buildSignatureDataParams($params);
// print_r($sign);die();
        $contents = $http->parseJSON(call_user_func_array([$http, $method], [$url, $params, $sign]));

        if (empty($contents)) {
            return null;
        }

        $this->checkAndThrow($contents);

        return (new Collection($contents));
    }

    /**
     * 与parse不同之处在于header头部 和 参数处理.
     *
     * @param $url
     * @param $params
     * @param string $method
     * @return Collection|null
     * @throws HttpException
     */
    protected function payload($url, $params, string $method = 'post')
    {
        $http = $this->getHttp();

        $params['sign'] = $this->buildSignatureParams($params);
        $params = $this->buildSignatureParams($params);

        $contents = $http->parseJSON(call_user_func_array([$http, $method], [$url, $params, '', false]));

        if (empty($contents)) {
            return null;
        }

        $this->checkAndThrow($contents);

        return (new Collection($contents));
    }

    /**
     * Register Guzzle middlewares.
     */
    protected function registerHttpMiddlewares()
    {
        // log
        $this->http->addMiddleware($this->logMiddleware());
        // signature
        $this->http->addMiddleware($this->signatureMiddleware());
    }

    protected function signatureMiddleware()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (!$this->config) {
                    return $handler($request, $options);
                }

                return $handler($request, $options);
            };
        };
    }

    /**
     * Log the request.
     *
     * @return \Closure
     */
    protected function logMiddleware()
    {
        return Middleware::tap(function (RequestInterface $request, $options) {
            Log::debug("Request: {$request->getMethod()} {$request->getUri()} " . json_encode($options));
            Log::debug('Request headers:' . json_encode($request->getHeaders()));
        });
    }

    /**
     * Check the array data errors, and Throw exception when the contents contains error.
     *
     * @param array $contents
     * @throws HttpException
     */
    protected function checkAndThrow(array $contents)
    {
        $successCodes = ['0000', '4002', '4003', '4004'];
        if (isset($contents['ret_code']) && !in_array($contents['ret_code'], $successCodes)) {
            if (empty($contents['ret_msg'])) {
                $contents['ret_msg'] = 'Unknown';
            }

            //throw new HttpException($contents['ret_msg'], $contents['ret_code']);
        }
    }

    /**
     * 地址.
     */
    protected function url(string $url): string
    {
        
        return static::getBaseUrl() .  $url;
    }


    /**
     * 验证签名
     * @param $params
     * @return bool
     */
    public function verifySignature($params)
    {
        if (!isset($params['sign'])) {
            return false;
        }

        $sign = $params['sign'];
        unset($params['sign']);
        $signRaw = $this->httpBuildKSortQuery($params);

        $pubKey = $this->getConfig()->getInstantPayLianLianPublicKey();
        $res = openssl_get_publickey($pubKey);

        // 调用openssl内置方法验签，返回bool值
        $result = (bool)openssl_verify($signRaw, base64_decode($sign), $res, OPENSSL_ALGO_MD5);

        Log::debug('Verify Signature Result:', compact('result', 'params'));

        // 释放资源
        openssl_free_key($res);
        return $result;
    }

    protected function filterNull($params): array
    {
        // 过滤空参数
        return Arr::where($params, function ($key, $value) {
            return !is_null($value);
        });
    }

    protected function httpBuildKSortQuery($params): string
    {
        // 排序
        ksort($params);
        reset($params);
        return urldecode(http_build_query($params));
    }

    /**
     * @param array $params
     * @return string
     */
    protected function buildSignatureParams(array $params): string
    {
        $params = $this->filterNull($params);
        $signRaw = $this->httpBuildKSortQuery($params);

        Log::debug('排序数据:', [$signRaw]);
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($this->getConfig()->getInstantPayPrivateKey());
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($signRaw, $signStr, $res, OPENSSL_ALGO_MD5);
        //释放资源
        openssl_free_key($res);
        //base64编码   sign
        return base64_encode($signStr);
    }

    /**
     * @param array $params
     * @return string
     */
    protected function buildSignatureDataParams(array $params): string
    {
        $params = $this->filterNull($params);
        Log::debug('json数据:', $params);
        $signRaw = md5(json_encode($params, JSON_UNESCAPED_UNICODE));
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($this->getConfig()->getInstantPayPrivateKey());
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($signRaw, $signStr, $res, OPENSSL_ALGO_MD5);
        //释放资源
        openssl_free_key($res);
        //base64编码   sign
        return base64_encode($signStr);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function buildPayLoadParams(array $params): array
    {
        Log::debug('Build PayLoad Before:', $params);
        $oidPartner = $this->getConfig()->get('oid_partner');
        $payLoad = LLHelper::encryptPayLoad(json_encode($params), $this->getConfig()->getInstantPayLianLianPublicKey());
        return [
            'oid_partner' => $oidPartner,
            'pay_load' => $payLoad
        ];
    }
}