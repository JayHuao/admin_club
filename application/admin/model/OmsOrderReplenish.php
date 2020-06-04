<?php

namespace app\admin\model;

use think\Model;

class OmsOrderReplenish extends Model
{
    protected $createTime = 'created_at';

    public function base()
    {
        return $this->belongsTo('OmsOrder', 'origin_order_id');
    }

    public function detail()
    {
        return $this->hasMany('OmsOrderReplenishDetail', 'order_replenish_id');
    }

    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('nickname|no|phone', 'like', '%' . $filter['search'] . '%');
        !empty($filter['nickname']) && $model->where('nickname', $filter['nickname']);
        !empty($filter['register_date']) && $model->where('create_time', 'between', [
            strtotime($filter['register_date']. ' 00:00:01'),
            strtotime($filter['register_date']. ' 23:59:59')
        ]);

        $total = $model
            ->alias('r')
            ->join('pms_store s','s.id = r.store_id')
            ->join('user u','u.id = r.user_id')
            ->count();

        return $total;
    }

    public static function getList($column, $filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('nickname|no|phone', 'like', '%' . $filter['search'] . '%');
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['delivery_date']) && $model->where('delivery_date', $filter['delivery_date']);
        !empty($filter['created_at']) && $model->whereTime('created_at', 'between', [
            strtotime($filter['created_at']),
            strtotime('+1 day', strtotime($filter['created_at']))
        ]);

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('created_at desc');
        }

        $list = $model
            ->alias('r')
            ->join('pms_store s','s.id = r.store_id')
            ->join('user u','u.id = r.user_id')
            ->field('r.*,s.name as store_name,u.nickname')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
