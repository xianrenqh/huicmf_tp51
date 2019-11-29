<?php
/**
 * Created by PhpStorm.
 * User: 程序猿
 * Date: 2019-11-27
 * Time: 14:29
 * Info:
 */

namespace app\admin\controller;


class Type extends Common
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function init()
    {
        $listTree = model('Type')->listTreeData();
        $total = model('Type')->countData('1=1');
        return $this->fetch('type_list', ['list' => $listTree,'total'=>$total]);
    }
    
    public function add()
    {
        $list = model('Type')->getListData();
        if (input('dosubmit')) {
            $res = model('Type')->saveData(input());
            return json(['status'=>$res['code'],'message'=>$res['msg']]);
        } else {
            return $this->fetch('type_add', ['typelist' => $list,'thisid'=>input('parentid')]);
        }
        
    }
    
    public function edit()
    {
        if(input('dosubmit')){
            $res = model('Type')->saveData(input());
            return json($res);
        }else{
            $typelist = model('Type')->getListData();
            $res = model('Type')->findData(input('id'));
            if($res['code']>1){
                $this->error($res['msg']);
            }else{
                $data =$res['data'];
            }
            return $this->fetch('type_edit',['typelist'=>$typelist,'data'=>$data]);
        }
    }
    
    public function del()
    {
        $res = model('Type')->delData(input('id'));
        return json($res);
    }
    
    
    public function change_status()
    {
        $res = model('Type')->statusChange(input());
        return json($res);
    }
    
    public function order()
    {
        if(input('dosubmit')){
            foreach(input('listorders') as $id=>$listorder){
                $dataArr = ['type_id'=>$id,'type_sort'=>$listorder];
                $dataArr1[]=$dataArr;
            }
            model('Type')->orderChange($dataArr1);
            $this->success('操作成功！', 'init', 1, 2);
        }else{
            $this->error('操作失败');
        }
        

    }
    
}