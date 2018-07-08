<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$search_skin_url.'/style.css">', 0);
?>

<!-- 全站搜索开始 { -->
<form name="fsearch" onsubmit="return fsearch_submit(this);" method="get">
<input type="hidden" name="srows" value="<?php echo $srows ?>">
<fieldset id="sch_res_detail">
    <legend>详细搜索</legend>
    <?php echo $group_select ?>
    <script>document.getElementById("gr_id").value = "<?php echo $gr_id ?>";</script>

    <label for="sfl" class="sound_only">搜索条件</label>
    <select name="sfl" id="sfl">
        <option value="wr_subject||wr_content"<?php echo get_selected($_GET['sfl'], "wr_subject||wr_content") ?>>标题+内容</option>
        <option value="wr_subject"<?php echo get_selected($_GET['sfl'], "wr_subject") ?>>主题</option>
        <option value="wr_content"<?php echo get_selected($_GET['sfl'], "wr_content") ?>>内容</option>
        <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id") ?>>会员ID</option>
        <option value="wr_name"<?php echo get_selected($_GET['sfl'], "wr_name") ?>>姓名</option>
    </select>

    <label for="stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
    <input type="text" name="stx" value="<?php echo $text_stx ?>" id="stx" required class="frm_input required" maxlength="20">
    <input type="submit" class="btn_submit" value="搜索">

    <script>
    function fsearch_submit(f)
    {
        if (f.stx.value.length < 2) {
            alert("请至少输入两个字以上关键词进行搜索");
            f.stx.select();
            f.stx.focus();
            return false;
        }

        // 如果产生负荷请注释以下代码
        var cnt = 0;
        for (var i=0; i<f.stx.value.length; i++) {
            if (f.stx.value.charAt(i) == ' ')
                cnt++;
        }

        if (cnt > 1) {
            alert("为了精确搜索请勿使用多个空格联想条件");
            f.stx.select();
            f.stx.focus();
            return false;
        }

        f.action = "";
        return true;
    }
    </script>
    <input type="radio" value="or" <?php echo ($sop == "or") ? "checked" : ""; ?> id="sop_or" name="sop">
    <label for="sop_or">OR</label>
    <input type="radio" value="and" <?php echo ($sop == "and") ? "checked" : ""; ?> id="sop_and" name="sop">
    <label for="sop_and">AND</label>
</fieldset>
</form>

<div id="sch_result">

    <?php
    if ($stx) {
        if ($board_count) {
    ?>
    <section id="sch_res_ov">
        <h2><?php echo $stx ?> 全站搜索结果</h2>
        <dl>
            <dt>论坛</dt>
            <dd><strong class="sch_word"><?php echo $board_count ?>个</strong></dd>
            <dt>主题</dt>
            <dd><strong class="sch_word"><?php echo number_format($total_count) ?>个</strong></dd>
        </dl>
        <p><?php echo number_format($page) ?>/<?php echo number_format($total_page) ?> 浏览页面</p>
    </section>
    <?php
        }
    }
    ?>

    <?php
    if ($stx) {
        if ($board_count) {
     ?>
    <ul id="sch_res_board">
        <li><a href="?<?php echo $search_query ?>&amp;gr_id=<?php echo $gr_id ?>" <?php echo $sch_all ?>>全部论坛</a></li>
        <?php echo $str_board_list; ?>
    </ul>
    <?php
        } else {
     ?>
    <div class="empty_list">未找到关联查询内容</div>
    <?php } }  ?>

    <hr>

    <?php if ($stx && $board_count) { ?><section class="sch_res_list"><?php }  ?>
    <?php
    $k=0;
    for ($idx=$table_index, $k=0; $idx<count($search_table) && $k<$rows; $idx++) {
     ?>
        <h2><a href="./board.php?bo_table=<?php echo $search_table[$idx] ?>&amp;<?php echo $search_query ?>"><?php echo $bo_subject[$idx] ?> 论坛内搜索结果</a></h2>
        <ul>
        <?php
        for ($i=0; $i<count($list[$idx]) && $k<$rows; $i++, $k++) {
            if ($list[$idx][$i]['wr_is_comment'])
            {
                $comment_def = '<span class="cmt_def">评论 | </span>';
                $comment_href = '#c_'.$list[$idx][$i]['wr_id'];
            }
            else
            {
                $comment_def = '';
                $comment_href = '';
            }
         ?>

            <li>
                <a href="<?php echo $list[$idx][$i]['href'] ?><?php echo $comment_href ?>" class="sch_res_title"><?php echo $comment_def ?><?php echo $list[$idx][$i]['subject'] ?></a>
                <a href="<?php echo $list[$idx][$i]['href'] ?><?php echo $comment_href ?>" target="_blank">新窗口</a>
                <p><?php echo $list[$idx][$i]['content'] ?></p>
                <?php echo $list[$idx][$i]['name'] ?>
                <span class="sch_datetime"><?php echo $list[$idx][$i]['wr_datetime'] ?></span>
            </li>
        <?php }  ?>
        </ul>
        <div class="sch_more"><a href="./board.php?bo_table=<?php echo $search_table[$idx] ?>&amp;<?php echo $search_query ?>"><strong><?php echo $bo_subject[$idx] ?></strong> 查看更多搜索结果</a></div>

        <hr>
    <?php }  ?>
    <?php if ($stx && $board_count) {  ?></section><?php }  ?>

    <?php echo $write_pages ?>

</div>
<!-- } 全局搜索结束 -->