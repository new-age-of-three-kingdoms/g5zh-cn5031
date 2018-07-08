<?php
$menu["menu900"] = array (
    array('900000', '短信管理', ''.G5_SMS5_ADMIN_URL.'/config.php', 'sms5'),
    array('900100', '短信设置', ''.G5_SMS5_ADMIN_URL.'/config.php', 'sms5_config'),
    array('900200', '会员信息更新', ''.G5_SMS5_ADMIN_URL.'/member_update.php', 'sms5_mb_update'),
    array('900300', '发送手机短信', ''.G5_SMS5_ADMIN_URL.'/sms_write.php', 'sms_write'),
    array('900400', '发送记录-按条', ''.G5_SMS5_ADMIN_URL.'/history_list.php', 'sms_history' , 1),
    array('900410', '发送记录-按号码', ''.G5_SMS5_ADMIN_URL.'/history_num.php', 'sms_history_num' , 1),
    array('900450', '发送记录-按ID', ''.G5_SMS5_ADMIN_URL.'/history_member.php', 'sms_history_mb' , 1),
    array('900500', '表情分类', ''.G5_SMS5_ADMIN_URL.'/form_group.php' , 'emoticon_group'),
    array('900600', '表情管理', ''.G5_SMS5_ADMIN_URL.'/form_list.php', 'emoticon_list'),
    array('900700', '号码分类', ''.G5_SMS5_ADMIN_URL.'/num_group.php' , 'hp_group', 1),
    array('900800', '号码管理', ''.G5_SMS5_ADMIN_URL.'/num_book.php', 'hp_manage', 1),
    array('900900', '号码文件', ''.G5_SMS5_ADMIN_URL.'/num_book_file.php' , 'hp_file', 1)
);
?>