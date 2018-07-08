<?php
// 当有新主题发布时向网站管理员发送通知邮件，如需修改通知内容请根据需要修改代码
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
?>
<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<title>[邮件通知]刊登新主题内容：<?php echo $wr_subject ?></title>
</head>

<body>

<div style="margin:30px auto;width:600px;border:10px solid #f7f7f7">
    <div style="border:1px solid #dedede">
        <h1 style="padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em">
            <?php echo $wr_subject ?>
        </h1>
        <span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">
            作者 <?php echo $wr_name ?>
        </span>
        <div style="margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee">
            <?php echo $wr_content ?>
        </div>
        <a href="<?php echo $link_url ?>" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center">点击查看原文</a>
    </div>
</div>

</body>
</html>
