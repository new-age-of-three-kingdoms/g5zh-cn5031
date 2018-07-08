<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('请您登录后使用此功能');

$g5['title'] = $member['mb_nick'].'的积分详情';
include_once(G5_PATH.'/head.sub.php');

$list = array();

$sql_common = " from {$g5['point_table']} where mb_id = '".escape_trim($member['mb_id'])."' ";
$sql_order = " order by po_id desc ";

$sql = " select count(*) as cnt {$sql_common} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 计算所有页码
if ($page < 1) { $page = 1; } // 如果没有页码时设置为1
$from_record = ($page - 1) * $rows; // 获取开始行

include_once($member_skin_path.'/point.skin.php');

include_once(G5_PATH.'/tail.sub.php');
?>