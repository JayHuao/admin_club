<?php

namespace app\common\model;

use mirse\Tree;
use think\Config;
use think\Model;

class AuthRule extends Model
{

    public static function getTreeData()
    {
        $ruleList = collection(self::where('status', '<>', Config::get('status_n'))->select())->toArray();
        Tree::instance()->init($ruleList);
        $ruleList = Tree::instance()->getTreeArray(0);
        return $ruleList;
    }


    public static function getTreeList()
    {
        $ruleList = collection(self::where('status', '<>', Config::get('status_n'))->select())->toArray();

        Tree::instance()->init($ruleList);
        $ruleList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
        return $ruleList;
    }

    public static function getColumn($field, $cond=[])
    {
        $cond['status'] = Config::get('status_y');
        $data = self::where($cond)->column($field);
        return $data;
    }

    public static function getValue($field, $cond=[])
    {
        $cond['status'] = Config::get('status_y');
        $data = self::where($cond)->value($field);
        return $data;
    }
}
