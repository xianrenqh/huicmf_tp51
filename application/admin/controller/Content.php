<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/18 0018
 * Time: 16:44
 */

namespace app\admin\controller;

use lib\Form;
use think\facade\Request;

class Content extends Common
{
 
    public function init()
    {
        echo "内容管理";
    }
    public function test(){
        $Form = new Form();
        $form_image = $Form->image('thumb');
        return $this->fetch('test',[
            'form_image' => $form_image
        ]);
    }
    
}