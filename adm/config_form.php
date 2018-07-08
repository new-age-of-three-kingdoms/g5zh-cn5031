<?php
$sub_menu = "100100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

if ($is_admin != 'super')
    alert('您没有访问权限');

if (!isset($config['cf_include_index'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_include_index` VARCHAR(255) NOT NULL AFTER `cf_admin`,
                    ADD `cf_include_head` VARCHAR(255) NOT NULL AFTER `cf_include_index`,
                    ADD `cf_include_tail` VARCHAR(255) NOT NULL AFTER `cf_include_head`,
                    ADD `cf_add_script` TEXT NOT NULL AFTER `cf_include_tail` ", true);
}

if (!isset($config['cf_mobile_new_skin'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_mobile_new_skin` VARCHAR(255) NOT NULL AFTER `cf_memo_send_point`,
                    ADD `cf_mobile_search_skin` VARCHAR(255) NOT NULL AFTER `cf_mobile_new_skin`,
                    ADD `cf_mobile_connect_skin` VARCHAR(255) NOT NULL AFTER `cf_mobile_search_skin`,
                    ADD `cf_mobile_member_skin` VARCHAR(255) NOT NULL AFTER `cf_mobile_connect_skin` ", true);
}

if (isset($config['cf_gcaptcha_mp3'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    CHANGE `cf_gcaptcha_mp3` `cf_captcha_mp3` VARCHAR(255) NOT NULL DEFAULT '' ", true);
} else if (!isset($config['cf_captcha_mp3'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_captcha_mp3` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_mobile_member_skin` ", true);
}

if(!isset($config['cf_editor'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_editor` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_captcha_mp3` ", true);
}

if(!isset($config['cf_googl_shorturl_apikey'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_googl_shorturl_apikey` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_captcha_mp3` ", true);
}

if(!isset($config['cf_mobile_pages'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_mobile_pages` INT(11) NOT NULL DEFAULT '0' AFTER `cf_write_pages` ", true);
    sql_query(" UPDATE `{$g5['config_table']}` SET cf_mobile_pages = '5' ", true);
}

if(!isset($config['cf_facebook_appid'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_facebook_appid` VARCHAR(255) NOT NULL AFTER `cf_googl_shorturl_apikey`,
                    ADD `cf_facebook_secret` VARCHAR(255) NOT NULL AFTER `cf_facebook_appid`,
                    ADD `cf_twitter_key` VARCHAR(255) NOT NULL AFTER `cf_facebook_secret`,
                    ADD `cf_twitter_secret` VARCHAR(255) NOT NULL AFTER `cf_twitter_key` ", true);
}

// 缺少uniqid数据表时创建
if(!sql_query(" DESC {$g5['uniqid_table']} ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['uniqid_table']}` (
                  `uq_id` bigint(20) unsigned NOT NULL,
                  `uq_ip` varchar(255) NOT NULL,
                  PRIMARY KEY (`uq_id`)
                ) ", false);
}

if(!sql_query(" SELECT uq_ip from {$g5['uniqid_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE {$g5['uniqid_table']} ADD `uq_ip` VARCHAR(255) NOT NULL ");
}

// 缺少临时储存数据表时创建
if(!sql_query(" DESC {$g5['autosave_table']} ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['autosave_table']}` (
                  `as_id` int(11) NOT NULL AUTO_INCREMENT,
                  `mb_id` varchar(20) NOT NULL,
                  `as_uid` bigint(20) unsigned NOT NULL,
                  `as_subject` varchar(255) NOT NULL,
                  `as_content` text NOT NULL,
                  `as_datetime` datetime NOT NULL,
                  PRIMARY KEY (`as_id`),
                  UNIQUE KEY `as_uid` (`as_uid`),
                  KEY `mb_id` (`mb_id`)
                ) ", false);
}

if(!isset($config['cf_admin_email'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_admin_email` VARCHAR(255) NOT NULL AFTER `cf_admin` ", true);
}

if(!isset($config['cf_admin_email_name'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_admin_email_name` VARCHAR(255) NOT NULL AFTER `cf_admin_email` ", true);
}

if(!isset($config['cf_cert_use'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_cert_use` TINYINT(4) NOT NULL DEFAULT '0' AFTER `cf_editor`,
                    ADD `cf_cert_ipin` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_use`,
                    ADD `cf_cert_hp` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_ipin`,
                    ADD `cf_cert_kcb_cd` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_hp`,
                    ADD `cf_cert_kcp_cd` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cf_cert_kcb_cd`,
                    ADD `cf_cert_limit` INT(11) NOT NULL DEFAULT '0' AFTER `cf_cert_kcp_cd` ", true);
    sql_query(" ALTER TABLE `{$g5['member_table']}`
                    CHANGE `mb_hp_certify` `mb_certify` VARCHAR(20) NOT NULL DEFAULT '' ", true);
    sql_query(" update {$g5['member_table']} set mb_certify = 'hp' where mb_certify = '1' ");
    sql_query(" update {$g5['member_table']} set mb_certify = '' where mb_certify = '0' ");
    sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['cert_history_table']}` (
                  `cr_id` int(11) NOT NULL auto_increment,
                  `mb_id` varchar(255) NOT NULL DEFAULT '',
                  `cr_company` varchar(255) NOT NULL DEFAULT '',
                  `cr_method` varchar(255) NOT NULL DEFAULT '',
                  `cr_ip` varchar(255) NOT NULL DEFAULT '',
                  `cr_date` date NOT NULL DEFAULT '0000-00-00',
                  `cr_time` time NOT NULL DEFAULT '00:00:00',
                  PRIMARY KEY (`cr_id`),
                  KEY `mb_id` (`mb_id`)
                )", true);
}

if(!isset($config['cf_analytics'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_analytics` TEXT NOT NULL AFTER `cf_intercept_ip` ", true);
}

if(!isset($config['cf_add_meta'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_add_meta` TEXT NOT NULL AFTER `cf_analytics` ", true);
}

if (!isset($config['cf_syndi_token'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_syndi_token` VARCHAR(255) NOT NULL AFTER `cf_add_meta` ", true);
}

if (!isset($config['cf_syndi_except'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_syndi_except` TEXT NOT NULL AFTER `cf_syndi_token` ", true);
}

if(!isset($config['cf_sms_use'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_sms_use` varchar(255) NOT NULL DEFAULT '' AFTER `cf_cert_limit`,
                    ADD `cf_icode_id` varchar(255) NOT NULL DEFAULT '' AFTER `cf_sms_use`,
                    ADD `cf_icode_pw` varchar(255) NOT NULL DEFAULT '' AFTER `cf_icode_id`,
                    ADD `cf_icode_server_ip` varchar(255) NOT NULL DEFAULT '' AFTER `cf_icode_pw`,
                    ADD `cf_icode_server_port` varchar(255) NOT NULL DEFAULT '' AFTER `cf_icode_server_ip` ", true);
}

if(!isset($config['cf_mobile_page_rows'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_mobile_page_rows` int(11) NOT NULL DEFAULT '0' AFTER `cf_page_rows` ", true);
}

if(!isset($config['cf_cert_req'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_cert_req` tinyint(4) NOT NULL DEFAULT '0' AFTER `cf_cert_limit` ", true);
}

if(!isset($config['cf_faq_skin'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_faq_skin` varchar(255) NOT NULL DEFAULT '' AFTER `cf_connect_skin`,
                    ADD `cf_mobile_faq_skin` varchar(255) NOT NULL DEFAULT '' AFTER `cf_mobile_connect_skin` ", true);
}

// LG实名认证模块数据创建
if(!isset($config['cf_lg_mid'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_lg_mid` varchar(255) NOT NULL DEFAULT '' AFTER `cf_cert_kcp_cd`,
                    ADD `cf_lg_mert_key` varchar(255) NOT NULL DEFAULT '' AFTER `cf_lg_mid` ", true);
}

if(!isset($config['cf_optimize_date'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_optimize_date` date NOT NULL default '0000-00-00' AFTER `cf_popular_del` ", true);
}

// kakao talk api
if(!isset($config['cf_kakao_js_apikey'])) {
    sql_query(" ALTER TABLE `{$g5['config_table']}`
                    ADD `cf_kakao_js_apikey` varchar(255) NOT NULL DEFAULT '' AFTER `cf_googl_shorturl_apikey` ", true);
}

if(!$config['cf_faq_skin']) $config['cf_faq_skin'] = "basic";
if(!$config['cf_mobile_faq_skin']) $config['cf_mobile_faq_skin'] = "basic";

$g5['title'] = '基本设置';
include_once ('./admin.head.php');

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_cf_basic">基本环境</a></li>
    <li><a href="#anc_cf_board">论坛默认值</a></li>
    <li><a href="#anc_cf_join">注册会员</a></li>
    <li><a href="#anc_cf_cert">实名认证</a></li>
    <li><a href="#anc_cf_mail">邮件设置</a></li>
    <li><a href="#anc_cf_article_mail">新主题通知</a></li>
    <li><a href="#anc_cf_join_mail">注册邮件</a></li>
    <li><a href="#anc_cf_vote_mail">投票邮件</a></li>
    <li><a href="#anc_cf_sns">SNS</a></li>
    <li><a href="#anc_cf_lay">布局样式设置</a></li>
    <li><a href="#anc_cf_sms">SMS</a></li>
    <li><a href="#anc_cf_extra">扩展数据</a></li>
</ul>';

$frm_submit = '<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="确定" class="btn_submit" accesskey="s">
    <a href="'.G5_URL.'/">进入主页</a>
</div>';

if (!$config['cf_icode_server_ip'])   $config['cf_icode_server_ip'] = '211.172.232.124';
if (!$config['cf_icode_server_port']) $config['cf_icode_server_port'] = '7295';

if ($config['cf_icode_id'] && $config['cf_icode_pw']) {
    $userinfo = get_icode_userinfo($config['cf_icode_id'], $config['cf_icode_pw']);
}
?>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="<?php echo $token ?>" id="token">

<section id="anc_cf_basic">
    <h2 class="h2_frm">网站基本设置</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>网站基本设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_title">网站名称<strong class="sound_only">必选项</strong></label></th>
            <td colspan="3"><input type="text" name="cf_title" value="<?php echo $config['cf_title'] ?>" id="cf_title" required class="required frm_input" size="40"></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_admin">网站管理员<strong class="sound_only">必选项</strong></label></th>
            <td colspan="3"><?php echo get_member_id_select('cf_admin', 10, $config['cf_admin'], 'required') ?></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_admin_email">管理员邮件地址<strong class="sound_only">必选项</strong></label></th>
            <td colspan="3">
                <?php echo help('用于接收、发送网站相关邮件时显示的发件人邮件地址(注册会员、邮件认证、测试邮件、群发邮件用途)') ?>
                <input type="text" name="cf_admin_email" value="<?php echo $config['cf_admin_email'] ?>" id="cf_admin_email" required class="required email frm_input" size="40">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_admin_email_name">邮件发件人名称<strong class="sound_only">必选项</strong></label></th>
            <td colspan="3">
                <?php echo help('用于接收、发送网站相关邮件时显示的发件人名称 (注册会员、邮件认证、测试邮件、群发邮件用途)') ?>
                <input type="text" name="cf_admin_email_name" value="<?php echo $config['cf_admin_email_name'] ?>" id="cf_admin_email_name" required class="required frm_input" size="40">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_use_point">积分功能</label></th>
            <td colspan="3"><input type="checkbox" name="cf_use_point" value="1" id="cf_use_point" <?php echo $config['cf_use_point']?'checked':''; ?>> 使用</td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_login_point">签到积分<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('会员每日首次登录将发放签到积分') ?>
                <input type="text" name="cf_login_point" value="<?php echo $config['cf_login_point'] ?>" id="cf_login_point" required class="required frm_input" size="5">分
            </td>
            <th scope="row"><label for="cf_memo_send_point">站内短息消耗积分<strong class="sound_only">必选项</strong></label></th>
            <td>
                 <?php echo help('请设置发送站内短信所需积分，设置为0时不扣除积分') ?>
                <input type="text" name="cf_memo_send_point" value="<?php echo $config['cf_memo_send_point'] ?>" id="cf_memo_send_point" required class="required frm_input" size="5">分
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_cut_name">昵称显示</label></th>
            <td colspan="3">
                仅显示<input type="text" name="cf_cut_name" value="<?php echo $config['cf_cut_name'] ?>" id="cf_cut_name" class="frm_input" size="5">位
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_nick_modify">昵称修改设置</label></th>
            <td>修改之后<input type="text" name="cf_nick_modify" value="<?php echo $config['cf_nick_modify'] ?>" id="cf_nick_modify" class="frm_input" size="3">天以内禁止再次修改</td>
            <th scope="row"><label for="cf_open_modify">信息公开设置编辑</label></th>
            <td>修改之后<input type="text" name="cf_open_modify" value="<?php echo $config['cf_open_modify'] ?>" id="cf_open_modify" class="frm_input" size="3">天以内禁止再次修改</td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_new_del">删除最新文章</label></th>
            <td>
                <?php echo help('自动删除达到指定日期的最新文章表示，并非删除文章内容') ?>
                <input type="text" name="cf_new_del" value="<?php echo $config['cf_new_del'] ?>" id="cf_new_del" class="frm_input" size="5"> 日
            </td>
            <th scope="row"><label for="cf_memo_del">删除站内信</label></th>
            <td>
                <?php echo help('删除达到指定日期的站内短信') ?>
                <input type="text" name="cf_memo_del" value="<?php echo $config['cf_memo_del'] ?>" id="cf_memo_del" class="frm_input" size="5"> 日
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_visit_del">删除访问日志</label></th>
            <td>
                <?php echo help('自动删除达到指定日期的访问日志') ?>
                <input type="text" name="cf_visit_del" value="<?php echo $config['cf_visit_del'] ?>" id="cf_visit_del" class="frm_input" size="5"> 日
            </td>
            <th scope="row"><label for="cf_popular_del">删除热门关键词</label></th>
            <td>
                <?php echo help('自动删除达到指定日期的热门关键词数据') ?>
                <input type="text" name="cf_popular_del" value="<?php echo $config['cf_popular_del'] ?>" id="cf_popular_del" class="frm_input" size="5"> 日
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_login_minutes">在线人数</label></th>
            <td>
                <?php echo help('设定值以内时间的访问人数均认定在线人数') ?>
                <input type="text" name="cf_login_minutes" value="<?php echo $config['cf_login_minutes'] ?>" id="cf_login_minutes" class="frm_input" size="3"> 分钟
            </td>
            <th scope="row"><label for="cf_new_rows">最新文章显示数量</label></th>
            <td>
                <?php echo help('每页显示数量') ?>
                <input type="text" name="cf_new_rows" value="<?php echo $config['cf_new_rows'] ?>" id="cf_new_rows" class="frm_input" size="3"> 行
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_page_rows">每页显示行数</label></th>
            <td>
                <?php echo help('目录(列表)页面每页显示行数') ?>
                <input type="text" name="cf_page_rows" value="<?php echo $config['cf_page_rows'] ?>" id="cf_page_rows" class="frm_input" size="3"> 行
            </td>
            <th scope="row"><label for="cf_mobile_page_rows">触屏板 每页显示行数</label></th>
            <td>
                <?php echo help('触屏版每页显示行数') ?>
                <input type="text" name="cf_mobile_page_rows" value="<?php echo $config['cf_mobile_page_rows'] ?>" id="cf_mobile_page_rows" class="frm_input" size="3"> 行
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_write_pages">页码显示数量<strong class="sound_only">必选项</strong></label></th>
            <td>显示 <input type="text" name="cf_write_pages" value="<?php echo $config['cf_write_pages'] ?>" id="cf_write_pages" required class="required numeric frm_input" size="3"> 页</td>
            <th scope="row"><label for="cf_mobile_pages">触屏版显示数量<strong class="sound_only">必选项</strong></label></th>
            <td>显示<input type="text" name="cf_mobile_pages" value="<?php echo $config['cf_mobile_pages'] ?>" id="cf_mobile_pages" required class="required numeric frm_input" size="3">页</td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_new_skin">最新文章皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_new_skin" id="cf_new_skin" required class="required">
                <?php
                $arr = get_skin_dir('new');
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_new_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
            <th scope="row"><label for="cf_mobile_new_skin">触屏版<br>最新文章皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_mobile_new_skin" id="cf_mobile_new_skin" required class="required">
                <?php
                $arr = get_skin_dir('new', G5_MOBILE_PATH.'/'.G5_SKIN_DIR);
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_mobile_new_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_search_skin">搜索皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_search_skin" id="cf_search_skin" required class="required">
                <?php
                $arr = get_skin_dir('search');
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_search_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
            <th scope="row"><label for="cf_mobile_search_skin">触屏版搜索皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_mobile_search_skin" id="cf_mobile_search_skin" required class="required">
                <?php
                $arr = get_skin_dir('search', G5_MOBILE_PATH.'/'.G5_SKIN_DIR);
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_mobile_search_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_connect_skin">在线人数皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_connect_skin" id="cf_connect_skin" required class="required">
                <?php
                $arr = get_skin_dir('connect');
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_connect_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
            <th scope="row"><label for="cf_mobile_connect_skin">触屏版在线人数皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_mobile_connect_skin" id="cf_mobile_connect_skin" required class="required">
                <?php
                $arr = get_skin_dir('connect', G5_MOBILE_PATH.'/'.G5_SKIN_DIR);
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_mobile_connect_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_faq_skin">常见问题皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_faq_skin" id="cf_faq_skin" required class="required">
                <?php
                $arr = get_skin_dir('faq');
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_faq_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
            <th scope="row"><label for="cf_mobile_faq_skin">触屏版常见问题皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_mobile_faq_skin" id="cf_mobile_faq_skin" required class="required">
                <?php
                $arr = get_skin_dir('faq', G5_MOBILE_PATH.'/'.G5_SKIN_DIR);
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_mobile_faq_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_editor">选择编辑器</label></th>
            <td colspan="3">
                <?php echo help(G5_EDITOR_URL.' 选择此目录下的DTHML编辑器') ?>
                <select name="cf_editor" id="cf_editor">
                <?php
                $arr = get_skin_dir('', G5_EDITOR_PATH);
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">不使用</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_editor'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_captcha_mp3">选择语音验证码<strong class="sound_only">必选项</strong></label></th>
            <td colspan="3">
                <?php echo help(G5_CAPTCHA_URL.'/mp3 选择此目录下的语音验证码') ?>
                <select name="cf_captcha_mp3" id="cf_captcha_mp3" required class="required">
                <?php
                $arr = get_skin_dir('mp3', G5_CAPTCHA_PATH);
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_captcha_mp3'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_use_copy_log">移动、复制时保留日志</label></th>
            <td colspan="3">
                <?php echo help('在主题内容中显示操作信息，如谁进行的复制或移动操作') ?>
                <input type="checkbox" name="cf_use_copy_log" value="1" id="cf_use_copy_log" <?php echo $config['cf_use_copy_log']?'checked':''; ?>> 开启
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_point_term">积分有效期</label></th>
            <td colspan="3">
                <?php echo help('设置为0时不设置积分有效期，积分长期有效。') ?>
                <input type="text" name="cf_point_term" value="<?php echo $config['cf_point_term']; ?>" id="cf_point_term" required class="required frm_input" size="5"> 日
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_possible_ip">允许访问IP地址</label></th>
            <td>
                <?php echo help('只有设置的ip地址才能访问网站<br>可以指定ip段落，如123.123.+,其中+为泛地址，多个地址使用回车键分隔') ?>
                <textarea name="cf_possible_ip" id="cf_possible_ip"><?php echo $config['cf_possible_ip'] ?></textarea>
            </td>
            <th scope="row"><label for="cf_intercept_ip">禁止访问IP地址 </label></th>
            <td>
                <?php echo help('输入的IP地址将禁止访问网站<br>可以指定ip段落，如123.123.+,其中+为泛地址，多个地址使用回车键分隔') ?>
                <textarea name="cf_intercept_ip" id="cf_intercept_ip"><?php echo $config['cf_intercept_ip'] ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_analytics">访问统计代码</label></th>
            <td colspan="3">
                <?php echo help('使用第三方统计时请将统计代码复制到这里'); ?>
                <textarea name="cf_analytics" id="cf_analytics"><?php echo $config['cf_analytics']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_add_meta">超链接方式meta</label></th>
            <td colspan="3">
                <?php echo help('请根据需要添加meta标签以便对搜索引擎进行优化'); ?>
                <textarea name="cf_add_meta" id="cf_add_meta"><?php echo $config['cf_add_meta']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_syndi_token">Naver Syndi api key设置</label></th>
            <td colspan="3">
                <?php if (!function_exists('curl_init')) echo help('<b>注意！由于当前服务器不支持Curl组建，此功能无法使用</b>'); ?>
                <?php echo help('请在此设置Naver syndi API key ,设置完成后可以使用Naver开发平台服务。<br>申请地址<a href="http://webmastertool.naver.com/" target="_blank"><u>naver webmaster tools</u></a> ->栏目中进行申请') ?>
                <input type="text" name="cf_syndi_token" value="<?php echo $config['cf_syndi_token'] ?>" id="cf_syndi_token" class="frm_input" size="70">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_syndi_except">naver Syndi排除版块</label></th>
            <td colspan="3">
                <?php echo help('请设置不希望应用naver Syndi服务的论坛版块，多个版块请使用(|)进行分割，如notice|adult<br>同时需要注意的时当浏览权限大于2或设定了群组访问权限的版块将不能使用naver syndi服务') ?>
                <input type="text" name="cf_syndi_except" value="<?php echo $config['cf_syndi_except'] ?>" id="cf_syndi_except" class="frm_input" size="70">
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_board">
    <h2 class="h2_frm">论坛基本设置</h2>
    <?php echo $pg_anchor ?>
    <div class="local_desc02 local_desc">
        <p>论坛版块可以进行独立设置</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>论坛基本设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_delay_sec">发帖间隔时间<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="cf_delay_sec" value="<?php echo $config['cf_delay_sec'] ?>" id="cf_delay_sec" required class="required numeric frm_input" size="3">秒为间隔时间</td>
            <th scope="row"><label for="cf_link_target">超链接方式</label></th>
            <td>
                <?php echo help('设定文章中有超链接内容时超链接属性') ?>
                <select name="cf_link_target" id="cf_link_target">
                    <option value="_blank"<?php echo get_selected($config['cf_link_target'], '_blank') ?>>_blank</option>
                    <option value="_self"<?php echo get_selected($config['cf_link_target'], '_self') ?>>_self</option>
                    <option value="_top"<?php echo get_selected($config['cf_link_target'], '_top') ?>>_top</option>
                    <option value="_new"<?php echo get_selected($config['cf_link_target'], '_new') ?>>_new</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_read_point">浏览积分<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="cf_read_point" value="<?php echo $config['cf_read_point'] ?>" id="cf_read_point" required class="required frm_input" size="3">分</td>
            <th scope="row"><label for="cf_write_point">发表积分</label></th>
            <td><input type="text" name="cf_write_point" value="<?php echo $config['cf_write_point'] ?>" id="cf_write_point" required class="required frm_input" size="3">分</td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_comment_point">评论积分</label></th>
            <td><input type="text" name="cf_comment_point" value="<?php echo $config['cf_comment_point'] ?>" id="cf_comment_point" required class="required frm_input" size="3">分</td>
            <th scope="row"><label for="cf_download_point">下载积分</label></th>
            <td><input type="text" name="cf_download_point" value="<?php echo $config['cf_download_point'] ?>" id="cf_download_point" required class="required frm_input" size="3">分</td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_search_part">搜索段落</label></th>
            <td colspan="3"><input type="text" name="cf_search_part" value="<?php echo $config['cf_search_part'] ?>" id="cf_search_part" class="frm_input" size="4">条数据为段落搜索</td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_image_extension">允许上传图片文件</label></th>
            <td colspan="3">
                <?php echo help('请设置论坛中允许上传的图片文件扩展名，多个扩展名请使用(|)进行分割') ?>
                <input type="text" name="cf_image_extension" value="<?php echo $config['cf_image_extension'] ?>" id="cf_image_extension" class="frm_input" size="70">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_flash_extension">允许上传flash文件</label></th>
            <td colspan="3">
                <?php echo help('请设置论坛中允许上传的flash文件扩展名，多个扩展名请使用(|)进行分割') ?>
                <input type="text" name="cf_flash_extension" value="<?php echo $config['cf_flash_extension'] ?>" id="cf_flash_extension" class="frm_input" size="70">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_movie_extension">允许上传影音文件</label></th>
            <td colspan="3">
                <?php echo help('请设置论坛中允许上传的影音文件扩展名，多个扩展名请使用(|)进行分割') ?>
                <input type="text" name="cf_movie_extension" value="<?php echo $config['cf_movie_extension'] ?>" id="cf_movie_extension" class="frm_input" size="70">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_filter">禁用词组</label></th>
            <td colspan="3">
                <?php echo help('请设置发布主题时禁止使用的词组，多个词组请使用(,)进行分割') ?>
                <textarea name="cf_filter" id="cf_filter" rows="7"><?php echo $config['cf_filter'] ?></textarea>
             </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_join">
    <h2 class="h2_frm">会员注册设置</h2>
    <?php echo $pg_anchor ?>
    <div class="local_desc02 local_desc">
        <p>设置会员注册时皮肤样式及会员信息输入格式</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>会员注册设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_member_skin">会员皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_member_skin" id="cf_member_skin" required class="required">
                <?php
                $arr = get_skin_dir('member');
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo '<option value="'.$arr[$i].'"'.get_selected($config['cf_member_skin'], $arr[$i]).'>'.$arr[$i].'</option>'."\n";
                }
                ?>
                </select>
            </td>
            <th scope="row"><label for="cf_mobile_member_skin">触屏版<br>会员皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="cf_mobile_member_skin" id="cf_mobile_member_skin" required class="required">
                <?php
                $arr = get_skin_dir('member', G5_MOBILE_PATH.'/'.G5_SKIN_DIR);
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">选择</option>";
                    echo '<option value="'.$arr[$i].'"'.get_selected($config['cf_mobile_member_skin'], $arr[$i]).'>'.$arr[$i].'</option>'."\n";
                }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">网站主页新建</th>
            <td>
                <input type="checkbox" name="cf_use_homepage" value="1" id="cf_use_homepage" <?php echo $config['cf_use_homepage']?'checked':''; ?>> <label for="cf_use_homepage">显示</label>
                <input type="checkbox" name="cf_req_homepage" value="1" id="cf_req_homepage" <?php echo $config['cf_req_homepage']?'checked':''; ?>> <label for="cf_req_homepage">必选输入项</label>
            </td>
            <th scope="row">地址</th>
            <td>
                <input type="checkbox" name="cf_use_addr" value="1" id="cf_use_addr" <?php echo $config['cf_use_addr']?'checked':''; ?>> <label for="cf_use_addr">显示</label>
                <input type="checkbox" name="cf_req_addr" value="1" id="cf_req_addr" <?php echo $config['cf_req_addr']?'checked':''; ?>> <label for="cf_req_addr">必选输入项</label>
            </td>
        </tr>
        <tr>
            <th scope="row">电话号码</th>
            <td>
                <input type="checkbox" name="cf_use_tel" value="1" id="cf_use_tel" <?php echo $config['cf_use_tel']?'checked':''; ?>> <label for="cf_use_tel">显示</label>
                <input type="checkbox" name="cf_req_tel" value="1" id="cf_req_tel" <?php echo $config['cf_req_tel']?'checked':''; ?>> <label for="cf_req_tel">必选输入项</label>
            </td>
            <th scope="row">手机号码</th>
            <td>
                <input type="checkbox" name="cf_use_hp" value="1" id="cf_use_hp" <?php echo $config['cf_use_hp']?'checked':''; ?>> <label for="cf_use_hp">显示</label>
                <input type="checkbox" name="cf_req_hp" value="1" id="cf_req_hp" <?php echo $config['cf_req_hp']?'checked':''; ?>> <label for="cf_req_hp">必选输入项</label>
            </td>
        </tr>
        <tr>
            <th scope="row">签名设置</th>
            <td>
                <input type="checkbox" name="cf_use_signature" value="1" id="cf_use_signature" <?php echo $config['cf_use_signature']?'checked':''; ?>> <label for="cf_use_signature">显示</label>
                <input type="checkbox" name="cf_req_signature" value="1" id="cf_req_signature" <?php echo $config['cf_req_signature']?'checked':''; ?>> <label for="cf_req_signature">必选输入项</label>
            </td>
            <th scope="row">自我介绍</th>
            <td>
                <input type="checkbox" name="cf_use_profile" value="1" id="cf_use_profile" <?php echo $config['cf_use_profile']?'checked':''; ?>> <label for="cf_use_profile">显示</label>
                <input type="checkbox" name="cf_req_profile" value="1" id="cf_req_profile" <?php echo $config['cf_req_profile']?'checked':''; ?>> <label for="cf_req_profile">必选输入项</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_register_level">新会员权限等级</label></th>
            <td><?php echo get_member_level_select('cf_register_level', 1, 9, $config['cf_register_level']) ?></td>
            <th scope="row"><label for="cf_register_point">新会员注册积分</label></th>
            <td><input type="text" name="cf_register_point" value="<?php echo $config['cf_register_point'] ?>" id="cf_register_point" class="frm_input" size="5">分</td>
        </tr>
        <tr>
            <th scope="row" id="th310"><label for="cf_leave_day">会员信息删除时间</label></th>
            <td colspan="3"><input type="text" name="cf_leave_day" value="<?php echo $config['cf_leave_day'] ?>" id="cf_leave_day" class="frm_input" size="2">日后自动删除</td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_use_member_icon">会员头像功能</label></th>
            <td>
                <?php echo help('是否使用会员头像功能，用于发布主题或评论时展示') ?>
                <select name="cf_use_member_icon" id="cf_use_member_icon">
                    <option value="0"<?php echo get_selected($config['cf_use_member_icon'], '0') ?>>不使用
                    <option value="1"<?php echo get_selected($config['cf_use_member_icon'], '1') ?>>仅显示头像图标
                    <option value="2"<?php echo get_selected($config['cf_use_member_icon'], '2') ?>>显示图标与名称
                </select>
            </td>
            <th scope="row"><label for="cf_icon_level">上传图标权限</label></th>
            <td><?php echo get_member_level_select('cf_icon_level', 1, 9, $config['cf_icon_level']) ?>以上</td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_member_icon_size">会员头像图标大小</label></th>
            <td><input type="text" name="cf_member_icon_size" value="<?php echo $config['cf_member_icon_size'] ?>" id="cf_member_icon_size" class="frm_input" size="10">字节以内</td>
            <th scope="row">会员头像图标尺寸</th>
            <td>
                <label for="cf_member_icon_width">宽</label>
                <input type="text" name="cf_member_icon_width" value="<?php echo $config['cf_member_icon_width'] ?>" id="cf_member_icon_width" class="frm_input" size="2">
                <label for="cf_member_icon_height">高</label>
                <input type="text" name="cf_member_icon_height" value="<?php echo $config['cf_member_icon_height'] ?>" id="cf_member_icon_height" class="frm_input" size="2">
                像素以内
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_use_recommend">推荐人功能</label></th>
            <td><input type="checkbox" name="cf_use_recommend" value="1" id="cf_use_recommend" <?php echo $config['cf_use_recommend']?'checked':''; ?>> 使用</td>
            <th scope="row"><label for="cf_recommend_point">推荐人积分</label></th>
            <td><input type="text" name="cf_recommend_point" value="<?php echo $config['cf_recommend_point'] ?>" id="cf_recommend_point" class="frm_input">分</td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_prohibit_id">会员ID,昵称禁用词</label></th>
            <td>
                <?php echo help('设置会员id，昵称禁用词，多个禁用词请使用(,)分隔') ?>
                <textarea name="cf_prohibit_id" id="cf_prohibit_id" rows="5"><?php echo $config['cf_prohibit_id'] ?></textarea>
            </td>
            <th scope="row"><label for="cf_prohibit_email">禁用邮箱</label></th>
            <td>
                <?php echo help('设置禁止使用的邮箱，多个邮箱请使用回车分隔，如:hotmail.com') ?>
                <textarea name="cf_prohibit_email" id="cf_prohibit_email" rows="5"><?php echo $config['cf_prohibit_email'] ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_stipulation">会员注册条款</label></th>
            <td colspan="3"><textarea name="cf_stipulation" id="cf_stipulation" rows="10"><?php echo $config['cf_stipulation'] ?></textarea></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_privacy">个人隐私保护条例</label></th>
            <td colspan="3"><textarea id="cf_privacy" name="cf_privacy" rows="10"><?php echo $config['cf_privacy'] ?></textarea></td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_cert">
    <h2 class="h2_frm">实名认证设置</h2>
    <?php echo $pg_anchor ?>
    <div class="local_desc02 local_desc">
        <p>
            请设置会员注册时使用的实名认证方式<br>
            可以使用手机及第三方验证系统进行实名、成人认证<br>
            论坛版块可以根据实名、成人认证验证状态设置不同权限等级
        </p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>实名认证设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_cert_use">实名认证</label></th>
            <td>
                <select name="cf_cert_use" id="cf_cert_use">
                    <?php echo option_selected("0", $config['cf_cert_use'], "不使用"); ?>
                    <?php echo option_selected("1", $config['cf_cert_use'], "测试邮件"); ?>
                    <?php echo option_selected("2", $config['cf_cert_use'], "正式启动"); ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row" class="cf_cert_service"><label for="cf_cert_ipin">实名认证</label></th>
            <td class="cf_cert_service">
                <select name="cf_cert_ipin" id="cf_cert_ipin">
                    <?php echo option_selected("",    $config['cf_cert_ipin'], "不使用"); ?>
                    <?php echo option_selected("kcb", $config['cf_cert_ipin'], "KCP IPIN认证"); ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row" class="cf_cert_service"><label for="cf_cert_hp">手机认证</label></th>
            <td class="cf_cert_service">
                <select name="cf_cert_hp" id="cf_cert_hp">
                    <?php echo option_selected("",    $config['cf_cert_hp'], "不使用"); ?>
                    <?php echo option_selected("kcb", $config['cf_cert_hp'], "KCB手机认证"); ?>
                    <?php echo option_selected("kcp", $config['cf_cert_hp'], "KCP手机认证"); ?>
                    <?php echo option_selected("lg",  $config['cf_cert_hp'], "LG U plus手机认证"); ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row" class="cf_cert_service"><label for="cf_cert_kcb_cd">KCP认证<br>用户名</label></th>
            <td class="cf_cert_service">
                <?php echo help('KCB是韩国知名认证企业，可以为韩国籍、在韩外国人（外国人登录证）进行实名认证服务。<br>如需此服务需要与KCB签订协议使用') ?>
                <input type="text" name="cf_cert_kcb_cd" value="<?php echo $config['cf_cert_kcb_cd'] ?>" id="cf_cert_kcb_cd" class="frm_input" size="20"> <a href="http://sir.co.kr/main/provider/b_ipin.php" target="_blank" class="btn_frmline">申请KCP认证服务</a>
                <a href="http://sir.co.kr/main/provider/b_cert.php" target="_blank" class="btn_frmline">申请KCB手机认证服务</a>
            </td>
        </tr>
        <tr>
            <th scope="row" class="cf_cert_service"><label for="cf_cert_kcp_cd">KCP验证服务<br>网站接入代码</label></th>
            <td class="cf_cert_service">
                <?php echo help('请输入已Sm开头的五位接入数字中的最后三位数字。<br>如需此服务需要与KCP签订协议使用') ?>
                <span class="sitecode">SM</span>
                <input type="text" name="cf_cert_kcp_cd" value="<?php echo $config['cf_cert_kcp_cd'] ?>" id="cf_cert_kcp_cd" class="frm_input" size="3"> <a href="http://sir.co.kr/main/provider/p_cert.php" target="_blank" class="btn_frmline">申请KCP手机认证</a>
            </td>
        </tr>
        <tr>
            <th scope="row" class="cf_cert_service"><label for="cf_lg_mid">LG U PLUS用户名</label></th>
            <td class="cf_cert_service">
                <?php echo help('请输入LG U PLUS用户名，无需输入用户名开头代码si_<br>如需此服务需要与LG U签订协议使用<br><strong>LG U plus认证需要安装 ActiveX插件，所以只能支持IE浏览器使用者进行验证</strong>') ?>
                <span class="sitecode">si_</span>
                <input type="text" name="cf_lg_mid" value="<?php echo $config['cf_lg_mid'] ?>" id="cf_lg_mid" class="frm_input" size="20"> <a href="http://sir.co.kr/main/provider/lg_cert.php" target="_blank" class="btn_frmline">申请LG实名认证服务</a>
            </td>
        </tr>
        <tr>
            <th scope="row" class="cf_cert_service"><label for="cf_lg_mert_key">LG MERT KEY</label></th>
            <td class="cf_cert_service">
                <?php echo help('请登录LG U PLUS会员中心获取您的 MERT KEY') ?>
                <input type="text" name="cf_lg_mert_key" value="<?php echo $config['cf_lg_mert_key'] ?>" id="cf_lg_mert_key" class="frm_input" size="40">
            </td>
        </tr>
        <tr>
            <th scope="row" class="cf_cert_service"><label for="cf_cert_limit">实名认证使用限制</label></th>
            <td class="cf_cert_service">
                <?php echo help('设置每天每个电话号码或ipn账号可以尝试验证的次数限制<br>为了防止滥用产生过多验证费用，请设置合理限制<br>设置为0时不限制验证次数'); ?>
                <input type="text" name="cf_cert_limit" value="<?php echo $config['cf_cert_limit']; ?>" id="cf_cert_limit" class="frm_input" size="3"> 次
            </td>
        </tr>
        <tr>
            <th scope="row" class="cf_cert_service"><label for="cf_cert_req">开启实名认证</label></th>
            <td class="cf_cert_service">
                <?php echo help('是否注册会员时要求会员强制进行实名认证？否则禁止注册？'); ?>
                <input type="checkbox" name="cf_cert_req" value="1" id="cf_cert_req"<?php echo get_checked($config['cf_cert_req'], 1); ?>> 是
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_mail">
    <h2 class="h2_frm">邮件设置</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>邮件设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_email_use">邮件通知功能</label></th>
            <td>
                <?php echo help('如不开启此功能将禁止网站内所有邮件应用使用，包括测试邮件功能也禁止使用') ?>
                <input type="checkbox" name="cf_email_use" value="1" id="cf_email_use" <?php echo $config['cf_email_use']?'checked':''; ?>> 使用
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_use_email_certify">开启邮件地址验证</label></th>
            <td>
                <?php echo help('开启此功能后新注册会员必须完成邮件地址验证后才能完成注册'); ?>
                <input type="checkbox" name="cf_use_email_certify" value="1" id="cf_use_email_certify" <?php echo $config['cf_use_email_certify']?'checked':''; ?>> 使用
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_formmail_is_member">站内邮件交互</label></th>
            <td>
                <?php echo help('如不设置条件游客也可以使用邮件交互功能（回复邮件通知等）') ?>
                <input type="checkbox" name="cf_formmail_is_member" value="1" id="cf_formmail_is_member" <?php echo $config['cf_formmail_is_member']?'checked':''; ?>> 仅限会员使用
            </td>
        </tr>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_article_mail">
    <h2 class="h2_frm">新主题邮件通知</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>新主题邮件通知</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_email_wr_super_admin">网站管理员</label></th>
            <td>
                <?php echo help('邮件通知管理员') ?>
                <input type="checkbox" name="cf_email_wr_super_admin" value="1" id="cf_email_wr_super_admin" <?php echo $config['cf_email_wr_super_admin']?'checked':''; ?>> 使用
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_email_wr_group_admin">群组管理员</label></th>
            <td>
                <?php echo help('邮件通知群组管理员') ?>
                <input type="checkbox" name="cf_email_wr_group_admin" value="1" id="cf_email_wr_group_admin" <?php echo $config['cf_email_wr_group_admin']?'checked':''; ?>> 使用
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_email_wr_board_admin">版块管理员</label></th>
            <td>
                <?php echo help('邮件通知版块管理员') ?>
                <input type="checkbox" name="cf_email_wr_board_admin" value="1" id="cf_email_wr_board_admin" <?php echo $config['cf_email_wr_board_admin']?'checked':''; ?>> 使用
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_email_wr_write">主题作者</label></th>
            <td>
                <?php echo help('邮件通知作者') ?>
                <input type="checkbox" name="cf_email_wr_write" value="1" id="cf_email_wr_write" <?php echo $config['cf_email_wr_write']?'checked':''; ?>> 使用
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_email_wr_comment_all">评论作者</label></th>
            <td>
                <?php echo help('如主题中有新的回帖时会邮件通知所有已发表评论的会员') ?>
                <input type="checkbox" name="cf_email_wr_comment_all" value="1" id="cf_email_wr_comment_all" <?php echo $config['cf_email_wr_comment_all']?'checked':''; ?>> 使用
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_join_mail">
    <h2 class="h2_frm">会员注册邮件设置</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>会员注册邮件设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_email_mb_super_admin">网站管理员 发送邮件</label></th>
            <td>
                <?php echo help('邮件通知管理员') ?>
                <input type="checkbox" name="cf_email_mb_super_admin" value="1" id="cf_email_mb_super_admin" <?php echo $config['cf_email_mb_super_admin']?'checked':''; ?>> 使用
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_email_mb_member">注册欢迎邮件</label></th>
            <td>
                <?php echo help('向注册会员发送欢迎邮件') ?>
                <input type="checkbox" name="cf_email_mb_member" value="1" id="cf_email_mb_member" <?php echo $config['cf_email_mb_member']?'checked':''; ?>> 使用
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_vote_mail">
    <h2 class="h2_frm">投票、评论邮件通知设置</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>投票、评论邮件通知设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_email_po_super_admin">网站管理员 发送邮件</label></th>
            <td>
                <?php echo help('邮件通知管理员') ?>
                <input type="checkbox" name="cf_email_po_super_admin" value="1" id="cf_email_po_super_admin" <?php echo $config['cf_email_po_super_admin']?'checked':''; ?>> 使用
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_sns">
    <h2 class="h2_frm">分享服务设置(SNS : Social Network Service)</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>分享服务设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_facebook_appid">Facebook App id</label></th>
            <td>
                <input type="text" name="cf_facebook_appid" value="<?php echo $config['cf_facebook_appid'] ?>" id="cf_facebook_appid" class="frm_input"> <a href="https://developers.facebook.com/apps" target="_blank" class="btn_frmline">登记app</a>
            </td>
            <th scope="row"><label for="cf_facebook_secret">Facebook app Secret</label></th>
            <td>
                <input type="text" name="cf_facebook_secret" value="<?php echo $config['cf_facebook_secret'] ?>" id="cf_facebook_secret" class="frm_input" size="35">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_twitter_key">twitter推特 Key</label></th>
            <td>
                <input type="text" name="cf_twitter_key" value="<?php echo $config['cf_twitter_key'] ?>" id="cf_twitter_key" class="frm_input"> <a href="https://dev.twitter.com/apps" target="_blank" class="btn_frmline">登记app</a>
            </td>
            <th scope="row"><label for="cf_twitter_secret">twitter推特 Secret</label></th>
            <td>
                <input type="text" name="cf_twitter_secret" value="<?php echo $config['cf_twitter_secret'] ?>" id="cf_twitter_secret" class="frm_input" size="35">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_googl_shorturl_apikey">谷歌短地址 API Key</label></th>
            <td>
                <input type="text" name="cf_googl_shorturl_apikey" value="<?php echo $config['cf_googl_shorturl_apikey'] ?>" id="cf_googl_shorturl_apikey" class="frm_input"> <a href="http://code.google.com/apis/console/" target="_blank" class="btn_frmline">添加API key</a>
            </td>
            <th scope="row"><label for="cf_kakao_js_apikey">kakao talk Javascript API Key</label></th>
            <td>
                <input type="text" name="cf_kakao_js_apikey" value="<?php echo $config['cf_kakao_js_apikey'] ?>" id="cf_kakao_js_apikey" class="frm_input"> <a href="http://developers.kakao.com/" target="_blank" class="btn_frmline">登记app</a>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_lay">
    <h2 class="h2_frm">布局样式设置</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>可以通过布局样式调整展示效果，在这里可以设置javascript、css等代码</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>布局样式设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_include_index">默认首页文件</label></th>
            <td>
                <?php echo help('不进行设置时将默认使用index.php，<br>如您需要设置其他页面时应保证页面与当前index.php在相同路径') ?>
                <input type="text" name="cf_include_index" value="<?php echo $config['cf_include_index'] ?>" id="cf_include_index" class="frm_input" size="50">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_include_head">顶部文件路径</label></th>
            <td>
                <?php echo help('不进行设置时将默认使用head.php,<br>如您需要设置其他页面时应保证页面与当前head.php在相同路径') ?>
                <input type="text" name="cf_include_head" value="<?php echo $config['cf_include_head'] ?>" id="cf_include_head" class="frm_input" size="50">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_include_tail">底部文件路径</label></th>
            <td>
                <?php echo help('不进行设置时将默认使用tail.php,<br>如您需要设置其他页面时应保证页面与当前tail.php在相同路径') ?>
                <input type="text" name="cf_include_tail" value="<?php echo $config['cf_include_tail'] ?>" id="cf_include_tail" class="frm_input" size="50">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_add_script">附加script及css代码</label></th>
            <td>
                <?php echo help('请设置HTML的 &lt;/HEAD&gt; 标签中加入的 JavaScript与 css 代码<br>系统后台将不会加载此页面') ?>
                <textarea name="cf_add_script" id="cf_add_script"><?php echo get_text($config['cf_add_script']); ?></textarea>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_sms">
    <h2 class="h2_frm">SMS</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>手机短信设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_sms_use">手机短信</label></th>
            <td>
                <select id="cf_sms_use" name="cf_sms_use">
                    <option value="" <?php echo get_selected($config['cf_sms_use'], ''); ?>>不使用</option>
                    <option value="icode" <?php echo get_selected($config['cf_sms_use'], 'icode'); ?>>ICODE </option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_icode_id">ICODE用户名</label></th>
            <td>
                <?php echo help("请设置ICODE用户名"); ?>
                <input type="text" name="cf_icode_id" value="<?php echo $config['cf_icode_id']; ?>" id="cf_icode_id" class="frm_input" size="20">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_icode_pw">ICODE登录密码</label></th>
            <td>
                <?php echo help("请设置ICODE登录密码"); ?>
                <input type="password" name="cf_icode_pw" value="<?php echo $config['cf_icode_pw']; ?>" id="cf_icode_pw" class="frm_input">
            </td>
        </tr>
        <tr>
            <th scope="row">计费类型</th>
            <td>
                <input type="hidden" name="cf_icode_server_ip" value="<?php echo $config['cf_icode_server_ip']; ?>">
                <?php
                    if ($userinfo['payment'] == 'A') {
                       echo '充值预付';
                        echo '<input type="hidden" name="cf_icode_server_port" value="7295">';
                    } else if ($userinfo['payment'] == 'C') {
                        echo '包月定额';
                        echo '<input type="hidden" name="cf_icode_server_port" value="7296">';
                    } else {
                        echo '注册账户.';
                        echo '<input type="hidden" name="cf_icode_server_port" value="7295">';
                    }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">申请开通<br>ICODE会员</th>
            <td>
                <?php echo help("点击下方链接注册账户将会获得额外优惠"); ?>
                <a href="http://icodekorea.com/res/join_company_fix_a.php?sellid=sir2" target="_blank" class="btn_frmline">注册icode</a>
            </td>
        </tr>
         <?php if ($userinfo['payment'] == 'A') { ?>
        <tr>
            <th scope="row">账户余额</th>
            <td colspan="3">
                <?php echo number_format($userinfo['coin']); ?> 元
                <a href="http://www.icodekorea.com/smsbiz/credit_card_amt.php?icode_id=<?php echo $config['cf_icode_id']; ?>&amp;icode_passwd=<?php echo $config['cf_icode_pw']; ?>" target="_blank" class="btn_frmline" onclick="window.open(this.href,'icode_payment', 'scrollbars=1,resizable=1'); return false;">充值</a>
            </td>
        </tr>
        <tr>
            <th scope="row">每条价格</th>
            <td colspan="3">
                <?php echo number_format($userinfo['gpay']); ?> 元
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_extra">
    <h2 class="h2_frm">扩展数据表设置</h2>
    <?php echo $pg_anchor ?>
    <div class="local_desc02 local_desc">
        <p>论坛版块可以进行独立设置</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>扩展数据表设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <?php for ($i=1; $i<=10; $i++) { ?>
        <tr>
            <th scope="row">扩展数据<?php echo $i ?></th>
            <td class="td_extra">
                <label for="cf_<?php echo $i ?>_subj">扩展数据<?php echo $i ?> 主题</label>
                <input type="text" name="cf_<?php echo $i ?>_subj" value="<?php echo get_text($config['cf_'.$i.'_subj']) ?>" id="cf_<?php echo $i ?>_subj" class="frm_input" size="30">
                <label for="cf_<?php echo $i ?>">扩展数据<?php echo $i ?>参数</label>
                <input type="text" name="cf_<?php echo $i ?>" value="<?php echo $config['cf_'.$i] ?>" id="cf_<?php echo $i ?>" class="frm_input" size="30">
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

</form>

<script>
$(function(){
    <?php
    if(!$config['cf_cert_use'])
        echo '$(".cf_cert_service").addClass("cf_cert_hide");';
    ?>
    $("#cf_cert_use").change(function(){
        switch($(this).val()) {
            case "0":
                $(".cf_cert_service").addClass("cf_cert_hide");
                break;
            default:
                $(".cf_cert_service").removeClass("cf_cert_hide");
                break;
        }
    });
});

function fconfigform_submit(f)
{
    f.action = "./config_form_update.php";
    return true;
}
</script>

<?php
// 实名认证模块运行权限检查
if($config['cf_cert_use']) {
    // kcb
    if($config['cf_cert_ipin'] == 'kcb' || $config['cf_cert_hp'] == 'kcb') {
        // 执行模块
        if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            if(PHP_INT_MAX == 2147483647) // 32-bit
                $exe = G5_OKNAME_PATH.'/bin/okname';
            else
                $exe = G5_OKNAME_PATH.'/bin/okname_x64';
        } else {
            if(PHP_INT_MAX == 2147483647) // 32-bit
                $exe = G5_OKNAME_PATH.'/bin/okname.exe';
            else
                $exe = G5_OKNAME_PATH.'/bin/oknamex64.exe';
        }

        echo module_exec_check($exe, 'okname');
    }

    // kcp
    if($config['cf_cert_hp'] == 'kcp') {
        if(PHP_INT_MAX == 2147483647) // 32-bit
            $exe = G5_KCPCERT_PATH . '/bin/ct_cli';
        else
            $exe = G5_KCPCERT_PATH . '/bin/ct_cli_x64';

        echo module_exec_check($exe, 'ct_cli');
    }

    // lg检测 log日志
    if($config['cf_cert_hp'] == 'lg') {
        $log_path = G5_LGXPAY_PATH.'/lgdacom/log';

        if(!is_dir($log_path)) {
            echo '<script>'.PHP_EOL;
            echo 'alert("'.str_replace(G5_PATH.'/', '', G5_LGXPAY_PATH).'/lgdacom 请在此目录创建log文件夹后添加写入属性\n> mkdir log\n> chmod 707 log");'.PHP_EOL;
            echo '</script>'.PHP_EOL;
        } else {
            if(!is_writable($log_path)) {
                echo '<script>'.PHP_EOL;
                echo 'alert("'.str_replace(G5_PATH.'/', '',$log_path).' 请设置写入权限\n> chmod 707 log");'.PHP_EOL;
                echo '</script>'.PHP_EOL;
            }
        }
    }
}

include_once ('./admin.tail.php');
?>
