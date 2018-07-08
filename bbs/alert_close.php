<?php
include_once('./_common.php');
include_once(G5_PATH.'/head.sub.php');

$msg2 = str_replace("\\n", "<br>", $msg);

if($error) {
    $header2 = "以下项目中有错误";
    $msg3 = "请关新窗口后重新执行操作";
} else {
    $header2 = "请确认以下内容是否正确";
    $msg3 = "请关闭窗口后重新使用当前服务";
}
?>

<script>
alert("<?php echo $msg; ?>");
window.close();
</script>

<noscript>
<div id="validation_check">
    <h1><?php echo $header2 ?></h1>
    <p class="cbg">
        <?php echo $msg2 ?>
    </p>
    <p class="cbg">
        <?php echo $msg3 ?>
    </p>

</div>

<?php /*
<article id="validation_check">
<header>
    <hgroup>
        <!-- <h1>会员注册信息输入确认</h1> --> <!-- 执行中的操作内容 -->
        <h1><?php echo $header ?></h1> <!-- 执行中的操作内容 -->
        <h2><?php echo $header2 ?></h2>
    </hgroup>
</header>
<p>
    <!-- <strong>项目</strong> 错误信息 -->
    <!--
    <strong>姓名</strong>是必须输入的项目，只可以使用中文输入<br>
    <strong>邮件地址</strong>格式错误<br>
    -->
    <?php echo $msg2 ?>
</p>
<p>
    <?php echo $msg3 ?>
</p>

</article>
*/ ?>

</noscript>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>