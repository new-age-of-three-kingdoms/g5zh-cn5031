<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/register.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

// referer检查
referer_check();

if (!($w == '' || $w == 'u')) {
    alert('w值传递错误！');
}

if ($w == 'u' && $is_admin == 'super') {
    if (file_exists(G5_PATH.'/DEMO'))
        alert('当前功能无法在演示(Demo)模式下使用');
}

if (!chk_captcha()) {
    alert('您输入的验证码错误，请重新输入');
}

if($w == 'u')
    $mb_id = isset($_SESSION['ss_mb_id']) ? trim($_SESSION['ss_mb_id']) : '';
else if($w == '')
    $mb_id = trim($_POST['mb_id']);
else
    alert('请使用正确方式访问', G5_URL);

if(!$mb_id)
    alert('您的操作已被记录，请使用正常方式登录使用！');

$mb_password    = trim($_POST['mb_password']);
$mb_password_re = trim($_POST['mb_password_re']);
$mb_name        = trim($_POST['mb_name']);
$mb_nick        = trim($_POST['mb_nick']);
$mb_email       = trim($_POST['mb_email']);
$mb_sex         = isset($_POST['mb_sex'])           ? trim($_POST['mb_sex'])         : "";
$mb_birth       = isset($_POST['mb_birth'])         ? trim($_POST['mb_birth'])       : "";
$mb_homepage    = isset($_POST['mb_homepage'])      ? trim($_POST['mb_homepage'])    : "";
$mb_tel         = isset($_POST['mb_tel'])           ? trim($_POST['mb_tel'])         : "";
$mb_hp          = isset($_POST['mb_hp'])            ? trim($_POST['mb_hp'])          : "";
$mb_zip1        = isset($_POST['mb_zip1'])          ? trim($_POST['mb_zip1'])        : "";
$mb_zip2        = isset($_POST['mb_zip2'])          ? trim($_POST['mb_zip2'])        : "";
$mb_addr1       = isset($_POST['mb_addr1'])         ? trim($_POST['mb_addr1'])       : "";
$mb_addr2       = isset($_POST['mb_addr2'])         ? trim($_POST['mb_addr2'])       : "";
$mb_addr3       = isset($_POST['mb_addr3'])         ? trim($_POST['mb_addr3'])       : "";
$mb_addr_jibeon = isset($_POST['mb_addr_jibeon'])   ? trim($_POST['mb_addr_jibeon']) : "";
$mb_signature   = isset($_POST['mb_signature'])     ? trim($_POST['mb_signature'])   : "";
$mb_profile     = isset($_POST['mb_profile'])       ? trim($_POST['mb_profile'])     : "";
$mb_recommend   = isset($_POST['mb_recommend'])     ? trim($_POST['mb_recommend'])   : "";
$mb_mailling    = isset($_POST['mb_mailling'])      ? trim($_POST['mb_mailling'])    : "";
$mb_sms         = isset($_POST['mb_sms'])           ? trim($_POST['mb_sms'])         : "";
$mb_1           = isset($_POST['mb_1'])             ? trim($_POST['mb_1'])           : "";
$mb_2           = isset($_POST['mb_2'])             ? trim($_POST['mb_2'])           : "";
$mb_3           = isset($_POST['mb_3'])             ? trim($_POST['mb_3'])           : "";
$mb_4           = isset($_POST['mb_4'])             ? trim($_POST['mb_4'])           : "";
$mb_5           = isset($_POST['mb_5'])             ? trim($_POST['mb_5'])           : "";
$mb_6           = isset($_POST['mb_6'])             ? trim($_POST['mb_6'])           : "";
$mb_7           = isset($_POST['mb_7'])             ? trim($_POST['mb_7'])           : "";
$mb_8           = isset($_POST['mb_8'])             ? trim($_POST['mb_8'])           : "";
$mb_9           = isset($_POST['mb_9'])             ? trim($_POST['mb_9'])           : "";
$mb_10          = isset($_POST['mb_10'])            ? trim($_POST['mb_10'])          : "";

