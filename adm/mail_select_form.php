<?php
$sub_menu = "200300";
include_once('./_common.php');

if (!$config['cf_email_use'])
    alert('网站设置中开启邮件发送功能后才能使用此功能');

auth_check($auth[$sub_menu], 'r');

$sql = " select * from {$g5['mail_table']} where ma_id = '$ma_id' ";
$ma = sql_fetch($sql);
if (!$ma['ma_id'])
    alert('请选择需要发送的内容');

// 全部会员数量
$sql = " select COUNT(*) as cnt from {$g5['member_table']} ";
$row = sql_fetch($sql);
$tot_cnt = $row['cnt'];

// 等待删除账号
$sql = " select COUNT(*) as cnt from {$g5['member_table']} where mb_leave_date <> '' ";
$row = sql_fetch($sql);
$finish_cnt = $row['cnt'];

$last_option = explode('||', $ma['ma_last_option']);
for ($i=0; $i<count($last_option); $i++) {
    $option = explode('=', $last_option[$i]);
    // 参数
    $var = $option[0];
    $$var = $option[1];
}

if (!isset($mb_id1)) $mb_id1 = 1;
if (!isset($mb_level_from)) $mb_level_from = 1;
if (!isset($mb_level_to)) $mb_level_to = 10;
if (!isset($mb_mailling)) $mb_mailling = 1;

$g5['title'] = '群发会员邮件';
include_once('./admin.head.php');
?>

<div class="local_ov01 local_ov">
    全部会员 <?php echo number_format($tot_cnt) ?>名 , 等待删除账号 <?php echo number_format($finish_cnt) ?>名, 正常会员 <?php echo number_format($tot_cnt - $finish_cnt) ?>名 请选择发送对象
</div>

<form name="frmsendmailselectform" id="frmsendmailselectform" action="./mail_select_list.php" method="post" autocomplete="off">
<input type="hidden" name="ma_id" value="<?php echo $ma_id ?>">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 选择对象</caption>
    <tbody>
    <tr>
        <th scope="row">会员 ID</th>
        <td>
            <input type="radio" name="mb_id1" value="1" id="mb_id1_all" <?php echo $mb_id1?"checked":""; ?>> <label for="mb_id1_all">全部</label>
            <input type="radio" name="mb_id1" value="0" id="mb_id1_section" <?php echo !$mb_id1?"checked":""; ?>> <label for="mb_id1_section">区间</label>
            <input type="text" name="mb_id1_from" value="<?php echo $mb_id1_from ?>" id="mb_id1_from" title="开始区间" class="frm_input"> 开始
            <input type="text" name="mb_id1_to" value="<?php echo $mb_id1_to ?>" id="mb_id1_to" title="结束区间" class="frm_input"> 截止
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_email">E-mail</label></th>
        <td>
            <?php echo help("邮件地址中包含的内容 (如：@".preg_replace('#^(www[^\.]*\.){1}#', '', $_SERVER['HTTP_HOST']).")") ?>
            <input type="text" name="mb_email" value="<?php echo $mb_email ?>" id="mb_email" class="frm_input" size="50">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_mailling">订阅列表</label></th>
        <td>
            <select name="mb_mailling" id="mb_mailling">
                <option value="1">仅向订阅会员
                <option value="">全部
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">权限</th>
        <td>
            <label for="mb_level_from" class="sound_only">最少权限</label>
            <select name="mb_level_from" id="mb_level_from">
            <?php for ($i=1; $i<=10; $i++) { ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
            <?php } ?>
            </select> 开始
            <label for="mb_level_to" class="sound_only">最大权限</label>
            <select name="mb_level_to" id="mb_level_to">
            <?php for ($i=1; $i<=10; $i++) { ?>
                <option value="<?php echo $i ?>"<?php echo $i==10 ? " selected" : ""; ?>><?php echo $i ?></option>
            <?php } ?>
            </select> 截止
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="gr_id">指定群组会员</label></th>
        <td>
            <select name="gr_id" id="gr_id">
                <option value=''>全部</option>
                <?php
                $sql = " select gr_id, gr_subject from {$g5['group_table']} order by gr_subject ";
                $result = sql_query($sql);
                for ($i=0; $row=sql_fetch_array($result); $i++) {
                    echo '<option value="'.$row['gr_id'].'">'.$row['gr_subject'].'</option>';
                }
                ?>
            </select>
        </td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="确定" class="btn_submit">
    <a href="./mail_list.php">目录 </a>
</div>
</form>

<?php
include_once('./admin.tail.php');
?>
