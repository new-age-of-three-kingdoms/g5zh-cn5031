<?php
$sub_menu = '300100';
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

$target_table   = trim($_POST['target_table']);
$target_subject = trim($_POST['target_subject']);

if (!preg_match('/[A-Za-z0-9_]{1,20}/', $target_table)) {
    alert('论坛版块数据表名称只能使用英文、数字及下划线（20字以内）');
}

$row = sql_fetch(" select count(*) as cnt from {$g5['board_table']} where bo_table = '$target_table' ");
if ($row['cnt'])
    alert($target_table.'已被使用的论坛数据表名称\\n 不能用于复制数据表名称使用');

// 创建论坛数据表
$sql = get_table_define($g5['write_prefix'] . $bo_table);
$sql = str_replace($g5['write_prefix'] . $bo_table, $g5['write_prefix'] . $target_table, $sql);
sql_query($sql, false);

$file_copy = array();

// 复制结构时排除公告序号
if ($copy_case == 'schema_only') {
    $board['bo_notice'] = '';
}

// 论坛版块信息
$sql = " insert into {$g5['board_table']}
            set bo_table = '$target_table',
                gr_id = '{$board['gr_id']}',
                bo_subject = '$target_subject',
                bo_device = '{$board['bo_device']}',
                bo_admin = '{$board['bo_admin']}',
                bo_list_level = '{$board[bo_list_level]}',
                bo_read_level = '{$board[bo_read_level]}',
                bo_write_level = '{$board[bo_write_level]}',
                bo_reply_level = '{$board[bo_reply_level]}',
                bo_comment_level = '{$board[bo_comment_level]}',
                bo_upload_level = '{$board[bo_upload_level]}',
                bo_download_level = '{$board[bo_download_level]}',
                bo_html_level = '{$board[bo_html_level]}',
                bo_link_level = '{$board[bo_link_level]}',
                bo_count_modify = '{$board[bo_count_modify]}',
                bo_count_delete = '{$board[bo_count_delete]}',
                bo_read_point = '{$board[bo_read_point]}',
                bo_write_point = '{$board[bo_write_point]}',
                bo_comment_point = '{$board[bo_comment_point]}',
                bo_download_point = '{$board[bo_download_point]}',
                bo_use_category = '{$board[bo_use_category]}',
                bo_category_list = '{$board['bo_category_list']}',
                bo_use_sideview = '{$board[bo_use_sideview]}',
                bo_use_file_content = '{$board[bo_use_file_content]}',
                bo_use_secret = '{$board[bo_use_secret]}',
                bo_use_dhtml_editor = '{$board[bo_use_dhtml_editor]}',
                bo_use_rss_view = '{$board[bo_use_rss_view]}',
                bo_use_good = '{$board[bo_use_good]}',
                bo_use_nogood = '{$board[bo_use_nogood]}',
                bo_use_name = '{$board[bo_use_name]}',
                bo_use_signature = '{$board[bo_use_signature]}',
                bo_use_ip_view = '{$board[bo_use_ip_view]}',
                bo_use_list_view = '{$board['bo_use_list_view']}',
                bo_use_list_content = '{$board[bo_use_list_content]}',
                bo_table_width = '{$board[bo_table_width]}',
                bo_subject_len = '{$board[bo_subject_len]}',
                bo_mobile_subject_len = '{$board[bo_mobile_subject_len]}',
                bo_page_rows = '{$board[bo_page_rows]}',
                bo_mobile_page_rows = '{$board[bo_mobile_page_rows]}',
                bo_new = '{$board[bo_new]}',
                bo_hot = '{$board[bo_hot]}',
                bo_image_width = '{$board[bo_image_width]}',
                bo_skin = '{$board['bo_skin']}',
                bo_mobile_skin = '{$board['bo_mobile_skin']}',
                bo_include_head = '{$board['bo_include_head']}',
                bo_include_tail = '{$board['bo_include_tail']}',
                bo_content_head = '".addslashes($board['bo_content_head'])."',
                bo_content_tail = '".addslashes($board['bo_content_tail'])."',
                bo_mobile_content_head = '".addslashes($board['bo_mobile_content_head'])."',
                bo_mobile_content_tail = '".addslashes($board['bo_mobile_content_tail'])."',
                bo_insert_content = '".addslashes($board['bo_insert_content'])."',
                bo_gallery_cols = '{$board[bo_gallery_cols]}',
                bo_gallery_width = '{$board[bo_gallery_width]}',
                bo_gallery_height = '{$board[bo_gallery_height]}',
                bo_mobile_gallery_width = '{$board[bo_mobile_gallery_width]}',
                bo_mobile_gallery_height = '{$board[bo_mobile_gallery_height]}',
                bo_upload_size = '{$board[bo_upload_size]}',
                bo_reply_order = '{$board[bo_reply_order]}',
                bo_use_search = '{$board[bo_use_search]}',
                bo_order = '{$board[bo_order]}',
                bo_notice = '{$board['bo_notice']}',
                bo_upload_count = '{$board[bo_upload_count]}',
                bo_use_email = '{$board[bo_use_email]}',
                bo_use_cert = '{$board[bo_use_cert]}',
                bo_use_sns = '{$board[bo_use_sns]}',
                bo_sort_field = '{$board['bo_sort_field']}',
                bo_1_subj = '{$board['bo_1_subj']}',
                bo_2_subj = '{$board['bo_2_subj']}',
                bo_3_subj = '{$board['bo_3_subj']}',
                bo_4_subj = '{$board['bo_4_subj']}',
                bo_5_subj = '{$board['bo_5_subj']}',
                bo_6_subj = '{$board['bo_6_subj']}',
                bo_7_subj = '{$board['bo_7_subj']}',
                bo_8_subj = '{$board['bo_8_subj']}',
                bo_9_subj = '{$board['bo_9_subj']}',
                bo_10_subj = '{$board['bo_10_subj']}',
                bo_1 = '{$board['bo_1']}',
                bo_2 = '{$board['bo_2']}',
                bo_3 = '{$board['bo_3']}',
                bo_4 = '{$board['bo_4']}',
                bo_5 = '{$board['bo_5']}',
                bo_6 = '{$board['bo_6']}',
                bo_7 = '{$board['bo_7']}',
                bo_8 = '{$board['bo_8']}',
                bo_9 = '{$board['bo_9']}',
                bo_10 = '{$board['bo_10']}' ";
