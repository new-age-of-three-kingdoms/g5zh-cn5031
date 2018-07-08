<?php
include_once('./_common.php');

if (isset($_SESSION['ss_mb_reg']))
    $mb = get_member($_SESSION['ss_mb_reg']);

// 如无会员信息则返回上一页
if (!$mb['mb_id'])
    goto_url(G5_URL);

$g5['title'] = '祝贺您成为会员！';
include_once('./_head.php');
include_once($member_skin_path.'/register_result.skin.php');
include_once('./_tail.php');
?>