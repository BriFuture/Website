<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 05.08
 * lastmodifytime: 05.xx
 * filename: Mail.class.php
 * 用来发送邮件
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Mail {
  /**
   * SMTP 服务器
   */
  private $host;
  /**
   * SMTP服务器的端口号
   */
  private $port;
  /**
   * SMTP服务器用户名
   */
  private $user;
  /**
   * SMTP服务器密码
   */
  private $pass;
  /**
   * 发信人
   */
  private $from;

  /**
   * 发信人名称
   */
  private $proxy_name;

  /**
   * 保存base对象
   */
  private $base;

  public function __construct() {
    $this->base = new Base();
    $this->init_config();
  }

  /**
   *
   */
  private function check() {
    if(!ALLOW_MAIL) {
      $this->base->fatal_error('管理员关闭了邮箱功能，请联系管理员','无法发送邮件','无法发送邮件');
    }
  }

  /**
   * 初始化参数
   * 应该从配置文件或者数据库中读取参数
   */
  private function init_config() {
    $this->host = 'smtp.qq.com';
    $this->port = 465;
    $this->user = 'noreply@mail.sunsmell.cc';
    $this->pass = 'tsqihwgdnpxydjdd';
    $this->from = 'noreply@mail.sunsmell.cc';
    $this->proxy_name = 'sunsmell.cc的管理员';
  }

  /**
   * 发送邮件
   * @param  $msg  包含收信人、内容和标题的数组
   */ 
  public function send($msg) {
    require_once CORE_PATH.'const.php';
    $security = new Security;
    
    $this->check();
    //先验证是否是通过正常途径发起的邮件
    if($security->check_code($GLOVARS['sendmail'], @$_POST['random_code']))
    {
      if(!is_null($to))
        $this->use_phpmailer($msg['to'], $msg['subject'], $msg['body']);
        // use_phpmailer($to, @$_POST['subject'], @$_POST['body']);
      else
        $this->base->fatal_error('没有填写收件人啊~！', '邮件发送失败', '邮件发送失败');
    }
    else
      $this->base->fatal_error('请用正确的姿势发送邮件！', '发送邮件失败');

  }
  /**
   * 使用phpmailer和smtp来进行邮件操作
   *  @param  $to       收信人
   *  @param  $content  内容
   *  @param  $subject  主题
   */
  private function use_phpmailer($to, $subject = '', $body = '') {
    require_once UTIL_PATH.'PHPMailer/class.phpmailer.php';
    require_once UTIL_PATH.'PHPMailer/class.smtp.php';
    $mail = new PHPMailer(); 
    //对邮件内容进行过滤
    $body = eregi_replace("[\]",'',$body); 
    //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->CharSet ="utf-8";
    // 设定使用SMTP服务
    $mail->IsSMTP(); 
    // 不启用SMTP调试功能// 1 = errors and messages// 2 = messages only
    $mail->SMTPDebug  = 0;                     
    // 启用 SMTP 验证功能
    $mail->SMTPAuth   = true;                  
    // 安全协议
    $mail->SMTPSecure = "ssl";                 
    //设置邮件相关选项    
    $mail->Host       = $this->host;      
    $mail->Port       = $this->port;                   
    $mail->Username   = $this->user;  
    $mail->Password   = $this->pass;            
    $mail->From       = $this->from;
    $mail->FromName   = $this->proxy_name; 
    $mail->addReplyTo('','');
    //设置邮件主题
    $mail->Subject    = $subject;

    global $TEXT;
    // optional, comment out and test
    $mail->AltBody    = $TEXT['mail_alter_body']; 
    $mail->msgHTML($body);

    $mail->addAddress($to, 'who');
    //添加附件
    //$mail->AddAttachment("/images/phpmailer.gif");      // attachment
    //$mail->AddAttachment("/images/phpmailer_mini.gif"); // attachment
    if(!$mail->send()) 
    {
      $this->base->fatal_error('由于某种未知的力量导致邮件发送失败！', '邮件发送失败');
    } 
    else 
    {
      $msg = array(
        'content' => '邮件发送成功',
        'title'   => '邮件发送成功',
        'cTitle'  => '邮件发送成功',
      );
      $this->base->report_msg($msg);
    }
  }

  /**
   *  @deprecated because of some reason it failed
   *  @param  $to       收信人
   *  @param  $content  内容
   *  @param  $subject  主题
   *  @param  $type     邮件类型 默认为文本，可以设置为html
   */
  private function use_smtp($to, $content, $subject, $type='txt') {
    require_once UTIL_PATH.'PHPMailer/class.smtp.php';
    $smtp = new SMTP();
    //是否显示发送的调试信息
    $smtp->setDebugLevel(4);
    //使用身份验证.
    if($smtp->connect($this->host,$this->port))
    {
      // $smtp->startTLS();
      $smtp->hello();
      $smtp->authenticate($this->user, $this->pass);
      $smtp->recipient($to);
      $state = $smtp->data($content);
      $smtp->mail($this->from);
      // ob_flush();
      // echo "<div style='width:300px; margin:36px auto;'>";
      // echo "恭喜！邮件发送成功！！";
      // echo "<a href='/index.php'>点此返回</a>";
      // echo "</div>";
      $smtp->quit();
      $smtp->close();
    }
    else
    {
      print_r($smtp->getDebugOutput());
    }
  }

}