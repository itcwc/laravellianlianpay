<?php

namespace Itcwc\LianLianPay\Withdrawal;

use Itcwc\LianLianPay\Core\AbstractAPI;
use Itcwc\LianLianPay\Exceptions\HttpException;
use Itcwc\LianLianPay\Support\Collection;

class Client extends AbstractAPI
{
    /**
     * 提现申请
     * 用户或商户将账户可用余额提现到开户时绑定的银行卡中，只能提现到本人卡。
     * 验证方式：支付密码+短信验证码。短验验证注册绑定手机号。
     * https://open.lianlianpay.com/docs/accp/accpstandard/withdrawal.html
     * @param array $params
     * @return Collection|null
     * @throws HttpException
     */
    public function apply(array $params)
    {
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);
        return $this->parse($this->url("txn/withdrawal"), $params);
    }

    /**
     * 提现确认
     * 提现申请时审核标识check_flag传了Y：需要提现确认，需要调用提现确认接口确认。
     * https://open.lianlianpay.com/docs/accp/accpstandard/withdrawal-check.html
     * @param array $params
     * @return Collection|null
     * @throws HttpException
     */
    public function confirm(array $params)
    {
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);
        return $this->parse($this->url("txn/withdrawal-check"), $params);
    }

    /**
     * 提现结果查询
     * 该接口提供所有提现场景下的订单查询，包括提现及代发到银行账户；商户可以通过该接口主动查询订单状态，完成下一步的业务逻辑。
     * 需要调用查询接口的情况：
     * 商户后台、网络、服务器等出现异常，商户最终未接收到提现结果通知；
     * 调用提现及代发申请接口后，返回系统错误或者未知交易、处理中交易状态情况。
     * https://open.lianlianpay.com/docs/accp/accpstandard/query-withdrawal.html
     * @param string|null $accpTxno 可选 ACCP系统交易单号。二选一，建议优先使用ACCP系统交易单号。
     * @param string|null $txnSeqno 可选 商户系统唯一交易流水号。二选一，建议优先使用ACCP系统交易单号。
     * @return Collection|null
     * @throws HttpException
     */
    public function query(string $accpTxno = null, string $txnSeqno = null)
    {
        $params = [
            'accp_txno' => $accpTxno,
            'txn_seqno' => $txnSeqno,
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner'],
        ];

        return $this->parse($this->url("txn/query-withdrawal"), $params);
    }
}