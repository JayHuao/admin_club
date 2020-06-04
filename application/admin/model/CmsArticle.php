<?php

namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;

class CmsArticle extends Model
{
    protected $createTime = 'publish_time';

    protected $insert = ['publisher'];

    public function setPublisherAttr()
    {
        return Session::get(Config::get('user_auth_key'))['id'];
    }

    public static function getTotal($filter=null)
    {
        $model = new self();
        $model->where('a.status', '<>', 0);

        !empty($filter['search']) && $model->where('category_name|title|desc|nickname', 'like', '%' . $filter['search'] . '%');
        !empty($filter['title']) && $model->where('title', $filter['title']);
        !empty($filter['nickname']) && $model->where('nickname', $filter['nickname']);
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['search_date']) && $model->whereTime('publish_time', 'between', [$filter['search_date'], strtotime($filter['search_date'].' +1 days')]);

        $total = $model
            ->alias('a')
            ->join('cms_article_category ac','ac.id = a.category_id')
            ->count();
        return $total;
    }

    public static function getList($filter=null, $page=null, $limit=null, $columns=null, $order=null)
    {
        $model = new self();
        $model->where('a.status', '<>', 0);

        !empty($filter['search']) && $model->where('category_name|title|desc|nickname', 'like', '%' . $filter['search'] . '%');
        !empty($filter['title']) && $model->where('title', $filter['title']);
        !empty($filter['nickname']) && $model->where('nickname', $filter['nickname']);
        !empty($filter['category_id']) && $model->where('category_id', $filter['category_id']);
        !empty($filter['search_date']) && $model->whereTime('publish_time', 'between', [$filter['search_date'], strtotime($filter['search_date'].' +1 days')]);

        if (isset($order)) {
            $model->order($columns[$order[0]['column']] . ' ' . $order[0]['dir']);
        }

        $list = $model
            ->alias('a')
            ->join('cms_article_category ac','ac.id = a.category_id')
            ->join('admin admin','admin.id = a.publisher')
            ->field('a.*,category_name,nickname')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
