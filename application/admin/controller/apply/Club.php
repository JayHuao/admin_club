<?php

namespace app\admin\controller\apply;

use app\admin\model\ClubApply;
use app\common\controller\AdminBase;
use think\Config;
use think\Db;
use think\Hook;
use think\Session;

class Club extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        $club = Db::name('club')
            ->where('status', 1)
            ->field('id,name')
            ->select();
        return $this->fetch('apply/club', [
            'club' => $club
        ]);
    }

    public function get_dt_list()
    {
        $data = Db::name('club_apply')
            ->alias('ca')
            ->join('club c','c.id = ca.club_id')
            ->join('admin a', 'a.id = ca.admin_id')
            ->field('ca.*,c.name as club,a.nickname as admin')
            ->where('ca.status', '<>', 0)
            ->where('admin_id', Session::get(Config::get('user_auth_key'))['id'])
            ->select();
        echo json_encode([
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    public function add()
    {
        $params = $this->request->post();
        ClubApply::create($params, true);

        return ['code'=>1,'msg'=>'添加申请成功'];
    }

    public function edit()
    {
        $params = $this->request->post();
        ClubApply::update($params);

        return ['code'=>1,'msg'=>'编辑申请成功'];
    }

    public function delete()
    {
        $id = $this->request->param('id');
        Db::name('club_apply')
            ->where('id', $id)
            ->update([
                'status' => 0
            ]);

        return ['code'=>1,'msg'=>'删除申请成功'];
    }
}
