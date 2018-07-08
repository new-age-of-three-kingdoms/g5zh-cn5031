<?php
@set_time_limit(0);
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0

include_once ('../config.php');
$title = G5_VERSION." 安装完成 3/3";
include_once ('./install.inc.php');

//print_r($_POST); exit;

$mysql_host  = $_POST['mysql_host'];
$mysql_user  = $_POST['mysql_user'];
$mysql_pass  = $_POST['mysql_pass'];
$mysql_db    = $_POST['mysql_db'];
$table_prefix= $_POST['table_prefix'];
$admin_id    = $_POST['admin_id'];
$admin_pass  = $_POST['admin_pass'];
$admin_name  = $_POST['admin_name'];
$admin_email = $_POST['admin_email'];

$dblink = @mysql_connect($mysql_host, $mysql_user, $mysql_pass);
if (!$dblink) {
?>

<div class="ins_inner">
    <p>数据库设置有误，请检查数据库连接信息设置！</p>
    <div class="inner_btn"><a href="./install_config.php">返回</a></div>
</div>

<?php
    include_once ('./install.inc2.php');
    exit;
}

$select_db = @mysql_select_db($mysql_db, $dblink);
if (!$select_db) {
?>

<div class="ins_inner">
    <p>无法连接的数据库，请检查mysql db设置</p>
    <div class="inner_btn"><a href="./install_config.php">返回</a></div>
</div>

<?php
    include_once ('./install.inc2.php');
    exit;
}

$mysql_set_mode = 'false';
@mysql_query('set names utf8');
if(version_compare(mysql_get_server_info(), '5.6.6', '>=')  == 1) {
    @mysql_query("SET SESSION sql_mode = ''");
    $mysql_set_mode = 'true';
}
?>

<div class="ins_inner">
    <h2><?php echo G5_VERSION ?> 正在安装</h2>

    <ol>
<?php
// 创建数据表 ------------------------------------
$file = implode('', file('./gnuboard5.sql'));
eval("\$file = \"$file\";");

$file = preg_replace('/^--.*$/m', '', $file);
$file = preg_replace('/`g5_([^`]+`)/', '`'.$table_prefix.'$1', $file);
$f = explode(';', $file);
for ($i=0; $i<count($f); $i++) {
    if (trim($f[$i]) == '') continue;
    mysql_query($f[$i]) or die(mysql_error());
}
// 创建数据表 ------------------------------------
?>

        <li>数据创建成功！</li>

<?php
$read_point = 0;
$write_point = 0;
$comment_point = 0;
$download_point = 0;

