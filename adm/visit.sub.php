<?php
if (!defined('_GNUBOARD_')) exit;

include_once(G5_LIB_PATH.'/visit.lib.php');
include_once('./admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

if (empty($fr_date)) $fr_date = G5_TIME_YMD;
if (empty($to_date)) $to_date = G5_TIME_YMD;

$qstr = "fr_date=".$fr_date."&amp;to_date=".$to_date;
$query_string = $qstr ? '?'.$qstr : '';
?>

<form name="fvisit" id="fvisit" class="local_sch02 local_sch" method="get">
<div class="sch_last">
    <strong>时间段搜索</strong>
    <input type="text" name="fr_date" value="<?php echo $fr_date ?>" id="fr_date" class="frm_input" size="11" maxlength="10">
    <label for="fr_date" class="sound_only">开始日期</label>
    ~
    <input type="text" name="to_date" value="<?php echo $to_date ?>" id="to_date" class="frm_input" size="11" maxlength="10">
    <label for="to_date" class="sound_only">结束日期</label>
    <input type="submit" value="搜索" class="btn_submit">
</div>
</form>

<ul class="anchor">
    <li><a href="./visit_list.php<?php echo $query_string ?>">访问人数</a></li>
    <li><a href="./visit_domain.php<?php echo $query_string ?>">域名</a></li>
    <li><a href="./visit_browser.php<?php echo $query_string ?>">浏览器</a></li>
    <li><a href="./visit_os.php<?php echo $query_string ?>">操作系统</a></li>
    <li><a href="./visit_hour.php<?php echo $query_string ?>">时间</a></li>
    <li><a href="./visit_week.php<?php echo $query_string ?>">星期</a></li>
    <li><a href="./visit_date.php<?php echo $query_string ?>">日</a></li>
    <li><a href="./visit_month.php<?php echo $query_string ?>">月</a></li>
    <li><a href="./visit_year.php<?php echo $query_string ?>">年</a></li>
</ul>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

function fvisit_submit(act)
{
    var f = document.fvisit;
    f.action = act;
    f.submit();
}
</script>
