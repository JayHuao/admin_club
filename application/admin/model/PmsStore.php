<?php

namespace app\admin\model;

use think\Model;

class PmsStore extends Model
{
    public static function getTotal($filter=null)
    {
        $model = new self();
        $model->where('status', '<>', 0);

        !empty($filter['search']) && $model->where('head|phone|name|address|remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['type']) && $model->where('type', $filter['type']);
        !empty($filter['head']) && $model->where('head', $filter['head']);
        !empty($filter['phone']) && $model->where('phone', $filter['phone']);
        !empty($filter['name']) && $model->where('name', $filter['name']);

        $total = $model
            ->count();

        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();
        $model->where('status', '<>', 0);

        !empty($filter['search']) && $model->where('head|phone|name|address|remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['type']) && $model->where('type', $filter['type']);
        !empty($filter['head']) && $model->where('head', $filter['head']);
        !empty($filter['phone']) && $model->where('phone', $filter['phone']);
        !empty($filter['name']) && $model->where('name', $filter['name']);

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 0: $model->order('type ' . $dir); break;
                case 1: $model->order('head ' . $dir); break;
                case 2: $model->order('phone ' . $dir); break;
                case 3: $model->order('name ' . $dir); break;
                case 4: $model->order('address ' . $dir); break;
                case 5: $model->order('remark ' . $dir); break;
                case 6: $model->order('status ' . $dir); break;
            }
        } else {
            $model->order('status');
        }

        $list = $model
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
