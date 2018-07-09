<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
list($w,$h)=explode(",",$options);
?>

<!-- <?php echo $bo_subject; ?> 最新文章开始 { -->
<div class="bann">
    <ul>
    <?php for ($i=0; $i<count($list); $i++) {  
	    
		$thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $w, $h);
		if ($i%6==0)
		    $class = "left";
		else if($i%6==5)
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
            ?>
        </li>
    <?php }  ?>
    <?php if (count($list) == 0) { //没有内容时  ?>
    <li>没有主题内容可以显示</li>
    <?php }  ?>
    </ul>
</div>
<!-- } <?php echo $bo_subject; ?> 最新内容结束 -->