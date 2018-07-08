<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$outlogin_skin_url.'/style.css">', 0);
?>

<aside id="ol_before" class="ol">
    <h2>会员登录</h2>
    <!-- 引用登录-登录前状态 开始 -->
    <form name="foutlogin" action="<?php echo $outlogin_action_url ?>" onsubmit="return fhead_submit(this);" method="post" autocomplete="off">
    <fieldset>
        <input type="hidden" name="url" value="<?php echo $outlogin_url ?>">
        <input type="text" name="mb_id" id="ol_id" placeholder="会员ID(必选项)" required class="required" maxlength="20">
        <input type="password" id="ol_pw" name="mb_password" placeholder="密码(必选项)" required class="required" maxlength="20">
        <input type="submit" id="ol_submit" value="登录">
        <div id="ol_svc">
            <input type="checkbox" id="auto_login" name="auto_login" value="1">
            <label for="auto_login" id="auto_login_label">自动登录</label>
            <a href="<?php echo G5_BBS_URL ?>/register.php"><b>注册会员</b></a>
            <a href="<?php echo G5_BBS_URL ?>/password_lost.php" id="ol_password_lost">忘记密码</a>
        </div>
    </fieldset>
    </form>
</aside>

<script>
<?php if (!G5_IS_MOBILE) { ?>
$omi = $('#ol_id');
$omp = $('#ol_pw');
$omp.css('display','inline-block').css('width',104);
$omi_label = $('#ol_idlabel');
$omi_label.addClass('ol_idlabel');
$omp_label = $('#ol_pwlabel');
$omp_label.addClass('ol_pwlabel');
$omi.focus(function() {
    $omi_label.css('visibility','hidden');
});
$omp.focus(function() {
    $omp_label.css('visibility','hidden');
});
$omi.blur(function() {
    $this = $(this);
    if($this.attr('id') == "ol_id" && $this.attr('value') == "") $omi_label.css('visibility','visible');
});
$omp.blur(function() {
    $this = $(this);
    if($this.attr('id') == "ol_pw" && $this.attr('value') == "") $omp_label.css('visibility','visible');
});
<?php } ?>

$("#auto_login").click(function(){
    if (this.checked) {
        this.checked = confirm("开启自动登录功能后系统将会记住您的登录信息.\n\n请勿在网吧等公共场所设备上开启此功能\n\n点击确定开启自动登录功能");
    }
});

function fhead_submit(f)
{
    return true;
}
</script>
<!-- 引用登录-登录前状态 结束 -->
