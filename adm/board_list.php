<?php
$sub_menu = "300100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " from {$g5['board_table']} a ";
$sql_search = " where (1) ";

if ($is_admin != "super") {
    $sql_common .= " , {$g5['group_table']} b ";
    $sql_search .= " and (a.gr_id = b.gr_id and b.gr_admin = '{$member['mb_id']}') ";
}

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "bo_table" :
            $sql_search .= " ($sfl like '$stx%') ";
            break;
        case "a.gr_id" :
            $sql_search .= " ($sfl = '$stx') ";
            break;
        default :
            $sql_search .= " ($sfl like '%$stx%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "a.gr_id, a.bo_table";
    $sod = "asc";
}
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 计算所有页码
if ($page < 1) { $page = 1; } // 如果没有页码时设置为1
$from_record = ($page - 1) * $rows; // 获取开始行

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">全部目录</a>';

$g5['title'] = '论坛管理';
include_once('./admin.head.php');

$colspan = 15;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    已创建论坛版块数量<?php echo number_format($total_count) ?>个
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">

<label for="sfl" class="sound_only">搜索类型</label>
<select name="sfl" id="sfl">
    <option value="bo_table"<?php echo get_selected($_GET['sfl'], "bo_subject", true); ?>>TABLE</option>
    <option value="bo_subject"<?php echo get_selected($_GET['sfl'], "bo_subject"); ?>>主题</option>
    <option value="a.gr_id"<?php echo get_selected($_GET['sfl'], "a.gr_id"); ?>>群组ID</option>
</select>
<label for="stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" value="搜索" class="btn_submit">

</form>

<?php if ($is_admin == 'super') { ?>
<div class="btn_add01 btn_add">
    <a href="./board_form.php" id="bo_add">新建论坛</a>
</div>
<?php } ?>

