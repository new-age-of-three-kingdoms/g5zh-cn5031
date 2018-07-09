<?php
if (!defined('_GNUBOARD_')) exit;

/*************************************************************************
**
**  常用函数整合
**
*************************************************************************/

// 使用microtime获取时间进行计算
function get_microtime()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}


// 每页显示行，当前页面，所有页码，URL
function get_paging($write_pages, $cur_page, $total_page, $url, $add="")
{
    //$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
    $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

    $str = '';
    if ($cur_page > 1) {
        $str .= '<a href="'.$url.'1'.$add.'" class="pg_page pg_start"><img src="/img/pg_prev.jpg" ></a>'.PHP_EOL;
    }
	else
	    $str .= '<a class="pg_page pg_start"><img src="/img/pg_prev.jpg" ></a>'.PHP_EOL;

    $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
    $end_page = $start_page + $write_pages - 1;

    if ($end_page >= $total_page) $end_page = $total_page;

    if ($start_page > 1) $str .= '<a href="'.$url.($start_page-1).$add.'" class="pg_page pg_prev"><img src="/img/pg_prev.jpg" ></a>'.PHP_EOL;

    if ($total_page > 1) {
        for ($k=$start_page;$k<=$end_page;$k++) {
            if ($cur_page != $k)
                $str .= '<a href="'.$url.$k.$add.'" class="pg_page">'.$k.'<span class="sound_only">页</span></a>'.PHP_EOL;
            else
                $str .= '<span class="sound_only">已开启</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">页</span>'.PHP_EOL;
        }
    }

    if ($total_page > $end_page) $str .= '<a href="'.$url.($end_page+1).$add.'" class="pg_page pg_next"><img src="/img/pg_next.jpg" ></a>'.PHP_EOL;

    if ($cur_page < $total_page) {
        $str .= '<a href="'.$url.$total_page.$add.'" class="pg_page pg_end"><img src="/img/pg_next.jpg" ></a>'.PHP_EOL;
    }
	else
	    $str .= '<a class="pg_page pg_end"><img src="/img/pg_next.jpg" ></a>'.PHP_EOL;

    if ($str)
        return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
    else
        return "";
}

// 分页代码<nav><span>后加入以下代码
function page_insertbefore($paging_html, $insert_html)
{
    if(!$paging_html)
        $paging_html = '<nav class="pg_wrap"><span class="pg"></span></nav>';

    return preg_replace("/^(<nav[^>]+><span[^>]+>)/", '$1'.$insert_html.PHP_EOL, $paging_html);
}

// 分页代码</span></nav>前加入以下代码
function page_insertafter($paging_html, $insert_html)
{
    if(!$paging_html)
        $paging_html = '<nav class="pg_wrap"><span class="pg"></span></nav>';

    if(preg_match("#".PHP_EOL."</span></nav>#", $paging_html))
        $php_eol = '';
    else
        $php_eol = PHP_EOL;

    return preg_replace("#(</span></nav>)$#", $php_eol.$insert_html.'$1', $paging_html);
}

// 获取参数，排列名称与参数 print_r()函数的另一种写法
function print_r2($var)
{
    ob_start();
    print_r($var);
    $str = ob_get_contents();
    ob_end_clean();
    $str = str_replace(" ", "&nbsp;", $str);
    echo nl2br("<span style='font-family:Tahoma, Simhei; font-size:9pt;'>$str</span>");
}


// 使用meta标签进行页面跳转
// header("location:URL") 代替
function goto_url($url)
{
    $url = str_replace("&amp;", "&", $url);
    //echo "<script> location.replace('$url'); </script>";

    if (!headers_sent())
        header('Location: '.$url);
    else {
        echo '<script>';
        echo 'location.replace("'.$url.'");';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
    }
    exit;
}


// session参数创建
function set_session($session_name, $value)
{
    if (PHP_VERSION < '5.3.0')
        session_register($session_name);
    // 消除php版本差异
    $$session_name = $_SESSION[$session_name] = $value;
}


// 获取session参数
function get_session($session_name)
{
    return isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : '';
}


// 创建cookie参数
function set_cookie($cookie_name, $value, $expire)
{
    global $g5;

    setcookie(md5($cookie_name), base64_encode($value), G5_SERVER_TIME + $expire, '/', G5_COOKIE_DOMAIN);
}


// 获取cookie参数
function get_cookie($cookie_name)
{
    $cookie = md5($cookie_name);
    if (array_key_exists($cookie, $_COOKIE))
        return base64_decode($_COOKIE[$cookie]);
    else
        return "";
}


// 使用警告窗口显示警告信息
function alert($msg='', $url='', $error=true, $post=false)
{
    global $g5, $config, $member;
    global $is_admin;

    if (!$msg) $msg = '请使用正常方式访问';

    $header = '';
    if (isset($g5['title'])) {
        $header = $g5['title'];
    }
    include_once(G5_BBS_PATH.'/alert.php');
    exit;
}


// 显示警告信息后关闭窗口
function alert_close($msg, $error=true)
{
    global $g5;

    $header = '';
    if (isset($g5['title'])) {
        $header = $g5['title'];
    }
    include_once(G5_BBS_PATH.'/alert_close.php');
    exit;
}

// confirm 窗口
function confirm($msg, $url1='', $url2='', $url3='')
{
    global $g5;

    if (!$msg) {
        $msg = '请使用正常方式访问';
        alert($msg);
    }

    if(!trim($url1) || !trim($url2)) {
        $msg = '请设置$url1 与 $url2 ';
        alert($msg);
    }

    if (!$url3) $url3 = $_SERVER['HTTP_REFERER'];

    $msg = str_replace("\\n", "<br>", $msg);

    $header = '';
    if (isset($g5['title'])) {
        $header = $g5['title'];
    }
    include_once(G5_BBS_PATH.'/confirm.php');
    exit;
}


// 参考way.co.kr的wayboard
function url_auto_link($str)
{
    global $g5;
    global $config;

    // 140326 根据刘昌化提议更改
    // http://sir.co.kr/bbs/board.php?bo_table=pg_lecture&wr_id=461
    // http://sir.co.kr/bbs/board.php?bo_table=pg_lecture&wr_id=463
    $str = str_replace(array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"), array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"), $str);
    //$str = preg_replace("`(?:(?:(?:href|src)\s*=\s*(?:\"|'|)){0})((http|https|ftp|telnet|news|mms)://[^\"'\s()]+)`", "<A HREF=\"\\1\" TARGET='{$config['cf_link_target']}'>\\1</A>", $str);
    $str = preg_replace("/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[一-龥豈-鶴\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<A HREF=\"\\2\" TARGET=\"{$config['cf_link_target']}\">\\2</A>", $str);
    $str = preg_replace("/(^|[\"'\s(])(www\.[^\"'\s()]+)/i", "\\1<A HREF=\"http://\\2\" TARGET=\"{$config['cf_link_target']}\">\\2</A>", $str);
    $str = preg_replace("/[0-9a-z_-]+@[a-z0-9._-]{4,}/i", "<a href=\"mailto:\\0\">\\0</a>", $str);
    $str = str_replace(array("\t_nbsp_\t", "\t_lt_\t", "\t_gt_\t", "'"), array("&nbsp;", "&lt;", "&gt;", "&#039;"), $str);

    /*
    // 提升速度 031011
    $str = preg_replace("/&lt;/", "\t_lt_\t", $str);
    $str = preg_replace("/&gt;/", "\t_gt_\t", $str);
    $str = preg_replace("/&amp;/", "&", $str);
    $str = preg_replace("/&quot;/", "\"", $str);
    $str = preg_replace("/&nbsp;/", "\t_nbsp_\t", $str);
    $str = preg_replace("/([^(http:\/\/)]|\(|^)(www\.[^[:space:]]+)/i", "\\1<A HREF=\"http://\\2\" TARGET='{$config['cf_link_target']}'>\\2</A>", $str);
    //$str = preg_replace("/([^(HREF=\"?'?)|(SRC=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,]+)/i", "\\1<A HREF=\"\\2\" TARGET='$config['cf_link_target']'>\\2</A>", $str);
    // 100825 : () 添加
    // 120315 : CHARSET 由于存在字符被截断现象而修改
    $str = preg_replace("/([^(HREF=\"?'?)|(SRC=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[一-龥豈-鶴\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<A HREF=\"\\2\" TARGET='{$config['cf_link_target']}'>\\2</A>", $str);

    // 邮件地址正则表达式 061004
    //$str = preg_replace("/(([a-z0-9_]|\-|\.)+@([^[:space:]]*)([[:alnum:]-]))/i", "<a href='mailto:\\1'>\\1</a>", $str);
    $str = preg_replace("/([0-9a-z]([-_\.]?[0-9a-z])*@[0-9a-z]([-_\.]?[0-9a-z])*\.[a-z]{2,4})/i", "<a href='mailto:\\1'>\\1</a>", $str);
    $str = preg_replace("/\t_nbsp_\t/", "&nbsp;" , $str);
    $str = preg_replace("/\t_lt_\t/", "&lt;", $str);
    $str = preg_replace("/\t_gt_\t/", "&gt;", $str);
    */

    return $str;
}


// url地址中添加http://
function set_http($url)
{
    if (!trim($url)) return;

    if (!preg_match("/^(http|https|ftp|telnet|news|mms)\:\/\//i", $url))
        $url = "http://" . $url;

    return $url;
}


// 获取文件大小
//function get_filesize($file)
function get_filesize($size)
{
    //$size = @filesize(addslashes($file));
    if ($size >= 1048576) {
        $size = number_format($size/1048576, 1) . "M";
    } else if ($size >= 1024) {
        $size = number_format($size/1024, 1) . "K";
    } else {
        $size = number_format($size, 0) . "byte";
    }
    return $size;
}


