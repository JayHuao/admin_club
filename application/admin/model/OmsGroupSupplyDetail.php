<?php

namespace app\admin\model;

use think\Model;

class OmsGroupSupplyDetail extends Model
{
    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('supply_no|s.name|product_name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['delivery_date']) && $model->where('delivery_date', $filter['delivery_date']);

        $total = $model
            ->alias('gsd')
            ->join('oms_group_supply gs','gs.id = gsd.group_supply_id')
            ->join('pms_store s','s.id = gs.store_id')
            ->count();

        return $total;
    }

    public static function getList($column, $filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('supply_no|s.name|product_name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['delivery_date']) && $model->where('delivery_date', $filter['delivery_date']);

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('delivery_date desc, store_id');
        }

        $list = $model
            ->alias('gsd')
            ->join('oms_group_supply gs','gs.id = gsd.group_supply_id')
            ->join('pms_store s','s.id = gs.store_id')
            ->field('gsd.*,supply_no,store_id,delivery_date,s.name as store_name')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
