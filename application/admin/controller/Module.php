<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/15 0015
 * Time: 9:29
 */

namespace app\admin\controller;

use app\admin\model\ModuleApi;
use think\Db;
use think\facade\Cache;
use think\facade\Request;
use think\facade\View;

class Module extends Common
{

    private $module;

    /*模块列表*/
    public function init()
    {


        define('INSTALL', true);
        $dirs     = $dirs_arr = [];
        $dirs     = glob(APP_PATH.'*', GLOB_ONLYDIR);
        $dirs_arr = [];
        foreach ($dirs as $d) {
            $dirs_arr[] = basename($d);
        }
        $total     = count($dirs_arr);
        $dirs_arr  = array_chunk($dirs_arr, 500, true);
        $page      = Request::param('page') ? Request::param('page') : 1;
        $directory = isset($dirs_arr[($page - 1)]) ? $dirs_arr[($page - 1)] : [];
        $data      = Db::name('module')->whereIn('module', $directory)->order('module asc')->select();
        foreach ($data as $val) {
            $modules[$val['module']] = $val;
        }
        View::assign(['directory' => $directory, 'total' => $total, 'data' => $data, 'modules' => $modules]);

        return $this->fetch('module_list');
    }

    /**
     * 模块安装
     *
     * @param string $module 模块名
     */
    public function install()
    {
        define('INSTALL', true);
        $this->module = Request::param('module');
        $module_api   = new ModuleApi();
        if (Request::param('dosubmit')) {
            if ($module_api->install($this->module)) {
                Cache::clear();
                $this->success('安装成功！', url('cache'));
            } else {
                $this->error($module_api->error_msg, url('init'));
            }
        } else {
            if ( ! $module_api->check($this->module)) {
                $this->error($module_api->error_msg, url('init'), '', 6);
            }
            $config = include APP_PATH.$this->module.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'config.inc.php';
            if ( ! is_array($config)) {
                $this->error('配置文件错误！', url('init'), '', 6);
            }
            extract($config);

            return $this->fetch('module_config', ['config' => $config]);
        }
    }

    /**
     * 模块卸载
     */
    public function uninstall()
    {
        if ( ! Request::param('module')) {
            $this->error('模块名称为空！');
        }
        $module_api = new ModuleApi();
        if ($module_api->uninstall(Request::param('module'))) {
            Cache::clear();
            $this->success('卸载成功!', url('cache'));
        } else {
            $this->error($module_api->error_msg, url('init'));
        }
    }

    /**
     * 更新模块缓存
     */
    public function cache()
    {
        echo '<script type="text/javascript">parent.location.reload();</script>';
    }

    function _dir_path($path)
    {
        $path = str_replace('\\', '/', $path);
        if (substr($path, -1) != '/') {
            $path = $path.'/';
        }

        return $path;
    }

}