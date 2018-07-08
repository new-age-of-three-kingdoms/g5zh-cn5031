<?php
define('G5_CAPTCHA', true);
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

// 090710
if (substr_count($wr_content, "&#") > 50) {
    alert('请勿在内容中使用代码或特殊符号');
    exit;
}

@include_once($board_skin_path.'/write_comment_update.head.skin.php');

$w = $_POST["w"];
$wr_name  = trim($_POST['wr_name']);
$wr_email = '';
if (!empty($_POST['wr_email']))
    $wr_email = get_email_address(trim($_POST['wr_email']));

// 游客可能会出现遗漏名称的情况
if ($is_guest) {
    if ($wr_name == '')
        alert('请输入名称');
    if(!chk_captcha())
        alert('您输入的验证码错误，请重新输入');
}

if ($w == "c" || $w == "cu") {
    if ($member['mb_level'] < $board['bo_comment_level'])
        alert('评论您没有发表主题权限');
}
else
    alert('w值传递错误！');

// session时间检查
// 4.00.15 - 修复编辑评论时显示连续发帖警告的错误
if ($w == 'c' && $_SESSION['ss_datetime'] >= (G5_SERVER_TIME - $config['cf_delay_sec']) && !$is_admin)
    alert('您发帖速度太快了，请休息一下~');

set_session('ss_datetime', G5_SERVER_TIME);

$wr = get_write($write_table, $wr_id);
if (empty($wr['wr_id']))
    alert("未找到您访问的内容\\n您访问的内容可能已经被删除或移动到其他栏目");


// IE浏览器安全设置中禁用action时提示错误
// 禁用此功能时会导致所有script都不能运行
//if (!trim($_POST["wr_content"])) die ("请输入内容");

if ($is_member)
{
    $mb_id = $member['mb_id'];
    // 4.00.13 - 修复开启实名时在评论中显示昵称的错误
    $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
    $wr_password = $member['mb_password'];
    $wr_email = addslashes($member['mb_email']);
    $wr_homepage = addslashes(clean_xss_tags($member['mb_homepage']));
}
else
{
    $mb_id = '';
    $wr_password = sql_password($wr_password);
}

