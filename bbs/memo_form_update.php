<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

if ($is_guest)
    alert('请登录后使用，如不是会员请您注册');

if (!chk_captcha()) {
    alert('您输入的验证码错误，请重新输入');
}

$recv_list = explode(',', trim($_POST['me_recv_mb_id']));
$str_nick_list = '';
$msg = '';
$error_list  = array();
$member_list = array();
for ($i=0; $i<count($recv_list); $i++) {
    $row = sql_fetch(" select mb_id, mb_nick, mb_open, mb_leave_date, mb_intercept_date from {$g5['member_table']} where mb_id = '{$recv_list[$i]}' ");
    if ($row) {
        if ($is_admin || ($row['mb_open'] && (!$row['mb_leave_date'] || !$row['mb_intercept_date']))) {
            $member_list['id'][]   = $row['mb_id'];
            $member_list['nick'][] = $row['mb_nick'];
        } else {
            $error_list[]   = $recv_list[$i];
        }
    }
    /*
    // 如果不是管理员
    // 游客、未公开个人信息的会员、已删除会员或已被封号会员发送时显示错误
    if ((!$row['mb_id'] || !$row['mb_open'] || $row['mb_leave_date'] || $row['mb_intercept_date']) && !$is_admin) {
        $error_list[]   = $recv_list[$i];
    } else {
        $member_list['id'][]   = $row['mb_id'];
        $member_list['nick'][] = $row['mb_nick'];
    }
    */
}

$error_msg = implode(",", $error_list);

if ($error_msg && !$is_admin)
    alert("会员账号 '{$error_msg}'不存在或未公开信息设置（同时已删除账号、封号账号禁止收发短信）\\n发送短信操作已取消");

if (!$is_admin) {
    if (count($member_list['id'])) {
        $point = (int)$config['cf_memo_send_point'] * count($member_list['id']);
        if ($point) {
            if ($member['mb_point'] - $point < 0) {
                alert('当前积分('.number_format($member['mb_point']).'分)不足支付发送短信所需积分');
            }
        }
    }
}

for ($i=0; $i<count($member_list['id']); $i++) {
    $tmp_row = sql_fetch(" select max(me_id) as max_me_id from {$g5['memo_table']} ");
    $me_id = $tmp_row['max_me_id'] + 1;

    $recv_mb_id   = $member_list['id'][$i];
    $recv_mb_nick = get_text($member_list['nick'][$i]);

    // 短信 INSERT
    $sql = " insert into {$g5['memo_table']} ( me_id, me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo ) values ( '$me_id', '$recv_mb_id', '{$member['mb_id']}', '".G5_TIME_YMDHIS."', '{$_POST['me_memo']}' ) ";
    sql_query($sql);

    // 短信实时提醒
    $sql = " update {$g5['member_table']} set mb_memo_call = '{$member['mb_id']}' where mb_id = '$recv_mb_id' ";
    sql_query($sql);

    if (!$is_admin) {
        insert_point($member['mb_id'], (int)$config['cf_memo_send_point'] * (-1), 向$recv_mb_nick.'('.$recv_mb_id.')发送短信', '@memo', $recv_mb_id, $me_id);
    }
}

if ($member_list) {
    $str_nick_list = implode(',', $member_list['nick']);
    alert("向会员".$str_nick_list." 短信发送完成", G5_HTTP_BBS_URL."/memo.php?kind=send", false);
} else {
    alert("会员账户错误！", G5_HTTP_BBS_URL."/memo_form.php", false);
}
?>