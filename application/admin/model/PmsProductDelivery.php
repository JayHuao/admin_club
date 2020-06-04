<?php

namespace app\admin\model;

use think\Model;

class PmsProductDelivery extends Model
{
    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('pc.name|pc.name|pl.name|desc|sp|remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['product_name']) && $model->where('pl.name', $filter['product_name']);

        $total = $model
            ->alias('pd')
            ->join('pms_product_library pl','pl.id = pd.product_library_id')
            ->join('pms_product_category pc', 'pc.id = pl.category_id')
            ->join('pms_delivery d','d.id = pd.delivery_id')
            ->count();

        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('pc.name|pl.name|desc|sp|remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['delivery_id']) && $model->where('delivery_id', $filter['delivery_id']);

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 1: $model->order('pl.name ' . $dir); break;
                case 2: $model->order('price ' . $dir); break;
                case 3: $model->order('d.name ' . $dir); break;
                case 4: $model->order('carriage ' . $dir); break;
            }
        } else {
            $model->order('pl.name');
        }

        $list = $model
            ->alias('pd')
            ->join('pms_product_library pl','pl.id = pd.product_library_id')
            ->join('pms_product_category pc', 'pc.id = pl.category_id')
            ->join('pms_delivery d','d.id = pd.delivery_id')
            ->field('
                pd.*,
                pl.name as product_name,cover,
                pc.name as category_name,
                d.name as delivery_name
            ')
            ->page($page, $limit)
            ->select();

        foreach ($list as $key => $value) {
            $value['carriage'] == '-1' ? $list[$key]['mode'] = 1 : $list[$key]['mode'] = 2;
        }

        return $list;
    }

    /**
     * 获取商品配送方式
     * @param integer $productId
     */
    public static function getProductDelivery($productId)
    {
        $res = self::get(['product_id', $productId]);
        return $res->delivery->delivery_name;
    }
}
