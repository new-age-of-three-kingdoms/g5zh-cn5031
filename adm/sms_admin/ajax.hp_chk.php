<?php
$sub_menu = "900800";
include_once("./_common.php");
@include_once(G5_PLUGIN_PATH."/sms5/JSON.php");

if( !function_exists('json_encode') ) {
    function json_encode($data) {
        $json = new Services_JSON();
        return( $json->encode($data) );
    }
}

ajax_auth_check($auth[$sub_menu], "r");

$err = '';
$arr_ajax_msg = array();
$exist_hplist = array();

if( !$bk_hp )
    $err = '请输入手机号码';

$bk_hp = get_hp($bk_hp);

$sql = " select count(*) as cnt from {$g5['sms5_book_table']} where bk_hp = '$bk_hp' ";
if($w == 'u' && $bk_no)
    $sql .= " and bk_no <> '$bk_no' ";
$row = sql_fetch($sql);

if($row['cnt'])
    $err = '已有相同号码';

// 编辑信息时检测会员信息是否重复
if(!$row['cnt'] && $w == 'u') {
    $sql = " select mb_id from {$g5['member_table']} where mb_hp = '{$bk_hp}' and mb_hp <> '' ";

    if( $mb_id )
        $sql .= " and mb_id <> '{$mb_id}' ";

    $result = sql_query($sql);

    while($row = sql_fetch_array($result)){
        $exist_hplist[] = $row['mb_id'];
    }
}

$arr_ajax_msg['error'] = $err;
$arr_ajax_msg['exist'] = $exist_hplist;

die( json_encode($arr_ajax_msg) );

?>