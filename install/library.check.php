<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

if(!extension_loaded('gd') || !function_exists('gd_info')) {
    echo '<script>'.PHP_EOL;
    echo 'alert("'.G5_VERSION.'需要有GD库支持才能正常运行\n缺少GD库将无法运行验证码功能、缩略图创建等功能，请与您的服务器供应商进行联络寻找解决方案！");'.PHP_EOL;
    echo '</script>'.PHP_EOL;
}
?>