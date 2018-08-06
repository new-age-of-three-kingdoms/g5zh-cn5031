<?php
if (!defined('_GNUBOARD_')) exit;
define('G5_MYSQL_HOST', 'localhost');
define('G5_MYSQL_USER', 'fana2005');
define('G5_MYSQL_PASSWORD', 'fana2005@admin');
define('G5_MYSQL_DB', 'fana2005');
define('G5_MYSQL_SET_MODE', false);

define('G5_TABLE_PREFIX', 'g5_');

$g5['write_prefix'] = G5_TABLE_PREFIX.'write_'; // 论坛数据表格前缀

$g5['auth_table'] = G5_TABLE_PREFIX.'auth'; // 管理权限设置数据
$g5['config_table'] = G5_TABLE_PREFIX.'config'; // 基本设置数据
$g5['group_table'] = G5_TABLE_PREFIX.'group'; // 论坛群组设置数据
$g5['group_member_table'] = G5_TABLE_PREFIX.'group_member'; // 论坛群组会员数据
$g5['board_table'] = G5_TABLE_PREFIX.'board'; // 论坛设置数据
$g5['board_file_table'] = G5_TABLE_PREFIX.'board_file'; // 论坛附件数据
$g5['board_good_table'] = G5_TABLE_PREFIX.'board_good'; // 论坛推荐及反对数据
$g5['board_new_table'] = G5_TABLE_PREFIX.'board_new'; // 论坛新主题数据
$g5['login_table'] = G5_TABLE_PREFIX.'login'; // 登录数据(在线人数
$g5['mail_table'] = G5_TABLE_PREFIX.'mail'; // 会员邮件数据
$g5['member_table'] = G5_TABLE_PREFIX.'member'; // 会员数据
$g5['memo_table'] = G5_TABLE_PREFIX.'memo'; // 备注数据
$g5['poll_table'] = G5_TABLE_PREFIX.'poll'; // 投票数据
$g5['poll_etc_table'] = G5_TABLE_PREFIX.'poll_etc'; // 投票意见及评论数据
$g5['point_table'] = G5_TABLE_PREFIX.'point'; // 积分数据
$g5['popular_table'] = G5_TABLE_PREFIX.'popular'; // 热门关键词数据
$g5['scrap_table'] = G5_TABLE_PREFIX.'scrap'; // 主题收藏数据
$g5['visit_table'] = G5_TABLE_PREFIX.'visit'; // 访客数据
$g5['visit_sum_table'] = G5_TABLE_PREFIX.'visit_sum'; // 访客统计数据
$g5['uniqid_table'] = G5_TABLE_PREFIX.'uniqid'; // uniqid数据
$g5['autosave_table'] = G5_TABLE_PREFIX.'autosave'; // 草稿箱数据
$g5['cert_history_table'] = G5_TABLE_PREFIX.'cert_history'; // 认证数据
$g5['qa_config_table'] = G5_TABLE_PREFIX.'qa_config'; // 在线咨询设置数据
$g5['qa_content_table'] = G5_TABLE_PREFIX.'qa_content'; // 在线咨询数据
$g5['content_table'] = G5_TABLE_PREFIX.'content'; // 内容(项目)信息数据
$g5['faq_table'] = G5_TABLE_PREFIX.'faq'; // 常见问题数据
$g5['faq_master_table'] = G5_TABLE_PREFIX.'faq_master'; // 常见问题主数据
$g5['new_win_table'] = G5_TABLE_PREFIX.'new_win'; // 新窗口数据
$g5['menu_table'] = G5_TABLE_PREFIX.'menu'; // 菜单管理数据
?>