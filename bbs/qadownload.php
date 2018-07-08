<?php
include_once('./_common.php');

// clean the output buffer
ob_end_clean();

$no = (int)$no;

// 比较cookie储存的id与传递来的id参数
// 防止盗链
if (!get_session('ss_qa_view_'.$qa_id))
    alert('错误路径！请通过正常方式访问');

$sql = " select qa_subject, qa_file{$no}, qa_source{$no} from {$g5['qa_content_table']} where qa_id = '$qa_id' ";
$file = sql_fetch($sql);
if (!$file['qa_file'.$no])
    alert_close('抱歉！没有找到附件');

if($is_guest) {
    alert('您没有下载权限\\n请您登录后使用', G5_BBS_URL.'/login.php?url='.urlencode(G5_BBS_URL.'/qaview.php?qa_id='.$qa_id));
}

$filepath = G5_DATA_PATH.'/qa/'.$file['qa_file'.$no];
$filepath = addslashes($filepath);
if (!is_file($filepath) || !file_exists($filepath))
    alert('找不到文件');

$g5['title'] = '下载 &gt; '.conv_subject($file['qa_subject'], 255);

$original = urlencode($file['qa_source'.$no]);

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
