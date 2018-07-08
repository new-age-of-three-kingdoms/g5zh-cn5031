<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
list($w,$h)=explode(",",$options);
?>

<!-- <?php echo $bo_subject; ?> 最新文章开始 { -->
<div class="lt">
    <div class="latest_title"><span class="left"><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>"><img src="<?php echo $latest_skin_url; ?>/img/latest_title_<?php echo $bo_table; ?>.png" /></a></span><span class="right"><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>"><img src="<?php echo $latest_skin_url; ?>/img/more.png" /></a><img src="<?php echo $latest_skin_url; ?>/img/more_icon.png" /></span></div>
    <ul>
    <?php for ($i=0; $i<count($list); $i++) {  
	    
		$thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $w, $h);
		if ($i%3==0)
		    $class = "left";
		else if($i%3==2)
		    $class = "right";
		else
		    $class = "";
            
        if($thumb['src'])
            $img_content = '<img src="'.$thumb['src'].'" alt="'.$list[$i]['subject'].'" width="'.$w.'" height="'.$h.'">';
        else
            $img_content = '<span style="width:'.$w.'px;height:'.$h.'px">no image</span>';
	?>
        <li class="<?php echo $class ?>" >
            <?php
            echo "<a href='".$list[$i]['href']."'>".$img_content."</a>";
            echo "<div class='title'><a href='".$list[$i]['href']."'>";
            echo $list[$i]['subject'];
			echo "</a>";
			echo "<span class='date'>".date("Y.m", strtotime($list[$i]['wr_datetime']))."</span></div>";
			echo "<div class='title2'>".$list[$i]['wr_1']."</div>";
            ?>
        </li>
    <?php }  ?>
    <?php if (count($list) == 0) { //没有内容时  ?>
    <li>没有主题内容可以显示</li>
    <?php }  ?>
    </ul>
</div>
<!-- } <?php echo $bo_subject; ?> 最新内容结束 -->