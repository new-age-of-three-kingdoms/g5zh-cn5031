<?
$sub_menu = "200100";
include_once("./_common.php");

check_demo();

auth_check($auth[$sub_menu], "d");

check_token();

$msg = "";
for ($i=0; $i<count($chk); $i++)
{
    // 传递实际编号
    $k = $_POST['chk'][$i];

    $mb = get_member($_POST['mb_id'][$k]);

    if (!$mb['mb_id']) {
        $msg .= "{$mb['mb_id']} : 会员信息不存在\\n";
    } else if ($member['mb_id'] == $mb['mb_id']) {
        $msg .= "{$mb['mb_id']} : 此会员当前登录使用状态中不能进行删除操作！\\n";
    } else if (is_admin($mb['mb_id']) == "super") {
        $msg .= "{$mb['mb_id']} : 不能对管理员账号进行操作！\\n";
    } else if ($is_admin != "super" && $mb['mb_level'] >= $member['mb_level']) {
        $msg .= "{$mb['mb_id']} : 会员权限等级高于您的等级，您没有权限删除！\\n";
    } else {
        // 删除会员数据
        member_delete($mb['mb_id']);
    }
}

if ($msg)
    echo "<script type='text/javascript'> alert('$msg'); </script>";

goto_url("./member_list.php?$qstr");
?>
