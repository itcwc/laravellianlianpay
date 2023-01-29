<?php
/*
 * @Author: Y95201 
 * @Date: 2022-03-18 14:10:43 
 * @Last Modified by: Y95201
 * @Last Modified time: 2022-03-18 14:20:38
 */
namespace Itcwc\LianLianPay\Replace;

use Itcwc\LianLianPay\Core\AbstractAPI;
use Itcwc\LianLianPay\Exceptions\HttpException;
use Itcwc\LianLianPay\Support\Collection;
/**
 * 代发
 * Class Client
 * @package Yoka\LianLianPay\Replace
 */
class Client extends AbstractAPI
{
    /**内部代发申请
     * 
     */
    public function transfer(array $params)
    {
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);
        print_r($params);
        return $this->parse($this->url("txn/transfer"), $params);
    }
    /**批量内部代发申请
     * 
     */
    public function batch_transfer(array $params)
    {
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);

        return $this->parse($this->url("txn/transfer-morepyee"), $params);
    }
}