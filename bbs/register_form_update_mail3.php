<?php
// 修改邮件地址时发送邮件地址认证
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
?>

<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<title>会员邮件地址认证</title>
</head>

<body>

<div style="margin:30px auto;width:600px;border:10px solid #f7f7f7">
    <div style="border:1px solid #dedede">
        <h1 style="padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em">
            会员地址认证邮件
        </h1>
        <span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">
            <a href="<?php echo G5_URL ?>" target="_blank"><?php echo $config['cf_title'] ?></a>
        </span>
        <p style="margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee">
            <?php if($w == 'u') { ?>
            <b><?php echo $mb_name ?></b>会员账号因邮件地址变更需要重新进行认证<br><br>
            <?php } ?>

            请点击下方链接完成邮件地址认证<br>
            <a href="<?php echo $certify_href ?>" target="_blank"><b><?php echo $certify_href ?></b></a><br><br>

            此为系统邮件请勿回复，如需其他服务请登录您的账号联系在线客服<br>
            谢谢！
        </p>
        <a href="<?php echo G5_BBS_URL ?>/login.php" target="_blank" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center"><?php echo $config['cf_title'] ?> 登录</a>
    </div>
</div>

</body>
</html>
