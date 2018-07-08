<?php
$sub_menu = "300200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$gr = get_group($gr_id);
if (!$gr['gr_id']) {
    alert('不存在的群组');
}

$sql_common = " from {$g5['group_member_table']} a
                         left outer join {$g5['member_table']} b on (a.mb_id = b.mb_id) ";

$sql_search = " where gr_id = '{$gr_id}' ";
// 修复不能使用会员id搜索的bug
if (isset($stx) && $stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        default :
            $sql_search .= " ($sfl like '%$stx%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "gm_datetime";
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

$g5['title'] = $gr['gr_subject'].' 允许访问群组会员 (群组ID:'.$gr['gr_id'].')';
include_once('./admin.head.php');

$colspan = 7;
?>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<input type="hidden" name="gr_id" value="<?php echo $gr_id ?>">
<label for="sfl" class="sound_only">搜索类型</label>
<select name="sfl" id="sfl">
    <option value="a.mb_id"<?php echo get_selected($_GET['sfl'], "a.mb_id") ?>>会员ID</option>
</select>
<label for="stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" value="搜索" class="btn_submit">
</form>

<form name="fboardgroupmember" id="fboardgroupmember" action="./boardgroupmember_update.php" onsubmit="return fboardgroupmember_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">
<input type="hidden" name="gr_id" value="<?php echo $gr_id ?>">
<input type="hidden" name="w" value="ld">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only">所有允许访问会员</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">群组</th>
        <th scope="col"><?php echo subject_sort_link('b.mb_id', 'gr_id='.$gr_id) ?>会员ID</a></th>
        <th scope="col"><?php echo subject_sort_link('b.mb_name', 'gr_id='.$gr_id) ?>姓名</a></th>
        <th scope="col"><?php echo subject_sort_link('b.mb_nick', 'gr_id='.$gr_id) ?>昵称</a></th>
        <th scope="col"><?php echo subject_sort_link('b.mb_today_login', 'gr_id='.$gr_id) ?>最后登录</a></th>
        <th scope="col"><?php echo subject_sort_link('a.gm_datetime', 'gr_id='.$gr_id) ?>处理时间</a></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        // 允许访问群组
        $sql2 = " select count(*) as cnt from {$g5['group_member_table']} where mb_id = '{$row['mb_id']}' ";
        $row2 = sql_fetch($sql2);
        $group = "";
        if ($row2['cnt'])
            $group = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">'.$row2['cnt'].'</a>';

        //$s_del = '<a href="javascript:post_delete(\'boardgroupmember_update.php\', \''.$row['gm_id'].'\');">删除</a>';

        $mb_nick = get_sideview($row['mb_id'], $row['mb_nick'], $row['mb_email'], $row['mb_homepage']);

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['mb_nick'] ?> 会员</label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['gm_id'] ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_grid"><?php echo $group ?></td>
        <td class="td_mbid"><?php echo $row['mb_id'] ?></td>
        <td class="td_mbname"><?php echo get_text($row['mb_name']); ?></td>
        <td class="td_name sv_use"><?php echo $mb_nick ?></td>
        <td class="td_datetime"><?php echo substr($row['mb_today_login'],2,8) ?></td>
        <td class="td_datetime"><?php echo $row['gm_datetime'] ?></td>
    </tr>
    <?php
    }

    if ($i == 0)
    {
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">未找到相应信息</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="" value="删除所选">
</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;gr_id=$gr_id&page="); ?>

<script>
function fboardgroupmember_submit(f)
{
    if (!is_checked("chk[]")) {
        alert("请选择需要删除的项目");
        return false;
    }

    return true;
}
</script>

<?php
include_once('./admin.tail.php');
?>