//-------------------------------------------------------------------------------------------------
// config 数据设置
$sql = " insert into `{$table_prefix}config`
            set cf_title = '".G5_VERSION."',
                cf_admin = '$admin_id',
                cf_admin_email = '$admin_email',
                cf_admin_email_name = '".G5_VERSION."',
                cf_use_point = '1',
                cf_use_copy_log = '1',
                cf_login_point = '100',
                cf_memo_send_point = '500',
                cf_cut_name = '15',
                cf_nick_modify = '60',
                cf_new_skin = 'basic',
                cf_new_rows = '15',
                cf_search_skin = 'basic',
                cf_connect_skin = 'basic',
                cf_read_point = '$read_point',
                cf_write_point = '$write_point',
                cf_comment_point = '$comment_point',
                cf_download_point = '$download_point',
                cf_write_pages = '10',
                cf_mobile_pages = '5',
                cf_link_target = '_blank',
                cf_delay_sec = '30',
                cf_filter = '政治风波,陈良宇,反民主,反政府,反共,政府软弱,政府无能,共狗,回民暴动,上海交大,九评,大纪元,大紀元,手机复制卡,手机魔卡,十七大代表,第二代身份证,暴力拆迁,轮暴致死,新式军服,对日强硬,李鹏,唐人电视台,人民报,天葬,平安夜自杀,轮奸虐杀,暴力镇压301医院,陈水扁,转法轮,达赖,共匪,胡锦涛,明慧,退党,天安门事件,自由亚州,极景,美国之音,活体,江泽民,毛泽东,民主评论,民运,六四事件,无界,高干子弟名单,窃听器,色情电影,成人贴图,激情电影,色情小说,黄网导航,蒙汗药,迷魂,迷奸,三唑仑,春药,催情药,麻醉枪,出售枪支,法+轮+功,法+輪+功,学院+暴动,共+产+党,共+產+黨,共+铲+党,域名备案,擦鞋机,老虎机,除湿机,时代互联,抢答器,光端机,美服,入党申请书,ow gold,uck,itch,他妈的,法轮,alundafa,alun,操你妈,三级片,草你妈,a轮功,a轮功,alun,日你,我日,uck,hit,我操,我草,阴茎,傻B,你妈的,操你,干你,日您,屁眼,国民党,台独,卖淫,傻逼,阴道,阳痿,法輪,大记元,六四运动,李洪志,真善忍,新唐人,肉棍,淫靡,淫水,迷药,迷昏药,六合彩,买卖枪支,麻醉,乙醚,群发器,帝国之梦,毛一鲜,黎阳平,色情,对日强硬,迷药,摇头丸,天葬,鬼村,军长发威,黑社会,恶搞,枪决女犯,投毒杀人,强硬发言,出售假币,监听,麻醉,昏药,侦探设备,反华,官商勾结,升达毕业证,手机复制,戴海静,自杀,张小平,佳静安定片,迷香,失意药,透视眼镜,远程偷拍,自制手枪,子女任职名单,激情小电影,黄色小电影,色情小电影,天鹅之旅,高校骚乱,大学骚乱,高校群体事件,高校暴乱,盘古乐队,拍肩神药,身份证生成器,枪决现场,出售手枪,办理证件,高干子女,高干子弟,藏独,疆独,枪支弹药,血腥图片,特码,无界浏览器,禁书,反政府,成人电影,换妻,换偶',
                cf_possible_ip = '',
                cf_intercept_ip = '',
                cf_analytics = '',
                cf_member_skin = 'basic',
                cf_mobile_new_skin = 'basic',
                cf_mobile_search_skin = 'basic',
                cf_mobile_connect_skin = 'basic',
                cf_mobile_member_skin = 'basic',
                cf_faq_skin = 'basic',
                cf_mobile_faq_skin = 'basic',
                cf_editor = 'smarteditor2',
                cf_captcha_mp3 = 'basic',
                cf_register_level = '2',
                cf_register_point = '1000',
                cf_icon_level = '2',
                cf_leave_day = '30',
                cf_search_part = '10000',
                cf_email_use = '1',
                cf_prohibit_id = 'admin,administrator,管理员,站长,版主,客服,webmaster,主编,sysop,店长,manager,root,游客,su,guest,访客,匿名,会员,红包,点击，领取',
                cf_prohibit_email = '',
                cf_new_del = '30',
                cf_memo_del = '180',
                cf_visit_del = '180',
                cf_popular_del = '180',
                cf_use_member_icon = '2',
                cf_member_icon_size = '5000',
                cf_member_icon_width = '22',
                cf_member_icon_height = '22',
                cf_login_minutes = '10',
                cf_image_extension = 'gif|jpg|jpeg|png',
                cf_flash_extension = 'swf',
                cf_movie_extension = 'asx|asf|wmv|wma|mpg|mpeg|mov|avi|mp3',
                cf_formmail_is_member = '1',
                cf_page_rows = '15',
                cf_mobile_page_rows = '15',
                cf_cert_limit = '2',
                cf_stipulation = '请设置会员注册条款',
                cf_privacy = '请设置个人隐私保护条例'
                ";
mysql_query($sql) or die(mysql_error() . "<p>" . $sql);

// 在线咨询设置
$sql = " insert into `{$table_prefix}qa_config`
            ( qa_title, qa_category, qa_skin, qa_mobile_skin, qa_use_email, qa_req_email, qa_use_hp, qa_req_hp, qa_use_editor, qa_subject_len, qa_mobile_subject_len, qa_page_rows, qa_mobile_page_rows, qa_image_width, qa_upload_size, qa_insert_content )
          values
            ( '在线咨询', '会员|积分', 'basic', 'basic', '1', '0', '1', '0', '1', '60', '30', '15', '15', '600', '1048576', '' ) ";
mysql_query($sql);

