<?php
include_once('./_common.php');

// 李景浩提供的代码
session_unset(); // unset所有session参数 
session_destroy(); // 解除session 

// 解除自动登录 --------------------------------
set_cookie('ck_mb_id', '', 0);
set_cookie('ck_auto', '', 0);
// 解除自动登录 end --------------------------------

if ($url) {
    $p = parse_url($url);
    if ($p['scheme'] || $p['host']) {
        alert('url中无法指定域名');
    }

    $link = $url;
} else if ($bo_table) {
    $link = G5_BBS_URL.'/board.php?bo_table='.$bo_table;
} else {
    $link = G5_URL;
}

goto_url($link);
?>
