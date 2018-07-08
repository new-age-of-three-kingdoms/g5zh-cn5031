<?php
$sub_menu = "300200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

if (!isset($group['gr_device'])) {
    // 添加论坛群组使用的数据字段
    // both : pc, mobile全部支持
    // pc : 仅限pc
    // mobile : 仅限移动端
    // none : 不使用
    sql_query(" ALTER TABLE  `{$g5['board_group_table']}` ADD  `gr_device` ENUM(  'both',  'pc',  'mobile' ) NOT NULL DEFAULT  'both' AFTER  `gr_subject` ", false);
}

$sql_common = " from {$g5['group_table']} ";

$sql_search = " where (1) ";
if ($is_admin != 'super')
    $sql_search .= " and (gr_admin = '{$member['mb_id']}') ";

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "gr_id" :
        case "gr_admin" :
            $sql_search .= " ({$sfl} = '{$stx}') ";
            break;
        default :
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if ($sst)
    $sql_order = " order by {$sst} {$sod} ";
else
    $sql_order = " order by gr_id asc ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 计算所有页码
if ($page < 1) $page = 1; // 如果没有页码时设置为1
$from_record = ($page - 1) * $rows; // 获取开始行

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">首页</a>';

$g5['title'] = '论坛群组设置';
include_once('./admin.head.php');

$colspan = 10;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    全部群组 <?php echo number_format($total_count) ?>个
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">搜索类型</label>
<select name="sfl" id="sfl">
    <option value="gr_subject"<?php echo get_selected($_GET['sfl'], "gr_subject"); ?>>主题</option>
    <option value="gr_id"<?php echo get_selected($_GET['sfl'], "gr_id"); ?>>ID</option>
    <option value="gr_admin"<?php echo get_selected($_GET['sfl'], "gr_admin"); ?>>群组管理员</option>
</select>
<label for="stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" required class="required frm_input">
<input type="submit" value="搜索" class="btn_submit">
</fieldset>
</form>

<?php if ($is_admin == 'super') { ?>
<div class="btn_add01 btn_add sort_with">
    <a href="./boardgroup_form.php" id="bo_gr_add">新建论坛群组</a>
</div>
<?php } ?>

<form name="fboardgrouplist" id="fboardgrouplist" action="./boardgroup_list_update.php" onsubmit="return fboardgrouplist_submit(this);" method="post">
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
            <label for="chkall" class="sound_only">群组 全部</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('gr_id') ?>群组ID</a></th>
        <th scope="col"><?php echo subject_sort_link('gr_subject') ?>主题</a></th>
        <th scope="col"><?php echo subject_sort_link('gr_admin') ?>群组管理员</a></th>
        <th scope="col">论坛</th>
        <th scope="col">访问<br>限制</th>
        <th scope="col">授权<br>会员</th>
        <th scope="col"><?php echo subject_sort_link('gr_order') ?>显示<br>排序</a></th>
        <th scope="col">访问设备</th>
        <th scope="col">管理</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        // 授权会员
        $sql1 = " select count(*) as cnt from {$g5['group_member_table']} where gr_id = '{$row['gr_id']}' ";
        $row1 = sql_fetch($sql1);

        // 论坛数量
        $sql2 = " select count(*) as cnt from {$g5['board_table']} where gr_id = '{$row['gr_id']}' ";
        $row2 = sql_fetch($sql2);

        $s_upd = '<a href="./boardgroup_form.php?'.$qstr.'&amp;w=u&amp;gr_id='.$row['gr_id'].'">修改</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <input type="hidden" name="group_id[<?php echo $i ?>]" value="<?php echo $row['gr_id'] ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['gr_subject'] ?> 群组</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_grid"><a href="<?php echo G5_BBS_URL ?>/group.php?gr_id=<?php echo $row['gr_id'] ?>"><?php echo $row['gr_id'] ?></a></td>
        <td class="td_input">
            <label for="gr_subject_<?php echo $i; ?>" class="sound_only">群组主题</label>
            <input type="text" name="gr_subject[<?php echo $i ?>]" value="<?php echo get_text($row['gr_subject']) ?>" id="gr_subject_<?php echo $i ?>" class="frm_input">
        </td>
        <td class="td_mng td_input">
        <?php if ($is_admin == 'super'){ ?>
            <label for="gr_admin_<?php echo $i; ?>" class="sound_only">群组管理员</label>
            <input type="text" name="gr_admin[<?php echo $i ?>]" value="<?php echo $row['gr_admin'] ?>" id="gr_admin_<?php echo $i ?>" class="frm_input" size="10" maxlength="20">
        <?php }else{ ?>
            <input type="hidden" name="gr_admin[<?php echo $i ?>]" value="<?php echo $row['gr_admin'] ?>"><?php echo $row['gr_admin'] ?>
        <?php } ?>
        </td>
        <td class="td_numsmall"><a href="./board_list.php?sfl=a.gr_id&amp;stx=<?php echo $row['gr_id'] ?>"><?php echo $row2['cnt'] ?></a></td>
        <td class="td_chk">
             <label for="gr_use_access_<?php echo $i; ?>" class="sound_only">开启访问限制</label>
            <input type="checkbox" name="gr_use_access[<?php echo $i ?>]" <?php echo $row['gr_use_access']?'checked':'' ?> value="1" id="gr_use_access_<?php echo $i ?>">
        </td>
        <td class="td_numsmall"><a href="./boardgroupmember_list.php?gr_id=<?php echo $row['gr_id'] ?>"><?php echo $row1['cnt'] ?></a></td>
        <td class="td_chk">
            <label for="gr_order_<?php echo $i; ?>" class="sound_only">主要栏目 显示顺序</label>
            <input type="text" name="gr_order[<?php echo $i ?>]" value="<?php echo $row['gr_order'] ?>" id="gr_order_<?php echo $i ?>" class="frm_input" size="2">
        </td>
        <td class="td_mng">
            <label for="gr_device_<?php echo $i; ?>" class="sound_only">访问设备</label>
            <select name="gr_device[<?php echo $i ?>]" id="gr_device_<?php echo $i ?>">
                <option value="both"<?php echo get_selected($row['gr_device'], 'both'); ?>>全部</option>
                <option value="pc"<?php echo get_selected($row['gr_device'], 'pc'); ?>>PC</option>
                <option value="mobile"<?php echo get_selected($row['gr_device'], 'mobile'); ?>>触屏版</option>
            </select>
        </td>
        <td class="td_mngsmall"><?php echo $s_upd ?></td>
    </tr>

    <?php
        }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">未找到相应信息</td></tr>';
    ?>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" onclick="document.pressed=this.value" value="编辑所选">
    <input type="submit" name="act_button" onclick="document.pressed=this.value" value="删除所选">
    <a href="./boardgroup_form.php">新建论坛群组</a>
</div>
</form>

<div class="local_desc01 local_desc">
    <p>
        可以通过群组授权访问功能设定指定会员允许或禁止访问<br>
        所设置的权限选项将对所属群组内的所有论坛版块进行相同设置
    </p>
</div>

<?php
$pagelist = get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr.'&amp;page=');
echo $pagelist;
?>

<script>
function fboardgrouplist_submit(f)
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
include_once('./admin.tail.php');
?>
