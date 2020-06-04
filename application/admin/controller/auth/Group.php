<?php

namespace app\admin\controller\auth;

use app\common\controller\AdminBase;
use app\common\model\AuthGroup;
use think\Config;
use think\Db;
use think\Hook;

class Group extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        $rules = Db::name('auth_rule')->where('status', Config::get('status_y'))->field(['id','pid','title','status'=>'open'])->select();
        return $this->fetch('auth/group', [
            'rules' => $rules
        ]);
    }

    public function get_dt_list()
    {
        $infos = AuthGroup::getGroupData();

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    public function add()
    {
        $params = $this->request->param();
        $group = new AuthGroup();
        $group->data($params);
        $group->allowField(true)->save();

        return ['code'=>1,'msg'=>'添加管理组成功'];
    }

    public function edit()
    {
        $id = $this->request->param('id');
        $params = $this->request->param();
        $group = new AuthGroup();
        $group->allowField(true)->save($params, ['id'=>$id]);

        return ['code'=>1,'msg'=>'编辑管理组成功'];
    }

    public function delete()
    {
        Db::name('auth_group')->where('id', $this->request->param('id'))->update([
            'status' => Config::get('status_n')
        ]);

        return ['code'=>1,'msg'=>'删除管理组成功'];
    }
}
