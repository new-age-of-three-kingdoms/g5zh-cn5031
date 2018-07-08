<?php
// 会员注册邮件
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
?>

<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<title>会员注册欢迎邮件</title>
</head>

<body>

<div style="margin:30px auto;width:600px;border:10px solid #f7f7f7">
    <div style="border:1px solid #dedede">
        <h1 style="padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em">
            恭喜您注册成为会员！
        </h1>
        <span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">
            <a href="<?php echo G5_URL ?>" target="_blank"><?php echo $config['cf_title'] ?></a>
        </span>
        <p style="margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee">
            亲爱的<b><?php echo $mb_name ?></b>，恭喜您成为会员！.<br>
            此为系统邮件请勿回复，如需其他服务请登录您的账号联系在线客服<br>
            <?php if ($config['cf_use_email_certify']) { ?>请您点击<strong>邮件地址认证</strong>完成会员注册！（如无法打开请把此链接复制粘贴到浏览器打开）<br><?php } ?>
            谢谢！
        </p>

        <?php if ($config['cf_use_email_certify']) { ?>
        <a href="<?php echo $certify_href ?>" target="_blank" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center">邮件地址认证</a>
        <?php } else { ?>
        <a href="<?php echo G5_URL ?>" target="_blank" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center">登录网站</a>
        <?php } ?>
    </div>
</div>

</body>
</html>
