<?php
// board_delete.php , boardgroup_delete.php 中include的文件

if (!defined('_GNUBOARD_')) exit;
if (!defined('_BOARD_DELETE_')) exit; //禁止单独访问此页

// $tmp_bo_table需要传递$bo_table值
if (!$tmp_bo_table) { return; }

// 为了进行复制唯一的论坛将不会被删除
//$row = sql_fetch(" select count(*) as cnt from $g5['board_table'] ");
//if ($row['cnt'] <= 1) { return; }

// 删除论坛版块设置
sql_query(" delete from {$g5['board_table']} where bo_table = '{$tmp_bo_table}' ");

// 删除最新文章
sql_query(" delete from {$g5['board_new_table']} where bo_table = '{$tmp_bo_table}' ");

// 删除收藏
sql_query(" delete from {$g5['scrap_table']} where bo_table = '{$tmp_bo_table}' ");

// 删除附件
sql_query(" delete from {$g5['board_file_table']} where bo_table = '{$tmp_bo_table}' ");

// 论坛版块数据表 DROP
sql_query(" drop table {$g5['write_prefix']}{$tmp_bo_table} ", FALSE);

delete_cache_latest($tmp_bo_table);

// 删除论坛版块文件夹
rm_rf(G5_DATA_PATH.'/file/'.$tmp_bo_table);
?>