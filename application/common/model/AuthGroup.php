<?php

namespace app\common\model;

use mirse\Tree;
use think\Config;
use think\Db;
use think\Model;

class AuthGroup extends Model
{

    public static function getGroupData()
    {
        $groupList = collection(self::where('status', '<>', Config::get('status_n'))->select())->toArray();

        foreach ($groupList as $key => $value)
        {
            $cond_rule['id'] = ['in', $value['rules']];
            $rule_1st = Db::name('auth_rule')
                ->where('id', 'in', $value['rules'])
                ->where('pid', '0')
                ->field('id,title')
                ->select();
            $rule_1st_ids = array_column($rule_1st, 'id');
            $rule_1st_titles = array_column($rule_1st, 'title');

            $rule_2nd = Db::name('auth_rule')
                ->where('id', 'in', $value['rules'])
                ->where('pid', 'in', $rule_1st_ids)
                ->field('id,title')
                ->select();
            $rule_2nd_titles = array_column($rule_2nd, 'title');

            // $groupList[$key]['rules_text'] = implode('；', array_merge($rule_1st_titles, $rule_2nd_titles));
            $groupList[$key]['rules_text'] = implode('；', $rule_2nd_titles);
            $groupList[$key]['rules_arr'] = AuthRule::getColumn('id', $cond_rule);
        }

        return $groupList;
    }


}
