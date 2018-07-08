<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// 论坛管理顶部内容
if (G5_IS_MOBILE) {
    // 触屏版不适用
    include_once('./_head.php');
    echo stripslashes($board['bo_mobile_content_head']);
} else {
    @include ($board['bo_include_head']);
    echo stripslashes($board['bo_content_head']);
}
?>