<?php
// 创建新文件时必须包含此页
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

$begin_time = get_microtime();

// 如有使用者定义的head.sub.php文件就进行include
if(defined('G5_HEAD_SUB_FILE') && is_file(G5_PATH.'/'.G5_HEAD_SUB_FILE)) {
    include_once(G5_PATH.'/'.G5_HEAD_SUB_FILE);
    return;
}

if (!isset($g5['title'])) {
    $g5['title'] = $config['cf_title'];
    $g5_head_title = $g5['title'];
}
else {
    $g5_head_title = $g5['title']; // 状态栏显示的标题
    $g5_head_title .= " | ".$config['cf_title'];
}

// 在线人数
// 标题中包含 ' 时发生错误
$g5['lo_location'] = addslashes($g5['title']);
if (!$g5['lo_location'])
    $g5['lo_location'] = addslashes($_SERVER['REQUEST_URI']);
$g5['lo_url'] = addslashes($_SERVER['REQUEST_URI']);
if (strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

/*
//使用过期页面时
header("Cache-Control: no-cache"); // HTTP/1.1
header("Expires: 0"); // rfc2616 - Section 14.21
header("Pragma: no-cache"); // HTTP/1.0
*/
?>
<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<?php
if (G5_IS_MOBILE) {
    echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">'.PHP_EOL;
    echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
    echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
} else {
    echo '<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
    echo '<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">'.PHP_EOL;
}

if($config['cf_add_meta'])
    echo $config['cf_add_meta'].PHP_EOL;
?>
<title><?php echo $g5_head_title; ?></title>
<?php
if (defined('G5_IS_ADMIN')) {
    echo '<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">'.PHP_EOL;
} else {
    echo '<link rel="stylesheet" href="'.G5_CSS_URL.'/'.(G5_IS_MOBILE?'mobile':'default').'.css">'.PHP_EOL;
}
?>
<!--[if lte IE 8]>
<script src="<?php echo G5_JS_URL ?>/html5.js"></script>
<![endif]-->
<script>
// 全局JavaScript参数
var g5_url       = "<?php echo G5_URL ?>";
var g5_bbs_url   = "<?php echo G5_BBS_URL ?>";
var g5_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
var g5_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
var g5_is_mobile = "<?php echo G5_IS_MOBILE ?>";
var g5_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
var g5_sca       = "<?php echo isset($sca)?$sca:''; ?>";
var g5_editor    = "<?php echo ($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
var g5_cookie_domain = "<?php echo G5_COOKIE_DOMAIN ?>";
<?php
if ($is_admin) {
    echo 'var g5_admin_url = "'.G5_ADMIN_URL.'";'.PHP_EOL;
}
?>
</script>
<script src="<?php echo G5_JS_URL ?>/jquery-1.8.3.min.js"></script>
<!--<script src="<?php echo G5_JS_URL ?>/jquery-1.11.0.min.js"></script>-->
<script src="<?php echo G5_JS_URL ?>/jquery.menu.js"></script>
<script src="<?php echo G5_JS_URL ?>/common.js"></script>
<script src="<?php echo G5_JS_URL ?>/wrest.js"></script>
<?php
if(G5_IS_MOBILE) {
    echo '<script src="'.G5_JS_URL.'/modernizr.custom.70111.js"></script>'.PHP_EOL; // overflow scroll 检测
}
if(!defined('G5_IS_ADMIN'))
    echo $config['cf_add_script'];
?>
</head>
<body>
<?php
if ($is_member) { // 如果是会员则显示正在登录中的msg
    $sr_admin_msg = '';
    if ($is_admin == 'super') $sr_admin_msg = "网站管理员 ";
    else if ($is_admin == 'group') $sr_admin_msg = "群组管理员 ";
    else if ($is_admin == 'board') $sr_admin_msg = "版块管理员 ";

    echo '<div id="hd_login_msg">'.$sr_admin_msg.$member['mb_nick'].'账号正在登录中 ';
    echo '<a href="'.G5_BBS_URL.'/logout.php">注销</a></div>';
}
?>