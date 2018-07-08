<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// 底部文件路径设置 : 请勿随意更改此段代码
if ($config['cf_include_tail'] && is_file(G5_PATH.'/'.$config['cf_include_tail'])) {
    include_once(G5_PATH.'/'.$config['cf_include_tail']);
    return; // 不执行以下代码
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/tail.php');
    return;
}
?>
    </div>
</div>

<!-- } 项目结束 -->

<hr>

<!-- 底部开始 { -->
<div id="ft">
    <?php echo popular('basic'); // 热门关键词  ?>
    <?php echo visit('basic'); // 访问人数统计 ?>
    <div id="ft_catch"><img src="<?php echo G5_IMG_URL; ?>/ft.png" alt="<?php echo G5_VERSION ?>"></div>
    <div id="ft_company">
    </div>
    <div id="ft_copy">
        <div>
            <a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=company">网站介绍</a>
            <a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=privacy">个人隐私保护条例</a>
            <a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=provision">网站服务条款</a>
            Copyright &copy; <b>您的域名.</b> All rights reserved.<br>
            <a href="#hd" id="ft_totop">返回顶部</a>
        </div>
    </div>
</div>

<?php
if(G5_DEVICE_BUTTON_DISPLAY && !G5_IS_MOBILE) {
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

            $href .= $sep.$key.'='.strip_tags($val);
            $sep = '&amp;';
            $seq++;
        }
    }
    if($seq)
        $href .= '&amp;device=mobile';
    else
        $href .= '?device=mobile';
?>
<a href="<?php echo $href; ?>" id="device_change">访问触屏版</a>
<?php
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>

<!-- } 底部结束 -->

<script>
$(function() {
    // 如有字体设定则执行
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?php
include_once(G5_PATH."/tail.sub.php");
?>