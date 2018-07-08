<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// 如有使用者定义的tail.sub.php文件就进行include
if(defined('G5_TAIL_SUB_FILE') && is_file(G5_PATH.'/'.G5_TAIL_SUB_FILE)) {
    include_once(G5_PATH.'/'.G5_TAIL_SUB_FILE);
    return;
}
?>

<?php if ($is_admin == 'super') {  ?><!-- <div style='float:left; text-align:center;'>RUN TIME : <?php echo get_microtime()-$begin_time; ?><br></div> --><?php }  ?>

<!-- 修改IE6,IE7中部分sideview遮挡栏目的问题 -->
<!--[if lte IE 7]>
<script>
$(function() {
    var $sv_use = $(".sv_use");
    var count = $sv_use.length;

    $sv_use.each(function() {
        $(this).css("z-index", count);
        $(this).css("position", "relative");
        count = count - 1;
    });
});
</script>
<![endif]-->

</body>
</html>
<?php echo html_end(); // HTML 结尾处理函数 : 请勿删除 ?>