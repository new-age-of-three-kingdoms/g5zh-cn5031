<?php
if (!defined('_GNUBOARD_')) exit;

include_once(G5_PHPMAILER_PATH.'/class.phpmailer.php');

// 发送邮件(可使用多个附件)
// type : text=0, html=1, text+html=2
function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="") 
{ 
    global $config; 
    global $g5; 

    // 如果不使用邮件发送功能
    if (!$config['cf_email_use']) return; 

    if ($type != 1) 
        $content = nl2br($content); 

    $mail = new PHPMailer(); // defaults to using php "mail()"
    if (defined('G5_SMTP') && G5_SMTP) {
        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->Host = G5_SMTP; // SMTP server
    }
    $mail->From = $fmail;
    $mail->FromName = $fname;
    $mail->Subject = $subject;
    $mail->AltBody = ""; // optional, comment out and test
    $mail->MsgHTML($content);
    $mail->AddAddress($to);
    if ($cc) 
        $mail->AddCC($cc);
    if ($bcc) 
        $mail->AddBCC($bcc);
    //print_r2($file); exit;
    if ($file != "") { 
        foreach ($file as $f) { 
            $mail->AddAttachment($f['path'], $f['name']);
        }
    }
    return $mail->Send();
}

// 添加附件
function attach_file($filename, $tmp_name)
{
    // 上传至服务器的文件均除去扩展名（安全考虑）
    $dest_file = G5_DATA_PATH.'/tmp/'.str_replace('/', '_', $tmp_name);
    move_uploaded_file($tmp_name, $dest_file);
    /*
    $fp = fopen($tmp_name, "r");
    $tmpfile = array(
        "name" => $filename,
        "tmp_name" => $tmp_name,
        "data" => fread($fp, filesize($tmp_name)));
    fclose($fp);
    */
    $tmpfile = array("name" => $filename, "path" => $dest_file);
    return $tmpfile;
}

