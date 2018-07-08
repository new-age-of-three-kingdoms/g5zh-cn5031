<?php
/*******************************************************************************
**全局公用参数代码集合
*******************************************************************************/
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );

// 可以在安全设定原因或使用不同iframe时也可以正常读取cookie
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');

if (!defined('G5_SET_TIME_LIMIT')) define('G5_SET_TIME_LIMIT', 0);
@set_time_limit(G5_SET_TIME_LIMIT);


//==========================================================================================================================
// 防止extract($_GET);导致产生 page.php?_POST[var1]=data1&_POST[var2]=data2 形式代码使用 _POST参数
// 081029 : letsgolee 提供了帮助
//--------------------------------------------------------------------------------------------------------------------------
$ext_arr = array ('PHP_SELF', '_ENV', '_GET', '_POST', '_FILES', '_SERVER', '_COOKIE', '_SESSION', '_REQUEST',
                  'HTTP_ENV_VARS', 'HTTP_GET_VARS', 'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS',
                  'HTTP_COOKIE_VARS', 'HTTP_SESSION_VARS', 'GLOBALS');
$ext_cnt = count($ext_arr);
for ($i=0; $i<$ext_cnt; $i++) {
    // 如有POST,GET设定全局参数则unset()
    if (isset($_GET[$ext_arr[$i]]))  unset($_GET[$ext_arr[$i]]);
    if (isset($_POST[$ext_arr[$i]])) unset($_POST[$ext_arr[$i]]);
}
//==========================================================================================================================


function g5_path()
{
    $result['path'] = str_replace('\\', '/', dirname(__FILE__));
    $tilde_remove = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $_SERVER['SCRIPT_NAME']);
    $document_root = str_replace($tilde_remove, '', $_SERVER['SCRIPT_FILENAME']);
    $root = str_replace($document_root, '', $result['path']);
    $port = $_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '';
    $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://';
    $user = str_replace(str_replace($document_root, '', $_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_NAME']);
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    if(isset($_SERVER['HTTP_HOST']) && preg_match('/:[0-9]+$/', $host))
        $host = preg_replace('/:[0-9]+$/', '', $host);
    $result['url'] = $http.$host.$port.$user.$root;
    return $result;
}

$g5_path = g5_path();

include_once($g5_path['path'].'/config.php');   //设定文件

unset($g5_path);


// multi-dimensional array中应用使用者定义代码
function array_map_deep($fn, $array)
{
    if(is_array($array)) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                $array[$key] = array_map_deep($fn, $value);
            } else {
                $array[$key] = call_user_func($fn, $value);
            }
        }
    } else {
        $array = call_user_func($fn, $array);
    }

    return $array;
}


// 防止SQL Injection (SQL注入攻击)
function sql_escape_string($str)
{
    if(defined('G5_ESCAPE_PATTERN') && defined('G5_ESCAPE_REPLACE')) {
        $pattern = G5_ESCAPE_PATTERN;
        $replace = G5_ESCAPE_REPLACE;

        if($pattern)
            $str = preg_replace($pattern, $replace, $str);
    }

    $str = call_user_func('addslashes', $str);

    return $str;
}


//==============================================================================
// 为了防止SQL注入攻击应用sql_escape_string()
//------------------------------------------------------------------------------
// 根据magic_quotes_gpc 去除 backslashes
if (get_magic_quotes_gpc()) {
    $_POST    = array_map_deep('stripslashes',  $_POST);
    $_GET     = array_map_deep('stripslashes',  $_GET);
    $_COOKIE  = array_map_deep('stripslashes',  $_COOKIE);
    $_REQUEST = array_map_deep('stripslashes',  $_REQUEST);
}

// 应用sql_escape_string
$_POST    = array_map_deep(G5_ESCAPE_FUNCTION,  $_POST);
$_GET     = array_map_deep(G5_ESCAPE_FUNCTION,  $_GET);
$_COOKIE  = array_map_deep(G5_ESCAPE_FUNCTION,  $_COOKIE);
$_REQUEST = array_map_deep(G5_ESCAPE_FUNCTION,  $_REQUEST);
//==============================================================================


