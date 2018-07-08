<?php
$sub_menu = "200300";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'r');

$token = get_token();

$html_title = '会员邮件';

if ($w == 'u') {
    $html_title .= '修改';
    $readonly = ' readonly';

    $sql = " select * from {$g5['mail_table']} where ma_id = '{$ma_id}' ";
    $ma = sql_fetch($sql);
    if (!$ma['ma_id'])
        alert('未找到已设置项目');
} else {
    $html_title .= '输入';
}

$g5['title'] = $html_title;
include_once('./admin.head.php');
?>

<p>邮件内容中使用{姓名},{昵称},{会员ID},{邮件地址}等快捷嗲吗时将会转换成对应的接收邮件的会员数据</p>

<form name="fmailform" id="fmailform" action="./mail_update.php" onsubmit="return fmailform_check(this);" method="post">
<input type="hidden" name="w" value="<?php echo $w ?>" id="w">
<input type="hidden" name="ma_id" value="<?php echo $ma['ma_id'] ?>" id="ma_id">
<input type="hidden" name="token" value="<?php echo $token ?>" id="token">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="ma_subject">邮件 主题<strong class="sound_only">必选项</strong></label></th>
        <td><input type="text" name="ma_subject" value="<?php echo $ma['ma_subject'] ?>" id="ma_subject" required class="required frm_input" size="100"></td>
    </tr>
    <tr>
        <th scope="row"><label for="ma_content">邮件 内容<strong class="sound_only">必选项</strong></label></th>
        <td><?php echo editor_html("ma_content", get_text($ma['ma_content'], 0)); ?></td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" class="btn_submit" accesskey="s" value="确定">
</div>
</form>

<script>
function fmailform_check(f)
{
    errmsg = "";
    errfld = "";

    check_field(f.ma_subject, "请输入标题");
    //check_field(f.ma_content, "请输入内容");

    if (errmsg != "") {
        alert(errmsg);
        errfld.focus();
        return false;
    }

    <?php echo get_editor_js("ma_content"); ?>
    <?php echo chk_editor_js("ma_content"); ?>

    return true;
}

document.fmailform.ma_subject.focus();
</script>

<?php
include_once('./admin.tail.php');
?>