// 获取主题附件（返回排列）
function get_file($bo_table, $wr_id)
{
    global $g5, $qstr;

    $file['count'] = 0;
    $sql = " select * from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '$wr_id' order by bf_no ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result))
    {
        $no = $row['bf_no'];
        $file[$no]['href'] = G5_BBS_URL."/download.php?bo_table=$bo_table&amp;wr_id=$wr_id&amp;no=$no" . $qstr;
        $file[$no]['download'] = $row['bf_download'];
        // 4.00.11 - 添加文件路径
        $file[$no]['path'] = G5_DATA_URL.'/file/'.$bo_table;
        $file[$no]['size'] = get_filesize($row['bf_filesize']);
        $file[$no]['datetime'] = $row['bf_datetime'];
        $file[$no]['source'] = addslashes($row['bf_source']);
        $file[$no]['bf_content'] = $row['bf_content'];
        $file[$no]['content'] = get_text($row['bf_content']);
        //$file[$no]['view'] = view_file_link($row['bf_file'], $file[$no]['content']);
        $file[$no]['view'] = view_file_link($row['bf_file'], $row['bf_width'], $row['bf_height'], $file[$no]['content']);
        $file[$no]['file'] = $row['bf_file'];
        $file[$no]['image_width'] = $row['bf_width'] ? $row['bf_width'] : 640;
        $file[$no]['image_height'] = $row['bf_height'] ? $row['bf_height'] : 480;
        $file[$no]['image_type'] = $row['bf_type'];
        $file['count']++;
    }

    return $file;
}


// 文件夹大小 ($dir请勿包含 / )
function get_dirsize($dir)
{
    $size = 0;
    $d = dir($dir);
    while ($entry = $d->read()) {
        if ($entry != '.' && $entry != '..') {
            $size += filesize($dir.'/'.$entry);
        }
    }
    $d->close();
    return $size;
}


/*************************************************************************
**
**  gnuboard关联函数整合
**
*************************************************************************/


// 为了显示主题信息($write_row)复制及加工$list信息
function get_list($write_row, $board, $skin_url, $subject_len=40)
{
    global $g5, $config;
    global $qstr, $page;

    //$t = get_microtime();

    // 复制全部排列
    $list = $write_row;
    unset($write_row);

    $board_notice = array_map('trim', explode(',', $board['bo_notice']));
    $list['is_notice'] = in_array($list['wr_id'], $board_notice);

    if ($subject_len)
        $list['subject'] = conv_subject($list['wr_subject'], $subject_len, '…');
    else
        $list['subject'] = conv_subject($list['wr_subject'], $board['bo_subject_len'], '…');

    // 优化目录中预览内容是的速度
    if ($board['bo_use_list_content'])
	{
		$html = 0;
		if (strstr($list['wr_option'], 'html1'))
			$html = 1;
		else if (strstr($list['wr_option'], 'html2'))
			$html = 2;

        $list['content'] = conv_content($list['wr_content'], $html);
	}

    $list['comment_cnt'] = '';
    if ($list['wr_comment'])
        $list['comment_cnt'] = "<span class=\"cnt_cmt\">".$list['wr_comment']."</span>";

    // 如果是当日则显示时间
    $list['datetime'] = substr($list['wr_datetime'],0,10);
    $list['datetime2'] = $list['wr_datetime'];
    if ($list['datetime'] == G5_TIME_YMD)
        $list['datetime2'] = substr($list['datetime2'],11,5);
    else
        $list['datetime2'] = substr($list['datetime2'],5,5);
    // 4.1
    $list['last'] = substr($list['wr_last'],0,10);
    $list['last2'] = $list['wr_last'];
    if ($list['last'] == G5_TIME_YMD)
        $list['last2'] = substr($list['last2'],11,5);
    else
        $list['last2'] = substr($list['last2'],5,5);

    $list['wr_homepage'] = get_text(addslashes($list['wr_homepage']));

    $tmp_name = get_text(cut_str($list['wr_name'], $config['cf_cut_name'])); // 仅显示设定长度
    if ($board['bo_use_sideview'])
        $list['name'] = get_sideview($list['mb_id'], $tmp_name, $list['wr_email'], $list['wr_homepage']);
    else
        $list['name'] = '<span class="'.($list['mb_id']?'sv_member':'sv_guest').'">'.$tmp_name.'</span>';

    $reply = $list['wr_reply'];

    $list['reply'] = strlen($reply)*10;

    $list['icon_reply'] = '';
    if ($list['reply'])
        $list['icon_reply'] = '<img src="'.$skin_url.'/img/icon_reply.gif" style="margin-left:'.$list['reply'].'px;" alt="回帖">';

    $list['icon_link'] = '';
    if ($list['wr_link1'] || $list['wr_link2'])
        $list['icon_link'] = '<img src="'.$skin_url.'/img/icon_link.gif" alt="链接地址">';

    // 分类链接
    $list['ca_name_href'] = G5_BBS_URL.'/board.php?bo_table='.$board['bo_table'].'&amp;sca='.urlencode($list['ca_name']);

    $list['href'] = G5_BBS_URL.'/board.php?bo_table='.$board['bo_table'].'&amp;wr_id='.$list['wr_id'].$qstr;
    $list['comment_href'] = $list['href'];

    $list['icon_new'] = '';
    if ($board['bo_new'] && $list['wr_datetime'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - ($board['bo_new'] * 3600)))
        $list['icon_new'] = '<img src="'.$skin_url.'/img/icon_new.gif" alt="新主题">';

    $list['icon_hot'] = '';
    if ($board['bo_hot'] && $list['wr_hit'] >= $board['bo_hot'])
        $list['icon_hot'] = '<img src="'.$skin_url.'/img/icon_hot.gif" alt="热门">';

    $list['icon_secret'] = '';
    if (strstr($list['wr_option'], 'secret'))
        $list['icon_secret'] = '<img src="'.$skin_url.'/img/icon_secret.gif" alt="加密贴">';

    // 链接
    for ($i=1; $i<=G5_LINK_COUNT; $i++) {
        $list['link'][$i] = set_http(get_text($list["wr_link{$i}"]));
        $list['link_href'][$i] = G5_BBS_URL.'/link.php?bo_table='.$board['bo_table'].'&amp;wr_id='.$list['wr_id'].'&amp;no='.$i.$qstr;
        $list['link_hit'][$i] = (int)$list["wr_link{$i}_hit"];
    }

    // 可变参数
    if ($board['bo_use_list_file'] || ($list['wr_file'] && $subject_len == 255) /* view 时 */) {
        $list['file'] = get_file($board['bo_table'], $list['wr_id']);
    } else {
        $list['file']['count'] = $list['wr_file'];
    }

    if ($list['file']['count'])
        $list['icon_file'] = '<img src="'.$skin_url.'/img/icon_file.gif" alt="附件">';

    return $list;
}

// get_list 的 alias
function get_view($write_row, $board, $skin_url)
{
    return get_list($write_row, $board, $skin_url, 255);
}


// 使用search_font() 代替set_search_font(), get_search_font()
function search_font($stx, $str)
{
    global $config;

    // 文字前添加\
    $src = array('/', '|');
    $dst = array('\/', '\|');

    if (!trim($stx)) return $str;

    // 所有搜索关键词使用空格分隔
    $s = explode(' ', $stx);

    // "/(搜索1|搜索2)/i" 	搜索字段
    $pattern = '';
    $bar = '';
    for ($m=0; $m<count($s); $m++) {
        if (trim($s[$m]) == '') continue;
        // 不应该包含代码，但效果不理想
        //$pattern .= $bar . '([^<])(' . quotemeta($s[$m]) . ')';
        //$pattern .= $bar . quotemeta($s[$m]);
        //$pattern .= $bar . str_replace("/", "\/", quotemeta($s[$m]));
        $tmp_str = quotemeta($s[$m]);
        $tmp_str = str_replace($src, $dst, $tmp_str);
        $pattern .= $bar . $tmp_str . "(?![^<]*>)";
        $bar = "|";
    }

    // 指定搜索结果字体颜色及背景色
    $replace = "<b class=\"sch_word\">\\1</b>";

    return preg_replace("/($pattern)/i", $replace, $str);
}


// 标题转换
function conv_subject($subject, $len, $suffix='')
{
    return get_text(cut_str($subject, $len, $suffix));
}

// 内容转换
function conv_content($content, $html, $filter=true)
{
    global $config, $board;

    if ($html)
    {
        $source = array();
        $target = array();

        $source[] = "//";
        $target[] = "";

        if ($html == 2) { // 自动换行
            $source[] = "/\n/";
            $target[] = "<br/>";
        }

        // 计算表格tag 防止表格分裂
        $table_begin_count = substr_count(strtolower($content), "<table");
        $table_end_count = substr_count(strtolower($content), "</table");
        for ($i=$table_end_count; $i<$table_begin_count; $i++)
        {
            $content .= "</table>";
        }

        $content = preg_replace($source, $target, $content);

        if($filter)
            $content = html_purifier($content);
    }
    else // 如果是text
    {
        // & 处理 : &amp; &nbsp; 正常显示
        $content = html_symbol($content);

        // 空白处理
		//$content = preg_replace("/  /", "&nbsp; ", $content);
		$content = str_replace("  ", "&nbsp; ", $content);
		$content = str_replace("\n ", "\n&nbsp;", $content);

        $content = get_text($content, 1);
        $content = url_auto_link($content);
    }

    return $content;
}


// http://htmlpurifier.org/
// Standards-Compliant HTML Filtering
// Safe  : HTML Purifier defeats XSS with an audited whitelist
// Clean : HTML Purifier ensures standards-compliant output
// Open  : HTML Purifier is open-source and highly customizable
function html_purifier($html)
{
    $f = file(G5_PLUGIN_PATH.'/htmlpurifier/safeiframe.txt');
    $domains = array();
    foreach($f as $domain){
        // 首行是#时注释
        if (!preg_match("/^#/", $domain)) {
            $domain = trim($domain);
            if ($domain)
                array_push($domains, $domain);
        }
    }
    // 添加域名
    array_push($domains, $_SERVER['HTTP_HOST'].'/');
    $safeiframe = implode('|', $domains);

    include_once(G5_PLUGIN_PATH.'/htmlpurifier/HTMLPurifier.standalone.php');
    $config = HTMLPurifier_Config::createDefault();
    // 在data/cache目录中创建CSS、HTML、URI等目录
    $config->set('Cache.SerializerPath', G5_DATA_PATH.'/cache');
    $config->set('HTML.SafeEmbed', true);
    $config->set('HTML.SafeObject', true);
    $config->set('HTML.SafeIframe', true);
    $config->set('URI.SafeIframeRegexp','%^(https?:)?//('.$safeiframe.')%');
    $config->set('Attr.AllowedFrameTargets', array('_blank'));
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}


