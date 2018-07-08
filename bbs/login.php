<?php
include_once('./_common.php');

$g5['title'] = '登录';
include_once('./_head.sub.php');

$url = $_GET['url'];

$p = parse_url($url);
if ((isset($p['scheme']) && $p['scheme']) || (isset($p['host']) && $p['host'])) {
    //print_r2($p);
    if ($p['host'].(isset($p['port']) ? ':'.$p['port'] : '') != $_SERVER['HTTP_HOST'])
        alert('url中不能指定其他域名');
}

// 如果已经登录
if ($is_member) {
    if ($url)
        goto_url($url);
    else
        goto_url(G5_URL);
}

$login_url        = login_url($url);
$login_action_url = G5_HTTPS_BBS_URL."/login_check.php";

// 防止未设置引用登录皮肤导致无法登录情况发生，未选择皮肤时使用默认皮肤
$login_file = $member_skin_path.'/login.skin.php';
if (!file_exists($login_file))
    $member_skin_path   = G5_SKIN_PATH.'/member/basic';

include_once($member_skin_path.'/login.skin.php');

include_once('./_tail.sub.php');
?>
