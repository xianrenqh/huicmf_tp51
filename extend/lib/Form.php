<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/12 0012
 * Time: 10:05
 */

namespace lib;
use think\facade\Request;

class Form
{
    
    /**
     * textarea
     * @param $name name
     * @param $value 默认值 如：YzmCMS
     * @param $required  是否为必填项 默认false
     * @param $width  宽度 如：100
     */
    public static function textarea($name = '', $value = '', $required=false, $width = 0) {
        $string = '<textarea name="'.$name.'" id="'.$name.'" ';
        if($width) $string .= ' width="'.$width.'px" ';
        if($required) $string .= ' required="required" ';
        $string .= '>'.$value.'</textarea>';
        return $string;
    }
    
    /**
     * 单选框
     *
     * @param $name name
     * @param $val 默认选中值 如：1
     * @param $array 一维数组 如：array('交易成功', '交易失败', '交易结果未知');
     */
    public static function radio($name, $val = '', $array = array()) {
        $string = '';
        $string .='<div class="layui-input-block" id="status" style="margin-left:0">';
        foreach($array as $value) {
            $checked = trim($val)==trim($value) ? 'checked' : '';
            $string .='<input type="radio" name="'.$name.'" id="'.$name.'_'.$value.'" '.$checked.' value="'.$value.'" title="'.$value.'">';
        }
        $string .='</div>';
        return $string;
    }
    
    /**
     * 下拉选择框
     * @param $name name
     * @param $val 默认选中值 如：1
     * @param $array 一维数组 如：array('交易成功', '交易失败', '交易结果未知');
     * @param $default_option 提示词 如：请选择交易
     */
    public static function select($name, $val = 0, $array = array(), $default_option = '') {
        $string = '<select name="'.$name.'" id="'.$name.'" class="select">';
        if($default_option) $string .= "<option value=''>$default_option</option>";
        if(!is_array($array) || count($array)== 0) return false;
        $ids = array();
        if(isset($val)) $ids = explode(',', $val);
        foreach($array as $value) {
            $selected = in_array($value, $ids) ? 'selected' : '';
            $string .= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
        }
        $string .= '</select>';
        return $string;
    }
    
    /**
     * 图片上传
     *
     * @param $name name
     * @param $val 默认值
     * @param $style 样式
     */
    public static function image($name, $val = '', $style = 'width:370px', $iscropper = false) {
        $string='
            <div class="layui-input-inline" style="width: 45%">
                <input type="text" name="'.$name.'"  value="'.$val.'"  onmouseover="hui_img_preview(\''.$name.'\', this.value)" onmouseout="layer.closeAll();" id="'.$name.'"   autocomplete="off" class="layui-input">
            </div>
            <div class="layui-input-inline" style="width: 120px">
                <button type="button" class="layui-btn" id="test3" onclick="hui_upload_att(\''.url('attachment/api/upload_box', array('module'=>Request::module(), 'pid'=>$name)).'\')" ><i class="layui-icon">&#xe67c;</i>浏览文件</button>
            </div>
        ';
        if($iscropper) $string = $string .' '.self::cropper($name);
        return $string;
    }
    
    /**
     * 图像裁剪
     *
     * @param $cid 		原图所在input的id
     * @param $spec  	裁剪规则，1：3*2, 2:4*3, 3:1*1
     */
    public static function cropper($cid, $spec=1) {
        $string='
        <div class="layui-input-inline" style="width: 45%">
            <button type="button" class="layui-btn" onclick="hui_img_cropper(\''.$cid.'\', \''.url('attachment/api/img_cropper', array('spec'=>$spec)).'\')" ><i class="layui-icon">&#xe663;</i>裁剪图片</button>
            </div>
            ';
        return $string;
    }
    
    /**
     * 附件上传
     *
     * @param $name name
     * @param $val 默认值
     * @param $style 样式
     */
    public static function attachment($name, $val = '', $style='width:370px') {
        $string = '
        <div class="layui-input-inline" style="width: 45%">
                <input type="text" name="'.$name.'"  value="'.$val.'"  id="'.$name.'"   autocomplete="off" class="layui-input">
            </div>
            <div class="layui-input-inline" style="width: 120px">
                <button type="button" class="layui-btn" id="test3" onclick="hui_upload_att(\''.url('attachment/api/upload_box', array('module'=>Request::module(), 'pid'=>$name,'t'=>2)).'\')" ><i class="layui-icon">&#xe67c;</i>浏览文件</button>
            </div>
        </div>
        ';
        return $string;
    }
    
    
}