<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 返回json数组
 * @param $arr
 * @return string
 */
function return_json($arr = []){
    header('Content-Type:application/json; charset=utf-8');
    die(json_encode($arr));
}

/*
返回13位的时间戳
*/
function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}

/**
 * 获取系统配置信息
 * @param $key 键值，可为空，为空获取整个数组
 * @return array|string
 */
function get_config($key = '')
{
    if(cache('configs')){
        $configs= cache('configs');
    }else{
        $data = Db::name('config')->where('status',1)->select();
        $configs = array();
        foreach($data as $val){
            $configs[$val['name']] = $val['value'];
        }
        cache('configs',$configs);
    }
    if(!$key){
        return $configs;
    }else{
        return array_key_exists($key, $configs) ? $configs[$key] : '';
    }
}


/**
 * 转换字节数为其他单位
 * @param	string	$filesize	字节大小
 * @return	string	返回大小
 */
function sizecount($filesize) {
    if ($filesize >= 1073741824) {
        $filesize = round($filesize / 1073741824 * 100) / 100 .' GB';
    } elseif ($filesize >= 1048576) {
        $filesize = round($filesize / 1048576 * 100) / 100 .' MB';
    } elseif($filesize >= 1024) {
        $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
    } else {
        $filesize = $filesize.' Bytes';
    }
    return $filesize;
}


/**
 * 获取请求ip
 * @return ip地址
 */
function getip(){
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '127.0.0.1';
}



/**
 * 获取内容中的图片
 * @param string $content 内容
 * @return string
 */
function match_img($content){
    preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/', $content, $match);
    return !empty($match) ? $match[1] : '';
}

/**
 * 获取请求地区
 * @param $ip
 * @return 所在位置
 */
function get_address($ip){
    if($ip == '127.0.0.1') return '本地地址';
    $content = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip);
    $arr = json_decode($content, true);
    if(is_array($arr) && $arr['code']==0){
        return $arr['data']['country'].'-'.$arr['data']['region'].'-'.$arr['data']['city'];
    }else{
        return '未知';
    }
}


/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int   $pid
 * @param int   $level
 * @return array
 */
function array2level($array, $pid = 0, $level = 1)
{
    static $list = [];
    foreach ($array as $v) {
        if ($v['parentid'] == $pid) {
            $v['level'] = $level;
            $list[]     = $v;
            array2level($array, $v['id'], $level + 1);
        }
    }
    return $list;
}


/**
 * 检测输入中是否含有错误字符
 *
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
    $badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
    foreach($badwords as $value){
        if(strpos($string, $value) !== false) {
            return true;
        }
    }
    return false;
}

/*错误页。不跳转*/
function error2($msg){
    echo "<h2 align='center'>".$msg."</h2>";exit;
}

/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username 要检查的用户名
 * @return 	TRUE or FALSE
 */
function is_username($username) {
    $strlen = strlen($username);
    if(is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)){
        return false;
    } elseif ( 20 < $strlen || $strlen < 2 ) {
        return false;
    }
    
    //新增用户名不全是数字时，不能以数字开头
    if(preg_match('/^\d*$/', $username)){
        return true;
    }
    if(preg_match('/^\d\S/', $username)){
        return false;
    }
    
    return true;
}



/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	TRUE or FALSE
 */
function is_password($password) {
    $strlen = strlen($password);
    if($strlen >= 6 && $strlen <= 20)
        return true;
    return false;
}


/**
 * 对用户的密码进行加密
 * @param $pass 字符串
 * @return string 字符串
 */
function password($pass) {
    return md5(substr(md5(trim($pass)), 3, 26));
}


/**
 * 列出目录下所有文件
 *
 * @param   string $path     路径
 * @param   string $exts     扩展名
 * @param   array  $list     增加的文件列表
 * @return  array  所有满足条件的文件
 */
function dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/') $path = $path . '/';
    return $path;
}

/**
 * 删除目录及目录下面的所有文件
 *
 * @param   string $dir      路径
 * @return  bool   如果成功则返回 TRUE，失败则返回 FALSE
 */
function dir_delete($dir) {
    $dir = dir_path($dir);
    if (!is_dir($dir)) return FALSE;
    $list = glob($dir.'*');
    foreach($list as $v) {
        is_dir($v) ? dir_delete($v) : @unlink($v);
    }
    return @rmdir($dir);
}

/**
 * 字符截取
 * @param $string 要截取的字符串
 * @param $length 截取长度
 * @param $dot	  截取之后用什么表示
 * @param $code	  编码格式，支持UTF8/GBK
 */
