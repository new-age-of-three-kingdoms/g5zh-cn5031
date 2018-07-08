<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

if (!$config['cf_email_use'])
    alert_close('需要在网站设置中开启邮件发送功能后才能使用\\n\\n请阅读使用指南或联络管理员');

if (!$is_member && $config['cf_formmail_is_member'])
    alert_close('请登录后使用，如不是会员请您注册');

if ($is_member && !$member['mb_open'] && $is_admin != "super" && $member['mb_id'] != $mb_id)
    alert_close('您由于未公开个人信息设置不能向其他会员发送邮件\\n\\n请到会员信息设置中进行修改');

if ($mb_id)
{
    $mb = get_member($mb_id);
    if (!$mb['mb_id'])
        alert_close('未找到会员数据\\n\\n会员账号可能已被删除');

    if (!$mb['mb_open'] && $is_admin != "super")
        alert_close('对方已设置关闭接收短信');
}

$sendmail_count = (int)get_session('ss_sendmail_count') + 1;
if ($sendmail_count > 3)
    alert_close('每次登陆均有发送邮件次数限制\\n\\n如果您需要继续发送邮件请您重新登录');

$g5['title'] = '编辑邮件';
include_once(G5_PATH.'/head.sub.php');

if (!$name)
    $name = base64_decode($email);

if (!isset($type))
    $type = 0;

$type_checked[0] = $type_checked[1] = $type_checked[2] = "";
$type_checked[$type] = 'checked';

include_once($member_skin_path.'/formmail.skin.php');

include_once(G5_PATH.'/tail.sub.php');
?>
