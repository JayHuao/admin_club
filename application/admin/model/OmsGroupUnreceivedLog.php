<?php

namespace app\admin\model;

use think\Model;

class OmsGroupUnreceivedLog extends Model
{
    public static function getTotal($filter=null)
    {
        $model = new self();

        $model->where('gu.status', '<>', 0);

        !empty($filter['search']) && $model->where('product_name|s.name|gu.remark|order_no', 'like', '%' . $filter['search'] . '%');
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['order_no']) && $model->where('order_no', $filter['order_no']);
        !empty($filter['product_name']) && $model->where('product_name', $filter['product_name']);
        !empty($filter['status']) && $model->where('gu.status', $filter['status']);
        !empty($filter['feedback_time']) && $model->whereTime('feedback_time', 'between', [$filter['feedback_time'].' 00:00:00', $filter['feedback_time'].' 23:59:59']);

        $total = $model
            ->alias('gu')
            ->join('oms_order o','o.id = gu.order_id')
            ->join('pms_store s', 's.id = o.store_id')
            ->join('user u','u.id = gu.user_id')
            ->count();

        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();

        $model->where('gu.status', '<>', 0);

        !empty($filter['search']) && $model->where('product_name|s.name|gu.remark|order_no', 'like', '%' . $filter['search'] . '%');
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['order_no']) && $model->where('order_no', $filter['order_no']);
        !empty($filter['product_name']) && $model->where('product_name', $filter['product_name']);
        !empty($filter['status']) && $model->where('gu.status', $filter['status']);
        !empty($filter['feedback_time']) && $model->whereTime('feedback_time', 'between', [$filter['feedback_time'].' 00:00:00', $filter['feedback_time'].' 23:59:59']);

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 0: $model->order('s.name ' . $dir); break;
                case 1: $model->order('s.head ' . $dir . ', s.phone ' . $dir . ', s.address ' . $dir); break;
                case 2: $model->order('order_no ' . $dir); break;
                case 3: $model->order('product_name ' . $dir); break;
                case 4: $model->order('remark ' . $dir); break;
                case 5: $model->order('feedback_time ' . $dir); break;
                case 6: $model->order('nickname ' . $dir); break;
                case 7: $model->order('gu.status ' . $dir); break;
            }
        } else {
            $model->order('feedback_time desc,gu.status,store_id');
        }

        $list = $model
            ->alias('gu')
            ->join('oms_order o','o.id = gu.order_id')
            ->join('pms_store s', 's.id = o.store_id')
            ->join('user u','u.id = gu.user_id')
            ->field('
                gu.*,
                order_no,
                nickname,
                s.name as store_name,s.head,s.phone,s.address
            ')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
