<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// 当客户端ip地址与cookie中储存的ip地址不同时更新到数据
if (get_cookie('ck_visit_ip') != $_SERVER['REMOTE_ADDR'])
{
    set_cookie('ck_visit_ip', $_SERVER['REMOTE_ADDR'], 86400); // 保存一天

    $tmp_row = sql_fetch(" select max(vi_id) as max_vi_id from {$g5['visit_table']} ");
    $vi_id = $tmp_row['max_vi_id'] + 1;

    // 用于预防伪造$_SERVER排列参数进行sql注入攻击 110810
    $remote_addr = escape_trim($_SERVER['REMOTE_ADDR']);
    $referer = "";
    if (isset($_SERVER['HTTP_REFERER']))
        $referer = escape_trim(clean_xss_tags($_SERVER['HTTP_REFERER']));
    $user_agent  = escape_trim($_SERVER['HTTP_USER_AGENT']);
    $sql = " insert {$g5['visit_table']} ( vi_id, vi_ip, vi_date, vi_time, vi_referer, vi_agent ) values ( '{$vi_id}', '{$remote_addr}', '".G5_TIME_YMD."', '".G5_TIME_HIS."', '{$referer}', '{$user_agent}' ) ";

    $result = sql_query($sql, FALSE);
    // insert正常就提交到访问统计合计
    if ($result) {
        $sql = " insert {$g5['visit_sum_table']} ( vs_count, vs_date) values ( 1, '".G5_TIME_YMD."' ) ";
        $result = sql_query($sql, FALSE);

        // DUPLICATE出现错误时因已有更新日期，利用此日期进行UPDATE
        if (!$result) {
            $sql = " update {$g5['visit_sum_table']} set vs_count = vs_count + 1 where vs_date = '".G5_TIME_YMD."' ";
            $result = sql_query($sql);
        }

        // 如有已执行insert，update储存到基本设置中
        // 由于不会在每次访问时独立运行查询会大大提高运行效率及缩减代码

        // 今天
        $sql = " select vs_count as cnt from {$g5['visit_sum_table']} where vs_date = '".G5_TIME_YMD."' ";
        $row = sql_fetch($sql);
        $vi_today = $row['cnt'];

        // 昨天
        $sql = " select vs_count as cnt from {$g5['visit_sum_table']} where vs_date = DATE_SUB('".G5_TIME_YMD."', INTERVAL 1 DAY) ";
        $row = sql_fetch($sql);
        $vi_yesterday = $row['cnt'];

        // 最大
        $sql = " select max(vs_count) as cnt from {$g5['visit_sum_table']} ";
        $row = sql_fetch($sql);
        $vi_max = $row['cnt'];

        // 全部
        $sql = " select sum(vs_count) as total from {$g5['visit_sum_table']} ";
        $row = sql_fetch($sql);
        $vi_sum = $row['total'];

        $visit = '今天:'.$vi_today.',昨天:'.$vi_yesterday.',最大:'.$vi_max.',全部:'.$vi_sum;

        // 在基本设置储存访问记录后
        // 不读取访问数据直接显示
        // 减少数据查询负荷
        sql_query(" update {$g5['config_table']} set cf_visit = '{$visit}' ");
    }
}
?>
