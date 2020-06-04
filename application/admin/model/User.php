<?php

namespace app\admin\model;

use mirse\Tree;
use think\Model;

class User extends Model
{
    public static function getDistributeTree()
    {
        $users = self::where('status', 1)
            ->where('role', '<>',0)
            ->select();
        foreach ($users as $key => $user) {
            $userId = $user['id'];
            $user['role'] == 3 ? $saleAmount = DmsUserIntegral::calcIntegral($userId,2) : $saleAmount = DmsUserCommission::calcCommission($userId, 2);
            $users[$key]['sale_amount'] = $saleAmount;
            $users[$key]['self_amount'] = DmsUserCommission::calcCommission($userId, 1);
            $users[$key]['distribute_amount'] = DmsUserCommission::calcCommission($userId, 3);
        }
        Tree::instance()->init($users);
        $userList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0),'no');
        return $userList;
    }

    public static function getTotal($filter=null)
    {
        $model = new self();
        $model->where('status', 1);

        !empty($filter['search']) && $model->where('nickname|no|phone', 'like', '%' . $filter['search'] . '%');
        !empty($filter['nickname']) && $model->where('nickname', $filter['nickname']);
        !empty($filter['register_date']) && $model->where('create_time', 'between', [
            strtotime($filter['register_date']. ' 00:00:01'),
            strtotime($filter['register_date']. ' 23:59:59')
        ]);

        switch ($filter['t']) {
            case 'today':
                $model->whereTime('create_time', 'today');
                break;
        }

        $total = $model
            ->count();

        return $total;
    }

    public static function getList($column, $filter, $page, $limit, $order)
    {
        $model = new self();
        $model->where('status', 1);

        !empty($filter['search']) && $model->where('nickname|no|phone', 'like', '%' . $filter['search'] . '%');
        !empty($filter['phone']) && $model->where('phone', 'like', '%' . $filter['phone'] . '%');
        !empty($filter['register_date']) && $model->whereTime('create_time', 'between', [
            strtotime($filter['register_date']),
            strtotime('+1 day', strtotime($filter['register_date']))
        ]);

        switch ($filter['t']) {
            case 'today':
                $model->whereTime('create_time', 'today');
                break;
        }

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('create_time desc');
        }

        $list = $model
            ->page($page, $limit)
            ->select();

        return $list;
    }
}
