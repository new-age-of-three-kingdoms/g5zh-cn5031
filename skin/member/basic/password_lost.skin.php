<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 找回会员信息开始 { -->
<div id="find_info" class="new_win mbskin">
    <h1 id="win_title">找回会员信息</h1>

    <form name="fpasswordlost" action="<?php echo $action_url ?>" onsubmit="return fpasswordlost_submit(this);" method="post" autocomplete="off">
    <fieldset id="info_fs">
        <p>
            请输入您在注册时使用的邮件地址<br>
            我们将给您的邮箱发送确认邮件
        </p>
        <label for="mb_email">E-mail地址<strong class="sound_only">必选项</strong></label>
        <input type="text" name="mb_email" id="mb_email" required class="required frm_input email" size="30">
    </fieldset>
    <?php echo captcha_html();  ?>
    <div class="win_btn">
        <input type="submit" value="确定" class="btn_submit">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
    </form>
</div>

<script>
function fpasswordlost_submit(f)
{
    <?php echo chk_captcha_js();  ?>

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
<!-- } 找回会员信息结束 -->