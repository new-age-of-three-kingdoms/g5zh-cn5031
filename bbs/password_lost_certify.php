<?php
include_once('./_common.php');

// 发生错误时显示简单error错误，防止黑客根据错误提示寻找漏洞

$mb_no = trim($_GET['mb_no']);
$mb_nonce = trim($_GET['mb_nonce']);

// 不使用会员id而是使用会员固定参数
$sql = " select mb_id, mb_lost_certify from {$g5['member_table']} where mb_no = '$mb_no' ";
$mb  = sql_fetch($sql);
if (strlen($mb['mb_lost_certify']) < 33)
    die("Error");

// 认证链接地址仅允许点击一次
sql_query(" update {$g5['member_table']} set mb_lost_certify = '' where mb_no = '$mb_no' ");

// 验证正确时将临时密码设置为正式密码
if ($mb_nonce === substr($mb['mb_lost_certify'], 0, 32)) {
    $new_password_hash = substr($mb['mb_lost_certify'], 33);
    sql_query(" update {$g5['member_table']} set mb_password = '$new_password_hash' where mb_no = '$mb_no' ");
    alert('登录密码修改成功\\n\\n请您使用新的密码进行登录', G5_BBS_URL.'/login.php');
}
else {
    die("Error");
}
?>
