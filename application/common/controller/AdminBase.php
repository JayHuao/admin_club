<?php

namespace app\common\controller;

use app\admin\library\Auth;
use think\Config;
use think\Controller;
use think\Session;

class AdminBase extends Controller
{

    /**
     * 无需登录的方法,同时也就不需要鉴权
     * @var array
     */
    protected $noNeedLogin = ['login'];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['get_dt_list','upload','delete_img','detail','logout','change_psw'];

    /**
     * 权限控制类
     * @var Auth
     */
    protected $auth = null;

    public function _initialize()
    {
        $path = strtolower($this->request->pathinfo());
        $path = str_replace('._', '/', $path);
        $path = str_replace('.', '/', $path);

        $this->auth = Auth::instance();
        // 设置当前请求的URI
        $this->auth->setRequestUri($path);

        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            //检测是否登录
            if (!$this->auth->isLogin()) {
                $this->error('请先登录...', url('index/login'));
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($path)) {
                     $this->error('您没有权限...');
                }
            }
        }

        $site = Config::get("site");

        //渲染站点配置
        $this->assign('site', $site);
        //渲染权限对象
        $this->assign('auth', $this->auth);
        //渲染管理员对象
        $this->assign('admin', Session::get(Config::get('user_auth_key')));
    }
}
