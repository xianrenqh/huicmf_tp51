<?php
use think\Db;
$menu = Db::name('menu');
$menu->where(['m'=>'link','c'=>'links'])->delete();