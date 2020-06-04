<?php

namespace app\admin\controller\auth;

use app\common\controller\AdminBase;
use app\common\model\AuthGroupAccess;
use mirse\Random;
use think\Config;
use think\Db;
use think\Hook;

class Admin extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        $groups = Db::name('auth_group')->where('status', Config::get('status_y'))->select();
        return $this->fetch('auth/admin', [
            'groups' => $groups
        ]);
    }

    public function get_dt_list()
    {
        $infos = \app\common\model\Admin::getUserData();

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    public function add()
    {
        $params = $this->request->post();
        $params['salt'] = Random::alnum();
        $params['password'] = md5(md5(Config::get('default_psw')).$params['salt']);
        \app\admin\model\Admin::create($params, true);

        return ['code'=>1,'msg'=>'添加管理员成功'];
    }

    public function edit()
    {
        $params = $this->request->post();
        \app\admin\model\Admin::update($params);

        return ['code'=>1,'msg'=>'修改管理员成功'];
    }

    public function edit_rule()
    {
        $uid = $this->request->param('id');

        if ($uid) {
            AuthGroupAccess::where('uid', $uid)->delete();
        }

        $groups = $this->request->param('groups/a');
        $list = [];
        foreach ($groups as $key => $value)
        {
            array_push($list, [
                'uid' => $uid,
                'group_id' => $value
            ]);
        }
        $aga = new AuthGroupAccess();
        $aga->saveAll($list);

        return ['code'=>1,'msg'=>'编辑管理员权限成功'];
    }

    public function delete()
    {
        Db::name('admin')->where('id', $this->request->param('id'))->update([
            'status' => Config::get('status_n')
        ]);

        return ['code'=>1,'msg'=>'删除管理成功'];
    }
}
