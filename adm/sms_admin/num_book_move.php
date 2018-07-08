<?php
$sub_menu = "900600";
include_once('./_common.php');

$inputbox_type="checkbox";
if ($sw == 'move'){
    $act = '移动';
} else if ($sw == 'copy') {
    $act = '复制';
} else {
    alert('sw值传递错误！');
}

auth_check($auth[$sub_menu], "r");

$g5['title'] = '序号群组 ' . $act;
include_once(G5_PATH.'/head.sub.php');

$bk_no_list = implode(',', $_POST['bk_no']);

$sql = " select * from {$g5['sms5_book_group_table']} order by bg_no ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $list[$i] = $row;
}
?>

<div id="copymove" class="new_win">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <form name="fboardmoveall" method="post" action="./number_move_update.php" onsubmit="return fboardmoveall_submit(this);">
    <input type="hidden" name="sw" value="<?php echo $sw ?>">
    <input type="hidden" name="bk_no_list" value="<?php echo $bk_no_list ?>">
    <input type="hidden" name="act" value="<?php echo $act ?>">
    <input type="hidden" name="url" value="<?php echo $_SERVER['HTTP_REFERER'] ?>">

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>请选择需要的<?php echo $act ?>群组</caption>
        <thead>
        <tr>
            <th scope="col">
                <?php if ( $inputbox_type == "checkbox" ){ //仅在复制时 ?>
                <label for="chkall" class="sound_only">群组 全部</label>
                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
                <?php } ?>
            </th>
            <th scope="col">群组</th>
        </tr>
        </thead>
        <tbody>
        <?php for ($i=0; $i<count($list); $i++) { ?>
        <tr>
            <td class="td_chk">
                <label for="chk<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['bg_name'] ?></label>
                <input type="<?php echo $inputbox_type; ?>" value="<?php echo $list[$i]['bg_no'] ?>" id="chk<?php echo $i ?>" name="chk_bg_no[]">
            </td>
            <td>
                <label for="chk<?php echo $i ?>">
                    <?php echo $list[$i]['bg_name'] ?>
                </label>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>

    <div class="win_btn">
        <input type="submit" value="<?php echo $act ?>" id="btn_submit" class="btn_submit">
        <button type="button" class="btn_cancel">关闭窗口</button>
    </div>
    </form>

</div>

<script>
(function($) {
    $(".win_btn button").click(function(e) {
        window.close();
        return false;
    });
})(jQuery);

function all_checked(sw) {
    var f = document.fboardmoveall;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_bg_no[]")
            f.elements[i].checked = sw;
    }
}

function fboardmoveall_submit(f)
{
    var check = false;

    if (typeof(f.elements['chk_bg_no[]']) == 'undefined')
        ;
    else {
        if (typeof(f.elements['chk_bg_no[]'].length) == 'undefined') {
            if (f.elements['chk_bg_no[]'].checked)
                check = true;
        } else {
            for (i=0; i<f.elements['chk_bg_no[]'].length; i++) {
                if (f.elements['chk_bg_no[]'][i].checked) {
                    check = true;
                    break;
                }
            }
        }
    }

    if (!check) {
        alert('请选择需要'+f.act.value+的分类');
        return false;
    }

    document.getElementById('btn_submit').disabled = true;

    return true;
}
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>