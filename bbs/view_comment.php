<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

$captcha_html = "";
if ($is_guest && $board['bo_comment_level'] < 2) {
    $captcha_html = captcha_html('_comment');
}

@include_once($board_skin_path.'/view_comment.head.skin.php');

// 使用新窗口发表评论时由于缺少session所以需要创建新的session
if ($is_admin && !$token) {
    set_session("ss_delete_token", $token = uniqid(time()));
}

$list = array();

$is_comment_write = false;
if ($member['mb_level'] >= $board['bo_comment_level'])
    $is_comment_write = true;

// 显示评论
//$sql = " select * from {$write_table} where wr_parent = '{$wr_id}' and wr_is_comment = 1 order by wr_comment desc, wr_comment_reply ";
$sql = " select * from $write_table where wr_parent = '$wr_id' and wr_is_comment = 1 order by wr_comment, wr_comment_reply ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $list[$i] = $row;

    //$list[$i]['name'] = get_sideview($row['mb_id'], cut_str($row['wr_name'], 20, ''), $row['wr_email'], $row['wr_homepage']);

    $tmp_name = get_text(cut_str($row['wr_name'], $config['cf_cut_name'])); // 仅显示设定长度
    if ($board['bo_use_sideview'])
        $list[$i]['name'] = get_sideview($row['mb_id'], $tmp_name, $row['wr_email'], $row['wr_homepage']);
    else
        $list[$i]['name'] = '<span class="'.($row['mb_id']?'member':'guest').'">'.$tmp_name.'</span>';



    // 分隔连续输入的文字（参考wayboard）
    //$list[$i]['content'] = eregi_replace("[^ \n<>]{130}", "\\0\n", $row['wr_content']);

    $list[$i]['content'] = $list[$i]['content1']= '使用加密功能的主题内容';
    if (!strstr($row['wr_option'], 'secret') ||
        $is_admin ||
        ($write['mb_id']==$member['mb_id'] && $member['mb_id']) ||
        ($row['mb_id']==$member['mb_id'] && $member['mb_id'])) {
        $list[$i]['content1'] = $row['wr_content'];
        $list[$i]['content'] = conv_content($row['wr_content'], 0, 'wr_content');
        $list[$i]['content'] = search_font($stx, $list[$i]['content']);
    } else {
        $ss_name = 'ss_secret_comment_'.$bo_table.'_'.$list[$i]['wr_id'];

        if(!get_session($ss_name))
            $list[$i]['content'] = '<a href="./password.php?w=sc&amp;bo_table='.$bo_table.'&amp;wr_id='.$list[$i]['wr_id'].$qstr.'" class="s_cmt">评论内容 确定</a>';
        else {
            $list[$i]['content'] = conv_content($row['wr_content'], 0, 'wr_content');
            $list[$i]['content'] = search_font($stx, $list[$i]['content']);
        }
    }

    $list[$i]['datetime'] = substr($row['wr_datetime'],2,14);

    // 除管理员外屏蔽部分ip地址后显示
    $list[$i]['ip'] = $row['wr_ip'];
    if (!$is_admin)
        $list[$i]['ip'] = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", G5_IP_DISPLAY, $row['wr_ip']);

    $list[$i]['is_reply'] = false;
    $list[$i]['is_edit'] = false;
    $list[$i]['is_del']  = false;
    if ($is_comment_write || $is_admin)
    {
        if ($member['mb_id'])
        {
            if ($row['mb_id'] == $member['mb_id'] || $is_admin)
            {
                $list[$i]['del_link']  = './delete_comment.php?bo_table='.$bo_table.'&amp;comment_id='.$row['wr_id'].'&amp;token='.$token.'&amp;page='.$page.$qstr;
                $list[$i]['is_edit']   = true;
                $list[$i]['is_del']    = true;
            }
        }
        else
        {
            if (!$row['mb_id']) {
                $list[$i]['del_link'] = './password.php?w=x&amp;bo_table='.$bo_table.'&amp;comment_id='.$row['wr_id'].'&amp;page='.$page.$qstr;
                $list[$i]['is_del']   = true;
            }
        }

        if (strlen($row['wr_comment_reply']) < 5)
            $list[$i]['is_reply'] = true;
    }

    // 05.05.22
    // 已有回复的评论内容禁止修改或删除
    if ($i > 0 && !$is_admin)
    {
        if ($row['wr_comment_reply'])
        {
            $tmp_comment_reply = substr($row['wr_comment_reply'], 0, strlen($row['wr_comment_reply']) - 1);
            if ($tmp_comment_reply == $list[$i-1]['wr_comment_reply'])
            {
                $list[$i-1]['is_edit'] = false;
                $list[$i-1]['is_del'] = false;
            }
        }
    }
}

//  评论限制设置
if ($is_admin)
{
    $comment_min = $comment_max = 0;
}
else
{
    $comment_min = (int)$board['bo_comment_min'];
    $comment_max = (int)$board['bo_comment_max'];
}

include_once($board_skin_path.'/view_comment.skin.php');

if (!$member['mb_id']) // 针对游客
    echo '<script src="'.G5_JS_URL.'/md5.js"></script>'."\n";

@include_once($board_skin_path.'/view_comment.tail.skin.php');
?>