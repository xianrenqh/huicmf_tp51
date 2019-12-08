<?php
/**
 * User: 程序猿
 * Date: 2019/12/08
 * Time: 10:33
 */

namespace app\file\controller;
use app\admin\controller\Common;
class File extends Common
{
    
    public function index($dirname = null)
    {
        $dirname = urldecode($dirname);
        if ($dirname) {
            // 是否能够返回上一级
            if (stripos($dirname, ROOT_PATH) === 0 && stripos($dirname, ROOT_PATH . '..') === false) {
                if (file_exists($this->g_u($dirname, false))) {
                    chdir($this->g_u($dirname, false));// 切换目录
                }
            }
        }
        $rootpath = getcwd();// 获取工作目录
        $data = [];
        
        $num['file'] = 0;
        $num['dir'] = 0;
        $dirFile = opendir($rootpath);
        while ($fileName = readdir($dirFile)) {
            if ($fileName != '.' && $fileName != '..') {
                // 是否是目录以选择图标并计算目录内的文件大小
                if (is_dir($fileName)) {
                    // 目录
                    $fileInfo['icon'] = '#icon-wenjianjia';
                    $fileInfo['size'] = $this->dirsize($fileName);
                    $fileInfo['dir'] = 1;
                    $num['dir']++;
                } else {
                    // 文件
                    $fileInfo['icon'] = $this->geticon($fileName);
                    $fileInfo['size'] = filesize($fileName);
                    $fileInfo['dir'] = 0;
                    $num['file']++;
                }
                $fileInfo['dirname'] = $this->g_u($rootpath . DIRECTORY_SEPARATOR . $fileName);// 获取绝对路径
                $fileInfo['name'] = $this->g_u($fileName);
                $fileInfo['ctime'] = filectime($fileName);
                $fileInfo['mtime'] = filemtime($fileName);
                $data[] = $fileInfo;
            }
        }
        
        // 排序
        array_multisort(array_column($data, 'dir'), SORT_DESC, $data);
        $page = $this->getpage($data, 10, input('page'));
        return $this->fetch('index', [
            'dirs' => $page['data'],
            'page' => $page['page'],
            'path' => $this->g_u($rootpath),
            'uppath' => $this->g_u($rootpath) . '/' . '..',
            'num' => $num,
        ]);
    }
    
    // 删除
    public function del()
    {
        if (request()->isPost()) {
            $data = $this->g_u(urldecode(input('data')), false);
            if (is_dir($data)) {
                $count = scandir($data);
                if (count($count) === 2) {
                    if (rmdir($data)) {
                        return $this->result('', 1, '目录删除成功');
                    } else {
                        return $this->result('', 0, '目录删除失败');
                    }
                } else {
                    $res = $this->deldir($data);
                    if ($res) {
                        return $this->result('', 1, '目录删除成功');
                    } else {
                        return $this->result('', 0, '目录删除失败');
                    }
                }
            }
            if (is_file($data)) {
                if (unlink($data)) {
                    return $this->result('', 1, '文件删除成功');
                } else {
                    return $this->result('', 0, '文件删除失败');
                }
            }
            return $data;
        }
    }
    
    // 重命名
    public function rname()
    {
        if (request()->isPost()) {
            
            $oldName = urldecode(input('oldname'));
            
            $name = input('newname');
            
            $newName = dirname($oldName) . '/' . $name;
            
            if (file_exists($newName) && is_writable($oldName)) {
                return $this->result('', 0, $name . ' 文件名已存在');
            }
            
            try {
                $is = rename($this->g_u($oldName, false), $this->g_u($newName, false));
            } catch (\Exception $e) {
                return $this->result('', 0, '文件名修改失败');
            }
            if ($is) {
                return $this->result('', 1, '文件名修改成功');
            } else {
                return $this->result('', 0, '文件名修改失败');
            }
            return $this->result('', 0, '操作异常');
            
        }
    }
    
    // 文件下载
    public function down()
    {
        // ajax判断一遍文件是否存在，只是为了前台提示，暂时可有可无。
        if (request()->isAjax()) {
            $data = urldecode(input('file'));
            $file = $this->g_u($data, false);
            if (!file_exists($file)) {
                return $this->result('', 0, '文件不存在');
            }
            return $this->result('', 1, '');
        }
        $data = urldecode(input('file'));
        $file = $this->g_u($data, false);
        $filename = basename($file);
        // 设置头
        header('Content-Disposition:attachment;filename=' . $filename);
        readfile($file);// 下载
        
    }
    
