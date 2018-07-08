<?php
$sub_menu = '100300';
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

if (!$config['cf_email_use'])
    alert('网站设置中开启邮件发送功能后才能使用此功能');

include_once(G5_LIB_PATH.'/mailer.lib.php');

$g5['title'] = '邮件测试';
include_once('./admin.head.php');

if (isset($_POST['email'])) {
    $email = explode(',', $_POST['email']);
    for ($i=0; $i<count($email); $i++)
        mailer($config['cf_admin_email_name'], $config['cf_admin_email'], trim($email[$i]), '[邮件检测] 主题', '<span style="font-size:9pt;">[邮件检测] 内容<p>如果您可以正常收到此邮件，说明当前服务器支持发送邮件。<p>'.G5_TIME_YMDHIS.'<p>请勿回复此邮件</span>', 1);

    echo '<section>';
    echo '<h2>结果显示</h2>';
    echo '<div class="local_desc01 local_desc"><p>';
    echo '已向'.count($email).'个邮箱发送测试邮件';
    echo '</p></div>';
    echo '<ul>';
    for ($i=0;$i<count($email);$i++) {
        echo '<li>'.$email[$i].'</li>';
    }
    echo '</ul>';
    echo '<div class="local_desc02 local_desc"><p>';
    echo '请进入邮箱查收邮件是否正常抵达<br>';
    echo '如无法收到邮箱请使用不同的邮箱供应商进行尝试<br>';
    echo '如依然无法收到请于服务器管理员联系确认sendmail服务是否正运行。<br>';
    echo '</p></div>';
    echo '</section>';
}
?>

<section>
    <h2>发送测试邮件</h2>
    <div class="local_desc02 local_desc">
        <p>
            用于检测服务器邮件服务器是否正常工作<br>
            请输入用于接收测试邮件的地址，您稍后将受到平[测试邮件]<br>
        </p>
    </div>
    <form name="fsendmailtest" method="post">
    <fieldset id="fsendmailtest">
        <legend>发送测试邮件</legend>
        <label for="email">收件地址<strong class="sound_only"> 必选项</strong></label>
        <input type="text" name="email" value="<?php echo $member['mb_email'] ?>" id="email" required class="required email frm_input" size="80">
        <input type="submit" value="发送" class="btn_submit">
    </fieldset>
    </form>
    <div class="local_desc02 local_desc">
        <p>
            如您未能收到测试邮件，请检查服务器sendmail服务是否正常工作。<br>
            建议您测试时使用多个不同供应商提供的邮箱进行测试。<br>
        </p>
    </div>
</section>

<?php
include_once('./admin.tail.php');
?>
