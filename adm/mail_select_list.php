<?php
$sub_menu = "200300";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

$ma_last_option = "";

$sql_common = " from {$g5['member_table']} ";
$sql_where = " where (1) ";

// 会员id开始与结束
if ($mb_id1 != 1)
    $sql_where .= " and mb_id between '{$mb_id1_from}' and '{$mb_id1_to}' ";

// 邮件地址特定关键词
if ($mb_email != "")
    $sql_where .= " and mb_email like '%{$mb_email}%' ";

// 订阅列表
if ($mb_mailling != "")
    $sql_where .= " and mb_mailling = '{$mb_mailling}' ";

// 权限
$sql_where .= " and mb_level between '{$mb_level_from}' and '{$mb_level_to}' ";

// 指定群组会员
if ($gr_id) {
    $group_member = "";
    $comma = "";
    $sql2 = " select mb_id from {$g5['group_member_table']} where gr_id = '{$gr_id}' order by mb_id ";
    $result2 = sql_query($sql2);
    for ($k=0; $row2=sql_fetch_array($result2); $k++) {
        $group_member .= "{$comma}'{$row2['mb_id']}'";
        $comma = ",";
    }

    if (!$group_member)
        alert('您所选择群组内尚无会员');

    $sql_where .= " and mb_id in ($group_member) ";
}

// 排除封号及删除会员信息
$sql_where .= " and mb_leave_date = '' and mb_intercept_date = '' ";

$sql = " select COUNT(*) as cnt {$sql_common} {$sql_where} ";
$row = sql_fetch($sql);
$cnt = $row['cnt'];
if ($cnt == 0)
    alert('未找到符合条件的会员数据');

// 储存最后选项
$ma_last_option .= "mb_id1={$mb_id1}";
$ma_last_option .= "||mb_id1_from={$mb_id1_from}";
$ma_last_option .= "||mb_id1_to={$mb_id1_to}";
$ma_last_option .= "||mb_email={$mb_email}";
$ma_last_option .= "||mb_mailling={$mb_mailling}";
$ma_last_option .= "||mb_level_from={$mb_level_from}";
$ma_last_option .= "||mb_level_to={$mb_level_to}";
$ma_last_option .= "||gr_id={$gr_id}";

sql_query(" update {$g5['mail_table']} set ma_last_option = '{$ma_last_option}' where ma_id = '{$ma_id}' ");

$g5['title'] = "发送邮件地址列表";
include_once('./admin.head.php');
?>

<form name="fmailselectlist" id="fmailselectlist" method="post" action="./mail_select_update.php">
<input type="hidden" name="token" value="<?php echo $token ?>">
<input type="hidden" name="ma_id" value="<?php echo $ma_id ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col">序号</th>
        <th scope="col">会员ID</th>
        <th scope="col">姓名</th>
        <th scope="col">昵称</th>
        <th scope="col">E-mail</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql = " select mb_id, mb_name, mb_nick, mb_email, mb_datetime $sql_common $sql_where order by mb_id ";
    $result = sql_query($sql);
    $i=0;
    $ma_list = "";
    $cr = "";
    while ($row=sql_fetch_array($result)) {
        $i++;
        $ma_list .= $cr . $row['mb_email'] . "||" . $row['mb_id'] . "||" . get_text($row['mb_name']) . "||" . $row['mb_nick'] . "||" . $row['mb_datetime'];
        $cr = "\n";

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_num"><?php echo $i ?></td>
        <td class="td_mbid"><?php echo $row['mb_id'] ?></td>
        <td class="td_mbname"><?php echo get_text($row['mb_name']); ?></td>
        <td class="td_mbname"><?php echo $row['mb_nick'] ?></td>
        <td><?php echo $row['mb_email'] ?></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
    <textarea name="ma_list" style="display:none"><?=$ma_list?></textarea>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="发送邮件" class="btn_submit">
    <a href="./mail_select_form.php?ma_id=<?php echo $ma_id ?>">返回</a>
</div>

</form>

<?php
include_once('./admin.tail.php');
?>
