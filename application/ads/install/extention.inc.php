<?php
/**
 * Created by PhpStorm.
 * Date: 2019/11/18 0018
 * Time: 8:18
 */

//引用数据库Db类
use think\Db;

$menu=Db::name('menu');

//写入父级菜单
$parentid = $menu->insertGetId(['name'=>'广告管理','parentid'=>4,'m'=>'ads','c'=>'ads','a'=>'init','data'=>'','listorder'=>1,'display'=>1]);

//写入对应子级菜单
$data = [
    ['name'=>'添加广告', 'parentid'=>$parentid, 'm'=>'ads', 'c'=>'ads', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'删除一条广告', 'parentid'=>$parentid, 'm'=>'ads', 'c'=>'ads', 'a'=>'del_one', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'修改广告', 'parentid'=>$parentid, 'm'=>'ads', 'c'=>'ads', 'a'=>'update', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
];
$menu->insertAll($data);
