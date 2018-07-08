<?php
include_once('./_common.php');

if (!$is_member)
    alert('请登录后使用，如不是会员请您注册');

$sql = " delete from {$g5['scrap_table']} where mb_id = '{$member['mb_id']}' and ms_id = '$ms_id' ";
sql_query($sql);

goto_url('./scrap.php?page='.$page);
?>
