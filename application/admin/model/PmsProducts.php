<?php

namespace app\admin\model;

use think\Model;

class PmsProducts extends Model
{
    protected $createTime = 'created_at';

    public function library()
    {
        return $this->belongsTo('PmsProductLibrary', 'product_library_id');
    }

    public static function getTotal($filter=null)
    {
        $model = new self();
        $model->where('p.status', '<>', 0)
            ->where('pl.status', 1)
            ->where('s.status', 1);

        !empty($filter['search']) && $model->where('pc.name|pl.name|desc|sp|pl.remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['is_expect_date']) && $model->where('is_expect_date', $filter['is_expect_date']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['product_name']) && $model->where('pl.name', $filter['product_name']);
        !empty($filter['is_recommend']) && $model->where('is_recommend', $filter['is_recommend']);
        !empty($filter['status']) && $model->where('p.status', $filter['status']);

        $total = $model
            ->alias('p')
            ->join('pms_product_library pl','pl.id = p.product_library_id')
            ->join('pms_product_category pc','pc.id = pl.category_id')
            ->join('pms_store s', 's.id = p.store_id')
            ->count();

        return $total;
    }

    public static function getList($column, $filter, $page, $limit, $order)
    {
        $model = new self();
        $model->where('p.status', '<>', 0)
            ->where('pl.status', 1)
            ->where('s.status', 1);

        !empty($filter['search']) && $model->where('pc.name|pl.name|desc|sp|pl.remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['is_expect_date']) && $model->where('is_expect_date', $filter['is_expect_date']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['product_name']) && $model->where('pl.name', $filter['product_name']);
        !empty($filter['is_recommend']) && $model->where('is_recommend', $filter['is_recommend']);
        !empty($filter['status']) && $model->where('p.status', $filter['status']);

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('store_id, sort desc, p.status');
        }

        $list = $model
            ->alias('p')
            ->join('pms_product_library pl','pl.id = p.product_library_id')
            ->join('pms_product_category pc','pc.id = pl.category_id')
            ->join('pms_store s', 's.id = p.store_id')
            ->field('
                p.*,
                all_inventory,
                pl.category_id,pl.name,pl.cover,pl.weight,pl.sp,pl.desc,pl.original_price,pl.floor_price,
                pc.name as category_name,
                s.name as store_name
            ')
            ->distinct(true)
            ->page($page, $limit)
            ->select();

        return $list;
    }

    public static function getSyncTotal($filter=null)
    {
        $model = new self();
        $model->where('p.status', '<>', 0)
            ->where('pl.status', 1);

        !empty($filter['search']) && $model->where('pc.name|pl.name|desc|sp|pl.remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['is_expect_date']) && $model->where('is_expect_date', $filter['is_expect_date']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['is_recommend']) && $model->where('is_recommend', $filter['is_recommend']);
        !empty($filter['status']) && $model->where('p.status', $filter['status']);

        $total = $model
            ->alias('p')
            ->join('pms_product_library pl','pl.id = p.product_library_id')
            ->join('pms_product_category pc','pc.id = pl.category_id')
            ->count();

        return $total;
    }

    public static function getSyncList($column, $filter, $page, $limit, $order)
    {
        $model = new self();
        $model->where('p.status', '<>', 0)
            ->where('pl.status', 1);

        !empty($filter['search']) && $model->where('pc.name|pl.name|desc|sp|pl.remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['is_expect_date']) && $model->where('is_expect_date', $filter['is_expect_date']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['is_recommend']) && $model->where('is_recommend', $filter['is_recommend']);
        !empty($filter['status']) && $model->where('p.status', $filter['status']);

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('sort desc, category_id, p.status');
        }

        $list = $model
            ->alias('p')
            ->join('pms_product_library pl','pl.id = p.product_library_id')
            ->join('pms_product_category pc','pc.id = pl.category_id')
            ->field('
                p.*,
                all_inventory,
                pl.category_id,pl.name,pl.cover,pl.weight,pl.sp,pl.desc,pl.original_price,pl.floor_price,
                pc.name as category_name
            ')
            ->distinct(true)
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
