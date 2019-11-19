<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/7 0007
 * Time: 17:03
 */

namespace app\admin\controller;

use think\Db;
use think\facade\Request;
use think\facade\View;

class AdminLog extends Common
{
    
    /**
     * 后台操作日志列表
     */
    public function init()
    {
        $data = Db::name('admin_log')->order('id desc')->paginate(13);
        $count = $data->total();
        View::assign(['total' => $count, 'data' => $data]);
        return $this->fetch('admin_log_list');
    }
    
    /**
     * 后台操作日志删除
     */
    public function del_admin_log()
    {
        if (Request::param('dosubmit')) {
            $data = Db::name('admin_log')->where('logtime', '<', strtotime('-1 month'))->delete();
            if ($data) {
                $this->success('数据删除成功');
            } else {
                $this->error('数据没有删除成功或者没有可删除数据');
            }
        }
    }
    
    /**
     * 后台登录日志列表
     */
    public function admin_login_log_list()
    {
        $data = Db::name('admin_login_log')->order('id desc')->paginate(13);
        $count = $data->total();
        View::assign(['total' => $count, 'data' => $data]);
        return $this->fetch('admin_login_log_list');
    }
    
    /**
     * 后台登录日志删除
     */
    public function del_login_log()
    {
        if (Request::param('dosubmit')) {
            $data = Db::name('admin_login_log')->where('logintime', '<', strtotime('-1 month'))->delete();
            if ($data) {
                $this->success('数据删除成功');
            } else {
                $this->error('数据没有删除成功或者没有可删除数据');
            }
        }
    }
    
}