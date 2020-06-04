<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Db;
use think\Hook;

class Dashboard extends AdminBase
{
    public function index()
    {
        Hook::exec('app\\admin\\behavior\\Menu');
        return $this->fetch('index');
    }
}
