<?php

namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;

class ActivityApply extends Model
{
    protected $insert = ['status' => 1];
    protected $auto = ['admin_id', 'apply_time'];

    public function setAdminIdAttr()
    {
        return Session::get(Config::get('user_auth_key'))['id'];
    }

    public function setApplyTimeAttr()
    {
        return date('Y-m-d H:i:s');
    }
}
