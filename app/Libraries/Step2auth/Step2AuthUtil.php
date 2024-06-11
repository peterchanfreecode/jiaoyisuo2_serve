<?php

namespace App\Libraries\Step2auth;

class  Step2AuthUtil
{
    /**
     * @var CommonLibGAGoogleAuthenticator
     */
    private $ga = null;

    public function __construct() {
        $this->init();
    }

    public function init() {
        $this->ga = new Step2authAlgorithm();
    }

    /**
     * 生成安全码
     * 创建一个新的"安全密匙SecretKey"
     * 把本次的"安全密匙SecretKey" 入库,和账户关系绑定,客户端也是绑定这同一个"安全密匙SecretKey"
     * @return string
     * @throws Exception
     */
    public function createSecret() {
        return $this->ga->createSecret();
    }

    /**
     * 生成qrcode url
     * //第一个参数是"标识",第二个参数为"安全密匙SecretKey" 生成二维码信息
     * @param $platformDomainName
     * @param $secret
     * @return string
     * @throws Exception
     */
    public function getQRCodeGoogleUrl($platformDomainName, $secret) {
        return $this->ga->getQRCodeGoogleUrl($platformDomainName, $secret);
    }

    /**
     * //服务端计算"一次性验证码"
     * @param $secret
     * @return string
     */
    public function getCode($secret) {
        return $this->ga->getCode($secret);
    }

    /**
     * //把提交的验证码和服务端上生成的验证码做对比
     * // $secret 服务端的 "安全密匙SecretKey"
     * // $oneCode 手机上看到的 "一次性验证码"
     * // 最后一个参数 为容差时间,这里是2 那么就是 2* 30 sec 一分钟.
     * @param $secret
     * @param $oneCode
     * @param int $discrepancy
     * @return bool
     */
    public function verifyCode($secret, $oneCode, $discrepancy = 0) {
        $result = $this->ga->verifyCode($secret, strval($oneCode), $discrepancy);
        if ($result) {
            return true;
        }
        return false;
    }
}
