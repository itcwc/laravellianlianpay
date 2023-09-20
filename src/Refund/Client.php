<?php

namespace Itcwc\LianLianPay\Refund;

use Itcwc\LianLianPay\Core\AbstractAPI;
use Itcwc\LianLianPay\Exceptions\HttpException;
use Itcwc\LianLianPay\Support\Collection;


/**
 * 退款
 * Class Client
 * @package Yoka\LianLianPay\Refund
 */
class Client extends AbstractAPI
{
    /**
     * 退款申请
     * 该接口只支持普通消费交易、担保消费交易退款。本接口可指定多个原收款方进行统一退款。 退款规则：
     * 1.每次发起退款可指定原消费交易一或多个收款方进行处理；
     * 2.支持全额或者部分退款；
     * 3.组合类消费交易，每次退款需要明确指定原付款方式对应的退款金额；
     * 4.异步退款申请在渠道真实退款结果获取之前状态为处理中，且该笔资金将被冻结。
     * https://open.lianlianpay.com/docs/accp/accpstandard/more-payee-refund.html
     * @param string $userID 必传 原交易付款方user_id，用户在商户系统中的唯一编号。
     * @param string|null $refundReason 可选 退款原因。
     * @param string|null $notifyUrl 可选 交易结果异步通知接收地址，建议HTTPS协议。
     * @return Collection|null
     * @throws HttpException
     */
    public function apply(string $userID, string $refundReason = null, string $notifyUrl = null)
    {
        $params = [
            'user_id' => $userID,
            'refund_reason' => $refundReason,
            'notify_url' => $notifyUrl,
        ];

        return $this->parse($this->url("txn/more-payee-refund"), $params);
    }

    /**
     * 退款结果查询
     * 该接口提供发起提现申请后的订单查询，商户可以通过该接口主动查询提现申请订单状态，完成下一步的业务逻辑。
     * https://open.lianlianpay.com/docs/accp/accpstandard/query-refund.html
     * @param string|null $accpTxno 可选 ACCP系统退款单号。二选一，建议优先使用ACCP系统交易单号。
     * @param string|null $txnSeqno 可选 退款订单号。二选一，建议使用ACCP系统退款单号。
     * @return Collection|null
     * @throws HttpException
     */
    public function query(string $accpTxno = null, string $txnSeqno = null)
    {
        $params = [
            'txn_seqno' => $txnSeqno,
            'accp_txno' => $accpTxno,
        ];

        return $this->parse($this->url("txn/query-refund"), $params);
    }

}