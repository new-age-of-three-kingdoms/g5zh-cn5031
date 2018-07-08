<?php
$menu['menu300'] = array (
    array('300000', '论坛管理', ''.G5_ADMIN_URL.'/board_list.php', 'board'),
    array('300100', '论坛管理', ''.G5_ADMIN_URL.'/board_list.php', 'bbs_board'),
    array('300200', '论坛群组管理', ''.G5_ADMIN_URL.'/boardgroup_list.php', 'bbs_group'),
    array('300300', '热门关键词管理', ''.G5_ADMIN_URL.'/popular_list.php', 'bbs_poplist', 1),
    array('300400', '热门关键词排序', ''.G5_ADMIN_URL.'/popular_rank.php', 'bbs_poprank', 1),
    array('300500', '在线咨询设置', ''.G5_ADMIN_URL.'/qa_config.php', 'qa'),
    array('300600', '内容管理', G5_ADMIN_URL.'/contentlist.php', 'scf_contents', 1),
    array('300700', 'FAQ管理', G5_ADMIN_URL.'/faqmasterlist.php', 'scf_faq', 1),
);
?>