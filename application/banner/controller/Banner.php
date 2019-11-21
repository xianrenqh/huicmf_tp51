<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/18 0018
 * Time: 10:53
 */

namespace app\banner\controller;

use app\admin\controller\Common;
use lib\Form;
use think\Db;
use think\facade\Request;
use think\facade\View;

class Banner extends Common
{
    private $db_banner;
    private $db_banner_type;
    
    public function __construct()
    {
        parent::__construct();
        $this->db_banner = Db::table('hui_banner');
        $this->db_banner_type = Db::table('hui_banner_type');
    }
    
    /**
     * banner列表
     */
    public function init()
    {
        $total = $this->db_banner->count();
        $data = $this->db_banner->order('typeid ASC,listorder ASC,id DESC')->paginate(10);
        $items = $data->items();
        foreach ($items as $k => $v) {
            $items[$k]['typename'] = $this->get_banner_type($v['typeid']);
        }
        View::assign(['total' => $total, 'items' => $items, 'data' => $data]);
        return $this->fetch('banner_list');
    }
    
    /**
     * 编辑banner
     */
    public function edit()
    {
        $id = Request::param('id');
        if (Request::param('dosubmit')) {
            if ($this->db_banner->where('id', Request::param('id'))->strict(false)->update(Request::post())) {
                return_json(array('status' => 1, 'icon' => 1, 'message' => '操作成功~~~'));
            } else {
                return_json(array('status' => 0, 'icon' => 2, 'message' => '操作失败！！！'));
            }
        } else {
            $Form = new Form();
            $types = $this->db_banner_type->select();
            $data = $this->db_banner->find(Request::param('id'));
            $form_image = $Form->image('image', $data['image']);
            return $this->fetch('banner_edit', ['types' => $types, 'data' => $data, 'form_image' => $form_image]);
        }
    }
    
    
    /**
     * 添加banner
     */
    public function add()
    {
        if (Request::param('dosubmit')) {
            $post_data = Request::post();
            $post_data['inputtime'] = time();
            $res = $this->db_banner->data($post_data)->strict(false)->insert();
            if ($res) {
                return_json(array('status' => 1, 'icon' => 1, 'message' => '操作成功~~~'));
            } else {
                return_json(array('status' => 0, 'icon' => 2, 'message' => '操作失败!!!'));
            }
        } else {
            $types = $this->db_banner_type->select();
            $Form = new Form();
            $form_image = $Form->image('image');
            return $this->fetch('banner_add', ['types' => $types, 'form_image' => $form_image]);
        }
    }
    
    
    /**
     * 删除banner
     */
    public function del()
    {
        if ($this->db_banner->delete(Request::param('id'))) {
            $this->success('操作成功！！！');
        } else {
            $this->error('操作失败！！！');
        }
    }
    
    
    /**
     * 添加banner分类
     */
    public function cat_add()
    {
        
        if (Request::param('dosubmit')) {
            $getname = $this->db_banner_type->where('name', Request::param('name'))->find();
            if ($getname) {
                return json(['status' => 0, 'message' => '分类名称已存在，请重新输入']);
            } else {
                $typeid = $this->db_banner_type->data(Request::post())->strict(false)->insert();
                switch ($typeid) {
                    case true:
                        $html = "<option value='" . $typeid . "' selected>" . $getname . "</option>";
                        return_json(array('status' => 1, 'message' => '操作成功~~，请选择！！', 'html' => $html));
                        break;
                    case false:
                        return_json(array('status' => 0, 'message' => '操作失败~~'));
                }
            }
        } else {
            return $this->fetch('cat_add');
        }
    }
    
    /**
     * banner分类管理
     */
    public function cat_manage()
    {
        
        if (Request::param('id')) {
            if ($this->db_banner_type->delete(Request::param('id'))) {
                return_json(array('status' => 1, 'message' => '操作成功'));
            } else {
                return_json(array('status' => 0, 'message' => '操作失败'));
            }
        } else {
            $data = $this->db_banner_type->select();
            return $this->fetch('cat_manage', ['data' => $data]);
        }
    }
    
    /**
     * 获取banner分类
     */
    public function get_banner_type($typeid)
    {
        if (!$typeid)
            return '无分类';
        $r = $this->db_banner_type->find($typeid);
        return $r ? $r['name'] : '';
    }
    
    
}