<?php
include_once('./_common.php');
include_once(G5_EDITOR_LIB);
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

if (!$board['bo_table']) {
    alert('不存在的论坛版块', G5_URL);
}

if (!$bo_table) {
    alert("bo_table参数传递错误\\nwrite.php?bo_table=code 请使用此方式进行传递", G5_URL);
}

check_device($board['bo_device']);

$notice_array = explode(',', trim($board['bo_notice']));

if (!($w == '' || $w == 'u' || $w == 'r')) {
    alert('w值传递错误！');
}

if ($w == 'u' || $w == 'r') {
    if ($write['wr_id']) {
        // 创建可变参数$wr_1 .. $wr_10
        for ($i=1; $i<=10; $i++) {
            $vvar = "wr_".$i;
            $$vvar = $write['wr_'.$i];
        }
    } else {
        alert("未找到您访问的内容\\n此内容可能已经被移动或删除", G5_URL);
    }
}

if ($w == '') {
    if ($wr_id) {
        alert('编辑主题时不使用\$wr_id', G5_BBS_URL.'/board.php?bo_table='.$bo_table);
    }

    if ($member['mb_level'] < $board['bo_write_level']) {
        if ($member['mb_id']) {
            alert('您没有发表主题权限');
        } else {
            alert("您没有发表主题权限\\n请您登录后使用", './login.php?'.$qstr.'&amp;url='.urlencode($_SERVER['PHP_SELF'].'?bo_table='.$bo_table));
        }
    }

    // 负数也是 true
    if ($is_member) {
        $tmp_point = ($member['mb_point'] > 0) ? $member['mb_point'] : 0;
        if ($tmp_point + $board['bo_write_point'] < 0 && !$is_admin) {
            alert('当前积分('.number_format($member['mb_point']).')不足支付发布主题所需的积分('.number_format($board['bo_write_point']).')\\n\\n请您获取更多积分后重试！');
        }
    }

    $title_msg = '发表主题';
} else if ($w == 'u') {
    // 金善勇 1.00：发布主题与编辑主题权限需要另行处理
    //if ($member['mb_level'] < $board['bo_write_level']) {
    if($member['mb_id'] && $write['mb_id'] == $member['mb_id']) {
        ;
    } else if ($member['mb_level'] < $board['bo_write_level']) {
        if ($member['mb_id']) {
            alert('您没有编辑主题权限');
        } else {
            alert('您没有编辑主题权限\\n\\n请您登录后使用', './login.php?'.$qstr.'&amp;url='.urlencode($_SERVER['PHP_SELF'].'?bo_table='.$bo_table));
        }
    }

    $len = strlen($write['wr_reply']);
    if ($len < 0) $len = 0;
    $reply = substr($write['wr_reply'], 0, $len);

    // 仅获取原文
    $sql = " select count(*) as cnt from {$write_table}
                where wr_reply like '{$reply}%'
                and wr_id <> '{$write['wr_id']}'
                and wr_num = '{$write['wr_num']}'
                and wr_is_comment = 0 ";
    $row = sql_fetch($sql);
    if ($row['cnt'] && !$is_admin)
        alert('此内容已有关联回复内容，不能进行编辑修改。\\n\\n已有回帖的原文禁止修改');

    // 已有回复的主题是否允许修改
    $sql = " select count(*) as cnt from {$write_table}
                where wr_parent = '{$wr_id}'
                and mb_id <> '{$member['mb_id']}'
                and wr_is_comment = 1 ";
    $row = sql_fetch($sql);
    if ($board['bo_count_modify'] && $row['cnt'] >= $board['bo_count_modify'] && !$is_admin)
        alert('此内容已有评论内容，不能进行编辑修改。\\n\\n评论达到'.$board['bo_count_modify'].'个以上的主题内容禁止修改');

    $title_msg = '修改主题';
} else if ($w == 'r') {
    if ($member['mb_level'] < $board['bo_reply_level']) {
        if ($member['mb_id'])
            alert('您没有回复或评论权限');
        else
            alert('您没有回复或评论权限\\n\\n请您登录后使用', './login.php?'.$qstr.'&amp;url='.urlencode($_SERVER['PHP_SELF'].'?bo_table='.$bo_table));
    }

    $tmp_point = isset($member['mb_point']) ? $member['mb_point'] : 0;
    if ($tmp_point + $board['bo_write_point'] < 0 && !$is_admin)
        alert('当前积分('.number_format($member['mb_point']).')不足支付发布评论所需积分('.number_format($board['bo_comment_point']).')\\n\\n请您获取更多积分后重试！');

    //if (preg_match("/[^0-9]{0,1}{$wr_id}[\r]{0,1}/",$board['bo_notice']))
    if (in_array((int)$wr_id, $notice_array))
        alert('公告内容禁止回复或评论');

    //----------
    // 4.06.13 : 修复可以查看加密内容的bug
    // 评论中禁止对原文进行回帖操作
    if ($write['wr_is_comment'])
        alert('您的操作已被记录，请使用正常方式访问！');

    // 检查是否是加密内容
    if (strstr($write['wr_option'], 'secret')) {
        if ($write['mb_id']) {
            // 会员内容时只有管理员及作者本人可以通过
            if (!($write['mb_id'] == $member['mb_id'] || $is_admin))
                alert('加密主题内容只有作者本人及管理员可以进行回复');
        } else {
            // 不能对游客发布内容进行回复
            if (!$is_admin)
                alert('您不能对游客发布的加密内容进行回复');
        }
    }
    //----------

    // 主题排列参考
    $reply_array = &$write;

    // 最大回复数量根据数据表中的wr_reply设置数量进行限制
    if (strlen($reply_array['wr_reply']) == 10)
        alert('回帖数量已达到回帖限制\\n\\n仅允许回复10层');

    $reply_len = strlen($reply_array['wr_reply']) + 1;
    if ($board['bo_reply_order']) {
        $begin_reply_char = 'A';
        $end_reply_char = 'Z';
        $reply_number = +1;
        $sql = " select MAX(SUBSTRING(wr_reply, {$reply_len}, 1)) as reply from {$write_table} where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
    } else {
        $begin_reply_char = 'Z';
        $end_reply_char = 'A';
        $reply_number = -1;
        $sql = " select MIN(SUBSTRING(wr_reply, {$reply_len}, 1)) as reply from {$write_table} where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
    }
    if ($reply_array['wr_reply']) $sql .= " and wr_reply like '{$reply_array['wr_reply']}%' ";
    $row = sql_fetch($sql);

    if (!$row['reply'])
        $reply_char = $begin_reply_char;
    else if ($row['reply'] == $end_reply_char) // A~Z 26个字母
        alert('回帖数量已达到回帖限制\\n\\n仅允许回复26层');
    else
        $reply_char = chr(ord($row['reply']) + $reply_number);

    $reply = $reply_array['wr_reply'] . $reply_char;

    $title_msg = '回帖';

    $write['wr_subject'] = 'Re: '.$write['wr_subject'];
}

