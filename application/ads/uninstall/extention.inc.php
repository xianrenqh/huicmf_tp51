<?php
use think\Db;
$menu = Db::name('menu');
$menu->where(['m'=>'ads','c'=>'ads'])->delete();