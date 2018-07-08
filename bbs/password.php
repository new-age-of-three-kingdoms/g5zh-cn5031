<?php
include_once('./_common.php');

$g5['title'] = '密码新建';

switch ($w) {
    case 'u' :
        $action = './write.php';
        $return_url = './board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id;
        break;
    case 'd' :
        $action = './delete.php';
        $return_url = './board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id;
        break;
    case 'x' :
        $action = './delete_comment.php';
        $row = sql_fetch(" select wr_parent from $write_table where wr_id = '$comment_id' ");
        $return_url = './board.php?bo_table='.$bo_table.'&amp;wr_id='.$row['wr_parent'];
        break;
    case 's' :
        // 在新窗口登录时管理员或作者本人直接跳转到文章内容页面
        if ($is_admin || ($member['mb_id'] == $write['mb_id'] && $write['mb_id']))
            goto_url('./board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id);
        else {
            $action = './password_check.php';
            $return_url = './board.php?bo_table='.$bo_table;
        }
        break;
    case 'sc' :
        // 在新窗口登录时管理员或作者本人直接跳转到文章内容页面
        if ($is_admin || ($member['mb_id'] == $write['mb_id'] && $write['mb_id']))
            goto_url('./board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id);
        else {
            $action = './password_check.php';
            $return_url = './board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id;
        }
        break;
    default :
        alert('w值传递错误！');
}

include_once(G5_PATH.'/head.sub.php');

//if ($board['bo_include_head']) { @include ($board['bo_include_head']); }
//if ($board['bo_content_head']) { echo stripslashes($board['bo_content_head']); }

/* 获取加密内容标题 子云爸爸 2013-01-29 */
$sql = " select wr_subject from {$write_table}
                      where wr_num = '{$write['wr_num']}'
                      and wr_reply = ''
                      and wr_is_comment = 0 ";
$row = sql_fetch($sql);

$g5['title'] = $row['wr_subject'];

include_once($member_skin_path.'/password.skin.php');

//if ($board['bo_content_tail']) { echo stripslashes($board['bo_content_tail']); }
//if ($board['bo_include_tail']) { @include ($board['bo_include_tail']); }

include_once(G5_PATH.'/tail.sub.php');
?>
