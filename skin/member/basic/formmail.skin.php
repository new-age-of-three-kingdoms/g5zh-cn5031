<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 站内邮件开始 { -->
<div id="formmail" class="new_win mbskin">
    <h1 id="win_title">发送邮件至<?php echo $name ?></h1>

    <form name="fformmail" action="./formmail_send.php" onsubmit="return fformmail_submit(this);" method="post" enctype="multipart/form-data" style="margin:0px;">
    <input type="hidden" name="to" value="<?php echo $email ?>">
    <input type="hidden" name="attach" value="2">
    <input type="hidden" name="token" value="<?php echo $token ?>">
    <?php if ($is_member) { // 如果是会员  ?>
    <input type="hidden" name="fnick" value="<?php echo $member['mb_nick'] ?>">
    <input type="hidden" name="fmail" value="<?php echo $member['mb_email'] ?>">
    <?php }  ?>

    <div class="tbl_frm01 tbl_form">
        <table>
        <caption>编辑邮件</caption>
        <tbody>
        <?php if (!$is_member) {  ?>
        <tr>
            <th scope="row"><label for="fnick">姓名<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="fnick" id="fnick" required class="frm_input required"></td>
        </tr>
        <tr>
            <th scope="row"><label for="fmail">E-mail<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="fmail"  id="fmail" required class="frm_input required"></td>
        </tr>
        <?php }  ?>
        <tr>
            <th scope="row"><label for="subject">主题<strong class="sound_only">必选项</strong></label></th>
            <td><input type="text" name="subject" id="subject" required class="frm_input required"></td>
        </tr>
        <tr>
            <th scope="row">类型</th>
            <td>
                <input type="radio" name="type" value="0" id="type_text" checked> <label for="type_text">TEXT</label>
                <input type="radio" name="type" value="1" id="type_html"> <label for="type_html">HTML</label>
                <input type="radio" name="type" value="2" id="type_both"> <label for="type_both">TEXT+HTML</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="content">内容<strong class="sound_only">必选项</strong></label></th>
            <td><textarea name="content" id="content" required class="required"></textarea></td>
        </tr>
        <tr>
            <th scope="row"><label for="file1">附件 1</label></th>
            <td>
                <input type="file" name="file1"  id="file1"  class="frm_input">
                请选择需要上传的附件
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="file2">附件 2</label></th>
            <td><input type="file" name="file2" id="file2" class="frm_input"></td>
        </tr>
        <tr>
            <th scope="row">验证码</th>
            <td><?php echo captcha_html(); ?></td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="win_btn">
        <input type="submit" value="发送邮件" id="btn_submit" class="btn_submit">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>

    </form>
</div>

<script>
with (document.fformmail) {
    if (typeof fname != "undefined")
        fname.focus();
    else if (typeof subject != "undefined")
        subject.focus();
}

function fformmail_submit(f)
{
    <?php echo chk_captcha_js();  ?>

    if (f.file1.value || f.file2.value) {
        // 4.00.11
        if (!confirm("较大的附件需要较长时间来上传\n\n提示邮件发送完成前请勿刷新或关闭当前页面"))
            return false;
    }

    document.getElementById('btn_submit').disabled = true;

    return true;
}
</script>
<!-- } 站内邮件结束 -->