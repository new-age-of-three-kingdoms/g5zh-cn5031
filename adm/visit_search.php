<?php
$sub_menu = '200810';
include_once('./_common.php');
include_once(G5_PATH.'/lib/visit.lib.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '统计数据搜索';
include_once('./admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$colspan = 5;
$listall = '<a href="'.$_SERVER['PHP_SELF'].'">首页</a>'; //页面初始化
?>

<div class="local_sch local_sch01">
    <form name="fvisit" method="get" onsubmit="return fvisit_submit(this);">
    <?=$listall?>
    <label for="sch_sort" class="sound_only">搜索分类</label>
    <select name="sfl" id="sch_sort" class="search_sort">
        <option value="vi_ip"<?php echo get_selected($sfl, 'vi_ip'); ?>>IP</option>
        <option value="vi_referer"<?php echo get_selected($sfl, 'vi_referer'); ?>>访问路径</option>
        <option value="vi_date"<?php echo get_selected($sfl, 'vi_date'); ?>>日期</option>
    </select>
    <label for="sch_word" class="sound_only">关键词</label>
    <input type="text" name="stx" size="20" value="<?php echo stripslashes($stx); ?>" id="sch_word" class="frm_input">
    <input type="submit" value="搜索" class="btn_submit">
    </form>
</div>

<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">IP</th>
        <th scope="col">访问路径</th>
        <th scope="col">浏览器</th>
        <th scope="col">OS</th>
        <th scope="col">时间</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql_common = " from {$g5['visit_table']} ";
    if ($sfl) {
        if($sst=='vi_ip' || $sst=='vi_date'){
            $sql_search = " where $sfl like '$stx%' ";
        }else{
            $sql_search = " where $sfl like '%$stx%' ";
        }
    }
    $sql = " select count(*) as cnt
                {$sql_common}
                {$sql_search} ";
    $row = sql_fetch($sql);
    $total_count = $row['cnt'];

    $rows = $config['cf_page_rows'];
    $total_page  = ceil($total_count / $rows);  // 计算所有页码
    if ($page < 1) $page = 1; // 如果没有页码时设置为1
    $from_record = ($page - 1) * $rows; // 获取开始行

    $sql = " select *
                {$sql_common}
                {$sql_search}
                order by vi_id desc
                limit {$from_record}, {$rows} ";
    $result = sql_query($sql);

    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $brow = get_brow($row['vi_agent']);
        $os   = get_os($row['vi_agent']);

        $link = "";
        $referer = "";
        $title = "";
        if ($row['vi_referer']) {

            $referer = get_text(cut_str($row['vi_referer'], 255, ""));
            $referer = urldecode($referer);

            if (!is_utf8($referer)) {
                $referer = iconv('gb2312', 'utf-8', $referer);
            }

            $title = str_replace(array("<", ">"), array("&lt;", "&gt;"), $referer);
            $link = '<a href="'.$row['vi_referer'].'" target="_blank" title="'.$title.'">';
        }

        if ($is_admin == 'super')
            $ip = $row['vi_ip'];
        else
            $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", G5_IP_DISPLAY, $row['vi_ip']);

        if ($brow == '其他') $brow = '<span title="'.$row['vi_agent'].'">'.$brow.'</span>';
        if ($os == '其他') $os = '<span title="'.$row['vi_agent'].'">'.$os.'</span>';

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_id"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?sfl=vi_ip&amp;stx=<?php echo $ip; ?>"><?php echo $ip; ?></a></td>
        <td><?php echo $link.$title; ?></a></td>
        <td class="td_idsmall"><?php echo $brow; ?></td>
        <td class="td_idsmall"><?php echo $os; ?></td>
        <td class="td_datetime"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?sfl=vi_date&amp;stx=<?php echo $row['vi_date']; ?>"><?php echo $row['vi_date']; ?></a> <?php echo $row['vi_time']; ?></td>
    </tr>
    <?php } ?>
    <?php if ($i == 0) echo '<tr><td colspan="'.$colspan.'" class="empty_table">未找到相应信息</td></tr>'; ?>
    </tbody>
    </table>
</div>

<?php
$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr.'&amp;domain='.$domain.'&amp;page=');
if ($pagelist) {
    echo $pagelist;
}
?>

<script>
$(function(){
    $("#sch_sort").change(function(){ // select #sch_sort选项变更时
        if($(this).val()=="vi_date"){ // 对应的value为vi_date时
            $("#sch_word").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" }); // 执行datepicker
        }else{ // 如果不是
            $("#sch_word").datepicker("destroy"); // datepicker 未执行
        }
    });

    if($("#sch_sort option:selected").val()=="vi_date"){ // select #sch_sort 选项中selected值是vi_date时
        $("#sch_word").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" }); // 执行datepicker
    }
});

function fvisit_submit(f)
{
    return true;
}
</script>

<?php
include_once('./admin.tail.php');
?>
