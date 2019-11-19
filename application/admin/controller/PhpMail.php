<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/11/13 0013
 * Time: 11:12
 */

namespace app\admin\controller;
use think\Db;
use PHPMailer\PHPMailer\PHPMailer;
use PhpMailer\PHPMailer\Exception;

class PhpMail
{
    public function email($mail_to,$mail_title,$mail_body,$mail_altbody=''){
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //服务器配置
            $mail->CharSet ="UTF-8";                     //设定邮件编码
            $mail->SMTPDebug = 0;                        // 调试模式输出
            $mail->isSMTP();                             // 使用SMTP
            $mail->Host = get_config('mail_server');                // SMTP服务器
            $mail->SMTPAuth = true;                      // 允许 SMTP 认证
            $mail->Username = get_config('mail_user');      // SMTP 用户名  即邮箱的用户名
            $mail->Password = get_config('mail_pass');                        // SMTP 密码  部分邮箱是授权码(例如163邮箱，不明白看下面有说明)
            $mail->SMTPSecure = 'ssl';                   // 允许 TLS 或者ssl协议
            $mail->Port = get_config('mail_port');                           // 服务器端口 25 或者465 具体要看邮箱服务器支持
            
            $mail->setFrom(get_config('mail_user'), get_config('mail_user'));  //发件人
            $mail->addAddress($mail_to, $mail_to);  // 收件人
            //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
            $mail->addReplyTo(get_config('mail_user'), get_config('mail_user')); //回复的时候回复给哪个邮箱 建议和发件人一致
            //$mail->addCC('cc@example.com');                    //抄送
            //$mail->addBCC('bcc@example.com');                    //密送
            
            //发送附件
            // $mail->addAttachment('../xy.zip');         // 添加附件
            // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名
            
            //Content
            $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
            $mail->Subject = $mail_title;
            $mail->Body    = $mail_body;
            $mail->AltBody =$mail_altbody.'<br>邮件客户端不支持HTML，则显示此内容';
            
            $mail->send();
            return json(['status'=>1,'msg'=>'邮件发送成功!','code'=>200,'icon'=>1]);
        } catch (Exception $e) {
            return json(['status'=>0,'msg'=>'邮件发送失败!'.$mail->ErrorInfo,'code'=>201,'icon'=>2]);
        }
    }
    
}