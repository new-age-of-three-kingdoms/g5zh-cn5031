<?php
include_once('./_common.php');

if ($is_admin)
{
    if (!($token && get_session('ss_delete_token') == $token))
        alert('验证错误！无法删除！');
}

//$wr = sql_fetch(" select * from $write_table where wr_id = '$wr_id' ");

@include_once($board_skin_path.'/delete.head.skin.php');

if ($is_admin == 'super') // 管理员通过
    ;
else if ($is_admin == 'group') { // 群组管理员
    $mb = get_member($write['mb_id']);
    if ($member['mb_id'] != $group['gr_admin']) // 检查是否拥有群组管理权限
        alert('当前群组并不属于您的管理，您没有权限进行删除');
    else if ($member['mb_level'] < $mb['mb_level']) // 检查会员等级权限，等级相同或大于时通过
        alert('你没有权限进行删除因为作者等级高于您的等级');
} else if ($is_admin == 'board') { // 如果是论坛版块管理员
    $mb = get_member($write['mb_id']);
    if ($member['mb_id'] != $board['bo_admin']) // 检查是否拥有版块管理权限
        alert('当前版块并不属于您的管理，您没有权限进行删除');
    else if ($member['mb_level'] < $mb['mb_level']) // 检查会员等级权限，等级相同或大于时通过
        alert('你没有权限进行删除因为作者等级高于您的等级');
} else if ($member['mb_id']) {
    if ($member['mb_id'] != $write['mb_id'])
        alert('您没有删除权限');
} else {
    if ($write['mb_id'])
        alert('请您登录后使用', './login.php?url='.urlencode('./board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id));
    else if (sql_password($wr_password) != $write['wr_password'])
        alert('您输入的密码错误，删除失败！');
}

$len = strlen($write['wr_reply']);
if ($len < 0) $len = 0;
$reply = substr($write['wr_reply'], 0, $len);

// 仅获取原文
$sql = " select count(*) as cnt from $write_table
            where wr_reply like '$reply%'
            and wr_id <> '{$write['wr_id']}'
            and wr_num = '{$write['wr_num']}'
            and wr_is_comment = 0 ";
$row = sql_fetch($sql);
if ($row['cnt'] && !$is_admin)
    alert('此内容已有关联回复内容\\n\\n如需删除请先删除关联回复内容');

// 检查删除主题评论限制
$sql = " select count(*) as cnt from $write_table
            where wr_parent = '$wr_id'
            and mb_id <> '{$member['mb_id']}'
            and wr_is_comment = 1 ";
$row = sql_fetch($sql);
if ($row['cnt'] >= $board['bo_count_delete'] && !$is_admin)
    alert('此内容已有评论内容\\n\\n当前版块设置评论超过'.$board['bo_count_delete'].'个以上时禁止删除主题');


// 执行用户代码
@include_once($board_skin_path.'/delete.skin.php');


// 修复原文与评论数量不一致的bug
//$sql = " select wr_id, mb_id, wr_comment from $write_table where wr_parent = '$write[wr_id]' order by wr_id ";
$sql = " select wr_id, mb_id, wr_is_comment, wr_content from $write_table where wr_parent = '{$write['wr_id']}' order by wr_id ";
$result = sql_query($sql);
while ($row = sql_fetch_array($result))
{
    // 如果是原文
    if (!$row['wr_is_comment'])
    {
        // 删除发帖积分
        if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], '编辑'))
            insert_point($row['mb_id'], $board['bo_write_point'] * (-1), "{$board['bo_subject']} {$row['wr_id']} 删除内容");

        // 如有附件则删除
        $sql2 = " select * from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row['wr_id']}' ";
        $result2 = sql_query($sql2);
        while ($row2 = sql_fetch_array($result2)) {
            @unlink(G5_DATA_PATH.'/file/'.$bo_table.'/'.$row2['bf_file']);
            // 缩略图删除
            if(preg_match("/\.({$config['cf_image_extension']})$/i", $row2['bf_file'])) {
                delete_board_thumbnail($bo_table, $row2['bf_file']);
            }
        }

        // 删除编辑器的缩略图
        delete_editor_thumbnail($row['wr_content']);

        // 删除附件数据
        sql_query(" delete from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row['wr_id']}' ");

        $count_write++;
    }
    else
    {
        // 积分
        if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], '评论'))
            insert_point($row['mb_id'], $board['bo_comment_point'] * (-1), "{$board['bo_subject']} {$write['wr_id']}-{$row['wr_id']} 评论删除内容");

        $count_comment++;
    }
}

// 删除主题
sql_query(" delete from $write_table where wr_parent = '{$write['wr_id']}' ");

// 删除最新文章
sql_query(" delete from {$g5['board_new_table']} where bo_table = '$bo_table' and wr_parent = '{$write['wr_id']}' ");

// 删除收藏
sql_query(" delete from {$g5['scrap_table']} where bo_table = '$bo_table' and wr_id = '{$write['wr_id']}' ");

/*
// 公告 删除
$notice_array = explode("\n", trim($board['bo_notice']));
$bo_notice = "";
for ($k=0; $k<count($notice_array); $k++)
    if ((int)$write[wr_id] != (int)$notice_array[$k])
        $bo_notice .= $notice_array[$k] . "\n";
$bo_notice = trim($bo_notice);
*/
$bo_notice = board_notice($board['bo_notice'], $write['wr_id']);
sql_query(" update {$g5['board_table']} set bo_notice = '$bo_notice' where bo_table = '$bo_table' ");

// 删除主题计数器
if ($count_write > 0 || $count_comment > 0)
    sql_query(" update {$g5['board_table']} set bo_count_write = bo_count_write - '$count_write', bo_count_comment = bo_count_comment - '$count_comment' where bo_table = '$bo_table' ");

@include_once($board_skin_path.'/delete.tail.skin.php');

delete_cache_latest($bo_table);

goto_url('./board.php?bo_table='.$bo_table.'&amp;page='.$page.$qstr);
?>
