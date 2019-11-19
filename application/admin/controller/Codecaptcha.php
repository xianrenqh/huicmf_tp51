<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/7 0007
 * Time: 11:28
 */

namespace app\admin\controller;
use think\facade\Request;
use think\captcha\Captcha;

class Codecaptcha
{
    
    //自定义验证码类
    public function verify()
    {
        $config =    [
            // 验证码字体大小
            'fontSize'    => 50,
            // 验证码位数
            'length'      => 5,
            // 关闭验证码杂点
            'useNoise'    => false,
            //中文验证
            'useZh'      => false,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
        
    }
    
    /**
     *获取bing背景图
     */
    public function getbing_bgpic(){
        $idx = Request::get('idx');
        $api = "http://cn.bing.com/HPImageArchive.aspx?format=js&idx=$idx&n=1";
        $data = self::object2array(json_decode(self::get_url($api)));
        $pic_url = $data['images'][0]->{'url'}; //获取数据里的图片地址
        if($pic_url){
            $images_url  ="https://cn.bing.com/".$pic_url;      //如果图片地址存在，则输出图片地址
        }else{
            $images_url="https://s1.ax1x.com/2018/12/10/FGbI81.jpg";     //否则输入一个自定义图
        }
        header("Location: $images_url");    //header跳转
        
    }
    private  function get_url($url)
    {
        $ch = curl_init();
        $header[] = "";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
    private function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }
    
    
    
}