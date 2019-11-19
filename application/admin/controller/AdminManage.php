<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/8 0008
 * Time: 16:25
 */

namespace app\admin\controller;

use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use think\facade\Cache;

class AdminManage extends Common
{
    /**
     * 管理员管理列表
     */
    public function init()
    {
        if (Request::param('do')) {
            $page = Request::param('page');
            $limit = Request::param('limit');
            $first = ($page - 1) * $limit;
            $getkey = Request::param('key');
            $where = '1=1';
            $adminname = $getkey['adminname'] ? $getkey['adminname'] : "";
            $roles = $getkey['roles'] ? $getkey['roles'] : "";
            $status = $getkey['isuse'] == '' ? '' : $getkey['isuse'];
            if ($adminname != '') {
                $where .= " and adminname like '%$adminname%' ";
            }
            if ($roles != '') {
                $where .= " and roleid=$roles ";
            }
            if ($status == '1' || $status == '0') {
                $where .= " and status =$status ";
            }
            $list = Db::name('admin')->where($where)->limit("$first,$limit")->order('adminid asc')->select();
            for ($i = 0; $i < count($list); $i++) {
                unset($list[$i]['password']);
                $list[$i]['rolename'] = $this->get_role($list[$i]['roleid'])['rolename'];
                $list[$i]['addtime'] = date("Y-m-d H:i:s", $list[$i]['addtime']);
                $list[$i]['logintime'] = $list[$i]['logintime'] == 0 ? '' : date("Y-m-d H:i:s", $list[$i]['logintime']);
            }
            $total = count(Db::name('admin')->where($where)->select());
            $data['code'] = 0;
            $data['msg'] = '';
            $data['count'] = $total;
            $data['data'] = $list;
            return json($data);
        } else {
            $role_list_data = $this->get_role();
            $admin_list = Db::name('admin')->select();
            $total = count($admin_list);
            View::assign(['role_list' => $role_list_data, 'total' => $total]);
            return $this->fetch('admin_list');
        }
    }
    
    /**
     * 编辑管理员
     */
    public function edit()
    {
        if (Request::param('dosubmit')) {
            $post_data = [];
            if (Request::param('password')) {
                if (Request::param('password') != Request::param('password2')) {
                    return json(['status' => 0, 'message' => '您输入的两次密码不一样，请重新输入']);
                }
                $post_data['password'] = password(Request::param('password'));
            }
            $post_data['adminname'] = Request::param('adminname');
            $post_data['roleid'] = Request::param('roleid');
            $post_data['realname'] = Request::param('realname');
            $post_data['email'] = Request::param('email');
            $post_data['status'] = Request::param('status');
            $update = Db::name('admin')->data($post_data)->where('adminid', Request::param('adminid'))->strict(false)->update();
            if ($update) {
                return json(array('status' => 1, 'message' => "修改成功"));
            } else {
                return json(['message' => '你没做任何修改!!']);
            }
            
        } else {
            $role_list_data = $this->get_role();
            $data = Db::name('admin')->where('adminid', Request::param('adminid'))->find();
            View::assign(['data' => $data, 'role_list' => $role_list_data]);
            return $this->fetch('admin_edit');
        }
    }
    
    /**
     * 添加管理员
     */
    public function add()
    {
        if(Request::param('dosubmit')){
            $res=Db::name('admin')->where(['adminname'=>Request::param('adminname')])->find();
            
            if($res){
                $return=['status'=>0,'message'=>'登录名已存在'];
            }else{
                unset($_POST['dosubmit']);
                $_POST['password']=password($_POST['password']);
                $_POST['rolename']=$role_list =self::get_role( $_POST['roleid'])['rolename'];
                $_POST['addtime']=time();
                $data=[
                    'password'=>password(Request::param('password')),
                    'realname'=>self::get_role( Request::param('roleid'))['rolename'],
                    'roleid'=>Request::param('roleid'),
                    'email'=>Request::param('email'),
                    'adminname'=>Request::param('adminname'),
                    'addpeople'=>Session::get('adminid'),
                    'status'=>Request::param('status'),
                    'addtime'=>time()
                ];
                Db::name('admin')->data($data)->insert();
                $return = ['status'=>1,'message'=>'添加成功'];
            }
            return json($return);
        }else{
            $role_list_data = $this->get_role();
            View::assign(['role_list' => $role_list_data]);
            return $this->fetch('admin_add');
        }
    }
    
    
    /**
     * 删除管理员
     */
    public function del()
    {
        if(Request::param('adminid')==1){
            $this->error('ID为1的超级管理员不允许删除！！！');
        }else{
            $res = Db::name('admin')->where('adminid',Request::param('adminid'))->delete();
            if($res){
                $this->success('删除成功~~~');
            }else{
                $this->error('删除失败！！！');
            }
        }
    }
    
    /**
     * 编辑用户信息
     */
    public function public_edit_info()
    {
        if (Request::param('dosubmit')) {
            $res = Db::name('admin')->where('adminid', Request::param('adminid'))->data('email', Request::post('email'))->update();
            if ($res) {
                $this->success('修改成功！');
            } else {
                $this->error('你没做任何修改！！！');
            }
        } else {
            $data = Db::name('admin')->where('adminid', Session::get('adminid'))->find();
            $data['rolename'] = self::get_role($data['roleid'])['rolename'];
            $data['logintime'] = date("Y-m-d H:i:s", $data['logintime']);
            View::assign(['data' => $data]);
            return $this->fetch('public_edit_info');
        }
    }
    
    /*
    *  修改个人密码
    */
    public function public_edit_pwd()
    {
        $data = Db::name('admin')->where(['adminid' => Session::get('adminid')])->find();
        if (Request::param('dosubmit')) {
            if (Request::param('type') == 'oldpwd') {
                //判断旧密码
                if ($data['password'] == password(Request::param('oldpwd'))) {
                    $result = ['msg' => "原密码正确", 'state' => 200];
                } else {
                    $result = ['msg' => "原密码不正确", 'state' => 201];
                }
                return json($result);
            }
            if (Request::param('type') == 'newpwd') {
                $data = Db::name('admin')->where(['adminid' => Session::get('adminid')])->data('password', password(Request::post('newpwd')))->update();
                if ($data) {
                    $result = ['msg' => "修改成功", 'state' => 200, 'icon' => 1];
                } else {
                    $result = ['msg' => "修改失败！！！", 'state' => 200, 'icon' => 2];
                }
                return json($result);
            }
        } else {
            View::assign(['data' => $data]);
            return $this->fetch('public_edit_pwd');
        }
        
    }
    
    /*
    *  获取角色表权限名称
    */
    public function get_role($roleid = '')
    {
        $admin_role = Db::name('admin_role');
        if ($roleid) {
            $getlist = $admin_role->field('rolename')->where(['roleid' => $roleid])->find();
        } else {
            $getlist = $admin_role->field('roleid,rolename,description')->where(['disabled' => 0])->select();
        }
        return $getlist;
    }
    
    
}