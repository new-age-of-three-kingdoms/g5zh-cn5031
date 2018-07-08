<?php
$sub_menu = "900100";
include_once("./_common.php");

auth_check($auth[$sub_menu], "w");

check_demo();

$g5['title'] = "短信设置";

$userinfo = get_icode_userinfo($cf_icode_id, $cf_icode_pw);

if ($userinfo['code'] == '202')
    alert('ICODE账户密码错误');

if ($cf_member == '1')
    $cf_member = 1;
else
    $cf_member = 0;

$res = sql_fetch("select * from ".$g5['sms5_config_table']." limit 1");

if (!$res)
    $sql = "insert into ";
else
    $sql = "update ";

$sql .= $g5['sms5_config_table']." set cf_phone='$cf_phone', cf_member='$cf_member', cf_level='$cf_level', cf_point='$cf_point', cf_day_count='$cf_day_count', cf_skin = '$cf_skin' ";

sql_query($sql);

// ICODE  设置
$sql = " update {$g5['config_table']}
            set cf_sms_use              = '$cf_sms_use',
                cf_icode_id             = '$cf_icode_id',
                cf_icode_pw             = '$cf_icode_pw',
                cf_icode_server_ip      = '$cf_icode_server_ip',
                cf_icode_server_port    = '$cf_icode_server_port' ";
sql_query($sql);

goto_url("./config.php");
?>