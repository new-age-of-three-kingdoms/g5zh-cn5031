<?php
if (!defined('_GNUBOARD_')) exit;

// http://kr1.php.net/manual/en/function.curl-setopt-array.php 参考
if (!function_exists('curl_setopt_array')) {
   function curl_setopt_array(&$ch, $curl_options)
   {
       foreach ($curl_options as $option => $value) {
           if (!curl_setopt($ch, $option, $value)) {
               return false;
           } 
       }
       return true;
   }
}


// 使用curl传递 naver syndi
function naver_syndi_ping($bo_table, $wr_id)
{
    global $config;

    $token = trim($config['cf_syndi_token']);

    // 如果没有token值则不适用naver syndi
    if ($token == '') return 0;

    // 排除禁用论坛版块
    if (preg_match('#^('.$config['cf_syndi_except'].')$#', $bo_table)) return -2;

    // 需要服务器支持curl library
    if (!function_exists('curl_init')) return -3;

    $ping_auth_header = "Authorization: Bearer " . $token;
    $ping_url = urlencode( G5_SYNDI_URL . "/ping.php?bo_table={$bo_table}&wr_id={$wr_id}" );
    $ping_client_opt = array( 
        CURLOPT_URL => "https://apis.naver.com/crawl/nsyndi/v2", 
        CURLOPT_POST => true, 
        CURLOPT_POSTFIELDS => "ping_url=" . $ping_url, 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_TIMEOUT => 10, 
        CURLOPT_HTTPHEADER => array("Host: apis.naver.com", "Pragma: no-cache", "Accept: */*", $ping_auth_header)
    ); 

    //print_r2($ping_client_opt); exit;
    $ping = curl_init(); 
    curl_setopt_array($ping, $ping_client_opt); 
    $response = curl_exec($ping); 
    curl_close($ping);

    return $response;
}
?>