// 群组访问限制
if (!empty($group['gr_use_access'])) {
    if ($is_guest) {
        alert("您没有访问权限\\n\\n请您登录会员后重试", 'login.php?'.$qstr.'&amp;url='.urlencode($_SERVER['PHP_SELF'].'?bo_table='.$bo_table));
    }

    if ($is_admin == 'super' || $group['gr_admin'] == $member['mb_id'] || $board['bo_admin'] == $member['mb_id']) {
        ; // 通过
    } else {
        // 群组限制
        $sql = " select gr_id from {$g5['group_member_table']} where gr_id = '{$board['gr_id']}' and mb_id = '{$member['mb_id']}' ";
        $row = sql_fetch($sql);
        if (!$row['gr_id'])
            alert('您没有当前群组访问权限\\n\\n请您参考网站使用指南或联系客服人员');
    }
}

// 开启实名认证时
if ($config['cf_cert_use'] && !$is_admin) {
    // 仅允许认证会员
    if ($board['bo_use_cert'] != '' && $is_guest) {
        alert('当前栏目进行实名认证的会员才能发布内容\\n\\n请您登录后使用', 'login.php?'.$qstr.'&amp;url='.urlencode($_SERVER['PHP_SELF'].'?bo_table='.$bo_table));
    }

    if ($board['bo_use_cert'] == 'cert' && !$member['mb_certify']) {
        alert('当前栏目进行实名认证的会员才能发布内容\\n\\n请在会员信息设置中进行实名认证后使用', G5_URL);
    }

    if ($board['bo_use_cert'] == 'adult' && !$member['mb_adult']) {
        alert('当前栏目进行成人认证的会员才能发布内容\\n\\n如果您是成年会员提示此内容，请在会员信息设置中重新进行认证', G5_URL);
    }

    if ($board['bo_use_cert'] == 'hp-cert' && $member['mb_certify'] != 'hp') {
        alert('当前栏目进行手机认证的会员才能浏览浏览\\n\\n请您在会员信息设置中进行手机认证', G5_URL);
    }

    if ($board['bo_use_cert'] == 'hp-adult' && (!$member['mb_adult'] || $member['mb_certify'] != 'hp')) {
        alert('当前栏目进行手机认证的成人会员才能浏览浏览\\n\\n如果您是成年会员提示此内容，请在会员信息设置中重新进行认证', G5_URL);
    }
}

