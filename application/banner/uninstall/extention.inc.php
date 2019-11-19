<?php
use think\Db;
$menu = Db::name('menu');
$menu->where(['m'=>'banner','c'=>'banner'])->delete();