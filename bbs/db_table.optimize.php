<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// 只有在网站管理员访问时允许操作
if($config['cf_admin'] != $member['mb_id'] || $is_admin != 'super')
    return;

// 对比执行日期
if(isset($config['cf_optimize_date']) && $config['cf_optimize_date'] >= G5_TIME_YMD)
    return;

// 删除达到指定日期的访问统计数据
if($config['cf_visit_del'] > 0) {
    $tmp_before_date = date("Y-m-d", G5_SERVER_TIME - ($config['cf_visit_del'] * 86400));
    $sql = " delete from {$g5['visit_table']} where vi_date < '$tmp_before_date' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$g5['visit_table']}`, `{$g5['visit_sum_table']}` ");
}

// 删除达到指定日期热门关键词
if($config['cf_popular_del'] > 0) {
    $tmp_before_date = date("Y-m-d", G5_SERVER_TIME - ($config['cf_popular_del'] * 86400));
    $sql = " delete from {$g5['popular_table']} where pp_date < '$tmp_before_date' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$g5['popular_table']}` ");
}

// 删除达到指定日期最新文章标示
if($config['cf_new_del'] > 0) {
    $sql = " delete from {$g5['board_new_table']} where (TO_DAYS('".G5_TIME_YMDHIS."') - TO_DAYS(bn_datetime)) > '{$config['cf_new_del']}' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$g5['board_new_table']}` ");
}

// 删除达到指定日期短信
if($config['cf_memo_del'] > 0) {
    $sql = " delete from {$g5['memo_table']} where (TO_DAYS('".G5_TIME_YMDHIS."') - TO_DAYS(me_send_datetime)) > '{$config['cf_memo_del']}' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$g5['memo_table']}` ");
}

// 自动删除会员（已删除会员账号）
if($config['cf_leave_day'] > 0) {
    $sql = " select mb_id from {$g5['member_table']} where (TO_DAYS('".G5_TIME_YMDHIS."') - TO_DAYS(mb_leave_date)) > '{$config['cf_leave_day']}' ";
    $result = sql_query($sql);
    while ($row=sql_fetch_array($result))
    {
        // 删除会员数据
        member_delete($row['mb_id']);
    }
}

// 删除语音验证码缓存
$captcha_mp3 = glob(G5_PATH.'/data/cache/kcaptcha-*.mp3');
if($captcha_mp3 && is_array($captcha_mp3)) {
    foreach ($captcha_mp3 as $file) {
        if (filemtime($file) + 86400 < G5_SERVER_TIME) {
            @unlink($file);
        }
    }
}

// 记录执行日期
if(isset($config['cf_optimize_date'])) {
    sql_query(" update {$g5['config_table']} set cf_optimize_date = '".G5_TIME_YMD."' ");
}
?>