<?php
// 
include_once('./_common.php');

if ($is_admin)
{
    if (!($token && get_session("ss_delete_token") == $token))
        alert('验证错误！无法删除！');
}

// 4.1
@include_once($board_skin_path.'/delete_comment.head.skin.php');

$write = sql_fetch(" select * from {$write_table} where wr_id = '{$comment_id}' ");

if (!$write['wr_id'] || !$write['wr_is_comment'])
    alert('没有找到评论内容');

if ($is_admin == 'super') // 管理员通过
    ;
else if ($is_admin == 'group') { // 群组管理员
    $mb = get_member($write['mb_id']);
    if ($member['mb_id'] == $group['gr_admin']) { // 检查是否拥有群组管理权限
        if ($member['mb_level'] >= $mb['mb_level']) // 检查会员等级权限，等级相同或大于时通过
            ;
        else
            alert('您没有权限进行删除，评论作者会员等级高于您的等级');
    } else
        alert('当前群组并不属于您的管理，您没有权限');
} else if ($is_admin == 'board') { // 如果是论坛版块管理员
    $mb = get_member($write['mb_id']);
    if ($member['mb_id'] == $board['bo_admin']) { // 检查是否拥有版块管理权限
        if ($member['mb_level'] >= $mb['mb_level']) // 检查会员等级权限，等级相同或大于时通过
            ;
        else
            alert('您没有权限进行删除，评论作者会员等级高于您的等级');
    } else
        alert('当前论坛版块并不属于您的管理，您没有权限进行删除');
} else if ($member['mb_id']) {
    if ($member['mb_id'] != $write['mb_id'])
        alert('您没有删除权限');
} else {
    if (sql_password($wr_password) != $write['wr_password'])
        alert('密码错误');
}

$len = strlen($write['wr_comment_reply']);
if ($len < 0) $len = 0;
$comment_reply = substr($write['wr_comment_reply'], 0, $len);

$sql = " select count(*) as cnt from {$write_table}
            where wr_comment_reply like '{$comment_reply}%'
            and wr_id <> '{$comment_id}'
            and wr_parent = '{$write[wr_parent]}'
            and wr_comment = '{$write[wr_comment]}'
            and wr_is_comment = 1 ";
$row = sql_fetch($sql);
if ($row['cnt'] && !$is_admin)
    alert('此评论已有回复内容，不能进行删除操作');

// 积分
if (!delete_point($write['mb_id'], $bo_table, $comment_id, '评论'))
    insert_point($write['mb_id'], $board['bo_comment_point'] * (-1), "{$board['bo_subject']} {$write['wr_parent']}-{$comment_id} 评论删除内容");

// 
sql_query(" delete from {$write_table} where wr_id = '{$comment_id}' ");

// 因评论删除重新获取最后回帖时间
$sql = " select max(wr_datetime) as wr_last from {$write_table} where wr_parent = '{$write['wr_parent']}' ";
$row = sql_fetch($sql);

// 减少主题的评论计数
sql_query(" update {$write_table} set wr_comment = wr_comment - 1, wr_last = '{$row['wr_last']}' where wr_id = '{$write['wr_parent']}' ");

// 减少评论数
sql_query(" update {$g5['board_table']} set bo_count_comment = bo_count_comment - 1 where bo_table = '{$bo_table}' ");

// 最新文章中删除
sql_query(" delete from {$g5['board_new_table']} where bo_table = '{$bo_table}' and wr_id = '{$comment_id}' ");

// 执行用户代码
@include_once($board_skin_path.'/delete_comment.skin.php');
@include_once($board_skin_path.'/delete_comment.tail.skin.php');

delete_cache_latest($bo_table);

goto_url('./board.php?bo_table='.$bo_table.'&amp;wr_id='.$write['wr_parent'].'&amp;page='.$page. $qstr);
?>
