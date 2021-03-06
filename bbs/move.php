<?php
include_once('./_common.php');

if ($sw == 'move')
    $act = '移动';
else if ($sw == 'copy')
    $act = '复制';
else
    alert('sw值传递错误！');

// 论坛版块管理员以上权限才能进行复制、移动
if ($is_admin != 'board' && $is_admin != 'group' && $is_admin != 'super')
    alert_close("只有论坛版块管理员以上会员才能进行访问");

$g5['title'] = '主题 ' . $act;
include_once(G5_PATH.'/head.sub.php');

$wr_id_list = '';
if ($wr_id)
    $wr_id_list = $wr_id;
else {
    $comma = '';
    for ($i=0; $i<count($_POST['chk_wr_id']); $i++) {
        $wr_id_list .= $comma . $_POST['chk_wr_id'][$i];
        $comma = ',';
    }
}

//$sql = " select * from {$g5['board_table']} a, {$g5['group_table']} b where a.gr_id = b.gr_id and bo_table <> '$bo_table' ";
// 选择原始论坛版块
$sql = " select * from {$g5['board_table']} a, {$g5['group_table']} b where a.gr_id = b.gr_id ";
if ($is_admin == 'group')
    $sql .= " and b.gr_admin = '{$member['mb_id']}' ";
else if ($is_admin == 'board')
    $sql .= " and a.bo_admin = '{$member['mb_id']}' ";
$sql .= " order by a.gr_id, a.bo_order, a.bo_table ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $list[$i] = $row;
}
?>

<div id="copymove" class="new_win">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <form name="fboardmoveall" method="post" action="./move_update.php" onsubmit="return fboardmoveall_submit(this);">
    <input type="hidden" name="sw" value="<?php echo $sw ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id_list" value="<?php echo $wr_id_list ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="act" value="<?php echo $act ?>">
    <input type="hidden" name="url" value="<?php echo $_SERVER['HTTP_REFERER'] ?>">

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>请选择需要<?php echo $act ?>的论坛版块</caption>
        <thead>
        <tr>
            <th scope="col">
                <label for="chkall" class="sound_only">当前 页 所有论坛</label>
                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
            </th>
            <th scope="col">论坛</th>
        </tr>
        </thead>
        <tbody>
        <?php for ($i=0; $i<count($list); $i++) {
            $atc_mark = '';
            $atc_bg = '';
            if ($list[$i]['bo_table'] == $bo_table) { // 如果时论坛版块内的主题时
                $atc_mark = '<span class="copymove_current">当前<span class="sound_only">论坛</span></span>';
                $atc_bg = 'copymove_currentbg';
            }
        ?>
        <tr class="<?php echo $atc_bg; ?>">
            <td class="td_chk">
                <label for="chk<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['bo_table'] ?></label>
                <input type="checkbox" value="<?php echo $list[$i]['bo_table'] ?>" id="chk<?php echo $i ?>" name="chk_bo_table[]">
            </td>
            <td>
                <label for="chk<?php echo $i ?>">
                    <?php
                    echo $list[$i]['gr_subject'] . ' &gt; ';
                    $save_gr_subject = $list[$i]['gr_subject'];
                    ?>
                    <?php echo $list[$i]['bo_subject'] ?> (<?php echo $list[$i]['bo_table'] ?>)
                    <?php echo $atc_mark; ?>
                </label>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>

    <div class="win_btn">
        <input type="submit" value="<?php echo $act ?>" id="btn_submit" class="btn_submit">
    </div>
    </form>

</div>

<script>
$(function() {
    $(".win_btn").append("<button type=\"button\" class=\"btn_cancel\">关闭窗口</button>");

    $(".win_btn button").click(function() {
        window.close();
    });
});

function all_checked(sw) {
    var f = document.fboardmoveall;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_bo_table[]")
            f.elements[i].checked = sw;
    }
}

function fboardmoveall_submit(f)
{
    var check = false;

    if (typeof(f.elements['chk_bo_table[]']) == 'undefined')
        ;
    else {
        if (typeof(f.elements['chk_bo_table[]'].length) == 'undefined') {
            if (f.elements['chk_bo_table[]'].checked)
                check = true;
        } else {
            for (i=0; i<f.elements['chk_bo_table[]'].length; i++) {
                if (f.elements['chk_bo_table[]'][i].checked) {
                    check = true;
                    break;
                }
            }
        }
    }

    if (!check) {
        alert('请选择需要'+f.act.value+'的论坛版块');
        return false;
    }

    document.getElementById('btn_submit').disabled = true;

    f.action = './move_update.php';
    return true;
}
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