// 获取搜索语句
function get_sql_search($search_ca_name, $search_field, $search_text, $search_operator='and')
{
    global $g5;

    $str = "";
    if ($search_ca_name)
        $str = " ca_name = '$search_ca_name' ";

    $search_text = strip_tags(($search_text));
    $search_text = trim(stripslashes($search_text));

    if (!$search_text) {
        if ($search_ca_name) {
            return $str;
        } else {
            return '0';
        }
    }

    if ($str)
        $str .= " and ";

    // 为了提高查询速度尽可能减少()
    $op1 = "";

    // 使用空格符分隔搜索字段
    $s = array();
    $s = explode(" ", $search_text);

    // 使用（+）分隔搜索字段
    $tmp = array();
    $tmp = explode(",", trim($search_field));
    $field = explode("||", $tmp[0]);
    $not_comment = "";
    if (!empty($tmp[1]))
        $not_comment = $tmp[1];

    $str .= "(";
    for ($i=0; $i<count($s); $i++) {
        // 关键词
        $search_str = trim($s[$i]);
        if ($search_str == "") continue;

        // 热门关键词
        insert_popular($field, $search_str);

        $str .= $op1;
        $str .= "(";

        $op2 = "";
        for ($k=0; $k<count($field); $k++) { // 根据字段实现多字段搜索

            // SQL Injection 注入防范
            // 字段中包含 a-z A-Z 0-9 _ , | 意外参数时 设定为wr_subject
            $field[$k] = preg_match("/^[\w\,\|]+$/", $field[$k]) ? $field[$k] : "wr_subject";

            $str .= $op2;
            switch ($field[$k]) {
                case "mb_id" :
                case "wr_name" :
                    $str .= " $field[$k] = '$s[$i]' ";
                    break;
                case "wr_hit" :
                case "wr_good" :
                case "wr_nogood" :
                    $str .= " $field[$k] >= '$s[$i]' ";
                    break;
                // 序号为对应关键词-1
                case "wr_num" :
                    $str .= "$field[$k] = ".((-1)*$s[$i]);
                    break;
                case "wr_ip" :
                case "wr_password" :
                    $str .= "1=0"; // 始终为假值
                    break;
                // INSTR速度快过LIKE
                default :
                    if (preg_match("/[a-zA-Z]/", $search_str))
                        $str .= "INSTR(LOWER($field[$k]), LOWER('$search_str'))";
                    else
                        $str .= "INSTR($field[$k], '$search_str')";
                    break;
            }
            $op2 = " or ";
        }
        $str .= ")";

        $op1 = " $search_operator ";
    }
    $str .= " ) ";
    if ($not_comment)
        $str .= " and wr_is_comment = '0' ";

    return $str;
}


// 获取论坛数据表
function get_write($write_table, $wr_id)
{
    return sql_fetch(" select * from $write_table where wr_id = '$wr_id' ");
}


// 获取下一个文章的编号
function get_next_num($table)
{
    // 获取最小编号
    $sql = " select min(wr_num) as min_wr_num from $table ";
    $row = sql_fetch($sql);
    // 最小的编号中去掉1后传递
    return (int)($row['min_wr_num'] - 1);
}


// 论坛群组设定数据中读取
function get_group($gr_id)
{
    global $g5;

    return sql_fetch(" select * from {$g5['group_table']} where gr_id = '$gr_id' ");
}


// 获取会员信息
function get_member($mb_id, $fields='*')
{
    global $g5;

    return sql_fetch(" select $fields from {$g5['member_table']} where mb_id = TRIM('$mb_id') ");
}


// 为了日期、点击等显示排列增加$flag
// $flag : asc 降序 , desc 升序
// 按照标题排列的QUERY STRING
function subject_sort_link($col, $query_string='', $flag='asc')
{
    global $sst, $sod, $sfl, $stx, $page;

    $q1 = "sst=$col";
    if ($flag == 'asc')
    {
        $q2 = 'sod=asc';
        if ($sst == $col)
        {
            if ($sod == 'asc')
            {
                $q2 = 'sod=desc';
            }
        }
    }
    else
    {
        $q2 = 'sod=desc';
        if ($sst == $col)
        {
            if ($sod == 'desc')
            {
                $q2 = 'sod=asc';
            }
        }
    }

    $arr_query = array();
    $arr_query[] = $query_string;
    $arr_query[] = $q1;
    $arr_query[] = $q2;
    $arr_query[] = 'sfl='.$sfl;
    $arr_query[] = 'stx='.$stx;
    $arr_query[] = 'page='.$page;
    $qstr = implode("&amp;", $arr_query);

    return "<a href=\"{$_SERVER['PHP_SELF']}?{$qstr}\">";
}


// 获取管理员信息
function get_admin($admin='super', $fields='*')
{
    global $config, $group, $board;
    global $g5;

    $is = false;
    if ($admin == 'board') {
        $mb = sql_fetch("select {$fields} from {$g5['member_table']} where mb_id in ('{$board['bo_admin']}') limit 1 ");
        $is = true;
    }

    if (($is && !$mb['mb_id']) || $admin == 'group') {
        $mb = sql_fetch("select {$fields} from {$g5['member_table']} where mb_id in ('{$group['gr_admin']}') limit 1 ");
        $is = true;
    }

    if (($is && !$mb['mb_id']) || $admin == 'super') {
        $mb = sql_fetch("select {$fields} from {$g5['member_table']} where mb_id in ('{$config['cf_admin']}') limit 1 ");
    }

    return $mb;
}


// 判断管理员
function is_admin($mb_id)
{
    global $config, $group, $board;

    if (!$mb_id) return;

    if ($config['cf_admin'] == $mb_id) return 'super';
    if (isset($group['gr_admin']) && ($group['gr_admin'] == $mb_id)) return 'group';
    if (isset($board['bo_admin']) && ($board['bo_admin'] == $mb_id)) return 'board';
    return '';
}


// 获取分类选项
// 4.00中除去分类数据表使用论坛数据表内数据
function get_category_option($bo_table='', $ca_name='')
{
    global $g5, $board, $is_admin;

    $categories = explode("|", $board['bo_category_list'].($is_admin?"|公告":"")); // 使用逗号分隔(,)
    $str = "";
    for ($i=0; $i<count($categories); $i++) {
        $category = trim($categories[$i]);
        if (!$category) continue;

        $str .= "<option value=\"$categories[$i]\"";
        if ($category == $ca_name) {
            $str .= ' selected="selected"';
        }
        $str .= ">$categories[$i]</option>\n";
    }

    return $str;
}


// SELECT方式获取群组
function get_group_select($name, $selected='', $event='')
{
    global $g5, $is_admin, $member;

    $sql = " select gr_id, gr_subject from {$g5['group_table']} a ";
    if ($is_admin == "group") {
        $sql .= " left join {$g5['member_table']} b on (b.mb_id = a.gr_admin)
                  where b.mb_id = '{$member['mb_id']}' ";
    }
    $sql .= " order by a.gr_id ";

    $result = sql_query($sql);
    $str = "<select id=\"$name\" name=\"$name\" $event>\n";
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($i == 0) $str .= "<option value=\"\">选择</option>";
        $str .= option_selected($row['gr_id'], $selected, $row['gr_subject']);
    }
    $str .= "</select>";
    return $str;
}


function option_selected($value, $selected, $text='')
{
    if (!$text) $text = $value;
    if ($value == $selected)
        return "<option value=\"$value\" selected=\"selected\">$text</option>\n";
    else
        return "<option value=\"$value\">$text</option>\n";
}


// SELECT方式获取'是', '否'
function get_yn_select($name, $selected='1', $event='')
{
    $str = "<select name=\"$name\" $event>\n";
    if ($selected) {
        $str .= "<option value=\"1\" selected>是</option>\n";
        $str .= "<option value=\"0\">否</option>\n";
    } else {
        $str .= "<option value=\"1\">是</option>\n";
        $str .= "<option value=\"0\" selected>否</option>\n";
    }
    $str .= "</select>";
    return $str;
}


// 增加积分
function insert_point($mb_id, $point, $content='', $rel_table='', $rel_id='', $rel_action='', $expire=0)
{
    global $config;
    global $g5;
    global $is_admin;

    // 如果关闭积分功能时 return
    if (!$config['cf_use_point']) { return 0; }

    // 无积分信息无需更新
    if ($point == 0) { return 0; }

    // 无会员id积分不进行更新
    if ($mb_id == '') { return 0; }
    $mb = sql_fetch(" select mb_id from {$g5['member_table']} where mb_id = '$mb_id' ");
    if (!$mb['mb_id']) { return 0; }

    // 会员积分
    $mb_point = get_point_sum($mb_id);

    // 跳过已登记详情
    if ($rel_table || $rel_id || $rel_action)
    {
        $sql = " select count(*) as cnt from {$g5['point_table']}
                  where mb_id = '$mb_id'
                    and po_rel_table = '$rel_table'
                    and po_rel_id = '$rel_id'
                    and po_rel_action = '$rel_action' ";
        $row = sql_fetch($sql);
        if ($row['cnt'])
            return -1;
    }

    // 按条创建积分
    $po_expire_date = '9999-12-31';
    if($config['cf_point_term'] > 0) {
        if($expire > 0)
            $po_expire_date = date('Y-m-d', strtotime('+'.($expire - 1).' days', G5_SERVER_TIME));
        else
            $po_expire_date = date('Y-m-d', strtotime('+'.($config['cf_point_term'] - 1).' days', G5_SERVER_TIME));
    }

    $po_expired = 0;
    if($point < 0) {
        $po_expired = 1;
        $po_expire_date = G5_TIME_YMD;
    }
    $po_mb_point = $mb_point + $point;

    $sql = " insert into {$g5['point_table']}
                set mb_id = '$mb_id',
                    po_datetime = '".G5_TIME_YMDHIS."',
                    po_content = '".addslashes($content)."',
                    po_point = '$point',
                    po_use_point = '0',
                    po_mb_point = '$po_mb_point',
                    po_expired = '$po_expired',
                    po_expire_date = '$po_expire_date',
                    po_rel_table = '$rel_table',
                    po_rel_id = '$rel_id',
                    po_rel_action = '$rel_action' ";
    sql_query($sql);

    // 如果开启积分功能时记录到积分使用记录中
    if($point < 0) {
        insert_use_point($mb_id, $point);
    }

    // 积分 UPDATE
    $sql = " update {$g5['member_table']} set mb_point = '$po_mb_point' where mb_id = '$mb_id' ";
    sql_query($sql);

    return 1;
}

