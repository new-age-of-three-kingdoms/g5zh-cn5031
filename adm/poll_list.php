<?php
$sub_menu = "200900";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

$sql_common = " from {$g5['poll_table']} ";

$sql_search = " where (1) ";
if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        default :
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "po_id";
    $sod = "desc";
}
$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}
            {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 计算所有页码
if ($page < 1) $page = 1; // 如果没有页码时设置为1
$from_record = ($page - 1) * $rows; // 获取开始行

$sql = " select *
            {$sql_common}
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">全部目录</a>';

$g5['title'] = '投票管理';
include_once('./admin.head.php');

$colspan = 7;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
   投票数 <?php echo number_format($total_count) ?>个
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<div class="sch_last">
    <label for="sfl" class="sound_only">搜索类型</label>
    <select name="sfl" id="sfl">
        <option value="po_subject"<?php echo get_selected($_GET['sfl'], "po_subject"); ?>>主题</option>
    </select>
    <label for="stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
    <input type="submit" class="btn_submit" value="搜索">
</div>
</form>

<div class="btn_add01 btn_add">
    <a href="./poll_form.php" id="poll_add">新建投票调查</a>
</div>

<form name="fpolllist" id="fpolllist" action="./poll_delete.php" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only">当前页面所有投票项目</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">序号</th>
        <th scope="col">主题</th>
        <th scope="col">投票权限</th>
        <th scope="col">投票数量</th>
        <th scope="col">其他评论</th>
        <th scope="col">管理</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $sql2 = " select sum(po_cnt1+po_cnt2+po_cnt3+po_cnt4+po_cnt5+po_cnt6+po_cnt7+po_cnt8+po_cnt9) as sum_po_cnt from {$g5['poll_table']} where po_id = '{$row['po_id']}' ";
        $row2 = sql_fetch($sql2);
        $po_etc = ($row['po_etc']) ? "使用" : "不使用";

        $s_mod = '<a href="./poll_form.php?'.$qstr.'&amp;w=u&amp;po_id='.$row['po_id'].'">修改</a>';
        //$s_del = '<a href="javascript:post_delete(\'poll_form_update.php\', \''.$row['po_id'].'\');">删除</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo cut_str(get_text($row['po_subject']),70) ?> 投票</label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['po_id'] ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_num"><?php echo $row['po_id'] ?></td>
        <td><?php echo cut_str(get_text($row['po_subject']),70) ?></td>
        <td class="td_num"><?php echo $row['po_level'] ?></td>
        <td class="td_num"><?php echo $row2['sum_po_cnt'] ?></td>
        <td class="td_etc"><?php echo $po_etc ?></td>
        <td class="td_mngsmall"><?php echo $s_mod ?></td>
    </tr>

    <?php
    }

    if ($i==0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">未找到相应信息</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <button type="submit">删除所选</button>
</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<script>
$(function() {
    $('#fpolllist').submit(function() {
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