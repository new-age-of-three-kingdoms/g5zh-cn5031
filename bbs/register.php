<?php
include_once('./_common.php');

// 已登录会员不能进行注册
if ($is_member) {
    goto_url(G5_URL);
}

// 清除session
set_session("ss_mb_reg", "");

$g5['title'] = '会员注册条款';
include_once('./_head.php');

$register_action_url = G5_BBS_URL.'/register_form.php';
include_once($member_skin_path.'/register.skin.php');

include_once('./_tail.php');
?>
