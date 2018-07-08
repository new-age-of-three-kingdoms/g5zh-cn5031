<?php
$sub_menu = '300700';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '常见问题内容管理';
if ($fm_subject) $g5['title'] .= ' : '.$fm_subject;
include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql = " select * from {$g5['faq_master_table']} where fm_id = '$fm_id' ";
$fm = sql_fetch($sql);

$sql_common = " from {$g5['faq_table']} where fm_id = '$fm_id' ";

// 仅计算所有数据行数量
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row[cnt];

$sql = "select * $sql_common order by fa_order , fa_id ";
$result = sql_query($sql);
?>

<div class="local_ov01 local_ov">
    已登记常见问题<?php echo $total_count; ?>件
</div>

<div class="local_desc01 local_desc">
    <ol>
        <li>常见问题无数量限制，请根据需要添加</li>
        <li>请点击<strong>新建常见问题</strong>设置提问与回答</li>
    </ol>
</div>

<div class="btn_add01 btn_add">
    <a href="./faqform.php?fm_id=<?php echo $fm['fm_id']; ?>">新建常见问题</a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col">序号</th>
        <th scope="col">主题</th>
        <th scope="col">顺序</th>
        <th scope="col">管理</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $row1 = sql_fetch(" select COUNT(*) as cnt from {$g5['faq_table']} where fm_id = '{$row['fm_id']}' ");
        $cnt = $row1[cnt];

        $s_mod = icon("修改", "");
        $s_del = icon("删除", "");

        $num = $i + 1;

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_num"><?php echo $num; ?></td>
        <td><?php echo stripslashes($row['fa_subject']); ?></td>
        <td class="td_num"><?php echo $row['fa_order']; ?></td>
        <td class="td_mngsmall">
            <a href="./faqform.php?w=u&amp;fm_id=<?php echo $row['fm_id']; ?>&amp;fa_id=<?php echo $row['fa_id']; ?>"><span class="sound_only"><?php echo stripslashes($row['fa_subject']); ?> </span>修改</a>
            <a href="javascript:del('./faqformupdate.php?w=d&amp;fm_id=<?php echo $row['fm_id']; ?>&amp;fa_id=<?php echo $row['fa_id']; ?>');"><span class="sound_only"><?php echo stripslashes($row['fa_subject']); ?> </span>删除</a>
        </td>
    </tr>

    <?php
    }

    if ($i == 0) {
        echo '<tr><td colspan="4" class="empty_table">未找到相应信息</td></tr>';
    }
    ?>
    </tbody>
    </table>

</div>

<div class="btn_confirm01 btn_confirm">
    <a href="./faqmasterlist.php">FAQ 管理</a>
</div>


<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
