<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

if ($is_guest)
    alert_close('请登录后使用，如不是会员请您注册');

if (!$member['mb_open'] && $is_admin != 'super' && $member['mb_id'] != $mb_id)
    alert_close("由于您未公开个人信息不能向其他会员发送站内短信，请到会员信息设置中进行修改");

$content = "";
// 不能向已删除账号发送短信
if ($me_recv_mb_id)
{
    $mb = get_member($me_recv_mb_id);
    if (!$mb['mb_id'])
        alert_close('未找到会员数据\\n\\n可能已被删除会员账号');

    if (!$mb['mb_open'] && $is_admin != 'super')
        alert_close('对方已设置关闭接收短信');

    // 4.00.15
    $row = sql_fetch(" select me_memo from {$g5['memo_table']} where me_id = '{$me_id}' and (me_recv_mb_id = '{$member['mb_id']}' or me_send_mb_id = '{$member['mb_id']}') ");
    if ($row['me_memo'])
    {
        $content = "\n\n\n".' >'
                         ."\n".' >'
                         ."\n".' >'.str_replace("\n", "\n> ", get_text($row['me_memo'], 0))
                         ."\n".' >'
                         .' >';

    }
}

$g5['title'] = '发送短信';
include_once(G5_PATH.'/head.sub.php');

$memo_action_url = G5_HTTPS_BBS_URL."/memo_form_update.php";
include_once($member_skin_path.'/memo_form.skin.php');

include_once(G5_PATH.'/tail.sub.php');
?>
