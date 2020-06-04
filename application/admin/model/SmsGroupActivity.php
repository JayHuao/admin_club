<?php

namespace app\admin\model;

use think\Model;

class SmsGroupActivity extends Model
{
    protected $insert = ['status'=>1];

    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('activity_name|group_name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['type']) && $model->where('type', $filter['type']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['activity_name']) && $model->where('activity_name', $filter['activity_name']);

        $total = $model
            ->alias('ga')
            ->join('group g','g.id = ga.store_id')
            ->count();

        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('activity_name|group_name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['type']) && $model->where('type', $filter['type']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['activity_name']) && $model->where('activity_name', $filter['activity_name']);

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 0: $model->order('activity_name ' . $dir); break;
            }
        } else {
            $model->order('start_time desc');
        }

        $list = $model
            ->alias('ga')
            ->join('group g','g.id = ga.store_id')
            ->field('ga.*,group_name as store_name')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
