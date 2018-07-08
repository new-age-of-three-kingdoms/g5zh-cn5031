<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

global $is_admin;

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$visit_skin_url.'/style.css">', 0);
?>

<aside id="visit">
    <div>
        <h2>访问人数统计</h2>
        <dl>
            <dt>今天</dt>
            <dd><?php echo number_format($visit[1]) ?></dd>
            <dt>昨天</dt>
            <dd><?php echo number_format($visit[2]) ?></dd>
            <dt>最大</dt>
            <dd><?php echo number_format($visit[3]) ?></dd>
            <dt>全部</dt>
            <dd><?php echo number_format($visit[4]) ?></dd>
        </dl>
        <?php if ($is_admin == "super") { ?><a href="<?php echo G5_ADMIN_URL ?>/visit_list.php">查看详细</a><?php } ?>
    </div>
</aside>
