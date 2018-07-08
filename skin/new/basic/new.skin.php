<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

// 根据选项导致表格变化
$colspan = 5;

if ($is_admin) $colspan++;

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$new_skin_url.'/style.css">', 0);
?>

<!-- 全部主题搜索开始 { -->
<fieldset id="new_sch">
    <legend>详细搜索</legend>
    <form name="fnew" method="get">
    <?php echo $group_select ?>
    <label for="view" class="sound_only">搜索类型</label>
    <select name="view" id="view">
        <option value="">所有主题
        <option value="w">仅主题
        <option value="c">仅评论
    </select>
    <label for="mb_id" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
    <input type="text" name="mb_id" value="<?php echo $mb_id ?>" id="mb_id" required class="frm_input required">
    <input type="submit" value="搜索" class="btn_submit">
    <p>可以搜索会员ID</p>
    </form>
    <script>
    /* 解除选择窗移动
    function select_change()
    {
        document.fnew.submit();
    }
    */
    document.getElementById("gr_id").value = "<?php echo $gr_id ?>";
    document.getElementById("view").value = "<?php echo $view ?>";
    </script>
</fieldset>
<!-- } 全部主题搜索结束 -->

<!-- 全部主题目录开始 { -->
<form name="fnewlist" method="post" action="#" onsubmit="return fnew_submit(this);">
<input type="hidden" name="sw"       value="move">
<input type="hidden" name="view"     value="<?php echo $view; ?>">
<input type="hidden" name="sfl"      value="<?php echo $sfl; ?>">
<input type="hidden" name="stx"      value="<?php echo $stx; ?>">
<input type="hidden" name="srows"    value="<?php echo $srows; ?>">
<input type="hidden" name="bo_table" value="<?php echo $bo_table; ?>">
<input type="hidden" name="page"     value="<?php echo $page; ?>">
<input type="hidden" name="pressed"  value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <thead>
    <tr>
        <?php if ($is_admin) { ?>
        <th scope="col">
            <label for="all_chk" class="sound_only">全部目录</label>
            <input type="checkbox" id="all_chk">
        </th>
        <?php } ?>
        <th scope="col">群组</th>
        <th scope="col">论坛</th>
        <th scope="col">主题</th>
        <th scope="col">姓名</th>
        <th scope="col">时间</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $i<count($list); $i++)
    {
        $num = $total_count - ($page - 1) * $config['cf_page_rows'] - $i;
        $gr_subject = cut_str($list[$i]['gr_subject'], 20);
        $bo_subject = cut_str($list[$i]['bo_subject'], 20);
        $wr_subject = get_text(cut_str($list[$i]['wr_subject'], 80));
    ?>
    <tr>
        <?php if ($is_admin) { ?>
        <td class="td_chk">
            <label for="chk_bn_id_<?php echo $i; ?>" class="sound_only"><?php echo $num?>号</label>
            <input type="checkbox" name="chk_bn_id[]" value="<?php echo $i; ?>" id="chk_bn_id_<?php echo $i; ?>">
            <input type="hidden" name="bo_table[<?php echo $i; ?>]" value="<?php echo $list[$i]['bo_table']; ?>">
            <input type="hidden" name="wr_id[<?php echo $i; ?>]" value="<?php echo $list[$i]['wr_id']; ?>">
        </td>
        <?php } ?>
        <td class="td_group"><a href="./new.php?gr_id=<?php echo $list[$i]['gr_id'] ?>"><?php echo $gr_subject ?></a></td>
        <td class="td_board"><a href="./board.php?bo_table=<?php echo $list[$i]['bo_table'] ?>"><?php echo $bo_subject ?></a></td>
        <td><a href="<?php echo $list[$i]['href'] ?>"><?php echo $list[$i]['comment'] ?><?php echo $wr_subject ?></a></td>
        <td class="td_name"><div><?php echo $list[$i]['name'] ?></div></td>
        <td class="td_date"><?php echo $list[$i]['datetime2'] ?></td>
    </tr>
    <?php }  ?>

    <?php if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">没有主题内容可以显示</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<?php if ($is_admin) { ?>
<div class="sir_bw02 sir_bw">
    <input type="submit" onclick="document.pressed=this.value" value="删除所选" class="btn_submit">
</div>
<?php } ?>
</form>

<?php if ($is_admin) { ?>
<script>
$(function(){
    $('#all_chk').click(function(){
        $('[name="chk_bn_id[]"]').attr('checked', this.checked);
    });
});

function fnew_submit(f)
{
    f.pressed.value = document.pressed;

    var cnt = 0;
    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_bn_id[]" && f.elements[i].checked)
            cnt++;
    }

    if (!cnt) {
        alert("请选择需要"+document.pressed+"内容");
        return false;
    }

    if (!confirm("确定将所内容进行"+document.pressed+"操作吗？\n\n一旦执行此操作将无法进行恢复")) {
        return false;
    }

    f.action = "./new_delete.php";

    return true;
}
</script>
<?php } ?>

<?php echo $write_pages ?>
<!-- } 全部主题目录结束 -->