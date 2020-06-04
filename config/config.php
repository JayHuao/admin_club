<?php

return [

    'status_n' => 0, // 删除
    'status_y' => 1, // 正常
    'status_b' => 2, // 禁用
    'status_w' => 3, // 等待处理

    'apply_p' => 1, // 已通过
    'apply_i' => 2, // 申请中
    'apply_b' => 3, // 已驳回

    'order_w' => 1, // 待支付
    'order_p' => 2, // 配货中
    'order_g' => 3, // 待收货
    'order_f' => 4, // 已完成
    'order_c' => 5, // 已取消

    'default_psw' => 123456,
    'host' => 'https://yrxgroup.yiqichi.top:4430',

    // 应用调试模式
    'app_debug' => true,
    // 开启应用Trace
    'app_trace' => false,
    // 自动搜索控制器
    'controller_auto_search' => true,
    // URL普通方式参数 用于自动生成
    'url_common_param' => true,
    // URL伪静态后缀
    'url_html_suffix' => '',
    // 视图输出字符串内容替换,留空则会自动进行计算
    'view_replace_str' =>  [
        '__STATIC__' => '/static/',
    ],
    // 默认输出类型
    'default_return_type' => 'json',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return' => 'json',
    'log' => [
        // 日志记录方式
        'type' => 'File',
        // error和sql日志单独记录
        'apart_level' => ['error', 'sql']
    ]
];
