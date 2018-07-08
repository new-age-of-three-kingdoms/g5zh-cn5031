<?php
include_once('./_common.php');

$po = sql_fetch(" select * from {$g5['poll_table']} where po_id = '{$_POST['po_id']}' ");
if (!$po['po_id'])
    alert('po_id 参数传递错误');

if ($member['mb_level'] < $po['po_level'])
    alert_close('会员权限等级'.$po['po_level'].'级以上会员才能参与投票');

$gb_poll = preg_replace('/[^0-9]/', '', $gb_poll);
if(!$gb_poll)
    alert_close('请选择项目');

$search_mb_id = false;
$search_ip = false;

if($is_member) {
    // 在已经投票的会员账户中进行查找
    $ids = explode(',', trim($po['mb_ids']));
    for ($i=0; $i<count($ids); $i++) {
        if ($member['mb_id'] == trim($ids[$i])) {
            $search_mb_id = true;
            break;
        }
    }
} else {
    // 在已经投票的ip地址中进行查证
    $ips = explode(',', trim($po['po_ips']));
    for ($i=0; $i<count($ips); $i++) {
        if ($_SERVER['REMOTE_ADDR'] == trim($ips[$i])) {
            $search_ip = true;
            break;
        }
    }
}

$result_url = G5_BBS_URL."/poll_result.php?po_id=$po_id&skin_dir={$_POST['skin_dir']}";

// 如果没有重复内容在投票计数器中增加1后记录ip地址、会员账户等信息
if (!($search_ip || $search_mb_id)) {
    $po_ips = $po['po_ips'] . $_SERVER['REMOTE_ADDR'].",";
    $mb_ids = $po['mb_ids'];
    if ($is_member) { // 如果是会员仅记录会员id
        $mb_ids .= $member['mb_id'].',';
        $sql = " update {$g5['poll_table']} set po_cnt{$gb_poll} = po_cnt{$gb_poll} + 1, mb_ids = '$mb_ids' where po_id = '$po_id' ";
    } else {
        $sql = " update {$g5['poll_table']} set po_cnt{$gb_poll} = po_cnt{$gb_poll} + 1, po_ips = '$po_ips' where po_id = '$po_id' ";
    }

    sql_query($sql);
} else {
    alert('您已经参加过《'.$po['po_subject'].'》在线调查项目了.', $result_url);
}

if (!$search_mb_id)
    insert_point($member['mb_id'], $po['po_point'], $po['po_id'] . '. ' . cut_str($po['po_subject'],20) . '参与投票', '@poll', $po['po_id'], '投票');

//goto_url($g5['bbs_url'].'/poll_result.php?po_id='.$po_id.'&amp;skin_dir='.$skin_dir);
goto_url($result_url);
?>
