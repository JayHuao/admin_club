<?php

namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;

class UserWithdraw extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $createTime = 'withdraw_time';
    protected $insert = ['status' => 1];
    protected $update = ['operator','operate_time'];
    protected $type = [
        'operate_time' => 'timestamp'
    ];

    public function setOperatorAttr()
    {
        return Session::get(Config::get('user_auth_key'))['nickname'];
    }

    public function setOperateTimeAttr()
    {
        return time();
    }


    ///////////////////////////////////////////////////////////////////////////////


    public static function getTotal($filter=null)
    {
        $model = new self();
        $model->where('up.position', 1);

        !empty($filter['search']) && $model->where('nickname|s.name|amount', 'like', '%' . $filter['search'] . '%');
        !empty($filter['status']) && $model->where('uw.status', $filter['status']);
        !empty($filter['withdraw_time']) && $model->whereTime('withdraw_time', 'between', [
            strtotime($filter['withdraw_time']),
            strtotime('+1 day', strtotime($filter['withdraw_time']))
        ]);

        $total = $model
            ->alias('uw')
            ->join('user u','u.id = uw.user_id')
            ->join('user_position up','up.user_id = u.id')
            ->join('pms_store s','s.id = up.store_id')
            ->count();

        return $total;
    }

    public static function getList($column, $filter, $page, $limit, $order)
    {
        $model = new self();
        $model->where('up.position', 1);

        !empty($filter['search']) && $model->where('nickname|s.name|amount', 'like', '%' . $filter['search'] . '%');
        !empty($filter['status']) && $model->where('uw.status', $filter['status']);
        !empty($filter['withdraw_time']) && $model->whereTime('withdraw_time', 'between', [
            strtotime($filter['withdraw_time']),
            strtotime('+1 day', strtotime($filter['withdraw_time']))
        ]);

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('withdraw_time desc');
        }

        $list = $model
            ->alias('uw')
            ->join('user u','u.id = uw.user_id')
            ->join('user_position up','up.user_id = u.id')
            ->join('pms_store s','s.id = up.store_id')
            ->field('uw.*,u.nickname,s.name as store_name')
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
