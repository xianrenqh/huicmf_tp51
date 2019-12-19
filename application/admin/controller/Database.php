<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/8 0008
 * Time: 11:37
 */

namespace app\admin\controller;

use lib\tree;
use think\facade\Request;
use think\facade\View;
use think\facade\Session;
use think\Db;
use xianrenqh\Backup;

class Database extends Common
{
    public $config;
    public function __construct() {
        parent::__construct();
        $this->config = array(
            'path'     => './Data/', //备份文件目录
            'part'     => 2097152, //2MB
            'compress' => 1,    //数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 4,	//数据库备份文件压缩级别 1普通 4 一般  9最高
        );
    }
    
    /**
     * 数据库列表
     */
    public function init()
    {
        $admin=Db::name('admin');
        $db= new Backup();
        $data = $db->dataList();
        for($i=0;$i<count($data);$i++){
            $data[$i]['data_length']=sizecount($data[$i]['data_length']);
        }
        if(Request::param('do')){
            $return = ["code"=>0,'msg'=>'获取成功','count'=>count($data),'data'=>$data];
            return json($return);
        }else{
            return $this->fetch('database_list');
        }
    }
    
    
    /**
     * 优化表
     */
    public function public_optimize()
    {
        $tables = Request::param('tables');
        $db= new Backup();
        if(is_array($tables)){
            if($tables[0]!=''){
                $db->optimize($tables);
                $return = ['message'=>'操作成功！','icon'=>1];
            }else{
                $return =['message'=>'请指定要优化的表！','icon'=>2];
            }
        }else{
            $db->optimize($tables);
            $return = ['message'=>'操作成功！','icon'=>1];
        }
        return json($return);
    }
    
    
    /**
     * 修复表
     */
    public function public_repair()
    {
        $db= new Backup();
        $tables = Request::param('tables');
        if(is_array($tables)){
            if($tables[0]!=''){
                $db->repair($tables);
                $return = ['message'=>'操作成功！','icon'=>1];
            }else{
                $return =['message'=>'请指定要修复的表！','icon'=>2];
            }
        }else{
            $db->repair($tables);
            $return = ['message'=>'操作成功！','icon'=>1];
        }
        return json($return);
    }
    
    /**
     * 表结构
     */
    public function public_datatable_structure()
    {
        $table = Request::param('table');
        if(!$table) $this->error('请选择表!');
        $admin = Db::name('admin');
        $data = $admin->query('SHOW CREATE TABLE '.$table);
        View::assign('data',$data[0]);
        return $this->fetch('datatable_structure');
    }
    
    /**
     * 数据库备份列表
     */
    public function databack_list()
    {
        $db=new Backup($this->config);
        $list = $db->fileList();
        if(count($list)>0){
            foreach($list as $key=>$v){
                $list1['filename']=$v['name'];
                $list1['filesize']=sizecount($v['size']);
                $list1['backtime']=date('Y-m-d H:i:s',$v['time']);
                $list1['part']=$v['part'];
                $list1['time']=$v['time'];
                $data[]=$list1;
            }
            array_multisort(array_column($data,'time'),SORT_DESC,$data);
        }else{
            $data=[];
        }
        
        View::assign(['roleid'=>Session::get('roleid'),'data'=>$data]);
        return $this->fetch('databack_list');
    }
    
    /**
     * 数据库导出备份
     */
    public function export_list()
    {
        $db= new Backup($this->config);
        $file=['name'=>date('Ymd-His'),'part'=>1];
        $tables = $db->dataList();
        for($i=0;$i<count($tables);$i++){
            $start= $db->setFile($file)->backup($tables[$i]['name'], 0);
        }
        return json(['message'=>'备份成功。请到根目录，Data 文件夹下查看','icon'=>1]);
    }
    
    /**
     * 备份文件下载
     */
    public function databack_down()
    {
        $db= new Backup($this->config);
        $time=Request::param('time');
        $db->downloadFile($time);
    }
    
    /**
     * 备份文件删除
     */
    public function databack_del()
    {
        $db= new Backup($this->config);
        $time=Request::param('time');
        $res=$db->delFile($time);
        if($res){
            $this->success('备份文件删除成功！');
        } else {
            $this->error('备份文件删除失败！');
        }
    }
    
    /**
     * 数据库导入
     */
    public function import()
    {
        $start=0;
        $db= new Backup($this->config);
        $time=Request::param('time');
        $start= $db->import($start,$time);
        $this->success('导入成功');
    }
    
}