if ($w == '' || $w == 'u') {

    if ($msg = empty_mb_id($mb_id))         alert($msg, "", true, true); // alert($msg, $url, $error, $post);
    if ($msg = valid_mb_id($mb_id))         alert($msg, "", true, true);
    if ($msg = count_mb_id($mb_id))         alert($msg, "", true, true);

    if ($w == '' && !$mb_password)
        alert('请输入密码');
    if($w == '' && $mb_password != $mb_password_re)
        alert('您输入的两次密码不一致');

    if ($msg = empty_mb_name($mb_id))       alert($msg, "", true, true);
    if ($msg = empty_mb_nick($mb_nick))     alert($msg, "", true, true);
    if ($msg = empty_mb_email($mb_email))   alert($msg, "", true, true);
    if ($msg = reserve_mb_id($mb_id))       alert($msg, "", true, true);
    if ($msg = reserve_mb_nick($mb_nick))   alert($msg, "", true, true);
    // 不进行姓名文字检查
    //if ($msg = valid_mb_name($mb_name))     alert($msg, "", true, true);
    if ($msg = valid_mb_nick($mb_nick))     alert($msg, "", true, true);
    if ($msg = valid_mb_email($mb_email))   alert($msg, "", true, true);
    if ($msg = prohibit_mb_email($mb_email))alert($msg, "", true, true);

    // 检查手机号码
    if (($config['cf_use_hp'] || $config['cf_cert_hp']) && $config['cf_req_hp']) {
        if ($msg = valid_mb_hp($mb_hp))     alert($msg, "", true, true);
    }

    if ($w=='') {
        if ($msg = exist_mb_id($mb_id))     alert($msg);

        // 实名认证检查
        if($config['cf_cert_use'] && $config['cf_cert_req']) {
            if(trim($_POST['cert_no']) != $_SESSION['ss_cert_no'] || !$_SESSION['ss_cert_no'])
                alert("为了完成注册需要进行实名认证");
        }

        if ($config['cf_use_recommend'] && $mb_recommend) {
            if (!exist_mb_id($mb_recommend))
                alert("找不到您输入的推荐人信息");
        }

        if (strtolower($mb_id) == strtolower($mb_recommend)) {
            alert('您不能设置自己为推荐人');
        }
    } else {
        // 修复利用javascript修改会员信息的bug
        // 如果未达到指定昵称修改期限
        if ($member['mb_nick_date'] > date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400)))
            $mb_nick = $member['mb_nick'];
        // 对比会员邮件地址
        $old_email = $member['mb_email'];
    }

    if ($msg = exist_mb_nick($mb_nick, $mb_id))     alert($msg, "", true, true);
    if ($msg = exist_mb_email($mb_email, $mb_id))   alert($msg, "", true, true);
}

$mb_name        = clean_xss_tags($mb_name);
$mb_email       = get_email_address($mb_email);
$mb_homepage    = clean_xss_tags($mb_homepage);
$mb_tel         = clean_xss_tags($mb_tel);
$mb_zip1        = preg_replace('/[^0-9]/', '', $mb_zip1);
$mb_zip2        = preg_replace('/[^0-9]/', '', $mb_zip2);
$mb_addr1       = clean_xss_tags($mb_addr1);
$mb_addr2       = clean_xss_tags($mb_addr2);
$mb_addr3       = clean_xss_tags($mb_addr3);
$mb_addr_jibeon = preg_match("/^(N|R)$/", $mb_addr_jibeon) ? $mb_addr_jibeon : '';

// 执行用户代码
@include_once($member_skin_path.'/register_form_update.head.skin.php');

//===============================================================
//  实名认证
//---------------------------------------------------------------
$mb_hp = hyphen_hp_number($mb_hp);
if($config['cf_cert_use'] && $_SESSION['ss_cert_type'] && $_SESSION['ss_cert_dupinfo']) {
    // 重复检查
    $sql = " select mb_id from {$g5['member_table']} where mb_id <> '{$member['mb_id']}' and mb_dupinfo = '{$_SESSION['ss_cert_dupinfo']}' ";
    $row = sql_fetch($sql);
    if ($row['mb_id']) {
        alert("您输入的实名认证信息已被其他会员注册使用\\n会员ID : ".$row['mb_id']);
    }
}

