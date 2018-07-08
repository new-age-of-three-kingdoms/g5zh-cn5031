<?php

/********************
    参数定义
********************/

define('G5_VERSION', 'gnuboard5');

// 缺少此参数时无法独立运行页面
define('_GNUBOARD_', true);

if (PHP_VERSION >= '5.1.0') {
    //if (function_exists("date_default_timezone_set")) date_default_timezone_set("Asia/Seoul");
    date_default_timezone_set("Asia/Seoul");
}

/********************
    路径参数
********************/

/*
安全链接域名
用于会员注册、发表内容时使用的https安全协议地址
如果有指定端口请在域名后输入端口如:443
如果没有或不使用安全链接地址则留空，请勿在域名后添加(/)
输入是) https://www.domain.com:443/gnuboard5
*/
define('G5_DOMAIN', '');
define('G5_HTTPS_DOMAIN', '');

/*
使用www.域名.com及域名.com域名时程序将会判断为不同来路访问导致cookie无法公用，如需要开启cookie泛域名支持请输入.域名.com
如果不在此进行设定则会在用户在不同域名访问时发生登录状态被解除的情况发生
*/
define('G5_COOKIE_DOMAIN',  '');

define('G5_DBCONFIG_FILE',  'dbconfig.php');

define('G5_ADMIN_DIR',      'adm');
define('G5_BBS_DIR',        'bbs');
define('G5_CSS_DIR',        'css');
define('G5_DATA_DIR',       'data');
define('G5_EXTEND_DIR',     'extend');
define('G5_IMG_DIR',        'img');
define('G5_JS_DIR',         'js');
define('G5_LIB_DIR',        'lib');
define('G5_PLUGIN_DIR',     'plugin');
define('G5_SKIN_DIR',       'skin');
define('G5_CAPTCHA_DIR',    'kcaptcha');
define('G5_EDITOR_DIR',     'editor');
define('G5_MOBILE_DIR',     'mobile');
define('G5_OKNAME_DIR',     'okname');

define('G5_KCPCERT_DIR',    'kcpcert');
define('G5_LGXPAY_DIR',     'lgxpay');

define('G5_SNS_DIR',        'sns');
define('G5_SYNDI_DIR',      'syndi');
define('G5_PHPMAILER_DIR',  'PHPMailer_v2.0.4');
define('G5_SESSION_DIR',    'session');

// url是浏览器地址路径(包含域名)
if (G5_DOMAIN) {
    define('G5_URL', G5_DOMAIN);
} else {
    if (isset($g5_path['url']))
        define('G5_URL', $g5_path['url']);
    else
        define('G5_URL', '');
}

if (isset($g5_path['path'])) {
    define('G5_PATH', $g5_path['path']);
} else {
    define('G5_PATH', '');
}

define('G5_ADMIN_URL',      G5_URL.'/'.G5_ADMIN_DIR);
define('G5_BBS_URL',        G5_URL.'/'.G5_BBS_DIR);
define('G5_CSS_URL',        G5_URL.'/'.G5_CSS_DIR);
define('G5_DATA_URL',       G5_URL.'/'.G5_DATA_DIR);
define('G5_IMG_URL',        G5_URL.'/'.G5_IMG_DIR);
define('G5_JS_URL',         G5_URL.'/'.G5_JS_DIR);
define('G5_SKIN_URL',       G5_URL.'/'.G5_SKIN_DIR);
define('G5_PLUGIN_URL',     G5_URL.'/'.G5_PLUGIN_DIR);
define('G5_CAPTCHA_URL',    G5_PLUGIN_URL.'/'.G5_CAPTCHA_DIR);
define('G5_EDITOR_URL',     G5_PLUGIN_URL.'/'.G5_EDITOR_DIR);
define('G5_OKNAME_URL',     G5_PLUGIN_URL.'/'.G5_OKNAME_DIR);
define('G5_KCPCERT_URL',    G5_PLUGIN_URL.'/'.G5_KCPCERT_DIR);
define('G5_LGXPAY_URL',     G5_PLUGIN_URL.'/'.G5_LGXPAY_DIR);
define('G5_SNS_URL',        G5_PLUGIN_URL.'/'.G5_SNS_DIR);
define('G5_SYNDI_URL',      G5_PLUGIN_URL.'/'.G5_SYNDI_DIR);
define('G5_MOBILE_URL',     G5_URL.'/'.G5_MOBILE_DIR);

// PATH是服务器上的绝对路径
define('G5_ADMIN_PATH',     G5_PATH.'/'.G5_ADMIN_DIR);
define('G5_BBS_PATH',       G5_PATH.'/'.G5_BBS_DIR);
define('G5_DATA_PATH',      G5_PATH.'/'.G5_DATA_DIR);
define('G5_EXTEND_PATH',    G5_PATH.'/'.G5_EXTEND_DIR);
define('G5_LIB_PATH',       G5_PATH.'/'.G5_LIB_DIR);
define('G5_PLUGIN_PATH',    G5_PATH.'/'.G5_PLUGIN_DIR);
define('G5_SKIN_PATH',      G5_PATH.'/'.G5_SKIN_DIR);
define('G5_MOBILE_PATH',    G5_PATH.'/'.G5_MOBILE_DIR);
define('G5_SESSION_PATH',   G5_DATA_PATH.'/'.G5_SESSION_DIR);
define('G5_CAPTCHA_PATH',   G5_PLUGIN_PATH.'/'.G5_CAPTCHA_DIR);
define('G5_EDITOR_PATH',    G5_PLUGIN_PATH.'/'.G5_EDITOR_DIR);
define('G5_OKNAME_PATH',    G5_PLUGIN_PATH.'/'.G5_OKNAME_DIR);

