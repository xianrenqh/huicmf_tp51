<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/8 0008
 * Time: 15:28
 */

namespace app\admin\controller;

use lib\FtpLib;
use think\Db;
use think\facade\Cache;
use think\facade\Request;
use think\facade\View;

class SystemManage extends Common
{
    
    /**
     * 配置信息
     */
    public function init()
    {
        if (is_array(Cache::get('configs'))) {
            $data = Cache::get('configs');
        } else {
            $datalist = Db::name('config')->select();
            $data= array_column($datalist,'value','name');
        }
        $id = Request::param('id');
        View::assign(['data' => $data,'id'=>$id]);
        return $this->fetch('system_set');
    }
    
    /**
     * 保存配置信息
     */
    public function save()
    {
        if (Request::param('dosubmit')) {
            foreach (Request::post() as $key => $value) {
                $arr[$key] = $value;
                $value = htmlspecialchars($value);
                Db::name('config')->strict(false)->where(['name' => $key])->update(['value' => $value]);
                cache('configs',null);
            }
            return json(['message' => "保存成功", 'icon' => 2]);
        }
    }
    
    /*
    * 自定义配置
    */
    public function user_config_list()
    {
        $data = Db::name('config')->where('type', '99')->paginate(10);
        $total = count($data);
        View::assign(['total' => $total, 'data' => $data]);
        return $this->fetch('user_config_list');
    }
    
    /*
    *  添加配置
    */
    public function user_config_add()
    {
        if (Request::param('dosubmit')) {
            $res = Db::name('config')->where('name', Request::param('name'))->find();
            if ($res)
                return json(array('status' => 0, 'message' => '配置名称已存在！'));
            
            if (Request::param('fieldtype') == 'attachment' || Request::param('fieldtype') == 'image') {
                $postdata['value'] = Request::param('image');
            }
            
            if (empty(Request::param('value')))
                return json(array('status' => 0, 'message' => '配置值不能为空！'));
            $postdata[] = Request::post();
            $postdata[0]['type'] = '99';
            
            if (in_array(Request::param('fieldtype'), ['select', 'radio'])) {
                $postdata[0]['setting'] = array2string(explode('|', rtrim(Request::param('setting'), '|')));
            } else {
                $postdata[0]['setting'] = '';
            }
            
            $data = Db::name('config')->strict(false)->fetchSql(false)->insert($postdata[0], true);
            Cache::set('configs', null);
            if ($data) {
                return json(array('status' => 1, 'message' => '操作成功'));
            } else {
                return json(array('status' => 0, 'message' => '操作失败'));
            }
        } else {
            return $this->fetch('user_config_add');
        }
    }
    
    /*
    * 用户自定义配置编辑
    */
    public function user_config_edit()
    {
        if (Request::param('dosubmit')) {
            $update_id = Db::name('config')->where('id', Request::param('id'))->data(Request::post())->strict(false)->update();
            Cache::set('configs', null);
            if ($update_id) {
                return json(array('status' => 1, 'message' => '操作成功', 'icon' => 1));
            } else {
                return json(array('status' => 0, 'message' => '操作失败或者你没有做任何修改', 'icon' => 2));
            }
        } else {
            $data = Db::name('config')->where('id', Request::param('id'))->find();
            View::assign(['data' => $data, 'fieldtype' => $data['fieldtype'] ? $data['fieldtype'] : 'textarea']);
            return $this->fetch('user_config_edit');
        }
    }
    
    /*
    * 用户自定义配置删除
    */
    public function user_config_del()
    {
        if (is_array(Request::param('id'))) {
            if (Db::name('config')->delete(Request::param('id'))) {
                Cache::set('configs', null);
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }
    }
    
    /*删除单条数据*/
    public function user_config_del_one()
    {
        if (Request::param('id')) {
            if (Db::name('config')->delete(Request::param('id'))) {
                Cache::set('configs', null);
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }
    }
    
    /*
    * 根据字段类型获取html
    */
    public function public_gethtml($ftype = '', $val = '', $setting = '')
    {
        public_gethtml($ftype, $val, $setting);
    }
    
    /*
    * 发送测试邮件
    */
    public function public_mail_test()
    {
        $mail_to =Request::param('mail_to');
        $phpmail = new PhpMail();
        $mail_title = "【测试】这是一封测试邮件";
        $mail_body = "<h2>这是一封测试邮件，测试是否发送成功。</h2><br>发送时间：".date("Y-m-d H:i:s",time());
        $sendmail = $phpmail->email($mail_to,$mail_title,$mail_body);
        return $sendmail;
    }
    
    //测试ftp连接
    public function public_check_ftp()
    {
        $post_data = input();
        $ftp_conn =  new FtpLib();
        if($ftp_conn->connect()=='-1'){
            return json(['code'=>1001,'msg'=>'FTP服务器连接失败! 请检查服务器地址和端口','icon'=>2]);
        }elseif ($ftp_conn->connect()=='-2'){
            return json(['code'=>1001,'msg'=>'FTP服务器登录失败','icon'=>2]);
        }elseif ($ftp_conn->connect()=='-3'){
            return json(['code'=>1001,'msg'=>'FTP模块不支持，请先开启！！','icon'=>2]);
        }
        else{
            return json(['code'=>1,'msg'=>'FTP连接成功','icon'=>1]);
        }
    }
    
}