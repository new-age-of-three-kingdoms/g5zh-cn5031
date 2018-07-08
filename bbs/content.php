<?php
include_once('./_common.php');

//检查dbconfig文件中是否有$g5['content_table']排列参数
if( !isset($g5['content_table']) ){
    die('<meta charset="utf-8">请您进入系统后台网站设置内容管理栏目');
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/content.php');
    return;
}

// 内容
$sql = " select * from {$g5['content_table']} where co_id = '$co_id' ";
$co = sql_fetch($sql);
if (!$co['co_id'])
    alert('无内容可以显示');

$g5['title'] = $co['co_subject'];

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

$str = conv_content($co['co_content'], $co['co_html'], $co['co_tag_filter_use']);

// $src 转换为 $dst
unset($src);
unset($dst);
$src[] = "/{{商城名称}}|{{网站名称}}/";
$dst[] = $config['cf_title'];
$src[] = "/{{公司名称}}|{{商标}}/";
$dst[] = $default['de_admin_company_name'];
$src[] = "/{{法人}}/";
$dst[] = $default['de_admin_company_owner'];
$src[] = "/{{营业执照}}/";
$dst[] = $default['de_admin_company_saupja_no'];
$src[] = "/{{服务热线}}/";
$dst[] = $default['de_admin_company_tel'];
$src[] = "/{{传真号码}}/";
$dst[] = $default['de_admin_company_fax'];
$src[] = "/{{ICP备案}}/";
$dst[] = $default['de_admin_company_tongsin_no'];
$src[] = "/{{邮编号码}}/";
$dst[] = $default['de_admin_company_zip'];
$src[] = "/{{企业地址}}/";
$dst[] = $default['de_admin_company_addr'];
$src[] = "/{{运营负责人}}|{{管理员}}/";
$dst[] = $default['de_admin_name'];
$src[] = "/{{运营方e-mail}}|{{管理员e-mail}}/i";
$dst[] = $default['de_admin_email'];
$src[] = "/{{信息安全管理员}}/";
$dst[] = $default['de_admin_info_name'];
$src[] = "/{{信息管理员e-mail}}|{{信息安全责任人e-mail}}/i";
$dst[] = $default['de_admin_info_email'];

$str = preg_replace($src, $dst, $str);

//皮肤(skin)目录
if(trim($co['co_skin']) == '')
    $co['co_skin'] = 'basic';

$content_skin_path = G5_SKIN_PATH.'/content/'.$co['co_skin'];
$content_skin_url  = G5_SKIN_URL.'/content/'.$co['co_skin'];
$skin_file = $content_skin_path.'/content.skin.php';

if ($is_admin)
    echo '<div class="ctt_admin"><a href="'.G5_ADMIN_URL.'/contentform.php?w=u&amp;co_id='.$co_id.'" class="btn_admin">内容编辑</a></div>';
?>

<?php
if(is_file($skin_file)) {
    $himg = G5_DATA_PATH.'/content/'.$co_id.'_h';
    if (file_exists($himg)) // 顶部图片
        echo '<div id="ctt_himg" class="ctt_img"><img src="'.G5_DATA_URL.'/content/'.$co_id.'_h" alt=""></div>';

    include($skin_file);

    $timg = G5_DATA_PATH.'/content/'.$co_id.'_t';
    if (file_exists($timg)) // 底部图片
        echo '<div id="ctt_timg" class="ctt_img"><img src="'.G5_DATA_URL.'/content/'.$co_id.'_t" alt=""></div>';
} else {
    echo '<p>'.str_replace(G5_PATH.'/', '', $skin_file).'未找到此文件</p>';
}

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>
