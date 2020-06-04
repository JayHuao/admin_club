<?php

namespace app\admin\model;

use think\Model;

class Cog extends Model
{
    /**
     * 获取参数
     * @param string $name
     */
    public static function getCog($name)
    {
        return json_decode(self::where('name', $name)->value('value'),true);
    }
}
