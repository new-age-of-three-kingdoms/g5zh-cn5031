<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
// 会员统计用 $row['mb_cnt'];

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$connect_skin_url.'/style.css">', 0);
?>
<?php echo $row['total_cnt'] ?>
