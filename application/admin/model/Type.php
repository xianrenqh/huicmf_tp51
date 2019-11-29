<?php
/**
 * Created by PhpStorm.
 * User: 程序猿
 * Date: 2019-11-27
 * Time: 14:27
 * Info:
 */

namespace app\admin\model;

use lib\Tree;
use think\Model;

class Type extends Model
{
    protected $pk = 'type_id';
    protected $name = 'type';
    
    
    // 模型初始化
    protected static function init()
    {
        //TODO:初始化内容
    }
    
    public function countData($where)
    {
        $total = $this->where($where)->count();
        return $total;
    }
    
    //获取顶级分类
    public function getListData()
    {
        $list = Type::where(['type_status' => 1, 'type_pid' => 0])->select();
        return $list;
    }
    
    public function findData($type_id)
    {
        $res = Type::where('type_id', $type_id)->find();
        if ($res) {
            return ['code' => 1, 'msg' => '请求成功', 'data' => $res];
        } else {
            return ['code' => 1001, 'msg' => '请求失败'];
        }
    }
    
    public function saveData($data)
    {
        if (isset($data['type_id'])) {
            //修改
            $data['type_uptime'] = time();
            $up = Type::where('type_id', $data['type_id'])->data($data)->strict(false)->update();
            if ($up) {
                $this->clearCache();
                return ['code' => 1, 'msg' => '修改成功'];
            } else {
                return ['code' => 1001, 'msg' => '修改失败'];
            }
        } else {
            //添加
            $cha = Type::where('type_name', $data['type_name'])->find();
            if ($cha) {
                return ['code' => 1001, 'msg' => '分类名已存在，请修改！！！'];
            } else {
                $add = Type::strict(false)->insert($data);
                if ($add) {
                    $this->clearCache();
                    return ['code' => 1, 'msg' => '添加成功~~~'];
                } else {
                    return ['code' => 1001, 'msg' => '添加失败！！！'];
                }
            }
        }
    }
    
    /**
     * @name:删除分类
     * @param $type_id
     * @return array
     * @tipes 没有做分类下是否有文章内容的判断
     */
    public function delData($type_id)
    {
        //判断是否父级并且是否有子级，有的话不允许删除
        $chapid = Type::field('type_pid')->where('type_id',$type_id)->find();
        if($chapid['type_pid']==0){
            $countp = Type::where('type_pid',$type_id)->count();
            if($countp>0){
                return ['code'=>1001,'msg'=>'有子级分类，不允许删除'];
            }else{
                $this->clearCache();
                $res = Type::where('type_id',$type_id)->delete();
                return ['code'=>1,'msg'=>'删除成功'];
            }
        }else{  //删除子级
            $this->clearCache();
            $res = Type::where('type_id',$type_id)->delete();
            return ['code'=>1,'msg'=>'删除成功'];
        }
    }
    
    //下拉选择框里的tree
    public function selectTreeData($type_id = '')
    {
        $tree = new Tree();
        $tree->icon = array('│ ', '├─ ', '└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $list = Type::field('type_id,type_name,type_pid,type_status')->where(['type_status' => 1])->select();
        $array = [];
        foreach ($list as $v) {
            $v['parentid'] = $v['type_pid'];
            $v['id'] = $v['type_id'];
            $v['selected'] = ($v['type_id'] == $type_id) ? "selected" : '';
            $array[] = $v;
        }
        $str = "<option value=\$id \$selected>\$spacer\$type_name</option>";
        $tree->init($array);
        if (cache('TypeselectTreeData')) {
            $menus = cache('TypeselectTreeData');
        } else {
            $menus = $tree->get_tree(0, $str);
            cache('TypeselectTreeData', $menus);
        }
        
        return $menus;
    }
    
    //列表页的tree
    public function listTreeData()
    {
        $tree = new Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $data = db('type')->order('type_sort ASC')->select();
        $array = [];
        foreach ($data as $v) {
            $v['id'] = $v['type_id'];
            $v['parentid'] = $v['type_pid'];
            $edit_tree = '<a title=\'编辑菜单\' href=\'javascript:;\' onclick=\'WeAdminShow("编辑菜单","' . url('edit', ['id' => $v['id']]) . '",800,560)\' style=\'text-decoration:none\'  class=\'layui-btn layui-btn-xs layui-btn-normal\'>编辑</a>';
            $del_tree = '<a title="删除" href="javascript:;" data-href="' . url('del', ['id' => $v['id']]) . '" style="text-decoration:none"  class="layui-btn layui-btn-xs layui-btn-danger j-tr-del">删除</a>';
            $add_tree = '<a title=\'增加菜单\' href=\'javascript:;\' onclick=\'WeAdminShow("增加子菜单","' . url('add', ['parentid' => $v['id']]) . '",800,560)\' style=\'text-decoration:none\'  class=\'layui-btn layui-btn-xs\'>增加子类</a>';
            $add_tree = $v['type_pid'] == 0 ? $add_tree : '';
            $v['string'] = $edit_tree . $del_tree . $add_tree;
            $v['checked'] = $v['type_status'] == 1 ? 'checked' : '';
            
            //这里需要计算对应栏目下的文章数量，默认写了0，没有做计算
            $v['count'] = 0;
            $array[] = $v;
            
        }
        $str = "<tr>
					<td align='center'><input name='listorders[\$id]' type='text' value='\$type_sort' class='input-text listorder'></td>
					<td align='center'>\$id</td>
					<td>\$spacer\$type_name <span class='layui-badge'>\$count</span></td>
					<td>\$type_en </td>
					<td><input type='checkbox' id='\$type_id' name='status' value='\$type_name' lay-skin='switch' lay-filter='switchTest' lay-text='正常|关闭'\$checked></td>
					<td>\$string</td>
				</tr>";
        $tree->init($array);
        if (cache('TypeListTreeData')) {
            $menus = cache('TypeListTreeData');
        } else {
            $menus = $tree->get_tree(0, $str);
            cache('TypeListTreeData', $menus);
        }
        
        return $menus;
    }
    
    public function statusChange($data)
    {
        $res = Type::where('type_id', $data['id'])->data('type_status', $data['status'])->strict()->update();
        if ($res) {
            $this->clearCache();
            return ['code' => 1, 'msg' => '操作成功~~~'];
        } else {
            return ['code' => 1001, 'msg' => '操作失败！！！'];
        }
    }
    
    public function orderChange($data)
    {
        $type = new Type;
        $res = $type->saveAll($data);
        $this->clearCache();
        return ['code' => 1, 'msg' => '操作成功~~~'];
    }
    
    private function clearCache()
    {
        cache('TypeselectTreeData', null);
        cache('TypeListTreeData', null);
    }
    
}