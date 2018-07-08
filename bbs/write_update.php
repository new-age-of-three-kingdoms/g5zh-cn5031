<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/naver_syndi.lib.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

$g5['title'] = '发布主题';

$msg = array();

$wr_subject = '';
if (isset($_POST['wr_subject'])) {
    $wr_subject = substr(trim($_POST['wr_subject']),0,255);
    $wr_subject = preg_replace("#[\\\]+$#", "", $wr_subject);
}
if ($wr_subject == '') {
    $msg[] = '请输入<strong>标题</strong>';
}

$wr_content = '';
if (isset($_POST['wr_content'])) {
    $wr_content = substr(trim($_POST['wr_content']),0,65536);
    $wr_content = preg_replace("#[\\\]+$#", "", $wr_content);
}
if ($wr_content == '') {
    $msg[] = '请输入<strong>内容</strong>';
}

$wr_link1 = '';
if (isset($_POST['wr_link1'])) {
    $wr_link1 = substr($_POST['wr_link1'],0,1000);
    $wr_link1 = trim(strip_tags($wr_link1));
    $wr_link1 = preg_replace("#[\\\]+$#", "", $wr_link1);
}

$wr_link2 = '';
if (isset($_POST['wr_link2'])) {
    $wr_link2 = substr($_POST['wr_link2'],0,1000);
    $wr_link2 = trim(strip_tags($wr_link2));
    $wr_link2 = preg_replace("#[\\\]+$#", "", $wr_link2);
}

$msg = implode('<br>', $msg);
if ($msg) {
    alert($msg);
}

// 090710
if (substr_count($wr_content, '&#') > 50) {
    alert('请勿在内容中使用代码或特殊符号');
    exit;
}

$upload_max_filesize = ini_get('upload_max_filesize');

if (empty($_POST)) {
    alert("上传附件或上传文章是发生错，文件大小超过服务器限定值\\npost_max_size=".ini_get('post_max_size')." , upload_max_filesize=".$upload_max_filesize."\\n请于管理员联系");
}

$notice_array = explode(",", $board['bo_notice']);

if ($w == 'u' || $w == 'r') {
    $wr = get_write($write_table, $wr_id);
    if (!$wr['wr_id']) {
        alert("未找到您访问的内容\\n您访问的内容可能已经被删除或移动到其他栏目");
    }
}

// 修复外部直接发帖bug
if (!$is_admin && !$board['bo_use_secret'] && $secret) {
	alert('此版块禁止使用加密主题功能');
}

// 修复加密版块发帖bug
if (!$is_admin && $board['bo_use_secret'] == 2) {
    $secret = 'secret';
}

$html = '';
if (isset($_POST['html']) && $_POST['html']) {
    $html = $_POST['html'];
}

$mail = '';
if (isset($_POST['mail']) && $_POST['mail']) {
    $mail = $_POST['mail'];
}

$notice = '';
if (isset($_POST['notice']) && $_POST['notice']) {
    $notice = $_POST['notice'];
}

for ($i=1; $i<=10; $i++) {
    $var = "wr_$i";
    $$var = "";
    if (isset($_POST['wr_'.$i]) && settype($_POST['wr_'.$i], 'string')) {
        $$var = trim($_POST['wr_'.$i]);
    }
}

@include_once($board_skin_path.'/write_update.head.skin.php');

