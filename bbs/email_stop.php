<?php
include_once('./_common.php');

$sql = " select mb_id, mb_email, mb_datetime from {$g5['member_table']} where mb_id = '{$mb_id}' ";
$row = sql_fetch($sql);
if (!$row['mb_id'])
    alert('找不到会员信息', G5_URL);

if ($mb_md5) {
    $tmp_md5 = md5($row['mb_id'].$row['mb_email'].$row['mb_datetime']);
    if ($mb_md5 == $tmp_md5) {
        sql_query(" update {$g5['member_table']} set mb_mailling  = 0 where mb_id = '{$mb_id}' ");

        alert('已退订邮件', G5_URL);
    }
}

alert('传递参数错误！', G5_URL);
?>