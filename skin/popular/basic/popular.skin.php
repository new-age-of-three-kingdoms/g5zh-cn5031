<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$popular_skin_url.'/style.css">', 0);
?>

<!-- 热门关键词开始 { -->
<section id="popular">
    <div>
        <h2>热门关键词</h2>
        <ul>
        <?php for ($i=0; $i<count($list); $i++) {  ?>
            <li><a href="<?php echo G5_BBS_URL ?>/search.php?sfl=wr_subject&amp;sop=and&amp;stx=<?php echo urlencode($list[$i]['pp_word']) ?>"><?php echo $list[$i]['pp_word'] ?></a></li>
        <?php }  ?>
        </ul>
    </div>
</section>
<!-- } 热门关键词结束 -->