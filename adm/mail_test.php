<?php
$sub_menu = "200300";
include_once('./_common.php');

if (!$config['cf_email_use'])
    alert('网站设置中开启邮件发送功能后才能使用此功能');

include_once(G5_LIB_PATH.'/mailer.lib.php');

auth_check($auth[$sub_menu], 'w');

check_demo();

$g5['title'] = '会员邮件测试';

$name = get_text($member['mb_name']);
$nick = $member['mb_nick'];
$mb_id = $member['mb_id'];
$email = $member['mb_email'];

$sql = "select ma_subject, ma_content from {$g5['mail_table']} where ma_id = '{$ma_id}' ";
$ma = sql_fetch($sql);

$subject = $ma['ma_subject'];

$content = $ma['ma_content'];
$content = preg_replace("/{姓名}/", $name, $content);
$content = preg_replace("/{昵称}/", $nick, $content);
$content = preg_replace("/{会员ID}/", $mb_id, $content);
$content = preg_replace("/{邮件地址}/", $email, $content);

$mb_md5 = md5($member['mb_id'].$member['mb_email'].$member['mb_datetime']);

$content = $content . '<p>如果您不想收到来至本站的订阅邮件内容请点击 [<a href="'.G5_BBS_URL.'/email_stop.php?mb_id='.$mb_id.'&amp;mb_md5='.$mb_md5.'" target="_blank">退订邮件</a> ]</p>';

mailer($config['cf_title'], $member['mb_email'], $member['mb_email'], $subject, $content, 1);

alert($member['mb_nick'].'('.$member['mb_email'].')已向此地址发送邮件，请您进入邮箱进行确认');
?>
