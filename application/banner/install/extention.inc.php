<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/18 0018
 * Time: 8:18
 */

//引用数据库Db类
use think\Db;

$menu=Db::name('menu');

//写入父级菜单
$parentid = $menu->insertGetId(['name'=>'轮播图管理','parentid'=>4,'m'=>'banner','c'=>'banner','a'=>'init','data'=>'','listorder'=>1,'display'=>1]);

//写入对应子级菜单
$data = [
    ['name'=>'添加轮播', 'parentid'=>$parentid, 'm'=>'banner', 'c'=>'banner', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'删除轮播', 'parentid'=>$parentid, 'm'=>'banner', 'c'=>'banner', 'a'=>'del', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'添加轮播分类', 'parentid'=>$parentid, 'm'=>'banner', 'c'=>'banner', 'a'=>'cat_add', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'管理轮播分类', 'parentid'=>$parentid, 'm'=>'banner', 'c'=>'banner', 'a'=>'cat_manage', 'data'=>'', 'listorder'=>0, 'display'=>'0']
];
$menu->insertAll($data);