sql_query($sql, false);

// 创建论坛版块目录
@mkdir(G5_DATA_PATH.'/file/'.$target_table, G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH.'/file/'.$target_table, G5_DIR_PERMISSION);

// 将目录内的文件进行隐藏
$board_path = G5_DATA_PATH.'/file/'.$target_table;
$file = $board_path . '/index.php';
$f = @fopen($file, 'w');
@fwrite($f, '');
@fclose($f);
@chmod($file, G5_FILE_PERMISSION);

$copy_file = 0;
if ($copy_case == 'schema_data_both') {
    $d = dir(G5_DATA_PATH.'/file/'.$bo_table);
    while ($entry = $d->read()) {
        if ($entry == '.' || $entry == '..') continue;

        // sir 金善勇 201007 :
        if(is_dir(G5_DATA_PATH.'/file/'.$bo_table.'/'.$entry)){
            $dd = dir(G5_DATA_PATH.'/file/'.$bo_table.'/'.$entry);
            @mkdir(G5_DATA_PATH.'/file/'.$target_table.'/'.$entry, G5_DIR_PERMISSION);
            @chmod(G5_DATA_PATH.'/file/'.$target_table.'/'.$entry, G5_DIR_PERMISSION);
            while ($entry2 = $dd->read()) {
                if ($entry2 == '.' || $entry2 == '..') continue;
                @copy(G5_DATA_PATH.'/file/'.$bo_table.'/'.$entry.'/'.$entry2, G5_DATA_PATH.'/file/'.$target_table.'/'.$entry.'/'.$entry2);
                @chmod(G5_DATA_PATH.'/file/'.$target_table.'/'.$entry.'/'.$entry2, G5_DIR_PERMISSION);
                $copy_file++;
            }
            $dd->close();
        }
        else {
            @copy(G5_DATA_PATH.'/file/'.$bo_table.'/'.$entry, G5_DATA_PATH.'/file/'.$target_table.'/'.$entry);
            @chmod(G5_DATA_PATH.'/file/'.$target_table.'/'.$entry, G5_DIR_PERMISSION);
            $copy_file++;
        }
    }
    $d->close();

    // 复制主题
    $sql = " insert into {$g5['write_prefix']}$target_table select * from {$g5['write_prefix']}$bo_table ";
    sql_query($sql, false);

    // 储存主题数量
    $sql = " select bo_count_write, bo_count_comment from {$g5['board_table']} where bo_table = '$bo_table' ";
    $row = sql_fetch($sql);
    $sql = " update {$g5['board_table']} set bo_count_write = '{$row['bo_count_write']}', bo_count_comment = '{$row['bo_count_comment']}' where bo_table = '$target_table' ";
    sql_query($sql, false);

    // 4.00.01
    // 会发生使用相同数据表名称的bug
    $sql = " select * from {$g5['board_file_table']} where bo_table = '$bo_table' ";
    $result = sql_query($sql, false);
    for ($i=0; $row=sql_fetch_array($result); $i++)
        $file_copy[$i] = $row;
}

if (count($file_copy)) {
    for ($i=0; $i<count($file_copy); $i++) {
        $sql = " insert into {$g5['board_file_table']}
                    set bo_table = '$target_table',
                         wr_id = '{$file_copy[$i]['wr_id']}',
                         bf_no = '{$file_copy[$i]['bf_no']}',
                         bf_source = '".addslashes($file_copy[$i]['bf_source'])."',
                         bf_file = '{$file_copy[$i]['bf_file']}',
                         bf_download = '{$file_copy[$i]['bf_download']}',
                         bf_content = '".addslashes($file_copy[$i]['bf_content'])."',
                         bf_filesize = '{$file_copy[$i]['bf_filesize']}',
                         bf_width = '{$file_copy[$i]['bf_width']}',
                         bf_height = '{$file_copy[$i]['bf_height']}',
                         bf_type = '{$file_copy[$i]['bf_type']}',
                         bf_datetime = '{$file_copy[$i]['bf_datetime']}' ";
        sql_query($sql, false);
    }
}

delete_cache_latest($bo_table);
delete_cache_latest($target_table);

echo "<script>opener.document.location.reload();</script>";

alert("复制完成！", './board_copy.php?bo_table='.$bo_table.'&amp;'.$qstr);
?>