// 管理员注册
$sql = " insert into `{$table_prefix}member`
            set mb_id = '$admin_id',
                 mb_password = PASSWORD('$admin_pass'),
                 mb_name = '$admin_name',
                 mb_nick = '$admin_name',
                 mb_email = '$admin_email',
                 mb_level = '10',
                 mb_mailling = '1',
                 mb_open = '1',
                 mb_email_certify = '".G5_TIME_YMDHIS."',
                 mb_datetime = '".G5_TIME_YMDHIS."',
                 mb_ip = '{$_SERVER['REMOTE_ADDR']}'
                 ";
@mysql_query($sql);

// 内容创建管理
@mysql_query(" insert into `{$table_prefix}content` set co_id = 'company', co_html = '1', co_subject = '网站介绍', co_content= '<p align=center><b>请设置公司介绍内容</b></p>' ") or die(mysql_error() . "<p>" . $sql);
@mysql_query(" insert into `{$table_prefix}content` set co_id = 'privacy', co_html = '1', co_subject = '个人隐私保护条例', co_content= '<p align=center><b>请设置个人隐私保护条例</b></p>' ") or die(mysql_error() . "<p>" . $sql);
@mysql_query(" insert into `{$table_prefix}content` set co_id = 'provision', co_html = '1', co_subject = '网站服务条款', co_content= '<p align=center><b>请设置网站服务条款</b></p>' ") or die(mysql_error() . "<p>" . $sql);

// FAQ Master
@mysql_query(" insert into `{$table_prefix}faq_master` set fm_id = '1', fm_subject = '常见问题' ") or die(mysql_error() . "<p>" . $sql);
?>

        <li>数据库设置完成</li>

<?php
//-------------------------------------------------------------------------------------------------

// 文件夹创建
$dir_arr = array (
    $data_path.'/cache',
    $data_path.'/editor',
    $data_path.'/file',
    $data_path.'/log',
    $data_path.'/member',
    $data_path.'/session',
    $data_path.'/content',
    $data_path.'/faq',
    $data_path.'/tmp'
);

for ($i=0; $i<count($dir_arr); $i++) {
    @mkdir($dir_arr[$i], G5_DIR_PERMISSION);
    @chmod($dir_arr[$i], G5_DIR_PERMISSION);
}
?>

        <li>文件夹创建完成</li>

<?php
//-------------------------------------------------------------------------------------------------

// 创建数据连接设置文件
$file = '../'.G5_DATA_DIR.'/'.G5_DBCONFIG_FILE;
$f = @fopen($file, 'a');