// 输入已使用积分
function insert_use_point($mb_id, $point, $po_id='')
{
    global $g5, $config;

    if($config['cf_point_term'])
        $sql_order = " order by po_expire_date asc, po_id asc ";
    else
        $sql_order = " order by po_id asc ";

    $point1 = abs($point);
    $sql = " select po_id, po_point, po_use_point
                from {$g5['point_table']}
                where mb_id = '$mb_id'
                  and po_id <> '$po_id'
                  and po_expired = '0'
                  and po_point > po_use_point
                $sql_order ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++) {
        $point2 = $row['po_point'];
        $point3 = $row['po_use_point'];

        if(($point2 - $point3) > $point1) {
            $sql = " update {$g5['point_table']}
                        set po_use_point = po_use_point + '$point1'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            break;
        } else {
            $point4 = $point2 - $point3;
            $sql = " update {$g5['point_table']}
                        set po_use_point = po_use_point + '$point4',
                            po_expired = '100'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            $point1 -= $point4;
        }
    }
}

// 删除已使用积分
function delete_use_point($mb_id, $point)
{
    global $g5, $config;

    if($config['cf_point_term'])
        $sql_order = " order by po_expire_date desc, po_id desc ";
    else
        $sql_order = " order by po_id desc ";

    $point1 = abs($point);
    $sql = " select po_id, po_use_point, po_expired, po_expire_date
                from {$g5['point_table']}
                where mb_id = '$mb_id'
                  and po_expired <> '1'
                  and po_use_point > 0
                $sql_order ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++) {
        $point2 = $row['po_use_point'];

        $po_expired = $row['po_expired'];
        if($row['po_expired'] == 100 && ($row['po_expire_date'] == '9999-12-31' || $row['po_expire_date'] >= G5_TIME_YMD))
            $po_expired = 0;

        if($point2 > $point1) {
            $sql = " update {$g5['point_table']}
                        set po_use_point = po_use_point - '$point1',
                            po_expired = '$po_expired'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            break;
        } else {
            $sql = " update {$g5['point_table']}
                        set po_use_point = '0',
                            po_expired = '$po_expired'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);

            $point1 -= $point2;
        }
    }
}

// 删除失效积分
function delete_expire_point($mb_id, $point)
{
    global $g5, $config;

    $point1 = abs($point);
    $sql = " select po_id, po_use_point, po_expired, po_expire_date
                from {$g5['point_table']}
                where mb_id = '$mb_id'
                  and po_expired = '1'
                  and po_point >= 0
                  and po_use_point > 0
                order by po_expire_date desc, po_id desc ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++) {
        $point2 = $row['po_use_point'];
        $po_expired = '0';
        $po_expire_date = '9999-12-31';
        if($config['cf_point_term'] > 0)
            $po_expire_date = date('Y-m-d', strtotime('+'.($config['cf_point_term'] - 1).' days', G5_SERVER_TIME));

        if($point2 > $point1) {
            $sql = " update {$g5['point_table']}
                        set po_use_point = po_use_point - '$point1',
                            po_expired = '$po_expired',
                            po_expire_date = '$po_expire_date'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            break;
        } else {
            $sql = " update {$g5['point_table']}
                        set po_use_point = '0',
                            po_expired = '$po_expired',
                            po_expire_date = '$po_expire_date'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);

            $point1 -= $point2;
        }
    }
}

// 合计积分详情
function get_point_sum($mb_id)
{
    global $g5, $config;

    if($config['cf_point_term'] > 0) {
        // 如有失效积分则添加记录
        $expire_point = get_expire_point($mb_id);
        if($expire_point > 0) {
            $mb = get_member($mb_id, 'mb_point');
            $content = '积分失效';
            $rel_table = '@expire';
            $rel_id = $mb_id;
            $rel_action = 'expire'.'-'.uniqid('');
            $point = $expire_point * (-1);
            $po_mb_point = $mb['mb_point'] + $point;
            $po_expire_date = G5_TIME_YMD;
            $po_expired = 1;

            $sql = " insert into {$g5['point_table']}
                        set mb_id = '$mb_id',
                            po_datetime = '".G5_TIME_YMDHIS."',
                            po_content = '".addslashes($content)."',
                            po_point = '$point',
                            po_use_point = '0',
                            po_mb_point = '$po_mb_point',
                            po_expired = '$po_expired',
                            po_expire_date = '$po_expire_date',
                            po_rel_table = '$rel_table',
                            po_rel_id = '$rel_id',
                            po_rel_action = '$rel_action' ";
            sql_query($sql);

            // 如果开启积分功能时记录到积分使用记录中
            if($point < 0) {
                insert_use_point($mb_id, $point);
            }
        }

        // 如有有效期则检查超出时间的积分expired
        $sql = " update {$g5['point_table']}
                    set po_expired = '1'
                    where mb_id = '$mb_id'
                      and po_expired <> '1'
                      and po_expire_date <> '9999-12-31'
                      and po_expire_date < '".G5_TIME_YMD."' ";
        sql_query($sql);
    }

    // 积分合计
    $sql = " select sum(po_point) as sum_po_point
                from {$g5['point_table']}
                where mb_id = '$mb_id' ";
    $row = sql_fetch($sql);

    return $row['sum_po_point'];
}

// 失效积分
function get_expire_point($mb_id)
{
    global $g5, $config;

    if($config['cf_point_term'] == 0)
        return 0;

    $sql = " select sum(po_point - po_use_point) as sum_point
                from {$g5['point_table']}
                where mb_id = '$mb_id'
                  and po_expired = '0'
                  and po_expire_date <> '9999-12-31'
                  and po_expire_date < '".G5_TIME_YMD."' ";
    $row = sql_fetch($sql);

    return $row['sum_point'];
}

