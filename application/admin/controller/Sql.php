<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/13 0013
 * Time: 9:17
 */

namespace app\admin\controller;

use think\Db;
use think\facade\Config;
use think\facade\Request;

class Sql extends Common
{
    
    /*Sql命令行*/
    public function init()
    {
        return $this->fetch('sql_command');
    }
    
    /**
     * 执行SQL命令
     */
    public function do_sql()
    {
        $sql_execute = Config::get('database.')['sql_execute'];
        if (Request::param('sqlstr')) {
            if (!$sql_execute) {
                return json(['status' => 201, 'message' => '根据系统配置，不允许在线执行SQL命令！如有需要请更改sql_execute项的值为1']);
            } else {
                $sqlstr = Request::param('sqlstr');
                //$sqlstr = rtrim(trim($sqlstr), ';');
                $sqls = Request::param('action') == 'many' ? explode(';', $sqlstr) : array(0 => $sqlstr);
                
                foreach ($sqls as $sql) {
                    $sql = trim($sql);
                    if (empty($sql))
                        continue;
                    if (stristr($sql, '.php')) {
                        return json(['status' => 201, 'message' => 'ERROR : 检测到非法字符 “.php” ！']);
                    }
                    if (stristr($sql, 'outfile')) {
                        return json(['status' => 201, 'message' => 'ERROR : 检测到非法字符 “outfile”！']);
                    }
                    if (stristr($sql, 'concat')) {
                        return json(['status' => 201, 'message' => 'ERROR : 检测到非法字符 “concat” ！']);
                    }
                    if (preg_match("/^drop(.*)database/i", $sql)) {
                        return json(['status' => 201, 'message' => 'ERROR : 不允许删除数据库！']);
                    }
                    if (!preg_match("/^(?:UPDATE|DELETE|TRUNCATE|ALTER|DROP|FLUSH|INSERT|REPLACE|SET|CREATE)\\s+/i", $sql)) {
                        $data = Db::query($sql);
                        $keys = array_keys($data[0]);
                        $data_key = [];
                        foreach ($keys as $val) {
                            $data_key[] = '<th>' . $val . '</th>';
                        }
                        $data_row = '';
                        foreach ($data as $row) {
                            $data_row .= '<tr>';
                            foreach ($row as $val) {
                                $data_row .= '<td>' . $val . '</td>';
                            }
                            $data_row .= '</tr>';
                        }
                        return json(['status'=>200,'message'=>'<span style="color:green">OK : 执行成功！</span>','data'=>['data_key'=>$data_key,'data_row'=>$data_row]]);
                    } else {
                        $result = Db::execute($sql);
                        if($result){
                            return json(['status'=>200,'message'=>'<span style="color:green">OK : 执行成功！</span>']);
                        }else{
                            return json(['status'=>201,'message'=>'ERROR : 执行失败！']);
                            break;
                        }
                    }
                }
                
                
            }
            
        }
    }
    

    
}