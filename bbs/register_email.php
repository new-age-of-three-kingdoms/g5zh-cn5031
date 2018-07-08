<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

$sql = " select mb_email, mb_datetime, mb_email_certify from {$g5['member_table']} where mb_id = '{$mb_id}' ";
$mb = sql_fetch($sql);
if (substr($mb['mb_email_certify'],0,1)!=0) {
    alert("您已完成邮箱地址认证", G5_URL);
}

$g5['title'] = '更改地址验证邮箱';
include_once('./_head.php');
?>

<p>如果您需要更换邮箱进行验证请设置您需要验证的邮箱地址</p>

<form method="post" name="fregister_email" action="<?php echo G5_HTTPS_BBS_URL.'/register_email_update.php'; ?>" onsubmit="return fregister_email_submit(this);">
<input type="hidden" name="mb_id" value="<?php echo $mb_id; ?>">

<div class="tbl_frm01 tbl_frm">
    <table>
    <caption>会员账户注册信息</caption>
    <tr>
        <th scope="row"><label for="reg_mb_email">E-mail<strong class="sound_only">必选项</strong></label></th>
        <td><input type="text" name="mb_email" id="reg_mb_email" required class="frm_input email required" size="50" maxlength="100" value="<?php echo $mb['mb_email']; ?>"></td>
    </tr>
    <tr>
        <th scope="row">验证码</th>
        <td><?php echo captcha_html(); ?></td>
    </tr>
    </table>
</div>

<div class="btn_confirm">
    <input type="submit" id="btn_submit" class="btn_submit" value="确定更改验证邮箱">
    <a href="<?php echo G5_URL ?>" class="btn_cancel">取消</a>
</div>

</form>

<script>
function fregister_email_submit(f)
{
    <?php echo chk_captcha_js();  ?>

    return true;
}
</script>
<?
include_once('./_tail.php');
?>
