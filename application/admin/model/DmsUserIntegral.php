<?php

namespace app\admin\model;

use think\Model;

class DmsUserIntegral extends Model
{
    protected $createTime = 'win_at';

    /**
     * 根据类型计算用户积分
     * @param integer $userId
     * @param integer $type 1=>自购返现；2=>销售返现；3=>分销返现；
     */
    public static function calcIntegral($userId,$type)
    {
        $total = self::where('user_id', $userId)
            ->where('type', $type)
            ->sum('commission_integral');
        return number_format(round($total,2),2);
    }
}
