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
$parentid = $menu->insertGetId(['name'=>'友情链接管理','parentid'=>4,'m'=>'link','c'=>'links','a'=>'init','data'=>'','listorder'=>1,'display'=>1]);

//写入对应子级菜单
$data = [
    ['name'=>'添加链接', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'links', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'删除多条链接', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'links', 'a'=>'del_all', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'删除一条链接', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'links', 'a'=>'del_one', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'修改链接', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'links', 'a'=>'update', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'检测链接', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'links', 'a'=>'check', 'data'=>'', 'listorder'=>0, 'display'=>'0'],
    ['name'=>'排序链接', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'links', 'a'=>'order', 'data'=>'', 'listorder'=>0, 'display'=>'0']
];
$menu->insertAll($data);
