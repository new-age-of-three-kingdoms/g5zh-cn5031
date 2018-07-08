<?php
include_once('./_common.php');

// 特殊文字转换
function specialchars_replace($str, $len=0) {
    if ($len) {
        $str = substr($str, 0, $len);
    }

    $str = str_replace(array("&", "<", ">"), array("&amp;", "&lt;", "&gt;"), $str);

    /*
    $str = preg_replace("/&/", "&amp;", $str);
    $str = preg_replace("/</", "&lt;", $str);
    $str = preg_replace("/>/", "&gt;", $str);
    */

    return $str;
}

$sql = " select gr_id, bo_subject, bo_page_rows, bo_read_level, bo_use_rss_view from {$g5['board_table']} where bo_table = '$bo_table' ";
$row = sql_fetch($sql);
$subj2 = specialchars_replace($row['bo_subject'], 255);
$lines = $row['bo_page_rows'];

// 开启RSS功能需要允许游客可访问权限才能运行
if ($row['bo_read_level'] >= 2) {
    echo '开启RSS功能需要允许游客可访问权限才能运行';
    exit;
}

// RSS功能检查
if (!$row['bo_use_rss_view']) {
    echo '禁用RSS功能';
    exit;
}

header('Content-type: text/xml');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

$sql = " select gr_subject from {$g5['group_table']} where gr_id = '{$row['gr_id']}' ";
$row = sql_fetch($sql);
$subj1 = specialchars_replace($row['gr_subject'], 255);

echo '<?xml version="1.0" encoding="utf-8" ?>'."\n";
?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title><?php echo specialchars_replace($config['cf_title'].' &gt; '.$subj1.' &gt; '.$subj2) ?></title>
<link><?php echo specialchars_replace(G5_BBS_URL.'/board.php?bo_table='.$bo_table) ?></link>
<description>测试版0.2 (2004-04-26)</description>
<language>zh-cn</language>

<?php
$sql = " select wr_id, wr_subject, wr_content, wr_name, wr_datetime, wr_option
            from {$g5['write_prefix']}$bo_table
            where wr_is_comment = 0
            and wr_option not like '%secret%'
            order by wr_num, wr_reply limit 0, $lines ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $file = '';

    if (strstr($row['wr_option'], 'html'))
        $html = 1;
    else
        $html = 0;
?>

<item>
<title><?php echo specialchars_replace($row['wr_subject']) ?></title>
<link><?php echo specialchars_replace(G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$row['wr_id']) ?></link>
<description><![CDATA[<?php echo $file ?><?php echo conv_content($row['wr_content'], $html) ?>]]></description>
<dc:creator><?php echo specialchars_replace($row['wr_name']) ?></dc:creator>
<?php
$date = $row['wr_datetime'];
// 使用rss浏览器时时间显示错误
//$date = substr($date,0,10) . "T" . substr($date,11,8) . "+09:00";
$date = date('r', strtotime($date));
?>
<dc:date><?php echo $date ?></dc:date>
</item>

<?php
}

echo '</channel>'."\n";
echo '</rss>'."\n";
?>