$sql_certify = '';
$md5_cert_no = $_SESSION['ss_cert_no'];
$cert_type = $_SESSION['ss_cert_type'];
if ($config['cf_cert_use'] && $cert_type && $md5_cert_no) {
    // 反馈数据正确时储存实名认证结果
    if ($_SESSION['ss_cert_hash'] == md5($mb_name.$cert_type.$_SESSION['ss_cert_birth'].$md5_cert_no)) {
        $sql_certify .= " , mb_hp = '{$mb_hp}' ";
        $sql_certify .= " , mb_certify  = '{$cert_type}' ";
        $sql_certify .= " , mb_adult = '{$_SESSION['ss_cert_adult']}' ";
        $sql_certify .= " , mb_birth = '{$_SESSION['ss_cert_birth']}' ";
        $sql_certify .= " , mb_sex = '{$_SESSION['ss_cert_sex']}' ";
        $sql_certify .= " , mb_dupinfo = '{$_SESSION['ss_cert_dupinfo']}' ";
        if($w == 'u')
            $sql_certify .= " , mb_name = '{$mb_name}' ";
    } else {
        $sql_certify .= " , mb_hp = '{$mb_hp}' ";
        $sql_certify .= " , mb_certify  = '' ";
        $sql_certify .= " , mb_adult = 0 ";
        $sql_certify .= " , mb_birth = '' ";
        $sql_certify .= " , mb_sex = '' ";
    }
} else {
    if (get_session("ss_reg_mb_name") != $mb_name || get_session("ss_reg_mb_hp") != $mb_hp) {
        $sql_certify .= " , mb_hp = '{$mb_hp}' ";
        $sql_certify .= " , mb_certify = '' ";
        $sql_certify .= " , mb_adult = 0 ";
        $sql_certify .= " , mb_birth = '' ";
        $sql_certify .= " , mb_sex = '' ";
    }
}
//===============================================================

