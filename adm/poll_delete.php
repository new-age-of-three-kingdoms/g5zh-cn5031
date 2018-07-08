<?php
$sub_menu = "200900";
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'd');

check_token();

$count = count($_POST['chk']);

if(!$count)
    alert('请选择需要删除的投票项目');

for($i=0; $i<$count; $i++) {
    $po_id = $_POST['chk'][$i];

    $sql = " delete from {$g5['poll_table']} where po_id = '$po_id' ";
    sql_query($sql);

    $sql = " delete from {$g5['poll_etc_table']} where po_id = '$po_id' ";
    sql_query($sql);
}

goto_url('./poll_list.php?'.$qstr);
?>