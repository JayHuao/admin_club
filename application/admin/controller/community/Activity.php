<?php

namespace app\admin\controller\community;

use app\common\controller\AdminBase;
use think\Db;
use think\Hook;

class Activity extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        $club = Db::name('club')
            ->where('status', 1)
            ->field('id,name')
            ->select();
        return $this->fetch('community/activity', [
            'club' => $club
        ]);
    }

    public function get_dt_list()
    {
        $data = Db::name('activity')
            ->alias('a')
            ->join('club c','c.id = a.club_id')
            ->field('a.*,c.name as club')
            ->where('a.status', '<>', 0)
            ->select();
        echo json_encode([
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    public function add()
    {
        $params = $this->request->param();
        \app\admin\model\Activity::create($params, true);

        return ['code'=>1,'msg'=>'添加活动成功'];
    }

    public function edit()
    {
        $params = $this->request->post();
        \app\admin\model\Activity::update($params);

        return ['code'=>1,'msg'=>'编辑活动成功'];
    }

    public function delete()
    {
        $id = $this->request->param('id');
        Db::name('activity')
            ->where('id', $id)
            ->update([
                'status' => 0
            ]);

        return ['code'=>1,'msg'=>'删除活动成功'];
    }
}