if ($w == '' || $w == 'u') {

    // 金善勇 1.00：发布主题与编辑主题权限需要另行处理
    if($w =='u' && $member['mb_id'] && $wr['mb_id'] == $member['mb_id']) {
        ;
    } else if ($member['mb_level'] < $board['bo_write_level']) {
        alert('您没有发表主题权限');
    }

	// 公告仅允许管理员发布
	if (!$is_admin && $notice) {
		alert('您没有发布公告权限');
    }

} else if ($w == 'r') {

    if (in_array((int)$wr_id, $notice_array)) {
        alert('公告内容禁止回复或评论');
    }

    if ($member['mb_level'] < $board['bo_reply_level']) {
        alert('您没有回复或评论权限');
    }

    // 主题排列参考
    $reply_array = &$wr;

    // 最大回复数量根据数据表中的wr_reply设置数量进行限制
    if (strlen($reply_array['wr_reply']) == 10) {
        alert("回帖数量已达到回帖限制\\n仅允许回复10层");
    }

    $reply_len = strlen($reply_array['wr_reply']) + 1;
    if ($board['bo_reply_order']) {
        $begin_reply_char = 'A';
        $end_reply_char = 'Z';
        $reply_number = +1;
        $sql = " select MAX(SUBSTRING(wr_reply, $reply_len, 1)) as reply from {$write_table} where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
    } else {
        $begin_reply_char = 'Z';
        $end_reply_char = 'A';
        $reply_number = -1;
        $sql = " select MIN(SUBSTRING(wr_reply, {$reply_len}, 1)) as reply from {$write_table} where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
    }
    if ($reply_array['wr_reply']) $sql .= " and wr_reply like '{$reply_array['wr_reply']}%' ";
    $row = sql_fetch($sql);

    if (!$row['reply']) {
        $reply_char = $begin_reply_char;
    } else if ($row['reply'] == $end_reply_char) { // A~Z 26个字母
        alert("回帖数量已达到回帖限制\\n仅允许回复26层");
    } else {
        $reply_char = chr(ord($row['reply']) + $reply_number);
    }

    $reply = $reply_array['wr_reply'] . $reply_char;

} else {
    alert('w值传递错误！');
}

if ($is_guest && !chk_captcha()) {
    alert('您输入的验证码错误，请重新输入');
}

if ($w == '' || $w == 'r') {
    if (isset($_SESSION['ss_datetime'])) {
        if ($_SESSION['ss_datetime'] >= (G5_SERVER_TIME - $config['cf_delay_sec']) && !$is_admin)
            alert('您发帖速度太快了，请休息一下~');
    }

    set_session("ss_datetime", G5_SERVER_TIME);
}

if (!isset($_POST['wr_subject']) || !trim($_POST['wr_subject']))
    alert('请输入标题');

