<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
?>
<!-- 底部开始 { -->
<div id="ft_container">
<div id="ft">
    <div id="ft_catch"><img src="<?php echo G5_IMG_URL; ?>/logo_ft.png" width="140" height="36" alt="panacom"></div>
    <div id="ft_company">
            <a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=aboutus">关于我们</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=brand">品牌设计</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=ci">企业形象 & 视觉</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=food">餐饮拍照</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=space">空间设计</a>
    </div>
    <div id="ft_wechat"><img src="<?php echo G5_IMG_URL; ?>/wechat.png" width="49" height="40" alt="panacom"></div>
    <div id="ft_copy">
        <div>
            Copyright &copy; 2018, 京ICP备 1000000号<br>
        </div>
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

<?php
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>