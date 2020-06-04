<?php

namespace app\admin\model;

use mirse\Tree;
use think\Model;

class PmsProductCategory extends Model
{
    public static function getList()
    {
        $data = self::where('status', '<>', 0)->order('status, is_index desc')->select();
        Tree::instance()->init($data);
        $treeData = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
        return $treeData;
    }
}
