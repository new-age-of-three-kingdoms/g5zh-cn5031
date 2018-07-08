<?php
include_once('./_common.php');

if ($w == 's') {
    $qstr = 'bo_table='.$bo_table.'&amp;sfl='.$sfl.'&amp;stx='.$stx.'&amp;sop='.$sop.'&amp;wr_id='.$wr_id.'&amp;page='.$page;

    $wr = get_write($write_table, $wr_id);

    if (sql_password($wr_password) != $wr['wr_password'])
        alert('密码错误');

    // session需要储存以下内容，以下内容不能有密码
    //$ss_name = 'ss_secret.'_'.$bo_table.'_'.$wr_id';
    $ss_name = 'ss_secret_'.$bo_table.'_'.$wr['wr_num'];
    //set_session("ss_secret", "$bo_table|$wr[wr_num]");
    set_session($ss_name, TRUE);

} else if ($w == 'sc') {
    $qstr = 'bo_table='.$bo_table.'&amp;sfl='.$sfl.'&amp;stx='.$stx.'&amp;sop='.$sop.'&amp;wr_id='.$wr_id.'&amp;page='.$page;

    $wr = get_write($write_table, $wr_id);

    if (sql_password($wr_password) != $wr['wr_password'])
        alert('密码错误');

    // session需要储存以下内容，以下内容不能有密码
    $ss_name = 'ss_secret_comment_'.$bo_table.'_'.$wr['wr_id'];
    //set_session("ss_secret", "$bo_table|$wr[wr_num]");
    set_session($ss_name, TRUE);

} else
    alert('w值传递错误！');

goto_url('./board.php?'.$qstr);
?>
