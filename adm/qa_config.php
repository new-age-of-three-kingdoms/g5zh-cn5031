<?php
$sub_menu = "300500";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'r');

$token = get_token();

$g5['title'] = '在线咨询设置';
include_once ('./admin.head.php');

// DB 创建数据表
if(!sql_query(" DESCRIBE `{$g5['qa_config_table']}` ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['qa_config_table']}` (
                  `qa_title` varchar(255) NOT NULL DEFAULT'',
                  `qa_category` varchar(255) NOT NULL DEFAULT'',
                  `qa_skin` varchar(255) NOT NULL DEFAULT '',
                  `qa_mobile_skin` varchar(255) NOT NULL DEFAULT '',
                  `qa_use_email` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_req_email` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_use_hp` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_req_hp` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_use_sms` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_send_number` varchar(255) NOT NULL DEFAULT '',
                  `qa_admin_hp` varchar(255) NOT NULL DEFAULT '',
                  `qa_use_editor` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_subject_len` int(11) NOT NULL DEFAULT '0',
                  `qa_mobile_subject_len` int(11) NOT NULL DEFAULT '0',
                  `qa_page_rows` int(11) NOT NULL DEFAULT '0',
                  `qa_mobile_page_rows` int(11) NOT NULL DEFAULT '0',
                  `qa_image_width` int(11) NOT NULL DEFAULT '0',
                  `qa_upload_size` int(11) NOT NULL DEFAULT '0',
                  `qa_insert_content` text NOT NULL,
                  `qa_include_head` varchar(255) NOT NULL DEFAULT '',
                  `qa_include_tail` varchar(255) NOT NULL DEFAULT '',
                  `qa_content_head` text NOT NULL,
                  `qa_content_tail` text NOT NULL,
                  `qa_mobile_content_head` text NOT NULL,
                  `qa_mobile_content_tail` text NOT NULL,
                  `qa_1_subj` varchar(255) NOT NULL DEFAULT '',
                  `qa_2_subj` varchar(255) NOT NULL DEFAULT '',
                  `qa_3_subj` varchar(255) NOT NULL DEFAULT '',
                  `qa_4_subj` varchar(255) NOT NULL DEFAULT '',
                  `qa_5_subj` varchar(255) NOT NULL DEFAULT '',
                  `qa_1` varchar(255) NOT NULL DEFAULT '',
                  `qa_2` varchar(255) NOT NULL DEFAULT '',
                  `qa_3` varchar(255) NOT NULL DEFAULT '',
                  `qa_4` varchar(255) NOT NULL DEFAULT '',
                  `qa_5` varchar(255) NOT NULL DEFAULT ''
                )", true);
    sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['qa_content_table']}` (
                  `qa_id` int(11) NOT NULL AUTO_INCREMENT,
                  `qa_num` int(11) NOT NULL DEFAULT '0',
                  `qa_parent` int(11) NOT NULL DEFAULT '0',
                  `qa_related` int(11) NOT NULL DEFAULT '0',
                  `mb_id` varchar(20) NOT NULL DEFAULT '',
                  `qa_name` varchar(255) NOT NULL DEFAULT '',
                  `qa_email` varchar(255) NOT NULL DEFAULT '',
                  `qa_hp` varchar(255) NOT NULL DEFAULT '',
                  `qa_type` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_category` varchar(255) NOT NULL DEFAULT '',
                  `qa_email_recv` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_sms_recv` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_html` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_subject` varchar(255) NOT NULL DEFAULT '',
                  `qa_content` text NOT NULL,
                  `qa_status` tinyint(4) NOT NULL DEFAULT '0',
                  `qa_file1` varchar(255) NOT NULL DEFAULT '',
                  `qa_source1` varchar(255) NOT NULL DEFAULT '',
                  `qa_file2` varchar(255) NOT NULL DEFAULT '',
                  `qa_source2` varchar(255) NOT NULL DEFAULT '',
                  `qa_ip` varchar(255) NOT NULL DEFAULT '',
                  `qa_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `qa_1` varchar(255) NOT NULL DEFAULT '',
                  `qa_2` varchar(255) NOT NULL DEFAULT '',
                  `qa_3` varchar(255) NOT NULL DEFAULT '',
                  `qa_4` varchar(255) NOT NULL DEFAULT '',
                  `qa_5` varchar(255) NOT NULL DEFAULT '',
                  PRIMARY KEY (`qa_id`),
                  KEY `qa_num_parent` (`qa_num`,`qa_parent`)
                )", true);
}

