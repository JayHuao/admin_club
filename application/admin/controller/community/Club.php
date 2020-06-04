<?php

namespace app\admin\controller\community;

use app\common\controller\AdminBase;
use think\Db;
use think\Hook;

class Club extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        return $this->fetch('community/club');
    }

    public function get_dt_list()
    {
        $data = Db::name('club')
            ->where('status', '<>', 0)
            ->select();
        echo json_encode([
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    public function add()
    {
        $params = $this->request->param();
        \app\admin\model\Club::create($params, true);

        return ['code'=>1,'msg'=>'添加社团成功'];
    }

    public function edit()
    {
        $params = $this->request->post();
        \app\admin\model\Club::update($params);

        return ['code'=>1,'msg'=>'编辑社团成功'];
    }

    public function delete()
    {
        $id = $this->request->param('id');
        Db::name('club')
            ->where('id', $id)
            ->update([
                'status' => 0
            ]);

        return ['code'=>1,'msg'=>'删除社团成功'];
    }
}
