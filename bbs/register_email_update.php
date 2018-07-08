<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

$mb_id    = trim($_POST['mb_id']);
$mb_email = trim($_POST['mb_email']);

$sql = " select mb_name, mb_datetime from {$g5['member_table']} where mb_id = '{$mb_id}' and mb_email_certify <> '' ";
$mb = sql_fetch($sql);
if (!$mb) {
    alert("您已完成邮箱地址认证", G5_URL);
}

if (!chk_captcha()) {
    alert('您输入的验证码错误，请重新输入');
}

$sql = " select count(*) as cnt from {$g5['member_table']} where mb_id <> '{$mb_id}' and mb_email = '$mb_email' ";
$row = sql_fetch($sql);
if ($row['cnt']) {
    alert("{$mb_email} 此邮件地址已被注册使用\\n\\n请使用其他邮件地址");
}

// 发送认证邮件
$subject = '['.$config['cf_title'].'] 邮件地址认证邮件';

$mb_name = $mb['mb_name'];
$mb_datetime = $mb['mb_datetime'] ? $mb['mb_datetime'] : G5_TIME_YMDHIS;
$mb_md5 = md5($mb_id.$mb_email.$mb_datetime);
$certify_href = G5_BBS_URL.'/email_certify.php?mb_id='.$mb_id.'&amp;mb_md5='.$mb_md5;

ob_start();
include_once ('./register_form_update_mail3.php');
$content = ob_get_contents();
ob_end_clean();

mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $mb_email, $subject, $content, 1);

$sql = " update {$g5['member_table']} set mb_email = '$mb_email' where mb_id = '$mb_id' ";
sql_query($sql);

alert("已发送邮件地址确认信至{$mb_email}邮箱\\n\\n请您稍后登录{$mb_email}邮箱进行查看", G5_URL);
?>