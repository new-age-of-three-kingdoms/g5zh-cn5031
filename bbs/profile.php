<?php
include_once('./_common.php');

if (!$member['mb_id'])
    alert_close('请登录后使用，如不是会员请您注册');

if (!$member['mb_open'] && $is_admin != 'super' && $member['mb_id'] != $mb_id)
    alert_close('由于您为公开个人信息，不能查看其它会员信息\\n\\n请到会员信息设置中进行修改');

$mb = get_member($mb_id);
if (!$mb['mb_id'])
    alert_close('未找到会员数据\\n\\n可能已被删除会员账号');

if (!$mb['mb_open'] && $is_admin != 'super' && $member['mb_id'] != $mb_id)
    alert_close('对方已设置关闭接收短信');

$g5['title'] = $mb['mb_nick'].'的自我介绍';
include_once(G5_PATH.'/head.sub.php');

$mb_nick = get_sideview($mb['mb_id'], $mb['mb_nick'], $mb['mb_email'], $mb['mb_homepage'], $mb['mb_open']);

// 已注册日期，+1是为了包括注册当天
$sql = " select (TO_DAYS('".G5_TIME_YMDHIS."') - TO_DAYS('{$mb['mb_datetime']}') + 1) as days ";
$row = sql_fetch($sql);
$mb_reg_after = $row['days'];

$mb_homepage = set_http(clean_xss_tags($mb['mb_homepage']));
$mb_profile = $mb['mb_profile'] ? conv_content($mb['mb_profile'],0) : '这家伙很懒，没有留下任何介绍';

include_once($member_skin_path.'/profile.skin.php');

include_once(G5_PATH.'/tail.sub.php');
?>
