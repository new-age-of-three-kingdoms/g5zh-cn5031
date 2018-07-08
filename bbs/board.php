<?php
include_once('./_common.php');

if (!$board['bo_table']) {
   alert('不存在的论坛版块', G5_URL);
}

check_device($board['bo_device']);

if (isset($write['wr_is_comment']) && $write['wr_is_comment']) {
    goto_url('./board.php?bo_table='.$bo_table.'&amp;wr_id='.$write['wr_parent'].'#c_'.$wr_id);
}

if (!$bo_table) {
    $msg = "bo_table参数传递错误\\n\\nboard.php?bo_table=code 请使用此方式进行传递";
    alert($msg);
}

// 如有wr_id传递值就浏览
if (isset($wr_id) && $wr_id) {
    // 如果没有内容就跳转到目录
    if (!$write['wr_id']) {
        $msg = '未找到您访问的内容\\n\\n此内容可能已经被移动或删除';
        alert($msg, './board.php?bo_table='.$bo_table);
    }

    // 开启群组访问权限
    if (isset($group['gr_use_access']) && $group['gr_use_access']) {
        if ($is_guest) {
            $msg = "游客没有此栏目浏览权限\\n\\n请您登录后使用";
            alert($msg, './login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr));
        }

        // 群组管理员以上级别通过
        if ($is_admin == "super" || $is_admin == "group") {
            ;
        } else {
            // 群组限制
            $sql = " select count(*) as cnt from {$g5['group_member_table']} where gr_id = '{$board['gr_id']}' and mb_id = '{$member['mb_id']}' ";
            $row = sql_fetch($sql);
            if (!$row['cnt']) {
                alert("您没有访问权限\\n\\n请您参考网站使用指南或联系客服人员", G5_URL);
            }
        }
    }

    // 会员权限不足设定限制权限时
    if ($member['mb_level'] < $board['bo_read_level']) {
        if ($is_member)
            alert('您没有浏览权限', G5_URL);
        else
            alert('您没有浏览权限\\n\\n请您登录后使用', './login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr));
    }

    // 开启实名认证时
    if ($config['cf_cert_use'] && !$is_admin) {
        // 仅允许认证会员
        if ($board['bo_use_cert'] != '' && $is_guest) {
            alert('当前论坛仅允许完成实名认证的会员访问浏览\\n\\n请您登录后使用', './login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr));
        }

        if ($board['bo_use_cert'] == 'cert' && !$member['mb_certify']) {
            alert('当前论坛仅允许完成实名认证的会员访问浏览\\n\\n请在会员信息设置中进行实名认证后使用', G5_URL);
        }

        if ($board['bo_use_cert'] == 'adult' && !$member['mb_adult']) {
            alert('当前论坛仅允许完成成人认证的会员访问浏览\\n\\n如果您是成人但依然显示当前信息，请您前往会员信息设置重新进行实名认证', G5_URL);
        }

        if ($board['bo_use_cert'] == 'hp-cert' && $member['mb_certify'] != 'hp') {
            alert('当前栏目进行手机认证的会员才能浏览浏览\\n\\n请您在会员信息设置中进行手机认证', G5_URL);
        }

        if ($board['bo_use_cert'] == 'hp-adult' && (!$member['mb_adult'] || $member['mb_certify'] != 'hp')) {
            alert('当前栏目进行手机认证的成人会员才能浏览浏览\\n\\n如果您是成年会员提示此内容，请在会员信息设置中重新进行认证', G5_URL);
        }
    }

    // 如果是作者本人或管理员时通过
    if (($write['mb_id'] && $write['mb_id'] == $member['mb_id']) || $is_admin) {
        ;
    } else {
        // 如果是加密内容
        if (strstr($write['wr_option'], "secret"))
        {
            // 会员发布加密贴由管理员回复时
            // 修复作者无法查看管理员回复的bug
            $is_owner = false;
            if ($write['wr_reply'] && $member['mb_id'])
            {
                $sql = " select mb_id from {$write_table}
                            where wr_num = '{$write['wr_num']}'
                            and wr_reply = ''
                            and wr_is_comment = 0 ";
                $row = sql_fetch($sql);
                if ($row['mb_id'] == $member['mb_id'])
                    $is_owner = true;
            }

            $ss_name = 'ss_secret_'.$bo_table.'_'.$write['wr_num'];

            if (!$is_owner)
            {
                //$ss_name = "ss_secret_{$bo_table}_{$wr_id}";
                // 读取一次后将记录储存至session，再次访问时不再要求输入密码
                // 当前主题不是已储存的内容且不是管理员时
                //if ("$bo_table|$write['wr_num']" != get_session("ss_secret"))
                if (!get_session($ss_name))
                    goto_url('./password.php?w=s&amp;bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr);
            }

            set_session($ss_name, TRUE);
        }
    }

    // 点击浏览一次在浏览器关闭之间不增加计数器
    $ss_name = 'ss_view_'.$bo_table.'_'.$wr_id;
    if (!get_session($ss_name))
    {
        sql_query(" update {$write_table} set wr_hit = wr_hit + 1 where wr_id = '{$wr_id}' ");

        // 如果是作者本人就通过
        if ($write['mb_id'] && $write['mb_id'] == $member['mb_id']) {
            ;
        } else if ($is_guest && $board['bo_read_level'] == 1 && $write['wr_ip'] == $_SERVER['REMOTE_ADDR']) {
            // 主题浏览权限为1，且游客时根据ip地址判断
            ;
        } else {
            // 如有浏览积分设置
            if ($config['cf_use_point'] && $board['bo_read_point'] && $member['mb_point'] + $board['bo_read_point'] < 0)
                alert('当前积分('.number_format($member['mb_point']).')不足支付浏览所需积分('.number_format($board['bo_read_point']).')\\n\\n请您获取更多积分后重试');

            insert_point($member['mb_id'], $board['bo_read_point'], "{$board['bo_subject']} {$wr_id} 浏览主题", $bo_table, $wr_id, '浏览');
        }

        set_session($ss_name, TRUE);
    }

    $g5['title'] = strip_tags(conv_subject($write['wr_subject'], 255))." > ".$board['bo_subject'];
} else {
    if ($member['mb_level'] < $board['bo_list_level']) {
        if ($member['mb_id'])
            alert('您没有浏览目录权限', G5_URL);
        else
            alert('您没有浏览目录权限\\n\\n请您登录后使用', './login.php?'.$qstr.'&url='.urlencode(G5_BBS_URL.'/board.php?bo_table='.$bo_table.($qstr?'&amp;':'')));
    }

    // 开启实名认证时
    if ($config['cf_cert_use'] && !$is_admin) {
        // 仅允许认证会员
        if ($board['bo_use_cert'] != '' && $is_guest) {
            alert('当前论坛仅允许完成实名认证的会员访问浏览\\n\\n请您登录后使用', './login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr));
        }

        if ($board['bo_use_cert'] == 'cert' && !$member['mb_certify']) {
            alert('当前论坛仅允许完成实名认证的会员访问浏览\\n\\n请在会员信息设置中进行实名认证后使用', G5_URL);
        }

        if ($board['bo_use_cert'] == 'adult' && !$member['mb_adult']) {
            alert('当前论坛仅允许完成成人认证的会员访问浏览\\n\\n如果您是成人但依然显示当前信息，请您前往会员信息设置重新进行实名认证', G5_URL);
        }

        if ($board['bo_use_cert'] == 'hp-cert' && $member['mb_certify'] != 'hp') {
            alert('当前栏目进行手机认证的会员才能浏览浏览\\n\\n请您在会员信息设置中进行手机认证', G5_URL);
        }

        if ($board['bo_use_cert'] == 'hp-adult' && (!$member['mb_adult'] || $member['mb_certify'] != 'hp')) {
            alert('当前栏目进行手机认证的成人会员才能浏览浏览\\n\\n如果您是成年会员提示此内容，请在会员信息设置中重新进行认证', G5_URL);
        }
    }

    if (!isset($page) || (isset($page) && $page == 0)) $page = 1;

    $g5['title'] = $board['bo_subject']." ".$page." 页";
}

include_once(G5_PATH.'/head.sub.php');

$width = $board['bo_table_width'];
if ($width <= 100)
    $width .= '%';
else
    $width .='px';

// ip地址显示功能
$ip = "";
$is_ip_view = $board['bo_use_ip_view'];
if ($is_admin) {
    $is_ip_view = true;
    if (array_key_exists('wr_ip', $write)) {
        $ip = $write['wr_ip'];
    }
} else {
    // 如果不是管理员则隐藏部分ip地址
    if (isset($write['wr_ip'])) {
        $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", G5_IP_DISPLAY, $write['wr_ip']);
    }
}

// 分类功能
$is_category = false;
$category_name = '';
if ($board['bo_use_category']) {
    $is_category = true;
    if (array_key_exists('ca_name', $write)) {
        $category_name = $write['ca_name']; // 分类名
    }
}

// 推荐功能
$is_good = false;
if ($board['bo_use_good'])
    $is_good = true;

// 反对功能
$is_nogood = false;
if ($board['bo_use_nogood'])
    $is_nogood = true;

$admin_href = "";
// 网站管理员或群组管理员时
if ($member['mb_id'] && ($is_admin == 'super' || $group['gr_admin'] == $member['mb_id']))
    $admin_href = G5_ADMIN_URL.'/board_form.php?w=u&amp;bo_table='.$bo_table;

include_once('./board_head.php');

// 如有主题id则include阅读模块
if (isset($wr_id) && $wr_id) {
    include_once('./view.php');
}

// 如设置显示全部目录或wr_id为空时直接显示目录
//if ($board['bo_use_list_view'] || empty($wr_id))
if ($member['mb_level'] >= $board['bo_list_level'] && $board['bo_use_list_view'] || empty($wr_id))
    include_once ('./list.php');

include_once('./board_tail.php');

echo "\n<!-- 使用皮肤 : ".(G5_IS_MOBILE ? $board['bo_mobile_skin'] : $board['bo_skin'])." -->\n";

include_once(G5_PATH.'/tail.sub.php');
?>
