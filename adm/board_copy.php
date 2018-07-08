<?php
$sub_menu = "300100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

$g5['title'] = '论坛版块复制';
include_once(G5_PATH.'/head.sub.php');
?>

<div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <form name="fboardcopy" id="fboardcopy" action="./board_copy_update.php" onsubmit="return fboardcopy_check(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>" id="bo_table">

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption><?php echo $g5['title']; ?></caption>
        <tbody>
        <tr>
            <th scope="col">源数据表名称</th>
            <td><?php echo $bo_table ?></td>
        </tr>
        <tr>
            <th scope="col"><label for="target_table">复制数据表名称<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="target_table" id="target_table" required class="required alnum_ frm_input" maxlength="20">英文、数字及下划线（不含空格）</td>
        </tr>
        <tr>
            <th scope="col"><label for="target_subject">论坛版块名称<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="target_subject" value="[副本] <?php echo $board['bo_subject'] ?>" id="target_subject" required class="required frm_input" maxlength="120"></td>
        </tr>
        <tr>
            <th scope="col">复制类型</th>
            <td>
                <input type="radio" name="copy_case" value="schema_only" id="copy_case" checked>
                <label for="copy_case">仅结构</label>
                <input type="radio" name="copy_case" value="schema_data_both" id="copy_case2">
                <label for="copy_case2">结构与数据</label>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" class="btn_submit" value="复制">
        <input type="button" class="btn_cancel" value="关闭窗口" onclick="window.close();">
    </div>

    </form>

</div>

<script>
function fboardcopy_check(f)
{
    if (f.bo_table.value == f.target_table.value) {
        alert("需要复制新建的数据表名称不能与源数据表名称相同");
        return false;
    }

    return true;
}
</script>


<?php
include_once(G5_PATH.'/tail.sub.php');
?>
