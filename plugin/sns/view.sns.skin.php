<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

if (!$board['bo_use_sns']) return;

$sns_msg = urlencode(str_replace('\"', '"', $view['subject']));
//$sns_url = googl_short_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//$msg_url = $sns_msg.' : '.$sns_url;

/*
$facebook_url  = 'http://www.facebook.com/sharer/sharer.php?s=100&p[url]='.$sns_url.'&p[title]='.$sns_msg;
$twitter_url   = 'http://twitter.com/home?status='.$msg_url;
$gplus_url     = 'https://plus.google.com/share?url='.$sns_url;
*/

$sns_send  = G5_BBS_URL.'/sns_send.php?longurl='.urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//$sns_send .= '&amp;title='.urlencode(utf8_strcut(get_text($view['subject']),140));
$sns_send .= '&amp;title='.$sns_msg;

$facebook_url = $sns_send.'&amp;sns=facebook';
$twitter_url  = $sns_send.'&amp;sns=twitter';
$gplus_url    = $sns_send.'&amp;sns=gplus';
?>

<?php if(G5_IS_MOBILE && $config['cf_kakao_js_apikey']) { ?>
<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
<script src="<?php echo G5_JS_URL; ?>/kakaolink.js"></script>
<script>
    // 请设置api key
    Kakao.init("<?php echo $config['cf_kakao_js_apikey']; ?>");
</script>
<?php } ?>

<ul id="bo_v_sns">
    <li><a href="<?php echo $facebook_url; ?>" target="_blank"><img src="<?php echo G5_SNS_URL; ?>/icon/facebook.png" alt="分享到Facebook"></a></li>
    <li><a href="<?php echo $twitter_url; ?>" target="_blank"><img src="<?php echo G5_SNS_URL; ?>/icon/twitter.png" alt="分享到推特"></a></li>
    <li><a href="<?php echo $gplus_url; ?>" target="_blank"><img src="<?php echo G5_SNS_URL; ?>/icon/gplus.png" alt="分享到google+"></a></li>
    <?php if(G5_IS_MOBILE && $config['cf_kakao_js_apikey']) { ?>
    <li><a href="javascript:kakaolink_send('<?php echo $sns_msg; ?>', '<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>');"><img src="<?php echo G5_SNS_URL; ?>/icon/kakaotalk.png" alt="分享到kakao talk"></a>
    <?php } ?>
</ul>
