<?php
/**
 * Created by PhpStorm.
 * User: 程序猿
 * Date: 2019/11/22 0022
 * Time: 17:44
 */

namespace app\admin\controller;

use think\App;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\facade\Request;
use think\Image;
use lib\FtpLib;

class Ueditor extends Controller
{
    
    private $userid;
    private $username;
    
    private $upload_mode;
    private $ftp_host;
    private $ftp_port;
    private $ftp_user;
    private $ftp_pwd;
    private $ftp_url;
    private $ftp_path_article;
    private $ftp_path_video;
    private $ftp_path_comic;
    
    function __construct()
    {
        parent::__construct();
        $this->userid = Session::get('adminid') ? Session::get('adminid') : (Session::get('_userid') ? Session::get('_userid') : 0);
        $this->username = Session::get('adminname');
        $this->isadmin = Session::get('roleid') ? 1 : 0;
        
        $this->upload_mode = get_config('upload_mode');
        $this->ftp_host = get_config('ftp_host');
        $this->ftp_port = get_config('ftp_port');
        $this->ftp_user = get_config('ftp_user');
        $this->ftp_pwd = get_config('ftp_pwd');
        $this->ftp_url = get_config('ftp_url');
        $this->ftp_path_article = get_config('ftp_path_article');
        $this->ftp_path_video = get_config('ftp_path_video');
        $this->ftp_path_comic = get_config('ftp_path_comic');
        
    }
    
    public function index()
    {
        header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
        date_default_timezone_set("Asia/chongqing");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");
        
        /* 旧，调用ueditor文件夹下的config.json配置文件
        $app_path = str_replace("\application\\", "", APP_PATH);
        $conf_path = file_get_contents($app_path . '\public\static\lib\ueditor\1.4.3.3\php\config.json');
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $conf_path), true);
        */
        //新，调用config/ueditor.php
        $CONFIG = config('ueditor.');
        $action = input('action');
        
        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;
            //上传图片
            case 'uploadimage':
                $fieldName = $CONFIG['imageFieldName'];
                $result = $this->upFile($fieldName);    //使用tp上传
                break;
            //上传涂鸦
            case 'uploadscrawl':
                $config = array("pathFormat" => $CONFIG['scrawlPathFormat'], "maxSize" => $CONFIG['scrawlMaxSize'], "allowFiles" => $CONFIG['scrawlAllowFiles'], "oriName" => "scrawl.png");
                $fieldName = $CONFIG['scrawlFieldName'];
                $base64 = "base64";
                $result = $this->upBase64($config, $fieldName);
                break;
            //上传视频
            case 'uploadvideo':
                $fieldName = $CONFIG['videoFieldName'];
                $result = $this->upFile($fieldName);
                break;
            //上传文件
            case 'uploadfile':
                $fieldName = $CONFIG['fileFieldName'];
                $result = $this->upFile($fieldName);
                break;
            //列出图片
            case 'listimage':
                $allowFiles = $CONFIG['imageManagerAllowFiles'];
                $listSize = $CONFIG['imageManagerListSize'];
                $path = $CONFIG['imageManagerListPath'];
                $get = $_GET;
                $result = $this->fileList($allowFiles, $listSize, $get);
                break;
            //列出文件
            case 'listfile':
                $allowFiles = $CONFIG['fileManagerAllowFiles'];
                $listSize = $CONFIG['fileManagerListSize'];
                $path = $CONFIG['fileManagerListPath'];
                $get = $_GET;
                $result = $this->fileList($allowFiles, $listSize, $get);
                break;
            //抓取远程文件
            case 'catchimage':
                $config = array("pathFormat" => $CONFIG['catcherPathFormat'], "maxSize" => $CONFIG['catcherMaxSize'], "allowFiles" => $CONFIG['catcherAllowFiles'], "oriName" => "remote.png");
                $fieldName = $CONFIG['catcherFieldName'];
                //抓取远程图片
                $list = array();
                isset($_POST[$fieldName]) ? $source = $_POST[$fieldName] : $source = $_GET[$fieldName];
                
                foreach ($source as $imgUrl) {
                    $info = json_decode($this->saveRemote($config, $imgUrl), true);
                    array_push($list, array("state" => $info["state"], "url" => $info["url"], "size" => $info["size"], "title" => htmlspecialchars($info["title"]), "original" => htmlspecialchars($info["original"]), "source" => htmlspecialchars($imgUrl)));
                }
                
                $result = json_encode(array('state' => count($list) ? 'SUCCESS' : 'ERROR', 'list' => $list));
                break;
            default:
                $result = json_encode(array('state' => '请求地址出错'));
                break;
        }
        
