<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="point" class="new_win">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <ul id="point_ul">
        <?php
        $sum_point1 = $sum_point2 = $sum_point3 = 0;

        $sql = " select *
                    {$sql_common}
                    {$sql_order}
                    limit {$from_record}, {$rows} ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            $point1 = $point2 = 0;
            if ($row['po_point'] > 0) {
                $point1 = '+' .number_format($row['po_point']);
                $sum_point1 += $row['po_point'];
            } else {
                $point2 = number_format($row['po_point']);
                $sum_point2 += $row['po_point'];
            }

            $po_content = $row['po_content'];

            $expr = '';
//            if($row['po_expired'] == 1)
                $expr = ' txt_expired';
        ?>
        <li>
            <div class="point_wrap01">
                <span class="point_date"><?php echo conv_date_format('y-m-d H时', $row['po_datetime']); ?></span>
                <span class="point_log"><?php echo $po_content; ?></span>
            </div>
            <div class="point_wrap02">
                <span class="point_expdate<?php echo $expr; ?>">
                    <?php if ($row['po_expired'] == 1) { ?>
                    截至<?php echo substr(str_replace('-', '', $row['po_expire_date']), 2); ?>
                    <?php } else echo $row['po_expire_date'] == '9999-12-31' ? '&nbsp;' : $row['po_expire_date']; ?>
                </span>
                <span class="point_inout"><?php if ($point1) echo $point1; else echo $point2; ?></span>
            </div>
        </li>
        <?php
        }

        if ($i == 0)
            echo '<li class="empty_list">未找到相应信息</li>';
        else {
            if ($sum_point1 > 0)
                $sum_point1 = "+" . number_format($sum_point1);
            $sum_point2 = number_format($sum_point2);
        }
        ?>
    </ul>

    <div id="point_sum">
        <div class="sum_row">
            <span class="sum_tit">支付</span>
            <b class="sum_val"><?php echo $sum_point1; ?></b>
        </div>
        <div class="sum_row">
            <span class="sum_tit">使用</span>
            <b class="sum_val"><?php echo $sum_point2; ?></b>
        </div>
        <div class="sum_row">
            <span class="sum_tit">拥有</span>
            <b class="sum_val"><?php echo number_format($member['mb_point']); ?></b>
        </div>
    </div>

    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr.'&amp;page='); ?>

    <div class="win_btn"><button type="button" onclick="javascript:window.close();">关闭窗口</button></div>
</div>