// 删除积分
function delete_point($mb_id, $rel_table, $rel_id, $rel_action)
{
    global $g5;

    $result = false;
    if ($rel_table || $rel_id || $rel_action)
    {
        // 积分详情
        $sql = " select * from {$g5['point_table']}
                    where mb_id = '$mb_id'
                      and po_rel_table = '$rel_table'
                      and po_rel_id = '$rel_id'
                      and po_rel_action = '$rel_action' ";
        $row = sql_fetch($sql);

        if($row['po_point'] < 0) {
            $mb_id = $row['mb_id'];
            $po_point = abs($row['po_point']);

            delete_use_point($mb_id, $po_point);
        } else {
            if($row['po_use_point'] > 0) {
                insert_use_point($row['mb_id'], $row['po_use_point'], $row['po_id']);
            }
        }

        $result = sql_query(" delete from {$g5['point_table']}
                     where mb_id = '$mb_id'
                       and po_rel_table = '$rel_table'
                       and po_rel_id = '$rel_id'
                       and po_rel_action = '$rel_action' ", false);

        // 反映到po_mb_point
        $sql = " update {$g5['point_table']}
                    set po_mb_point = po_mb_point - '{$row['po_point']}'
                    where mb_id = '$mb_id'
                      and po_id > '{$row['po_id']}' ";
        sql_query($sql);

        // 合计积分详情积分
        $sum_point = get_point_sum($mb_id);

        // 积分 UPDATE
        $sql = " update {$g5['member_table']} set mb_point = '$sum_point' where mb_id = '$mb_id' ";
        $result = sql_query($sql);
    }

    return $result;
}

// 会员信息
function get_sideview($mb_id, $name='', $email='', $homepage='')
{
    global $config;
    global $g5;
    global $bo_table, $sca, $is_admin, $member;

    $email = base64_encode($email);
    $homepage = set_http(clean_xss_tags($homepage));

    $name = preg_replace("/\&#039;/", "", $name);
    $name = preg_replace("/\'/", "", $name);
    $name = preg_replace("/\"/", "&#034;", $name);
    $title_name = $name;

    $tmp_name = "";
    if ($mb_id) {
        //$tmp_name = "<a href=\"".G5_BBS_URL."/profile.php?mb_id=".$mb_id."\" class=\"sv_member\" title=\"$name 自我介绍\" target=\"_blank\" onclick=\"return false;\">$name</a>";
        $tmp_name = '<a href="'.G5_BBS_URL.'/profile.php?mb_id='.$mb_id.'" class="sv_member" title="'.$name.' 自我介绍" target="_blank" onclick="return false;">';

        if ($config['cf_use_member_icon']) {
            $mb_dir = substr($mb_id,0,2);
            $icon_file = G5_DATA_PATH.'/member/'.$mb_dir.'/'.$mb_id.'.gif';

            if (file_exists($icon_file)) {
                $width = $config['cf_member_icon_width'];
                $height = $config['cf_member_icon_height'];
                $icon_file_url = G5_DATA_URL.'/member/'.$mb_dir.'/'.$mb_id.'.gif';
                $tmp_name .= '<img src="'.$icon_file_url.'" width="'.$width.'" height="'.$height.'" alt="">';

                if ($config['cf_use_member_icon'] == 2) // 会员头像+姓名
                    $tmp_name = $tmp_name.' '.$name;
            } else {
                  $tmp_name = $tmp_name." ".$name;
            }
        } else {
            $tmp_name = $tmp_name.' '.$name;
        }
        $tmp_name .= '</a>';

        $title_mb_id = '['.$mb_id.']';
    } else {
        if(!$bo_table)
            return $name;

        $tmp_name = '<a href="'.G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;sca='.$sca.'&amp;sfl=wr_name,1&amp;stx='.$name.'" title="'.$name.' 使用名称搜索" class="sv_guest" onclick="return false;">'.$name.'</a>';
        $title_mb_id = '[游客]';
    }

    $name     = get_text($name);
    $email    = get_text($email);
    $homepage = get_text($homepage);

    $str = "<span class=\"sv_wrap\">\n";
    $str .= $tmp_name."\n";

    $str2 = "<span class=\"sv\">\n";
    if($mb_id)
        $str2 .= "<a href=\"".G5_BBS_URL."/memo_form.php?me_recv_mb_id=".$mb_id."\" onclick=\"win_memo(this.href); return false;\">发送站内信</a>\n";
    if($email)
        $str2 .= "<a href=\"".G5_BBS_URL."/formmail.php?mb_id=".$mb_id."&amp;name=".urlencode($name)."&amp;email=".$email."\" onclick=\"win_email(this.href); return false;\">发送邮件</a>\n";
    if($homepage)
        $str2 .= "<a href=\"".$homepage."\" target=\"_blank\">网站主页</a>\n";
    if($mb_id)
        $str2 .= "<a href=\"".G5_BBS_URL."/profile.php?mb_id=".$mb_id."\" onclick=\"win_profile(this.href); return false;\">自我介绍</a>\n";
    if($bo_table) {
        if($mb_id)
            $str2 .= "<a href=\"".G5_BBS_URL."/board.php?bo_table=".$bo_table."&amp;sca=".$sca."&amp;sfl=mb_id,1&amp;stx=".$mb_id."\">使用ID搜索</a>\n";
        else
            $str2 .= "<a href=\"".G5_BBS_URL."/board.php?bo_table=".$bo_table."&amp;sca=".$sca."&amp;sfl=wr_name,1&amp;stx=".$name."\">使用名称搜索</a>\n";
    }
    if($mb_id)
        $str2 .= "<a href=\"".G5_BBS_URL."/new.php?mb_id=".$mb_id."\">所有主题</a>\n";
    if($g5['sms5_use_sideview']){
        $mb = get_member($mb_id, " mb_open, mb_sms , mb_hp ");
        if( $mb['mb_open'] && $mb['mb_sms'] && $mb['mb_hp'] ) $str2 .= "<a href=\"".G5_SMS5_URL."/?mb_id=".$mb_id."\" class=\"win_sms5\" target=\"_blank\">发送短信</a>\n";
    }
    if($is_admin == "super" && $mb_id) {
        $str2 .= "<a href=\"".G5_ADMIN_URL."/member_form.php?w=u&amp;mb_id=".$mb_id."\" target=\"_blank\">修改会员信息</a>\n";
        $str2 .= "<a href=\"".G5_ADMIN_URL."/point_list.php?sfl=mb_id&amp;stx=".$mb_id."\" target=\"_blank\">积分详情</a>\n";
    }
    $str2 .= "</span>\n";
    $str .= $str2;
    $str .= "\n<noscript class=\"sv_nojs\">".$str2."</noscript>";

    $str .= "</span>";

    return $str;
}


// 显示文件链接（图片、flash、视频文件）
function view_file_link($file, $width, $height, $content='')
{
    global $config, $board;
    global $g5;
    static $ids;

    if (!$file) return;

    $ids++;

    // 如果图片宽度超过论坛限制宽度则修改图片宽度，并根据此图片原有比例计算图片高度
    if ($width > $board['bo_image_width'] && $board['bo_image_width'])
    {
        $rate = $board['bo_image_width'] / $width;
        $width = $board['bo_image_width'];
        $height = (int)($height * $rate);
    }

    // 如有宽度设置，则设置宽度与高度属性，如无也不进行自动计算
    if ($width)
        $attr = ' width="'.$width.'" height="'.$height.'" ';
    else
        $attr = '';

    if (preg_match("/\.({$config['cf_image_extension']})$/i", $file)) {
        $img = '<a href="'.G5_BBS_URL.'/view_image.php?bo_table='.$board['bo_table'].'&amp;fn='.urlencode($file).'" target="_blank" class="view_image">';
        $img .= '<img src="'.G5_DATA_URL.'/file/'.$board['bo_table'].'/'.urlencode($file).'" alt="'.$content.'">';
        $img .= '</a>';

        return $img;
    }
}


// view_file_link() 传递图片显示
// {img:0} ... {img:n} 相同格式
function view_image($view, $number, $attribute)
{
    if ($view['file'][$number]['view'])
        return preg_replace("/>$/", " $attribute>", $view['file'][$number]['view']);
    else
        //return "{".$number."号图片无效}";
        return "";
}


/*
// {link:0} ... {link:n} 相同格式
function view_link($view, $number, $attribute)
{
    global $config;

    if ($view['link'][$number]['link'])
    {
        if (!preg_match("/target/i", $attribute))
            $attribute .= " target='$config['cf_link_target']'";
        return "<a href='{$view['link'][$number]['href']}' $attribute>{$view['link'][$number]['link']}</a>";
    }
    else
        return "{".$number."号连接无效}";
}
*/


function cut_str($str, $len, $suffix="…")
{
    $arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    $str_len = count($arr_str);

    if ($str_len >= $len) {
        $slice_str = array_slice($arr_str, 0, $len);
        $str = join("", $slice_str);

        return $str . ($str_len > $len ? $suffix : '');
    } else {
        $str = join("", $arr_str);
        return $str;
    }
}


// TEXT 格式转换
function get_text($str, $html=0)
{
    /* 3.22版本后禁用(html换行错误)
    $source[] = "/  /";
    $target[] = " &nbsp;";
    */

    // 3.31
    // TEXT格式输出时转换&amp; &nbsp;可显示的正常符号
    if ($html == 0) {
        $str = html_symbol($str);
    }

    $source[] = "/</";
    $target[] = "&lt;";
    $source[] = "/>/";
    $target[] = "&gt;";
    //$source[] = "/\"/";
    //$target[] = "&#034;";
    $source[] = "/\'/";
    $target[] = "&#039;";
    //$source[] = "/}/"; $target[] = "&#125;";
    if ($html) {
        $source[] = "/\n/";
        $target[] = "<br/>";
    }

    return preg_replace($source, $target, $str);
}


/*
// HTML特殊文字变换htmlspecialchars
function hsc($str)
{
    $trans = array("\"" => "&#034;", "'" => "&#039;", "<"=>"&#060;", ">"=>"&#062;");
    $str = strtr($str, $trans);
    return $str;
}
*/

// 3.31
// HTML SYMBOL 变换
// &nbsp; &amp; &middot; 正常显示
function html_symbol($str)
{
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}


/*************************************************************************
**
**  SQL关联函数
**
*************************************************************************/

// DB连接
function sql_connect($host, $user, $pass)
{
    global $g5;

    return @mysql_connect($host, $user, $pass);
}


// DB选择
function sql_select_db($db, $connect)
{
    global $g5;

    return @mysql_select_db($db, $connect);
}


// mysql_query 和 mysql_error 同时处理
// mysql connect resource 指定 - 明朗废人提供
function sql_query($sql, $error=G5_DISPLAY_SQL_ERROR)
{
    global $g5;

    // Blind SQL Injection 漏洞补丁
    $sql = trim($sql);
    // 禁用union
    $sql = preg_replace("#^select.*from.*union.*#i", "select 1", $sql);
    // `information_schema` 数据库访问权限受限！
    $sql = preg_replace("#^select.*from.*where.*`?information_schema`?.*#i", "select 1", $sql);

    if ($error)
        $result = @mysql_query($sql, $g5['connect_db']) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : {$_SERVER['PHP_SELF']}");
    else
        $result = @mysql_query($sql, $g5['connect_db']);

    return $result;
}


// 执行查询后获取结果值
function sql_fetch($sql, $error=G5_DISPLAY_SQL_ERROR)
{
    $result = sql_query($sql, $error);
    //$row = @sql_fetch_array($result) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : $_SERVER['PHP_SELF']");
    $row = sql_fetch_array($result);
    return $row;
}


// 结果值中获取关联排列（根据名称）
function sql_fetch_array($result)
{
    $row = @mysql_fetch_assoc($result);
    return $row;
}


// 在内存中删除相关$result的所有内容
// s当ql_free_result()获取的结果过大导致内存占用过多时使用
// 执行结果随着script结束会自动清空内存
function sql_free_result($result)
{
    return mysql_free_result($result);
}


function sql_password($value)
{
    // mysql 4.0x 以下 password() 长度 16bytes
    // mysql 4.1x 以上 password() 长度 41bytes
    $row = sql_fetch(" select password('$value') as pass ");

    return $row['pass'];
}


// PHPMyAdmin 参考
function get_table_define($table, $crlf="\n")
{
    global $g5;

    // For MySQL < 3.23.20
    $schema_create .= 'CREATE TABLE ' . $table . ' (' . $crlf;

    $sql = 'SHOW FIELDS FROM ' . $table;
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result))
    {
        $schema_create .= '    ' . $row['Field'] . ' ' . $row['Type'];
        if (isset($row['Default']) && $row['Default'] != '')
        {
            $schema_create .= ' DEFAULT \'' . $row['Default'] . '\'';
        }
        if ($row['Null'] != 'YES')
        {
            $schema_create .= ' NOT NULL';
        }
        if ($row['Extra'] != '')
        {
            $schema_create .= ' ' . $row['Extra'];
        }
        $schema_create     .= ',' . $crlf;
    } // end while
    sql_free_result($result);

    $schema_create = preg_replace('/,' . $crlf . '$/', '', $schema_create);

    $sql = 'SHOW KEYS FROM ' . $table;
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result))
    {
        $kname    = $row['Key_name'];
        $comment  = (isset($row['Comment'])) ? $row['Comment'] : '';
        $sub_part = (isset($row['Sub_part'])) ? $row['Sub_part'] : '';

        if ($kname != 'PRIMARY' && $row['Non_unique'] == 0) {
            $kname = "UNIQUE|$kname";
        }
        if ($comment == 'FULLTEXT') {
            $kname = 'FULLTEXT|$kname';
        }
        if (!isset($index[$kname])) {
            $index[$kname] = array();
        }
        if ($sub_part > 1) {
            $index[$kname][] = $row['Column_name'] . '(' . $sub_part . ')';
        } else {
            $index[$kname][] = $row['Column_name'];
        }
    } // end while
    sql_free_result($result);

    while (list($x, $columns) = @each($index)) {
        $schema_create     .= ',' . $crlf;
        if ($x == 'PRIMARY') {
            $schema_create .= '    PRIMARY KEY (';
        } else if (substr($x, 0, 6) == 'UNIQUE') {
            $schema_create .= '    UNIQUE ' . substr($x, 7) . ' (';
        } else if (substr($x, 0, 8) == 'FULLTEXT') {
            $schema_create .= '    FULLTEXT ' . substr($x, 9) . ' (';
        } else {
            $schema_create .= '    KEY ' . $x . ' (';
        }
        $schema_create     .= implode($columns, ', ') . ')';
    } // end while

    $schema_create .= $crlf . ') ENGINE=MyISAM DEFAULT CHARSET=utf8';

    return $schema_create;
} // end of the 'PMA_getTableDef()' function


// referer检查
function referer_check($url='')
{
    /*
    // 由于无法正常工作注释
    global $g5;

    if (!$url)
        $url = G5_URL;

    if (!preg_match("/^http['s']?:\/\/".$_SERVER['HTTP_HOST']."/", $_SERVER['HTTP_REFERER']))
        alert("请使用正确方式访问", $url);
    */
}


// 星期
function get_yoil($date, $full=0)
{
    $arr_yoil = array ('日', '一', '二', '三', '四', '五', '六');

    $yoil = date("w", strtotime($date));
    $str = $arr_yoil[$yoil];
    if ($full) {
        $str .= '星期';
    }
    return $str;
}


