<?php

namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;

class SmsPriceAdjustment extends Model
{
    protected $createTime = 'log_time';
    protected $insert = ['operator'];

    protected function getAdjustmentAttr($value,$data)
    {
        return abs(bcsub($data['original_price'], $data['current_price'], 2));
    }

    protected function setOperatorAttr()
    {
        return Session::get(Config::get('user_auth_key'))['id'];
    }

    public function admin()
    {
        return $this->belongsTo('Admin','operator');
    }

    ////////////////////////////////////////////////////////////////////

    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('pc.name|pl.name|s.name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['type']) && $model->where('pa.type', $filter['type']);
        !empty($filter['log_time']) && $model->whereTime('log_time', $filter['log_time']);

        $total = $model
            ->alias('pa')
            ->join('pms_products p','p.id = pa.product_id')
            ->join('pms_product_library pl','pl.id = p.product_library_id')
            ->join('pms_store s', 's.id = p.store_id')
            ->count();

        return $total;
    }

    public static function getList($filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('pc.name|pl.name|s.name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['type']) && $model->where('pa.type', $filter['type']);
        !empty($filter['log_time']) && $model->whereTime('log_time', $filter['log_time']);

        if (isset($order)) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            switch ($column) {
                case 1: $model->order('s.name ' . $dir); break;
                case 2: $model->order('pl.name ' . $dir); break;
                case 3: $model->order('pa.type ' . $dir); break;
                case 4: $model->order('original_price ' . $dir); break;
                case 5: $model->order('current_price ' . $dir); break;
                case 6: $model->order('nickname ' . $dir); break;
                case 7: $model->order('log_time ' . $dir); break;
            }
        } else {
            $model->order('log_time desc');
        }

        $list = $model
            ->alias('pa')
            ->join('pms_products p','p.id = pa.product_id')
            ->join('pms_product_library pl','pl.id = p.product_library_id')
            ->join('pms_store s', 's.id = p.store_id')
            ->join('admin a','a.id = pa.operator')
            ->field('
                pa.*,
                a.nickname,
                pl.cover,
                pl.name as product_name,
                s.name as store_name
            ')
            ->page($page, $limit)
            ->select();

        foreach ($list as $key => $value) {
            $list[$key]['adjustment'] = $value->adjustment;
        }

        return $list;
    }
}
