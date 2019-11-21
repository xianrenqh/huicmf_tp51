<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/20 0020
 * Time: 11:24
 */

namespace app\ads\controller;

use app\admin\controller\Common;
use think\Db;
use think\facade\Request;

class Ads extends Common
{
    private $db_adver;
    
    public function __construct()
    {
        parent::__construct();
        $this->db_adver = Db::table('hui_adver');
    }
    
    //广告列表
    public function init()
    {
        $total = $this->db_adver->count();
        $data = $this->db_adver->paginate(10);
        return $this->fetch('ads_list',[
            'total'=>$total,
            'data'=>$data
        ]);
    }
    
    //添加广告
    public function add()
    {
        if(Request::param('dosubmit')){
            $post_data = Request::post();
            $post_data['inputtime']=time();
            $res = $this->db_adver->strict(false)->data($post_data)->insert();
            if($res){
                return_json(array('status' => 1, 'icon' => 1, 'message' => '操作成功~~~'));
            } else {
                return_json(array('status' => 0, 'icon' => 2, 'message' => '操作失败！！！'));
            }
        }else{
            return $this->fetch('ads_add');
        }
    }
    
    //删除广告
    public function del_one()
    {
        if ($this->db_adver->delete(Request::param('id'))) {
            $this->success('操作成功！！！');
        } else {
            $this->error('操作失败！！！');
        }
    }
    
    //编辑广告
    public function update()
    {
        if (Request::param('dosubmit')) {
            $res = $this->db_adver->data(Request::post())->where('id',input('id'))->strict(false)->update();
            if ($res) {
                return_json(array('status' => 1, 'icon' => 1, 'message' => '操作成功~~~'));
            } else {
                return_json(array('status' => 0, 'icon' => 2, 'message' => '操作失败！！或者你没有做任何修改'));
            }
        } else {
            $id = Request::param('id');
            $data = $this->db_adver->find($id);
            return $this->fetch('ads_edit', ['data' => $data]);
        }
    }
    
}