if ($w == '' || $w == 'r') {

    if ($member['mb_id']) {
        $mb_id = $member['mb_id'];
        $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
        $wr_password = $member['mb_password'];
        $wr_email = addslashes($member['mb_email']);
        $wr_homepage = addslashes(clean_xss_tags($member['mb_homepage']));
    } else {
        $mb_id = '';
        // 游客可能会出现遗漏名称的情况
        $wr_name = clean_xss_tags(trim($_POST['wr_name']));
        if (!$wr_name)
            alert('请输入名称');
        $wr_password = sql_password($wr_password);
        $wr_email = get_email_address(trim($_POST['wr_email']));
        $wr_homepage = clean_xss_tags($wr_homepage);
    }

    if ($w == 'r') {
        // 回复的主题为加密内容时密码设置为与主题密码一致
        if ($secret)
            $wr_password = $wr['wr_password'];

        $wr_id = $wr_id . $reply;
        $wr_num = $write['wr_num'];
        $wr_reply = $reply;
    } else {
        $wr_num = get_next_num($write_table);
        $wr_reply = '';
    }

    $sql = " insert into $write_table
                set wr_num = '$wr_num',
                     wr_reply = '$wr_reply',
                     wr_comment = 0,
                     ca_name = '$ca_name',
                     wr_option = '$html,$secret,$mail',
                     wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_link1 = '$wr_link1',
                     wr_link2 = '$wr_link2',
                     wr_link1_hit = 0,
                     wr_link2_hit = 0,
                     wr_hit = 0,
                     wr_good = 0,
                     wr_nogood = 0,
                     mb_id = '{$member['mb_id']}',
                     wr_password = '$wr_password',
                     wr_name = '$wr_name',
                     wr_email = '$wr_email',
                     wr_homepage = '$wr_homepage',
                     wr_datetime = '".G5_TIME_YMDHIS."',
                     wr_last = '".G5_TIME_YMDHIS."',
                     wr_ip = '{$_SERVER['REMOTE_ADDR']}',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10' ";
    sql_query($sql);

    $wr_id = mysql_insert_id();

    // 父id update
    sql_query(" update $write_table set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

    // insert新内容
    sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '{$bo_table}', '{$wr_id}', '{$wr_id}', '".G5_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

    // 主题数量加1
    sql_query("update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = '{$bo_table}'");

    // 编辑 增加积分
    if ($w == '') {
        if ($notice) {
            $bo_notice = $wr_id.($board['bo_notice'] ? ",".$board['bo_notice'] : '');
            sql_query(" update {$g5['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");
        }

        insert_point($member['mb_id'], $board['bo_write_point'], "{$board['bo_subject']} {$wr_id} 发表主题", $bo_table, $wr_id, '编辑');
    } else {
        // 回复发放积分与评论积分一致
        // 防止因回帖积分高于评论积分而为了赚取积分使用回帖不使用评论
        insert_point($member['mb_id'], $board['bo_comment_point'], "{$board['bo_subject']} {$wr_id} 回帖", $bo_table, $wr_id, '编辑');
    }
}  else if ($w == 'u') {
    if (get_session('ss_bo_table') != $_POST['bo_table'] || get_session('ss_wr_id') != $_POST['wr_id']) {
        alert('请使用正常访问操作', G5_BBS_URL.'/board.php?bo_table='.$bo_table);
    }

    $return_url = './board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id;

    if ($is_admin == 'super') // 管理员通过
        ;
    else if ($is_admin == 'group') { // 群组管理员
        $mb = get_member($write['mb_id']);
        if ($member['mb_id'] != $group['gr_admin']) // 检查是否拥有群组管理权限
            alert('当前群组并不属于您管理，您没有权限进行编辑', $return_url);
        else if ($member['mb_level'] < $mb['mb_level']) // 检查会员等级权限，等级相同或大于时通过
            alert('你没有权限进行修改因为作者等级高于您的等级', $return_url);
    } else if ($is_admin == 'board') { // 如果是论坛版块管理员
        $mb = get_member($write['mb_id']);
        if ($member['mb_id'] != $board['bo_admin']) // 检查是否拥有版块管理权限
            alert('当前版块并不属于您管理，您没有权限进行编辑', $return_url);
        else if ($member['mb_level'] < $mb['mb_level']) // 检查会员等级权限，等级相同或大于时通过
            alert('你没有权限进行修改因为作者等级高于您的等级', $return_url);
    } else if ($member['mb_id']) {
        if ($member['mb_id'] != $write['mb_id'])
            alert('您没有编辑权限', $return_url);
    } else {
        if ($write['mb_id'])
            alert('请您登录后使用', './login.php?url='.urlencode($return_url));
    }

    if ($member['mb_id']) {
        // 如果是作者本人
        if ($member['mb_id'] == $wr['mb_id']) {
            $mb_id = $member['mb_id'];
            $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
            $wr_email = addslashes($member['mb_email']);
            $wr_homepage = addslashes(clean_xss_tags($member['mb_homepage']));
        } else {
            $mb_id = $wr['mb_id'];
            if(isset($_POST['wr_name']) && $_POST['wr_name'])
                $wr_name = clean_xss_tags(trim($_POST['wr_name']));
            else
                $wr_name = addslashes(clean_xss_tags($wr['wr_name']));
            if(isset($_POST['wr_email']) && $_POST['wr_email'])
                $wr_email = get_email_address(trim($_POST['wr_email']));
            else
                $wr_email = addslashes($wr['wr_email']);
            if(isset($_POST['wr_homepage']) && $_POST['wr_homepage'])
                $wr_homepage = addslashes(clean_xss_tags($_POST['wr_homepage']));
            else
                $wr_homepage = addslashes(clean_xss_tags($wr['wr_homepage']));
        }
    } else {
        $mb_id = "";
        // 游客可能会出现遗漏名称的情况
        if (!trim($wr_name)) alert("请输入名称");
        $wr_name = clean_xss_tags(trim($_POST['wr_name']));
        $wr_email = get_email_address(trim($_POST['wr_email']));
    }

    $sql_password = $wr_password ? " , wr_password = '".sql_password($wr_password)."' " : "";

    $sql_ip = '';
    if (!$is_admin)
        $sql_ip = " , wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";

    $sql = " update {$write_table}
                set ca_name = '{$ca_name}',
                     wr_option = '{$html},{$secret},{$mail}',
                     wr_subject = '{$wr_subject}',
                     wr_content = '{$wr_content}',
                     wr_link1 = '{$wr_link1}',
                     wr_link2 = '{$wr_link2}',
                     mb_id = '{$mb_id}',
                     wr_name = '{$wr_name}',
                     wr_email = '{$wr_email}',
                     wr_homepage = '{$wr_homepage}',
                     wr_1 = '{$wr_1}',
                     wr_2 = '{$wr_2}',
                     wr_3 = '{$wr_3}',
                     wr_4 = '{$wr_4}',
                     wr_5 = '{$wr_5}',
                     wr_6 = '{$wr_6}',
                     wr_7 = '{$wr_7}',
                     wr_8 = '{$wr_8}',
                     wr_9 = '{$wr_9}',
                     wr_10= '{$wr_10}'
                     {$sql_ip}
                     {$sql_password}
              where wr_id = '{$wr['wr_id']}' ";
    sql_query($sql);

    // 如果修改分类名称时所属评论也进行修改
    // 防止无法正常进行搜索
    $sql = " update {$write_table} set ca_name = '{$ca_name}' where wr_parent = '{$wr['wr_id']}' ";
    sql_query($sql);

    /*
    if ($notice) {
        //if (!preg_match("/[^0-9]{0,1}{$wr_id}[\r]{0,1}/",$board['bo_notice']))
        if (!in_array((int)$wr_id, $notice_array)) {
            $bo_notice = $wr_id . ',' . $board['bo_notice'];
            sql_query(" update {$g5['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");
        }
    } else {
        $bo_notice = '';
        for ($i=0; $i<count($notice_array); $i++)
            if ((int)$wr_id != (int)$notice_array[$i])
                $bo_notice .= $notice_array[$i] . ',';
        $bo_notice = trim($bo_notice);
        //$bo_notice = preg_replace("/^".$wr_id."[\n]?$/m", "", $board['bo_notice']);
        sql_query(" update {$g5['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");
    }
    */

    $bo_notice = board_notice($board['bo_notice'], $wr_id, $notice);
    sql_query(" update {$g5['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");
}

// 不能使用群组限制并且允许游客浏览
if (!$group['gr_use_access'] && $board['bo_read_level'] < 2 && !$secret) {
    naver_syndi_ping($bo_table, $wr_id);
}

// 如果没有文件夹就创建一个，同时设置属性
@mkdir(G5_DATA_PATH.'/file/'.$bo_table, G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH.'/file/'.$bo_table, G5_DIR_PERMISSION);

$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

// 附件上传
$file_upload_msg = '';
$upload = array();
for ($i=0; $i<count($_FILES['bf_file']['name']); $i++) {
    $upload[$i]['file']     = '';
    $upload[$i]['source']   = '';
    $upload[$i]['filesize'] = 0;
    $upload[$i]['image']    = array();
    $upload[$i]['image'][0] = '';
    $upload[$i]['image'][1] = '';
    $upload[$i]['image'][2] = '';

    // 如选择删除附件则进行删除
    if (isset($_POST['bf_file_del'][$i]) && $_POST['bf_file_del'][$i]) {
        $upload[$i]['del_check'] = true;

        $row = sql_fetch(" select bf_file from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and bf_no = '{$i}' ");
        @unlink(G5_DATA_PATH.'/file/'.$bo_table.'/'.$row['bf_file']);
        // 缩略图删除
        if(preg_match("/\.({$config['cf_image_extension']})$/i", $row['bf_file'])) {
            delete_board_thumbnail($bo_table, $row['bf_file']);
        }
    }
    else
        $upload[$i]['del_check'] = false;

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
        if (!$is_admin && $filesize > $board['bo_upload_size']) {
            $file_upload_msg .= '\"'.$filename.'\" 文件大小('.number_format($filesize).' 字节)超过允许上传大小('.number_format($board['bo_upload_size']).' 字节)您的上传进程已被取消\\n';
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

        $upload[$i]['image'] = $timg;

        // 4.00.11 - 修复回帖时包含附件导致原文附件被删除的bug
        if ($w == 'u') {
            // 如果已有文件就删除
            $row = sql_fetch(" select bf_file from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$i' ");
            @unlink(G5_DATA_PATH.'/file/'.$bo_table.'/'.$row['bf_file']);
            // 删除缩略图文件
            if(preg_match("/\.({$config['cf_image_extension']})$/i", $row['bf_file'])) {
                delete_board_thumbnail($bo_table, $row['bf_file']);
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

        $dest_file = G5_DATA_PATH.'/file/'.$bo_table.'/'.$upload[$i]['file'];

        // 上传失败是显示错误信息后关闭
        $error_code = move_uploaded_file($tmp_file, $dest_file) or die($_FILES['bf_file']['error'][$i]);

        // 上传完成文件修改属性
        chmod($dest_file, G5_FILE_PERMISSION);
    }
}

// 最后储存到数据库是因为需要获得$wr_id参数
for ($i=0; $i<count($upload); $i++)
{
    if (!get_magic_quotes_gpc()) {
        $upload[$i]['source'] = addslashes($upload[$i]['source']);
    }

    $row = sql_fetch(" select count(*) as cnt from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and bf_no = '{$i}' ");
    if ($row['cnt'])
    {
        // 如有附件选项时执行
        // 否则仅提交文本内容
        if ($upload[$i]['del_check'] || $upload[$i]['file'])
        {
            $sql = " update {$g5['board_file_table']}
                        set bf_source = '{$upload[$i]['source']}',
                             bf_file = '{$upload[$i]['file']}',
                             bf_content = '{$bf_content[$i]}',
                             bf_filesize = '{$upload[$i]['filesize']}',
                             bf_width = '{$upload[$i]['image']['0']}',
                             bf_height = '{$upload[$i]['image']['1']}',
                             bf_type = '{$upload[$i]['image']['2']}',
                             bf_datetime = '".G5_TIME_YMDHIS."'
                      where bo_table = '{$bo_table}'
                                and wr_id = '{$wr_id}'
                                and bf_no = '{$i}' ";
            sql_query($sql);
        }
        else
        {
            $sql = " update {$g5['board_file_table']}
                        set bf_content = '{$bf_content[$i]}'
                        where bo_table = '{$bo_table}'
                                  and wr_id = '{$wr_id}'
                                  and bf_no = '{$i}' ";
            sql_query($sql);
        }
    }
    else
    {
        $sql = " insert into {$g5['board_file_table']}
                    set bo_table = '{$bo_table}',
                         wr_id = '{$wr_id}',
                         bf_no = '{$i}',
                         bf_source = '{$upload[$i]['source']}',
                         bf_file = '{$upload[$i]['file']}',
                         bf_content = '{$bf_content[$i]}',
                         bf_download = 0,
                         bf_filesize = '{$upload[$i]['filesize']}',
                         bf_width = '{$upload[$i]['image']['0']}',
                         bf_height = '{$upload[$i]['image']['1']}',
                         bf_type = '{$upload[$i]['image']['2']}',
                         bf_datetime = '".G5_TIME_YMDHIS."' ";
        sql_query($sql);
    }
}

// 已上传文件中获取最大编号使用倒叙方式查询
// 如无文件信息则删除数据表内容.
$row = sql_fetch(" select max(bf_no) as max_bf_no from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' ");
for ($i=(int)$row['max_bf_no']; $i>=0; $i--)
{
    $row2 = sql_fetch(" select bf_file from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and bf_no = '{$i}' ");

    // 如有信息则跳过
    if ($row2['bf_file']) break;

    // 否则删除信息
    sql_query(" delete from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and bf_no = '{$i}' ");
}

// 更新附件数量
$row = sql_fetch(" select count(*) as cnt from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' ");
sql_query(" update {$write_table} set wr_file = '{$row['cnt']}' where wr_id = '{$wr_id}' ");

// 删除自动记录的recode代码
sql_query(" delete from {$g5['autosave_table']} where as_uid = '{$uid}' ");
//------------------------------------------------------------------------------

// 如果是加密内容则在session中储存加密主题的密码，以便作者本人无需输入密码即可查看
if ($secret)
    set_session("ss_secret_{$bo_table}_{$wr_num}", TRUE);

// 发送邮件通知，编辑修改内容不通知
if (!($w == 'u' || $w == 'cu') && $config['cf_email_use'] && $board['bo_use_email']) {

    // 获取管理员信息
    $super_admin = get_admin('super');
    $group_admin = get_admin('group');
    $board_admin = get_admin('board');

    $wr_subject = get_text(stripslashes($wr_subject));

    $tmp_html = 0;
    if (strstr($html, 'html1'))
        $tmp_html = 1;
    else if (strstr($html, 'html2'))
        $tmp_html = 2;

    $wr_content = conv_content(conv_unescape_nl($wr_content), $tmp_html);

    $warr = array( ''=>'输入', 'u'=>'修改', 'r'=>'回复', 'c'=>'评论', 'cu'=>'评论编辑' );
    $str = $warr[$w];

    $subject = '['.$config['cf_title'].'] '.$board['bo_subject'].' 论坛版块新发表'.$str.'内容';

    $link_url = G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;'.$qstr;

    include_once(G5_LIB_PATH.'/mailer.lib.php');

    ob_start();
    include_once ('./write_update_mail.php');
    $content = ob_get_contents();
    ob_end_clean();

    $array_email = array();
    // 邮件通知论坛版块管理员
    if ($config['cf_email_wr_board_admin']) $array_email[] = $board_admin['mb_email'];
    // 邮件通知群组管理员
    if ($config['cf_email_wr_group_admin']) $array_email[] = $group_admin['mb_email'];
    // 邮件通知管理员
    if ($config['cf_email_wr_super_admin']) $array_email[] = $super_admin['mb_email'];

    // 邮件通知原文作者
    if ($config['cf_email_wr_write']) {
        if($w == '')
            $wr['wr_email'] = $wr_email;

        $array_email[] = $wr['wr_email'];
    }

    // 选项中如有邮件通知选项且获得邮件地址时
    if (strstr($wr['wr_option'], 'mail') && $wr['wr_email'])
        $array_email[] = $wr['wr_email'];

    // 去掉重复邮件地址
    $unique_email = array_unique($array_email);
    $unique_email = array_values($unique_email);
    for ($i=0; $i<count($unique_email); $i++) {
        mailer($wr_name, $wr_email, $unique_email[$i], $subject, $content, 1);
    }
}

// 执行用户代码
@include_once($board_skin_path.'/write_update.skin.php');
@include_once($board_skin_path.'/write_update.tail.skin.php');

delete_cache_latest($bo_table);

if ($file_upload_msg)
    alert($file_upload_msg, G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;page='.$page.$qstr);
else
    goto_url(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr);
?>