if ($w == '') {
    $sql = " insert into {$g5['member_table']}
                set mb_id = '{$mb_id}',
                     mb_password = '".sql_password($mb_password)."',
                     mb_name = '{$mb_name}',
                     mb_nick = '{$mb_nick}',
                     mb_nick_date = '".G5_TIME_YMD."',
                     mb_email = '{$mb_email}',
                     mb_homepage = '{$mb_homepage}',
                     mb_tel = '{$mb_tel}',
                     mb_zip1 = '{$mb_zip1}',
                     mb_zip2 = '{$mb_zip2}',
                     mb_addr1 = '{$mb_addr1}',
                     mb_addr2 = '{$mb_addr2}',
                     mb_addr3 = '{$mb_addr3}',
                     mb_addr_jibeon = '{$mb_addr_jibeon}',
                     mb_signature = '{$mb_signature}',
                     mb_profile = '{$mb_profile}',
                     mb_today_login = '".G5_TIME_YMDHIS."',
                     mb_datetime = '".G5_TIME_YMDHIS."',
                     mb_ip = '{$_SERVER['REMOTE_ADDR']}',
                     mb_level = '{$config['cf_register_level']}',
                     mb_recommend = '{$mb_recommend}',
                     mb_login_ip = '{$_SERVER['REMOTE_ADDR']}',
                     mb_mailling = '{$mb_mailling}',
                     mb_sms = '{$mb_sms}',
                     mb_open = '{$mb_open}',
                     mb_open_date = '".G5_TIME_YMD."',
                     mb_1 = '{$mb_1}',
                     mb_2 = '{$mb_2}',
                     mb_3 = '{$mb_3}',
                     mb_4 = '{$mb_4}',
                     mb_5 = '{$mb_5}',
                     mb_6 = '{$mb_6}',
                     mb_7 = '{$mb_7}',
                     mb_8 = '{$mb_8}',
                     mb_9 = '{$mb_9}',
                     mb_10 = '{$mb_10}'
                     {$sql_certify} ";

    // 如果未开启邮件地址认证服务直接标记验证完成
    if (!$config['cf_use_email_certify'])
        $sql .= " , mb_email_certify = '".G5_TIME_YMDHIS."' ";
    sql_query($sql);

    // 会员注册积分
    insert_point($mb_id, $config['cf_register_point'], '祝贺成为会员', '@member', $mb_id, '注册会员');

    // 发放推荐人积分
    if ($config['cf_use_recommend'] && $mb_recommend)
        insert_point($mb_recommend, $config['cf_recommend_point'], $mb_id.'的推荐人', '@member', $mb_recommend, $mb_id.' 推荐');

    // 发送邮件给会员
    if ($config['cf_email_mb_member']) {
        $subject = '['.$config['cf_title'].'] 祝贺您成为会员！';

        $mb_md5 = md5($mb_id.$mb_email.G5_TIME_YMDHIS);
        $certify_href = G5_BBS_URL.'/email_certify.php?mb_id='.$mb_id.'&amp;mb_md5='.$mb_md5;

        ob_start();
        include_once ('./register_form_update_mail1.php');
        $content = ob_get_contents();
        ob_end_clean();

        mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $mb_email, $subject, $content, 1);

        // 开启邮件认证时直接发送邮件地址认证信息
        if($config['cf_use_email_certify'])
            $old_email = $mb_email;
    }

    // 发送邮件至管理员
    if ($config['cf_email_mb_super_admin']) {
        $subject = '['.$config['cf_title'].'] '.$mb_nick .'注册成为了新会员';

        ob_start();
        include_once ('./register_form_update_mail2.php');
        $content = ob_get_contents();
        ob_end_clean();

        mailer($mb_nick, $mb_email, $config['cf_admin_email'], $subject, $content, 1);
    }

    // 不使用地址验证时进行登录
    if (!$config['cf_use_email_certify'])
        set_session('ss_mb_id', $mb_id);

    set_session('ss_mb_reg', $mb_id);

} else if ($w == 'u') {
    if (!trim($_SESSION['ss_mb_id']))
        alert('请您登录后使用');

    if (trim($_POST['mb_id']) != $mb_id)
        alert("您的登录信息与操作会员账户不符\\n您的操作及IP地址已被记录，请使用正常方式操作！");

    $sql_password = "";
    if ($mb_password)
        $sql_password = " , mb_password = '".sql_password($mb_password)."' ";

    $sql_nick_date = "";
    if ($mb_nick_default != $mb_nick)
        $sql_nick_date =  " , mb_nick_date = '".G5_TIME_YMD."' ";

    $sql_open_date = "";
    if ($mb_open_default != $mb_open)
        $sql_open_date =  " , mb_open_date = '".G5_TIME_YMD."' ";

    // 如果更换邮件地址需要重新进行认证
    $sql_email_certify = '';
    if ($old_email != $mb_email && $config['cf_use_email_certify'])
        $sql_email_certify = " , mb_email_certify = '' ";

    $sql = " update {$g5['member_table']}
                set mb_nick = '{$mb_nick}',
                    mb_mailling = '{$mb_mailling}',
                    mb_sms = '{$mb_sms}',
                    mb_open = '{$mb_open}',
                    mb_email = '{$mb_email}',
                    mb_homepage = '{$mb_homepage}',
                    mb_tel = '{$mb_tel}',
                    mb_zip1 = '{$mb_zip1}',
                    mb_zip2 = '{$mb_zip2}',
                    mb_addr1 = '{$mb_addr1}',
                    mb_addr2 = '{$mb_addr2}',
                    mb_addr3 = '{$mb_addr3}',
                    mb_addr_jibeon = '{$mb_addr_jibeon}',
                    mb_signature = '{$mb_signature}',
                    mb_profile = '{$mb_profile}',
                    mb_1 = '{$mb_1}',
                    mb_2 = '{$mb_2}',
                    mb_3 = '{$mb_3}',
                    mb_4 = '{$mb_4}',
                    mb_5 = '{$mb_5}',
                    mb_6 = '{$mb_6}',
                    mb_7 = '{$mb_7}',
                    mb_8 = '{$mb_8}',
                    mb_9 = '{$mb_9}',
                    mb_10 = '{$mb_10}'
                    {$sql_password}
                    {$sql_nick_date}
                    {$sql_open_date}
                    {$sql_email_certify}
                    {$sql_certify}
              where mb_id = '$mb_id' ";
    sql_query($sql);
}


// 会员头像
$mb_dir = G5_DATA_PATH.'/member/'.substr($mb_id,0,2);

