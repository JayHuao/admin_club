<?php

namespace app\admin\model;

use app\admin\model\SmsPriceAdjustment;
use think\Model;

class Goods extends Model
{
    public function imgs()
    {
        return $this->hasMany('GoodsImg')->field('id,img_url');
    }

    public function types()
    {
        return $this->belongsTo('GoodsType','type_id');
    }

    public static function getTotal($filter=null)
    {
        $model = new self();
        $model->where('status', '<>', 0);
        !empty($filter['search']) && $model->where('goods_name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['goods_name']) && $model->where('goods_name', $filter['goods_name']);
        !empty($filter['type_id']) && $model->where('type_id', $filter['type_id']);
        !empty($filter['is_recommend']) && $model->where('is_recommend', $filter['is_recommend']);
        !empty($filter['is_direct']) && $model->where('is_direct', $filter['is_direct']);
        !empty($filter['status']) && $model->where('status', $filter['status']);
        !empty($filter['perfect_detail']) && $model->where('detail', '=', '');
        !empty($filter['create_time']) && $model->whereTime('create_time', 'between', [$filter['create_time'], strtotime($filter['create_time'].' +1 days')]);

        $total = $model->count();
        return $total;
    }

    public static function getList($filter=null, $page=null, $limit=null, $columns=null, $order=null)
    {
        $model = new self();
        $model->where('status', '<>', 0);

        !empty($filter['search']) && $model->where('goods_name|desc', 'like', '%' . $filter['search'] . '%');
        !empty($filter['goods_name']) && $model->where('goods_name', $filter['goods_name']);
        !empty($filter['type_id']) && $model->where('type_id', $filter['type_id']);
        !empty($filter['is_recommend']) && $model->where('is_recommend', $filter['is_recommend']);
        !empty($filter['is_direct']) && $model->where('is_direct', $filter['is_direct']);
        !empty($filter['status']) && $model->where('status', $filter['status']);
        !empty($filter['perfect_detail']) && $model->where('detail', '=', '');
        !empty($filter['create_time']) && $model->whereTime('create_time', 'between', [$filter['create_time'], strtotime($filter['create_time'].' +1 days')]);

        if (!empty($filter['perfect_img'])) {
            $goodsData = self::withCount(['imgs'=>function($query){
                $query->where('status',1);
            }])->select();
            foreach ($goodsData as $goods) {
                Goods::update(['imgs_count' =>$goods->imgs_count],[
                    'id' => $goods->id
                ]);
            }
            $model->where('imgs_count', '0');
        }

        if (isset($order)) {
            $model->order($columns[$order[0]['column']] . ' ' . $order[0]['dir']);
        } else {
            $model->order('is_recommend desc,status,sort desc,create_time desc');
        }

        $list = $model
            ->page($page, $limit)
            ->select();
        foreach ($list as $key => $value) {
            $list[$key]['type_name'] = $value->types->type_name;
        }

        return $list;
    }

    /**
     * 编辑商品信息
     * @param array $fields
     */
    public static function edit($fields)
    {
        if (isset($fields['seckill_price'])) {
            $goodsId = $fields['id'];
            $goods = self::get($goodsId);
            $originPrice = $goods->seckill_price;
            $currentPrice = $fields['seckill_price'];
            if ($currentPrice != $originPrice) {
                $currentPrice > $originPrice ? $type = 1 : $type =2;
                SmsPriceAdjustment::create([
                    'goods_id'       => $goodsId,
                    'original_price' => $originPrice,
                    'current_price'  => $currentPrice,
                    'type'           => $type
                ]);
            }
        }
        self::update($fields);
    }
}
