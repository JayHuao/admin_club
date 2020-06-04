<?php

namespace app\admin\model;

use think\Model;

class CmsArticleDetail extends Model
{
    protected $insert = ['status'=>1];

    public function attachment()
    {
        return $this->hasMany('CmsArticleDetailAttachment', 'article_detail_id');
    }
}
