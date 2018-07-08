<?php
if (!defined('_GNUBOARD_')) exit;

/*
// 081022 : CSRF攻击防范代码，由于效果不佳进行注释
if (!get_session('ss_admin')) {
    set_session('ss_admin', true);
    goto_url('.');
}
*/

// 以SELECT形式获取皮肤目录
function get_skin_select($skin_gubun, $id, $name, $selected='', $event='')
{
    $skins = get_skin_dir($skin_gubun);
    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
    for ($i=0; $i<count($skins); $i++) {
        if ($i == 0) $str .= "<option value=\"\">选择</option>";
        $str .= option_selected($skins[$i], $selected);
    }
    $str .= "</select>";
    return $str;
}

// 以SELECT形式获取触屏版本皮肤目录
function get_mobile_skin_select($skin_gubun, $id, $name, $selected='', $event='')
{
    $skins = get_skin_dir($skin_gubun, G5_MOBILE_PATH.'/'.G5_SKIN_DIR);
    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
    for ($i=0; $i<count($skins); $i++) {
        if ($i == 0) $str .= "<option value=\"\">选择</option>";
        $str .= option_selected($skins[$i], $selected);
    }
    $str .= "</select>";
    return $str;
}


// 获取皮肤路径
function get_skin_dir($skin, $skin_path=G5_SKIN_PATH)
{
    global $g5;

    $result_array = array();

    $dirname = $skin_path.'/'.$skin.'/';
    $handle = opendir($dirname);
    while ($file = readdir($handle)) {
        if($file == '.'||$file == '..') continue;

        if (is_dir($dirname.$file)) $result_array[] = $file;
    }
    closedir($handle);
    sort($result_array);

    return $result_array;
}


// 以SELECT形式获取会员权限
function get_member_level_select($name, $start_id=0, $end_id=10, $selected="", $event="")
{
    global $g5;

    $str = "\n<select id=\"{$name}\" name=\"{$name}\"";
    if ($event) $str .= " $event";
    $str .= ">\n";
    for ($i=$start_id; $i<=$end_id; $i++) {
        $str .= '<option value="'.$i.'"';
        if ($i == $selected)
            $str .= ' selected="selected"';
        $str .= ">{$i}</option>\n";
    }
    $str .= "</select>\n";
    return $str;
}


// 以SELECT形式获取会员ID
function get_member_id_select($name, $level, $selected="", $event="")
{
    global $g5;

    $sql = " select mb_id from {$g5['member_table']} where mb_level >= '{$level}' ";
    $result = sql_query($sql);
    $str = '<select id="'.$name.'" name="'.$name.'" '.$event.'><option value="">未选择</option>';
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $str .= '<option value="'.$row['mb_id'].'"';
        if ($row['mb_id'] == $selected) $str .= ' selected';
        $str .= '>'.$row['mb_id'].'</option>';
    }
    $str .= '</select>';
    return $str;
}

// 权限检查
function auth_check($auth, $attr)
{
    global $is_admin;

    if ($is_admin == 'super') return;

    if (!trim($auth))
        alert('没有当前栏目访问权限\\n\\n访问权限由网站管理员设置');

    $attr = strtolower($attr);

    if (!strstr($auth, $attr)) {
        if ($attr == 'r')
            alert('没有浏览权限');
        else if ($attr == 'w')
            alert('没有编辑、创建权限');
        else if ($attr == 'd')
            alert('没有删除权限');
        else
            alert('属性设置错误');
    }
}


// 工作类型图标
function icon($act, $link='', $target='_parent')
{
    global $g5;

    $img = array('输入'=>'insert', '添加'=>'insert', '创建'=>'insert', '修改'=>'modify', '删除'=>'delete', '移动'=>'move', '群组'=>'move', '查看'=>'view', '预览'=>'view', '复制'=>'copy');
    $icon = '<img src="'.G5_ADMIN_PATH.'/img/icon_'.$img[$act].'.gif" title="'.$act.'">';
    if ($link)
        $s = '<a href="'.$link.'">'.$icon.'</a>';
    else
        $s = $icon;
    return $s;
}


