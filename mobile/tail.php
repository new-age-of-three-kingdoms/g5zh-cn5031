<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
?>
    </div>
</div>

<hr>

<?php echo poll('basic'); // 在线投票 ?>

<hr>

<div id="ft">
    <?php echo popular('basic'); // 热门关键词 ?>
    <?php echo visit('basic'); // 访问人数 ?>
    <div id="ft_copy">
        <div id="ft_company">
            <a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=company">网站介绍</a>
            <a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=privacy">个人隐私保护条例</a>
            <a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=provision">网站服务条款</a>
        </div>
        Copyright &copy; <b>您的域名.</b> All rights reserved.<br>
        <a href="#">返回顶部</a>
    </div>
</div>

<?php
if(G5_DEVICE_BUTTON_DISPLAY && G5_IS_MOBILE) {
    $seq = 0;
    $p = parse_url(G5_URL);
    $href = $p['scheme'].'://'.$p['host'];
    if(isset($p['port']) && $p['port'])
        $href .= ':'.$p['port'];
    $href .= $_SERVER['PHP_SELF'];
    if($_SERVER['QUERY_STRING']) {
        $sep = '?';
        foreach($_GET as $key=>$val) {
            if($key == 'device')
                continue;

            $href .= $sep.$key.'='.$val;
            $sep = '&amp;';
            $seq++;
        }
    }
    if($seq)
        $href .= '&amp;device=pc';
    else
        $href .= '?device=pc';
?>
<a href="<?php echo $href; ?>" id="device_change">进入电脑版</a>
<?php
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>

<script>
$(function() {
    // 如有字体设定则执行
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?php
include_once(G5_PATH."/tail.sub.php");
?>