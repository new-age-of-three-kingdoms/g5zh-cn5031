<?php
$sub_menu = "200820";
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'd');

if ($is_admin != 'super')
    alert('您没有访问权限');

$year = preg_replace('/[^0-9]/', '', $_POST['year']);
$month = preg_replace('/[^0-9]/', '', $_POST['month']);
$method = $_POST['method'];
$pass = trim($_POST['pass']);

if(!$pass)
    alert('请输入管理员密码');

// 管理员密码验证
$admin = get_admin('super');
if(sql_password($pass) != $admin['mb_password'])
    alert('您输入的密码错误');

if(!$year)
    alert('请选择年份');

if(!$month)
    alert('请选择月份');

// 删除日志 query
$del_date = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT);
switch($method) {
    case 'before':
        $sql_common = " where substring(vi_date, 1, 7) < '$del_date' ";
        break;
    case 'specific':
        $sql_common = " where substring(vi_date, 1, 7) = '$del_date' ";
        break;
    default:
        alert('请使用正常方式访问');
        break;
}

// 所有日志数量
$sql = " select count(*) as cnt from {$g5['visit_table']} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// 删除日志
$sql = " delete from {$g5['visit_table']} $sql_common ";
sql_query($sql);

// 删除后日志数量
$sql = " select count(*) as cnt from {$g5['visit_table']} ";
$row = sql_fetch($sql);
$total_count2 = $row['cnt'];

alert('合计'.number_format($total_count).'条数据中删除完成'.number_format($total_count - $total_count2).'条数据', './visit_delete.php');
?>