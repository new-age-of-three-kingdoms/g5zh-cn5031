<?php
$sub_menu = "200820";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '删除访问数据';
include_once('./admin.head.php');

// 获取起始年度
$sql = " select min(vi_date) as min_date from {$g5['visit_table']} ";
$row = sql_fetch($sql);

$min_year = (int)substr($row['min_date'], 0, 4);
$now_year = (int)substr(G5_TIME_YMD, 0, 4);
?>

<div class="local_ov01 local_ov">
    请选择需要删除访问统计数据的年份及删除方式
</div>

<form name="fvisitdelete" class="local_sch02 local_sch" method="post" action="./visit_delete_update.php" onsubmit="return form_submit(this);">
    <div>
        <label for="year" class="sound_only">选择年份</label>
        <select name="year" id="year">
            <option value="">选择年份</option>
            <?php
            for($year=$min_year; $year<=$now_year; $year++) {
            ?>
            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
            <?php
            }
            ?>
        </select> 年
        <label for="month" class="sound_only">选择月份</label>
        <select name="month" id="month">
            <option value="">选择月份</option>
            <?php
            for($i=1; $i<=12; $i++) {
            ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php
            }
            ?>
        </select> 月
        <label for="method" class="sound_only">选择删除方式</label>
        <select name="method" id="method">
            <option value="before">删除所选日期以前数据</option>
            <option value="specific">删除所选日期以后数据</option>
        </select>
    </div>
    <div class="sch_last">
        <label for="pass">管理员密码<strong class="sound_only"> 必选项</strong></label>
        <input type="password" name="pass" id="pass" class="frm_input required">
        <input type="submit" value="确定" class="btn_submit">
    </div>
</form>

<script>
function form_submit(f)
{
    var year = $("#year").val();
    var month = $("#month").val();
    var method = $("#method").val();
    var pass = $("#pass").val();

    if(!year) {
        alert("请选择年份");
        return false;
    }

    if(!month) {
        alert("请选择月份");
        return false;
    }

    if(!pass) {
        alert("请输入管理员密码");
        return false;
    }

    var msg = "确定将"year+"年 "+month+"月";
    if(method == "before")
        msg += "之前";
    else
        msg += "之后";
    msg += "的数据吗?";

    return confirm(msg);
}
</script>

<?php
include_once('./admin.tail.php');
?>
