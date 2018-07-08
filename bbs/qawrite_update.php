<?php
include_once('./_common.php');

/*==========================
$w == a : 回复
$w == r : 追加提问
$w == u :编辑
==========================*/

if($is_guest)
    alert('请您登录后使用', './login.php?url='.urlencode(G5_BBS_URL.'/qalist.php'));

$msg = array();

// 在线咨询设置参数
$qaconfig = get_qa_config();

// e-mail 检查
if(isset($_POST['qa_email']) && $qa_email) {
    $qa_email = get_email_address(trim($_POST['qa_email']));

    if($qaconfig['qa_req_email'] && !$qa_email)
        $msg[] = '请输入邮件地址';

    if (!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $qa_email))
        $msg[] = '您输入的邮件地址格式错误';
}

$qa_subject = '';
if (isset($_POST['qa_subject'])) {
    $qa_subject = substr(trim($_POST['qa_subject']),0,255);
    $qa_subject = preg_replace("#[\\\]+$#", "", $qa_subject);
}
if ($qa_subject == '') {
    $msg[] = '请输入<strong>标题</strong>';
}

$qa_content = '';
if (isset($_POST['qa_content'])) {
    $qa_content = substr(trim($_POST['qa_content']),0,65536);
    $qa_content = preg_replace("#[\\\]+$#", "", $qa_content);
}
if ($qa_content == '') {
    $msg[] = '请输入<strong>内容</strong>';
}

if (!empty($msg)) {
    $msg = implode('<br>', $msg);
    alert($msg);
}

if($qa_hp)
    $qa_hp = preg_replace('/[^0-9\-]/', '', strip_tags($qa_hp));

// 090710
if (substr_count($qa_content, '&#') > 50) {
    alert('请勿在内容中使用代码或特殊符号');
    exit;
}

$upload_max_filesize = ini_get('upload_max_filesize');

if (empty($_POST)) {
    alert("上传附件或上传文章是发生错，文件大小超过服务器限定值\\npost_max_size=".ini_get('post_max_size')." , upload_max_filesize=".$upload_max_filesize."\\n请于管理员联系");
}

for ($i=1; $i<=5; $i++) {
    $var = "qa_$i";
    $$var = "";
    if (isset($_POST['qa_'.$i]) && $_POST['qa_'.$i]) {
        $$var = trim($_POST['qa_'.$i]);
    }
}

if($w == 'u' || $w == 'a' || $w == 'r') {
    if($w == 'a' && !$is_admin)
        alert('只有管理员及指定的会员才能进行回复');

    $sql = " select * from {$g5['qa_content_table']} where qa_id = '$qa_id' ";
    if(!$is_admin) {
        $sql .= " and mb_id = '{$member['mb_id']}' ";
    }

    $write = sql_fetch($sql);

    if($w == 'u') {
        if(!$write['qa_id'])
            alert('未找到主题内容\\n可能已被删除或您没有权限查看');

        if(!$is_admin) {
            if($write['qa_type'] == 0 && $write['qa_status'] == 1)
                alert('已有回复内容不能进行修改');

            if($write['mb_id'] != $member['mb_id'])
                alert('您没有编辑修改权限\\n\\n请使用正常方式访问', G5_URL);
        }
    }

    if($w == 'a') {
        if(!$write['qa_id'])
            alert('未找到提问内容，不能进行回复');

        if($write['qa_type'] == 1)
            alert('回复内容不能进行再次回复');
    }
}

// 如果没有文件夹就创建一个，同时设置属性
@mkdir(G5_DATA_PATH.'/qa', G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH.'/qa', G5_DIR_PERMISSION);

$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

