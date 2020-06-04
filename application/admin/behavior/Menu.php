<?php

namespace app\admin\behavior;

use app\admin\library\Auth;
use think\Controller;

class Menu extends Controller
{
    public function run()
    {
        list($menulist, $fixedmenu, $referermenu) = Auth::instance()->getSidebar();
        $this->assign('menulist', $menulist);
        $this->assign('fixedmenu', $fixedmenu);
        $this->assign('referermenu', $referermenu);
    }
}