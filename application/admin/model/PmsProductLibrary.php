<?php

namespace app\admin\model;

use think\Model;

class PmsProductLibrary extends Model
{
    public function imgs()
    {
        return $this->hasMany('PmsProductImgs', 'product_id');
    }

    public function seo()
    {
        return $this->hasMany('SmsProductSeo', 'product_library_id');
    }


    public static function getTotal($filter=null)
    {
        $model = new self();

        $model->where('pl.status', '<>', 0);

        !empty($filter['search']) && $model->where('pc.name|pl.name|desc|sp|remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['product_name']) && $model->where('pl.name', $filter['product_name']);
        !empty($filter['status']) && $model->where('pl.status', $filter['status']);
        !empty($filter['perfect_detail']) && $model->where('detail', '');
        if ($filter['perfect_img']) {
            $model->where('pi.id', 'null');
        }
        switch ($filter['t']) {
            case 'inventory':
                $model->where('all_inventory', '<=', 10);
                break;
        }

        $list = $model
            ->alias('pl')
            ->join('pms_product_category pc','pc.id = pl.category_id')
            ->join('pms_product_imgs pi', 'pi.product_library_id = pl.id','LEFT')
            ->distinct(true)
            ->field('pl.id')
            ->select();
        $total = count($list);
        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();

        $model->where('pl.status', '<>', 0);

        !empty($filter['search']) && $model->where('pc.name|pl.name|desc|sp|remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['product_name']) && $model->where('pl.name', $filter['product_name']);
        !empty($filter['status']) && $model->where('pl.status', $filter['status']);
        !empty($filter['perfect_detail']) && $model->where('detail', '');
        if ($filter['perfect_img']) {
            $model->where('pi.id', 'null');
        }
        switch ($filter['t']) {
            case 'inventory':
                $model->where('all_inventory', '<=', 10);
                break;
        }

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 0: $model->order('cover ' . $dir); break;
                case 1: $model->order('pc.name ' . $dir); break;
                case 2: $model->order('name ' . $dir); break;
                case 3: $model->order('all_inventory ' . $dir); break;
                case 4: $model->order('original_price ' . $dir); break;
                case 5: $model->order('pl.status ' . $dir); break;
            }
        } else {
            $model->order('pl.id desc, pl.status');
        }

        $list = $model
            ->alias('pl')
            ->join('pms_product_category pc','pc.id = pl.category_id')
            ->join('pms_product_imgs pi', 'pi.product_library_id = pl.id','LEFT')
            ->field('pl.*,pc.name as category_name')
            ->distinct(true)
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
