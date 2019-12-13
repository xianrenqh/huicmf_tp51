<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/7 0007
 * Time: 14:06
 */

namespace app\admin\model;

use think\Db;
use think\Model;
use think\facade\Session;
use think\facade\Cookie;

class Admin extends Model
{
    protected $pk = 'adminid';
    
    // 模型初始化
    protected static function init()
    {
    
    }
    
    public function check_admin($adminname, $password)
    {
        $loginip = getip();
        $res = Db::name('admin')->where('adminname', $adminname)->find();
        if (!$res) {
            Db::name('admin_login_log')->insert(['adminname' => $adminname, 'logintime' => time(), 'loginip' => $loginip, 'address'=>get_address($loginip),'password' => $password, 'loginresult' => 0, 'cause' => '用户名不存在！']);
            $res_data = ['tips' => '用户名密码不正确！', 'status' => 0];
            return json_encode($res_data);
            return false;
        }
        if ($res['status'] == 0) {
            Db::name('admin_login_log')->insert(['adminname' => $adminname, 'logintime' => time(), 'loginip' => $loginip, 'address'=>get_address($loginip),'password' => $password, 'loginresult' => 0, 'cause' => '用户已被锁定！']);
            $res_data = ['tips' => '用户已被锁定！', 'status' => 0];
        }else{
            if (password($password) == $res['password'] ) {
                Session::set('last_loginip',$res['loginip']);
                Session::set('last_logintime',$res['logintime']);
                Db::name('admin')->where('adminid',$res['adminid'])->update(['loginip'=>$loginip,'logintime'=>time()]);
                //写入日志
                Db::name('admin_login_log')->insert(['adminname' => $adminname, 'logintime' => time(), 'loginip' => $loginip, 'address'=>get_address($loginip),'password' => '***', 'loginresult' => 1, 'cause' => '登录成功！']);
                $getrolename = Db::name('admin_role')->field('roleid,rolename')->where(['roleid'=>$res['roleid']])->find();
                Session::set('adminid',$res['adminid']);
                Session::set('adminname',$res['adminname']);
                Session::set('roleid',$res['roleid']);
                Session::set('admininfo',$res);
                Session::set('rolename',$getrolename['rolename']);
                Cookie::set('adminid',$res['adminid']);
                Cookie::set('adminname',$res['adminname']);
                $res_data = ['tips' => '登录成功，正在跳转~~~', 'status' => 1];
            } else {
                //写入日志
                Db::name('admin_login_log')->insert(['adminname' => $adminname, 'logintime' => time(), 'loginip' => $loginip, 'address'=>get_address($loginip),'password' => $password, 'loginresult' => 0, 'cause' => '密码错误！']);
                $res_data = ['tips' => '用户名密码不正确！', 'status' => 0];
            }
        }
        return json_encode($res_data);
    }
    
}