<?php
$sub_menu = "200100";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert("请选择需要".$_POST['act_button']."的项目");
}

auth_check($auth[$sub_menu], 'w');

if ($_POST['act_button'] == "编辑所选") {

    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 传递实际编号
        $k = $_POST['chk'][$i];

        $mb = get_member($_POST['mb_id'][$k]);

        if (!$mb['mb_id']) {
            $msg .= $mb['mb_id'].' : 会员信息不存在\\n';
        } else if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
            $msg .= $mb['mb_id'].' : 会员权限等级高于您的等级，您没有权限修改！\\n';
        } else if ($member['mb_id'] == $mb['mb_id']) {
            $msg .= $mb['mb_id'].' : 此会员当前登录使用状态中不能进行修改！\\n';
        } else {
            if($_POST['mb_certify'][$k])
                $mb_adult = $_POST['mb_adult'][$k];
            else
                $mb_adult = 0;

            $sql = " update {$g5['member_table']}
                        set mb_level = '{$_POST['mb_level'][$k]}',
                            mb_intercept_date = '{$_POST['mb_intercept_date'][$k]}',
                            mb_mailling = '{$_POST['mb_mailling'][$k]}',
                            mb_sms = '{$_POST['mb_sms'][$k]}',
                            mb_open = '{$_POST['mb_open'][$k]}',
                            mb_certify = '{$_POST['mb_certify'][$k]}',
                            mb_adult = '{$mb_adult}'
                        where mb_id = '{$_POST['mb_id'][$k]}' ";
            sql_query($sql);
        }
    }

} else if ($_POST['act_button'] == "删除所选") {

    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 传递实际编号
        $k = $_POST['chk'][$i];

        $mb = get_member($_POST['mb_id'][$k]);

        if (!$mb['mb_id']) {
            $msg .= $mb['mb_id'].' : 会员信息不存在\\n';
        } else if ($member['mb_id'] == $mb['mb_id']) {
            $msg .= $mb['mb_id'].' : 此会员当前登录使用状态中不能进行删除操作！\\n';
        } else if (is_admin($mb['mb_id']) == 'super') {
            $msg .= $mb['mb_id'].' : 不能对管理员账号进行操作！\\n';
        } else if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
            $msg .= $mb['mb_id'].' : 会员权限等级高于您的等级，您没有权限删除！\\n';
        } else {
            // 删除会员数据
            member_delete($mb['mb_id']);
        }
    }
}

if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

goto_url('./member_list.php?'.$qstr);
?>
