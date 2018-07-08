<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 短信目录开始 { -->
<div id="memo_list" class="new_win mbskin">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <ul class="win_ul">
        <li><a href="./memo.php?kind=recv">收件箱</a></li>
        <li><a href="./memo.php?kind=send">发件箱</a></li>
        <li><a href="./memo_form.php">写短信</a></li>
    </ul>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>
            全部 <?php echo $kind_title ?>短信 <?php echo $total_count ?>封<br>
        </caption>
        <thead>
        <tr>
            <th scope="col"><?php echo  ($kind == "recv") ? "发件人" : "收件人";  ?></th>
            <th scope="col">发送时间</th>
            <th scope="col">浏览时间</th>
            <th scope="col">管理</th>
        </tr>
        </thead>
        <tbody>
        <?php for ($i=0; $i<count($list); $i++) {  ?>
        <tr>
            <td><?php echo $list[$i]['name'] ?></td>
            <td class="td_datetime"><a href="<?php echo $list[$i]['view_href'] ?>"><?php echo $list[$i]['send_datetime'] ?></a></td>
            <td class="td_datetime"><a href="<?php echo $list[$i]['view_href'] ?>"><?php echo $list[$i]['read_datetime'] ?></a></td>
            <td class="td_mng"><a href="<?php echo $list[$i]['del_href'] ?>" onclick="del(this.href); return false;">删除</a></td>
        </tr>
        <?php }  ?>
        <?php if ($i==0) { echo '<tr><td colspan="4" class="empty_table">未找到相应信息</td></tr>'; }  ?>
        </tbody>
        </table>
    </div>

    <p class="win_desc">
        在内短信保存时间为 <strong><?php echo $config['cf_memo_del'] ?></strong>天
    </p>

    <div class="win_btn">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
</div>
<!-- } 短信目录结束 -->