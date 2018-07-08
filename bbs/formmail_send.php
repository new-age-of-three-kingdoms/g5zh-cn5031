<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

if (!$config['cf_email_use'])
    alert('网站设置中开启邮件发送功能后才能使用此功能\\n\\n请阅读使用指南或联络管理员');

if (!$is_member && $config['cf_formmail_is_member'])
    alert_close('请登录后使用，如不是会员请您注册');

$to = base64_decode($to);

if (substr_count($to, "@") > 1)
    alert_close('您每次只能向一个收件人发送邮件');


if (!chk_captcha()) {
    alert('您输入的验证码错误，请重新输入');
}


$file = array();
for ($i=1; $i<=$attach; $i++) {
    if ($_FILES['file'.$i]['name'])
        $file[] = attach_file($_FILES['file'.$i]['name'], $_FILES['file'.$i]['tmp_name']);
}

$content = stripslashes($content);
if ($type == 2) {
    $type = 1;
    $content = str_replace("\n", "<br>", $content);
}

// 如果是html
if ($type) {
    $current_url = G5_URL;
    $mail_content = '<!doctype html><html lang="zh-cn"><head><meta charset="utf-8"><title>发送邮件</title><link rel="stylesheet" href="'.$current_url.'/style.css"></head><body>'.$content.'</body></html>';
}
else
    $mail_content = $content;

mailer($fnick, $fmail, $to, $subject, $mail_content, $type, $file);

// 删除临时附件
if(!empty($file)) {
    foreach($file as $f) {
        @unlink($f['path']);
    }
}

//$html_title = $tmp_to . "发送邮件";
$html_title = '正在发送邮件';
include_once(G5_PATH.'/head.sub.php');

alert_close('邮件发送完成');

include_once(G5_PATH.'/tail.sub.php');
?>