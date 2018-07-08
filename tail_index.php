<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// 底部文件路径设置 : 请勿随意更改此段代码
if ($config['cf_include_tail'] && is_file(G5_PATH.'/'.$config['cf_include_tail'])) {
    include_once(G5_PATH.'/'.$config['cf_include_tail']);
    return; // 不执行以下代码
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/tail.php');
    return;
}
?>
</div>

<!-- } 项目结束 -->

<hr>

<!-- 底部开始 { -->
<?php include_once(G5_PATH.'/bottom.php');?>
<!-- } 底部结束 -->

<script>
$(function() {
    // 如有字体设定则执行
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?php
include_once(G5_PATH."/tail.sub.php");
?>