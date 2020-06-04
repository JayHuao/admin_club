<?php

namespace app\admin\model;

use think\Model;

class OmsGroupReceivedLog extends Model
{
    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('s.name|order_no', 'like', '%' . $filter['search'] . '%');
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['order_no']) && $model->where('order_no', $filter['order_no']);
        !empty($filter['received_time']) && $model->whereTime('received_time', 'between', [$filter['received_time'].' 00:00:00', $filter['received_time'].' 23:59:59']);

        $total = $model
            ->alias('gr')
            ->join('oms_order o','o.id = gr.order_id')
            ->join('pms_store s', 's.id = o.store_id')
            ->join('user u','u.id = gr.user_id')
            ->count();

        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('s.name|order_no', 'like', '%' . $filter['search'] . '%');
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['order_no']) && $model->where('order_no', $filter['order_no']);
        !empty($filter['received_time']) && $model->whereTime('received_time', 'between', [$filter['received_time'].' 00:00:00', $filter['received_time'].' 23:59:59']);

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 0: $model->order('s.name ' . $dir); break;
                case 1: $model->order('nickname ' . $dir); break;
                case 2: $model->order('order_no ' . $dir); break;
                case 3: $model->order('received_time ' . $dir); break;
            }
        } else {
            $model->order('received_time desc,store_id');
        }

        $list = $model
            ->alias('gr')
            ->join('oms_order o','o.id = gr.order_id')
            ->join('pms_store s', 's.id = o.store_id')
            ->join('user u','u.id = gr.user_id')
            ->field('
                gr.*,
                order_no,
                nickname,
                s.name as store_name,s.head,s.phone,s.address
            ')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
