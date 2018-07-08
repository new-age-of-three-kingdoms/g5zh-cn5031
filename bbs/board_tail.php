<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// 论坛管理底部内容
if (G5_IS_MOBILE) {
    echo stripslashes($board['bo_mobile_content_tail']);
    // 触屏版不适用
    include_once('./_tail.php');
} else {
    echo stripslashes($board['bo_content_tail']);
    @include ($board['bo_include_tail']);
}
?>