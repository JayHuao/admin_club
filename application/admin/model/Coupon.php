<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Coupon extends Model
{
    public function category()
    {
        return $this->hasOne('SmsCouponProductCategoryRelation');
    }

    //////////////////////////////////////////////////////////////////////

    public static function getTotal($filter=null)
    {
        $model = new self();
        $model->where('status', '<>', 0);

        !empty($filter['search']) && $model->where('title|full|reduce', 'like', '%' . $filter['search'] . '%');
        !empty($filter['type']) && $model->where('type', $filter['type']);
        !empty($filter['use_type']) && $model->where('use_type', $filter['use_type']);

        $total = $model
            ->count();

        return $total;
    }

    public static function getList($column, $filter, $page, $limit, $order)
    {
        $model = new self();
        $model->where('status', '<>', 0);

        !empty($filter['search']) && $model->where('title|full|reduce', 'like', '%' . $filter['search'] . '%');
        !empty($filter['type']) && $model->where('type', $filter['type']);
        !empty($filter['use_type']) && $model->where('use_type', $filter['use_type']);

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('use_type, status');
        }

        $list = $model
            ->page($page, $limit)
            ->select();

        foreach ($list as $key => $value) {
            $useType = $value['use_type'];
            if ($useType == 2) {
                // 指定分类可用
                $list[$key]['product_category_id'] = $value->category->product_category_id;
                $list[$key]['product_category_name'] = $value->category->product_category_name;
            }
            if ($useType == 3) {
                $productLibraryIds = Db::name('sms_coupon_product_relation')->where('coupon_id', $value['id'])->column('product_library_id');
                array_walk($productLibraryIds,function (&$value,$key) {
                    $value = (string)$value;
                });
                $products = Db::name('sms_coupon_product_relation')->where('coupon_id', $value['id'])->column('product_name');
                $list[$key]['product_library_ids'] = $productLibraryIds;
                $list[$key]['products'] = implode('<br>',$products);
            }
        }

        return $list;
    }
}
