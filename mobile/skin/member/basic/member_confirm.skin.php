<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="mb_confirm" class="mbskin">
    <h1><?php echo $g5['title'] ?></h1>

    <p>
        <strong>请您输入登录密码</strong>
        为了确保您的账户安全将进行再次验证
    </p>

    <form name="fmemberconfirm" action="<?php echo $url ?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
    <input type="hidden" name="mb_id" value="<?php echo $member['mb_id'] ?>">
    <input type="hidden" name="w" value="u">

    <fieldset>
        会员ID
        <span id="mb_confirm_id"><?php echo $member['mb_id'] ?></span>
        <input type="password" name="mb_password" id="mb_confirm_pw" placeholder="密码(必选项)" required class="frm_input" size="15" maxLength="20">
        <input type="submit" value="确定" id="btn_submit" class="btn_submit">
    </fieldset>

    </form>

    <div class="btn_confirm">
        <a href="<?php echo G5_URL ?>">返回首页</a>
    </div>

</div>

<script>
function fmemberconfirm_submit(f)
{
    document.getElementById("btn_submit").disabled = true;

    return true;
}
</script>
