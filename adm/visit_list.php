<?php
$sub_menu = "200800";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '访问人数统计';
include_once('./visit.sub.php');

$colspan = 5;

$sql_common = " from {$g5['visit_table']} ";
$sql_search = " where vi_date between '{$fr_date}' and '{$to_date}' ";
if (isset($domain))
    $sql_search .= " and vi_referer like '%{$domain}%' ";

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
?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col">IP</th>
        <th scope="col">访问路径</th>
        <th scope="col">浏览器</th>
        <th scope="col">操作系统</th>
        <th scope="col">时间</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $brow = get_brow($row['vi_agent']);
        $os   = get_os($row['vi_agent']);

        $link = '';
        $link2 = '';
        $referer = '';
        $title = '';
        if ($row['vi_referer']) {

            $referer = get_text(cut_str($row['vi_referer'], 255, ''));
            $referer = urldecode($referer);

            if (!is_utf8($referer)) {
                $referer = iconv_utf8($referer);
            }

            $title = str_replace(array('<', '>', '&'), array("&lt;", "&gt;", "&amp;"), $referer);
            $link = '<a href="'.$row['vi_referer'].'" target="_blank">';
            $link = str_replace('&', "&amp;", $link);
            $link2 = '</a>';
        }

        if ($is_admin == 'super')
            $ip = $row['vi_ip'];
        else
            $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", G5_IP_DISPLAY, $row['vi_ip']);

        if ($brow == '其他') { $brow = '<span title="'.$row['vi_agent'].'">'.$brow.'</span>'; }
        if ($os == '其他') { $os = '<span title="'.$row['vi_agent'].'">'.$os.'</span>'; }

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_category"><?php echo $ip ?></td>
        <td><?php echo $link ?><?php echo $title ?><?php echo $link2 ?></td>
        <td class="td_category"><?php echo $brow ?></td>
        <td class="td_category"><?php echo $os ?></td>
        <td class="td_datetime"><?php echo $row['vi_date'] ?> <?php echo $row['vi_time'] ?></td>
    </tr>

    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">没有找到数据或已被删除</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<?php
if (isset($domain))
    $qstr .= "&amp;domain=$domain";
$qstr .= "&amp;page=";

$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr");
echo $pagelist;

include_once('./admin.tail.php');
?>
