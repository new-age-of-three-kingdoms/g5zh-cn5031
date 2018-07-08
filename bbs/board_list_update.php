<?php
include_once('./_common.php');

$count = count($_POST['chk_wr_id']);

if(!$count) {
    alert('请选择需要'.$_POST['btn_submit'].'项目');
}

if($_POST['btn_submit'] == '删除所选') {
    include './delete_all.php';
} else if($_POST['btn_submit'] == '复制所选') {
    $sw = 'copy';
    include './move.php';
} else if($_POST['btn_submit'] == '移动所选') {
    $sw = 'move';
    include './move.php';
} else {
    alert('您的操作已被记录，请使用正常方式访问！');
}
?>