if ($w == 'c') // 编辑评论
{
    /*
    if ($member[mb_point] + $board[bo_comment_point] < 0 && !$is_admin)
        alert('当前积分('.number_format($member[mb_point]).')不足支付发布评论所需的积分('.number_format($board[bo_comment_point]).')\\n\\n请获取更多积分后重试');
    */
    // 修复开启积分模式时积分负数时不能发表评论的bug
    $tmp_point = ($member['mb_point'] > 0) ? $member['mb_point'] : 0;
    if ($tmp_point + $board['bo_comment_point'] < 0 && !$is_admin)
        alert('当前积分('.number_format($member['mb_point']).')不足支付发表评论所需的积分('.number_format($board['bo_comment_point']).')\\n\\n请获取更多积分后重试');

    // 评论回复
    if ($comment_id)
    {
        $sql = " select wr_id, wr_comment, wr_comment_reply from $write_table
                    where wr_id = '$comment_id' ";
        $reply_array = sql_fetch($sql);
        if (!$reply_array['wr_id'])
            alert('没有找到您要回复的评论内容\\n\\n此内容可能在您编辑回复时已被删除');

        $tmp_comment = $reply_array['wr_comment'];

        if (strlen($reply_array['wr_comment_reply']) == 5)
            alert('回帖数量已达到回帖限制\\n\\n仅允许回复5层');

        $reply_len = strlen($reply_array['wr_comment_reply']) + 1;
        if ($board['bo_reply_order']) {
            $begin_reply_char = 'A';
            $end_reply_char = 'Z';
            $reply_number = +1;
            $sql = " select MAX(SUBSTRING(wr_comment_reply, $reply_len, 1)) as reply
                        from $write_table
                        where wr_parent = '$wr_id'
                        and wr_comment = '$tmp_comment'
                        and SUBSTRING(wr_comment_reply, $reply_len, 1) <> '' ";
        }
        else
        {
            $begin_reply_char = 'Z';
            $end_reply_char = 'A';
            $reply_number = -1;
            $sql = " select MIN(SUBSTRING(wr_comment_reply, $reply_len, 1)) as reply
                        from $write_table
                        where wr_parent = '$wr_id'
                        and wr_comment = '$tmp_comment'
                        and SUBSTRING(wr_comment_reply, $reply_len, 1) <> '' ";
        }
        if ($reply_array['wr_comment_reply'])
            $sql .= " and wr_comment_reply like '{$reply_array['wr_comment_reply']}%' ";
        $row = sql_fetch($sql);

        if (!$row['reply'])
            $reply_char = $begin_reply_char;
        else if ($row['reply'] == $end_reply_char) // A~Z 26个字母
            alert('回帖数量已达到回帖限制\\n\\n仅允许回复26层');
        else
            $reply_char = chr(ord($row['reply']) + $reply_number);

        $tmp_comment_reply = $reply_array['wr_comment_reply'] . $reply_char;
    }
    else
    {
        $sql = " select max(wr_comment) as max_comment from $write_table
                    where wr_parent = '$wr_id' and wr_is_comment = 1 ";
        $row = sql_fetch($sql);
        //$row[max_comment] -= 1;
        $row['max_comment'] += 1;
        $tmp_comment = $row['max_comment'];
        $tmp_comment_reply = '';
    }

    $wr_subject = get_text(stripslashes($wr['wr_subject']));

    $sql = " insert into $write_table
                set ca_name = '{$wr['ca_name']}',
                     wr_option = '$wr_secret',
                     wr_num = '{$wr['wr_num']}',
                     wr_reply = '',
                     wr_parent = '$wr_id',
                     wr_is_comment = 1,
                     wr_comment = '$tmp_comment',
                     wr_comment_reply = '$tmp_comment_reply',
                     wr_subject = '',
                     wr_content = '$wr_content',
                     mb_id = '$mb_id',
                     wr_password = '$wr_password',
                     wr_name = '$wr_name',
                     wr_email = '$wr_email',
                     wr_homepage = '$wr_homepage',
                     wr_datetime = '".G5_TIME_YMDHIS."',
                     wr_last = '',
                     wr_ip = '{$_SERVER['REMOTE_ADDR']}',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10' ";
    sql_query($sql);

    $comment_id = mysql_insert_id();

    // 原文更新评论数量及最后编辑时间
    sql_query(" update $write_table set wr_comment = wr_comment + 1, wr_last = '".G5_TIME_YMDHIS."' where wr_id = '$wr_id' ");

    // insert新内容
    sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '$bo_table', '$comment_id', '$wr_id', '".G5_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

    // 评论加1
    sql_query(" update {$g5['board_table']} set bo_count_comment = bo_count_comment + 1 where bo_table = '$bo_table' ");

    // 增加积分
    insert_point($member['mb_id'], $board['bo_comment_point'], "{$board['bo_subject']} {$wr_id}-{$comment_id} 发表评论", $bo_table, $comment_id, '评论');

    // 邮件通知功能
    if ($config['cf_email_use'] && $board['bo_use_email'])
    {
        // 获取管理员信息
        $super_admin = get_admin('super');
        $group_admin = get_admin('group');
        $board_admin = get_admin('board');

        $wr_content = nl2br(get_text(stripslashes("原文\n{$wr['wr_subject']}\n\n\n评论\n$wr_content")));

        $warr = array( ''=>'输入', 'u'=>'修改', 'r'=>'回复', 'c'=>'评论 ', 'cu'=>'修改评论' );
        $str = $warr[$w];

        $subject = '['.$config['cf_title'].'] '.$board['bo_subject'].' 论坛版块新发表'.$str.'内容';
        // 4.00.15 - 修复邮件中直接查看评论链接
        $link_url = G5_BBS_URL."/board.php?bo_table=".$bo_table."&amp;wr_id=".$wr_id."&amp;".$qstr."#c_".$comment_id;

        include_once(G5_LIB_PATH.'/mailer.lib.php');

        ob_start();
        include_once ('./write_update_mail.php');
        $content = ob_get_contents();
        ob_end_clean();

        $array_email = array();
        // 邮件通知论坛版块管理员
        if ($config['cf_email_wr_board_admin']) $array_email[] = $board_admin['mb_email'];
        // 邮件通知群组管理员
        if ($config['cf_email_wr_group_admin']) $array_email[] = $group_admin['mb_email'];
        // 邮件通知管理员
        if ($config['cf_email_wr_super_admin']) $array_email[] = $super_admin['mb_email'];

        // 邮件通知原文作者
        if ($config['cf_email_wr_write']) $array_email[] = $wr['wr_email'];

        // 邮件通知所有参与评论的会员(除本人)
        if ($config['cf_email_wr_comment_all']) {
            $sql = " select distinct wr_email from {$write_table}
                        where wr_email not in ( '{$wr['wr_email']}', '{$member['mb_email']}', '' )
                        and wr_parent = '$wr_id' ";
            $result = sql_query($sql);
            while ($row=sql_fetch_array($result))
                $array_email[] = $row['wr_email'];
        }

        // 去掉重复邮件地址
        $unique_email = array_unique($array_email);
        $unique_email = array_values($unique_email);
        for ($i=0; $i<count($unique_email); $i++) {
            mailer($wr_name, $wr_email, $unique_email[$i], $subject, $content, 1);
        }
    }

    // SNS分享
    include_once("./write_comment_update.sns.php");
    if($wr_facebook_user || $wr_twitter_user) {
        $sql = " update $write_table
                    set wr_facebook_user = '$wr_facebook_user',
                        wr_twitter_user  = '$wr_twitter_user'
                    where wr_id = '$comment_id' ";
        sql_query($sql);
    }
}
else if ($w == 'cu') // 修改评论
{
    $sql = " select mb_id, wr_password, wr_comment, wr_comment_reply from $write_table
                where wr_id = '$comment_id' ";
    $comment = $reply_array = sql_fetch($sql);
    $tmp_comment = $reply_array['wr_comment'];

    $len = strlen($reply_array['wr_comment_reply']);
    if ($len < 0) $len = 0;
    $comment_reply = substr($reply_array['wr_comment_reply'], 0, $len);
    //print_r2($GLOBALS); exit;

    if ($is_admin == 'super') // 管理员通过
        ;
    else if ($is_admin == 'group') { // 群组管理员
        $mb = get_member($comment['mb_id']);
        if ($member['mb_id'] == $group['gr_admin']) { // 检查是否拥有群组管理权限
            if ($member['mb_level'] >= $mb['mb_level']) // 检查会员等级权限，等级相同或大于时通过
                ;
            else
                alert('评论作者等级高于您的等级，您没有编辑权限');
        } else
            alert('由于您没有当前版块管理权限所以不能进行编辑');
    } else if ($is_admin == 'board') { // 如果是论坛版块管理员
        $mb = get_member($comment['mb_id']);
        if ($member['mb_id'] == $board['bo_admin']) { // 检查是否拥有版块管理权限
            if ($member['mb_level'] >= $mb['mb_level']) // 检查会员等级权限，等级相同或大于时通过
                ;
            else
                alert('评论作者等级高于您的等级，您没有编辑权限');
        } else
            alert('由于您没有当前版块管理权限所以不能进行编辑');
    } else if ($member['mb_id']) {
        if ($member['mb_id'] != $comment['mb_id'])
            alert('您没有编辑权限');
    } else {
        if($comment['wr_password'] != $wr_password)
            alert('您没有修改评论权限');
    }

    $sql = " select count(*) as cnt from $write_table
                where wr_comment_reply like '$comment_reply%'
                and wr_id <> '$comment_id'
                and wr_parent = '$wr_id'
                and wr_comment = '$tmp_comment'
                and wr_is_comment = 1 ";
    $row = sql_fetch($sql);
    if ($row['cnt'] && !$is_admin)
        alert('此内容已有关联评论，不能进行编辑修改。');

    $sql_ip = "";
    if (!$is_admin)
        $sql_ip = " , wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";

    $sql_secret = "";
    if ($wr_secret)
        $sql_secret = " , wr_option = '$wr_secret' ";

    $sql = " update $write_table
                set wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10',
                     wr_option = '$wr_option'
                     $sql_ip
                     $sql_secret
              where wr_id = '$comment_id' ";
    sql_query($sql);
}

// 执行用户代码
@include_once($board_skin_path.'/write_comment_update.skin.php');
@include_once($board_skin_path.'/write_comment_update.tail.skin.php');

delete_cache_latest($bo_table);

goto_url('./board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr['wr_parent'].'&amp;'.$qstr.'&amp;#c_'.$comment_id);
?>
