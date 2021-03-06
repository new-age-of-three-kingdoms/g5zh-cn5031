<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$poll_skin_url.'/style.css">', 0);
?>

<!-- 在线投票结果开始 { -->
<div id="poll_result" class="new_win">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <!-- 在线投票结果图表开始 { -->
    <section id="poll_result_list">
        <h2><?php echo $po_subject ?> 结果</h2>

        <dl>
            <dt><span>全部 <?php echo $nf_total_po_cnt ?>票</span></dt>
            <dd>
                <ol>
                <?php for ($i=1; $i<=count($list); $i++) {  ?>
                    <li>
                        <p>
                            <?php echo $list[$i]['content'] ?>
                            <strong><?php echo $list[$i]['cnt'] ?> 票</strong>
                            <span><?php echo number_format($list[$i]['rate'], 1) ?> 百分比</span>
                        </p>
                        <div class="poll_result_graph">
                            <span style="width:<?php echo number_format($list[$i]['rate'], 1) ?>%"></span>
                        </div>
                    </li>
                <?php }  ?>
                </ol>
            </dd>
        </dl>
    </section>
    <!-- } 在线投票结果图表结束 -->

    <!-- 在线投票其他意见开始 { -->
    <?php if ($is_etc) {  ?>
    <section id="poll_result_cmt">
        <h2>对此投票的其他评论</h2>

        <?php for ($i=0; $i<count($list2); $i++) {  ?>
        <article>
            <header>
                <h1><?php echo $list2[$i]['pc_name'] ?><span class="sound_only">的评论</span></h1>
                <?php echo $list2[$i]['name'] ?>
                <span class="poll_datetime"><?php echo $list2[$i]['datetime'] ?></span>
            </header>
            <p>
                <?php echo $list2[$i]['idea'] ?>
            </p>
            <footer>
                <span class="poll_cmt_del"><?php if ($list2[$i]['del']) { echo $list2[$i]['del']."删除</a>"; }  ?></span>
            </footer>
        </article>
        <?php }  ?>

        <?php if ($member['mb_level'] >= $po['po_level']) {  ?>
        <form name="fpollresult" action="./poll_etc_update.php" onsubmit="return fpollresult_submit(this);" method="post" autocomplete="off">
        <input type="hidden" name="po_id" value="<?php echo $po_id ?>">
        <input type="hidden" name="w" value="">
        <input type="hidden" name="skin_dir" value="<?php echo $skin_dir ?>">
        <?php if ($is_member) {  ?><input type="hidden" name="pc_name" value="<?php echo cut_str($member['mb_nick'],255) ?>"><?php }  ?>
        <h3><?php echo $po_etc ?></h3>

        <div class="tbl_frm01 tbl_wrap">
            <table id="poll_result_wcmt">
            <tbody>
            <?php if ($is_guest) {  ?>
            <tr>
                <th scope="row"><label for="pc_name">姓名<strong class="sound_only">必选项</strong></label></th>
                <td><input type="text" name="pc_name" id="pc_name" required class="frm_input required" size="10"></td>
            </tr>
            <?php }  ?>
            <tr>
                <th scope="row"><label for="pc_idea">评论<strong class="sound_only">必选项</strong></label></th>
                <td><input type="text" id="pc_idea" name="pc_idea" required class="frm_input required" size="47" maxlength="100"></td>
            </tr>
            <?php if ($is_guest) {  ?>
            <tr>
                <th scope="row">验证码</th>
                <td><?php echo captcha_html(); ?></td>
            </tr>
            <?php }  ?>
            </tbody>
            </table>
        </div>

        <div class="btn_confirm">
            <input type="submit" class="btn_submit" value="发表评论">
        </div>
        </form>
        <?php }  ?>

    </section>
    <?php }  ?>
    <!-- } 在线投票其他意见结束 -->

    <!-- 在线投票其他结果开始 { -->
    <aside id="poll_result_oth">
        <h2>查看其他投票结果</h2>
        <ul>
            <?php for ($i=0; $i<count($list3); $i++) {  ?>
            <li><a href="./poll_result.php?po_id=<?php echo $list3[$i]['po_id'] ?>&amp;skin_dir=<?php echo $skin_dir ?>">[<?php echo $list3[$i]['date'] ?>] <?php echo $list3[$i]['subject'] ?></a></li>
            <?php }  ?>
        </ul>
    </aside>
    <!-- } 查看其他投票结果结束 -->

    <div class="win_btn">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
</div>

<script>
$(function() {
    $(".poll_delete").click(function() {
        if(!confirm("删除投票评论内容吗？"))
            return false;
    });
});

function fpollresult_submit(f)
{
    <?php if ($is_guest) { echo chk_captcha_js(); }  ?>

    return true;
}
</script>
<!-- } 在线投票结果结束 -->