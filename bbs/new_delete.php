<?php
include_once('./_common.php');

//print_r2($_POST); exit;

if ($is_admin != 'super')
    alert("您没有访问权限");

$board = array();
$save_bo_table = array();

for($i=0;$i<count($_POST['chk_bn_id']);$i++)
{
    // 传递实际编号
    $k = $_POST['chk_bn_id'][$i];

    $bo_table = $_POST['bo_table'][$k];
    $wr_id    = $_POST['wr_id'][$k];

    $save_bo_table[] = $bo_table;

    $write_table = $g5['write_prefix'].$bo_table;

    if ($board['bo_table'] != $bo_table)
        $board = sql_fetch(" select bo_subject, bo_write_point, bo_comment_point, bo_notice from {$g5['board_table']} where bo_table = '$bo_table' ");

    $sql = " select * from $write_table where wr_id = '$wr_id' ";
    $write = sql_fetch($sql);
    if (!$write) continue;

    // 删除原始内容
    if ($write['wr_is_comment']==0)
    {
        $len = strlen($write['wr_reply']);
        if ($len < 0) $len = 0;
        $reply = substr($write['wr_reply'], 0, $len);

        // 修复原文与评论数量不一致的bug
        $sql = " select wr_id, mb_id, wr_is_comment from $write_table where wr_parent = '{$write['wr_id']}' order by wr_id ";
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result))
        {
            // 如果是原文
            if (!$row['wr_is_comment'])
            {
                if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], '编辑'))
                    insert_point($row['mb_id'], $board['bo_write_point'] * (-1), "{$board['bo_subject']} {$row['wr_id']} 删除内容");

                // 如有附件则删除
                $sql2 = " select * from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row['wr_id']}' ";
                $result2 = sql_query($sql2);
                while ($row2 = sql_fetch_array($result2))
                    @unlink(G5_DATA_PATH.'/file/'.$bo_table.'/'.$row2['bf_file']);

                // 删除附件数据
                sql_query(" delete from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row['wr_id']}' ");

                $count_write++;
            }
            else
            {
                // 积分
                if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], '评论'))
                    insert_point($row['mb_id'], $board['bo_comment_point'] * (-1), "{$board['bo_subject']} {$write['wr_id']}-{$row['wr_id']} ");

                $count_comment++;
            }
        }

        if ($pressed == '选择删除内容') {
            // 仅删除文章内容
            sql_query(" update $write_table set wr_subject = '{$g5['time_ymdhis']} - 根据作者请求进行删除 ☆', wr_content = '', wr_name='作者删除请求☆' where wr_id = '{$write['wr_id']}' ");
        } else {
            // 删除主题
            sql_query(" delete from $write_table where wr_parent = '{$write['wr_id']}' ");
        }

        // 删除最新文章
        sql_query(" delete from {$g5['board_new_table']} where bo_table = '$bo_table' and wr_parent = '{$write['wr_id']}' ");

        // 删除收藏
        sql_query(" delete from {$g5['scrap_table']} where bo_table = '$bo_table' and wr_id = '{$write['wr_id']}' ");

        // 公告 删除
        $notice_array = explode(",", trim($board['bo_notice']));
        $bo_notice = "";
        $lf = '';
        for ($k=0; $k<count($notice_array); $k++) {
            if ((int)$write['wr_id'] != (int)$notice_array[$k])
                $bo_notice .= $nl.$notice_array[$k];

            if($bo_notice)
                $lf = ',';
        }
        $bo_notice = trim($bo_notice);
        sql_query(" update {$g5['board_table']} set bo_notice = '$bo_notice' where bo_table = '$bo_table' ");

        if ($pressed == '删除所选') {
            // 删除主题计数器
            if ($count_write > 0 || $count_comment > 0) {
                sql_query(" update {$g5['board_table']} set bo_count_write = bo_count_write - '$count_write', bo_count_comment = bo_count_comment - '$count_comment' where bo_table = '$bo_table' ");
            }
        }
    }
    else // 
    {
        //--------------------------------------------------------------------
        // 时不会的回复内容
        //--------------------------------------------------------------------
        //print_r2($write);

        $comment_id = $wr_id;

        $len = strlen($write['wr_comment_reply']);
        if ($len < 0) $len = 0;
        $comment_reply = substr($write['wr_comment_reply'], 0, $len);

        // 
        if (!delete_point($write['mb_id'], $bo_table, $comment_id, '评论')) {
            insert_point($write['mb_id'], $board['bo_comment_point'] * (-1), "{$board['bo_subject']} {$write[wr_parent]}-{$comment_id} ");
        }

        // 
        sql_query(" delete from $write_table where wr_id = '$comment_id' ");

        // 因评论删除重新获取最后回帖时间
        $sql = " select max(wr_datetime) as wr_last from $write_table where wr_parent = '{$write['wr_parent']}' ";
        $row = sql_fetch($sql);

        // 减少主题的评论计数
        sql_query(" update $write_table set wr_comment = wr_comment - 1, wr_last = '$row[wr_last]' where wr_id = '{$write['wr_parent']}' ");

        // 减少评论数
        sql_query(" update {$g5['board_table']} set bo_count_comment = bo_count_comment - 1 where bo_table = '$bo_table' ");

        // 最新文章中删除
        sql_query(" delete from {$g5['board_new_table']} where bo_table = '$bo_table' and wr_id = '$comment_id' ");
    }
}

$save_bo_table = array_unique($save_bo_table);
foreach ($save_bo_table as $key=>$value) {
    delete_cache_latest($value);
}

goto_url("new.php?sfl=$sfl&stx=$stx&page=$page");
?>