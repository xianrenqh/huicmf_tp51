<?php
/**
 * Created by PhpStorm.
 * Date: 2019/11/20 0020
 * Time: 8:11
 */

namespace app\link\controller;

use app\admin\controller\Common;
use app\link\model\Link;
use think\Db;
use think\facade\Request;

class Links extends Common
{
    
    private $db_link;
    
    public function __construct()
    {
        parent::__construct();
        $this->db_link = Db::table('hui_link');
    }
    
    //友链列表
    public function init()
    {
        $total = $this->db_link->count();
        $data = $this->db_link->paginate(10);
        return $this->fetch('link_list', ['total' => $total, 'data' => $data]);
    }
    
    //添加
    public function add()
    {
        if (Request::param('dosubmit')) {
            $link = new Link();
            $postdata = Request::post();
            $postdata['addtime'] = time();
            $res = $link->allowField(true)->save($postdata);
            if ($res) {
                return_json(array('status' => 1, 'icon' => 1, 'message' => '操作成功~~~'));
            } else {
                return_json(array('status' => 0, 'icon' => 2, 'message' => '操作失败!!!'));
            }
        } else {
            return $this->fetch('link_add');
        }
    }
    
    //修改
    public function update()
    {
        $link = new Link();
        if (Request::param('dosubmit')) {
            $res = $link->allowField(true)->save(Request::post(), ['id' => Request::post('id')]);
            if ($res) {
                return_json(array('status' => 1, 'icon' => 1, 'message' => '操作成功~~~'));
            } else {
                return_json(array('status' => 0, 'icon' => 2, 'message' => '操作失败!!!'));
            }
        } else {
            $data = Link::get(Request::param('id'));
            return $this->fetch('link_edit', ['data' => $data]);
        }
    }
    
    //删除多条
    public function del_all()
    {
        $res = Link::destroy(Request::param('id'));
        if ($res) {
            $this->success('操作成功！！！');
        } else {
            $this->error('操作失败！！！');
        }
    }
    
    //删除一条
    public function del_one()
    {
        $link = Link::get(Request::param('id'));
        $res = $link->delete();
        if ($res) {
            $this->success('操作成功！！！');
        } else {
            $this->error('操作失败！！！');
        }
    }
    
    //排序
    public function order()
    {
        if (Request::param('dosubmit')) {
            $link = new Link();
            foreach (Request::param('listorder') as $id => $listorder) {
                $list[] = ['id' => $id, 'listorder' => $listorder];
            }
            $link->saveAll($list);
            $this->success('操作成功！', 'init', 1, 2);
        } else {
            $this->error('操作失败');
        }
    }
    
    //检测url
    public function check()
    {
        $id = input('id');
        $where = [];
        $where['id'] = ['eq', $id];
        $res = model('Link')->infoData($where);
        if ($res['code'] > 1) {
            return json($res);
        }
        $url = $res['info']['url'];
        $site_url = parse_url(get_config('site_url'));
        $site_url = $site_url['host'];
        $html = cmf_curl_get($url);
        $res = [];
        $res['code'] = 1;
        $res['msg'] = '';
        $msg = '';
        $code = 1;
        
        $ok = ' 友链正常';
        $err = ' 友链异常';
        
        $msg .= '[' . $site_url . ']';
        if (strpos($html, $site_url) !== false) {
            $code = 1;
            $msg .= $ok;
        } else {
            $code = 101;
            $msg .= $err;
        }
        
        $res['msg'] = $msg;
        return json($res);
    }
    
}