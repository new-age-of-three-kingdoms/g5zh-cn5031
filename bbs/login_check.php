<?php
include_once('./_common.php');

$g5['title'] = "登录检查";

$mb_id       = trim($_POST['mb_id']);
$mb_password = trim($_POST['mb_password']);

if (!$mb_id || !$mb_password)
    alert('请输入用户名或密码');

$mb = get_member($mb_id);

// 不显示已注册会员信息的原因如下
// 通常黑客会使用排除及密码字典进行破解
// 所以在尝试时不提示id或密码错误，进行模糊提示，使攻击者难以判断是密码错误还是id错误
if (!$mb['mb_id'] || (sql_password($mb_password) != $mb['mb_password'])) {
    alert('您输入的用户名或密码错误！\\n请注意密码区分大小写字母');
}

// 是否是已封号会员?
if ($mb['mb_intercept_date'] && $mb['mb_intercept_date'] <= date("Ymd", G5_SERVER_TIME)) {
    $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1年 \\2月 \\3日", $mb['mb_intercept_date']);
    alert('此会员账号已被封号处理\n处理时间 : '.$date);
}

// 是否是删除的账号
if ($mb['mb_leave_date'] && $mb['mb_leave_date'] <= date("Ymd", G5_SERVER_TIME)) {
    $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1年 \\2月 \\3日", $mb['mb_leave_date']);
    alert('此会员账号已被删除\n注销删除时间 : '.$date);
}

if ($config['cf_use_email_certify'] && !preg_match("/[1-9]/", $mb['mb_email_certify'])) {
    confirm("{$mb['mb_email']} 您需要完成邮箱地址验证才能正常登录使用，如您需要更换验证邮箱请点击取消", G5_URL, G5_BBS_URL.'/register_email.php?mb_id='.$mb_id);
}

@include_once($member_skin_path.'/login_check.skin.php');

// 创建会员id session
set_session('ss_mb_id', $mb['mb_id']);
// 为了防止flash xss攻击设置会员固定参数
set_session('ss_mb_key', md5($mb['mb_datetime'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));

// 积分检查
if($config['cf_use_point']) {
    $sum_point = get_point_sum($mb['mb_id']);

    $sql= " update {$g5['member_table']} set mb_point = '$sum_point' where mb_id = '{$mb['mb_id']}' ";
    sql_query($sql);
}

// 3.26
// 储存cookie 1个月
if ($auto_login) {
    // 3.27
    // 自动登录 ---------------------------
    // cookie储存1个月
    $key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $mb['mb_password']);
    set_cookie('ck_mb_id', $mb['mb_id'], 86400 * 31);
    set_cookie('ck_auto', $key, 86400 * 31);
    // 自动登录 end ---------------------------
} else {
    set_cookie('ck_mb_id', '', 0);
    set_cookie('ck_auto', '', 0);
}

if ($url) {
    $link = urldecode($url);
    // 2003-06-14 添加 (为了传递其他参数)
    if (preg_match("/\?/", $link))
        $split= "&amp;";
    else
        $split= "?";

    // $_POST 排列参数仅传递没有以下内容的参数
    foreach($_POST as $key=>$value) {
        if ($key != 'mb_id' && $key != 'mb_password' && $key != 'x' && $key != 'y' && $key != 'url') {
            $link .= "$split$key=$value";
            $split = "&amp;";
        }
    }
} else  {
    $link = G5_URL;
}

goto_url($link);
?>
