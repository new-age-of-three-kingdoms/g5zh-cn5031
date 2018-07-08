<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
?>

<div class="lt">
    <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>" class="lt_title"><strong><?php echo $bo_subject ?></strong></a>
    <ul>
    <?php for ($i=0; $i<count($list); $i++) { ?>
        <li>
            <?php
            //echo $list[$i]['icon_reply']." ";
            echo "<a href=\"".$list[$i]['href']."\">";
            if ($list[$i]['is_notice'])
                echo "<strong>".$list[$i]['subject']."</strong>";
            else
                echo $list[$i]['subject'];

            if ($list[$i]['comment_cnt'])
                echo " <span class=\"cnt_cmt\">".$list[$i]['comment_cnt']."</span>";

                // if ($list[$i]['link']['count']) { echo "[{$list[$i]['link']['count']}]"; }
                // if ($list[$i]['file']['count']) { echo "<{$list[$i]['file']['count']}>"; }

                if (isset($list[$i]['icon_new']))    echo " " . $list[$i]['icon_new'];
                if (isset($list[$i]['icon_hot']))    echo " " . $list[$i]['icon_hot'];
                if (isset($list[$i]['icon_file']))   echo " " . $list[$i]['icon_file'];
                if (isset($list[$i]['icon_link']))   echo " " . $list[$i]['icon_link'];
                if (isset($list[$i]['icon_secret'])) echo " " . $list[$i]['icon_secret'];

            echo "</a>";

            ?>
        </li>
    <?php } ?>
    <?php if (count($list) == 0) { //没有内容时 ?>
    <li>没有主题内容可以显示</li>
    <?php } ?>
    </ul>
    <div class="lt_more"><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>"><span class="sound_only"><?php echo $bo_subject ?></span>更多</a></div>
</div>