// PHP 4.1.0以上版本开始支持
// php.ini 设置项register_globals=off的情况下使用
@extract($_GET);
@extract($_POST);
@extract($_SERVER);


// 会员豌豆荚提供的安全相关修复
// $member可附带参数传递
$config = array();
$member = array();
$board  = array();
$group  = array();
$g5     = array();


//==============================================================================
// 公用
//------------------------------------------------------------------------------
$dbconfig_file = G5_DATA_PATH.'/'.G5_DBCONFIG_FILE;
if (file_exists($dbconfig_file)) {
    include_once($dbconfig_file);
    include_once(G5_LIB_PATH.'/common.lib.php');    // 公用库

    $connect_db = sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD) or die('MySQL Connect Error!!!');
    $select_db  = sql_select_db(G5_MYSQL_DB, $connect_db) or die('MySQL DB Error!!!');

    // mysql connect resource $g5 储存排列 
    $g5['connect_db'] = $connect_db;

    sql_query(" set names utf8 ");
    if(defined('G5_MYSQL_SET_MODE') && G5_MYSQL_SET_MODE) sql_query("SET SESSION sql_mode = ''");
    if (defined(G5_TIMEZONE)) sql_query(" set time_zone = '".G5_TIMEZONE."'");
} else {
?>

<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<title>错误！<?php echo G5_VERSION ?> 开始安装</title>
<link rel="stylesheet" href="install/install.css">
</head>
<body>

<div id="ins_bar">
    <span id="bar_img">GNUBOARD5</span>
    <span id="bar_txt">Message</span>
</div>
<h1>请先安装Gnuboard5主程序</h1>
<div class="ins_inner">
    <p>无法找到以下文件</p>
    <ul>
        <li><strong><?php echo G5_DATA_DIR.'/'.G5_DBCONFIG_FILE ?></strong></li>
    </ul>
    <p>请安装Gnuboard程序后重新运行此程序</p>
    <div class="inner_btn">
        <a href="<?php echo G5_URL; ?>/install/"><?php echo G5_VERSION ?> 开始安装</a>
    </div>
</div>
<div id="ins_ft">
    <strong>GNUBOARD5</strong>
    <p>GPL! OPEN SOURCE GNUBOARD中文版</p>
</div>

</body>
</html>

<?php
    exit;
}
//==============================================================================


//==============================================================================
// SESSION 设置
//------------------------------------------------------------------------------
@ini_set("session.use_trans_sid", 0);    // 防止自动传递PHPSESSID
@ini_set("url_rewriter.tags",""); // 防止在url地址中传递PHPSESSID（会员提供）

session_save_path(G5_SESSION_PATH);

if (isset($SESSION_CACHE_LIMITER))
    @session_cache_limiter($SESSION_CACHE_LIMITER);
else
    @session_cache_limiter("no-cache, must-revalidate");

ini_set("session.cache_expire", 180); // session储存时间（分钟）
ini_set("session.gc_maxlifetime", 10800); // session data的 garbage collection 储存时间（秒）
ini_set("session.gc_probability", 1); // session.gc_probability与 session.gc_divisor链接，进行gc(回收) 启动几率管理，默认值为1，更多内容请参看session.gc_divisor
ini_set("session.gc_divisor", 100); // session.gc_divisor将于session.gc_probability结合，在session初始化时gc(回收) 启动几率管理，启动几率通过 gc_probability/gc_divisor计算. 如：1/100时每次请求时GC启动几率为1%. session.gc_divisor默认值为100

session_set_cookie_params(0, '/');
ini_set("session.cookie_domain", G5_COOKIE_DOMAIN);

@session_start();
//==============================================================================


