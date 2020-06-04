<?php

namespace app\admin\model;

use think\Model;

class SmsRecommendArticle extends Model
{
    public static function getTotal($filter=null)
    {
        $model = new self();
        $model->where('r.status', '<>', 0);

        !empty($filter['search']) && $model->where('rc.category_name|pl.name', 'like', '%' . $filter['search'] . '%');
        !empty($filter['recommend_category_id']) && $model->where('recommend_category_id', $filter['recommend_category_id']);
        !empty($filter['type']) && $model->where('rc.type', $filter['type']);

        $total = $model
            ->alias('r')
            ->join('cms_article a','a.id = r.article_id')
            ->join('sms_recommend_category rc','rc.id = r.recommend_category_id')
            ->count();

        return $total;
    }

    public static function getList($column, $filter, $page, $limit, $order)
    {
        $model = new self();
        $model->where('r.status', '<>', 0);

        !empty($filter['search']) && $model->where('rc.category_name|title', 'like', '%' . $filter['search'] . '%');
        !empty($filter['recommend_category_id']) && $model->where('recommend_category_id', $filter['recommend_category_id']);
        !empty($filter['type']) && $model->where('rc.type', $filter['type']);

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('recommend_category_id');
        }

        $list = $model
            ->alias('r')
            ->join('cms_article a','a.id = r.article_id')
            ->join('sms_recommend_category rc','rc.id = r.recommend_category_id')
            ->field('r.*,category_name,a.cover as article_cover,a.title as article_title')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