// 附件上传
$file_upload_msg = '';
$upload = array();
for ($i=1; $i<=count($_FILES['bf_file']['name']); $i++) {
    $upload[$i]['file']     = '';
    $upload[$i]['source']   = '';
    $upload[$i]['del_check'] = false;

    // 如选择删除附件则进行删除
    if (isset($_POST['bf_file_del'][$i]) && $_POST['bf_file_del'][$i]) {
        $upload[$i]['del_check'] = true;
        @unlink(G5_DATA_PATH.'/qa/'.$write['qa_file'.$i]);
        // 缩略图删除
        if(preg_match("/\.({$config['cf_image_extension']})$/i", $write['qa_file'.$i])) {
            delete_qa_thumbnail($write['qa_file'.$i]);
        }
    }

    $tmp_file  = $_FILES['bf_file']['tmp_name'][$i];
    $filesize  = $_FILES['bf_file']['size'][$i];
    $filename  = $_FILES['bf_file']['name'][$i];
    $filename  = get_safe_filename($filename);

    // 上传超过服务器限制的文件时
    if ($filename) {
        if ($_FILES['bf_file']['error'][$i] == 1) {
            $file_upload_msg .= '\"'.$filename.'\" 文件容量超过服务器支持容量('.$upload_max_filesize.')附件上传已被取消\\n';
            continue;
        }
        else if ($_FILES['bf_file']['error'][$i] != 0) {
            $file_upload_msg .= '\"'.$filename.'\" 附件上传出现错误\\n';
            continue;
        }
    }

    if (is_uploaded_file($tmp_file)) {
        // 如果不是管理员超过设定容量时
        if (!$is_admin && $filesize > $qaconfig['qa_upload_size']) {
            $file_upload_msg .= '\"'.$filename.'\" 文件大小('.number_format($filesize).' 字节)超过允许上传大小('.number_format($qaconfig['qa_upload_size']).' 字节)您的上传进程已被取消\\n';
            continue;
        }

        //=================================================================\
        // 090714
        // 防止在图片或flash文件中加入恶意代码
        // 不显示错误信息
        //-----------------------------------------------------------------
        $timg = @getimagesize($tmp_file);
        // image type
        if ( preg_match("/\.({$config['cf_image_extension']})$/i", $filename) ||
             preg_match("/\.({$config['cf_flash_extension']})$/i", $filename) ) {
            if ($timg['2'] < 1 || $timg['2'] > 16)
                continue;
        }
        //=================================================================

        if ($w == 'u') {
            // 如果已有文件就删除
            @unlink(G5_DATA_PATH.'/qa/'.$write['qa_file'.$i]);
            // 删除缩略图文件
            if(preg_match("/\.({$config['cf_image_extension']})$/i", $write['qa_file'.$i])) {
                delete_qa_thumbnail($row['qa_file'.$i]);
            }
        }

        // 文件原始名称
        $upload[$i]['source'] = $filename;
        $upload[$i]['filesize'] = $filesize;

        // 以下类型文件扩展名增加-x，防止在获取路径的情况下在服务器上被运行
        $filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $filename);

        shuffle($chars_array);
        $shuffle = implode('', $chars_array);

        // 附件名称包含空格时在部分电脑上将不能正常显示
        $upload[$i]['file'] = abs(ip2long($_SERVER['REMOTE_ADDR'])).'_'.substr($shuffle,0,8).'_'.str_replace('%', '', urlencode(str_replace(' ', '_', $filename)));

        $dest_file = G5_DATA_PATH.'/qa/'.$upload[$i]['file'];

        // 上传失败是显示错误信息后关闭
        $error_code = move_uploaded_file($tmp_file, $dest_file) or die($_FILES['bf_file']['error'][$i]);

        // 上传完成文件修改属性
        chmod($dest_file, G5_FILE_PERMISSION);
    }
}

if($w == '' || $w == 'a' || $w == 'r') {
    if($w == '' || $w == 'r') {
        $row = sql_fetch(" select MIN(qa_num) as min_qa_num from {$g5['qa_content_table']} ");
        $qa_num = $row['min_qa_num'] - 1;
    }

    if($w == 'a') {
        $qa_num = $write['qa_num'];
        $qa_parent = $write['qa_id'];
        $qa_related = $write['qa_related'];
        $qa_category = $write['qa_category'];
        $qa_type = 1;
        $qa_status = 1;
    }

    $sql = " insert into {$g5['qa_content_table']}
                set qa_num          = '$qa_num',
                    mb_id           = '{$member['mb_id']}',
                    qa_name         = '{$member['mb_nick']}',
                    qa_email        = '$qa_email',
                    qa_hp           = '$qa_hp',
                    qa_type         = '$qa_type',
                    qa_parent       = '$qa_parent',
                    qa_related      = '$qa_related',
                    qa_category     = '$qa_category',
                    qa_email_recv   = '$qa_email_recv',
                    qa_sms_recv     = '$qa_sms_recv',
                    qa_html         = '$qa_html',
                    qa_subject      = '$qa_subject',
                    qa_content      = '$qa_content',
                    qa_status       = '$qa_status',
                    qa_file1        = '{$upload[1]['file']}',
                    qa_source1      = '{$upload[1]['source']}',
                    qa_file2        = '{$upload[2]['file']}',
                    qa_source2      = '{$upload[2]['source']}',
                    qa_ip           = '{$_SERVER['REMOTE_ADDR']}',
                    qa_datetime     = '".G5_TIME_YMDHIS."',
                    qa_1            = '$qa_1',
                    qa_2            = '$qa_2',
                    qa_3            = '$qa_3',
                    qa_4            = '$qa_4',
                    qa_5            = '$qa_5' ";
    sql_query($sql);

    if($w == '' || $w == 'r') {
        $qa_id = mysql_insert_id();

        if($w == 'r' && $write['qa_related']) {
            $qa_related = $write['qa_related'];
        } else {
            $qa_related = $qa_id;
        }

        $sql = " update {$g5['qa_content_table']}
                    set qa_parent   = '$qa_id',
                        qa_related  = '$qa_related'
                    where qa_id = '$qa_id' ";
        sql_query($sql);
    }

    if($w == 'a') {
        $sql = " update {$g5['qa_content_table']}
                    set qa_status = '1'
                    where qa_id = '{$write['qa_parent']}' ";
        sql_query($sql);
    }
} else if($w == 'u') {
    if(!$upload[1]['file'] && !$upload[1]['del_check']) {
        $upload[1]['file'] = $write['qa_file1'];
        $upload[1]['source'] = $write['qa_source1'];
    }

    if(!$upload[2]['file'] && !$upload[2]['del_check']) {
        $upload[2]['file'] = $write['qa_file2'];
        $upload[2]['source'] = $write['qa_source2'];
    }

    $sql = " update {$g5['qa_content_table']}
                set qa_email    = '$qa_email',
                    qa_hp       = '$qa_hp',
                    qa_category = '$qa_category',
                    qa_html     = '$qa_html',
                    qa_subject  = '$qa_subject',
                    qa_content  = '$qa_content',
                    qa_file1    = '{$upload[1]['file']}',
                    qa_source1  = '{$upload[1]['source']}',
                    qa_file2    = '{$upload[2]['file']}',
                    qa_source2  = '{$upload[2]['source']}',
                    qa_1        = '$qa_1',
                    qa_2        = '$qa_2',
                    qa_3        = '$qa_3',
                    qa_4        = '$qa_4',
                    qa_5        = '$qa_5' ";
    if($qa_sms_recv)
        $sql .= ", qa_sms_recv = '$qa_sms_recv' ";
    $sql .= " where qa_id = '$qa_id' ";
    sql_query($sql);
}

