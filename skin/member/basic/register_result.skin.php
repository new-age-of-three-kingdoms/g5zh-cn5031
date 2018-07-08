<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 会员注册结果开始 { -->
<div id="reg_result" class="mbskin">

    <p>
        <strong><?php echo get_text($mb['mb_name']); ?></strong> 感谢您的注册<br>
    </p>

    <?php if ($config['cf_use_email_certify']) {  ?>
    <p>
        已向您的邮箱发送会员注册确认邮件。<br>
        请您前往邮箱进行确认以便完成会员账户激活操作！
    </p>
    <div id="result_email">
        <span>会员ID</span>
        <strong><?php echo $mb['mb_id'] ?></strong><br>
        <span>邮件地址</span>
        <strong><?php echo $mb['mb_email'] ?></strong>
    </div>
    <p>
        如果您的邮箱地址有误请联系客服
    </p>
    <?php }  ?>

    <p>
        您的会员信息我们将会以加密数据格式进行储存，请放心使用！<br>
        如果您忘记了会员ID，登录密码等会员相关信息可以通过邮箱地址进行找回操作！
    </p>

    <p>
        您可以随时删除在本站的会员账户资料，您一旦执行删除账户操作您的所有数据将会从服务器上直接删除。<br>
        谢谢！
    </p>

    <div class="btn_confirm">
        <a href="<?php echo G5_URL ?>/" class="btn02">进入主页</a>
    </div>

</div>
<!-- } 会员注册结果结束 -->