<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

if (G5_IS_MOBILE) {
    echo conv_content($qaconfig['qa_mobile_content_tail'], 1);
    // 触屏版不适用
    include_once('./_tail.php');
} else {
    echo conv_content($qaconfig['qa_mobile_content_tail'], 1);
    if($qaconfig['qa_include_tail'])
        @include ($qaconfig['qa_include_tail']);
    else
        include ('./_tail.php');
}
?>