// 字节长度限制
if ($is_admin || $board['bo_use_dhtml_editor'])
{
    $write_min = $write_max = 0;
}
else
{
    $write_min = (int)$board['bo_write_min'];
    $write_max = (int)$board['bo_write_max'];
}

$g5['title'] = $board['bo_subject']." ".$title_msg;

$is_notice = false;
$notice_checked = '';
if ($is_admin && $w != 'r') {
    $is_notice = true;

    if ($w == 'u') {
        // 编辑回复内容时无公告选项
        if ($write['wr_reply']) {
            $is_notice = false;
        } else {
            if (in_array((int)$wr_id, $notice_array)) {
                $notice_checked = 'checked';
            }
        }
    }
}

$is_html = false;
if ($member['mb_level'] >= $board['bo_html_level'])
    $is_html = true;

$is_secret = $board['bo_use_secret'];

$is_mail = false;
if ($config['cf_email_use'] && $board['bo_use_email'])
    $is_mail = true;

$recv_email_checked = '';
if ($w == '' || strstr($write['wr_option'], 'mail'))
    $recv_email_checked = 'checked';

$is_name     = false;
$is_password = false;
$is_email    = false;
$is_homepage = false;
if ($is_guest || ($is_admin && $w == 'u' && $member['mb_id'] != $write['mb_id'])) {
    $is_name = true;
    $is_password = true;
    $is_email = true;
    $is_homepage = true;
}

$is_category = false;
$category_option = '';
if ($board['bo_use_category']) {
    $ca_name = "";
    if (isset($write['ca_name']))
        $ca_name = $write['ca_name'];
    $category_option = get_category_option($bo_table, $ca_name);
    $is_category = true;
}

$is_link = false;
if ($member['mb_level'] >= $board['bo_link_level']) {
    $is_link = true;
}

$is_file = false;
if ($member['mb_level'] >= $board['bo_upload_level']) {
    $is_file = true;
}

$is_file_content = false;
if ($board['bo_use_file_content']) {
    $is_file_content = true;
}

$file_count = (int)$board['bo_upload_count'];

$name     = "";
$email    = "";
$homepage = "";
if ($w == "" || $w == "r") {
    if ($is_member) {
        if (isset($write['wr_name'])) {
            $name = get_text(cut_str(stripslashes($write['wr_name']),20));
        }
        $email = get_email_address($member['mb_email']);
        $homepage = get_text(stripslashes($member['mb_homepage']));
    }
}

