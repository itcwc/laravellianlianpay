<?php

namespace Itcwc\LianLianPay\Secured;

use Itcwc\LianLianPay\Core\AbstractAPI;
use Itcwc\LianLianPay\Exceptions\HttpException;
use Itcwc\LianLianPay\Support\Collection;

/**
 * 担保交易
 * Class Client
 * @package Yoka\LianLianPay\Secured
 */
class Client extends AbstractAPI
{
    /**
     * 担保交易确认
     * 在支付交易统一创单时指定担保收款方信息，担保确认时支持全额及部分金额多次确认；对于创单时指定的担保收款方不支持确认时修改；
     * 在支付交易统一创单时不指定担保收款方信息，担保交易确认时动态指定收款方并进行交易确认，资金从担保平台商户账户转入担保收款方账户。
     * https://open.lianlianpay.com/docs/accp/accpstandard/secured-confirm.html
     * @param array $params
     * @return Collection|null
     * @throws HttpException
     */
    public function confirm(array $params)
    {
        return $this->parse($this->url("txn/secured-confirm"), $params);
    }

    /**
     * 担保交易信息查询
     * 针对担保交易的担保支付和担保交易确认多次操作，为商户提供某笔担保单的真实收款方金额确认情况以及多次确认、退款操作的查询接口。
     * https://open.lianlianpay.com/docs/accp/accpstandard/secured-query.html
     * @param string|null $accpTxno 原担保支付ACCP系统交易单号。txn_seqno或accp_txno二选一，建议优先使用ACCP系统交易单号。
     * @param string|null $txnSeqno 原担保支付商户系统唯一交易流水号。txn_seqno或accp_txno二选一，建议优先使用ACCP系统交易单号。
     * @return Collection|null
     * @throws HttpException
     */
    public function query(string $accpTxno = null, string $txnSeqno = null)
    {
        $params = [
            'accp_txno' => $accpTxno,
            'txn_seqno' => $txnSeqno,
        ];

        return $this->parse($this->url("txn/secured-query"), $params);
    }
}