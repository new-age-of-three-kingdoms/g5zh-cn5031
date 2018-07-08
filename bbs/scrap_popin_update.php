<?php
include_once('./_common.php');

include_once(G5_PATH.'/head.sub.php');

if (!$is_member)
{
    $href = './login.php?'.$qstr.'&amp;url='.urlencode('./board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id);
    echo '<script> alert(\'请您登录后使用此功能\'); top.location.href = \''.$href.'\'; </script>';
    exit;
}

$sql = " select count(*) as cnt from {$g5['scrap_table']}
            where mb_id = '{$member['mb_id']}'
            and bo_table = '$bo_table'
            and wr_id = '$wr_id' ";
$row = sql_fetch($sql);
if ($row['cnt'])
{
    echo '
    <script>
    if (confirm(\'您已收藏此内容'."\n\n".'点击确定查看收藏内容\'))
        document.location.href = \'./scrap.php\';
    else
        window.close();
    </script>
    <noscript>
    <p>您已收藏此内容</p>
    <a href="./scrap.php">查看收藏内容</a>
    <a href="./board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'">返回</a>
    </noscript>';
    exit;
}

// 评论数据传递正常且有评论权限
if ($wr_content && ($member['mb_level'] >= $board['bo_comment_level']))
{
    $wr = get_write($write_table, $wr_id);
    // 原文存在时
    if ($wr['wr_id'])
    {
        $mb_id = $member['mb_id'];
        $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
        $wr_password = $member['mb_password'];
        $wr_email = addslashes($member['mb_email']);
        $wr_homepage = addslashes(clean_xss_tags($member['mb_homepage']));

        $sql = " select max(wr_comment) as max_comment from $write_table
                    where wr_parent = '$wr_id' and wr_is_comment = '1' ";
        $row = sql_fetch($sql);
        $row['max_comment'] += 1;

        $sql = " insert into $write_table
                    set ca_name = '{$wr['ca_name']}',
                         wr_option = '',
                         wr_num = '{$wr['wr_num']}',
                         wr_reply = '',
                         wr_parent = '$wr_id',
                         wr_is_comment = '1',
                         wr_comment = '{$row['max_comment']}',
                         wr_content = '$wr_content',
                         mb_id = '$mb_id',
                         wr_password = '$wr_password',
                         wr_name = '$wr_name',
                         wr_email = '$wr_email',
                         wr_homepage = '$wr_homepage',
                         wr_datetime = '".G5_TIME_YMDHIS."',
                         wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";
        sql_query($sql);

        $comment_id = mysql_insert_id();

        // 原文增加评论数量
        sql_query(" update $write_table set wr_comment = wr_comment + 1 where wr_id = '$wr_id' ");

        // insert新内容
        sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '$bo_table', '$comment_id', '$wr_id', '".G5_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

        // 评论数量加1
        sql_query(" update {$g5['board_table']}  set bo_count_comment = bo_count_comment + 1 where bo_table = '$bo_table' ");

        // 增加积分
        insert_point($member['mb_id'], $board['bo_comment_point'], "{$board['bo_subject']} {$wr_id}-{$comment_id} 评论编辑", $bo_table, $comment_id, '评论');
    }
}

$sql = " insert into {$g5['scrap_table']} ( mb_id, bo_table, wr_id, ms_datetime ) values ( '{$member['mb_id']}', '$bo_table', '$wr_id', '".G5_TIME_YMDHIS."' ) ";
sql_query($sql);

delete_cache_latest($bo_table);

echo <<<HEREDOC
<script>
    if (confirm('已加入到收藏夹\\n\\n点击确定查看收藏内容'))
        document.location.href = './scrap.php';
    else
        window.close();
</script>
<noscript>
<p>已加入到收藏夹</p>
<a href="./scrap.php">查看收藏内容</a>
</noscript>
HEREDOC;
?>