// select获取日期
function date_select($date, $name='')
{
    global $g5;

    $s = '';
    if (substr($date, 0, 4) == "0000") {
        $date = G5_TIME_YMDHIS;
    }
    preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date, $m);

    // 年
    $s .= "<select name='{$name}_y'>";
    for ($i=$m['0']-3; $i<=$m['0']+3; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['0']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>年 \n";

    // 月
    $s .= "<select name='{$name}_m'>";
    for ($i=1; $i<=12; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['2']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>月 \n";

    // 日
    $s .= "<select name='{$name}_d'>";
    for ($i=1; $i<=31; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['3']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>日 \n";

    return $s;
}


// select获取时间
// 1.04.00
// 竞拍时间设置
function time_select($time, $name="")
{
    preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $time, $m);

    // 时
    $s .= "<select name='{$name}_h'>";
    for ($i=0; $i<=23; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['0']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>时 \n";

    // 分
    $s .= "<select name='{$name}_i'>";
    for ($i=0; $i<=59; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['2']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>分 \n";

    // 秒
    $s .= "<select name='{$name}_s'>";
    for ($i=0; $i<=59; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['3']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>秒 \n";

    return $s;
}


// 如果有demo文件则判断为演示状态
function check_demo()
{
    global $is_admin;
    if ($is_admin != 'super' && file_exists(G5_PATH.'/DEMO'))
        alert('当前功能无法在演示(Demo)模式下使用');
}


// 文字类检查
function check_string($str, $options)
{
    global $g5;

    $s = '';
    for($i=0;$i<strlen($str);$i++) {
        $c = $str[$i];
        $oc = ord($c);

        // 汉字
        if ($oc >= 0xA0 && $oc <= 0xFF) {
            if ($options & G5_CHINESE) {
                $s .= $c . $str[$i+1] . $str[$i+2];
            }
            $i+=2;
        }
        // 数字
        else if ($oc >= 0x30 && $oc <= 0x39) {
            if ($options & G5_NUMERIC) {
                $s .= $c;
            }
        }
        // 英文大写
        else if ($oc >= 0x41 && $oc <= 0x5A) {
            if (($options & G5_ALPHABETIC) || ($options & G5_ALPHAUPPER)) {
                $s .= $c;
            }
        }
        // 英文小写
        else if ($oc >= 0x61 && $oc <= 0x7A) {
            if (($options & G5_ALPHABETIC) || ($options & G5_ALPHALOWER)) {
                $s .= $c;
            }
        }
        // 空格
        else if ($oc == 0x20) {
            if ($options & G5_SPACE) {
                $s .= $c;
            }
        }
        else {
            if ($options & G5_SPECIAL) {
                $s .= $c;
            }
        }
    }

    // 比较传递值
    return ($str == $s);
}


// 防止汉字(2bytes)最后一个字节出现1byte的情况
// 为了防止文字显示错误将最后一个不完整的字节删除
function cut_hangul_last($chinese)
{
    global $g5;

    // 防止双字节文字（汉字）出现截断显示问号的现象
    $cnt = 0;
    for($i=0;$i<strlen($chinese);$i++) {
        // 仅计算汉字
        if (ord($chinese[$i]) >= 0xA0) {
            $cnt++;
        }
    }

    return $chinese;
}


// 检查数据表index
function explain($sql)
{
    if (preg_match("/^(select)/i", trim($sql))) {
        $q = "explain $sql";
        echo $q;
        $row = sql_fetch($q);
        if (!$row['key']) $row['key'] = "NULL";
        echo " <font color=blue>(type={$row['type']} , key={$row['key']})</font>";
    }
}

// 转换危险代码
function bad_tag_convert($code)
{
    global $view;
    global $member, $is_admin;

    if ($is_admin && $member['mb_id'] != $view['mb_id']) {
        //$code = preg_replace_callback("#(\<(embed|object)[^\>]*)\>(\<\/(embed|object)\>)?#i",
        // 未屏蔽embed或object时可以使用过滤而修改
        $code = preg_replace_callback("#(\<(embed|object)[^\>]*)\>?(\<\/(embed|object)\>)?#i",
                    create_function('$matches', 'return "<div class=\"embedx\">为了确保管理账户安全，无法使用管理员ID查看包含embed或object的内容</div>";'),
                    $code);
    }

    return preg_replace("/\<([\/]?)(script|iframe|form)([^\>]*)\>?/i", "&lt;$1$2$3&gt;", $code);
}


// token创建
function _token()
{
    return md5(uniqid(rand(), true));
}


// 防止非法入侵进行token创建
function get_token()
{
    $token = md5(uniqid(rand(), true));
    set_session('ss_token', $token);

    return $token;
}


// 比较post传递来的token与session中储存的token
function check_token()
{
    set_session('ss_token', '');
    return true;
}


// 检查是否包含utf8文字函数
// 代码 : http://in2.php.net/manual/en/function.mb-check-encoding.php#95289
function is_utf8($str)
{
    $len = strlen($str);
    for($i = 0; $i < $len; $i++) {
        $c = ord($str[$i]);
        if ($c > 128) {
            if (($c > 247)) return false;
            elseif ($c > 239) $bytes = 4;
            elseif ($c > 223) $bytes = 3;
            elseif ($c > 191) $bytes = 2;
            else return false;
            if (($i + $bytes) > $len) return false;
            while ($bytes > 1) {
                $i++;
                $b = ord($str[$i]);
                if ($b < 128 || $b > 191) return false;
                $bytes--;
            }
        }
    }
    return true;
}


// utf-8文字处理
// 出处 : https://www.google.co.kr/search?q=utf8_strcut&aq=f&oq=utf8_strcut&aqs=chrome.0.57j0l3.826j0&sourceid=chrome&ie=UTF-8
function utf8_strcut( $str, $size, $suffix='...' )
{
        $substr = substr( $str, 0, $size * 2 );
        $multi_size = preg_match_all( '/[\x80-\xff]/', $substr, $multi_chars );

        if ( $multi_size > 0 )
            $size = $size + intval( $multi_size / 3 ) - 1;

        if ( strlen( $str ) > $size ) {
            $str = substr( $str, 0, $size );
            $str = preg_replace( '/(([\x80-\xff]{3})*?)([\x80-\xff]{0,2})$/', '$1', $str );
            $str .= $suffix;
        }

        return $str;
}


/*
-----------------------------------------------------------
    Charset 转换函数
-----------------------------------------------------------
如有iconv就使用iconv
没有的时候使用mb_convert_encoding
如果都没有就无法执行
*/
function convert_charset($from_charset, $to_charset, $str)
{

    if( function_exists('iconv') )
        return iconv($from_charset, $to_charset, $str);
    elseif( function_exists('mb_convert_encoding') )
        return mb_convert_encoding($str, $to_charset, $from_charset);
    else
        die("Not found 'iconv' or 'mbstring' library in server.");
}


// mysql_real_escape_string 的 alias 
function sql_real_escape_string($field)
{
    global $g5;

    return mysql_real_escape_string($field, $g5['connect_db']);
}

function escape_trim($field)
{
    $str = call_user_func(G5_ESCAPE_FUNCTION, $field);
    return $str;
}


// 检查$_POST格式中checkbox checked属性中包含 checked参数 
function is_checked($field)
{
    return !empty($_POST[$field]);
}


function abs_ip2long($ip='')
{
    $ip = $ip ? $ip : $_SERVER['REMOTE_ADDR'];
    return abs(ip2long($ip));
}


function get_selected($field, $value)
{
    return ($field==$value) ? ' selected="selected"' : '';
}


function get_checked($field, $value)
{
    return ($field==$value) ? ' checked="checked"' : '';
}


function is_mobile()
{
    return preg_match('/'.G5_MOBILE_AGENT.'/i', $_SERVER['HTTP_USER_AGENT']);
}


/*******************************************************************************
    获取唯一值

    结果 :

        年月日时分秒00~年月日时分秒99
        年(4) 月(2) 日(2) 时(2) 分(2) 秒(2) 100分之1秒(2)
        共计16位，年度可以使用2位
        例子) 2008062611570199 或者 08062611570199 (2100前的唯一值)

    用途 :
    1. 发表主题是提前获取唯一值用于上传附件字段
    2. 创建订单时使用
    3. 其他需要验证唯一性的场合
*******************************************************************************/
// get_uniqid()替换原有的get_unique_id()
function get_uniqid()
{
    global $g5;

    sql_query(" LOCK TABLE {$g5['uniqid_table']} WRITE ");
    while (1) {
        // 年月日时分秒添加100/1秒的两位数，当100/1秒位数不足时以0填充
        $key = date('ymdHis', time()) . str_pad((int)(microtime()*100), 2, "0", STR_PAD_LEFT);

        $result = sql_query(" insert into {$g5['uniqid_table']} set uq_id = '$key', uq_ip = '{$_SERVER['REMOTE_ADDR']}' ", false);
        if ($result) break; // 如果正常则退出

        // 不能insert时暂停一段时间后重新设置唯一值
        usleep(10000); // 100/1秒
    }
    sql_query(" UNLOCK TABLES ");

    return $key;
}


// CHARSET 变更 : gb2312 -> utf-8
function iconv_utf8($str)
{
    return iconv('gb2312', 'utf-8', $str);
}


// CHARSET 变更 : utf-8 -> gb2312
function iconv_euckr($str)
{
    return iconv('utf-8', 'gb2312', $str);
}


// 检查手机及pc
function check_device($device)
{
    global $is_admin;

    if ($is_admin) return;

    if ($device=='pc' && G5_IS_MOBILE) {
        alert('请使用PC浏览器访问此版块', G5_URL);
    } else if ($device=='mobile' && !G5_IS_MOBILE) {
        alert('此栏目为触屏版专用栏目，请使用移动设备访问', G5_URL);
    }
}


// 删除论坛最新文章缓存数据
function delete_cache_latest($bo_table)
{
    $files = glob(G5_DATA_PATH.'/cache/latest-'.$bo_table.'-*');
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}

// 删除论坛缩略图
function delete_board_thumbnail($bo_table, $file)
{
    if(!$bo_table || !$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob(G5_DATA_PATH.'/file/'.$bo_table.'/thumb-'.$fn.'*');
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}

// 获取编辑器的图片
function get_editor_image($contents, $view=true)
{
    if(!$contents)
        return false;

    // $contents img 标签显示
    if ($view)
        $pattern = "/<img([^>]*)>/iS";
    else
        $pattern = "/<img[^>]*src=[\'\"]?([^>\'\"]+[^>\'\"]+)[\'\"]?[^>]*>/i";
    preg_match_all($pattern, $contents, $matchs);

    return $matchs;
}

// 删除编辑器的缩略图
function delete_editor_thumbnail($contents)
{
    if(!$contents)
        return;

    // $contents img 标签显示
    $matchs = get_editor_image($contents);

    if(!$matchs)
        return;

    for($i=0; $i<count($matchs[1]); $i++) {
        // 获取图片path
        $imgurl = parse_url($matchs[1][$i]);
        $srcfile = $_SERVER['DOCUMENT_ROOT'].$imgurl['path'];

        $filename = preg_replace("/\.[^\.]+$/i", "", basename($srcfile));
        $filepath = dirname($srcfile);
        $files = glob($filepath.'/thumb-'.$filename.'*');
        if (is_array($files)) {
            foreach($files as $filename)
                unlink($filename);
        }
    }
}

// 删除在线提问附件缩略图
function delete_qa_thumbnail($file)
{
    if(!$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob(G5_DATA_PATH.'/qa/thumb-'.$fn.'*');
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}

// 获取皮肤style sheet文件
function get_skin_stylesheet($skin_path, $dir='')
{
    if(!$skin_path)
        return "";

    $str = "";
    $files = array();

    if($dir)
        $skin_path .= '/'.$dir;

    $skin_url = G5_URL.str_replace("\\", "/", str_replace(G5_PATH, "", $skin_path));

    if(is_dir($skin_path)) {
        if($dh = opendir($skin_path)) {
            while(($file = readdir($dh)) !== false) {
                if($file == "." || $file == "..")
                    continue;

                if(is_dir($skin_path.'/'.$file))
                    continue;

                if(preg_match("/\.(css)$/i", $file))
                    $files[] = $file;
            }
            closedir($dh);
        }
    }

    if(!empty($files)) {
        sort($files);

        foreach($files as $file) {
            $str .= '<link rel="stylesheet" href="'.$skin_url.'/'.$file.'?='.date("md").'">'."\n";
        }
    }

    return $str;

    /*
    // 使用glob的代码
    if (!$skin_path) return '';
    $skin_path .= $dir ? '/'.$dir : '';

    $str = '';
    $skin_url = G5_URL.str_replace('\\', '/', str_replace(G5_PATH, '', $skin_path));

    foreach (glob($skin_path.'/*.css') as $filepath) {
        $file = str_replace($skin_path, '', $filepath);
        $str .= '<link rel="stylesheet" href="'.$skin_url.'/'.$file.'?='.date('md').'">'."\n";
    }
    return $str;
    */
}

// 获取皮肤的javascript文件
function get_skin_javascript($skin_path, $dir='')
{
    if(!$skin_path)
        return "";

    $str = "";
    $files = array();

    if($dir)
        $skin_path .= '/'.$dir;

    $skin_url = G5_URL.str_replace("\\", "/", str_replace(G5_PATH, "", $skin_path));

    if(is_dir($skin_path)) {
        if($dh = opendir($skin_path)) {
            while(($file = readdir($dh)) !== false) {
                if($file == "." || $file == "..")
                    continue;

                if(is_dir($skin_path.'/'.$file))
                    continue;

                if(preg_match("/\.(js)$/i", $file))
                    $files[] = $file;
            }
            closedir($dh);
        }
    }

    if(!empty($files)) {
        sort($files);

        foreach($files as $file) {
            $str .= '<script src="'.$skin_url.'/'.$file.'"></script>'."\n";
        }
    }

    return $str;
}

// file_put_contents是php5函数，为了可以在php4使用进行调整
// http://www.phpied.com/file_get_contents-for-php4/
if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
        if (!$f) {
            return false;
        } else {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}


// HTML最后处理
function html_end()
{
    global $html_process;

    return $html_process->run();
}

function add_stylesheet($stylesheet, $order=0)
{
    global $html_process;

    if(trim($stylesheet))
        $html_process->merge_stylesheet($stylesheet, $order);
}

function add_javascript($javascript, $order=0)
{
    global $html_process;

    if(trim($javascript))
        $html_process->merge_javascript($javascript, $order);
}

class html_process {
    protected $css = array();
    protected $js  = array();

    function merge_stylesheet($stylesheet, $order)
    {
        $links = $this->css;
        $is_merge = true;

        foreach($links as $link) {
            if($link[1] == $stylesheet) {
                $is_merge = false;
                break;
            }
        }

        if($is_merge)
            $this->css[] = array($order, $stylesheet);
    }

    function merge_javascript($javascript, $order)
    {
        $scripts = $this->js;
        $is_merge = true;

        foreach($scripts as $script) {
            if($script[1] == $javascript) {
                $is_merge = false;
                break;
            }
        }

        if($is_merge)
            $this->js[] = array($order, $javascript);
    }

    function run()
    {
        global $config, $g5, $member;

        // 在线人数处理
        $tmp_sql = " select count(*) as cnt from {$g5['login_table']} where lo_ip = '{$_SERVER['REMOTE_ADDR']}' ";
        $tmp_row = sql_fetch($tmp_sql);

        if ($tmp_row['cnt']) {
            $tmp_sql = " update {$g5['login_table']} set mb_id = '{$member['mb_id']}', lo_datetime = '".G5_TIME_YMDHIS."', lo_location = '{$g5['lo_location']}', lo_url = '{$g5['lo_url']}' where lo_ip = '{$_SERVER['REMOTE_ADDR']}' ";
            sql_query($tmp_sql, FALSE);
        } else {
            $tmp_sql = " insert into {$g5['login_table']} ( lo_ip, mb_id, lo_datetime, lo_location, lo_url ) values ( '{$_SERVER['REMOTE_ADDR']}', '{$member['mb_id']}', '".G5_TIME_YMDHIS."', '{$g5['lo_location']}',  '{$g5['lo_url']}' ) ";
            sql_query($tmp_sql, FALSE);

            // 超过达到指定时间连接
            sql_query(" delete from {$g5['login_table']} where lo_datetime < '".date("Y-m-d H:i:s", G5_SERVER_TIME - (60 * $config['cf_login_minutes']))."' ");

            // 如有负载（overhead）进行数据优化
            //$row = sql_fetch(" SHOW TABLE STATUS FROM `$mysql_db` LIKE '$g5['login_table']' ");
            //if ($row['Data_free'] > 0) sql_query(" OPTIMIZE TABLE $g5['login_table'] ");
        }

        $buffer = ob_get_contents();
        ob_end_clean();

        $stylesheet = '';
        $links = $this->css;

        if(!empty($links)) {
            foreach ($links as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $style[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $links);

            foreach($links as $link) {
                if(!trim($link[1]))
                    continue;

                $stylesheet .= PHP_EOL.$link[1];
            }
        }

        $javascript = '';
        $scripts = $this->js;
        $php_eol = '';

        unset($order);
        unset($index);

        if(!empty($scripts)) {
            foreach ($scripts as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $script[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $scripts);

            foreach($scripts as $js) {
                if(!trim($js[1]))
                    continue;

                $javascript .= $php_eol.$js[1];
                $php_eol = PHP_EOL;
            }
        }

        /*
        </title>
        <link rel="stylesheet" href="default.css">
        调整样式位置
        */
        $buffer = preg_replace('#(</title>[^<]*<link[^>]+>)#', "$1$stylesheet", $buffer);

        /*
        </head>
        <body>
        调整javascript位置
        */
        $buffer = preg_replace('#(</head>[^<]*<body[^>]*>)#', "$javascript\n$1", $buffer);

        return $buffer;
    }
}

// 获取手机号码后添加-
function hyphen_hp_number($hp)
{
    $hp = preg_replace("/[^0-9]/", "", $hp);
    return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $hp);
}


// 登录后需要移动的地址
function login_url($url='')
{
    if (!$url) $url = G5_URL;
    /*
    $p = parse_url($url);
    echo urlencode($_SERVER['REQUEST_URI']);
    return $url.urldecode(preg_replace("/^".urlencode($p['path'])."/", "", urlencode($_SERVER['REQUEST_URI'])));
    */
    return $url;
}


// 包含$dir转换成http或https地址
function https_url($dir, $https=true)
{
    if ($https) {
        if (G5_HTTPS_DOMAIN) {
            $url = G5_HTTPS_DOMAIN.'/'.$dir;
        } else {
            $url = G5_URL.'/'.$dir;
        }
    } else {
        if (G5_DOMAIN) {
            $url = G5_DOMAIN.'/'.$dir;
        } else {
            $url = G5_URL.'/'.$dir;
        }
    }

    return $url;
}


// 多个论坛公告使用逗号分隔进行更新
function board_notice($bo_notice, $wr_id, $insert=false)
{
    $notice_array = explode(",", trim($bo_notice));

    if($insert && in_array($wr_id, $notice_array))
        return $bo_notice;

    $notice_array = array_merge(array($wr_id), $notice_array);
    $notice_array = array_unique($notice_array);
    foreach ($notice_array as $key=>$value) {
        if (!trim($value))
            unset($notice_array[$key]);
    }
    if (!$insert) {
        foreach ($notice_array as $key=>$value) {
            if ((int)$value == (int)$wr_id)
                unset($notice_array[$key]);
        }
    }
    return implode(",", $notice_array);
}


// 创建短地址goo.gl
function googl_short_url($longUrl)
{
    global $config;

    // Get API key from : http://code.google.com/apis/console/
    // URL Shortener API ON
    $apiKey = $config['cf_googl_shorturl_apikey'];

    $postData = array('longUrl' => $longUrl, 'key' => $apiKey);
    $jsonData = json_encode($postData);

    $curlObj = curl_init();

    curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url');
    curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlObj, CURLOPT_HEADER, 0);
    curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
    curl_setopt($curlObj, CURLOPT_POST, 1);
    curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

    $response = curl_exec($curlObj);

    //change the response json string to object
    $json = json_decode($response);

    curl_close($curlObj);

    return $json->id;
}


// 草稿箱储存数量
function autosave_count($mb_id)
{
    global $g5;

    if ($mb_id) {
        $row = sql_fetch(" select count(*) as cnt from {$g5['autosave_table']} where mb_id = '$mb_id' ");
        return (int)$row['cnt'];
    } else {
        return 0;
    }
}

// 实名认证记录
function insert_cert_history($mb_id, $company, $method)
{
    global $g5;

    $sql = " insert into {$g5['cert_history_table']}
                set mb_id = '$mb_id',
                    cr_company = '$company',
                    cr_method = '$method',
                    cr_ip = '{$_SERVER['REMOTE_ADDR']}',
                    cr_date = '".G5_TIME_YMD."',
                    cr_time = '".G5_TIME_HIS."' ";
    sql_query($sql);
}

// 认证尝试次数
function certify_count_check($mb_id, $type)
{
    global $g5, $config;

    if($config['cf_cert_use'] != 2)
        return;

    if($config['cf_cert_limit'] == 0)
        return;

    $sql = " select count(*) as cnt from {$g5['cert_history_table']} ";

    if($mb_id) {
        $sql .= " where mb_id = '$mb_id' ";
    } else {
        $sql .= " where cr_ip = '{$_SERVER['REMOTE_ADDR']}' ";
    }

    $sql .= " and cr_method = '".$type."' and cr_date = '".G5_TIME_YMD."' ";

    $row = sql_fetch($sql);

    switch($type) {
        case 'hp':
            $cert = '手机';
            break;
        case 'ipin':
            $cert = '实名';
            break;
        default:
            break;
    }

    if((int)$row['cnt'] >= (int)$config['cf_cert_limit'])
        alert_close('今天 '.$cert.' 已使用实名认证 '.$row['cnt'].'次，无法进行更多认证');
}

// 在线提问设置读取
function get_qa_config($fld='*')
{
    global $g5;

    $sql = " select $fld from {$g5['qa_config_table']} ";
    $row = sql_fetch($sql);

    return $row;
}

// get_sock 函数代替
if (!function_exists("get_sock")) {
    function get_sock($url)
    {
        // 分离host 与 uri 
        //if (ereg("http://([a-zA-Z0-9_\-\.]+)([^<]*)", $url, $res))
        if (preg_match("/http:\/\/([a-zA-Z0-9_\-\.]+)([^<]*)/", $url, $res))
        {
            $host = $res[1];
            $get  = $res[2];
        }

        // 尝试连接80端口
        $fp = fsockopen ($host, 80, $errno, $errstr, 30);
        if (!$fp)
        {
            die("$errstr ($errno)\n");
        }
        else
        {
            fputs($fp, "GET $get HTTP/1.0\r\n");
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "\r\n");

            // header 和 content 进行分离
            while (trim($buffer = fgets($fp,1024)) != "")
            {
                $header .= $buffer;
            }
            while (!feof($fp))
            {
                $buffer .= fgets($fp,1024);
            }
        }
        fclose($fp);

        // 仅return content
        return $buffer;
    }
}

// 认证、结算模块
function module_exec_check($exe, $type)
{
    $error = '';
    $is_linux = false;
    if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
        $is_linux = true;

    // 检测模块文件
    if(!is_file($exe)) {
        $error = $exe.' 找不到文件';
    } else {
        // 执行权限检测
        if(!is_executable($exe)) {
            if($is_linux)
                $error = $exe.'\n没有此文件执行权限\n\nchmod 755 '.basename($exe).' 请更改文件权限';
            else
                $error = $exe.'\n没有此文件执行权限\n\n'.basename($exe).' 请赋予文件执行权限';
        } else {
            // 二进制检测
            if($is_linux) {
                $search = false;
                $isbinary = true;
                $executable = true;

                switch($type) {
                    case 'ct_cli':
                        exec($exe.' -h 2>&1', $out, $return_var);

                        if($return_var == 139) {
                            $isbinary = false;
                            break;
                        }

                        for($i=0; $i<count($out); $i++) {
                            if(strpos($out[$i], 'KCP ENC') !== false) {
                                $search = true;
                                break;
                            }
                        }
                        break;
                    case 'okname':
                        exec($exe.' D 2>&1', $out, $return_var);

                        if($return_var == 139) {
                            $isbinary = false;
                            break;
                        }

                        for($i=0; $i<count($out); $i++) {
                            if(strpos(strtolower($out[$i]), 'ret code') !== false) {
                                $search = true;
                                break;
                            }
                        }
                        break;
                }

                if(!$isbinary || !$search) {
                    $error = $exe.'\n请使用二进制格式重新上传';
                }
            }
        }
    }

    if($error) {
        $error = '<script>alert("'.$error.'");</script>';
    }

    return $error;
}

// 地址显示
function print_address($addr1, $addr2, $addr3, $addr4)
{
    $address = trim($addr1);
    $addr2 = trim($addr2);
    $addr3 = trim($addr3);

    if($addr4 == 'N') {
        if($addr2)
            $address .= ' '.$addr2;
    } else {
        if($addr2)
            $address .= ', '.$addr2;
    }

    if($addr3)
        $address .= ' '.$addr3;

    return $address;
}

// input vars 检查
function check_input_vars()
{
    $max_input_vars = ini_get('max_input_vars');

    if($max_input_vars) {
        $post_vars = count($_POST, COUNT_RECURSIVE);
        $get_vars = count($_GET, COUNT_RECURSIVE);
        $cookie_vars = count($_COOKIE, COUNT_RECURSIVE);

        $input_vars = $post_vars + $get_vars + $cookie_vars;

        if($input_vars > $max_input_vars) {
            alert('传递的文本数据超过max_input_vars设置限定\\n部分传送数据尚未被数据库接收\\n\\n需要解决此问题需要修改php.ini的max_input_vars设定');
        }
    }
}

// HTML特殊文字变换htmlspecialchars
function htmlspecialchars2($str)
{
    $trans = array("\"" => "&#034;", "'" => "&#039;", "<"=>"&#060;", ">"=>"&#062;");
    $str = strtr($str, $trans);
    return $str;
}

// data格式变换
function conv_date_format($format, $date, $add='')
{
    if($add)
        $timestamp = strtotime($add, strtotime($date));
    else
        $timestamp = strtotime($date);

    return date($format, $timestamp);
}

// 除去关键词特殊文字
function get_search_string($stx)
{
    $stx_pattern = array();
    $stx_pattern[] = '#\.*/+#';
    $stx_pattern[] = '#\\\*#';
    $stx_pattern[] = '#\.{2,}#';
    $stx_pattern[] = '#[/\'\"%=*\#\(\)\|\+\&\!\$~\{\}\[\]`;:\?\^\,]+#';

    $stx_replace = array();
    $stx_replace[] = '';
    $stx_replace[] = '';
    $stx_replace[] = '.';
    $stx_replace[] = '';

    $stx = preg_replace($stx_pattern, $stx_replace, $stx);

    return $stx;
}

// 删除xss关联代码
function clean_xss_tags($str)
{
    $str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

    return $str;
}

// unescape nl 获取
function conv_unescape_nl($str)
{
    $search = array('\\r', '\r', '\\n', '\n');
    $replace = array('', '', "\n", "\n");

    return str_replace($search, $replace, $str);
}

// 删除会员
function member_delete($mb_id)
{
    global $config;
    global $g5;

    $sql = " select mb_name, mb_nick, mb_ip, mb_recommend, mb_memo, mb_level from {$g5['member_table']} where mb_id= '".$mb_id."' ";
    $mb = sql_fetch($sql);
    if ($mb['mb_recommend']) {
        $row = sql_fetch(" select count(*) as cnt from {$g5['member_table']} where mb_id = '".addslashes($mb['mb_recommend'])."' ");
        if ($row['cnt'])
            insert_point($mb['mb_recommend'], $config['cf_recommend_point'] * (-1), $mb_id.'删除账号导致的推荐积分回收', "@member", $mb['mb_recommend'], $mb_id.' 推荐积分回收');
    }

    // 删除会员资料仅删除会员所有数据，对ID进行删除标记以防止短时间内被其他人注册产生混淆 : 061025
    $sql = " update {$g5['member_table']} set mb_password = '', mb_level = 1, mb_email = '', mb_homepage = '', mb_tel = '', mb_hp = '', mb_zip1 = '', mb_zip2 = '', mb_addr1 = '', mb_addr2 = '', mb_birth = '', mb_sex = '', mb_signature = '', mb_memo = '".date('Ymd', G5_SERVER_TIME)." 已删\n{$mb['mb_memo']}', mb_leave_date = '".date('Ymd', G5_SERVER_TIME)."' where mb_id = '{$mb_id}' ";
    sql_query($sql);

    // 删除积分数据
    sql_query(" delete from {$g5['point_table']} where mb_id = '$mb_id' ");

    // 删除群组访问权限
    sql_query(" delete from {$g5['group_member_table']} where mb_id = '$mb_id' ");

    // 删除站内信
    sql_query(" delete from {$g5['memo_table']} where me_recv_mb_id = '$mb_id' or me_send_mb_id = '$mb_id' ");

    // 删除收藏
    sql_query(" delete from {$g5['scrap_table']} where mb_id = '$mb_id' ");

    // 删除管理权限
    sql_query(" delete from {$g5['auth_table']} where mb_id = '$mb_id' ");

    // 如果是群组管理员则将群组管理员设置为空
    sql_query(" update {$g5['group_table']} set gr_admin = '' where gr_admin = '$mb_id' ");

    // 如果是论坛管理员则将管理员设置为空
    sql_query(" update {$g5['board_table']} set bo_admin = '' where bo_admin = '$mb_id' ");

    // 删除图标
    @unlink(G5_DATA_PATH.'/member/'.substr($mb_id,0,2).'/'.$mb_id.'.gif');
}

// 显示邮件地址
function get_email_address($email)
{
    preg_match("/[0-9a-z._-]+@[a-z0-9._-]{4,}/i", $email, $matches);

    return $matches[0];
}

// 除去文件名中的特殊字符
function get_safe_filename($name)
{
    $pattern = '/["\'<>=#&!%\\\\(\)\*\+\?]/';
    $name = preg_replace($pattern, '', $name);

    return $name;
}

// icode使用信息设置
function get_icode_userinfo($id, $pass)
{
    $res = get_sock('http://www.icodekorea.com/res/userinfo.php?userid='.$id.'&userpw='.$pass);
    $res = explode(';', $res);
    $userinfo = array(
        'code'      => $res[0], // 结果代码
        'coin'      => $res[1], // 余额显示（充值方式）
        'gpay'      => $res[2], // 显示每条扣除金额（充值方式）
        'payment'   => $res[3]  // 结算方式显示 A充值 C包月
    );

    return $userinfo;
}

// 热门关键词输入
function insert_popular($field, $str)
{
    global $g5;

    if(!in_array('mb_id', $field)) {
        $sql = " insert into {$g5['popular_table']} set pp_word = '{$str}', pp_date = '".G5_TIME_YMD."', pp_ip = '{$_SERVER['REMOTE_ADDR']}' ";
        sql_query($sql, FALSE);
    }
}
?>