//==============================================================================
// 公用参数
//------------------------------------------------------------------------------
//基本环境设置
//获取基本数据表后根据情况添加字段
$config = sql_fetch(" select * from {$g5['config_table']} ");

define('G5_HTTP_BBS_URL',  https_url(G5_BBS_DIR, false));
define('G5_HTTPS_BBS_URL', https_url(G5_BBS_DIR, true));
if ($config['cf_editor'])
    define('G5_EDITOR_LIB', G5_EDITOR_PATH."/{$config['cf_editor']}/editor.lib.php");
else
    define('G5_EDITOR_LIB', G5_LIB_PATH."/editor.lib.php");

//==============================================================================
//访问终端设置
// 可通过config.php G5_SET_DEVICE 设定
// 设置为pc时强制在所有终端显示为pc版本界面
// 设置为mobile时在所有终端显示为触屏版本界面
// 设置为both时根据接入设备自动适应界面
//------------------------------------------------------------------------------
$is_mobile = false;
$set_device = true;
if(defined('G5_SET_DEVICE')) {
    switch(G5_SET_DEVICE) {
        case 'pc':
            $is_mobile  = false;
            $set_device = false;
            break;
        case 'mobile':
            $is_mobile  = true;
            $set_device = false;
            break;
        default:
            break;
    }
}
//==============================================================================

//==============================================================================
// Mobile移动终端设置
// 如cookie中储存的客户端参数为mobile时无论客户端使用何种浏览器均进入移动终端界面
//如果不是则根据HTTP_USER_AGENT进行判断
// G5_MOBILE_AGENT : config.php 中进行设置
//------------------------------------------------------------------------------
if (G5_USE_MOBILE && $set_device) {
    if ($_REQUEST['device']=='pc')
        $is_mobile = false;
    else if ($_REQUEST['device']=='mobile')
        $is_mobile = true;
    else if (isset($_SESSION['ss_is_mobile']))
        $is_mobile = $_SESSION['ss_is_mobile'];
    else if (is_mobile())
        $is_mobile = true;
} else {
    $set_device = false;
}

$_SESSION['ss_is_mobile'] = $is_mobile;
define('G5_IS_MOBILE', $is_mobile);
define('G5_DEVICE_BUTTON_DISPLAY', $set_device);
if (G5_IS_MOBILE) {
    $g5['mobile_path'] = G5_PATH.'/'.$g5['mobile_dir'];
}
//==============================================================================

// 4.00.03 : [安全补丁] PHPSESSID 不一致则注销账户.
if (isset($_REQUEST['PHPSESSID']) && $_REQUEST['PHPSESSID'] != session_id())
    goto_url(G5_BBS_URL.'/logout.php');

// QUERY_STRING
$qstr = '';

if (isset($_REQUEST['sca']))  {
    $sca = clean_xss_tags(trim($_REQUEST['sca']));
    if ($sca)
        $qstr .= '&amp;sca=' . urlencode($sca);
} else {
    $sca = '';
}

if (isset($_REQUEST['sfl']))  {
    $sfl = trim($_REQUEST['sfl']);
    $sfl = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $sfl);
    if ($sfl)
        $qstr .= '&amp;sfl=' . urlencode($sfl); // search field (搜索字段)
} else {
    $sfl = '';
}


if (isset($_REQUEST['stx']))  { // search text (关键词)
    $stx = get_search_string(trim($_REQUEST['stx']));
    if ($stx)
        $qstr .= '&amp;stx=' . urlencode(cut_str($stx, 20, ''));
} else {
    $stx = '';
}

if (isset($_REQUEST['sst']))  {
    $sst = trim($_REQUEST['sst']);
    $sst = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $sst);
    if ($sst)
        $qstr .= '&amp;sst=' . urlencode($sst); // search sort (搜索排列字段)
} else {
    $sst = '';
}

if (isset($_REQUEST['sod']))  { // search order (搜索结果排序)
    $sod = preg_match("/^(asc|desc)$/i", $sod) ? $sod : '';
    if ($sod)
        $qstr .= '&amp;sod=' . urlencode($sod);
} else {
    $sod = '';
}

