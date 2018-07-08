<?php
$sub_menu = "200200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

check_token();

$mb_id = $_POST['mb_id'];
$po_point = $_POST['po_point'];
$po_content = $_POST['po_content'];
$expire = preg_replace('/[^0-9]/', '', $_POST['po_expire_term']);

$mb = get_member($mb_id);

if (!$mb['mb_id'])
    alert('不存在的会员ID', './point_list.php?'.$qstr);

if (($po_point < 0) && ($po_point * (-1) > $mb['mb_point']))
    alert('需要删除的积分不能小于当前拥有的积分数量', './point_list.php?'.$qstr);

insert_point($mb_id, $po_point, $po_content, '@passive', $mb_id, $member['mb_id'].'-'.uniqid(''), $expire);

goto_url('./point_list.php?'.$qstr);
?>
