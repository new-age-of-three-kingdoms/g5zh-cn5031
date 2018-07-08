<?
// 对比验证码session参数后显示结果
include_once("_common.php");
//header("Content-Type: text/html; charset=$g4[charset]");

$count = (int)get_session("ss_captcha_count");
if ($count >= 5) { // 超过最大尝试次数哪怕输入的验证码是正确的也显示错误
    echo false;
} else {
    set_session("ss_captcha_count", $count + 1);
    echo (get_session("ss_captcha_key") == $_POST['captcha_key']) ? true : false;
}
?>