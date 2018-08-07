<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<!-- 论坛版块目录开始 { -->
<div id="bo_gall" style="width:<?php echo $width; ?>">

    <?php if ($is_category) { ?>
    <nav id="bo_cate">
        <h2><?php echo $board['bo_subject'] ?> 分类</h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
    <?php } ?>

    <div class="bo_fx">
        <div id="bo_list_total">
            <img src="<?php echo $board_skin_url; ?>/img/board_title_<?php echo $bo_table; ?>.jpg" height="24" />
        </div>

        <?php if ($rss_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01">RSS</a></li><?php } ?>
            <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin">管理员</a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">发表主题</a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>

    <form name="fboardlist"  id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <?php if ($is_checkbox) { ?>
    <div id="gall_allchk">
        <label for="chkall" class="sound_only">当前所有主题</label>
        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
    </div>
    <?php } ?>

    <ul id="gall_ul">
        <?php for ($i=0; $i<count($list); $i++) {
            if($i>0 && ($i % $bo_gallery_cols == 0))
                $style = 'clear:both;';
            else
                $style = '';
            if ($i == 0) $k = 0;
            $k += 1;
            if ($k % $bo_gallery_cols == 0) $style .= "margin:0 !important;";
			
			if ($i%3==0)
				$class = "gall_li_left";
			else if($i%3==2)
				$class = "gall_li_right";
			else
				$class = "gall_li";			
         ?>
        <li class="<?php echo $class; ?> <?php if ($wr_id == $list[$i]['wr_id']) { ?>gall_now<?php } ?>">
            <?php if ($is_checkbox) { ?>
            <label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
            <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
            <?php } ?>
            <span class="sound_only">
                <?php
                if ($wr_id == $list[$i]['wr_id'])
                    echo "<span class=\"bo_current\">浏览中</span>";
                else
                    echo $list[$i]['num'];
                 ?>
            </span>
            <ul class="gall_con">
                <li class="gall_href">
<<<<<<< HEAD
                <div class="grid">
                <figure class="effect-layla">
                    <?php
					$thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);

					if($thumb['src']) {
						$img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$board['bo_gallery_width'].'" height="'.$board['bo_gallery_height'].'">';
					} else {
						$img_content = '<div style="width:'.$board['bo_gallery_width'].'px;height:'.$board['bo_gallery_height'].'px">no image</div>';
					}
					echo $img_content;
                    ?>
                    <figcaption>
                        <h2><?php echo $list[$i]['subject'] ?></h2>
                        <p><?php echo $list[$i]['wr_1'] ?> <br /> <?php echo date("Y.m", strtotime($list[$i]['wr_datetime'])) ?></p>
                        <a href="<?php echo $list[$i]['href'] ?>" target="_blank">View more</a>
                    </figcaption>		
                </figure>
                </div>
=======
                    <a href="<?php echo $list[$i]['href'] ?>">
                    <?php
                    if ($list[$i]['is_notice']) { // 公告  ?>
                        <strong style="width:<?php echo $board['bo_gallery_width'] ?>px;height:<?php echo $board['bo_gallery_height'] ?>px">公告</strong>
                    <?php } else {
                        $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);

                        if($thumb['src']) {
                            $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$board['bo_gallery_width'].'" height="'.$board['bo_gallery_height'].'">';
                        } else {
                            $img_content = '<div style="width:'.$board['bo_gallery_width'].'px;height:'.$board['bo_gallery_height'].'px">no image</div>';
                        }

                        echo $img_content;
                    }
                     ?>
                    </a>
>>>>>>> parent of bd06081... mid-image style mod
                </li>
                <li class="gall_text_href" style="width:<?php echo $board['bo_gallery_width'] ?>px">
                    <?php
                    // echo $list[$i]['icon_reply']; 相册将不使用主题回复Replay功能 - 子云爸爸 2013-03-04
                    if ($is_category && $list[$i]['ca_name']) {
                     ?>
                    <a href="<?php echo $list[$i]['ca_name_href'] ?>" class="bo_cate_link"><?php echo $list[$i]['ca_name'] ?></a>
                    <?php } ?>
                    <a href="<?php echo $list[$i]['href'] ?>">
                        <?php echo $list[$i]['subject'] ?>
                    </a>
                    <span class="gall_date"><?php echo date("Y.m", strtotime($list[$i]['wr_datetime'])) ?></span>
                </li>
                <li class="gall_subtitle"><?php echo $list[$i]['wr_1'] ?></li>
                
            </ul>
        </li>
        <?php } ?>
        <?php if (count($list) == 0) { echo "<li class=\"empty_list\">没有主题内容可以显示</li>"; } ?>
    </ul>

    <?php if ($list_href || $is_checkbox || $write_href) { ?>
    <div class="bo_fx">
        <?php if ($is_checkbox) { ?>
        <ul class="btn_bo_adm">
            <li><input type="submit" name="btn_submit" value="删除所选" onclick="document.pressed=this.value"></li>
            <li><input type="submit" name="btn_submit" value="复制所选" onclick="document.pressed=this.value"></li>
            <li><input type="submit" name="btn_submit" value="移动所选" onclick="document.pressed=this.value"></li>
        </ul>
        <?php } ?>

        <?php if ($list_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($list_href) { ?><li><a href="<?php echo $list_href ?>" class="btn_b01">目录</a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">发表主题</a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <?php } ?>
    </form>
</div>

<?php if($is_checkbox) { ?>
<noscript>
<p>浏览器中屏蔽javascript代码运行时<br>将不会出现操作提示直接执行所选操作，请务必谨慎操作！</p>
</noscript>
<?php } ?>

<!-- 页 -->
<?php echo $write_pages;  ?>

<!-- 搜索主题开始 { -->
<!-- } 搜索主题结束 -->

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert("请选择需要"+document.pressed+"内容");
        return false;
    }

    if(document.pressed == "复制所选") {
        select_copy("copy");
        return;
    }

    if(document.pressed == "移动所选") {
        select_copy("move");
        return;
    }

    if(document.pressed == "删除所选") {
        if (!confirm("确定删除所选内容吗？\n\n删除后的内容将无法进行恢复\n\n如主题包含评论内容\n只有在选择的情况下才能删除主题"))
            return false;

        f.removeAttribute("target");
        f.action = "./board_list_update.php";
    }

    return true;
}

// 所选主题复制及移动
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == 'copy')
        str = "复制";
    else
        str = "移动";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = "./move.php";
    f.submit();
}
</script>
<?php } ?>
<!-- } 论坛版块目录结束 -->
