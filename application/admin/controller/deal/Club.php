<?php

namespace app\admin\controller\deal;

use app\common\controller\AdminBase;
use think\Db;
use think\Hook;

class Club extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        return $this->fetch('deal/club');
    }

    public function get_dt_list()
    {
        $data = Db::name('club_apply')
            ->alias('ca')
            ->join('club c','c.id = ca.club_id')
            ->join('admin a', 'a.id = ca.admin_id')
            ->field('ca.*,c.name as club,a.nickname as admin')
            ->where('ca.status', 1)
            ->select();
        echo json_encode([
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    public function pass()
    {
        $id = $this->request->param('id');
        Db::name('club_apply')
            ->where('id', $id)
            ->update([
                'status'    => 2,
                'deal_time' => date('Y-m-d H:i:s')
            ]);

        return ['code'=>1,'msg'=>'通过申请成功'];
    }

    public function ban()
    {
        $id = $this->request->param('id');
        Db::name('club_apply')
            ->where('id', $id)
            ->update([
                'status'    => 3,
                'remark' => $this->request->param('remark'),
                'deal_time' => date('Y-m-d H:i:s')
            ]);

        return ['code'=>1,'msg'=>'驳回申请成功'];
    }
}
