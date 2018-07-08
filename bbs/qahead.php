<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

$qa_skin_path = (G5_IS_MOBILE ? G5_MOBILE_PATH : G5_PATH).'/'.G5_SKIN_DIR.'/qa/'.(G5_IS_MOBILE ? $qaconfig['qa_mobile_skin'] : $qaconfig['qa_skin']);
$qa_skin_url = (G5_IS_MOBILE ? G5_MOBILE_URL : G5_URL).'/'.G5_SKIN_DIR.'/qa/'.(G5_IS_MOBILE ? $qaconfig['qa_mobile_skin'] : $qaconfig['qa_skin']);

if (G5_IS_MOBILE) {
    // 触屏版不适用
    include_once('./_head.php');
    echo conv_content($qaconfig['qa_mobile_content_head'], 1);
} else {
    if($qaconfig['qa_include_head'])
        @include ($qaconfig['qa_include_head']);
    else
        include ('./_head.php');
    echo conv_content($qaconfig['qa_content_head'], 1);
}
?>