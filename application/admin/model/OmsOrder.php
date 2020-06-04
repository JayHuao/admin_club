<?php

namespace app\admin\model;

use think\Config;
use think\Model;

class OmsOrder extends Model
{
    protected $type = [
        'order_time'    => 'timestamp',
        'pay_time'      => 'timestamp',
        'delivery_time' => 'timestamp',
        'collect_time'  => 'timestamp',
        'close_time'    => 'timestamp',
    ];

    public function detail()
    {
        return $this->hasMany('OmsOrderDetail','order_id')->field([
            'concat(name," ￥",price,"*",number)' => 'detail'
        ]);
    }

    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('order_no|s.name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['status']) && $model->where('o.status', 'in', $filter['status']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['type']) && $model->where('o.type', $filter['type']);
        !empty($filter['order_no']) && $model->where('order_no', $filter['order_no']);
        !empty($filter['nickname']) && $model->where('nickname', $filter['nickname']);
        !empty($filter['order_time']) && $model->whereTime('order_time', $filter['order_time']);
        !empty($filter['pay_time']) && $model->whereTime('pay_time', $filter['pay_time']);
        !empty($filter['collect_time']) && $model->whereTime('collect_time', $filter['collect_time']);
        !empty($filter['close_time']) && $model->whereTime('close_time', $filter['close_time']);

        switch ($filter['t']) {
            case 'today':
                $model->whereTime('order_time', 'today');
                break;
            case 'paid':
                $model->where('o.status', 2);
                break;
        }

        $total = $model
            ->alias('o')
            ->join('pms_store s','s.id = o.store_id')
            ->join('user u','u.id = o.user_id')
            ->count();

        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('order_no|s.name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['status']) && $model->where('o.status', 'in', $filter['status']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['type']) && $model->where('o.type', $filter['type']);
        !empty($filter['order_no']) && $model->where('order_no', $filter['order_no']);
        !empty($filter['nickname']) && $model->where('nickname', $filter['nickname']);
        !empty($filter['order_time']) && $model->whereTime('order_time', $filter['order_time']);
        !empty($filter['pay_time']) && $model->whereTime('pay_time', $filter['pay_time']);
        !empty($filter['collect_time']) && $model->whereTime('collect_time', $filter['collect_time']);
        !empty($filter['close_time']) && $model->whereTime('close_time', $filter['close_time']);

        switch ($filter['t']) {
            case 'today':
                $model->whereTime('order_time', 'today');
                break;
            case 'paid':
                $model->where('o.status', 2);
                break;
        }

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 1: $model->order('order_no ' . $dir); break;
                case 2: $model->order('o.type ' . $dir); break;
                case 3: $model->order('s.name ' . $dir); break;
                case 4: $model->order('nickname ' . $dir); break;
                case 5: $model->order('price ' . $dir); break;
                case 6: $model->order('order_time ' . $dir); break;
                case 7: $model->order('o.status ' . $dir); break;
                case 8: $model->order('o.pay_status ' . $dir); break;
            }
        } else {
            $model->order('order_time desc,o.status');
        }

        $list = $model
            ->alias('o')
            ->join('pms_store s','s.id = o.store_id')
            ->join('user u','u.id = o.user_id')
            ->field('o.*,s.name as store_name,u.nickname')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
