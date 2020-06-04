<?php

namespace app\admin\model;

use think\Model;

class DmsUserCommission extends Model
{
    protected $createTime = 'win_at';

    /**
     * 根据类型计算用户佣金
     * @param integer $userId
     * @param integer $type 1=>自购返现；2=>销售返现；3=>分销返现；
     */
    public static function calcCommission($userId,$type)
    {
        $total = self::where('user_id', $userId)
            ->where('type', $type)
            ->sum('commission_amount');
        return number_format(round($total,2),2);
    }

    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('s.name|order_no', 'like', '%' . $filter['search'] . '%');
        !empty($filter['type']) && $model->where('uc.type', $filter['type']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['order_no']) && $model->where('order_no', $filter['order_no']);
        !empty($filter['win_at']) && $model->whereTime('win_at', $filter['win_at']);

        $total = $model
            ->alias('uc')
            ->join('user u','u.id = uc.user_id')
            ->join('oms_order o','o.id = uc.order_id')
            ->join('pms_store s', 's.id = o.store_id')
            ->count();

        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('s.name|order_no', 'like', '%' . $filter['search'] . '%');
        !empty($filter['type']) && $model->where('uc.type', $filter['type']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['order_no']) && $model->where('order_no', $filter['order_no']);
        !empty($filter['win_at']) && $model->whereTime('win_at', 'between', [strtotime($filter['win_at'].' 00:00:00'), strtotime($filter['win_at'].' 23:59:59')]);

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 0: $model->order('nickname ' . $dir); break;
                case 1: $model->order('s.name ' . $dir); break;
                case 2: $model->order('order_no ' . $dir); break;
                case 3: $model->order('commission_amount ' . $dir); break;
                case 4: $model->order('win_at ' . $dir); break;
            }
        } else {
            $model->order('win_at desc,store_id');
        }

        $list = $model
            ->alias('uc')
            ->join('user u','u.id = uc.user_id')
            ->join('oms_order o','o.id = uc.order_id')
            ->join('pms_store s', 's.id = o.store_id')
            ->field('
                uc.*,
                order_no,
                nickname,
                s.name as store_name
            ')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
