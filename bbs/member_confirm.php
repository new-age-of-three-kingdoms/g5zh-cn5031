<?php
include_once('./_common.php');

if ($is_guest)
    alert('请您登录后使用', G5_BBS_URL.'/login.php');

/*
if ($url)
    $urlencode = urlencode($url);
else
    $urlencode = urlencode($_SERVER[REQUEST_URI]);
*/

$g5['title'] = '登录密码确认';
include_once('./_head.sub.php');

$url = $_GET['url'];

$p = parse_url($url);
if ((isset($p['scheme']) && $p['scheme']) || (isset($p['host']) && $p['host'])) {
    //print_r2($p);
    if ($p['host'].(isset($p['port']) ? ':'.$p['port'] : '') != $_SERVER['HTTP_HOST'])
        alert('url中不能指定其他域名');
}

include_once($member_skin_path.'/member_confirm.skin.php');

include_once('./_tail.sub.php');
?>
