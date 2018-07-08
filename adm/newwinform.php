<?php
$sub_menu = '100310';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "弹出层";
if ($w == "u")
{
    $html_title .= "编辑";
    $sql = " select * from {$g5['new_win_table']} where nw_id = '$nw_id' ";
    $nw = sql_fetch($sql);
    if (!$nw['nw_id']) alert("未找到已设置项目");
}
else
{
    $html_title .= "新建";
    $nw['nw_device'] = 'both';
    $nw['nw_disable_hours'] = 24;
    $nw['nw_left']   = 10;
    $nw['nw_top']    = 10;
    $nw['nw_width']  = 450;
    $nw['nw_height'] = 500;
    $nw['nw_content_html'] = 2;
}

$g5['title'] = $html_title;
include_once (G5_ADMIN_PATH.'/admin.head.php');
?>

<form name="frmnewwin" action="./newwinformupdate.php" onsubmit="return frmnewwin_check(this);" method="post">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="nw_id" value="<?php echo $nw_id; ?>">

<div class="local_desc01 local_desc">
    <p>设置首次访问时弹出的内容</p>
</div>

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="nw_device">访问设备</label></th>
        <td>
            <?php echo help("设置需要显示弹窗的访问设备"); ?>
            <select name="nw_device" id="nw_device">
                <option value="both"<?php echo get_selected($nw['nw_device'], 'both', true); ?>>电脑与触屏版</option>
                <option value="pc"<?php echo get_selected($nw['nw_device'], 'pc'); ?>>PC</option>
                <option value="mobile"<?php echo get_selected($nw['nw_device'], 'mobile'); ?>>触屏版</option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="nw_disable_hours">时间<strong class="sound_only"> 必选项</strong></label></th>
        <td>
            <?php echo help("设置方可可以屏蔽弹窗的时间长度"); ?>
            <input type="text" name="nw_disable_hours" value="<?php echo $nw['nw_disable_hours']; ?>" id="nw_disable_hours" required class="frm_input required" size="5">小时
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="nw_begin_time">开始时间<strong class="sound_only"> 必选项</strong></label></th>
        <td>
            <input type="text" name="nw_begin_time" value="<?php echo $nw['nw_begin_time']; ?>" id="nw_begin_time" required class="frm_input required" size="21" maxlength="19">
            <input type="checkbox" name="nw_begin_chk" value="<?php echo date("Y-m-d 00:00:00", G5_SERVER_TIME); ?>" id="nw_begin_chk" onclick="if (this.checked == true) this.form.nw_begin_time.value=this.form.nw_begin_chk.value; else this.form.nw_begin_time.value = this.form.nw_begin_time.defaultValue;">
            <label for="nw_begin_chk">今天开始</label>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="nw_end_time">结束时间<strong class="sound_only"> 必选项</strong></label></th>
        <td>
            <input type="text" name="nw_end_time" value="<?php echo $nw['nw_end_time']; ?>" id="nw_end_time" required class="frm_input required" size="21" maxlength="19">
            <input type="checkbox" name="nw_end_chk" value="<?php echo date("Y-m-d 23:59:59", G5_SERVER_TIME+(60*60*24*7)); ?>" id="nw_end_chk" onclick="if (this.checked == true) this.form.nw_end_time.value=this.form.nw_end_chk.value; else this.form.nw_end_time.value = this.form.nw_end_time.defaultValue;">
            <label for="nw_end_chk">设置截止日期为7天后</label>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="nw_left">弹窗左侧位置<strong class="sound_only"> 必选项</strong></label></th>
        <td>
           <input type="text" name="nw_left" value="<?php echo $nw['nw_left']; ?>" id="nw_left" required class="frm_input required" size="5"> px
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="nw_top">弹窗顶部位置<strong class="sound_only"> 必选项</strong></label></th>
        <td>
            <input type="text" name="nw_top" value="<?php echo $nw['nw_top']; ?>" id="nw_top" required class="frm_input required"  size="5"> px
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="nw_width">弹窗宽度<strong class="sound_only"> 必选项</strong></label></th>
        <td>
            <input type="text" name="nw_width" value="<?php echo $nw['nw_width'] ?>" id="nw_width" required class="frm_input required" size="5"> px
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="nw_height">弹窗高度<strong class="sound_only"> 必选项</strong></label></th>
        <td>
            <input type="text" name="nw_height" value="<?php echo $nw['nw_height'] ?>" id="nw_height" required class="frm_input required" size="5"> px
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="nw_subject">弹窗标题<strong class="sound_only"> 必选项</strong></label></th>
        <td>
            <input type="text" name="nw_subject" value="<?php echo stripslashes($nw['nw_subject']) ?>" id="nw_subject" required class="frm_input required" size="80">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="nw_content">内容</label></th>
        <td><?php echo editor_html('nw_content', get_text($nw['nw_content'], 0)); ?></td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="确定" class="btn_submit" accesskey="s">
    <a href="./newwinlist.php">目录</a>
</div>
</form>

<script>
function frmnewwin_check(f)
{
    errmsg = "";
    errfld = "";

    <?php echo get_editor_js('nw_content'); ?>

    check_field(f.nw_subject, "请输入标题");

    if (errmsg != "") {
        alert(errmsg);
        errfld.focus();
        return false;
    }
    return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
