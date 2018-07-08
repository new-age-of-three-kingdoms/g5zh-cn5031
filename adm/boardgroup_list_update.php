<?php
$sub_menu = "300200";
include_once('./_common.php');

//print_r2($_POST); exit;

check_demo();

auth_check($auth[$sub_menu], 'w');

$count = count($_POST['chk']);

if(!$count)
    alert('请选择需要'.$_POST['act_button'].'的群组');

for ($i=0; $i<$count; $i++)
{
    $k     = $_POST['chk'][$i];
    $gr_id = $_POST['group_id'][$k];

    if($_POST['act_button'] == '编辑所选') {
        $sql = " update {$g5['group_table']}
                    set gr_subject    = '{$_POST['gr_subject'][$k]}',
                        gr_device     = '{$_POST['gr_device'][$k]}',
                        gr_admin      = '{$_POST['gr_admin'][$k]}',
                        gr_use_access = '{$_POST['gr_use_access'][$k]}',
                        gr_order      = '{$_POST['gr_order'][$k]}'
                  where gr_id         = '{$gr_id}' ";
        if ($is_admin != 'super')
            $sql .= " and gr_admin    = '{$_POST['gr_admin'][$k]}' ";
        sql_query($sql);
    } else if($_POST['act_button'] == '删除所选') {
        $row = sql_fetch(" select count(*) as cnt from {$g5['board_table']} where gr_id = '$gr_id' ");
        if ($row['cnt'])
            alert("当前群组内尚有论坛版块在运行\\n\\n如需删除群组请先删除论坛版块", './board_list.php?sfl=gr_id&amp;stx='.$gr_id);

        // 群组 删除
        sql_query(" delete from {$g5['group_table']} where gr_id = '$gr_id' ");

        // 删除授权会员权限
        sql_query(" delete from {$g5['group_member_table']} where gr_id = '$gr_id' ");
    }
}

goto_url('./boardgroup_list.php?'.$qstr);
?>
