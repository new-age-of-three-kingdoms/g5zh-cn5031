<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
//add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/main.bak.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/main.css">', 0);
list($w,$h)=explode(",",$options);
?>

<!-- <?php echo $bo_subject; ?> 最新文章开始 { -->
<div class="banner" id="b04">
	<ul>
        <?php for ($i=0; $i<count($list); $i++) {
			$thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $w, $h);
			
			if($thumb['src'])
				$img_content = '<img src="'.$thumb['src'].'" alt="'.$list[$i]['subject'].'" width="'.$w.'" height="'.$h.'">';
			else
				$img_content = '<span style="width:'.$w.'px;height:'.$h.'px">no image</span>';
		?>    
		<li class="slider-item"><a href="<?php echo $list[$i]['wr_link1']; ?>"><?php echo $img_content; ?><span class="slider-title"><em><?php //echo $list[$i]['subject']; ?></em></span></a></li>
		<?php }  ?>
		<?php if (count($list) == 0) { //没有内容时  ?>
		<li>没有主题内容可以显示</li>
		<?php }  ?>	        
	</ul>
	<div class="progress"></div>
	<a href="javascript:void(0);" class="unslider-arrow04 prev"><img class="arrow" id="al" src="<?php echo $latest_skin_url; ?>/img/arrowl.png" alt="prev" width="20" height="35"></a>
	<a href="javascript:void(0);" class="unslider-arrow04 next"><img class="arrow" id="ar" src="<?php echo $latest_skin_url; ?>/img/arrowr.png" alt="next" width="20" height="37"></a>
</div>
<div class="bottom-div"><img src="<?php echo $latest_skin_url; ?>/img/bottom-title.jpg" width="248" height="39"></div>

<!--<script src="<?php echo $latest_skin_url; ?>/jquery-1.11.1.min.js"></script>-->
<!--<script src="<?php echo $latest_skin_url; ?>/unslider.min.bak.js"></script>-->
<script src="<?php echo $latest_skin_url; ?>/unslider.min.js"></script>
<script>
$(document).ready(function(e) {
	var progress = $(".progress"),li_width = $("#b04 li").length;
    var unslider04 = $('#b04').unslider({
		dots: true,
		complete:function(index){//自己添加的，官方没有
			progress.animate({"width":(100/li_width)*(index+1)+"%"});
		}
	}),

	data04 = unslider04.data('unslider');
	$('.unslider-arrow04').click(function() {
        var fn = this.className.split(' ')[1];
        data04[fn]();
    });
});
</script>

<!-- } <?php echo $bo_subject; ?> 最新内容结束 -->