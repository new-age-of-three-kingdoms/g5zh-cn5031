<?php
include_once('./_common.php');

// clean the output buffer
ob_end_clean();

$no = (int)$no;

@include_once($board_skin_path.'/download.head.skin.php');

// 比较cookie储存的id与传递来的id参数
// 防止盗链
if (!get_session('ss_view_'.$bo_table.'_'.$wr_id))
    alert('错误路径！请通过正常方式访问');

// 开启下载积分时禁止游客下载
if($board['bo_download_point'] < 0 && $is_guest)
    alert('您没有下载权限\\n请您登录后使用', G5_BBS_URL.'/login.php?wr_id='.$wr_id.'&amp;'.$qstr.'&amp;url='.urlencode(G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id));

$sql = " select bf_source, bf_file from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$no' ";
$file = sql_fetch($sql);
if (!$file['bf_file'])
    alert_close('抱歉！没有找到附件');

// 禁用javascript时
if($js != 'on' && $board['bo_download_point'] < 0) {
    $msg = $file['bf_source'].' 下载附件将会扣除积分('.number_format($board['bo_download_point']).'分\\n积分仅在首次下载时扣除，多次下载也仅需支付一次积分。\\n点击确定开始下载附件并扣除积分';
    $url1 = G5_BBS_URL.'/download.php?'.$_SERVER['QUERY_STRING'].'&amp;js=on';
    $url2 = $_SERVER['HTTP_REFERER'];

    //$url1 = 确定link, $url2=取消link
    // 移动到特定地址时使用$url3
    confirm($msg, $url1, $url2);
}

if ($member['mb_level'] < $board['bo_download_level']) {
    $alert_msg = '您没有下载权限';
    if ($member['mb_id'])
        alert($alert_msg);
    else
        alert($alert_msg.'\\n请您登录后使用', G5_BBS_URL.'/login.php?wr_id='.$wr_id.'&amp;'.$qstr.'&amp;url='.urlencode(G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id));
}

$filepath = G5_DATA_PATH.'/file/'.$bo_table.'/'.$file['bf_file'];
$filepath = addslashes($filepath);
if (!is_file($filepath) || !file_exists($filepath))
    alert('找不到文件');

// 执行用户代码
@include_once($board_skin_path.'/download.skin.php');

// 修改为下载仅扣除一次积分
$ss_name = 'ss_down_'.$bo_table.'_'.$wr_id;
if (!get_session($ss_name))
{
    // 如果是作者本人就通过
    // 如果是管理员就通过
    if (($write['mb_id'] && $write['mb_id'] == $member['mb_id']) || $is_admin)
        ;
    else if ($board['bo_download_level'] >= 1) // 允许下载会员等级
    {
        // 下载积分不足时
        if ($member['mb_point'] + $board['bo_download_point'] < 0)
            alert('当前积分('.number_format($member['mb_point']).')不足支付下载所需积分('.number_format($board['bo_download_point']).')\\n\\n请获取更多积分后重试');

        // 每个主题仅计算一次下载次数
        insert_point($member['mb_id'], $board['bo_download_point'], "{$board['bo_subject']} $wr_id 文件下载", $bo_table, $wr_id, "下载");
    }

    // 增加下载计数器
    $sql = " update {$g5['board_file_table']} set bf_download = bf_download + 1 where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$no' ";
    sql_query($sql);

    set_session($ss_name, TRUE);
}

$g5['title'] = '下载 &gt; '.conv_subject($write['wr_subject'], 255);

$original = urlencode($file['bf_source']);

@include_once($board_skin_path.'/download.tail.skin.php');

if(preg_match("/msie/i", $_SERVER['HTTP_USER_AGENT']) && preg_match("/5\.5/", $_SERVER['HTTP_USER_AGENT'])) {
    header("content-type: doesn/matter");
    header("content-length: ".filesize("$filepath"));
    header("content-disposition: attachment; filename=\"$original\"");
    header("content-transfer-encoding: binary");
} else {
    header("content-type: file/unknown");
    header("content-length: ".filesize("$filepath"));
    header("content-disposition: attachment; filename=\"$original\"");
    header("content-description: php generated data");
}
header("pragma: no-cache");
header("expires: 0");
flush();

$fp = fopen($filepath, 'rb');

// 4.00 代替
// 为了降低服务器负载相对使用print、echo或while方式以下方法更加提高效率
//if (!fpassthru($fp)) {
//    fclose($fp);
//}

$download_rate = 10;

while(!feof($fp)) {
    //echo fread($fp, 100*1024);
    /*
    echo fread($fp, 100*1024);
    flush();
    */

    print fread($fp, round($download_rate * 1024));
    flush();
    usleep(1000);
}
fclose ($fp);
flush();
?>
