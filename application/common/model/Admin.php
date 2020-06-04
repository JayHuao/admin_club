<?php

namespace app\common\model;

use think\Config;
use think\Db;
use think\Model;

class Admin extends Model
{

    public function getLogintimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function setStatusAttr($value)
    {
        return Config::get('status_y');
    }


    public static function getUserData()
    {
        $userList = collection(self::where('status', '<>', Config::get('status_n'))->order('status')->select())->toArray();

        foreach ($userList as $key => $value)
        {
            $join = [
                ['auth_group ag','ag.id = aga.group_id']
            ];
            $groupInfo = Db::name('auth_group_access')
                ->alias('aga')
                ->join($join)
                ->where('uid',$value['id'])
                ->field('uid,group_id,title')
                ->select();
            $groupsText = '';
            $groupsArr = [];
            foreach ($groupInfo as $k => $v)
            {
                $groupsText .= $v['title'].'；';
                array_push($groupsArr, strval($v['group_id']));
            }

            $userList[$key]['groups_text'] = $groupsText;
            $userList[$key]['groups_arr'] = $groupsArr;
        }

        return $userList;
    }
}
