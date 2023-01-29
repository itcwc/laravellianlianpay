<?php

namespace Itcwc\LianLianPay\Payment;

use Itcwc\LianLianPay\Core\AbstractAPI;
use Itcwc\LianLianPay\Exceptions\HttpException;
use Itcwc\LianLianPay\Exceptions\InvalidArgumentException;
use Itcwc\LianLianPay\Support\Arr;
use Itcwc\LianLianPay\Support\Collection;
use Itcwc\LianLianPay\Support\Log;
use Itcwc\LianLianPay\Core\LLHelper;

class Client extends AbstractAPI
{
    const FLAG_CARD_PERSON = '0';
    const FLAG_CARD_ORGANIZE = '1';

    //protected $baseUrl = 'https://instantpay.lianlianpay.com/';

    /**
     * 生产有效的商户订单号(最好排重)
     * @return string
     */
    public static function findAvailableNoOrder()
    {
        return date('YmdHis') . substr(explode(' ', microtime())[0], 2, 6) . rand(1000, 9999);
    }


    /**
     * 发起一笔付款申请
     *
     * @param string $moneyOrder 付款金额保留小数点后2位,单位元
     * @param string $cardNo 收款方银行账号
     * @param string $acctName 收款方姓名
     * @param string $infoOrder 订单描述。说明付款用途，5W以上必传。
     * @param string $memo 收款备注。 传递至银行， 一般作为订单摘要展示。
     * @param string $noOrder 商户订单号。
     * @param string $riskItem 风险控制参数。
     * @param string $notifyUrl 接收异步通知的线上地址。
     * @param string $flagCard 对公对私标志。
     * @param string $bankName 收款银行名称。
     * @param string $prcptcd 大额行号。 可调用大额行号查询接口进行查询。
     * @param string $bankCode 银行编码。 flag_card为1时， 建议选择大额行号+银行编码或开户支行名称+开户行所在市编码+银行编码中的一组传入。
     * @param string $cityCode 开户行所在省市编码， 标准地市编码。
     * @param string $braBankName 开户支行名称
     * @return Collection|null
     * @throws HttpException
     */
    public function payment($moneyOrder, $cardNo, $acctName, $infoOrder, $memo, $noOrder = null, $riskItem = null,
                            $notifyUrl = null, $flagCard = self::FLAG_CARD_PERSON, $bankName = null, $prcptcd = null,
                            $bankCode = null, $cityCode = null, $braBankName = null)
    {
        $params = [
            "oid_partner" => $this->config['oid_partner'],
            "platform" => $this->config['platform'],
            "api_version" => $this->production ? '1.1' : '1.0',
            "sign_type" => self::SIGN_TYPE_RSA,
            "no_order" => $noOrder ?: $this->findAvailableNoOrder(),
            "dt_order" => date('YmdHis'),
            "money_order" => $moneyOrder,
            "card_no" => $cardNo,
            "acct_name" => $acctName,
            "info_order" => $infoOrder,
            "flag_card" => $flagCard,
            "memo" => $memo,
            "notify_url" => $notifyUrl ?: $this->config['notify_url'],
            "risk_item" => $this->production ? $riskItem : null,
            // 以下是对公打款可选参数
            "bank_name" => $bankName,
            "prcptcd" => $prcptcd,
            "bank_code" => $bankCode,
            "city_code" => $cityCode,
            "brabank_name" => $braBankName,
        ];

        return $this->payload($this->url('paymentapi/payment.htm'), $params);
    }

    /**
     * 确认付款 (疑似重复订单需要确认付款)
     *
     * @param $noOrder
     * @param $confirmCode
     * @param null $notifyUrl
     * @return Collection|null
     * @throws HttpException
     */
    public function confirmPayment($noOrder, $confirmCode, $notifyUrl = null)
    {
        $params = [
            "oid_partner" => $this->config['oid_partner'],
            "platform" => $this->config['platform'],
            "api_version" => '1.0',
            "sign_type" => self::SIGN_TYPE_RSA,
            "no_order" => $noOrder,
            "confirm_code" => $confirmCode,
            "notify_url" => $notifyUrl ?: $this->config['notify_url'],
        ];

        return $this->payload($this->url('paymentapi/confirmPayment.htm'), $params);
    }

