<?php

namespace app\admin\model;

use think\Model;

class PmsProductSeo extends Model
{
    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('pc.name|pl.name|desc|sp', 'like', '%' . $filter['search'] . '%');
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['store_id']) && $model->where('p.store_id', $filter['store_id']);
        !empty($filter['product_name']) && $model->where('pl.name', $filter['product_name']);
        !empty($filter['status']) && $model->where('p.status', $filter['status']);

        $total = $model
            ->alias('ps')
            ->join('pms_products p','p.id = ps.product_id')
            ->join('pms_product_library pl','pl.id = p.product_library_id')
            ->join('pms_product_category pc','pc.id = pl.category_id')
            ->count();

        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('pc.name|pl.name|desc|sp', 'like', '%' . $filter['search'] . '%');
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['store_id']) && $model->where('p.store_id', $filter['store_id']);
        !empty($filter['product_name']) && $model->where('pl.name', $filter['product_name']);
        !empty($filter['status']) && $model->where('p.status', $filter['status']);

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 1: $model->order('pc.name ' . $dir); break;
                case 2: $model->order('s.name ' . $dir); break;
                case 3: $model->order('pl.name ' . $dir); break;
                case 4: $model->order('sp ' . $dir); break;
                case 5: $model->order('price ' . $dir); break;
                case 6: $model->order('desc ' . $dir); break;
                case 7: $model->order('p.sort ' . $dir); break;
                case 8: $model->order('p.status ' . $dir); break;
            }
        } else {
            $model->order('p.status, sort desc');
        }

        $list = $model
            ->alias('ps')
            ->join('pms_products p','p.id = ps.product_id')
            ->join('pms_product_library pl','pl.id = p.product_library_id')
            ->join('pms_product_category pc','pc.id = pl.category_id')
            ->join('pms_store s', 's.id = p.store_id')
            ->field('
                p.*,
                pl.category_id,pl.name,pl.cover,pl.weight,pl.sp,pl.desc,pl.original_price,pl.floor_price,
                pc.name as category_name,
                s.name as store_name
            ')
            ->distinct(true)
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