$html_checked   = "";
$html_value     = "";
$secret_checked = "";

if ($w == '') {
    $password_required = 'required';
} else if ($w == 'u') {
    $password_required = '';

    if (!$is_admin) {
        if (!($is_member && $member['mb_id'] == $write['mb_id'])) {
            if (sql_password($wr_password) != $write['wr_password']) {
                alert('密码错误');
            }
        }
    }

    $name = get_text(cut_str(stripslashes($write['wr_name']),20));
    $email = get_email_address($write['wr_email']);
    $homepage = get_text(stripslashes($write['wr_homepage']));

    for ($i=1; $i<=G5_LINK_COUNT; $i++) {
        $write['wr_link'.$i] = get_text($write['wr_link'.$i]);
        $link[$i] = $write['wr_link'.$i];
    }

    if (strstr($write['wr_option'], 'html1')) {
        $html_checked = 'checked';
        $html_value = 'html1';
    } else if (strstr($write['wr_option'], 'html2')) {
        $html_checked = 'checked';
        $html_value = 'html2';
    }

    if (strstr($write['wr_option'], 'secret')) {
        $secret_checked = 'checked';
    }

    $file = get_file($bo_table, $wr_id);
} else if ($w == 'r') {
    if (strstr($write['wr_option'], 'secret')) {
        $is_secret = true;
        $secret_checked = 'checked';
    }

    $password_required = "required";

    for ($i=1; $i<=G5_LINK_COUNT; $i++) {
        $write['wr_link'.$i] = get_text($write['wr_link'.$i]);
    }
}

set_session('ss_bo_table', $_REQUEST['bo_table']);
set_session('ss_wr_id', $_REQUEST['wr_id']);

$subject = "";
if (isset($write['wr_subject'])) {
    $subject = str_replace("\"", "&#034;", get_text(cut_str($write['wr_subject'], 255), 0));
}

$content = '';
if ($w == '') {
    $content = $board['bo_insert_content'];
} else if ($w == 'r') {
    if (!strstr($write['wr_option'], 'html')) {
        $content = "\n\n\n &gt; "
                 ."\n &gt; "
                 ."\n &gt; ".str_replace("\n", "\n> ", get_text($write['wr_content'], 0))
                 ."\n &gt; "
                 ."\n &gt; ";

    }
} else {
    $content = get_text($write['wr_content'], 0);
}

$upload_max_filesize = number_format($board['bo_upload_size']) . ' 字节';

$width = $board['bo_table_width'];
if ($width <= 100)
    $width .= '%';
else
    $width .= 'px';

$captcha_html = '';
$captcha_js   = '';
if ($is_guest) {
    $captcha_html = captcha_html();
    $captcha_js   = chk_captcha_js();
}

$is_dhtml_editor = false;
// 触屏版不能使用dhtml编辑器
if ($config['cf_editor'] && !G5_IS_MOBILE && $board['bo_use_dhtml_editor'] && $member['mb_level'] >= $board['bo_html_level']) {
    $is_dhtml_editor = true;
}
$editor_html = editor_html('wr_content', $content, $is_dhtml_editor);
$editor_js = '';
$editor_js .= get_editor_js('wr_content', $is_dhtml_editor);
$editor_js .= chk_editor_js('wr_content', $is_dhtml_editor);

// 草稿箱储存数量
$autosave_count = autosave_count($member['mb_id']);

include_once(G5_PATH.'/head.sub.php');
@include_once ($board_skin_path.'/write.head.skin.php');
include_once('./board_head.php');

$action_url = https_url(G5_BBS_DIR)."/write_update.php";

echo '<!-- skin : '.(G5_IS_MOBILE ? $board['bo_mobile_skin'] : $board['bo_skin']).' -->';
include_once ($board_skin_path.'/write.skin.php');

include_once('./board_tail.php');
@include_once ($board_skin_path.'/write.tail.skin.php');
include_once(G5_PATH.'/tail.sub.php');
?>