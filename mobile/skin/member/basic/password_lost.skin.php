<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="find_info" class="new_win mbskin">
    <h1 id="win_title">忘记密码及忘记账号</h1>

    <form name="fpasswordlost" action="<?php echo $action_url ?>" onsubmit="return fpasswordlost_submit(this);" method="post" autocomplete="off">
    <fieldset id="info_fs">
        <p>
            请输入您在注册时使用的邮件地址<br>
            我们将给您的邮箱发送确认邮件
        </p>
        <input type="email" id="mb_email" name="mb_email" placeholder="邮件地址地址(必选项)" required class="frm_input email">
    </fieldset>

    <?php echo captcha_html(); ?>

    <div class="win_btn">
        <input type="submit" class="btn_submit" value="确定">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
    </form>
</div>

<script>
function fpasswordlost_submit(f)
{
    <?php echo chk_captcha_js(); ?>

    return true;
}

$(function() {
    var sw = screen.width;
    var sh = screen.height;
    var cw = document.body.clientWidth;
    var ch = document.body.clientHeight;
    var top  = sh / 2 - ch / 2 - 100;
    var left = sw / 2 - cw / 2;
    moveTo(left, top);
});
</script>