// 手机短信通知
if($config['cf_sms_use'] == 'icode' && $qaconfig['qa_use_sms']) {
    include_once(G5_LIB_PATH.'/icode.sms.lib.php');

    // 通知提问者
    if($w == 'a' && $write['qa_sms_recv'] && trim($write['qa_hp'])) {
        $sms_content = $config['cf_title'].' '.$qaconfig['qa_title'].'提交的提问已被回复';
        $send_number = preg_replace('/[^0-9]/', '', $qaconfig['qa_send_number']);
        $recv_number = preg_replace('/[^0-9]/', '', $write['qa_hp']);

        if($recv_number) {
            $SMS = new SMS; // 链接手机短息模块
            $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
            $SMS->Add($recv_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "gb2312", stripslashes($sms_content)), "");
            $SMS->Send();
        }
    }

    // 新提问通知管理员
    if(($w == '' || $w == 'r') && trim($qaconfig['qa_admin_hp'])) {
        $sms_content = $config['cf_title'].' '.$qaconfig['qa_title'].'有新的提问内容';
        $send_number = preg_replace('/[^0-9]/', '', $qa_hp);
        $recv_number = preg_replace('/[^0-9]/', '', $qaconfig['qa_admin_hp']);

        if($recv_number) {
            $SMS = new SMS; // 链接手机短息模块
            $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
            $SMS->Add($recv_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "gb2312", stripslashes($sms_content)), "");
            $SMS->Send();
        }
    }
}

// 回复内容邮件通知
if($w == 'a' && $write['qa_email_recv'] && trim($write['qa_email'])) {
    include_once(G5_LIB_PATH.'/mailer.lib.php');

    $subject = $config['cf_title'].' '.$qaconfig['qa_title'].' 回复邮件通知';
    $content = nl2br(conv_unescape_nl($qa_content));

    mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $write['qa_email'], $subject, $content, 1);
}

// 发送通知内容至邮件
if(($w == '' || $w == 'r') && trim($qaconfig['qa_admin_email'])) {
    include_once(G5_LIB_PATH.'/mailer.lib.php');

    $subject = $config['cf_title'].' '.$qaconfig['qa_title'].' 提问回复提醒';
    $content = nl2br(conv_unescape_nl($qa_content));

    mailer($config['cf_admin_email_name'], $qa_email, $qaconfig['qa_admin_email'], $subject, $content, 1);
}

if($w == 'a')
    $result_url = G5_BBS_URL.'/qaview.php?qa_id='.$qa_id.$qstr;
else if($w == 'u' && $write['qa_type'])
    $result_url = G5_BBS_URL.'/qaview.php?qa_id='.$write['qa_parent'].$qstr;
else
    $result_url = G5_BBS_URL.'/qalist.php'.preg_replace('/^&amp;/', '?', $qstr);

if ($file_upload_msg)
    alert($file_upload_msg, $result_url);
else
    goto_url($result_url);
?>