        //输出结果
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array('state' => 'callback参数不合法'));
            }
        } else {
            echo $result;
        }
    }
    
    //上传文件
    private function upFile($fieldName)
    {
        switch (input('act')){
            case "article":
                $file_path =get_config('ftp_path_article');
                break;
            case "video":
                $file_path =get_config('ftp_path_video');
                break;
            case "comic":
                $file_path =get_config('ftp_path_comic');
                break;
            default:
                $file_path='/uploads/ueditor/';
                break;
        }
        switch ($this->upload_mode){
            case 'local':   //上传到本地服务器
                $file = request()->file($fieldName);
                $ext = str_replace("|",",",get_config('upload_types'));
                $conf_size = get_config('upload_maxsize')*1024;
                $info = $file->validate(['size'=>$conf_size,'ext'=>$ext])->move(".".$file_path);
                $fileInfo = $info->getInfo();
                $fileOldName = $fileInfo['name'];
                if ($info) {//上传成功
                    $filename=str_replace('\\','/',$info->getSaveName());
                    $fname = $file_path.$filename;
                    $imgArr = explode(',', 'jpg,gif,png,jpeg,bmp,tif,ttf');
                    $imgExt = strtolower($info->getExtension());
                    $isImg = in_array($imgExt, $imgArr);
                    if ($isImg) {//如果是图片，开始处理
                        //获取水印配置
                        $this->add_water(".".$fname);
                        //写入数据库
                    }
                    $data = array('state' => 'SUCCESS', 'url' => str_replace("./uploads/", "/uploads/", $fname), 'title' => $info->getFilename(), 'original' => $info->getFilename(), 'type' => '.' . $info->getExtension(), 'size' => $info->getSize(),);
                } else {
                    $data = array('state' => $file->getError(),);
                }
                return json_encode($data);
                break;
            case 'Ftp':     //上传到ftp服务器
                $ftp_conn =  new FtpLib();
                if($ftp_conn->connect()==1){
                    $File = $_FILES[$fieldName];
                    $get_ftp_url = get_config('ftp_url');
                    $file_p=$File['tmp_name'];
                    $file_name = md5(getMillisecond());
                    $file_ext = $this->get_file_ext($File['name']);
                    $newpath = $file_path.$file_name.".".$file_ext;
                    $this->add_water($file_p);
                    $dd=$ftp_conn->up_file($file_p,$newpath);
                    if($dd['code']==1){
                        $data = (['state'=>'SUCCESS','title'=>$get_ftp_url.$newpath,'filetype'=>$file_ext,'url'=>$get_ftp_url.$newpath,'msg'=>$dd['msg']]);
                    }
                }else{
                    $data =(['state'=>'FTP配置错误，请检查！！！','msg'=>'FTP配置错误，请检查！！！']);
                }
                return json_encode($data);
                break;
        }
    }
    
    //添加水印
    private function add_water($imginfo)
    {
        //获取水印配置
        if(get_config('watermark_enable')){
            $waterpic = "./static/water/".get_config('watermark_name');
            $pic_url =$imginfo;
            $image=Image::open($pic_url);
            $image->water($waterpic,get_config('watermark_position'),get_config('watermark_touming'))->save($pic_url);
        }else{
            return true;
        }
    }
    
    //列出图片
    private function fileList($allowFiles, $listSize, $get)
    {
        $dirname = './uploads/';
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        
        //获取参数
        $size = isset($get['size']) ? htmlspecialchars($get['size']) : $listSize;
        $start = isset($get['start']) ? htmlspecialchars($get['start']) : 0;
        $end = $start + $size;
        
        //获取文件列表
        $path = $dirname;
        $files = $this->getFiles($path, $allowFiles);
        if (!count($files)) {
            return json_encode(array("state" => "no match file", "list" => array(), "start" => $start, "total" => count($files)));
        }
        
        //获取指定范围的列表
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }
        
        //返回数据
        $result = json_encode(array("state" => "SUCCESS", "list" => $list, "start" => $start, "total" => count($files)));
        
        return $result;
    }
    
    /*
  * 遍历获取目录下的指定类型的文件
  * @param $path
  * @param array $files
  * @return array
 */
    private function getFiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path))
            return null;
        if (substr($path, strlen($path) - 1) != '/')
            $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                //dump($path);
                if (is_dir($path2)) {
                    $this->getFiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(" . $allowFiles . ")$/i", $file)) {
                        $files[] = array('url' => substr($path2, 1), 'mtime' => filemtime($path2));
                    }
                }
            }
        }
        
        return $files;
    }
    
    //抓取远程图片
    private function saveRemote($config, $fieldName)
    {
        $imgUrl = htmlspecialchars($fieldName);
        $imgUrl = str_replace("&amp;", "&", $imgUrl);
        
        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            $data = array('state' => '链接不是http链接',);
            return json_encode($data);
        }
        //获取请求头并检测死链
        $heads = get_headers($imgUrl);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $data = array('state' => '链接不可用',);
            return json_encode($data);
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $config['allowFiles']) || stristr($heads['Content-Type'], "image")) {
            $data = array('state' => '链接contentType不正确',);
            return json_encode($data);
        }
        
        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(array('http' => array('follow_location' => false // don't follow redirects
        )));
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);
        
        $dirname = './public/uploads/remote/';
        $file['oriName'] = $m ? $m[1] : "";
        $file['filesize'] = strlen($img);
        $file['ext'] = strtolower(strrchr($config['oriName'], '.'));
        $file['name'] = uniqid() . $file['ext'];
        $file['fullName'] = $dirname . $file['name'];
        $fullName = $file['fullName'];
        
        //检查文件大小是否超出限制
        if ($file['filesize'] >= ($config["maxSize"])) {
            $data = array('state' => '文件大小超出网站限制',);
            return json_encode($data);
        }
        
        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $data = array('state' => '目录创建失败',);
            return json_encode($data);
        } else if (!is_writeable($dirname)) {
            $data = array('state' => '目录没有写权限',);
            return json_encode($data);
        }
        
        //移动文件
        if (!(file_put_contents($fullName, $img) && file_exists($fullName))) { //移动失败
            $data = array('state' => '写入文件内容错误',);
            return json_encode($data);
        } else { //移动成功
            $data = array('state' => 'SUCCESS', 'url' => substr($file['fullName'], 1), 'title' => $file['name'], 'original' => $file['oriName'], 'type' => $file['ext'], 'size' => $file['filesize'],);
        }
        
        return json_encode($data);
    }
    
    /*
	 * 处理base64编码的图片上传
	 * 例如：涂鸦图片上传
	*/
    private function upBase64($config, $fieldName)
    {
        $base64Data = $_POST[$fieldName];
        $img = base64_decode($base64Data);
        
        $dirname = './public/uploads/scrawl/';
        $file['filesize'] = strlen($img);
        $file['oriName'] = $config['oriName'];
        $file['ext'] = strtolower(strrchr($config['oriName'], '.'));
        $file['name'] = uniqid() . $file['ext'];
        $file['fullName'] = $dirname . $file['name'];
        $fullName = $file['fullName'];
        
        //检查文件大小是否超出限制
        if ($file['filesize'] >= ($config["maxSize"])) {
            $data = array('state' => '文件大小超出网站限制',);
            return json_encode($data);
        }
        
        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $data = array('state' => '目录创建失败',);
            return json_encode($data);
        } else if (!is_writeable($dirname)) {
            $data = array('state' => '目录没有写权限',);
            return json_encode($data);
        }
        
        //移动文件
        if (!(file_put_contents($fullName, $img) && file_exists($fullName))) { //移动失败
            $data = array('state' => '写入文件内容错误',);
        } else { //移动成功
            $data = array('state' => 'SUCCESS', 'url' => substr($file['fullName'], 1), 'title' => $file['name'], 'original' => $file['oriName'], 'type' => $file['ext'], 'size' => $file['filesize'],);
        }
        
        return json_encode($data);
    }
    
    //获取上传文件后缀
    private function get_file_ext($file_name)
    {
        $temp_arr = explode(".", $file_name);
        $file_ext = array_pop($temp_arr);
        $file_ext = trim($file_ext);
        $file_ext = strtolower($file_ext);
        return $file_ext;
    }
    
}