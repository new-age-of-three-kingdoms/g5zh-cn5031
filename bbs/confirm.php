<?php
include_once('./_common.php');
include_once(G5_PATH.'/head.sub.php');
?>

<script>
var conf = "<?php echo strip_tags($msg); ?>";
if (confirm(conf)) {
    document.location.replace("<?php echo $url1; ?>");
} else {
    document.location.replace("<?php echo $url2; ?>");
}
</script>

<noscript>
<article id="confirm_check">
<header>
    <hgroup>
        <h1><?php echo $header; ?></h1> <!-- 执行中的操作内容 -->
        <h2>请确认一下内容</h2>
    </hgroup>
</header>
<p>
    <?php echo $msg; ?>
</p>

<a href="<?php echo $url1; ?>">确定</a>
<a href="<?php echo $url2; ?>">取消</a><br><br>
<a href="<?php echo $url3; ?>">返回</a>
</article>
</noscript>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>