<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$connect_skin_url.'/style.css">', 0);
?>

<!-- 当前在线人数目录开始 { -->
<div class="tbl_head01 tbl_wrap">
    <table id="current_connect_tbl">
    <thead>
    <tr>
        <th scope="col">序号</th>
        <th scope="col">姓名</th>
        <th scope="col">位置</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $i<count($list); $i++) {
        //$location = conv_content($list[$i]['lo_location'], 0);
        $location = $list[$i]['lo_location'];
        // 仅限网站管理员权限
        // 请勿修改此权限
        if ($list[$i]['lo_url'] && $is_admin == 'super') $display_location = "<a href=\"".$list[$i]['lo_url']."\">".$location."</a>";
        else $display_location = $location;
    ?>
        <tr>
            <td class="td_num"><?php echo $list[$i]['num'] ?></td>
            <td class="td_name"><?php echo $list[$i]['name'] ?></td>
            <td><?php echo $display_location ?></td>
        </tr>
    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan=\"3\" class=\"empty_table\">当前没有访问用户</td></tr>";
    ?>
    </tbody>
    </table>
</div>
<!-- } 当前在线人数目录结束 -->