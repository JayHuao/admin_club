<?php

namespace app\admin\controller\auth;

use app\common\controller\AdminBase;
use app\common\model\AuthRule;
use think\Cache;
use think\Config;
use think\Db;
use think\Hook;

class Rule extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        $parent = Db::name('auth_rule')
            ->where('status', 1)
            ->where('pid', 0)
            ->field('id,title')
            ->select();
        return $this->fetch('auth/rule', [
            'parent' => $parent
        ]);
    }

    public function get_dt_list()
    {
        $infos = AuthRule::getTreeList();

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    public function add()
    {
        $params = $this->request->param();
        $rule = new AuthRule();
        $rule->data($params);
        $rule->allowField(true)->save();

        Cache::rm('__rule__');

        return ['code'=>1,'msg'=>'添加权限成功'];
    }

    public function edit()
    {
        $id = $this->request->param('id');
        $params = $this->request->param();
        $rule = new AuthRule();
        $rule->allowField(true)->save($params, ['id' => $id]);

        Cache::rm('__rule__');

        return ['code'=>1,'msg'=>'编辑权限成功'];
    }

    public function delete()
    {
        Db::name('auth_rule')->where('id', $this->request->param('id'))->update([
            'status' => Config::get('status_n')
        ]);

        return ['code'=>1,'msg'=>'删除权限成功'];
    }
}
