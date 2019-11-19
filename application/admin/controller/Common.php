<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/7 0007
 * Time: 9:06
 */

namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use think\facade\Cookie;
use think\facade\Session;

class Common extends Controller
{
    
    public static $ip;
    public function __construct() {
        self::$ip = getip();
        self::check_admin();
        self::check_priv();
        self::check_ip();
        if(get_config('admin_log')) self::manage_log();
    }
    
    /**
     * 判断用户是否已经登陆
     */
    final private function check_admin() {
        if(Request::module()=='admin' && Request::controller() =='Index' && Request::action() =='login') {
            return true;
        } else {
            $adminid=intval(Cookie::get('adminid'));
            if(!Session::get('adminid') || !(Session::get('roleid')) || $adminid != Session::get('adminid'))
                $this->error('请先登录','Index/login');
            self::check_referer();
        }
    }
    
    /**
     * 权限判断
     */
    final private function check_priv() {
        if(Request::module() =='admin' && Request::controller() =='Index' && in_array(Request::action(),['login','init','index'])) return true;
        if(Session::get('roleid')== 1) return true;
        if(strpos(Request::action(), 'public_') === 0) return true;
        $r = Db::name('admin_role_priv')->where(array('m'=>Request::module(),'c'=>Request::controller(),'a'=>Request::action(),'roleid'=>Session::get('roleid')))->find();
        if(!$r) {
            error2("你没有操作权限~~~");
        }
        
    }
    
    /**
     * 后台IP禁止判断
     */
    final private function check_ip(){
        $admin_prohibit_ip = get_config('admin_prohibit_ip');
        if(!$admin_prohibit_ip) return true;
        $arr = explode(',', $admin_prohibit_ip);
        var_dump($arr);
        foreach($arr as $val){
            //是否是IP段
            if(strpos($val,'*')){
                if(strpos(self::$ip, str_replace('.*', '', $val)) !== false) {
                    error2("你在IP禁止段内,禁止访问！~~~");
                }
            }else{
                //不是IP段,用绝对匹配
                if(self::$ip == $val) {
                    error2("IP地址绝对匹配,禁止访问！~~~");
                }
            }
        }
    }
    
    
    /**
     * 检查REFERER
     */
    final private function check_referer(){
        if(strpos(Request::action(), 'public_') === 0) return true;
        if(HTTP_REFERER){
            if(strpos(HTTP_REFERER, SERVER_PORT.HTTP_HOST) === false){
                //showmsg('非法来源，拒绝访问！', 'stop');
                $this->error('非法来源，拒绝访问！');
            }
        }
        return true;
    }
    
    /**
     * 记录日志
     */
    final private function manage_log() {
        if(Request::action() == '' || Request::action() == 'index' || strpos(Request::action(), '_list') || in_array(Request::action(), array('login', 'public_home'))) {
            return false;
        }else {
            $adminid = Session::get('adminid');
            $adminname = Session::get('adminname');
            $url = 'm='.Request::module().'&c='.Request::controller().'&a='.Request::action();
            Db::name('admin_log')->insert([
                'module'=>Request::module(),
                'action'=>Request::controller(),
                'adminname'=>$adminname,
                'adminid'=>$adminid,
                'querystring'=>$url,
                'logtime'=>time(),
                'ip'=>self::$ip
            ]);
        }
    }
    
    
    
}