// rm -rf 选项 : 当无法执行exec(), system() 函数或windows服务器时使用
// www.php.net 参考 : pal at degerstrom dot com
function rm_rf($file)
{
    if (file_exists($file)) {
        if (is_dir($file)) {
            $handle = opendir($file);
            while($filename = readdir($handle)) {
                if ($filename != '.' && $filename != '..')
                    rm_rf($file.'/'.$filename);
            }
            closedir($handle);

            @chmod($file, G5_DIR_PERMISSION);
            @rmdir($file);
        } else {
            @chmod($file, G5_FILE_PERMISSION);
            @unlink($file);
        }
    }
}

// 输入框指南
function help($help="")
{
    global $g5;

    $str  = '<span class="frm_info">'.str_replace("\n", "<br>", $help).'</span>';

    return $str;
}

// 显示顺序
function order_select($fld, $sel='')
{
    $s = '<select name="'.$fld.'" id="'.$fld.'">';
    for ($i=1; $i<=100; $i++) {
        $s .= '<option value="'.$i.'" ';
        if ($sel) {
            if ($i == $sel) {
                $s .= 'selected';
            }
        } else {
            if ($i == 50) {
                $s .= 'selected';
            }
        }
        $s .= '>'.$i.'</option>';
    }
    $s .= '</select>';

    return $s;
}

// 访问权限检查
if (!$member['mb_id'])
{
    //alert('请登录后使用', '$g5['bbs_path']/login.php?url=' . urlencode('$_SERVER['PHP_SELF']?w=$w&mb_id=$mb_id'));
    alert('请登录后使用', G5_BBS_URL.'/login.php?url=' . urlencode(G5_ADMIN_URL));
}
else if ($is_admin != 'super')
{
    $auth = array();
    $sql = " select au_menu, au_auth from {$g5['auth_table']} where mb_id = '{$member['mb_id']}' ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++)
    {
        $auth[$row['au_menu']] = $row['au_auth'];
    }

    if (!$i)
    {
        alert('您没有管理或访问权限', G5_URL);
    }
}

// 对比ip地址与浏览器信息，判断异常数据时发送邮件至管理员
$admin_key = md5($member['mb_datetime'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
if (get_session('ss_mb_key') !== $admin_key) {

    session_destroy();

    include_once(G5_LIB_PATH.'/mailer.lib.php');
    // 邮件通知
    mailer($member['mb_nick'], $member['mb_email'], $member['mb_email'], 'XSS攻击提醒', $_SERVER['REMOTE_ADDR'].' 此IP地址尝试进行XSS攻击\n\n尝试获取管理员权限\n\n建议立即进入后台屏蔽此ip地址访问\n\n'.G5_URL, 0);

    alert_close('请通过正常访问路径进行操作');
}

@ksort($auth);

// 可变菜单
unset($auth_menu);
unset($menu);
unset($amenu);
$tmp = dir(G5_ADMIN_PATH);
while ($entry = $tmp->read()) {
    if (!preg_match('/^admin.menu([0-9]{3}).*\.php$/', $entry, $m))
        continue;  // 如文件名未以menu开头开始则忽略

    $amenu[$m[1]] = $entry;
    include_once(G5_ADMIN_PATH.'/'.$entry);
}
@ksort($amenu);

$arr_query = array();
if (isset($sst))  $arr_query[] = 'sst='.$sst;
if (isset($sod))  $arr_query[] = 'sod='.$sod;
if (isset($sfl))  $arr_query[] = 'sfl='.$sfl;
if (isset($stx))  $arr_query[] = 'stx='.$stx;
if (isset($page)) $arr_query[] = 'page='.$page;
$qstr = implode("&amp;", $arr_query);

// 系统后台不使用附加javascript
//$config['cf_add_script'] = '';
?>