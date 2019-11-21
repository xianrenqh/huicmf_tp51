<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/20 0020
 * Time: 9:29
 */

namespace app\link\model;

use think\Model;

class Link extends Model
{
    protected $connection = 'hui_link';
    
    public function infoData($where,$field='*')
    {
        if(empty($where) || !is_array($where)){
            return ['code'=>1001,'msg'=>'参数错误'];
        }
        $info = $this->field($field)->where($where)->find();
        if(empty($info)){
            return ['code'=>1002,'msg'=>'获取数据失败'];
        }
        $info = $info->toArray();
        return ['code'=>1,'msg'=>'获取成功','info'=>$info];
    }
    
}