<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="memo_list" class="new_win mbskin">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <ul class="win_ul">
        <li><a href="./memo.php?kind=recv">收件箱</a></li>
        <li><a href="./memo.php?kind=send">发件箱</a></li>
        <li><a href="./memo_form.php">写短信</a></li>
    </ul>

    <div class="win_desc">
        全部 <?php echo $kind_title ?>短信 <?php echo $total_count ?>封<br>
    </div>

    <ul id="memo_list_ul">
        <?php for ($i=0; $i<count($list); $i++) { ?>
        <li>
            <a href="<?php echo $list[$i]['view_href'] ?>" class="memo_link"><?php echo $list[$i]['send_datetime'] ?>收到的短信</a>
            <span class="memo_read"><?php echo $list[$i]['read_datetime'] ?></span>
            <span class="memo_send"><?php echo $list[$i]['name'] ?></span>
            <a href="<?php echo $list[$i]['del_href'] ?>" onclick="del(this.href); return false;" class="memo_del">删除</a>
        </li>
        <?php } ?>
        <?php if ($i==0) { echo "<li class=\"empty_list\">未找到相应信息</li>"; } ?>
    </ul>

    <p class="win_desc">
        在内短信保存时间为 <strong><?php echo $config['cf_memo_del'] ?></strong>天
    </p>

    <div class="win_btn">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
</div>