fwrite($f, "<?php\n");
fwrite($f, "if (!defined('_GNUBOARD_')) exit;\n");
fwrite($f, "define('G5_MYSQL_HOST', '{$mysql_host}');\n");
fwrite($f, "define('G5_MYSQL_USER', '{$mysql_user}');\n");
fwrite($f, "define('G5_MYSQL_PASSWORD', '{$mysql_pass}');\n");
fwrite($f, "define('G5_MYSQL_DB', '{$mysql_db}');\n");
fwrite($f, "define('G5_MYSQL_SET_MODE', {$mysql_set_mode});\n\n");
fwrite($f, "define('G5_TABLE_PREFIX', '{$table_prefix}');\n\n");
fwrite($f, "\$g5['write_prefix'] = G5_TABLE_PREFIX.'write_'; // 论坛数据表格前缀\n\n");
fwrite($f, "\$g5['auth_table'] = G5_TABLE_PREFIX.'auth'; // 管理权限设置数据\n");
fwrite($f, "\$g5['config_table'] = G5_TABLE_PREFIX.'config'; // 基本设置数据\n");
fwrite($f, "\$g5['group_table'] = G5_TABLE_PREFIX.'group'; // 论坛群组设置数据\n");
fwrite($f, "\$g5['group_member_table'] = G5_TABLE_PREFIX.'group_member'; // 论坛群组会员数据\n");
fwrite($f, "\$g5['board_table'] = G5_TABLE_PREFIX.'board'; // 论坛设置数据\n");
fwrite($f, "\$g5['board_file_table'] = G5_TABLE_PREFIX.'board_file'; // 论坛附件数据\n");
fwrite($f, "\$g5['board_good_table'] = G5_TABLE_PREFIX.'board_good'; // 论坛推荐及反对数据\n");
fwrite($f, "\$g5['board_new_table'] = G5_TABLE_PREFIX.'board_new'; // 论坛新主题数据\n");
fwrite($f, "\$g5['login_table'] = G5_TABLE_PREFIX.'login'; // 登录数据(在线人数\n");
fwrite($f, "\$g5['mail_table'] = G5_TABLE_PREFIX.'mail'; // 会员邮件数据\n");
fwrite($f, "\$g5['member_table'] = G5_TABLE_PREFIX.'member'; // 会员数据\n");
fwrite($f, "\$g5['memo_table'] = G5_TABLE_PREFIX.'memo'; // 备注数据\n");
fwrite($f, "\$g5['poll_table'] = G5_TABLE_PREFIX.'poll'; // 投票数据\n");
fwrite($f, "\$g5['poll_etc_table'] = G5_TABLE_PREFIX.'poll_etc'; // 投票意见及评论数据\n");
fwrite($f, "\$g5['point_table'] = G5_TABLE_PREFIX.'point'; // 积分数据\n");
fwrite($f, "\$g5['popular_table'] = G5_TABLE_PREFIX.'popular'; // 热门关键词数据\n");
fwrite($f, "\$g5['scrap_table'] = G5_TABLE_PREFIX.'scrap'; // 主题收藏数据\n");
fwrite($f, "\$g5['visit_table'] = G5_TABLE_PREFIX.'visit'; // 访客数据\n");
fwrite($f, "\$g5['visit_sum_table'] = G5_TABLE_PREFIX.'visit_sum'; // 访客统计数据\n");
fwrite($f, "\$g5['uniqid_table'] = G5_TABLE_PREFIX.'uniqid'; // uniqid数据\n");
fwrite($f, "\$g5['autosave_table'] = G5_TABLE_PREFIX.'autosave'; // 草稿箱数据\n");
fwrite($f, "\$g5['cert_history_table'] = G5_TABLE_PREFIX.'cert_history'; // 认证数据\n");
fwrite($f, "\$g5['qa_config_table'] = G5_TABLE_PREFIX.'qa_config'; // 在线咨询设置数据\n");
fwrite($f, "\$g5['qa_content_table'] = G5_TABLE_PREFIX.'qa_content'; // 在线咨询数据\n");
fwrite($f, "\$g5['content_table'] = G5_TABLE_PREFIX.'content'; // 内容(项目)信息数据\n");
fwrite($f, "\$g5['faq_table'] = G5_TABLE_PREFIX.'faq'; // 常见问题数据\n");
fwrite($f, "\$g5['faq_master_table'] = G5_TABLE_PREFIX.'faq_master'; // 常见问题主数据\n");
fwrite($f, "\$g5['new_win_table'] = G5_TABLE_PREFIX.'new_win'; // 新窗口数据\n");
fwrite($f, "\$g5['menu_table'] = G5_TABLE_PREFIX.'menu'; // 菜单管理数据\n");
fwrite($f, "?>");

fclose($f);
@chmod($file, G5_FILE_PERMISSION);
?>

        <li>数据库设定文件创建完毕(<?php echo $file ?>)</li>

<?php
// data目录及下级目录使用.htaccess .htpasswd使.php .phtml .html .htm .inc .cgi .pl文件无法执行
$f = fopen($data_path.'/.htaccess', 'w');
$str = <<<EOD
<FilesMatch "\.(htaccess|htpasswd|[Pp][Hh][Pp]|[Pp]?[Hh][Tt][Mm][Ll]?|[Ii][Nn][Cc]|[Cc][Gg][Ii]|[Pp][Ll])">
Order allow,deny
Deny from all
</FilesMatch>
EOD;
fwrite($f, $str);
fclose($f);
//-------------------------------------------------------------------------------------------------
?>
    </ol>

    <p>恭喜您！ <?php echo G5_VERSION ?> 安装完成</p>

</div>

<div class="ins_inner">

    <h2>安装完成后您还需要进行以下操作</h2>

    <ol>
        <li>进入首页</li>
        <li>登录管理员账号</li>
        <li>进入系统后台</li>
        <li>进行基本设置及会员设置</li>
    </ol>

    <div class="inner_btn">
        <a href="../index.php">进入全新的G5中文版</a>
    </div>

</div>

<?php
include_once ('./install.inc2.php');
?>