<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

$sql = " select * from {$g5['new_win_table']}
          where '".G5_TIME_YMDHIS."' between nw_begin_time and nw_end_time
            and nw_device IN ( 'both', 'mobile' )
          order by nw_id asc ";
$result = sql_query($sql, false);
?>

<!-- 弹出层开始 { -->
<div id="hd_pop">
    <h2>弹出层通知</h2>

<?php
for ($i=0; $row_nw=sql_fetch_array($result); $i++)
{
    // 如果已经选择 Continue
    if ($_COOKIE["hd_pops_{$row_nw['nw_id']}"])
        continue;

    $sql = " select * from {$g5['new_win_table']} where nw_id = '{$row_nw['nw_id']}' ";
    $nw = sql_fetch($sql);
?>

    <div id="hd_pops_<?php echo $nw['nw_id'] ?>" class="hd_pops" style="top:<?php echo $nw['nw_top']?>px;left:<?php echo $nw['nw_left']?>px;">
        <div class="hd_pops_con" style="width:<?php echo $nw['nw_width'] ?>px;height:<?php echo $nw['nw_height'] ?>px">
            <?php echo conv_content($nw['nw_content'], 1); ?>
        </div>
        <div class="hd_pops_footer">
            <button class="hd_pops_reject hd_pops_<?php echo $nw['nw_id']; ?> <?php echo $nw['nw_disable_hours']; ?>"><strong><?php echo $nw['nw_disable_hours']; ?></strong>小时以内不再显示</button>
            <button class="hd_pops_close hd_pops_<?php echo $nw['nw_id']; ?>">关闭</button>
        </div>
    </div>
<?php }
if ($i == 0) echo '<span class="sound_only">无弹出层内容</span>';
?>
</div>

<script>
$(function() {
    $(".hd_pops_reject").click(function() {
        var id = $(this).attr('class').split(' ');
        var ck_name = id[1];
        var exp_time = parseInt(id[2]);
        $("#"+id[1]).css("display", "none");
        set_cookie(ck_name, 1, exp_time, g5_cookie_domain);
    });
    $('.hd_pops_close').click(function() {
        var idb = $(this).attr('class').split(' ');
        $('#'+idb[1]).css('display','none');
    });
});
</script>
<!-- } 弹出层结束 -->