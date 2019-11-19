<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/18 0018
 * Time: 16:44
 */

namespace app\admin\controller;

use think\facade\Request;

class Content
{
 
    public function init(){
        var_dump(Request::param('cid'));
    }
    
}