<?php
include_once('./_common.php');

// 论坛版块管理员以上权限才能进行复制、移动
if ($is_admin != 'board' && $is_admin != 'group' && $is_admin != 'super')
    alert_close('只有论坛版块管理员以上会员才能进行访问');

if ($sw != 'move' && $sw != 'copy')
    alert('sw值传递错误！');

if(!count($_POST['chk_bo_table']))
    alert('请选择需要'.$act.'的论坛版块', $url);

// 原始文件路径
$src_dir = G5_DATA_PATH.'/file/'.$bo_table;

$save = array();
$save_count_write = 0;
$save_count_comment = 0;
$cnt = 0;

$sql = " select distinct wr_num from $write_table where wr_id in ({$wr_id_list}) order by wr_id ";
$result = sql_query($sql);
while ($row = sql_fetch_array($result))
{
    $wr_num = $row['wr_num'];
    for ($i=0; $i<count($_POST['chk_bo_table']); $i++)
    {
        $move_bo_table = $_POST['chk_bo_table'][$i];
        $move_write_table = $g5['write_prefix'] . $move_bo_table;

        $src_dir = G5_DATA_PATH.'/file/'.$bo_table; // 原始文件夹
        $dst_dir = G5_DATA_PATH.'/file/'.$move_bo_table; // 副本 目录

        $count_write = 0;
        $count_comment = 0;

        $next_wr_num = get_next_num($move_write_table);

        $sql2 = " select * from $write_table where wr_num = '$wr_num' order by wr_parent, wr_is_comment, wr_comment desc, wr_id ";
        $result2 = sql_query($sql2);
        while ($row2 = sql_fetch_array($result2))
        {
            $nick = cut_str($member['mb_nick'], $config['cf_cut_name']);
            if (!$row2['wr_is_comment'] && $config['cf_use_copy_log']) {
                if(strstr($row2['wr_option'], 'html')) {
                    $log_tag1 = '<div class="content_'.$sw.'">';
                    $log_tag2 = '</div>';
                } else {
                    $log_tag1 = "\n";
                    $log_tag2 = '';
                }

                $row2['wr_content'] .= "\n".$log_tag1.'[此内容由'.$nick.'在'.G5_TIME_YMDHIS.' ，从'.$board['bo_subject'].'论坛版块'.($sw == 'copy' ? '复制' : '移动').' 到此版块]'.$log_tag2;
            }

            // 文章推荐反对数量
            $wr_good = $wr_nogood = 0;
            if ($sw == 'move' && $i == 0) {
                $wr_good = $row2['wr_good'];
                $wr_nogood = $row2['wr_nogood'];
            }

            $sql = " insert into $move_write_table
                        set wr_num = '$next_wr_num',
                             wr_reply = '{$row2['wr_reply']}',
                             wr_is_comment = '{$row2['wr_is_comment']}',
                             wr_comment = '{$row2['wr_comment']}',
                             wr_comment_reply = '{$row2['wr_comment_reply']}',
                             ca_name = '".addslashes($row2['ca_name'])."',
                             wr_option = '{$row2['wr_option']}',
                             wr_subject = '".addslashes($row2['wr_subject'])."',
                             wr_content = '".addslashes($row2['wr_content'])."',
                             wr_link1 = '".addslashes($row2['wr_link1'])."',
                             wr_link2 = '".addslashes($row2['wr_link2'])."',
                             wr_link1_hit = '{$row2['wr_link1_hit']}',
                             wr_link2_hit = '{$row2['wr_link2_hit']}',
                             wr_hit = '{$row2['wr_hit']}',
                             wr_good = '{$wr_good}',
                             wr_nogood = '{$wr_nogood}',
                             mb_id = '{$row2['mb_id']}',
                             wr_password = '{$row2['wr_password']}',
                             wr_name = '".addslashes($row2['wr_name'])."',
                             wr_email = '".addslashes($row2['wr_email'])."',
                             wr_homepage = '".addslashes($row2['wr_homepage'])."',
                             wr_datetime = '{$row2['wr_datetime']}',
                             wr_file = '{$row2['wr_file']}',
                             wr_last = '{$row2['wr_last']}',
                             wr_ip = '{$row2['wr_ip']}',
                             wr_1 = '".addslashes($row2['wr_1'])."',
                             wr_2 = '".addslashes($row2['wr_2'])."',
                             wr_3 = '".addslashes($row2['wr_3'])."',
                             wr_4 = '".addslashes($row2['wr_4'])."',
                             wr_5 = '".addslashes($row2['wr_5'])."',
                             wr_6 = '".addslashes($row2['wr_6'])."',
                             wr_7 = '".addslashes($row2['wr_7'])."',
                             wr_8 = '".addslashes($row2['wr_8'])."',
                             wr_9 = '".addslashes($row2['wr_9'])."',
                             wr_10 = '".addslashes($row2['wr_10'])."' ";
            sql_query($sql);

            $insert_id = mysql_insert_id();

            // 如果不是评论
            if (!$row2['wr_is_comment'])
            {
                $save_parent = $insert_id;

                $sql3 = " select * from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' order by bf_no ";
                $result3 = sql_query($sql3);
                for ($k=0; $row3 = sql_fetch_array($result3); $k++)
                {
                    if ($row3['bf_file'])
                    {
                        // 复制原始文件并更改属性
                        @copy($src_dir.'/'.$row3['bf_file'], $dst_dir.'/'.$row3['bf_file']);
                        @chmod($dst_dir/$row3['bf_file'], G5_FILE_PERMISSION);
                    }

                    $sql = " insert into {$g5['board_file_table']}
                                set bo_table = '$move_bo_table',
                                     wr_id = '$insert_id',
                                     bf_no = '{$row3['bf_no']}',
                                     bf_source = '".addslashes($row3['bf_source'])."',
                                     bf_file = '{$row3['bf_file']}',
                                     bf_download = '{$row3['bf_download']}',
                                     bf_content = '".addslashes($row3['bf_content'])."',
                                     bf_filesize = '{$row3['bf_filesize']}',
                                     bf_width = '{$row3['bf_width']}',
                                     bf_height = '{$row3['bf_height']}',
                                     bf_type = '{$row3['bf_type']}',
                                     bf_datetime = '{$row3['bf_datetime']}' ";
                    sql_query($sql);

                    if ($sw == 'move' && $row3['bf_file'])
                        $save[$cnt]['bf_file'][$k] = $src_dir.'/'.$row3['bf_file'];
                }

                $count_write++;

                if ($sw == 'move' && $i == 0)
                {
                    // 收藏 移动
                    sql_query(" update {$g5['scrap_table']} set bo_table = '$move_bo_table', wr_id = '$save_parent' where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' ");

                    // 最新文章 移动
                    sql_query(" update {$g5['board_new_table']} set bo_table = '$move_bo_table', wr_id = '$save_parent', wr_parent = '$save_parent' where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' ");

                    // 移动推荐数据
                    sql_query(" update {$g5['board_good_table']} set bo_table = '$move_bo_table', wr_id = '$save_parent' where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' ");
                }
            }
            else
            {
                $count_comment++;

                if ($sw == 'move')
                {
                    // 最新文章 移动
                    sql_query(" update {$g5['board_new_table']} set bo_table = '$move_bo_table', wr_id = '$insert_id', wr_parent = '$save_parent' where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' ");
                }
            }

            sql_query(" update $move_write_table set wr_parent = '$save_parent' where wr_id = '$insert_id' ");

            if ($sw == 'move')
                $save[$cnt]['wr_id'] = $row2['wr_parent'];

            $cnt++;
        }

        sql_query(" update {$g5['board_table']} set bo_count_write = bo_count_write + '$count_write' where bo_table = '$move_bo_table' ");
        sql_query(" update {$g5['board_table']} set bo_count_comment = bo_count_comment + '$count_comment' where bo_table = '$move_bo_table' ");

        delete_cache_latest($move_bo_table);
    }

    $save_count_write += $count_write;
    $save_count_comment += $count_comment;
}

