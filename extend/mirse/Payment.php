<?php

namespace mirse;

use app\api\model\OmsOrder;
use EasyWeChat\Factory;
use think\Config;
use think\Db;
use think\Url;

class Payment {
    protected $config;

    public function __construct()
    {
        $this->config = [
            // 必要配置
            'app_id'             => Config::get('site.appid'),
            'mch_id'             => Config::get('site.mchid'),
            'key'                => Config::get('site.apikey'),   // API 密钥

            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          => EXTEND_PATH . 'mirse/apiclient_cert.pem',
            'key_path'           => EXTEND_PATH . 'mirse/apiclient_key.pem',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__.'/payment.log',
            ]
        ];
    }

    /**
     * 企业付款
     * @param
     */
    public function transfer($openid, $amount, $desc)
    {
        $tradeNo = OmsOrder::createTransferNo();
        $amount = bcmul($amount, 100);
        $app = Factory::payment($this->config);
        $app->rebind('cache', new \Symfony\Component\Cache\Adapter\FilesystemAdapter('yrx_group',3600,CACHE_PATH));
        $app->transfer->toBalance([
            'partner_trade_no' => $tradeNo, // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid'           => $openid,
            'check_name'       => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
            're_user_name'     => '', // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
            'amount'           => $amount, // 企业付款金额，单位为分
            'desc'             => $desc, // 企业付款操作说明信息。必填
        ]);

        return $tradeNo;
    }

    /**
     * 查询付款到零钱的订单
     */
    public function queryBalanceOrder($tradeNo)
    {
        $app = Factory::payment($this->config);
        $app->rebind('cache', new \Symfony\Component\Cache\Adapter\FilesystemAdapter('yrx_group',3600,CACHE_PATH));
        $res = $app->transfer->queryBalanceOrder($tradeNo);

        return $res;
    }

    /**
     * 退款
     * @param string $orderNo 订单号
     * @param float $totalFee 订单金额
     * @param float $refundFee 退款金额
     * @param string $remark 备注
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function refund($orderNo, $totalFee, $refundFee, $remark)
    {
        $refundNo = OmsOrder::createRefundNo();

        $app = Factory::payment($this->config);
        $app->rebind('cache', new \Symfony\Component\Cache\Adapter\FilesystemAdapter('yrx_group',3600,CACHE_PATH));
        $result = $app->refund->byOutTradeNumber($orderNo, $refundNo, $totalFee, $refundFee, [
            'refund_desc' => $remark,
            'notify_url'  => 'http://yrxgroup.yiqichi.top/index.php/api/notify/refund_notify'
        ]);

        if ($result['return_code'] == 'SUCCESS') {
            Db::name('oms_order_refund_log')->insert([
                'out_trade_no'   => $result['out_trade_no'],
                'out_refund_no'  => $result['out_refund_no'],
                'transaction_id' => $result['transaction_id'],
                'refund_id'      => $result['refund_id'],
                'total_fee'      => $result['total_fee'],
                'refund_fee'     => $result['refund_fee'],
                'refund_time'    => date('Y-m-d H:i:s')
            ]);
        }

        return $result;
    }

}
