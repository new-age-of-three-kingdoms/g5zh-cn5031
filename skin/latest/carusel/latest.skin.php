<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
//add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/normalize.css">', 0);
//add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/demo.css">', 1);
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/app.css">', 0);
list($w,$h)=explode(",",$options);
?>

<!-- <?php echo $bo_subject; ?> 最新文章开始 { -->
<div class="slider">
	<div class="slider-img">
		<ul class="slider-img-ul">
        <?php for ($i=0; $i<count($list); $i++) {
			$thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $w, $h);
			
			if($thumb['src'])
				$img_content = '<img src="'.$thumb['src'].'" alt="'.$list[$i]['subject'].'" width="'.$w.'" height="'.$h.'">';
			else
				$img_content = '<span style="width:'.$w.'px;height:'.$h.'px">no image</span>';
		?>			 
			<li><a href='<?php echo $list[$i]['href']?>'><?php echo $img_content; ?></a></li>
		<?php }  ?>
		<?php if (count($list) == 0) { //没有内容时  ?>
		<li>没有主题内容可以显示</li>
		<?php }  ?>			
		</ul>
	</div>
</div>
<!--<script src="<?php echo $latest_skin_url; ?>/jquery-1.11.0.min.js" type="text/javascript"></script>-->
<script type="text/javascript" src="<?php echo $latest_skin_url; ?>/xSlider.js"></script>

<!-- } <?php echo $bo_subject; ?> 最新内容结束 -->