define('G5_KCPCERT_PATH',   G5_PLUGIN_PATH.'/'.G5_KCPCERT_DIR);
define('G5_LGXPAY_PATH',    G5_PLUGIN_PATH.'/'.G5_LGXPAY_DIR);

define('G5_SNS_PATH',       G5_PLUGIN_PATH.'/'.G5_SNS_DIR);
define('G5_SYNDI_PATH',     G5_PLUGIN_PATH.'/'.G5_SYNDI_DIR);
define('G5_PHPMAILER_PATH', G5_PLUGIN_PATH.'/'.G5_PHPMAILER_DIR);
//==============================================================================


//==============================================================================
//访问终端设置
// 设置为pc时强制在所有终端显示为pc版本界面
// 设置为mobile时在所有终端显示为触屏版本界面
// 设置为both时根据接入设备自动适应界面
//------------------------------------------------------------------------------
define('G5_SET_DEVICE', 'both');

define('G5_USE_MOBILE', true); // 如果不使用触屏版请使用flase进行关闭
define('G5_USE_CACHE',  true); // 是否开启最新文章cache功能，开启cache功能将大幅降低数据库负荷


/********************
    时间参数定义
********************/
// 当服务器时间与实际时间不同，且无法更改服务器时间时使用
// 一天是86400秒，一小时3600秒
// 如果快6个小时 time() + (3600 * 6);
// 如果慢6个小时 time() - (3600 * 6);
define('G5_SERVER_TIME',    time());
define('G5_TIME_YMDHIS',    date('Y-m-d H:i:s', G5_SERVER_TIME));
define('G5_TIME_YMD',       substr(G5_TIME_YMDHIS, 0, 10));
define('G5_TIME_HIS',       substr(G5_TIME_YMDHIS, 11, 8));

// 输入内容检测(请勿更改数字)
define('G5_ALPHAUPPER',      1); // 英文大写
define('G5_ALPHALOWER',      2); // 英文小写
define('G5_ALPHABETIC',      4); // 英文大小写
define('G5_NUMERIC',         8); // 数字
define('G5_CHINESE',         16); // 汉字
define('G5_SPACE',          32); // 空格
define('G5_SPECIAL',        64); // 特殊符号

// 属性
define('G5_DIR_PERMISSION',  0755); // 创建文件夹时属性设置
define('G5_FILE_PERMISSION', 0644); // 创建文件时属性设置

// 判断移动终端设备设置 $_SERVER['HTTP_USER_AGENT']
define('G5_MOBILE_AGENT',   'iphone|ipod|ipad|ipad|Android|nokia|blackberry|webos|webos|webmate|bada|lg|ucweb|skyfire|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|wap|mobile');

// SMTP
// lib/mailer.lib.php 中使用
define('G5_SMTP', '127.0.0.1');


/********************
    其他参数定义
********************/

// 是否显示SQL错误
// 如果需要显示则修改为TRUE
define('G5_DISPLAY_SQL_ERROR', FALSE);

// escape string 处理函数设定
// 可修改为addslashes
define('G5_ESCAPE_FUNCTION', 'sql_escape_string');

// 使用sql_escape_string函数时
//define('G5_ESCAPE_PATTERN',  '/(and|or).*(union|select|insert|update|delete|from|where|limit|create|drop).*/i');
//define('G5_ESCAPE_REPLACE',  '');

// 论坛版块默认链接添加数量
// 如果需要增加数据字段请根据需要修改数字
define('G5_LINK_COUNT', 2);

// 缩略图压缩 jpg Quality 压缩质量设置
define('G5_THUMB_JPG_QUALITY', 90);

// 缩略图压缩 png Compress 压缩质量设置
define('G5_THUMB_PNG_COMPRESS', 5);

// ip地址隐藏设置
/* 将123.456.789.012 ip地址隐藏方法如下
\\1 对应 123, \\2对应 456, \\3对应 789, \\4对应 012
如需显示的部分可以使用如 \\1 段落表示，需要隐藏部分可以使用 ♡等符号
进行文字替换实现隐藏。
*/
define('G5_IP_DISPLAY', '\\1.♡.\\3.\\4');

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {   //https 通信时daum地址 js
    define('G5_POSTCODE_JS', '<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.js"></script>');
} else {  //http 通信时daum地址 js
    define('G5_POSTCODE_JS', '<script src="http://dmaps.daum.net/map_js_init/postcode.js"></script>');
}
?>