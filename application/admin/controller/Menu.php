<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/8 0008
 * Time: 8:34
 */

namespace app\admin\controller;

require '../extend/lib/Tree.php';

use lib\Tree;
use think\Db;
use think\facade\Cache;
use think\facade\Request;
use think\facade\View;

class Menu extends Common
{
    /**
     * 菜单管理列表
     */
    public function init()
    {
        if (Cache::get('menu_string_1')) {
            $menus = Cache::get('menu_string_1');
        } else {
            $tree = new Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $data = Db::name('menu')->order('listorder ASC')->select();
            $array = [];
            foreach ($data as $v) {
                $add_tree = '<a title=\'增加菜单\' href=\'javascript:;\' onclick=\'WeAdminShow("增加子菜单","' . url('add', ['parentid' => $v['id']]) . '",800,560)\' style=\'text-decoration:none\'  class=\'layui-btn layui-btn-xs\'>增加子类</a>';
                $edit_tree = '<a title=\'编辑菜单\' href=\'javascript:;\' onclick=\'WeAdminShow("编辑菜单","' . url('edit', ['id' => $v['id']]) . '",800,560)\' style=\'text-decoration:none\'  class=\'layui-btn layui-btn-xs layui-btn-normal\'>编辑</a>';
                $del_tree = '<a title="删除" href="javascript:;" onclick=\'WeAdminDel("' . url('delete', ['id' => $v['id']]) . '")\'style="text-decoration:none"  class="layui-btn layui-btn-xs layui-btn-danger">删除</a>';
                $v['string'] = $add_tree . $edit_tree . $del_tree;
                $v['display'] = $v['display'] ? '<span class="layui-btn layui-btn-xs">显示</span>' : '<span class="layui-btn layui-btn-xs layui-bg-red">隐藏</span>';
                $array[] = $v;
            }
            $str = "<tr>
					<td><input name='listorders[\$id]' type='text' value='\$listorder' class='input-text listorder'></td>
					<td>\$id</td>
					<td>\$spacer\$name</td>
					<td style='text-align: center'>\$display</td>
					<td>\$string</td>
				</tr>";
            $tree->init($array);
            $menus = $tree->get_tree(0, $str);
            Cache::set('menu_string_1', $menus);
        }
        View::assign(['menus' => $menus]);
        return $this->fetch('menu_list');
    }
    
    
    /**
     * 删除菜单
     */
    public function delete()
    {
        $id = Request::param('id');
        $data = Db::name('menu')->where(['parentid'=>$id])->select();
        if(count($data)> 0){
            $this->error('删除失败，该菜单下有子菜单！');
        }else{
            Db::name('menu')->where('id',$id)->delete();
            Cache::clear();
        }
        $this->success('操作成功');
    }
    
    /**
     * 添加菜单
     */
    public function add()
    {
        if (Request::param('dosubmit')) {
            Db::name('menu')->strict(false)->insert(Request::post(), true);
            Cache::clear();
            return json(array('status'=>1,'message'=>'添加成功！'));
        }else{
            $parentid = Request::param('parentid')?Request::param('parentid'):0;
            $tree=new Tree();
            $data = Db::name('menu')->order('listorder ASC,id DESC')->select();
            $array =[];
            foreach($data as $v) {
                $v['selected'] = $v['id'] == $parentid ? 'selected' : '';
                $array[] = $v;
            }
            $str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
            $tree->init($array);
            $select_menus = $tree->get_tree(0, $str);
            View::assign('select_menus',$select_menus);
            return $this->fetch('menu_add');
        }
    }
    
    /**
     * 编辑菜单
     */
    public function edit()
    {
        $id = Request::param('id')?Request::param('id'):0;
        if (Request::param('dosubmit')) {
            $update = Db::name('menu')->where('id',$id)->strict(false)->update(Request::post());
            if($update){
                Cache::clear();
                return json(['status'=>1,'message'=>'修改成功！']);
            }else{
                return json(['status'=>0,'message'=>'修改失败获取没有做任何修改！！！']);
            }
        }else{
            $parentid = Request::param('parentid')?Request::param('parentid'):0;
            $r = $r = Db::name('menu')->where(['id'=>$id])->find();
            if($r) extract($r);
            $tree=new Tree();
            $r = Db::name('menu')->order('listorder ASC,id DESC')->select();
            $array =[];
            foreach($r as $v) {
                $v['selected'] = $v['id'] == $parentid ? 'selected' : '';
                $array[] = $v;
            }
            $str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
            $tree->init($array);
            $select_menus=$tree->get_tree(0, $str);
            $data = Db::name('menu')->where('id',Request::param('id'))->find();
            View::assign([
                'datas'=>$data,
                'select_menus'=>$select_menus
            ]);
            return $this->fetch('menu_edit');
        }
    }
    
    
    /**
     * 菜单排序
     */
    public function order()
    {
        if (Request::param('dosubmit')) {
            foreach (Request::param('listorders') as $id => $listorder) {
                Db::name('menu')->where(['id' => $id])->update(['listorder' => $listorder]);
            }
            Cache::clear();
            $this->success('操作成功！', 'init', 1, 2);
        } else {
            $this->error('操作失败');
        }
    }
    
    
}