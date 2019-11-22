<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/12 0012
 * Time: 11:03
 */

namespace app\attachment\controller;

use app\admin\controller\Common;
use think\facade\Request;
use think\facade\View;
use think\facade\Session;
use think\Db;
use think\Image;

class Api extends Common
{
    private $filepath = './uploads/';     //指定上传文件保存的路径
    private $isadmin;
    private $groupid;
    private $userid;
    private $username;
    
    function __construct()
    {
        parent::__construct();
        $this->userid = Session::get('adminid') ? Session::get('adminid') : (Session::get('_userid') ? Session::get('_userid') : 0);
        $this->username = Session::get('adminname');
        $this->isadmin = Session::get('roleid') ? 1 : 0;
    }
    
    /**
     * 上传文件
     */
    public function upload()
    {
        //$filename = 'Filedata';
        $type = Request::param('type') ? Request::param('type') : 1;
        $module = Request::param('module') ? htmlspecialchars(Request::param('module')) : '';
        $option = [];
        if($type == 1){
            $option['allowtype'] =['gif','jpg', 'png', 'jpeg'];
        }else{
            $option['allowtype'] = $this->_get_upload_types();
        }
        $file = request()->file('Filedata');
        $getsize = $file->getSize();
        $info = $file
            ->validate(['size'=>get_config('upload_maxsize')*1024,'ext'=>$option['allowtype']])
            ->move( $this->filepath);
        if($info){
            $filename=str_replace('\\','/',$info->getSaveName());
            $fileurl = str_replace("./","/",$this->filepath.$filename);
            $fileinfo=[];
            $fileinfo['originname']=$info->getInfo()['name'];
            $fileinfo['filepath']=str_replace($info->getFilename(),"",$fileurl);
            $fileinfo['filesize']=$getsize;
            $fileinfo['filename']=$info->getFilename();
            $fileinfo['module'] =Request::param('module')?Request::param('module'): Request::module();
            $fileinfo['fileext']=$info->getExtension();
            $this->add_water($fileinfo);
            $this->_att_write($fileinfo);
            return json(['status'=>1,'filetype'=>$info->getExtension(),'title'=>$info->getFilename(),'msg'=>$fileurl]);
        }else{
            // 上传失败获取错误信息
            return json(['msg'=>$file->getError(),'status'=>0]);
        }
    }
        
        /**
     * 上传框
     */
    public function upload_box()
    {
        $pid = Request::param('pid') ? Request::param('pid') : 'uploadfile';
        $module = Request::param('module') ? Request::param('module') : '';
        $t = Request::param('t') ? Request::param('t') : 1; //上传类型，1为图片类型
        $n = Request::param('n') ? 20 : 1;  //上传数量
        $s = round(get_config('upload_maxsize') / 1024, 2) . 'MB';  //允许上传附件大小
        if ($t == 1) {
            $type = '*.jpg; *.jpeg; *.png; *.gif;';
        } else {
            $type = '*.' . join(',*.', self::_get_upload_types());
        }
        //如果不是管理员，只列出自己上传的附件
        $where ='1=1';
        if (!$this->isadmin)
            $where.= ['userid'=>'$this->userid'];
        $attachment = Db::name('attachment');
        $data = $attachment->field('isimage,originname,filename,filepath,fileext')->where($where)->order('id DESC')
            ->paginate(8,false,['query'=>['tab'=>1]]);
        $total = $data->total();
        $parameter = Request::param();
        $parameter['tab'] = 1;
        View::assign(['total'=>$total,'data'=>$data,'type'=>$type,'s'=>$s,'n'=>$n,'t'=>$t,'module'=>$module,'pid'=>$pid]);
        return $this->fetch('upload_box_html5');
    }
    
    /**
     * 获取上传类型
     */
    private function _get_upload_types()
    {
        
        $arr = explode('|', get_config('upload_types'));
        $allow = array('gif', 'jpg', 'png', 'jpeg', 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt', 'csv', 'mp4', 'avi', 'wmv', 'rmvb', 'flv', 'mp3', 'wma', 'wav', 'amr', 'ogg');
        foreach ($arr as $key => $val) {
            if (!in_array($val, $allow))
                unset($arr[$key]);
        }
        return $arr;
    }
    
    /**
     * 上传附件写入数据库
     */
    private function _att_write($fileinfo){

        $arr=$fileinfo;
        $arr['isimage'] = in_array($fileinfo['fileext'], array('gif', 'jpg', 'png', 'jpeg')) ? 1 : 0;
        $arr['downloads'] = 0;
        $arr['userid'] = $this->userid;
        $arr['username'] = $this->username;
        $arr['uploadtime'] = time();
        $arr['uploadip'] = getip();
        Db::name('attachment')->data($arr)->insert();
    }
    
    //添加水印
    private function add_water($imginfo)
    {
        //获取水印配置
        if(get_config('watermark_enable')){
            $waterpic = "./static/water/".get_config('watermark_name');
            $pic_url =".".$imginfo['filepath'].$imginfo['filename'];
            $image=Image::open($pic_url);
            $image->water($waterpic,get_config('watermark_position'),get_config('watermark_touming'))->save($pic_url);
        }else{
            return false;
        }
    }
    
    
}