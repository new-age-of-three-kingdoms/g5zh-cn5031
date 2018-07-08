<?php
$sub_menu = "100200";
include_once('./_common.php');

check_demo();

if ($is_admin != 'super')
    alert('您没有访问权限');

check_token();

$count = count($_POST['chk']);

if (!$count)
    alert("请选择需要".$_POST['act_button']."的项目");

for ($i=0; $i<$count; $i++)
{
    // 传递实际编号
    $k = $chk[$i];

    $sql = " delete from {$g5['auth_table']} where mb_id = '{$_POST['mb_id'][$k]}' and au_menu = '{$_POST['au_menu'][$k]}' ";
    sql_query($sql);
}

goto_url('./auth_list.php?'.$qstr);
?>