    /**
     * @param null $noOrder
     * @param null $oidPayBill
     * @return Collection|null
     * @throws InvalidArgumentException|HttpException
     */
    public function queryPayment($noOrder = null, $oidPayBill = null)
    {
        if (empty($noOrder) && empty($oidPayBill)) {
            throw new InvalidArgumentException('noOrder 和 oidPayBill 不能都为空');
        }

        $params = [
            "oid_partner" => $this->config['oid_partner'],
            "sign_type" => self::SIGN_TYPE_RSA,
            "no_order" => $noOrder,
            "platform" => $this->config['platform'],
            "oid_paybill" => $oidPayBill,
            "api_version" => '1.0',
        ];

        return $this->payload($this->url('paymentapi/queryPayment.htm'), $params);
    }
    /**
     * 余额支付
     * 用户账户余额支付接口，支持附加优惠券组合支付。
     * https://open.lianlianpay.com/docs/accp/accpstandard/payment-balance.html
     * @param array $params
     * @return Collection|null
     * @throws HttpException
     */
    public function balance(array $params)
    {
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);
        return $this->parse($this->url("txn/payment-balance"), $params);
    }
    /**
     * 支付统一创单
     * 商户在充值/消费交易模式场景下使用，先通过该接口完成支付统一创单，后续根据业务场景调用不同的支付接口完成付款。
     * https://open.lianlianpay.com/docs/accp/accpstandard/accp-tradecreate.html
     * @param array $params
     * @return Collection|null
     * @throws HttpException
     */
    public function create(array $params)
    { 
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);
       
        return $this->parse($this->url("txn/tradecreate"), $params);
    }

    /**
     * 网关类支付
     * 微信、支付宝、网银类支付申请，通过该接口唤起相应渠道的支付界面，用户确认并支付，支持附加优惠券、余额组合支付。
     * https://open.lianlianpay.com/docs/accp/accpstandard/payment-gw.html
     * @param array $params
     * @return Collection|null
     * @throws HttpException
     */
    public function gateway(array $params)
    {
        $params['timestamp'] = $this->timestamp;
        $params['oid_partner'] = $this->config['oid_partner'];
        // return $params;
        return $this->parse($this->url("txn/payment-gw"), $params);
    }

    /**
     * 银行卡快捷支付
     * 银行卡快捷支付接口，支持附加优惠券、余额组合支付；适用于如下几种场景：已开户绑定银行卡的个人用户支付、未注册的匿名用户首次/历次支付
     * https://open.lianlianpay.com/docs/accp/accpstandard/payment-gw.html
     * @param array $params
     * @return Collection|null
     * @throws HttpException
     */
    public function bankCard(array $params)
    {
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);
        return $this->parse($this->url("txn/payment-bankcard"), $params);
    }

    /**
     * 支付单关单
     * 该接口提供对支付处理中的支付单进行关单处理。需要调用关单接口的情况：当用户支付失败了，需要重新发起支付，此时可调用关单接口关闭之前的支付单后再发起新的支付单支付。主要用于商户侧控制用户重复支付的问题。
     * https://open.lianlianpay.com/docs/accp/accpstandard/close-payment.html
     * @param null $accpTxno 可选 ACCP系统交易单号。二选一，建议优先使用ACCP系统交易单号。
     * @param null $txnSeqno 可选 商户系统唯一交易流水号。二选一，建议优先使用ACCP系统交易单号。
     * @return Collection|null
     * @throws HttpException
     */
    public function close($accpTxno = null, $txnSeqno = null)
    {
        $params = [
            'accp_txno' => $accpTxno,
            'txn_seqno' => $txnSeqno,
        ];
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);
        return $this->parse($this->url("txn/close-payment"), $params);
    }

    /**
     * 支付结果查询
     * 该接口提供所有消费/充值场景下的支付订单查询，商户可以通过该接口主动查询订单状态，完成下一步的业务逻辑。 需要调用查询接口的情况：
     * 商户后台、网络、服务器等出现异常，商户最终未接收到支付结果通知；
     * 调用支付接口后，返回系统错误或者未知交易、处理中交易状态情况。
     * https://open.lianlianpay.com/docs/accp/accpstandard/query-payment.html
     * @param null $accpTxno 可选 ACCP系统交易单号。【三选一】，建议优先使用ACCP系统交易单号。
     * @param null $txnSeqno 可选 商户系统唯一交易流水号。【三选一】，建议优先使用ACCP系统交易单号。
     * @param null $subChnlNO 可选 上游渠道流水号。【三选一】，建议优先使用 ACCP 系统交易单号 。
     * @return Collection|null
     * @throws HttpException
     */
    public function query($accpTxno = null, $txnSeqno = null, $subChnlNO = null)
    {
        $params = [
            'accp_txno' => $accpTxno,
            'txn_seqno' => $txnSeqno,
            'sub_chnl_no' => $subChnlNO,
        ];
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);
        return $this->parse($this->url("txn/close-payment"), $params);
    }

    public function transfer($params) {
        
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);

        return $this->parse($this->url('txn/transfer'), $params);
    }

    public function queryWithdrawal($params) {
        
        $baseParams = [
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        $params = array_merge($baseParams, $params);

        return $this->parse($this->url('txn/query-withdrawal'), $params);
    }


    // 内部代发
    public function InternalIssuing($params)
    {
        $params['timestamp'] = $this->timestamp;
        $params['oid_partner'] = $this->config['oid_partner'];

        return $this->parse($this->url("txn/transfer-morepyee"), $params);   
    }

    // 电子回单申请
    public function ReceiptProduce($params)
    {
        $params['timestamp'] = $this->timestamp;
        $params['oid_partner'] = $this->config['oid_partner'];
        
        return $this->parse($this->url("offlinetxn/receipt-produce"), $params);
    }

    // 电子回单下载
    public function ReceiptDownload($params)
    {
        $params['timestamp'] = $this->timestamp;
        $params['oid_partner'] = $this->config['oid_partner'];

        return $this->parse($this->url("offlinetxn/receipt-download"), $params);
    }

    // 交易二次短信验证
    public function ValidationSms($params)
    {
        $params['timestamp'] = $this->timestamp;
        $params['oid_partner'] = $this->config['oid_partner'];
        
        return $this->parse($this->url("txn/validation-sms"), $params);
    }

    // 虚拟卡申请
    public function VirtualnoApply($params)
    {
        $params['timestamp'] = $this->timestamp;
        $params['oid_partner'] = $this->config['oid_partner'];

        return $this->parse($this->url("acctmgr/virtualno-apply"), $params);
    }

    // 来帐宝申请
    public function InitLzb($params)
    {
        $params['timestamp'] = $this->timestamp;
        $params['oid_partner'] = $this->config['oid_partner'];

        return $this->parse('https://accpgw.lianlianpay.com/v1/lzb/init-lzb', $params);
    }

    // 退款申请
    public function MorePayeeRefund($params)
    {
        $params['timestamp'] = $this->timestamp;
        $params['oid_partner'] = $this->config['oid_partner'];
        // dd($params);
        return $this->parse($this->url("txn/more-payee-refund"), $params);
    }

}