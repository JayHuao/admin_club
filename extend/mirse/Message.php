<?php

namespace mirse;


use app\api\model\OmsOrder;
use EasyWeChat\Factory;
use think\Db;

class Message {
    protected $config = [
        'app_id' => 'wxd3284a78285124a9',
        'secret' => '82413f0f1ba5e5e1dc1d9a9e0bdd8a43',
        'response_type' => 'array'
    ];


    public function __construct()
    {
        $log = [
            'log' => [
                'level' => 'debug',
                'file' => __DIR__.'/message.log',
            ]
        ];
        $this->config = array_merge($this->config, $log);
    }

    /**
     * 下单成功通知
     * @param string $orderNo 订单号
     */
    public function sendCreateOrderMessage($orderNo)
    {
        $order = OmsOrder::get(['order_no'=>$orderNo], ['user','store']);
        $openid = $order->user->openid;

        switch ($order->type) {
            case 1:
                $type = '到店自提';
                break;
            case 2:
                $type = '商家配送';
                break;
            case 3:
                $type = '快递配送';
                break;
        }

        $data = [
            'template_id' => 'EXvLnFEnpB0-0YaZud_RE9NU5Tos6MozyuKz94csIus',
            'touser' => $openid,
            'page' => '/pages/order/order',
            'data' => [
                'thing1' => [
                    'value' => $type
                ],
                'character_string6' => [
                    'value' => $order->order_no
                ],
                'amount3' => [
                    'value' => $order->pay_price,
                ],
                'date4' => [
                    'value' => $order->order_time,
                ],
                'thing5' => [
                    'value' => $order->store->name,
                ],
            ],
        ];
        $app = Factory::miniProgram($this->config);
        $app->rebind('cache', new \Symfony\Component\Cache\Adapter\FilesystemAdapter('yrx_group',3600,CACHE_PATH));
        $app->subscribe_message->send($data);
    }

    /**
     * 订单发货通知
     * @param int $orderId 订单id
     */
    public function sendDeliveryMessage($orderId)
    {
        $order = OmsOrder::get($orderId, ['user','store']);
        $openid = $order->user->openid;

        switch ($order->type) {
            case 1:
                $type = '到店自提';
                break;
            case 2:
                $type = '商家配送';
                break;
            case 3:
                $type = '快递配送';
                break;
        }

        $data = [
            'template_id' => 'HZgLGrC53ajVnX6xTtqLIUD2k0PA8Z4ydeSz_draKo8',
            'touser' => $openid,
            'page' => '/pages/order/order',
            'data' => [
                'thing4' => [
                    'value' => $order->store->name
                ],
                'thing16' => [
                    'value' => $type
                ],
                'character_string7' => [
                    'value' => $order->order_no
                ],
                'amount11' => [
                    'value' => $order->pay_price
                ],
                'thing9' => [
                    'value' => $order->receiver_address
                ]
            ],
        ];
        $app = Factory::miniProgram($this->config);
        $app->rebind('cache', new \Symfony\Component\Cache\Adapter\FilesystemAdapter('yrx_group',3600,CACHE_PATH));
        $app->subscribe_message->send($data);
    }

    /**
     * 订单自提通知
     * @param int $orderId 订单id
     */
    public function sendPickUpMessage($orderId)
    {
        $order = OmsOrder::get($orderId, ['user','store']);
        $openid = $order->user->openid;

        $data = [
            'template_id' => 'o7oK-NkYnqN3W-RpMr304OwCT1D4e4E77_cDkFo5a14', // 所需下发的订阅模板id
            'touser' => $openid, // 接收者（用户）的 openid
            'page' => '/pages/order/order', // 点击模板卡片后的跳转页面
            'data' => [
                'thing2' => [
                    'value' => $order->store->name,
                ],
                'thing3' => [
                    'value' => $order->store->address,
                ],
                'character_string7' => [
                    'value' => $order->order_no,
                ],
                'character_string8' => [
                    'value' => $order->pick_no,
                ],
            ],
        ];
        $app = Factory::miniProgram($this->config);
        $app->rebind('cache', new \Symfony\Component\Cache\Adapter\FilesystemAdapter('yrx_group',3600,CACHE_PATH));
        $app->subscribe_message->send($data);
    }

    /**
     * 订单退款通知
     * @param int $orderId 订单id
     */
    public function sendRefundMessage($orderId, $refundAmount)
    {
        $order = OmsOrder::get($orderId, ['user','store']);
        $openid = $order->user->openid;

        $data = [
            'template_id' => 'Fb_yWCq0tU2vizKZ3H55nKzOEUEU-F8zVz8GzohRNZI',
            'touser' => $openid,
            'page' => '/pages/order/order',
            'data' => [
                'thing12' => [
                    'value' => $order->store->name
                ],
                'character_string7' => [
                    'value' => $order->order_no
                ],
                'amount3' => [
                    'value' => '¥' . $refundAmount
                ],
                'date4' => [
                    'value' => date('Y-m-d H:i:s')
                ],
                'thing5' => [
                    'value' => '退款会原路退回，请注意查收'
                ],
            ],
        ];
        $app = Factory::miniProgram($this->config);
        $app->rebind('cache', new \Symfony\Component\Cache\Adapter\FilesystemAdapter('yrx_group',3600,CACHE_PATH));
        $app->subscribe_message->send($data);
    }
}