<?
$sub_menu = "200100";
include_once("./_common.php");

check_demo();

auth_check($auth[$sub_menu], "d");

$mb = get_member($_POST['mb_id']);

if (!$mb['mb_id'])
    alert("会员信息不存在");
else if ($member['mb_id'] == $mb['mb_id'])
    alert("此会员当前登录使用状态中不能进行删除操作！");
else if (is_admin($mb['mb_id']) == "super")
    alert("不能对管理员账号进行操作！");
else if ($mb['mb_level'] >= $member['mb_level'])
    alert("会员权限等级高于您的等级，您没有权限删除！");

check_token();

// 删除会员数据
member_delete($mb['mb_id']);

if ($url)
    goto_url("{$url}?$qstr&amp;w=u&amp;mb_id=$mb_id");
else
    goto_url("./member_list.php?$qstr");
?>
