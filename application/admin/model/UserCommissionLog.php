<?php

namespace app\admin\model;

use think\Model;

class UserCommissionLog extends Model
{
    protected $createTime = 'log_time';

    public static function createWithdrawData($userWithdrawId,$userId,$amount)
    {
        $res = self::create([
            'type'     => 2,
            'event_id' => $userWithdrawId,
            'user_id'  => $userId,
            'amount'   => $amount,
            'remark'   => '提现'.$amount.'元'
        ]);
        return $res->id;
    }
}
