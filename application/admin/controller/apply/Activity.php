<?php

namespace app\admin\controller\apply;

use app\admin\model\ActivityApply;
use app\common\controller\AdminBase;
use think\Config;
use think\Db;
use think\Hook;
use think\Session;

class Activity extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        $activity = Db::name('activity')
            ->where('status', 1)
            ->field('id,title')
            ->select();
        return $this->fetch('apply/activity', [
            'activity' => $activity
        ]);
    }

    public function get_dt_list()
    {
        $data = Db::name('activity_apply')
            ->alias('aa')
            ->join('activity a','a.id = aa.activity_id')
            ->join('admin an', 'an.id = aa.admin_id')
            ->field('aa.*,a.title as activity,an.nickname as admin')
            ->where('aa.status', '<>', 0)
            ->where('admin_id', Session::get(Config::get('user_auth_key'))['id'])
            ->select();
        echo json_encode([
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    public function add()
    {
        $params = $this->request->post();
        ActivityApply::create($params, true);

        return ['code'=>1,'msg'=>'添加申请成功'];
    }

    public function edit()
    {
        $params = $this->request->post();
        ActivityApply::update($params);

        return ['code'=>1,'msg'=>'编辑申请成功'];
    }

    public function delete()
    {
        $id = $this->request->param('id');
        Db::name('activity_apply')
            ->where('id', $id)
            ->update([
                'status' => 0
            ]);

        return ['code'=>1,'msg'=>'删除申请成功'];
    }
}
