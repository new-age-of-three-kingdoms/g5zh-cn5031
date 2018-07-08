<?php
$sub_menu = "200300";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

$html_title = '群发会员邮件';

check_demo();

check_token();

include_once('./admin.head.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

$countgap = 10; // 设置单次发送数量
$maxscreen = 500; // 画面中显示多杀条处理信息
$sleepsec = 200;  // 设置休息时间 千分之秒

echo "<span style='font-size:9pt;'>";
echo "<p>邮件发送中...<P>显示<font color=crimson><b>[发送完成]</b></font>之前请勿离开或刷新当前页面<p>";
echo "</span>";
?>

<span id="cont"></span>

<?php
include_once('./admin.tail.php');
?>

<?php
flush();
ob_flush();

$ma_id = trim($_POST['ma_id']);
$select_member_list = trim($_POST['ma_list']);

//print_r2($_POST); EXIT;
$member_list = explode("\n", conv_unescape_nl($select_member_list));

// 获取邮件内容
$sql = "select ma_subject, ma_content from {$g5['mail_table']} where ma_id = '$ma_id' ";
$ma = sql_fetch($sql);

$subject = $ma['ma_subject'];

$cnt = 0;
for ($i=0; $i<count($member_list); $i++)
{
    list($to_email, $mb_id, $name, $nick, $datetime) = explode("||", trim($member_list[$i]));

    $sw = preg_match("/[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*@[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*/", $to_email);
    // 仅读取正确的邮件地址
    if ($sw == true)
    {
        $cnt++;

        $mb_md5 = md5($mb_id.$to_email.$datetime);

        $content = $ma['ma_content'];
        $content = preg_replace("/{姓名}/", $name, $content);
        $content = preg_replace("/{昵称}/", $nick, $content);
        $content = preg_replace("/{会员ID}/", $mb_id, $content);
        $content = preg_replace("/{邮件地址}/", $to_email, $content);

        $content = $content . "<hr size=0><p><span style='font-size:9pt; font-familye:Simhei'>▶ 如果您不想收到来至本站的订阅邮件内容请点击 [<a href='".G5_BBS_URL."/email_stop.php?mb_id={$mb_id}&amp;mb_md5={$mb_md5}' target='_blank'>退订邮件</a> ]</span></p>";

        mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $to_email, $subject, $content, 1);

        echo "<script> document.all.cont.innerHTML += '$cnt. $to_email ($mb_id : $name)<br>'; </script>\n";
        //echo "+";
        flush();
        ob_flush();
        ob_end_flush();
        usleep($sleepsec);
        if ($cnt % $countgap == 0)
        {
            echo "<script> document.all.cont.innerHTML += '<br>'; document.body.scrollTop += 1000; </script>\n";
        }

        // 清空画面减轻负载
        if ($cnt % $maxscreen == 0)
            echo "<script> document.all.cont.innerHTML = ''; document.body.scrollTop += 1000; </script>\n";
    }
}
?>
<script> document.all.cont.innerHTML += "<br><br>发送成功<?php echo number_format($cnt) ?>条邮件<br><br><font color=crimson><b>[结束]</b></font>"; document.body.scrollTop += 1000; </script>
