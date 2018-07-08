<?php
$menu['menu100'] = array (
    array('100000', '基本设置', G5_ADMIN_URL.'/config_form.php',   'config'),
    array('', '基本环境设置', G5_ADMIN_URL.'/config_form.php',   'cf_basic'),
    array('', '管理权限设置', G5_ADMIN_URL.'/auth_list.php',     'cf_auth'),
    array('', '栏目设置', G5_ADMIN_URL.'/menu_list.php',     'cf_menu', 1),
    array('100300', '邮件测试', G5_ADMIN_URL.'/sendmail_test.php', 'cf_mailtest'),
    array('100310', '弹窗管理', G5_ADMIN_URL.'/newwinlist.php', 'scf_poplayer'),
    array('100800', '批量删除session',G5_ADMIN_URL.'/session_file_delete.php', 'cf_session', 1),
    array('100900', '删除缓存文件',G5_ADMIN_URL.'/cache_file_delete.php',   'cf_cache', 1),
    array('100910', '删除验证码图片',G5_ADMIN_URL.'/captcha_file_delete.php',   'cf_captcha', 1),
    array('100920', '缩略图文件批量删除',G5_ADMIN_URL.'/thumbnail_file_delete.php',   'cf_thumbnail', 1),
    array('100500', 'phpinfo()',        G5_ADMIN_URL.'/phpinfo.php',       'cf_phpinfo'),
    array('100400', '附加服务', G5_ADMIN_URL.'/service.php', 'cf_service')
);
?>