delete_cache_latest($bo_table);

if ($sw == 'move')
{
    for ($i=0; $i<count($save); $i++)
    {
        for ($k=0; $k<count($save[$i]['bf_file']); $k++)
            @unlink($save[$i]['bf_file'][$k]);

        sql_query(" delete from $write_table where wr_parent = '{$save[$i]['wr_id']}' ");
        sql_query(" delete from {$g5['board_new_table']} where bo_table = '$bo_table' and wr_id = '{$save[$i]['wr_id']}' ");
        sql_query(" delete from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$save[$i]['wr_id']}' ");
    }
    sql_query(" update {$g5['board_table']} set bo_count_write = bo_count_write - '$save_count_write', bo_count_comment = bo_count_comment - '$save_count_comment' where bo_table = '$bo_table' ");
}

$msg = '已将所选内容'.$act.到您选择的论坛版块';
$opener_href  = './board.php?bo_table='.$bo_table.'&amp;page='.$page.'&amp;'.$qstr;
$opener_href1 = str_replace('&amp;', '&', $opener_href);

echo <<<HEREDOC
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script>
alert("$msg");
opener.document.location.href = "$opener_href1";
window.close();
</script>
<noscript>
<p>
    "$msg"
</p>
<a href="$opener_href">返回</a>
</noscript>
HEREDOC;
?>
