<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="scrap" class="new_win mbskin">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <ul id="scrap_ul">
        <?php for ($i=0; $i<count($list); $i++) { ?>
        <li>
            <a href="<?php echo $list[$i]['opener_href'] ?>" target="_blank" class="scrap_board" onclick="opener.document.location.href='<?php echo $list[$i]['opener_href'] ?>'; return false;"><?php echo $list[$i]['bo_subject'] ?></a>
            <a href="<?php echo $list[$i]['opener_href_wr_id'] ?>" target="_blank" class="scrap_link" onclick="opener.document.location.href='<?php echo $list[$i]['opener_href_wr_id'] ?>'; return false;"><?php echo $list[$i]['subject'] ?></a>
            <a href="<?php echo $list[$i]['del_href']; ?>" class="scrap_del" onclick="del(this.href); return false;">删除</a>
        </li>
        <?php } ?>
        <?php if ($i == 0) echo "<li class=\"empty_list\">未找到相应信息</li>"; ?>
    </ul>

    <?php echo get_paging($config['cf_mobile_pages'], $page, $total_page, "?$qstr&amp;page="); ?>

    <div class="win_btn">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
</div>
