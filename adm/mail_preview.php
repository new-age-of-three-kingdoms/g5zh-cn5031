<?php
$sub_menu = "200300";
include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

auth_check($auth[$sub_menu], 'r');

$se = sql_fetch("select ma_subject, ma_content from {$g5['mail_table']} where ma_id = '{$ma_id}' ");

$subject = $se['ma_subject'];
$content = conv_content($se['ma_content'], 1) . "<hr size=0><p><span style='font-size:9pt; font-family:Simhei'>▶ 如果您不想收到来至本站的订阅邮件内容请点击 [<a href='".G5_BBS_URL."/email_stop.php?mb_id=***&amp;mb_md5=***' target='_blank'>退订邮件</a> ]</span></p>";
?>

<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<title><?php echo G5_VERSION ?> 邮件群发测试</title>
</head>

<body>

<h1><?php echo $subject; ?></h1>

<p>
    <?php echo $content; ?>
</p>

<p>
    <strong>注意!</strong>此页面效果可能与实际接收效果有所差异
</p>

</body>
</html>