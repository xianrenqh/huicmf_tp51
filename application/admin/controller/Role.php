<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/13 0013
 * Time: 15:44
 */

namespace app\admin\controller;

require '../extend/lib/Tree.php';
use lib\Tree;
use think\Db;
use think\facade\Request;
use think\facade\View;


class Role extends Common
{
    
    public function init()
    {
        $list = Db::name('admin_role')->order('roleid asc')->select();
        $total = count($list);
        if (Request::param('do')) {
            $data['code'] = 0;
            $data['msg'] = '';
            $data['count'] = $total;
            $data['data'] = $list;
            return json($data);
        } else {
            View::assign(['data' => $list, 'total' => $total]);
            return $this->fetch('role_list');
        }
    }
    
    //添加角色
    public function add()
    {
        if(Request::param('dosubmit')){
            $res=Db::name('admin_role')->where(['rolename'=>Request::param('rolename')])->find();
            if($res){
                $return=['status'=>0,'message'=>'角色名称已存在'];
            }else{
                $data=[
                    'system'=>0,
                    'disabled'=>Request::param('disabled'),
                    'rolename'=>Request::param('rolename'),
                    'description'=>Request::param('description'),
                ];
                Db::name('admin_role')->data($data)->insert();
                $return = ['status'=>1,'message'=>'添加成功'];
            }
            return json($return);
        }else{
            return $this->fetch('role_add');
        }
    }
    
    //修改角色
    public function edit(){
        if(Request::param('dosubmit')){
            if(Request::param('roleid')==1 && Request::param('disabled')==1 ){
                return json(array('status'=>0,'message'=>'超级管理员权限不允许禁用~~~'));
            }else{
                $update = Db::name('admin_role')->where('roleid',Request::param('roleid'))->strict(false)->data(Request::post())->update();
                if($update){
                    Db::name('admin')->data('status',0)->where('roleid',Request::param('roleid'))->update();
                    return json(['status'=>1,'message'=>'修改成功~~~']);
                }else{
                    return json(['status'=>0,'message'=>'你没有做任何修改']);
                }
            }
        }else{
            $data =Db::name('admin_role')->where('roleid',Request::param('roleid'))->find();
            View::assign(['data'=>$data]);
            return $this->fetch('role_edit');
        }
    }
    
    //删除角色
    public function del()
    {
        //查询是否有在使用此角色的管理员
        $findadmin = Db::name('admin')->where('roleid',Request::param('roleid'))->count();
        if($findadmin>0){
            $this->error('有在使用此角色的管理员，删除失败！');
        }else{
            if(Request::param('roleid')==1){
                $this->error('超级管理员权限不允许删除！！！');
            }else{
                $res = Db::name('admin_role')->where('roleid',Request::param('roleid'))->delete();
                if($res){
                    $this->success('删除成功~~~');
                }else{
                    $this->error('删除失败！！！');
                }
            }
        }
        
    }
    
    /**
     * 权限管理
     */
    public function role_priv(){
        if(Request::param('dosubmit')){
            $admin_role_priv = Db::name('admin_role_priv');
            $menuid=Request::param('menuid');
            $roleid=Request::param('roleid');
            if(is_array($menuid) && count($menuid)>0 ){
                $admin_role_priv->where('roleid',$roleid)->delete();
                $menuinfo = Db::name('menu')->field('`id`,`m`,`c`,`a`,`data`')->select();
                foreach ($menuinfo as $_v) {
                    $menu_info[$_v['id']] = $_v;
                }
                foreach(Request::param('menuid') as $menuid){
                    $info = [];
                    $info = $menu_info[$menuid];
                    if($info['m'] == '') continue;
                    $info['roleid'] = Request::param('roleid');
                    $info['authid'] = $info['id'];
                    $admin_role_priv->data($info)->strict(false)->insert();
                }
            }else{
                $admin_role_priv->where('roleid',$roleid)->delete();
            }
            $this->success('操作成功~~~');
        }else{
            $roleid = Request::param('roleid');
            if($roleid == 1) $this->error('超级管理员权限不允许修改！',url('init'));
            $tree = new Tree();
            $tree->icon = array('│ ','├─ ','└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
    
            $data = Db::name('menu')->order('listorder ASC,id DESC')->select();
            $priv_data = Db::name('admin_role_priv')->where(['roleid'=>$roleid])->select();
            foreach($data as $k=>$v) {
                $data[$k]['level'] = $this->get_level($v['id'],$data);
                $data[$k]['checked'] = ($this->is_checked($v,$roleid,$priv_data))? ' checked' : '';
            }
            $data=array2level($data);
            
            $checkedId =array_column($priv_data, 'authid');
            $checkIds=[];
            foreach($checkedId as $k=> $v){
                $checkIds[]= ($v);
            }
            $returndata = ['code'=>0,'msg'=>'获取成功',
                'data'=>[
                    'list'=>$data,
                    'checkedId'=> $checkIds
                ]
            ];
            if(Request::param('do')){
                return json($returndata);
            }else{
                View::assign(['roleid'=>$roleid]);
                return $this->fetch('role_priv');
            }
        }
    }
    
    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    private function get_level($id, $array=array(), $i=0) {
        foreach($array as $n=>$value){
            if($value['id'] == $id){
                if($value['parentid']== '0') return $i;
                $i++;
                return $this->get_level($value['parentid'],$array,$i);
            }
        }
    }
    
    
    /**
     *  检查指定菜单是否有权限
     * @param array $data menu表中数组
     * @param int $roleid 需要检查的角色ID
     */
    private function is_checked($data,$roleid,$priv_data) {
        $priv_arr = array('m','c','a','data');
        if($data['m'] == '') return false;
        foreach($data as $key=>$value){
            if(!in_array($key,$priv_arr)) unset($data[$key]);
        }
        $data['roleid'] = $roleid;
        return in_array($data, $priv_data) ? true : false;
    }
    
    
}