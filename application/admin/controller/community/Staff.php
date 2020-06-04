<?php

namespace app\admin\controller\community;

use app\common\controller\AdminBase;
use think\Config;
use think\Db;
use think\Hook;

class Staff extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        return $this->fetch('community/staff');
    }

    public function get_dt_list()
    {
        $data = Db::name('staff')
            ->where('status', '<>', 0)
            ->select();
        echo json_encode([
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    public function add()
    {
        $params = $this->request->param();
        \app\admin\model\Staff::create($params, true);

        return ['code'=>1,'msg'=>'添加职工成功'];
    }

    public function edit()
    {
        $params = $this->request->post();
        \app\admin\model\Staff::update($params);

        return ['code'=>1,'msg'=>'编辑职工成功'];
    }

    public function delete()
    {
        $id = $this->request->param('id');
        Db::name('staff')
            ->where('id', $id)
            ->update([
                'status' => 0
            ]);

        return ['code'=>1,'msg'=>'删除职工成功'];
    }
}
