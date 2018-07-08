<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

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
<div id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div id="skip_to_container"><a href="#container">浏览全文</a></div>

    <?php
    if(defined('_INDEX_')) { // 仅在index页面执行
        include G5_BBS_PATH.'/newwin.inc.php'; // 弹出层
    }
    ?>

    <div id="hd_wrapper">
        <ul id="tnb">
            <li><img src="<?php echo G5_IMG_URL ?>/tnb1.jpg" width="65" height="18" /></li>
            <li><img src="<?php echo G5_IMG_URL ?>/tnb2.jpg" width="20" height="18" /></li>
        </ul>
    </div>

    <nav id="gnb">
        <div id="logo">
            <a href="<?php echo G5_URL ?>"><img src="<?php echo G5_IMG_URL ?>/logo_146x57.png" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>
        
        <ul id="gnb_1dul">
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_URL ?>" class="gnb_1da">关于我们<span>About Us</span></a>
            </li>
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_URL ?>" class="gnb_1da">品牌设计<span>Branding</span></a>
            </li>
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_URL ?>" class="gnb_1da">企业形象 & 视觉<span>CI & Portfoli</span></a>
            </li>
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_URL ?>" class="gnb_1da">餐饮拍照<span>Food Photos</span></a>
            </li>
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_URL ?>" class="gnb_1da">空间设计<span>Space</span></a>
            </li>
        </ul>
    </nav>

    <hr>
</div>
<!-- } 顶部结束 -->

<hr>

<!-- 项目开始 { -->
<div id="wrapper">
    <div id="aside">
    </div>
    <div id="container">
        <?php if ((!$bo_table || $w == 's' ) && !defined("_INDEX_")) { ?><div id="container_title"><?php echo $g5['title'] ?></div><?php } ?>