/*
// 发送邮件(可使用多个附件)
// type : text=0, html=1, text+html=2
function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="") 
{ 
    global $config; 
    global $g5; 

    // 如果不使用邮件发送功能
    if (!$config['cf_email_use']) return; 

    $boundary = uniqid(time()); 

    $header = "Message-ID: <".generate_mail_id(preg_replace("/@.+$/i","",$to)).">\r\n".
              "From:=?utf-8?B?".base64_encode($fname)."?=<$fmail>\r\n";
    if ($cc)  $header .= "Cc: $cc\n"; 
    if ($bcc) $header .= "Bcc: $bcc\n"; 
    $header .= "MIME-Version: 1.0\n"; 
    $header .= "X-Mailer: SIR Mailer 0.94 : {$_SERVER['SERVER_ADDR']} : {$_SERVER['REMOTE_ADDR']} : ".G5_URL." : {$_SERVER['PHP_SELF']} : {$_SERVER['HTTP_REFERER']} \n"; 
    $header .= "Date: ".date ("D, j M Y H:i:s T",time())."\r\n".
               "To: $to\r\n".
               "Subject: =?utf-8?B?".base64_encode($subject)."?=\r\n";

    if ($file == "") { 
        $header .= "Content-Type: MULTIPART/ALTERNATIVE;\n".
                   "              BOUNDARY=\"$boundary\"\n\n";
    } else {
        $header .= "Content-Type: MULTIPART/MIXED;\n".
                   "              BOUNDARY=\"$boundary\"\n\n";
    }

    if ($type == 2) 
        $content = nl2br($content); 

    $strip_content  = stripslashes(trim($content));
    $encode_content = chunk_split(base64_encode($strip_content));

    $body = "";
    $body .= "\n--$boundary\n";
    $body .= "Content-Type: TEXT/PLAIN; charset=utf-8\n"; 
    $body .= "Content-Transfer-Encoding: BASE64\n\n"; 
    $body .= $encode_content; 
    $body .= "\n--$boundary\n";

    if ($type) { 
        $body .= "Content-Type: TEXT/HTML; charset=utf-8\n"; 
        $body .= "Content-Transfer-Encoding: BASE64\n\n"; 
        $body .= $encode_content; 
        $body .= "\n--$boundary\n";
    }

    if ($file != "") { 
        foreach ($file as $f) { 
            $body .= "n--$boundary\n"; 
            $body .= "Content-Type: APPLICATION/OCTET-STREAM; name=$fname\n"; 
            $body .= "Content-Transfer-Encoding: BASE64\n"; 
            $body .= "Content-Disposition: inline; filename=$fname\n"; 

            $body .= "\n"; 
            $body .= chunk_split(base64_encode($f['data'])); 
            $body .= "\n"; 
        } 
        $body .= "--$boundary--\n"; 
    } 

    $mails['to'] = $to;
    $mails['from'] = $fmail;
    $mails['text'] = $header.$body;

    if (defined(G5_SMTP)) {
        ini_set('SMTP', G5_SMTP);
        @mail($to, $subject, $body, $header, "-f $fmail"); 
    } else {
        new maildaemon($mails);
    }
}

// 包含附件时
$fp = fopen(__FILE__, "r");
$file[] = array(
    "name"=>basename(__FILE__),
    "data"=>fread($fp, filesize(__FILE__)));
fclose($fp);

// 邮件有效性检查
// core PHP Programming 参考书
// hanmail.net , hotmail.com , kebi.com 以上服务商无法正常使用
function verify_email($address, &$error)
{
    global $g5;

    $WAIT_SECOND = 3; // ?秒 等待时间

    list($user, $domain) = explode("@", $address);

    // 检查是否有邮件交换记录（mx）
    if (checkdnsrr($domain, "MX")) {
        // 获取邮件服务器反馈
        if (!getmxrr($domain, $mxhost, $mxweight)) {
            $error = '无法与邮件服务器进行通信';
            return false;
        }
    } else {
        // 如无邮件服务器则已当前域名接收设定
        $mxhost[] = $domain;
        $mxweight[] = 1;
    }

    // 创建邮件服务器排队序列
    for ($i=0; $i<count($mxhost); $i++)
        $weighted_host[($mxweight[$i])] = $mxhost[$i];
    ksort($weighted_host);

    // 检测端口
    foreach($weighted_host as $host) {
        // 连接至smtp
        if (!($fp = @fsockopen($host, 25))) continue;

        // 跳过220信息
        // 超过三秒无应答则放弃
        socket_set_blocking($fp, false);
        $stoptime = G5_SERVER_TIME + $WAIT_SECOND;
        $gotresponse = false;

        while (true) {
            // 获取邮件服务器回应信息
            $line = fgets($fp, 1024);

            if (substr($line, 0, 3) == '220') {
                // 初始化计时器
                $stoptime = G5_SERVER_TIME + $WAIT_SECOND;
                $gotresponse = true;
            } else if ($line == '' && $gotresponse)
                break;
            else if (G5_SERVER_TIME > $stoptime)
                break;
        }

        // 如无应答则跳掉下一个
        if (!$gotresponse) continue;

        socket_set_blocking($fp, true);

        // 进行smtp通信
        fputs($fp, "HELO {$_SERVER['SERVER_NAME']}\r\n");
        echo "HELO {$_SERVER['SERVER_NAME']}\r\n";
        fgets($fp, 1024);

        // 设置from
        fputs($fp, "MAIL FROM: <info@$domain>\r\n");
        echo "MAIL FROM: <info@$domain>\r\n";
        fgets($fp, 1024);

        // 尝试地址
        fputs($fp, "RCPT TO: <$address>\r\n");
        echo "RCPT TO: <$address>\r\n";
        $line = fgets($fp, 1024);

        // 关闭连接
        fputs($fp, "QUIT\r\n");
        fclose($fp);

        if (substr($line, 0, 3) != '250') {
            // smtp服务器无法识别地址则是错误地址
            $error = $line;
            return false;
        } else
            // 地址识别成功
            return true;

    }

    $error = '邮件未能抵达邮件交换服务器';
    return false;
}


# 添加jsboard的邮件发送class 130808
# http://kldp.net/projects/jsboard/

# 邮件发送函数 2001.11.30 金正均
# $Id: include/sendmail.php,v 1.4 2009/11/19 05:29:51 oops Exp $

# 不依赖服务器的smtp进程，可以直接发送邮件的smtp组件
#
# 使用特定排列class传递邮件，请参照以下内容
#
# debug -> 是否显示debug
# ofhtml -> 用于网页或shell执行
# from -> 发件人邮件地址
# to -> 收件人邮件地址
# text -> 邮件内容
#
class maildaemon {
  var $failed = 0;

  function __construct($v) {
    $this->debug = $v['debug'];
    $this->ofhtml = $v['ofhtml'];
    if($_SERVER['SERVER_NAME']) $this->helo = $_SERVER['SERVER_NAME'];
    if(!$this->helo || preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/i",$this->helo))
      $this->helo = "JSBoardMessage";

    $this->from = $v['from'];
    $this->to   = $v['to'];
    $this->body = $v['text']."\r\n.";

    //die($v['text']);
    $this->newline = $this->ofhtml ? "<br>\n" : "\n";

    $this->mx = $this->getMX($this->to);

    if($this->debug) {
      echo "DEBUG: ".$this->mx." start".$this->newline;
      echo "################################################################".$this->newline;
    }
    $this->sockets("open");
    $this->send("HELO ".$this->helo);
    $this->send("MAIL FROM: <".$this->from.">");
    $this->send("RCPT TO: <".$this->to.">");
    $this->send("data");
    $this->send($this->body);
    $this->send("quit");
    $this->sockets("close");
  }

  function getMX($email) {
    $dev = explode("@",$email);
    $account = $dev[0];
    $host = $dev[1];

    if(checkdnsrr($host,"MX") && getmxrr($host,$mx,$weight)) {
      $idx = 0;
      for($i=0;$i<sizeof($mx);$i++) {
        $dest = $dest ? $dest : $weight[$i];
        if($dest > $weight[$i]) {
          $dest = $weight[$i];
          $idx = $i;
        }
      }
    } else return $host;
    return $mx[$idx];
  }

  # debug 函数
  #  $t -> 1 (debug of socket open,close)
  #        0 (regular smtp message)
  #  $p -> 1 (print detail debug)
  # 
  # return 1 -> success
  # return 0 -> failed
  #
  function debug($str,$t=0,$p=0) {
    if($t) {
      if(!$str) $this->failed = 1;
      if($this->sock) $returnmsg = trim(fgets($this->sock,1024));
    } else {
      if(!preg_match("/^(220|221|250|251|354)$/",substr(trim($str),0,3)))
        $this->failed = 1;
    }

    # DEBUG mode -> 显示所有消息
    if($p) {
      if($t) {
        $str = "Conncet ".$this->mx;
        $str .= $this->failed ? " Failed" : " Success";
        $str .= $this->newline."DEBUG: $returnmsg";
      }
      echo "DEBUG: $str".$this->newline;
    }

    # 不是debug模式时显示错误信息
    if(!$p && $this->failed) {
      if($this->ofhtml) echo "<SCRIPT>\nalert('$str')\n</SCRIPT>\n";
      else "ERROR: $str\n";
    }
  }

  function sockets($option=0) {
    switch($option) {
      case "open" :
        $this->sock = @fsockopen($this->mx,25,$this->errno,$this->errstr,30);
        $this->debug($this->sock,1,$this->debug);
        break;
      default :
        if($this->sock) fclose($this->sock);
        break;
    }
  }

  function send($str,$chk=0) {
    if(!$this->failed) {
      if($this->debug) {
        if(preg_match("/\r\n/",trim($str)))
          $str_debug = trim(str_replace("\r\n","\r\n       ",$str));
        else $str_debug = $str;
      }
      fputs($this->sock,"$str\r\n");
      $recv = trim(fgets($this->sock,1024));
      $recvchk = $recv;
      $this->debug($recv,0,$this->debug);

      if(preg_match("/Mail From:/i",$str) && preg_match("/exist|require|error/i",$recvchk) && !$chk) {
        $this->failed = 0;
        $this->send("MAIL FROM: <".$this->to.">",1);
      }
    }
  }
}


function generate_mail_id($uid) {
  $id = date("YmdHis",time());
  mt_srand((float) microtime() * 1000000);
  $randval = mt_rand();
  $id .= $randval."@$uid";
  return $id;
}


function mail_header($to,$from,$title,$mta=0) {
  global $langs,$boundary;

  # 创建mail header 
  $boundary = get_boundary_msg();
  $header = "Message-ID: <".generate_mail_id(preg_replace("/@.+$/i","",$to)).">\r\n".
            "From:=?utf-8?B?".base64_encode('发件人')."?=<$from>\r\n".
            "MIME-Version: 1.0\r\n";

  if(!$mta) $header .= "Date: ".date ("D, j M Y H:i:s T",time())."\r\n".
                       "To: $to\r\n".
                       "Subject: $title\r\n";

  $header .= "Content-Type: multipart/alternative;\r\n".
             "              boundary=\"$boundary\"\r\n\r\n";

  return $header;
}


function get_boundary_msg() {
  $uniqchr = uniqid("");
  $one = strtoupper($uniqchr[0]);
  $two = strtoupper(substr($uniqchr,0,8));
  $three = strtoupper(substr(strrev($uniqchr),0,8));
  return "----=_NextPart_000_000${one}_${two}.${three}";
}
*/
?>