<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
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
            <label for="chkall" class="sound_only">所有论坛</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('a.gr_id') ?>群组</a></th>
        <th scope="col"><?php echo subject_sort_link('bo_table') ?>TABLE</a></th>
        <th scope="col"><?php echo subject_sort_link('bo_skin', '', 'desc') ?>皮肤</a></th>
        <th scope="col"><?php echo subject_sort_link('bo_mobile_skin', '', 'desc') ?>触屏版<br>皮肤</span></a></th>
        <th scope="col"><?php echo subject_sort_link('bo_subject') ?>主题</a></th>
        <th scope="col">浏览<span class="sound_only">积分</span></th>
        <th scope="col">发表<span class="sound_only">积分</span></th>
        <th scope="col">评论<span class="sound_only">积分</span></th>
        <th scope="col">下载<span class="sound_only">积分</span></th>
        <th scope="col"><?php echo subject_sort_link('bo_use_sns') ?>分享<br>功能</a></th>
        <th scope="col"><?php echo subject_sort_link('bo_use_search') ?>搜索<br>功能</a></th>
        <th scope="col"><?php echo subject_sort_link('bo_order') ?>显示<br>排序</a></th>
        <th scope="col">访问设备</th>
        <th scope="col">管理</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $one_update = '<a href="./board_form.php?w=u&amp;bo_table='.$row['bo_table'].'&amp;'.$qstr.'">修改</a>';
        $one_copy = '<a href="./board_copy.php?bo_table='.$row['bo_table'].'" class="board_copy" target="win_board_copy">复制</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['bo_subject']) ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td>
            <?php if ($is_admin == 'super'){ ?>
                <?php echo get_group_select("gr_id[$i]", $row['gr_id']) ?>
            <?php }else{ ?>
                <input type="hidden" name="gr_id[<?php echo $i ?>]" value="<?php echo $row['gr_id'] ?>"><?php echo $row['gr_subject'] ?>
            <?php } ?>
        </td>
        <td>
            <input type="hidden" name="board_table[<?php echo $i ?>]" value="<?php echo $row['bo_table'] ?>">
            <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $row['bo_table'] ?>"><?php echo $row['bo_table'] ?></a>
        </td>
        <td>
            <label for="bo_skin_<?php echo $i; ?>" class="sound_only">皮肤</label>
            <?php echo get_skin_select('board', 'bo_skin_'.$i, "bo_skin[$i]", $row['bo_skin']); ?>
        </td>
        <td>
            <label for="bo_mobile_skin_<?php echo $i; ?>" class="sound_only">触屏版皮肤</label>
            <?php echo get_mobile_skin_select('board', 'bo_mobile_skin_'.$i, "bo_mobile_skin[$i]", $row['bo_mobile_skin']); ?>
        </td>
        <td>
            <label for="bo_subject_<?php echo $i; ?>" class="sound_only">论坛版块名称<strong class="sound_only"> 必选项</strong></label>
            <input type="text" name="bo_subject[<?php echo $i ?>]" value="<?php echo get_text($row['bo_subject']) ?>" id="bo_subject_<?php echo $i ?>" required class="required frm_input bo_subject full_input" size="10">
        </td>
        <td class="td_numsmall">
            <label for="bo_read_point_<?php echo $i; ?>" class="sound_only">浏览积分</label>
            <input type="text" name="bo_read_point[<?php echo $i ?>]" value="<?php echo $row['bo_read_point'] ?>" id="bo_read_point_<?php echo $i; ?>" class="frm_input" size="2">
        </td>
        <td class="td_numsmall">
            <label for="bo_write_point_<?php echo $i; ?>" class="sound_only">发帖积分</label>
            <input type="text" name="bo_write_point[<?php echo $i ?>]" value="<?php echo $row['bo_write_point'] ?>" id="bo_write_point_<?php echo $i; ?>" class="frm_input" size="2">
        </td>
        <td class="td_numsmall">
            <label for="bo_comment_point_<?php echo $i; ?>" class="sound_only">回帖积分</label>
            <input type="text" name="bo_comment_point[<?php echo $i ?>]" value="<?php echo $row['bo_comment_point'] ?>" id="bo_comment_point_<?php echo $i; ?>" class="frm_input" size="2">
        </td>
        <td class="td_numsmall">
            <label for="bo_download_point_<?php echo $i; ?>" class="sound_only">下载积分</label>
            <input type="text" name="bo_download_point[<?php echo $i ?>]" value="<?php echo $row['bo_download_point'] ?>" id="bo_download_point_<?php echo $i; ?>" class="frm_input" size="2">
        </td>
        <td class="td_chk">
            <label for="bo_use_sns_<?php echo $i; ?>" class="sound_only">sns分享功能</label>
            <input type="checkbox" name="bo_use_sns[<?php echo $i ?>]" value="1" id="bo_use_sns_<?php echo $i ?>" <?php echo $row['bo_use_sns']?"checked":"" ?>>
        </td>
        <td class="td_chk">
            <label for="bo_use_search_<?php echo $i; ?>" class="sound_only">搜索功能</label>
            <input type="checkbox" name="bo_use_search[<?php echo $i ?>]" value="1" id="bo_use_search_<?php echo $i ?>" <?php echo $row['bo_use_search']?"checked":"" ?>>
        </td>
        <td class="td_chk">
            <label for="bo_order_<?php echo $i; ?>" class="sound_only">显示顺序</label>
            <input type="text" name="bo_order[<?php echo $i ?>]" value="<?php echo $row['bo_order'] ?>" id="bo_order_<?php echo $i ?>" class="frm_input" size="2">
        </td>
        <td class="td_mngsmall">
            <label for="bo_device_<?php echo $i; ?>" class="sound_only">访问设备</label>
            <select name="bo_device[<?php echo $i ?>]" id="bo_device_<?php echo $i ?>">
                <option value="both"<?php echo get_selected($row['bo_device'], 'both', true); ?>>全部</option>
                <option value="pc"<?php echo get_selected($row['bo_device'], 'pc'); ?>>PC</option>
                <option value="mobile"<?php echo get_selected($row['bo_device'], 'mobile'); ?>>触屏版</option>
            </select>
        </td>
        <td class="td_mngsmall">
            <?php echo $one_update ?>
            <?php echo $one_copy ?>
        </td>
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
    <input type="submit" name="act_button" value="编辑所选" onclick="document.pressed=this.value">
    <?php if ($is_admin == 'super') { ?>
    <input type="submit" name="act_button" value="删除所选" onclick="document.pressed=this.value">
    <?php } ?>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr.'&amp;page='); ?>

<script>
function fboardlist_submit(f)
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

$(function(){
    $(".board_copy").click(function(){
        window.open(this.href, "win_board_copy", "left=100,top=100,width=550,height=450");
        return false;
    });
});
</script>

<?php
include_once('./admin.tail.php');
?>