// 删除图标
if (isset($_POST['del_mb_icon'])) {
    @unlink($mb_dir.'/'.$mb_id.'.gif');
}

$msg = "";

// 上传会员头像
$mb_icon = '';
if (isset($_FILES['mb_icon']) && is_uploaded_file($_FILES['mb_icon']['tmp_name'])) {
    if (preg_match("/(\.gif)$/i", $_FILES['mb_icon']['name'])) {
        // 符合设定大小时允许上传
        if ($_FILES['mb_icon']['size'] <= $config['cf_member_icon_size']) {
            @mkdir($mb_dir, G5_DIR_PERMISSION);
            @chmod($mb_dir, G5_DIR_PERMISSION);
            $dest_path = $mb_dir.'/'.$mb_id.'.gif';
            move_uploaded_file($_FILES['mb_icon']['tmp_name'], $dest_path);
            chmod($dest_path, G5_FILE_PERMISSION);
            if (file_exists($dest_path)) {
                //=================================================================\
                // 090714
                // 防止gif文件中加入恶意代码
                // 不显示错误信息
                //-----------------------------------------------------------------
                $size = getimagesize($dest_path);
                if ($size[2] != 1) // 如果不是gif文件则直接删除
                    @unlink($dest_path);
                else
                // 删除超出指定设置头像
                if ($size[0] > $config['cf_member_icon_width'] || $size[1] > $config['cf_member_icon_height'])
                    @unlink($dest_path);
                //=================================================================\
            }
        } else {
            $msg .= '会员头像文件请使用 '.number_format($config['cf_member_icon_size']).'字节以内的文件';
        }

    } else {
        $msg .= $_FILES['mb_icon']['name'].'不是gif格式';
    }
}


// 发送认证邮件
if ($config['cf_use_email_certify'] && $old_email != $mb_email) {
    $subject = '['.$config['cf_title'].'] 邮件地址认证邮件';

    $mb_datetime = $member['mb_datetime'] ? $member['mb_datetime'] : G5_TIME_YMDHIS;
    $mb_md5 = md5($mb_id.$mb_email.$mb_datetime);
    $certify_href = G5_BBS_URL.'/email_certify.php?mb_id='.$mb_id.'&amp;mb_md5='.$mb_md5;

    ob_start();
    include_once ('./register_form_update_mail3.php');
    $content = ob_get_contents();
    ob_end_clean();

    mailer($config['cf_title'], $config['cf_admin_email'], $mb_email, $subject, $content, 1);
}


// 执行用户代码
@include_once ($member_skin_path.'/register_form_update.tail.skin.php');

unset($_SESSION['ss_cert_type']);
unset($_SESSION['ss_cert_no']);
unset($_SESSION['ss_cert_hash']);
unset($_SESSION['ss_cert_birth']);
unset($_SESSION['ss_cert_adult']);

if ($msg)
    echo '<script>alert(\''.$msg.'\');</script>';

if ($w == '') {
    goto_url(G5_HTTP_BBS_URL.'/register_result.php');
} else if ($w == 'u') {
    $row  = sql_fetch(" select mb_password from {$g5['member_table']} where mb_id = '{$member['mb_id']}' ");
    $tmp_password = $row['mb_password'];

    if ($old_email != $mb_email && $config['cf_use_email_certify']) {
        set_session('ss_mb_id', '');
        alert('您的会员账户信息已发生更改\n\n由于邮件地址变更，您需要重新进行邮件地址认证', G5_URL);
    } else {
        echo '
        <!doctype html>
        <html lang="zh-cn">
        <head>
        <meta charset="utf-8">
        <title>会员信息修改</title>
        <body>
        <form name="fregisterupdate" method="post" action="'.G5_HTTPS_BBS_URL.'/register_form.php">
        <input type="hidden" name="w" value="u">
        <input type="hidden" name="mb_id" value="'.$mb_id.'">
        <input type="hidden" name="mb_password" value="'.$tmp_password.'">
        <input type="hidden" name="is_update" value="1">
        </form>
        <script>
        alert("您的会员账户信息已发生更改");
        document.fregisterupdate.submit();
        </script>
        </body>
        </html>';
    }
}
?>
