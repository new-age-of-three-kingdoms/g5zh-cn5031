<?php
include_once('./_common.php');

if($is_guest)
    alert('请您登录后使用', G5_URL);

$tmp_array = array();
if ($qa_id) // 逐条删除
    $tmp_array[0] = $qa_id;
else //批量删除
    $tmp_array = $_POST['chk_qa_id'];

$count = count($tmp_array);
if(!$count)
    alert('请选择需要删除的内容');

for($i=0; $i<$count; $i++) {
    $qa_id = $tmp_array[$i];

    $sql = " select qa_id, mb_id, qa_type, qa_status, qa_parent, qa_content, qa_file1, qa_file2
                from {$g5['qa_content_table']}
                where qa_id = '$qa_id' ";
    $row = sql_fetch($sql);

    if(!$row['qa_id'])
        continue;

    // 如果不是作者文章则跳过
    if($is_admin != 'super' && $row['mb_id'] != $member['mb_id'])
        continue;

    // 附件删除附件
    for($k=1; $k<=2; $k++) {
        @unlink(G5_DATA_PATH.'/qa/'.$row['qa_file'.$k]);
        // 缩略图删除
        if(preg_match("/\.({$config['cf_image_extension']})$/i", $row['qa_file'.$k])) {
            delete_qa_thumbnail($row['qa_file'.$k]);
        }
    }

    // 删除编辑器的缩略图
    delete_editor_thumbnail($row['qa_content']);

    // 如有回复的提问则删除回复
    if(!$row['qa_type'] && $row['qa_status']) {
        $row2 = sql_fetch(" select qa_content, qa_file1, qa_file2 from {$g5['qa_content_table']} where qa_parent = '$qa_id' ");
        // 附件删除附件
        for($k=1; $k<=2; $k++) {
            @unlink(G5_DATA_PATH.'/qa/'.$row2['qa_file'.$k]);
            // 缩略图删除
            if(preg_match("/\.({$config['cf_image_extension']})$/i", $row2['qa_file'.$k])) {
                delete_qa_thumbnail($row2['qa_file'.$k]);
            }
        }

        // 删除编辑器的缩略图
        delete_editor_thumbnail($row2['qa_content']);

        sql_query(" delete from {$g5['qa_content_table']} where qa_type = '1' and qa_parent = '$qa_id' ");
    }

    // 删除回帖时恢复提问状态
    if($row['qa_type']) {
        sql_query(" update {$g5['qa_content_table']} set qa_status = '0' where qa_id = '{$row['qa_parent']}' ");
    }

    // 删除内容
    sql_query(" delete from {$g5['qa_content_table']} where qa_id = '$qa_id' ");
}

goto_url(G5_BBS_URL.'/qalist.php'.preg_replace('/^&amp;/', '?', $qstr));
?>