    // 文件编辑
    public function edit()
    {
        if (input('dosubmit')) {
            $data = input('text');
            $file = urldecode(input('filename'));
            if (file_put_contents($file, $data)) {
                return $this->result('', 1, '修改成功');
            }
            
        }
        
        $data = urldecode(input('file'));
        $exts = ['PHP', 'HTML', 'JS', 'CSS', 'TXT', 'JSON', 'XML', 'HTACCESS'];
        $ext = strtoupper(pathinfo($data, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $exts)) {
            return $this->result('', 0, '该文件不支持编辑');
        }
        
        
        // return $data;
        if (empty($data) || !file_exists($data)) {
            return $this->result('', 0, '文件不存在');
        }
        
        $code = htmlentities(file_get_contents($data), ENT_COMPAT, 'UTF-8');
        return $this->fetch('edit', [
            'code' => $code,
            'ext' => strtolower($ext),
            'filename'=>$data
        ]);
    }
    
    // 分页
    private function getpage($arr, $list = 3, $curr)
    {
        $arrCount = count($arr);
        $total = ceil($arrCount / $list);// 获取页数
        if ($curr <= 0) {
            $curr = 1;
        }
        if ($curr > $total) {
            $curr = $total;
        }
        $data = array_slice($arr, ($curr - 1) * $list, $list);// 按页数分割数组
        
        return [
            'data' => $data,
            'page' => [
                'count' => $arrCount,
                'limit' => $list,
                'curr' => $curr,
            ],
        ];
    }
    
    // 返回文件图标
    private function geticon($file)
    {
        // 获取文件格式
        $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
        $ico = '';
        switch ($ext) {
            case 'PHP':
                $ico = '#icon-php';
                break;
            case 'HTML':
                $ico = '#icon-html';
                break;
            case 'JS':
                $ico = '#icon-js';
                break;
            case 'CSS':
                $ico = '#icon-css';
                break;
            case 'JSON':
                $ico = '#icon-json';
                break;
            case 'JPG':
                $ico = '#icon-Jpg';
                break;
            case 'PNG':
                $ico = '#icon-png';
                break;
            case 'GIF':
                $ico = '#icon-gif';
                break;
            case 'HTACCESS':
                $ico = '#icon-htaccess';
                break;
            case 'ICO':
                $ico = '#icon-img';
                break;
            case 'BMP':
                $ico = '#icon-bmp';
                break;
            default:
                $ico = '#icon-file';
                break;
        }
        return $ico;
    }
    
    public function charset($fileName)
    {
        $encode = mb_detect_encoding($fileName, ['ASCII', 'GB2312', 'GBK', 'UTF-8']);
        if ($encode == 'UTF-8') {
            return $fileName;
        } else {
            // return iconv($encode,'UTF-8',$fileName);
            return mb_convert_encoding($fileName, 'UTF-8', $encode);
            
        }
    }
    
    /**
     * gb2312转为utf-8
     * @param $str
     * @param bool $is true：其它编码转utf8   false：utf8转gb2312
     * @return string
     */
    public function g_u($str, $is = true)
    {
        $encode = mb_detect_encoding($str, ['ASCII', 'UTF-8', 'GB2312', 'GBK','CP936']);
        // return $encode;
        if ($encode == 'UTF-8' && $is) {
            return $str;
        } elseif ($is == false && PHP_OS != 'Linux') {
            return iconv($encode, 'GB2312', $str);
        } else {
            return iconv($encode, 'UTF-8', $str);
        }
    }
    
    // 删除含有目录的文件
    public function deldir($path)
    {
        //如果是目录则继续
        if (is_dir($path)) {
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $dirs = opendir($path);
            while ($file = readdir($dirs)) {
                
                if ($file != '.' && $file != '..') {
                    //如果是目录则递归子目录，继续操作
                    if (is_dir($path . '/' . $file)) {
                        $this->deldir($path . '/' . $file);
                    } else {
                        unlink($path . '/' . $file);
                    }
                }
            }
            closedir($dirs);
            // 最后删除要处理的根目录
            $res = rmdir($path);
            return $res;
        }
    }
    
    // 获取目录的大小
    public function dirsize($dirname)
    {
        $dir = opendir($dirname);
        $size = 0;
        while ($fileName = readdir($dir)) {
            if ($fileName != '.' and $fileName != '..') {
                $path = $dirname . '/' . $fileName;// 获取文件夹下的目录
                if (is_dir($path)) {
                    $size += $this->dirsize($path);
                } elseif (is_file($path)) {
                    $size += filesize($path);
                }
            }
        }
        
        return $size;
    }
}
