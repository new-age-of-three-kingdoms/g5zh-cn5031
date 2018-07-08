<?php
$sub_menu = "900600";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

check_token();

if($atype == "del"){
    $count = count($_POST['fo_no']);
    if(!$count)
        alert('请选择需要删除的项目');

    for ($i=0; $i<$count; $i++)
    {
        // 传递实际编号
        $fo_no = $_POST['fo_no'][$i];
        if (!trim($fo_no)) continue;

        $res = sql_fetch("select * from {$g5['sms5_form_table']} where fo_no='$fo_no'");
        if (!$res) continue;

        sql_query("delete from {$g5['sms5_form_table']} where fo_no='$fo_no'");
        sql_query("update {$g5['sms5_form_group_table']} set fg_count = fg_count - 1 where fg_no='{$res['fg_no']}'");
    }
}
goto_url('./form_list.php');