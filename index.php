<?php
define('_INDEX_', true);
include_once('./_common.php');

// 首页初始画面路径设置 : 请勿随意更改此段代码
if ($config['cf_include_index'] && is_file(G5_PATH.'/'.$config['cf_include_index'])) {
    include_once(G5_PATH.'/'.$config['cf_include_index']);
    return; // 不执行以下代码
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_PATH.'/head_index.php');
?>

<h2 class="sound_only">最新文章</h2>
<!-- 最新文章开始 { -->
    <div style="width:960px; height:740px;">
		<?php echo latest("brand", "brand", 6, 25, 1, "300,240"); ?>
    </div>
    <div style="width:960px; height:740px;">
		<?php echo latest("brand", "food", 6, 25, 1, "300,240"); ?>
    </div>
    <div style="width:960px; height:740px;">
		<?php echo latest("brand", "space", 6, 25, 1, "300,240"); ?>
    </div>
    
    <div style="width:960px; height:455px;">
        <?php echo latest("banner", "banner", 12, 25, 1, "154,164"); ?>
    </div>
<!-- } 最新内容结束 -->

<?php
include_once(G5_PATH.'/tail_index.php');
?>