function str_cut($string, $length, $dot = '...', $code = 'utf-8') {
    $strlen = strlen($string);
    if($strlen <= $length) return $string;
    $string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strcut = '';
    if($code == 'utf-8') {
        $length = intval($length-strlen($dot)-$length/3);
        $n = $tn = $noc = 0;
        while($n < strlen($string)) {
            $t = ord($string[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1; $n++; $noc++;
            } elseif(194 <= $t && $t <= 223) {
                $tn = 2; $n += 2; $noc += 2;
            } elseif(224 <= $t && $t <= 239) {
                $tn = 3; $n += 3; $noc += 2;
            } elseif(240 <= $t && $t <= 247) {
                $tn = 4; $n += 4; $noc += 2;
            } elseif(248 <= $t && $t <= 251) {
                $tn = 5; $n += 5; $noc += 2;
            } elseif($t == 252 || $t == 253) {
                $tn = 6; $n += 6; $noc += 2;
            } else {
                $n++;
            }
            if($noc >= $length) {
                break;
            }
        }
        if($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        $strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
        $replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
        $search_flip = array_flip($search_arr);
        for ($i = 0; $i < $maxi; $i++) {
            $current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
            if (in_array($current_str, $search_arr)) {
                $key = $search_flip[$current_str];
                $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
            }
            $strcut .= $current_str;
        }
    }
    return $strcut.$dot;
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
    $string = str_replace('%20','',$string);
    $string = str_replace('%27','',$string);
    $string = str_replace('%2527','',$string);
    $string = str_replace('*','',$string);
    $string = str_replace('"','',$string);
    $string = str_replace("'",'',$string);
    $string = str_replace(';','',$string);
    $string = str_replace('<','&lt;',$string);
    $string = str_replace('>','&gt;',$string);
    $string = str_replace("{",'',$string);
    $string = str_replace('}','',$string);
    $string = str_replace('\\','',$string);
    return $string;
}


/**
 * 单选框
 *
 * @param $name name
 * @param $val 默认选中值 如：1
 * @param $array 一维数组 如：array('交易成功', '交易失败', '交易结果未知');
 */
function radio($name, $val = '', $array = array()) {
    $string = '';
    foreach($array as $value) {
        $checked = trim($val)==trim($value) ? 'checked' : '';
        $string .= '<label class="option_label option_radio" >';
        $string .= '<input type="radio" name="'.$name.'" id="'.$name.'_'.$value.'" '.$checked.' value="'.$value.'">'.$value;
        $string .= '</label>';
    }
    return $string;
}

/**
 * 将数组转换为字符串，返回字符串
 *
 * @param	array	$data		数组
 * @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return	string	返回字符串，如果，data为空，则返回空
 */
function array2string($data, $isformdata = 1) {
    if($data == '' || empty($data)) return '';
    
    if($isformdata) $data = new_stripslashes($data);
    if (version_compare(PHP_VERSION,'5.3.0','<')){
        return addslashes(json_encode($data));
    }else{
        return addslashes(json_encode($data,JSON_FORCE_OBJECT));
    }
}

/**
 * 将字符串转换为数组，返回数组
 *
 * @param	string	$data	字符串
 * @return	array	返回数组格式，如果，data为空，则返回空数组
 */
function string2array($data) {
    $data = trim($data);
    if($data == '') return array();
    
    if(strpos($data, '{\\')===0) $data = stripslashes($data);
    $array=json_decode($data,true);
    return $array;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
    if(!is_array($string)) return stripslashes($string);
    foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
    return $string;
}

function public_gethtml($ftype = '', $val = '', $setting = '')
{
    $fieldtype = $ftype ? $ftype : (isset($_POST['fieldtype'])&&is_string($_POST['fieldtype']) ? safe_replace($_POST['fieldtype']) : 'textarea');
    if($fieldtype == 'textarea'){
        echo '<textarea name="value" class="layui-textarea"  placeholder="例如：214243830">'.$val.'</textarea>';
    }elseif(in_array($fieldtype, array('select', 'radio'))){
        if($val){
            echo \lib\Form::$fieldtype('value', $val, string2array($setting));
        }else{
            echo '<textarea name="setting" class="layui-textarea"  placeholder="选项用“|”分开，如“男|女|人妖”"></textarea> &nbsp;<input type="text" name="value" class="layui-input" style="width:180px" placeholder="默认值用配置值填写">';
        }
    }elseif($fieldtype == 'image' || $fieldtype == 'attachment'){
        echo \lib\Form::$fieldtype('value', $val);
    }else{
        echo '<textarea name="value" class="layui-textarea"  placeholder="例如：214243830">'.$val.'</textarea>';
    }
}

// CurlPOST数据提交-----------------------------------------
function cmf_curl_get($url,$heads=array(),$cookie='')
{
    $ch = @curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36');
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    if(!empty($cookie)){
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }
    if(count($heads)>0){
        curl_setopt ($ch, CURLOPT_HTTPHEADER , $heads );
    }
    $response = @curl_exec($ch);
    if(curl_errno($ch)){//出错则显示错误信息
        //print curl_error($ch);die;
    }
    curl_close($ch); //关闭curl链接
    return $response;//显示返回信息
}