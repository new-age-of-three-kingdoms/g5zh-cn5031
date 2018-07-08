<?php
$sub_menu = '200300';
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " from {$g5['mail_table']} ";

// 仅计算所有数据行数量
$sql = " select COUNT(*) as cnt {$sql_common} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$page = 1;

$sql = " select * {$sql_common} order by ma_id desc ";
$result = sql_query($sql);

$g5['title'] = '群发会员邮件';
include_once('./admin.head.php');

$colspan = 7;
?>

<div class="local_desc01 local_desc">
    <p>
        <b>测试邮件</b>将会发送到管理员邮箱<br>
        当前可用邮箱地址合计<?php echo $total_count ?>个<br>
        <strong>注意)请勿向为订阅邮件的会员发送大量邮件，以免被投诉或举报垃圾邮件</strong>
    </p>
</div>

<div class="btn_add01 btn_add">
    <a href="./mail_form.php" id="mail_add">邮件内容添加</a>
</div>

<form name="fmaillist" id="fmaillist" action="./mail_delete.php" method="post">
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col"><input type="checkbox" name="chkall" value="1" id="chkall" title="选择当前页面全部" onclick="check_all(this.form)"></th>
        <th scope="col">序号</th>
        <th scope="col">主题</th>
        <th scope="col">发布时间时</th>
        <th scope="col">测试邮件</th>
        <th scope="col">发送</th>
        <th scope="col">预览</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=mysql_fetch_array($result); $i++) {
        //$s_del = '<a href="javascript:post_delete(\'mail_update.php\', '.$row['ma_id'].');">删除</a>';
        $s_vie = '<a href="./mail_preview.php?ma_id='.$row['ma_id'].'" target="_blank">预览</a>';

        $num = number_format($total_count - ($page - 1) * $config['cf_page_rows'] - $i);

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['ma_subject']; ?> 邮件</label>
            <input type="checkbox" id="chk_<?php echo $i ?>" name="chk[]" value="<?php echo $row['ma_id'] ?>">
        </td>
        <td class="td_num"><?php echo $num ?></td>
        <td><a href="./mail_form.php?w=u&amp;ma_id=<?php echo $row['ma_id'] ?>"><?php echo $row['ma_subject'] ?></a></td>
        <td class="td_datetime"><?php echo $row['ma_time'] ?></td>
        <td class="td_test"><a href="./mail_test.php?ma_id=<?php echo $row['ma_id'] ?>">测试邮件</a></td>
        <td class="td_send"><a href="./mail_select_form.php?ma_id=<?php echo $row['ma_id'] ?>">发送</a></td>
        <td class="td_mngsmall"><?php echo $s_vie ?></td>
    </tr>

    <?php
    }
    if (!$i)
        echo "<tr><td colspan=\"".$colspan."\" class=\"empty_table\">未找到相应信息</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <button type="submit">删除所选</button>
</div>
</form>

<script>
$(function() {
    $('#fmaillist').submit(function() {
        if(confirm("删除操作执行后将不能进行恢复\n\n点击确定执行操作")) {
            if (!is_checked("chk[]")) {
                alert("请选择需要删除的项目");
                return false;
            }

            return true;
        } else {
            return false;
        }
    });
});
</script>

<?php
include_once ('./admin.tail.php');
?>