<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$new_skin_url.'/style.css">', 0);
?>

<!-- 全部主题搜索开始 { -->
<fieldset id="new_sch">
    <legend>详细搜索</legend>
    <form name="fnew" method="get">
    <?php echo $group_select ?>
    <label for="view" class="sound_only">搜索类型</label>
    <select name="view" id="view" onchange="select_change()">
        <option value="">所有主题
        <option value="w">仅主题
        <option value="c">仅评论
    </select>
    <input type="text" name="mb_id" value="<?php echo $mb_id ?>" id="mb_id" placeholder="关键词(必选项)" required class="frm_input required">
    <input type="submit" value="搜索" class="btn_submit">
    </form>
    <script>
    function select_change()
    {
        document.fnew.submit();
    }
    document.getElementById("gr_id").value = "<?php echo $gr_id ?>";
    document.getElementById("view").value = "<?php echo $view ?>";
    </script>
</fieldset>
<!-- } 全部主题搜索结束 -->

<!-- 全部主题目录开始 { -->
<div class="tbl_head01 tbl_wrap">
    <table id="new_tbl">
    <thead>
    <tr>
        <th scope="col">论坛</th>
        <th scope="col">主题</th>
        <th scope="col">时间</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $i<count($list); $i++)
    {
        $gr_subject = cut_str($list[$i]['gr_subject'], 20);
        $bo_subject = cut_str($list[$i]['bo_subject'], 20);
        $wr_subject = get_text(cut_str($list[$i]['wr_subject'], 80));
    ?>
    <tr>
        <td class="td_board"><a href="./board.php?bo_table=<?php echo $list[$i]['bo_table'] ?>"><?php echo $bo_subject ?></a></td>
        <td><a href="<?php echo $list[$i]['href'] ?>"><?php echo $list[$i]['comment'] ?><?php echo $wr_subject ?></a></td>
        <td class="td_date"><?php echo $list[$i]['datetime2'] ?></td>
    </tr>
    <?php } ?>

    <?php if ($i == 0)
        echo '<tr><td colspan="3" class="empty_table">没有主题内容可以显示</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<?php echo $write_pages ?>
<!-- } 全部主题目录结束 -->