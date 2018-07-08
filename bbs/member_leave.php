<?php
include_once('./_common.php');

if (!$member['mb_id'])
    alert('您没有访问权限，请登录后使用');

if ($is_admin == 'super')
    alert('不能删除管理员账号');

if (!($_POST['mb_password'] && $member['mb_password'] == sql_password($_POST['mb_password'])))
    alert('密码错误');

// 记录会员删除账号日期
$date = date("Ymd");
$sql = " update {$g5['member_table']} set mb_leave_date = '{$date}' where mb_id = '{$member['mb_id']}' ";
sql_query($sql);

// 3.09 修改 (注销)
unset($_SESSION['ss_mb_id']);

if (!$url)
    $url = G5_URL;

alert(''.$member['mb_nick'].'在'. date("Y年 m月 d日") .'删除了本站的会员账号', $url);
?>
