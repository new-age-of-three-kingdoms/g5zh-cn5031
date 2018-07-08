<?php
$sub_menu = "100200";
include_once('./_common.php');

if ($is_admin != 'super')
    alert('您没有访问权限');

$token = get_token();

$sql_common = " from {$g5['auth_table']} a left join {$g5['member_table']} b on (a.mb_id=b.mb_id) ";

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
    $sst  = "a.mb_id, au_menu";
    $sod = "";
}
$sql_order = " order by $sst $sod ";

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

$g5['title'] = "管理权限设置";
include_once('./admin.head.php');

$colspan = 5;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    已设置管理权限 <?php echo number_format($total_count) ?>件
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<input type="hidden" name="sfl" value="a.mb_id" id="sfl">

<label for="stx" class="sound_only">会员ID<strong class="sound_only"> 必选项</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" value="搜索" id="fsearch_submit" class="btn_submit">

</form>

<form name="fauthlist" id="fauthlist" method="post" action="./auth_list_delete.php" onsubmit="return fauthlist_submit(this);">
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
            <label for="chkall" class="sound_only">当前所有会员</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('a.mb_id') ?>会员ID</a></th>
        <th scope="col"><?php echo subject_sort_link('mb_nick') ?>昵称</a></th>
        <th scope="col">栏目</th>
        <th scope="col">权限</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $count = 0;
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $is_continue = false;
        // 删除没有会员id的栏目
        if($row['mb_id'] == '' && $row['mb_nick'] == '') {
            sql_query(" delete from {$g5['auth_table']} where au_menu = '{$row['au_menu']}' ");
            $is_continue = true;
        }

        // 当栏目id变更时删除当前未指定的栏目
        if (!isset($auth_menu[$row['au_menu']]))
        {
            sql_query(" delete from {$g5['auth_table']} where au_menu = '{$row['au_menu']}' ");
            $is_continue = true;
        }

        if($is_continue)
            continue;

        $mb_nick = get_sideview($row['mb_id'], $row['mb_nick'], $row['mb_email'], $row['mb_homepage']);

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <input type="hidden" name="au_menu[<?php echo $i ?>]" value="<?php echo $row['au_menu'] ?>">
            <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['mb_nick'] ?>的权限</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_mbid"><a href="?sfl=a.mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo $row['mb_id'] ?></a></td>
        <td class="td_auth_mbnick"><?php echo $mb_nick ?></td>
        <td class="td_menu">
            <?php echo $row['au_menu'] ?>
            <?php echo $auth_menu[$row['au_menu']] ?>
        </td>
        <td class="td_auth"><?php echo $row['au_auth'] ?></td>
    </tr>
    <?php
        $count++;
    }

    if ($count == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">未找到相应信息</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="删除所选" onclick="document.pressed=this.value">
</div>

<?php
//if (isset($stx))
//    echo '<script>document.fsearch.sfl.value = "'.$sfl.'";</script>'."\n";

if (strstr($sfl, 'mb_id'))
    $mb_id = $stx;
else
    $mb_id = '';
?>
</form>

<?php
$pagelist = get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr.'&amp;page=');
echo $pagelist;
?>

<form name="fauthlist2" id="fauthlist2" action="./auth_update.php" method="post" autocomplete="off">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">

<section id="add_admin">
    <h2 class="h2_frm">新建管理权限</h2>

    <div class="local_desc01 local_desc">
        <p>
            您可以在此设定指定会员id的权限<br>
            权限 <strong>r</strong>为浏览、浏览权限, <strong>w</strong>为写入、编辑权限, <strong>d</strong>为删除权限
        </p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="mb_id">会员ID<strong class="sound_only">必选项</strong></label></th>
            <td>
                <strong id="msg_mb_id" class="msg_sound_only"></strong>
                <input type="text" name="mb_id" value="<?php echo $mb_id ?>" id="mb_id" required class="required frm_input">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="au_menu">授权访问栏目<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select id="au_menu" name="au_menu" required class="required">
                    <option value=''>请选择</option>
                    <?php
                    foreach($auth_menu as $key=>$value)
                    {
                        if (!(substr($key, -3) == '000' || $key == '-' || !$key))
                            echo '<option value="'.$key.'">'.$key.' '.$value.'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">权限设置</th>
            <td>
                <input type="checkbox" name="r" value="r" id="r" checked>
                <label for="r">r (浏览)</label>
                <input type="checkbox" name="w" value="w" id="w">
                <label for="w">w (编辑)</label>
                <input type="checkbox" name="d" value="d" id="d">
                <label for="d">d (删除)</label>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="添加" class="btn_submit">
    </div>
</section>

</form>

<script>
function fauthlist_submit(f)
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
