<?php
include_once('./_common.php');

if (!$is_member)
    alert('请登录后使用，如不是会员请您注册');

$me_id = (int)$_REQUEST['me_id'];

if ($kind == 'recv')
{
    $t = '收件';
    $unkind = 'send';

    $sql = " update {$g5['memo_table']}
                set me_read_datetime = '".G5_TIME_YMDHIS."'
                where me_id = '$me_id'
                and me_recv_mb_id = '{$member['mb_id']}'
                and me_read_datetime = '0000-00-00 00:00:00' ";
    sql_query($sql);
}
else if ($kind == 'send')
{
    $t = '发件';
    $unkind = 'recv';
}
else
{
    alert($kind.' 请传递正确参数');
}

$g5['title'] = $t.'查看短信';
include_once(G5_PATH.'/head.sub.php');

$sql = " select * from {$g5['memo_table']}
            where me_id = '$me_id'
            and me_{$kind}_mb_id = '{$member['mb_id']}' ";
$memo = sql_fetch($sql);

// 上一条短信
$sql = " select * from {$g5['memo_table']}
            where me_id > '{$me_id}'
            and me_{$kind}_mb_id = '{$member['mb_id']}'
            order by me_id asc
            limit 1 ";
$prev = sql_fetch($sql);
if ($prev['me_id'])
    $prev_link = './memo_view.php?kind='.$kind.'&amp;me_id='.$prev['me_id'];
else
    //$prev_link = 'javascript:alert(\'短信首页\');';
    $prev_link = '';


// 下一条短信
$sql = " select * from {$g5[memo_table]}
            where me_id < '{$me_id}'
            and me_{$kind}_mb_id = '{$member[mb_id]}'
            order by me_id desc
            limit 1 ";
$next = sql_fetch($sql);
if ($next[me_id])
    $next_link = './memo_view.php?kind='.$kind.'&amp;me_id='.$next[me_id];
else
    //$next_link = 'javascript:alert(\'最后一页短信\');';
    $next_link = '';

$mb = get_member($memo['me_'.$unkind.'_mb_id']);

include_once($member_skin_path.'/memo_view.skin.php');

include_once(G5_PATH.'/tail.sub.php');
?>
