<?php
include_once('./_common.php');

$html_title = '链接 &gt; '.conv_subject($write['wr_subject'], 255);

if (!($bo_table && $wr_id && $no))
    alert_close('参数传递错误');

// SQL Injection 预防
$row = sql_fetch(" select count(*) as cnt from {$g5['write_prefix']}{$bo_table} ", FALSE);
if (!$row['cnt'])
    alert_close('您访问的论坛版块不存在！');

if (!$write['wr_link'.$no])
    alert_close('链接地址不存在');

$ss_name = 'ss_link_'.$bo_table.'_'.$wr_id.'_'.$no;
if (empty($_SESSION[$ss_name]))
{
    $sql = " update {$g5['write_prefix']}{$bo_table} set wr_link{$no}_hit = wr_link{$no}_hit + 1 where wr_id = '{$wr_id}' ";
    sql_query($sql);

    set_session($ss_name, true);
}

goto_url(set_http($write['wr_link'.$no]));
?>