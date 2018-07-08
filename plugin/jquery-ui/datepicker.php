<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

add_stylesheet('<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet" />', 0);
add_stylesheet('<link type="text/css" href="'.G5_PLUGIN_URL.'/jquery-ui/style.css">', 0);
?>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
<script>
jQuery(function($){
    $.datepicker.regional["zh-cn"] = {
        closeText: "关闭",
        prevText: "上个月",
        nextText: "下个月",
        currentText: "今天",
        monthNames: ["1月(JAN)","2月(FEB)","3月(MAR)","4月(APR)","5月(MAY)","6月(JUN)", "7月(JUL)","8月(AUG)","9月(SEP)","10月(OCT)","11月(NOV)","12月(DEC)"],
        monthNamesShort: ["1月","2月","3月","4月","5月","6月", "7月","8月","9月","10月","11月","12月"],
        dayNames: ["日","一","二","三","四","五","六"],
        dayNamesShort: ["日","一","二","三","四","五","六"],
        dayNamesMin: ["日","一","二","三","四","五","六"],
        weekHeader: "Wk",
        dateFormat: "yymmdd",
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: ""
    };
	$.datepicker.setDefaults($.datepicker.regional["zh-cn"]);
});
</script>