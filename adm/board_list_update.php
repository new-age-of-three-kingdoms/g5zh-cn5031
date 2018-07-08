<?php
$sub_menu = "300100";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert("请选择需要".$_POST['act_button']."的项目");
}

if ($_POST['act_button'] == "编辑所选") {

    auth_check($auth[$sub_menu], 'w');

    for ($i=0; $i<count($_POST['chk']); $i++) {

        // 传递实际编号
        $k = $_POST['chk'][$i];

        if ($is_admin != 'super') {
            $sql = " select count(*) as cnt from {$g5['board_table']} a, {$g5['group_table']} b
                      where a.gr_id = '{$_POST['gr_id'][$k]}'
                        and a.gr_id = b.gr_id
                        and b.gr_admin = '{$member['mb_id']}' ";
            $row = sql_fetch($sql);
            if (!$row['cnt'])
                alert('您没有权限编辑修改其他管理员所有的论坛：('.$board_table[$k].')');
        }

        $sql = " update {$g5['board_table']}
                    set gr_id               = '{$_POST['gr_id'][$k]}',
                        bo_subject          = '{$_POST['bo_subject'][$k]}',
                        bo_device           = '{$_POST['bo_device'][$k]}',
                        bo_skin             = '{$_POST['bo_skin'][$k]}',
                        bo_mobile_skin      = '{$_POST['bo_mobile_skin'][$k]}',
                        bo_read_point       = '{$_POST['bo_read_point'][$k]}',
                        bo_write_point      = '{$_POST['bo_write_point'][$k]}',
                        bo_comment_point    = '{$_POST['bo_comment_point'][$k]}',
                        bo_download_point   = '{$_POST['bo_download_point'][$k]}',
                        bo_use_search       = '{$_POST['bo_use_search'][$k]}',
                        bo_use_sns          = '{$_POST['bo_use_sns'][$k]}',
                        bo_order            = '{$_POST['bo_order'][$k]}'
                  where bo_table            = '{$_POST['board_table'][$k]}' ";
        sql_query($sql);
    }

} else if ($_POST['act_button'] == "删除所选") {

    if ($is_admin != 'super')
        alert('只有网站管理员才能删除论坛版块');

    auth_check($auth[$sub_menu], 'd');

    check_token();

    // 需要设置_BOARD_DELETE_才能让board_delete.php正常运行
    define('_BOARD_DELETE_', true);

    for ($i=0; $i<count($_POST['chk']); $i++) {
        // 传递实际编号
        $k = $_POST['chk'][$i];

        // include 前必须传递$bo_table
        $tmp_bo_table = trim($_POST['board_table'][$k]);
        include ('./board_delete.inc.php');
    }


}

goto_url('./board_list.php?'.$qstr);
?>
