<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

$mobile_sns_icon = '';
if (G5_IS_MOBILE) $sns_mc_icon = '';
else $sns_mc_icon = '_cmt';

if (!$board['bo_use_sns']) return;
?>
<?php if ($list[$i]['wr_facebook_user']) { ?>
<a href="https://www.facebook.com/profile.php?id=<?php echo $list[$i]['wr_facebook_user']; ?>" target="_blank"><img src="<?php echo G5_SNS_URL; ?>/icon/facebook<?php echo $sns_mc_icon; ?>.png" alt="Facebook分享成功"></a>
<?php } ?>
<?php if ($list[$i]['wr_twitter_user']) { ?>
<a href="https://www.twitter.com/<?php echo $list[$i]['wr_twitter_user']; ?>" target="_blank"><img src="<?php echo G5_SNS_URL; ?>/icon/twitter<?php echo $sns_mc_icon; ?>.png" alt="推特分享成功"></a>
<?php } ?>
