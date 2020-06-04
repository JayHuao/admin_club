<?php

namespace app\admin\controller\system;

use app\common\controller\AdminBase;
use app\common\model\AdminNav;
use think\Cache;
use think\Config;
use think\Db;
use think\Hook;

class Menu extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        $parent = Db::name('admin_nav')
            ->where('status', 1)
            ->where('pid', 0)
            ->field('id,nav_name')
            ->select();
        return $this->fetch('system/menu',[
            'parent' => $parent
        ]);
    }

    public function get_dt_list()
    {
        $infos = AdminNav::getTreeList();

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    public function add()
    {
        $params = $this->request->param();
        $nav = new AdminNav();
        $nav->data($params);
        $nav->allowField(true)->save();

        Cache::rm('__menu__');

        return ['code'=>1,'msg'=>'添加菜单成功'];
    }

    public function edit()
    {
        $id = $this->request->param('id');
        $params = $this->request->param();
        $nav = new AdminNav();
        $nav->allowField(true)->save($params, ['id'=>$id]);

        Cache::rm('__menu__');

        return ['code'=>1,'msg'=>'编辑菜单成功'];
    }

    public function sort()
    {
        Db::name('admin_nav')->where('id', $this->request->param('id'))->update([
            'sort' => $this->request->param('sort')
        ]);

        Cache::rm('__menu__');

        return ['code'=>1,'msg'=>'菜单排序成功'];
    }

    public function delete()
    {
        Db::name('admin_nav')->where('id', $this->request->param('id'))->update([
            'status' => Config::get('status_n')
        ]);

        return ['code'=>1,'msg'=>'删除菜单成功'];
    }
}
