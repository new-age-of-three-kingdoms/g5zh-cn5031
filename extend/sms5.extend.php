<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

//------------------------------------------------------------------------------
// SMS函数定义
//------------------------------------------------------------------------------

define('G5_SMS5_DIR',             'sms5');
define('G5_SMS5_PATH',            G5_PLUGIN_PATH.'/'.G5_SMS5_DIR);
define('G5_SMS5_URL',             G5_PLUGIN_URL.'/'.G5_SMS5_DIR);

define('G5_SMS5_ADMIN_DIR',        'sms_admin');
define('G5_SMS5_ADMIN_PATH',       G5_ADMIN_PATH.'/'.G5_SMS5_ADMIN_DIR);
define('G5_SMS5_ADMIN_URL',        G5_ADMIN_URL.'/'.G5_SMS5_ADMIN_DIR);

// SMS数据表名称
$g5['sms5_prefix']                = 'sms5_';
$g5['sms5_config_table']          = $g5['sms5_prefix'] . 'config';
$g5['sms5_write_table']           = $g5['sms5_prefix'] . 'write';
$g5['sms5_history_table']         = $g5['sms5_prefix'] . 'history';
$g5['sms5_book_table']            = $g5['sms5_prefix'] . 'book';
$g5['sms5_book_group_table']      = $g5['sms5_prefix'] . 'book_group';
$g5['sms5_form_table']            = $g5['sms5_prefix'] . 'form';
$g5['sms5_form_group_table']      = $g5['sms5_prefix'] . 'form_group';
$g5['sms5_member_history_table']  = $g5['sms5_prefix'] . 'member_history';

if (!empty($config['cf_sms_use'])) {

    $sms5 = sql_fetch("select * from {$g5['sms5_config_table']} ", false);
    if( $sms5['cf_member'] && trim($member['mb_hp']) ) {
        $g5['sms5_use_sideview'] = true; //添加至会员信息弹出层
    } else {
        $g5['sms5_use_sideview'] = false;
    }

    //==============================================================================
    //皮肤(skin)目录
    //------------------------------------------------------------------------------

    $sms5_skin_path = G5_SMS5_PATH.'/skin/'.$sms5['cf_skin']; //sms5皮肤 path
    $sms5_skin_url = G5_SMS5_URL .'/skin/'.$sms5['cf_skin']; //sms5皮肤 url

    // 演示（demo）设置
    if (file_exists(G5_PATH.'/DEMO'))
    {
        // 设置010-0000-0000为接收号码
        $g5['sms5_demo'] = true;

        // 虚拟发送短信数据执行虚拟random
        $g5['sms5_demo_send'] = true;
    }

    include_once(G5_LIB_PATH.'/icode.sms.lib.php');
    include_once(G5_SMS5_PATH.'/sms5.lib.php');

}
?>