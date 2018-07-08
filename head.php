<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// 顶部文件路径设置 : 请勿随意更改此段代码
if ($config['cf_include_head'] && is_file(G5_PATH.'/'.$config['cf_include_head'])) {
    include_once(G5_PATH.'/'.$config['cf_include_head']);
    return; // 不执行以下代码
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/head.php');
    return;
}
?>

<!-- 顶部开始 { -->
<?php include_once(G5_PATH.'/top.php');?>
<!-- } 顶部结束 -->

<hr>

<!-- 项目开始 { -->
<div id="wrapper" <?php echo $co_id == 'aboutus' ? "style='background:url(/img/bg_aboutus.jpg) 190% 2% no-repeat;'":""; ?>>
    <div id="container">