<?php

namespace app\common\model;

use mirse\Tree;
use think\Config;
use think\Model;

class AdminNav extends Model
{

    protected $auto = [];
    protected $insert = [];
    protected $update = [];

    public static function getTreeList()
    {
        $navList = collection(self::where('status', '<>', Config::get('status_n'))->order('sort desc,id')->select())->toArray();

        Tree::instance()->init($navList);
        $navList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'nav_name');
        return $navList;
    }
}
