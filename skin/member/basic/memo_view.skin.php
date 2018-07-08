<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
$nick = get_sideview($mb['mb_id'], $mb['mb_nick'], $mb['mb_email'], $mb['mb_homepage']);
if($kind == "recv") {
    $kind_str = "发件";
    $kind_date = "收件";
}
else {
    $kind_str = "收件";
    $kind_date = "发件";
}

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 浏览短信开始{ -->
<div id="memo_view" class="new_win mbskin">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <!-- 短信信箱选择开始 { -->
    <ul class="win_ul">
        <li><a href="./memo.php?kind=recv">收件箱</a></li>
        <li><a href="./memo.php?kind=send">发件箱</a></li>
        <li><a href="./memo_form.php">写短信</a></li>
    </ul>
    <!-- } 短信信箱选择结束 -->

    <article id="memo_view_contents">
        <header>
            <h1>短信内容</h1>
        </header>
        <ul id="memo_view_ul">
            <li class="memo_view_li">
                <span class="memo_view_subj"><?php echo $kind_str ?>人</span>
                <strong><?php echo $nick ?></strong>
            </li>
            <li class="memo_view_li">
                <span class="memo_view_subj"><?php echo $kind_date ?>时间</span>
                <strong><?php echo $memo['me_send_datetime'] ?></strong>
            </li>
        </ul>
        <p>
            <?php echo conv_content($memo['me_memo'], 0) ?>
        </p>
    </article>

    <div class="win_btn">
        <?php if($prev_link) {  ?>
        <a href="<?php echo $prev_link ?>">上一条</a>
        <?php }  ?>
        <?php if($next_link) {  ?>
        <a href="<?php echo $next_link ?>">下一条</a>
        <?php }  ?>
        <?php if ($kind == 'recv') {  ?><a href="./memo_form.php?me_recv_mb_id=<?php echo $mb['mb_id'] ?>&amp;me_id=<?php echo $memo['me_id'] ?>">回复</a><?php }  ?>
        <a href="./memo.php?kind=<?php echo $kind ?>">查看目录</a>
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
</div>
<!-- } 浏览短信结束 -->