if (isset($_REQUEST['sop']))  { // search operator (搜索 or, and 运算符)
    $sop = preg_match("/^(or|and)$/i", $sop) ? $sop : '';
    if ($sop)
        $qstr .= '&amp;sop=' . urlencode($sop);
} else {
    $sop = '';
}

if (isset($_REQUEST['spt']))  { // search part (搜索段落[区间])
    $spt = (int)$spt;
    if ($spt)
        $qstr .= '&amp;spt=' . urlencode($spt);
} else {
    $spt = '';
}

if (isset($_REQUEST['page'])) { // 目录列表(LIST)页面
    $page = (int)$_REQUEST['page'];
    if ($page)
        $qstr .= '&amp;page=' . urlencode($page);
} else {
    $page = '';
}

if (isset($_REQUEST['w'])) {
    $w = substr($w, 0, 2);
} else {
    $w = '';
}

if (isset($_REQUEST['wr_id'])) {
    $wr_id = (int)$_REQUEST['wr_id'];
} else {
    $wr_id = 0;
}

if (isset($_REQUEST['bo_table'])) {
    $bo_table = preg_replace('/[^a-z0-9_]/i', '', trim($_REQUEST['bo_table']));
    $bo_table = substr($bo_table, 0, 20);
} else {
    $bo_table = '';
}

// URL ENCODING
if (isset($_REQUEST['url'])) {
    $url = strip_tags(trim($_REQUEST['url']));
    $urlencode = urlencode($url);
} else {
    $url = '';
    $urlencode = urlencode($_SERVER['REQUEST_URI']);
    if (G5_DOMAIN) {
        $p = parse_url(G5_DOMAIN);
        $urlencode = G5_DOMAIN.urldecode(preg_replace("/^".urlencode($p['path'])."/", "", $urlencode));
    }
}

if (isset($_REQUEST['gr_id'])) {
    if (!is_array($_REQUEST['gr_id'])) {
        $gr_id = preg_replace('/[^a-z0-9_]/i', '', trim($_REQUEST['gr_id']));
    }
} else {
    $gr_id = '';
}
//===================================


// 自动登录代码中原有首次登录赠送积分功能由首次登录时产生积分更改为登录状态时赠送积分
if ($_SESSION['ss_mb_id']) { //如果已登录
    $member = get_member($_SESSION['ss_mb_id']);

    // 如果是已被禁止登录的会员将ss_mb_id进行初始化
    if($member['mb_intercept_date'] && $member['mb_intercept_date'] <= date("Ymd", G5_SERVER_TIME)) {
        set_session('ss_mb_id', '');
        $member = array();
    } else {
        // 如果判断为今天首次登录
        if (substr($member['mb_today_login'], 0, 10) != G5_TIME_YMD) {
            // 每日首次登录积分（签到）
            insert_point($member['mb_id'], $config['cf_login_point'], G5_TIME_YMD.' 每日签到', '@login', $member['mb_id'], G5_TIME_YMD);

            // 当前登录由可能是首次登录同时也是当天最后一次登录
            // 记录会员登录时间与ip地址
            $sql = " update {$g5['member_table']} set mb_today_login = '".G5_TIME_YMDHIS."', mb_login_ip = '{$_SERVER['REMOTE_ADDR']}' where mb_id = '{$member['mb_id']}' ";
            sql_query($sql);
        }
    }
} else {
    // 自动登录 ---------------------------------------
    // 如果cookie中已有会员id信息 (3.27)
    if ($tmp_mb_id = get_cookie('ck_mb_id')) {

        $tmp_mb_id = substr(preg_replace("/[^a-zA-Z0-9_]*/", "", $tmp_mb_id), 0, 20);
        // 网站管理员禁止使用自动登录功能
        if (strtolower($tmp_mb_id) != strtolower($config['cf_admin'])) {
            $sql = " select mb_password, mb_intercept_date, mb_leave_date, mb_email_certify from {$g5['member_table']} where mb_id = '{$tmp_mb_id}' ";
            $row = sql_fetch($sql);
            $key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $row['mb_password']);
            // cookie储存值验证一致时
            $tmp_key = get_cookie('ck_auto');
            if ($tmp_key == $key && $tmp_key) {
                // 验证会员账户是否状态正常(非注销账户、邮件未认证账号等)
                if ($row['mb_intercept_date'] == '' &&
                    $row['mb_leave_date'] == '' &&
                    (!$config['cf_use_email_certify'] || preg_match('/[1-9]/', $row['mb_email_certify'])) ) {
                    // session记录会员id后标记为登录
                    set_session('ss_mb_id', $tmp_mb_id);

                    // 重新加载执行
                    echo "<script type='text/javascript'> window.location.reload(); </script>";
                    exit;
                }
            }
            // 解除$row排列参数
            unset($row);
        }
    }
    // 自动登录 end ---------------------------------------
}


