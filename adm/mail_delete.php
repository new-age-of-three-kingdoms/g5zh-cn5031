<?php
$sub_menu = '200300';
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'd');

check_token();

$count = count($_POST['chk']);

if(!$count)
    alert('请选择需要删除的邮件');

for($i=0; $i<$count; $i++) {
    $ma_id = $_POST['chk'][$i];

    $sql = " delete from {$g5['mail_table']} where ma_id = '$ma_id' ";
    sql_query($sql);
}

goto_url('./mail_list.php');
?>