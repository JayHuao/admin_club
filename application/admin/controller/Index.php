<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Config;
use think\Db;
use think\Session;
use think\Validate;

class Index extends AdminBase
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $this->redirect('index/login');
    }

    public function login()
    {
        $url = $this->request->get('url', 'Dashboard/index');
        if ($this->auth->isLogin()) {
            // $this->redirect($url);
             $this->success("You've logged in, do not login again", $url);
        }

        if ($this->request->isPost()) {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $keeplogin = $this->request->post('keeplogin');
            $token = $this->request->post('__token__');

            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                '__token__' => 'token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                '__token__' => $token,
            ];
            if (Config::get('mirseadmin.login_captcha')) {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [], ['username' =>'用户名', 'password' => '密码', 'captcha' => '验证码']);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }

            $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);
            if ($result === true) {
                $this->success('登录成功', $url, ['url' => $url, 'id' => $this->auth->id, 'username' => $username]);
            } else {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : '用户名或密码错误';
                $this->error($msg, $url, ['token' => $this->request->token()]);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            $this->redirect($url);
        }

        return $this->fetch();
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success('退出成功', 'index/login');
    }

    /**
     * 修改密码
     * @param old_psw 旧密码
     * @param new_psw 新密码
     */
    public function change_psw()
    {
        $oldPsw = $this->request->param('old_psw');
        $newPsw = $this->request->param('new_psw');
        $admin = Session::get(Config::get('user_auth_key'));

        if (md5(md5($oldPsw).$admin['salt']) == $admin['password']) {
            Db::name('admin')->where('id', $admin['id'])->update([
                'password' => md5(md5($newPsw).$admin['salt'])
            ]);

            return ['code'=>1, 'message'=>'修改密码成功'];
        }

        return ['code'=>0, 'message'=>'旧密码错误'];
    }
}