$write = array();
$write_table = "";
if ($bo_table) {
    $board = sql_fetch(" select * from {$g5['board_table']} where bo_table = '$bo_table' ");
    if ($board['bo_table']) {
        set_cookie("ck_bo_table", $board['bo_table'], 86400 * 1);
        $gr_id = $board['gr_id'];
        $write_table = $g5['write_prefix'] . $bo_table; // 所有论坛版块名称
        //$comment_table = $g5['write_prefix'] . $bo_table . $g5['comment_suffix']; // 所有评论数据表名称
        if (isset($wr_id) && $wr_id)
            $write = sql_fetch(" select * from $write_table where wr_id = '$wr_id' ");
    }
}

if ($gr_id) {
    $group = sql_fetch(" select * from {$g5['group_table']} where gr_id = '$gr_id' ");
}


// 会员、游客区分判断
$is_member = $is_guest = false;
$is_admin = '';
if ($member['mb_id']) {
    $is_member = true;
    $is_admin = is_admin($member['mb_id']);
    $member['mb_dir'] = substr($member['mb_id'],0,2);
} else {
    $is_guest = true;
    $member['mb_id'] = '';
    $member['mb_level'] = 1; // 游客(未注册访客)会员等级设置为最低（最低值1）
}


if ($is_admin != 'super') {
    // 允许访问IP地址
    $cf_possible_ip = trim($config['cf_possible_ip']);
    if ($cf_possible_ip) {
        $is_possible_ip = false;
        $pattern = explode("\n", $cf_possible_ip);
        for ($i=0; $i<count($pattern); $i++) {
            $pattern[$i] = trim($pattern[$i]);
            if (empty($pattern[$i]))
                continue;

            $pattern[$i] = str_replace(".", "\.", $pattern[$i]);
            $pattern[$i] = str_replace("+", "[0-9\.]+", $pattern[$i]);
            $pat = "/^{$pattern[$i]}$/";
            $is_possible_ip = preg_match($pat, $_SERVER['REMOTE_ADDR']);
            if ($is_possible_ip)
                break;
        }
        if (!$is_possible_ip)
            die ("没有访问权限");
    }

    // 禁止访问IP地址 
    $is_intercept_ip = false;
    $pattern = explode("\n", trim($config['cf_intercept_ip']));
    for ($i=0; $i<count($pattern); $i++) {
        $pattern[$i] = trim($pattern[$i]);
        if (empty($pattern[$i]))
            continue;

        $pattern[$i] = str_replace(".", "\.", $pattern[$i]);
        $pattern[$i] = str_replace("+", "[0-9\.]+", $pattern[$i]);
        $pat = "/^{$pattern[$i]}$/";
        $is_intercept_ip = preg_match($pat, $_SERVER['REMOTE_ADDR']);
        if ($is_intercept_ip)
            die ("状态异常，无可用服务");
    }
}


