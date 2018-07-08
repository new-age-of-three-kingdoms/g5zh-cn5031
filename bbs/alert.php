<?php
global $lo_location;
global $lo_url;

include_once('./_common.php');

if($error) {
    $g5['title'] = "错误指南";
} else {
    $g5['title'] = "执行结果";
}
include_once(G5_PATH.'/head.sub.php');
// 必须输入项目
// 除去两端空白
// 必须(选择或输入)项目
// 电话号码格式错误，请使用(-)区分
// 邮件地址格式错误
// 请使用中文(请勿使用汉字特殊符号)
// 请使用数字
// 请使用中、英及数字
// 请使用中文、英文
// 请使用数字
// 请使用英文字母
// 请使用英文、数字
// 请使用英文、数字、下划线
// 最少输入 字以上.
// 非图片格式，请使用gif，jpg，png格式
// 只能使用文件
// 请勿输入空格

$msg2 = str_replace("\\n", "<br>", $msg);

if (!$url) $url = $_SERVER['HTTP_REFERER'];

if($error) {
    $header2 = "以下项目中有错误";
} else {
    $header2 = "请确认以下内容是否正确";
}
?>

<script>
alert("<?php echo strip_tags($msg); ?>");
//document.location.href = "<?php echo $url; ?>";
<?php if ($url) { ?>
document.location.replace("<?php echo str_replace('&amp;', '&', $url); ?>");
<?php } else { ?>
//alert('history.back();');
history.back();
<?php } ?>
</script>

<noscript>
<div id="validation_check">
    <h1><?php echo $header2 ?></h1>
    <p class="cbg">
        <?php echo $msg2 ?>
    </p>
    <?php if($post) { ?>
    <form method="post" action="<?php echo $url ?>">
    <?php
    foreach($_POST as $key => $value) {
        if(strlen($value) < 1)
            continue;

        if(preg_match("/pass|pwd|capt|url/", $key))
            continue;
    ?>
    <input type="hidden" name="<?php echo $key ?>" value="<?php echo $value ?>">
    <?php
    }
    ?>
    <input type="submit" value="返回">
    </form>
    <?php } else { ?>
    <div class="btn_confirm">
        <a href="<?php echo $url ?>">返回</a>
    </div>
    <?php } ?>

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

<a href="<?php echo $url ?>">返回</a>
</article>
*/ ?>
</div>
</noscript>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>