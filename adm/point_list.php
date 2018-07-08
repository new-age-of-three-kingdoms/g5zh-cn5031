<?php
$sub_menu = "200200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

$sql_common = " from {$g5['point_table']} ";

$sql_search = " where (1) ";

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case 'mb_id' :
            $sql_search .= " ({$sfl} = '{$stx}') ";
            break;
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

$mb = array();
if ($sfl == 'mb_id' && $stx)
    $mb = get_member($stx);

$g5['title'] = '积分管理';
include_once ('./admin.head.php');

$colspan = 9;

$po_expire_term = '';
if($config['cf_point_term'] > 0) {
    $po_expire_term = $config['cf_point_term'];
}

if (strstr($sfl, "mb_id"))
    $mb_id = $stx;
else
    $mb_id = "";
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    全部 <?php echo number_format($total_count) ?> 件
    <?php
    if (isset($mb['mb_id']) && $mb['mb_id']) {
        echo '&nbsp;(' . $mb['mb_id'] .'的积分合计：: ' . number_format($mb['mb_point']) . '分)';
    } else {
        $row2 = sql_fetch(" select sum(po_point) as sum_point from {$g5['point_table']} ");
        echo '&nbsp;(全部合计 '.number_format($row2['sum_point']).'分)';
    }
    ?>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">搜索类型</label>
<select name="sfl" id="sfl">
    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>会员ID</option>
    <option value="po_content"<?php echo get_selected($_GET['sfl'], "po_content"); ?>>内容</option>
</select>
<label for="stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" class="btn_submit" value="搜索">
</form>

<form name="fpointlist" id="fpointlist" method="post" action="./point_list_delete.php" onsubmit="return fpointlist_submit(this);">
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
            <label for="chkall" class="sound_only">全部积分详情</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('mb_id') ?>会员ID</a></th>
        <th scope="col">姓名</th>
        <th scope="col">昵称</th>
        <th scope="col"><?php echo subject_sort_link('po_content') ?>积分 内容</a></th>
        <th scope="col"><?php echo subject_sort_link('po_point') ?>积分</a></th>
        <th scope="col"><?php echo subject_sort_link('po_datetime') ?>时间</a></th>
        <th scope="col">截止日</th>
        <th scope="col">积分合计</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($i==0 || ($row2['mb_id'] != $row['mb_id'])) {
            $sql2 = " select mb_id, mb_name, mb_nick, mb_email, mb_homepage, mb_point from {$g5['member_table']} where mb_id = '{$row['mb_id']}' ";
            $row2 = sql_fetch($sql2);
        }

        $mb_nick = get_sideview($row['mb_id'], $row2['mb_nick'], $row2['mb_email'], $row2['mb_homepage']);

        $link1 = $link2 = '';
        if (!preg_match("/^\@/", $row['po_rel_table']) && $row['po_rel_table']) {
            $link1 = '<a href="'.G5_BBS_URL.'/board.php?bo_table='.$row['po_rel_table'].'&amp;wr_id='.$row['po_rel_id'].'" target="_blank">';
            $link2 = '</a>';
        }

        $expr = '';
        if($row['po_expired'] == 1)
            $expr = ' txt_expired';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
            <input type="hidden" name="po_id[<?php echo $i ?>]" value="<?php echo $row['po_id'] ?>" id="po_id_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['po_content'] ?> 详情</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_mbid"><a href="?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo $row['mb_id'] ?></a></td>
        <td class="td_mbname"><?php echo get_text($row2['mb_name']); ?></td>
        <td class="td_name sv_use"><div><?php echo $mb_nick ?></div></td>
        <td class="td_pt_log"><?php echo $link1 ?><?php echo $row['po_content'] ?><?php echo $link2 ?></td>
        <td class="td_num td_pt"><?php echo number_format($row['po_point']) ?></td>
        <td class="td_datetime"><?php echo $row['po_datetime'] ?></td>
        <td class="td_date<?php echo $expr; ?>">
            <?php if ($row['po_expired'] == 1) { ?>
            截至<?php echo substr(str_replace('-', '', $row['po_expire_date']), 2); ?>
            <?php } else echo $row['po_expire_date'] == '9999-12-31' ? '&nbsp;' : $row['po_expire_date']; ?>
        </td>
        <td class="td_num td_pt"><?php echo number_format($row['po_mb_point']) ?></td>
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">未找到相应信息</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="删除所选" onclick="document.pressed=this.value">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<section id="point_mng">
    <h2 class="h2_frm">指定会员积分增减设定</h2>

    <form name="fpointlist2" method="post" id="fpointlist2" action="./point_update.php" autocomplete="off">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="token" value="<?php echo $token ?>">

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="mb_id">会员ID<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="mb_id" value="<?php echo $mb_id ?>" id="mb_id" class="required frm_input" required></td>
        </tr>
        <tr>
            <th scope="row"><label for="po_content">积分 内容<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="po_content" id="po_content" required class="required frm_input" size="80"></td>
        </tr>
        <tr>
            <th scope="row"><label for="po_point">积分<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="po_point" id="po_point" required class="required frm_input"></td>
        </tr>
        <?php if($config['cf_point_term'] > 0) { ?>
        <tr>
            <th scope="row"><label for="po_expire_term">积分有效期</label></th>
            <td><input type="text" name="po_expire_term" value="<?php echo $po_expire_term; ?>" id="po_expire_term" class="frm_input" size="5"> 日</td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="确定" class="btn_submit">
    </div>

    </form>

</section>

<script>
function fpointlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert("请选择需要"+document.pressed+"的项目");
        return false;
    }

    if(document.pressed == "删除所选") {
        if(!confirm("点击确定删除所选内容")) {
            return false;
        }
    }

    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
