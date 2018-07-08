<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/register.lib.php');

// 防止非法链接创建token
$token = md5(uniqid(rand(), true));
set_session("ss_token", $token);
set_session("ss_cert_no",   "");
set_session("ss_cert_hash", "");
set_session("ss_cert_type", "");

if ($w == "") {

    // 已登录的会员禁止再进行注册
    // 防止显示警告窗口，使用下方代码代替
    // alert("您已经是会员", "./");
    if ($is_member) {
        goto_url(G5_URL);
    }

    // referer检查
    referer_check();

    if (!isset($_POST['agree']) || !$_POST['agree']) {
        alert('您需要同意会员注册条款才能进行注册', G5_BBS_URL.'/register.php');
    }

    if (!isset($_POST['agree2']) || !$_POST['agree2']) {
        alert('您需要同意个人隐私保护条例才能进行注册', G5_BBS_URL.'/register.php');
    }

    $member['mb_birth'] = '';
    $member['mb_sex']   = '';
    $member['mb_name']  = '';
    if (isset($_POST['birth'])) {
        $member['mb_birth'] = $_POST['birth'];
    }
    if (isset($_POST['sex'])) {
        $member['mb_sex']   = $_POST['sex'];
    }
    if (isset($_POST['mb_name'])) {
        $member['mb_name']  = $_POST['mb_name'];
    }

    $g5['title'] = '注册会员';

} else if ($w == 'u') {

    if ($is_admin)
        alert('更改管理员账户信息请到系统后台进行修改', G5_URL);

    if (!$is_member)
        alert('请您登录后使用', G5_URL);

    if ($member['mb_id'] != $_POST['mb_id'])
        alert('登录信息发生错误，请重新尝试！');

    /*
    if (!($member[mb_password] == sql_password($_POST[mb_password]) && $_POST[mb_password]))
        alert("密码错误");

    // 为了修改后可以返回临时储存
    set_session("ss_tmp_password", $_POST[mb_password]);
    */

    if ($_POST['mb_password']) {
        // 修改后返回的数据进行update后密码是加密形式
        if ($_POST['is_update'])
            $tmp_password = $_POST['mb_password'];
        else
            $tmp_password = sql_password($_POST['mb_password']);

        if ($member['mb_password'] != $tmp_password)
            alert('密码错误');
    }

    $g5['title'] = '修改会员信息';

    set_session("ss_reg_mb_name", $member['mb_name']);
    set_session("ss_reg_mb_hp", $member['mb_hp']);

    $member['mb_email']       = get_text($member['mb_email']);
    $member['mb_homepage']    = get_text($member['mb_homepage']);
    $member['mb_birth']       = get_text($member['mb_birth']);
    $member['mb_tel']         = get_text($member['mb_tel']);
    $member['mb_hp']          = get_text($member['mb_hp']);
    $member['mb_addr1']       = get_text($member['mb_addr1']);
    $member['mb_addr2']       = get_text($member['mb_addr2']);
    $member['mb_signature']   = get_text($member['mb_signature']);
    $member['mb_recommend']   = get_text($member['mb_recommend']);
    $member['mb_profile']     = get_text($member['mb_profile']);
    $member['mb_1']           = get_text($member['mb_1']);
    $member['mb_2']           = get_text($member['mb_2']);
    $member['mb_3']           = get_text($member['mb_3']);
    $member['mb_4']           = get_text($member['mb_4']);
    $member['mb_5']           = get_text($member['mb_5']);
    $member['mb_6']           = get_text($member['mb_6']);
    $member['mb_7']           = get_text($member['mb_7']);
    $member['mb_8']           = get_text($member['mb_8']);
    $member['mb_9']           = get_text($member['mb_9']);
    $member['mb_10']          = get_text($member['mb_10']);
} else {
    alert('w值传递错误！');
}

include_once('./_head.php');

// 会员头像路径
$mb_icon_path = G5_DATA_PATH.'/member/'.substr($member['mb_id'],0,2).'/'.$member['mb_id'].'.gif';
$mb_icon_url  = G5_DATA_URL.'/member/'.substr($member['mb_id'],0,2).'/'.$member['mb_id'].'.gif';

$register_action_url = G5_HTTPS_BBS_URL.'/register_form_update.php';
$req_nick = !isset($member['mb_nick_date']) || (isset($member['mb_nick_date']) && $member['mb_nick_date'] <= date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400)));
$required = ($w=='') ? 'required' : '';
$readonly = ($w=='u') ? 'readonly' : '';

// add_javascript('js语句', 显示顺序); 数字小的优先显示
if ($config['cf_use_addr'])
    add_javascript(G5_POSTCODE_JS, 0);    //js地址

include_once($member_skin_path.'/register_form.skin.php');
include_once('./_tail.php');
?>