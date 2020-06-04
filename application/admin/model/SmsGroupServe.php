<?php

namespace app\admin\model;

use think\Model;

class SmsGroupServe extends Model
{
    public static function getList()
    {
        $data = self::where('status', 1)
            ->field('title,content')
            ->order('sort desc')
            ->select();
        return $data;
    }
}
