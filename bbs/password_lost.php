<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

if ($is_member) {
    alert("您已登录");
}

$g5['title'] = '找回会员信息';
include_once(G5_PATH.'/head.sub.php');

$action_url = G5_HTTPS_BBS_URL."/password_lost2.php";
include_once($member_skin_path.'/password_lost.skin.php');

include_once(G5_PATH.'/tail.sub.php');
?>