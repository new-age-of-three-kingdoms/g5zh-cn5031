<?php
$menu['menu200'] = array (
    array('200000', '会员管理', G5_ADMIN_URL.'/member_list.php', 'member'),
    array('200100', '会员管理', G5_ADMIN_URL.'/member_list.php', 'mb_list'),
    array('200300', '群发会员邮件', G5_ADMIN_URL.'/mail_list.php', 'mb_mail'),
    array('200800', '访问人数统计', G5_ADMIN_URL.'/visit_list.php', 'mb_visit', 1),
    array('200810', '统计数据搜索', G5_ADMIN_URL.'/visit_search.php', 'mb_search', 1),
    array('200820', '删除访问数据', G5_ADMIN_URL.'/visit_delete.php', 'mb_delete', 1),
    array('200200', '积分管理', G5_ADMIN_URL.'/point_list.php', 'mb_point'),
    array('200900', '投票管理', G5_ADMIN_URL.'/poll_list.php', 'mb_poll')
);
?>