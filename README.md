

# 连连支付 原作者：yuuhao
# [原项目地址](https://open.lianlianpay.com/docs/accp/accpstandard/regphone-verifycode-apply.html)
### 侵权联系请我删除
### 如何使用

* 安装
```shell
$ composer require itcwc/laravel-lianlianpay:dev-master 
```

* 使用

```php
$config = [
    'debug' => true, // 开启调试
    'oid_partner' => '2020042200284052', // 商户号
    'private_key' => file_get_contents('merchant_rsa_private_key.pem'), // 商户私钥 地址自行配置，绝对路径
    'public_key' => file_get_contents('merchant_rsa_public_key.pem'), // 商户公钥
    'll_public_key' => file_get_contents('llpay_public_key.pem'), // 连连支付公钥
    'production' => env('APP_ENV') == 'pro', // 是否生产环境 根据.env文件值判断
    // 日志
    'log' => [
        'level' => 'debug',
        'permission' => 0777,
        'file' => storage_path('logs/lianlianpay-' . date('Y-m-d') . '.log'), // 日志文件, 你可以自定义
    ],
];
$llp = new \Yoka\LianLianPay\LianLianPay($config);
$result = $llp->account->phoneVerifyCodeApply('s1', '13250840721'); // 绑定手机验证码申请
```

### SDK【后续调用不再更新md，可进入项目文件夹查看或自增】

调用示例：
``` $llp->account->phoneVerifyCodeApply($userId, $regPhone, $timestamp = null) ```
接口参数具体值请查看相关文档地址
接口中涉及字段`timestamp`、`oid_partner`统一不要传,`oid_partner`采用实例化config中配置参数，`timestamp`采用服务器当前时间
**** 
| 模块 | 方法 | 接口名  |文档地址|
|:-------------:|:-------------|:-------------|:-----|
| account | ```phoneVerifyCodeApply``` |绑定手机验证码申请 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/regphone-verifycode-apply.html)|
| account | ```phoneVerifyCodeVerify``` |绑定手机验证码验证 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/regphone-verifycode-verify.html)|
| account | ```personOpenAcctApply``` |个人用户开户申请 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/openacct-apply-individual.html)|
| account | ```personOpenAcctVerify``` |个人用户开户验证 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/openacct-verify-individual.html)|
| account | ```enterpriseOpenAcctApply``` |企业用户开户申请 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/openacct-apply-enterprise.html)|
| account | ```enterpriseOpenAcctVerify``` |企业用户开户验证 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/openacct-verify-enterprise.html)|
| account | ```upload``` |文件上传 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/upload.html)|
| account | ```uploadPhotos``` |上传照片 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/upload-photos.html)|
| account | ```openAcctApply``` |用户开户申请(页面接入) |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/openacct-apply.html)|
| account | ```modifyPersonUserInfo``` |个人用户信息修改 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/modify-userinfo-individual.html)|
| account | ```modifyEnterpriseUserInfo``` |企业用户信息修改 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/modify-userinfo-enterprise.html)|
| common | ```getRandom``` |获取随机因子 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/get-random.html)|
| common | ```verifyNotifySignature``` |回调通知数据验签 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/accp-async-notification-overview.html)|
| common | ```validationSms``` |交易二次短信验证 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/validation-sms.html)|
| payment | ```create``` |支付统一创单 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/accp-tradecreate.html)|
| payment | ```gateway``` |网关类支付 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/payment-gw.html)|
| payment | ```bankCard``` |银行卡快捷支付 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/payment-gw.html)|
| payment | ```close``` |支付单关单 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/close-payment.html)|
| payment | ```query``` |支付结果查询 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/query-payment.html)|
| secured | ```confirm``` |担保交易确认 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/secured-confirm.html)|
| secured | ```query``` |担保交易信息查询 |[地址](https://open.lianlianpay.com/docs/accp/accpstandard/secured-query.html)|
| refund | ```apply``` |退款申请|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/more-payee-refund.html)|
| refund | ```query``` |退款查询|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/query-refund.html)|
| withdrawal | ```apply``` |提现申请|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/withdrawal.html)|
| withdrawal | ```confirm``` |提现确认|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/withdrawal-check.html)|
| withdrawal | ```query``` |提现结果查询|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/query-withdrawal.html)|
| password | ```reset``` |重置密码|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/change-password.html)|
| password | ```find``` |找回密码申请|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/find-password-apply.html)|
| password | ```verify``` |找回密码验证|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/find-password-verify.html)|
| password | ```validate``` |支付密码校验|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/validate-password.html)|
| accManage | ```personBindCardApply``` |个人用户新增绑卡|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/individual-bindcard-apply.html)|
| accManage | ```personBindCardVerify``` |个人用户绑卡验证|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/individual-bindcard-verify.html)|
| accManage | ```unbindCardApply``` |个人用户解绑银行卡|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/unlinkedacct-ind-apply.html)|
| accManage | ```enterpriseChangeCardApply``` |企业用户更换绑定账号申请|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/enterprise-changecard-apply.html)|
| accManage | ```enterpriseChangeCardVerify``` |企业用户更换绑定账号验证|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/enterprise-changecard-verify.html)|
| accManage | ```changeRegPhoneApply``` |修改绑定手机申请|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/change-regphone-apply.html)|
| accManage | ```changeRegPhoneVerify``` |修改绑定手机验证|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/change-regphone-verify.html)|
| accManage | ```changeLinkedPhoneApply``` |修改预留手机号申请|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/change-linkedphone-apply.html)|
| accManage | ```changeLinkedPhoneVerify``` |修改预留手机号验证|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/change-linkedphone-verify.html)|
| accManage | ```queryLinkedAcct``` |绑卡信息查询|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/query-linkedacct.html)|
| accManage | ```queryUserInfo``` |用户信息查询|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/query-userinfo.html)|
| accManage | ```queryAcctInfo``` |账户信息查询|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/query-acctinfo.html)|
| accManage | ```queryAcctSerial``` |资金流水列表查询|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/query-acctserial.html)|
| accManage | ```queryAcctSerialDetail``` |资金流水详情查询|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/query-acctserialdetail.html)|
| accManage | ```queryTxn``` |交易流水结果查询|[地址](https://open.lianlianpay.com/docs/accp/accpstandard/query-txn.html)|