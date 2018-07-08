<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="mb_login" class="mbskin">
    <h1><?php echo $g5['title'] ?></h1>

    <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post">
    <input type="hidden" name="url" value='<?php echo $login_url ?>'>

    <div id="login_frm">
        <label for="login_id" class="sound_only">会员ID<strong class="sound_only"> 必选项</strong></label>
        <input type="text" name="mb_id" id="login_id" placeholder="会员ID(必选项)" required class="frm_input required" maxLength="20">
        <label for="login_pw" class="sound_only">密码<strong class="sound_only"> 必选项</strong></label>
        <input type="password" name="mb_password" id="login_pw" placeholder="密码(必选项)" required class="frm_input required" maxLength="20">
        <input type="submit" value="登录" class="btn_submit">
        <div>
            <input type="checkbox" name="auto_login" id="login_auto_login">
            <label for="login_auto_login">自动登录</label>
        </div>
    </div>

    <section>
        <h2>会员登录指南</h2>
        <p>
            如果您忘记了您的ID或登录密码请通过忘记密码进行找回操作！<br>
            如果还不是会员，我们非常期待您的注册！
        </p>
        <div>
            <a href="<?php echo G5_BBS_URL ?>/password_lost.php" target="_blank" id="login_password_lost" class="btn02">忘记ID/密码</a>
            <a href="./register.php" class="btn01">注册会员</a>
        </div>
    </section>

    <div class="btn_confirm">
        <a href="<?php echo G5_URL ?>/">返回首页</a>
    </div>

    </form>

</div>

<script>
$(function(){
    $("#login_auto_login").click(function(){
        if (this.checked) {
            this.checked = confirm("开启自动登录功能后系统将会记住您的登录信息.\n\n请勿在网吧等公共场所设备上开启此功能\n\n点击确定开启自动登录功能");
        }
    });
});

function flogin_submit(f)
{
    return true;
}
</script>
