<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 收藏目录开始 { -->
<div id="scrap" class="new_win mbskin">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>收藏目录</caption>
        <thead>
        <tr>
            <th scope="col">序号</th>
            <th scope="col">论坛</th>
            <th scope="col">主题</th>
            <th scope="col">收藏时间</th>
            <th scope="col">删除</th>
        </tr>
        </thead>
        <tbody>
        <?php for ($i=0; $i<count($list); $i++) {  ?>
        <tr>
            <td class="td_num"><?php echo $list[$i]['num'] ?></td>
            <td class="td_board"><a href="<?php echo $list[$i]['opener_href'] ?>" target="_blank" onclick="opener.document.location.href='<?php echo $list[$i]['opener_href'] ?>'; return false;"><?php echo $list[$i]['bo_subject'] ?></a></td>
            <td><a href="<?php echo $list[$i]['opener_href_wr_id'] ?>" target="_blank" onclick="opener.document.location.href='<?php echo $list[$i]['opener_href_wr_id'] ?>'; return false;"><?php echo $list[$i]['subject'] ?></a></td>
            <td class="td_datetime"><?php echo $list[$i]['ms_datetime'] ?></td>
            <td class="td_mng"><a href="<?php echo $list[$i]['del_href'];  ?>" onclick="del(this.href); return false;">删除</a></td>
        </tr>
        <?php }  ?>

        <?php if ($i == 0) echo "<tr><td colspan=\"5\" class=\"empty_table\">未找到相应信息</td></tr>";  ?>
        </tbody>
        </table>
    </div>

    <?php echo get_paging($config['cf_write_pages'], $page, $total_page, "?$qstr&amp;page="); ?>

    <div class="win_btn">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
</div>
<!-- } 收藏目录结束 -->