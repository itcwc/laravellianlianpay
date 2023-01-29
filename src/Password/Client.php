<?php

namespace Itcwc\LianLianPay\Password;

use Itcwc\LianLianPay\Core\AbstractAPI;
use Itcwc\LianLianPay\Exceptions\HttpException;
use Itcwc\LianLianPay\Support\Collection;


/**
 * 密码操作
 * Class Client
 * @package Yoka\LianLianPay\Password
 */
class Client extends AbstractAPI
{
    /**
     * 重置密码
     * 用户需要更新密码或者输错密码次数过多被锁定可以通过重置密码解决
     * https://open.lianlianpay.com/docs/accp/accpstandard/change-password.html
     * @param string $userID 必传 用户在商户系统中的唯一编号，要求该编号在商户系统能唯一标识用户。由商户自定义。
     * @param string $password 必传 密码。6-12位的字母、数字，不可以是连续或者重复的数字和字母，正则：^[a-zA-Z0-9]{6,12}$。
     * @param string $passwordNew 必传 新支付密码。6-12位的字母、数字，不可以是连续或者重复的数字和字母，正则：^[a-zA-Z0-9]{6,12}$。
     * @param string $randomKey 必传 密码随机因子key。随机因子获取方法返回。
     * @param null $riskItem 可选 风险控制参数。连连风控部门要求商户统一传入风险控制参数字段，字段值为json 字符串的形式。
     * @return Collection|null
     * @throws HttpException
     */
    public function reset(string $userID, string $password, string $passwordNew, string $randomKey, $riskItem = null)
    {
        $params = [
            'user_id' => $userID,
            'password' => $password,
            'password_new' => $passwordNew,
            'random_key' => $randomKey,
            'risk_item' => $riskItem,
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];
        return $this->parse($this->url("acctmgr/change-password"), $params);
    }
 
    /**
     * 找回密码申请
     * 用户忘记密码或者输错密码次数过多被锁定可以通过找回密码解决。
     * https://open.lianlianpay.com/docs/accp/accpstandard/find-password-apply.html
     * @param string $userID 必传 用户在商户系统中的唯一编号，要求该编号在商户系统能唯一标识用户。由商户自定义。
     * @param string|null $linkedAcctno 个人用户必填 绑定银行账号。个人用户绑定的银行卡号。
     * @param string|null $riskItem 可选 风险控制参数。连连风控部门要求商户统一传入风险控制参数字段，字段值为json 字符串的形式。
     * @return Collection|null
     * @throws HttpException
     */
    public function find(string $userID, string $linkedAcctno = null, string $riskItem = null)
    {
        $params = [
            'user_id' => $userID,
            'linked_acctno' => $linkedAcctno,
            'risk_item' => $riskItem,
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];

        return $this->parse($this->url("acctmgr/find-password-apply"), $params);
    }

    /**
     * 找回密码验证
     * 用户忘记密码或者输错密码次数过多被锁定可以通过找回密码解决。
     * https://open.lianlianpay.com/docs/accp/accpstandard/find-password-verify.html
     * @param string|null $userID 必传 用户在商户系统中的唯一编号，要求该编号在商户系统能唯一标识用户。由商户自定义。
     * @param string|null $token 必传 授权令牌，有效期为30分钟。
     * @param string|null $verifyCode 必传 短信验证码。企业和个体工商户验证注册绑定手机号。个人验证银行预留手机号。
     * @param string|null $randomKey 必传 密码随机因子key。随机因子获取方法返回。
     * @param string|null $password 必传 新支付密码。6-12位的字母、数字，不可以是连续或者重复的数字和字母，正则：^[a-zA-Z0-9]{6,12}$。
     * @return Collection|null
     * @throws HttpException
     */
    public function verify(string $userID, string $token, string $verifyCode, string $randomKey, string $password)
    {
        $params = [
            'user_id' => $userID,
            'token' => $token,
            'verify_code' => $verifyCode,
            'random_key' => $randomKey,
            'password' => $password,
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];

        return $this->parse($this->url("acctmgr/find-password-verify"), $params);
    }

    /**
     * 支付密码校验
     * 此接口提供校验开户时设置的支付密码的功能。
     * https://open.lianlianpay.com/docs/accp/accpstandard/validate-password.html
     * @param string $userID 必传 用户在商户系统中的唯一编号，要求该编号在商户系统能唯一标识用户。由商户自定义。
     * @param string $randomKey 必传 密码随机因子key。随机因子获取方法返回。
     * @param string $password 必传 密码。6-12位的字母、数字，不可以是连续或者重复的数字和字母，正则：^[a-zA-Z0-9]{6,12}$。
     * @return Collection|null
     * @throws HttpException
     */
    public function validate(string $userID, string $randomKey, string $password)
    {
        $params = [
            'user_id' => $userID,
            'random_key' => $randomKey,
            'password' => $password,
            'timestamp' => $this->timestamp,
            'oid_partner' => $this->config['oid_partner']
        ];

        return $this->parse($this->url("acctmgr/validate-password"), $params);
    }

}