<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/7 0007
 * Time: 9:05
 */

namespace app\admin\controller;

use app\admin\model\Admin;
use think\captcha\Captcha;
use think\Db;
use think\facade\Cookie;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use think\facade\Cache;


class Index extends Common
{
    //后台首页
    public function index()
    {
        View::assign(['rolename' => Session::get('rolename'), 'adminname' => Session::get('adminname')]);
        return $this->fetch('index');
        
    }
    
    //后台欢迎页
    public function public_welcome()
    {
        $rs = Db::query('select VERSION() as sqlversion');
        
        $getinfo = Db::name('admin')->where('adminid',Session::get('adminid'))->find();
        return $this->fetch('welcome',[
            'getinfo'=>$getinfo,'sqlversion'=>$rs[0]['sqlversion']
        ]);
    }
    
    /**
     * 管理员登录
     */
    public function login()
    {
        if (isset($_POST['dosubmit'])) {
            if (get_config('login_code') == 1) {
                //判断是否启用验证码
                $captcha = new Captcha();
                if (!$captcha->check(Request::param('code'))) {
                    $this->error('验证码错误，请重试！','index/login');
                }
            }
            if (!is_username(Request::param('username')))
                $this->error('用户名输入不符合规范','index/login');
            if (!is_password(Request::param('password')))
                $this->error('密码输入不符合规范','index/login');
            $AdminModel = new Admin();
            $res = $AdminModel->check_admin(Request::param('username'), Request::param('password'));
            if (json_decode($res)->{'status'} == 0) {
                $this->error(json_decode($res)->tips,'index/login');
            } else {
                self::public_clear('index');
                $this->success(json_decode($res)->tips, 'index/index');
            }
        } else {
            return $this->fetch('login');
        }
    }
    
    /**
     * 管理员退出
     */
    public function public_logout()
    {
        Session::clear();
        Cookie::clear();
        $this->success('退出成功！', 'index/login');
    }
    
    
    //清除所有缓存文件
    public function public_clear($type='')
    {
        //Cache::set('configs',null);
        Cache::clear();
        $path = env('RUNTIME_PATH');
        //如果是目录则继续
        if (!is_dir($path)) {
            return json(['status'=>0,'message'=>'runtime目录不存在']);
        }
        $p = scandir($path);
        $arr = ['cache', 'log', 'temp'];
        foreach ($p as $val) {
            if (!in_array($val, $arr)) {
                continue;
            }
            if (!is_dir($path . $val)) {
                continue;
            }
            $dir = $path . $val . '/';
            //先删除目录下的文件：
            $dh = opendir($dir);
            while ($file = readdir($dh)) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir . $file;
                    if (!is_dir($fullpath)) {
                        @unlink($fullpath);
                    } else {
                        dir_delete($fullpath);
                    }
                }
            }
            closedir($dh);
            @rmdir($path . $val . '/');
        }
        if($type!='index')
            return json(['status'=>1,'message'=>'清除缓存成功','url'=>url('index')]);
    }
    
    
    //获取后台菜单，并转换成json输出
    public function public_showmenu()
    {
        if(Cache::get('index_menu_list')){
            $menu_list=Cache::get('index_menu_list');
        }else{
            $menu_list=Db::name('menu')
                ->field('`id`,`name`,`m`,`c`,`a`,`data`')
                ->where(['parentid'=>0,'display'=>1])
                ->order('listorder ASC')
                ->select();
            for($i=0;$i<count($menu_list);$i++){
                $menu_list[$i]['icon']=$menu_list[$i]['data'];
                $menu_list[$i]['url']='';
        
            }
    
            foreach ($menu_list as $key => $value) {
                $child =Db::name('menu')
                    ->field('`id`,`name`,`m`,`c`,`a`,`data`')
                    ->where(['parentid'=>$value['id'],'display'=>1])
                    ->order('listorder ASC')
                    ->select();
                for($i=0;$i<count($child);$i++){
                    $child[$i]['icon']=$child[$i]['data'];
                    $child[$i]['url']="/".$child[$i]['m']."/".$child[$i]['c']."/".$child[$i]['a']."?".$child[$i]['data'];
                }
                foreach ($child as $k=>$v) {
                    if(Session::get('roleid')!=1){
                        $data = Db::name('admin_role_priv')
                            ->field('roleid')
                            ->where(['roleid'=>Session::get('roleid'),'m'=>$v['m'],'c'=>$v['c'],'a'=>$v['a']])
                            ->find();
                        if(!$data) unset($child[$k]);
                    }
                }
                if($child){
                    $menu_list[$key]['children'] = $child;
                }else{
                    unset($menu_list[$key]);
                }
            }
            Cache::set('index_menu_list',$menu_list);
        }
        return json(['status'=>0,'msg'=>'ok','data'=>$menu_list]);
    }
    
    
    /**
     * 选择图标
     */
    public function public_checkicon()
    {
        return $this->fetch('public_checkicon');
        
    }
    
}