//==============================================================================
//皮肤(skin)目录
//------------------------------------------------------------------------------
if (G5_IS_MOBILE) {
    $board_skin_path    = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/board/'.$board['bo_mobile_skin'];
    $board_skin_url     = G5_MOBILE_URL .'/'.G5_SKIN_DIR.'/board/'.$board['bo_mobile_skin'];
    $member_skin_path   = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/member/'.$config['cf_mobile_member_skin'];
    $member_skin_url    = G5_MOBILE_URL .'/'.G5_SKIN_DIR.'/member/'.$config['cf_mobile_member_skin'];
    $new_skin_path      = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/new/'.$config['cf_mobile_new_skin'];
    $new_skin_url       = G5_MOBILE_URL .'/'.G5_SKIN_DIR.'/new/'.$config['cf_mobile_new_skin'];
    $search_skin_path   = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/search/'.$config['cf_mobile_search_skin'];
    $search_skin_url    = G5_MOBILE_URL .'/'.G5_SKIN_DIR.'/search/'.$config['cf_mobile_search_skin'];
    $connect_skin_path  = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/connect/'.$config['cf_mobile_connect_skin'];
    $connect_skin_url   = G5_MOBILE_URL .'/'.G5_SKIN_DIR.'/connect/'.$config['cf_mobile_connect_skin'];
    $faq_skin_path      = G5_MOBILE_PATH .'/'.G5_SKIN_DIR.'/faq/'.$config['cf_mobile_faq_skin'];
    $faq_skin_url       = G5_MOBILE_URL .'/'.G5_SKIN_DIR.'/faq/'.$config['cf_mobile_faq_skin'];
} else {
    $board_skin_path    = G5_SKIN_PATH.'/board/'.$board['bo_skin'];
    $board_skin_url     = G5_SKIN_URL .'/board/'.$board['bo_skin'];
    $member_skin_path   = G5_SKIN_PATH.'/member/'.$config['cf_member_skin'];
    $member_skin_url    = G5_SKIN_URL .'/member/'.$config['cf_member_skin'];
    $new_skin_path      = G5_SKIN_PATH.'/new/'.$config['cf_new_skin'];
    $new_skin_url       = G5_SKIN_URL .'/new/'.$config['cf_new_skin'];
    $search_skin_path   = G5_SKIN_PATH.'/search/'.$config['cf_search_skin'];
    $search_skin_url    = G5_SKIN_URL .'/search/'.$config['cf_search_skin'];
    $connect_skin_path  = G5_SKIN_PATH.'/connect/'.$config['cf_connect_skin'];
    $connect_skin_url   = G5_SKIN_URL .'/connect/'.$config['cf_connect_skin'];
    $faq_skin_path      = G5_SKIN_PATH.'/faq/'.$config['cf_faq_skin'];
    $faq_skin_url       = G5_SKIN_URL.'/faq/'.$config['cf_faq_skin'];
}
//==============================================================================


// 记录访客访问记录
include_once(G5_BBS_PATH.'/visit_insert.inc.php');


// 达到指定时间删除DB数据及进行优化
include_once(G5_BBS_PATH.'/db_table.optimize.php');


// 减少common.php修改需求 
$extend_file = array();
$tmp = dir(G5_EXTEND_PATH);
while ($entry = $tmp->read()) {
    // 仅include php文件
    if (preg_match("/(\.php)$/i", $entry))
        $extend_file[] = $entry;
}

if(!empty($extend_file) && is_array($extend_file)) {
    natsort($extend_file);

    foreach($extend_file as $file) {
        include_once(G5_EXTEND_PATH.'/'.$file);
    }
}
unset($extend_file);

ob_start();

// 解决javascript中使用 go(-1)函数时输入框消失的问题
// 获取cookie内容，尚未进行全面验证
header('Content-Type: text/html; charset=utf-8');
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0

$html_process = new html_process();
?>