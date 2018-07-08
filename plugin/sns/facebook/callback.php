<?php
include_once("./_common.php");
include_once(G5_SNS_PATH."/facebook/src/facebook.php");

$facebook = new Facebook(array(
    'appId'  => $config['cf_facebook_appid'],
    'secret' => $config['cf_facebook_secret']
));

$user = $facebook->getUser();

if ($user) {
    try {
        $user_profile = $facebook->api('/me');
    } catch (FacebookApiException $e) {
        error_log($e);
        $user = NULL;
    }
}

$g5['title'] = 'Facebook call back';
include_once(G5_PATH.'/head.sub.php');

if ($user) {
    $sns_name = $user_profile['name'];
    $sns_user = $user;

    set_cookie('ck_sns_name', $sns_name, 86400);
    set_session('ss_facebook_user', $user);

    $g5_sns_url = G5_SNS_URL;

    echo <<<EOT
    <script>
    $(function() {
        document.write("<strong>Facebook 认证成功</strong>");

        var opener = window.opener;
        opener.$("#wr_name").val("{$sns_name}");
        opener.$("#facebook_icon").attr("src", "{$g5_sns_url}/icon/facebook.png");
        opener.$("#facebook_checked").attr("disabled", false);
        opener.$("#facebook_checked").attr("checked", true);
        window.close();
    });
    </script>
EOT;

} else {

    echo <<<EOT
    <script>
    $(function() {
        alert("Facebook 认证失败");
        window.close();
    });
    </script>
EOT;

}

include_once(G5_PATH.'/tail.sub.php');
?>