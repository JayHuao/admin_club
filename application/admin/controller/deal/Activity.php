<?php

namespace app\admin\controller\deal;

use app\common\controller\AdminBase;
use think\Db;
use think\Hook;

class Activity extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        return $this->fetch('deal/activity');
    }

    public function get_dt_list()
    {
        $data = Db::name('activity_apply')
            ->alias('aa')
            ->join('activity a','a.id = aa.activity_id')
            ->join('admin an', 'an.id = aa.admin_id')
            ->field('aa.*,a.title as activity,an.nickname as admin')
            ->where('aa.status', 1)
            ->select();
        echo json_encode([
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    public function pass()
    {
        $id = $this->request->param('id');
        Db::name('activity_apply')
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
        Db::name('activity_apply')
            ->where('id', $id)
            ->update([
                'status'    => 3,
                'remark' => $this->request->param('remark'),
                'deal_time' => date('Y-m-d H:i:s')
            ]);

        return ['code'=>1,'msg'=>'驳回申请成功'];
    }
}
