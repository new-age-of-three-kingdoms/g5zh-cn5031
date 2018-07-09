<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
?>
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
            <a href="<?php echo G5_URL ?>"><img src="<?php echo G5_IMG_URL ?>/logo_146x57.png" width="146" height="57" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>
        
        <ul id="gnb_1dul">
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_BBS_URL ?>/content.php?co_id=aboutus" class="gnb_1da">关于我们<span>About Us</span></a>
            </li>
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=brand" class="gnb_1da">品牌设计<span>Branding</span></a>
            </li>
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=ci" class="gnb_1da">企业形象 & 视觉<span>CI & Portfolio</span></a>
            </li>
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=food" class="gnb_1da">餐饮拍照<span>Food Photos</span></a>
            </li>
            <li class="gnb_1dli" style="z-index:9;">
                <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=space" class="gnb_1da">空间设计<span>Space</span></a>
            </li>
        </ul>
    </nav>

    <hr>
</div>


<div id="main_carusel">
	<?php echo latest("slider", "slide", 6, 25, 1, "960,410"); ?>
	<?php //echo latest("slider", "slide", 6, 25, 1, "640,480"); ?>
</div>