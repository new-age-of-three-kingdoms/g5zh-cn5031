<?php
include_once('./_common.php');

// 内容
$sql = " select * from {$g5['content_table']} where co_id = '$co_id' ";
$co = sql_fetch($sql);
if (!$co['co_id'])
    alert('无内容可以显示');

$g5['title'] = $co['co_subject'];
include_once('./_head.php');

$co_content = $co['co_mobile_content'] ? $co['co_mobile_content'] : $co['co_content'];
$str = conv_content($co_content, $co['co_html'], $co['co_tag_filter_use']);

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
if(trim($co['co_mobile_skin']) == '')
    $co['co_mobile_skin'] = 'basic';

$content_skin_path = G5_MOBILE_PATH .'/'.G5_SKIN_DIR.'/content/'.$co['co_mobile_skin'];
$content_skin_url  = G5_MOBILE_URL .'/'.G5_SKIN_DIR.'/content/'.$co['co_mobile_skin'];
$skin_file = $content_skin_path.'/content.skin.php';

if(is_file($skin_file)) {
    include($skin_file);
} else {
    echo '<p>'.str_replace(G5_PATH.'/', '', $skin_file).'未找到此文件</p>';
}

include_once('./_tail.php');
?>