$sql = " SHOW COLUMNS FROM `{$g5['qa_content_table']}` LIKE 'qa_content' ";
$row = sql_fetch($sql);
if(strpos($row['Type'], 'text') === false) {
    sql_query(" ALTER TABLE `{$g5['qa_content_table']}` CHANGE `qa_content` `qa_content` text NOT NULL ", true);
}

$qaconfig = get_qa_config();

if(empty($qaconfig)) {
    $sql = " insert into `{$g5['qa_config_table']}`
                ( qa_title, qa_category, qa_skin, qa_mobile_skin, qa_use_email, qa_req_email, qa_use_hp, qa_req_hp, qa_use_editor, qa_subject_len, qa_mobile_subject_len, qa_page_rows, qa_mobile_page_rows, qa_image_width, qa_upload_size, qa_insert_content )
              values
                ( '在线咨询', '会员|积分', 'basic', 'basic', '1', '0', '1', '0', '1', '60', '30', '15', '15', '600', '1048576', '' ) ";
    sql_query($sql);

    $qaconfig = get_qa_config();
}

// 添加管理员邮件数据
if(!isset($qaconfig['qa_admin_email'])) {
    sql_query(" ALTER TABLE `{$g5['qa_config_table']}`
                    ADD `qa_admin_email` varchar(255) NOT NULL DEFAULT '' AFTER `qa_admin_hp` ", true);
}

// 添加顶部、底部设置数据
if(!isset($qaconfig['qa_include_head'])) {
    sql_query(" ALTER TABLE `{$g5['qa_config_table']}`
                    ADD `qa_include_head` varchar(255) NOT NULL DEFAULT '' AFTER `qa_insert_content`,
                    ADD `qa_include_tail` varchar(255) NOT NULL DEFAULT '' AFTER `qa_include_head`,
                    ADD `qa_content_head` text NOT NULL AFTER `qa_include_tail`,
                    ADD `qa_content_tail` text NOT NULL AFTER `qa_content_head`,
                    ADD `qa_mobile_content_head` text NOT NULL AFTER `qa_content_tail`,
                    ADD `qa_mobile_content_tail` text NOT NULL AFTER `qa_mobile_content_head` ", true);
}
?>

<form name="fqaconfigform" id="fqaconfigform" method="post" onsubmit="return fqaconfigform_submit(this);" autocomplete="off">
<input type="hidden" name="token" value="<?php echo $token ?>" id="token">

<section id="anc_cf_qa_config">
    <h2 class="h2_frm">在线咨询设置</h2>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>在线咨询设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="qa_title">标题<strong class="sound_only">必选项</strong></label></th>
            <td>
                <input type="text" name="qa_title" value="<?php echo $qaconfig['qa_title'] ?>" id="qa_title" required class="required frm_input" size="40">
                <a href="<?php echo G5_BBS_URL; ?>/qalist.php" class="btn_frmline">进入在线咨询</a>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_category">分类<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('多个分类之间请使用(|)进行分割。(例:提问|回答)，请勿在分类名称前加入特殊符号') ?>
                <input type="text" name="qa_category" value="<?php echo $qaconfig['qa_category'] ?>" id="qa_category" required class="required frm_input" size="70">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_skin">皮肤目录<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo get_skin_select('qa', 'qa_skin', 'qa_skin', $qaconfig['qa_skin'], 'required'); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_mobile_skin">触屏版皮肤<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo get_mobile_skin_select('qa', 'qa_mobile_skin', 'qa_mobile_skin', $qaconfig['qa_mobile_skin'], 'required'); ?>
            </td>
        </tr>
        <tr>
            <th scope="row">输入邮件地址</th>
            <td>
                <input type="checkbox" name="qa_use_email" value="1" id="qa_use_email" <?php echo $qaconfig['qa_use_email']?'checked':''; ?>> <label for="qa_use_email">显示</label>
                <input type="checkbox" name="qa_req_email" value="1" id="qa_req_email" <?php echo $qaconfig['qa_req_email']?'checked':''; ?>> <label for="qa_req_email">必选输入项</label>
            </td>
        </tr>
        <tr>
            <th scope="row">输入手机号码</th>
            <td>
                <input type="checkbox" name="qa_use_hp" value="1" id="qa_use_hp" <?php echo $qaconfig['qa_use_hp']?'checked':''; ?>> <label for="qa_use_hp">显示</label>
                <input type="checkbox" name="qa_req_hp" value="1" id="qa_req_hp" <?php echo $qaconfig['qa_req_hp']?'checked':''; ?>> <label for="qa_req_hp">必选输入项</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_use_sms">手机短信通知</label></th>
            <td>
                <?php echo help('开启手机短信通知后，提问者可以选择是否通过手机短信接收回复内容。<br>使用此功能需要在网站基本设置中开启短信功能 > <a href="'.G5_ADMIN_URL.'/config_form.php#anc_cf_sms">手机短信设置</a>') ?>
                <select name="qa_use_sms" id="qa_use_sms">
                    <?php echo option_selected(0, $qaconfig['qa_use_sms'], '不使用'); ?>
                    <?php echo option_selected(1, $qaconfig['qa_use_sms'], '使用'); ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_send_number">短信发信号码</label></th>
            <td>
                <?php echo help('用于显示在接收方的来短信号码'); ?>
                <input type="text" name="qa_send_number" value="<?php echo $qaconfig['qa_send_number'] ?>" id="qa_send_number" class="frm_input"  size="30">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_admin_hp">管理员手机号码</label></th>
            <td>
                <?php echo help('输入管理员手机号码后如有新提问发布将通过手机短信通知管理员<br>如未开启手机短信功能将不发送'); ?>
                <input type="text" name="qa_admin_hp" value="<?php echo $qaconfig['qa_admin_hp'] ?>" id="qa_admin_hp" class="frm_input"  size="30">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_admin_email">管理员邮件地址</label></th>
            <td>
                <?php echo help('输入管理员邮件地址后如有新提问发布将通过邮件通知管理员'); ?>
                <input type="text" name="qa_admin_email" value="<?php echo $qaconfig['qa_admin_email'] ?>" id="qa_admin_email" class="frm_input"  size="50">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_use_editor">开启富文本编辑器</label></th>
            <td>
                <?php echo help('是否允许作者使用富文本编辑器制作内容。（根据皮肤选项有所区别）'); ?>
                <select name="qa_use_editor" id="qa_use_editor">
                    <?php echo option_selected(0, $qaconfig['qa_use_editor'], '不使用'); ?>
                    <?php echo option_selected(1, $qaconfig['qa_use_editor'], '使用'); ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_subject_len">标题长度<strong class="sound_only"> 必选项</strong></label></th>
            <td>
                <?php echo help('目录中显示的标题长度限制') ?>
                <input type="text" name="qa_subject_len" value="<?php echo $qaconfig['qa_subject_len'] ?>" id="qa_subject_len" required class="required numeric frm_input"  size="4">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_mobile_subject_len">触屏版标题长度<strong class="sound_only"> 必选项</strong></label></th>
            <td>
                <?php echo help('目录中显示的标题长度限制') ?>
                <input type="text" name="qa_mobile_subject_len" value="<?php echo $qaconfig['qa_mobile_subject_len'] ?>" id="qa_mobile_subject_len" required class="required numeric frm_input"  size="4">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_page_rows">每页显示内容数量<strong class="sound_only"> 必选项</strong></label></th>
            <td>
                <input type="text" name="qa_page_rows" value="<?php echo $qaconfig['qa_page_rows'] ?>" id="qa_page_rows" required class="required numeric frm_input"  size="4">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_mobile_page_rows">触屏版每页显示内容数量<strong class="sound_only"> 必选项</strong></label></th>
            <td>
                <input type="text" name="qa_mobile_page_rows" value="<?php echo $qaconfig['qa_mobile_page_rows'] ?>" id="qa_mobile_page_rows" required class="required numeric frm_input"  size="4">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_image_width">图片宽度<strong class="sound_only"> 必选项</strong></label></th>
            <td>
                <?php echo help('论坛中显示图片的最大宽度限制') ?>
                <input type="text" name="qa_image_width" value="<?php echo $qaconfig['qa_image_width'] ?>" id="qa_image_width" required class="required numeric frm_input"  size="4"> 像素
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_upload_size">附件上传限制<strong class="sound_only"> 必选项</strong></label></th>
            <td>
                <?php echo help('当前服务器支持最大值为'.ini_get("upload_max_filesize").' , 1 MB = 1,048,576 bytes') ?>
                限制每个上传附件<input type="text" name="qa_upload_size" value="<?php echo $qaconfig['qa_upload_size'] ?>" id="qa_upload_size" required class="required numeric frm_input"  size="10"> bytes 以内
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_include_head">顶部文件路径</label></th>
            <td>
                <input type="text" name="qa_include_head" value="<?php echo $qaconfig['qa_include_head'] ?>" id="qa_include_head" class="frm_input" size="50">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_include_tail">底部文件路径</label></th>
            <td>
                <input type="text" name="qa_include_tail" value="<?php echo $qaconfig['qa_include_tail'] ?>" id="qa_include_tail" class="frm_input" size="50">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_content_head">顶部内容</label></th>
            <td>
                <?php echo editor_html("qa_content_head", get_text($qaconfig['qa_content_head'], 0)); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_content_tail">底部内容</label></th>
            <td>
                <?php echo editor_html("qa_content_tail", get_text($qaconfig['qa_content_tail'], 0)); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_mobile_content_head">触屏版顶部内容</label></th>
            <td>
                <?php echo editor_html("qa_mobile_content_head", get_text($qaconfig['qa_mobile_content_head'], 0)); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_mobile_content_tail">触屏版底部内容</label></th>
            <td>
                <?php echo editor_html("qa_mobile_content_tail", get_text($qaconfig['qa_mobile_content_tail'], 0)); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="qa_insert_content">发布主题默认内容</label></th>
            <td>
                <textarea id="qa_insert_content" name="qa_insert_content" rows="5"><?php echo $qaconfig['qa_insert_content'] ?></textarea>
            </td>
        </tr>
        <?php for ($i=1; $i<=5; $i++) { ?>
        <tr>
            <th scope="row">扩展数据<?php echo $i ?></th>
            <td class="td_extra">
                <label for="qa_<?php echo $i ?>_subj">扩展数据 <?php echo $i ?> 主题</label>
                <input type="text" name="qa_<?php echo $i ?>_subj" id="qa_<?php echo $i ?>_subj" value="<?php echo get_text($qaconfig['qa_'.$i.'_subj']) ?>" class="frm_input">
                <label for="qa_<?php echo $i ?>">扩展数据 <?php echo $i ?>参数</label>
                <input type="text" name="qa_<?php echo $i ?>" value="<?php echo get_text($qaconfig['qa_'.$i]) ?>" id="qa_<?php echo $i ?>" class="frm_input">
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="确定" class="btn_submit" accesskey="s">
</div>

</form>

<script>
function fqaconfigform_submit(f)
{
    <?php echo get_editor_js("qa_content_head"); ?>
    <?php echo get_editor_js("qa_content_tail"); ?>
    <?php echo get_editor_js("qa_mobile_content_head"); ?>
    <?php echo get_editor_js("qa_mobile_content_tail"); ?>

    f.action = "./qa_config_update.php";
    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>