<?php

namespace app\admin\model;

use think\Model;

class SmsProductSeo extends Model
{
    protected $insert = ['status' => 1];

    public static function getTotal($filter=null)
    {
        $model = new self();
        $model->where('s.status', '<>', 0);

        !empty($filter['search']) && $model->where('pl.name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['position']) && $model->where('s.position', $filter['position']);

        $total = $model
            ->alias('s')
            ->join('pms_product_library pl','pl.id = s.product_library_id')
            ->count();

        return $total;
    }

    public static function getList($column, $filter, $page, $limit, $order)
    {
        $model = new self();
        $model->where('s.status', '<>', 0);

        !empty($filter['search']) && $model->where('pl.name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['position']) && $model->where('s.position', $filter['position']);

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('position');
        }

        $list = $model
            ->alias('s')
            ->join('pms_product_library pl','pl.id = s.product_library_id')
            ->field('s.*,pl.name as product_name,pl.cover as product_cover')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
