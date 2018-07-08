<?php
$sub_menu = "300100";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

$sql = " select count(*) as cnt from {$g5['group_table']} ";
$row = sql_fetch($sql);
if (!$row['cnt'])
    alert('请先建立论坛群组', './boardgroup_form.php');

$html_title = '论坛';

if (!isset($board['bo_device'])) {
    // 创建论坛使用数据
    // both : pc, mobile全部支持
    // pc : 仅限pc
    // mobile : 仅限移动端
    // none : 不使用
    sql_query(" ALTER TABLE  `{$g5['board_table']}` ADD  `bo_device` ENUM(  'both',  'pc',  'mobile' ) NOT NULL DEFAULT  'both' AFTER  `bo_subject` ", false);
}

if (!isset($board['bo_mobile_skin'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_skin` VARCHAR(255) NOT NULL DEFAULT '' AFTER `bo_skin` ", false);
}

if (!isset($board['bo_gallery_width'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_gallery_width` INT NOT NULL AFTER `bo_gallery_cols`,  ADD `bo_gallery_height` INT NOT NULL DEFAULT '0' AFTER `bo_gallery_width`,  ADD `bo_mobile_gallery_width` INT NOT NULL DEFAULT '0' AFTER `bo_gallery_height`,  ADD `bo_mobile_gallery_height` INT NOT NULL DEFAULT '0' AFTER `bo_mobile_gallery_width` ", false);
}

if (!isset($board['bo_mobile_subject_len'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_subject_len` INT(11) NOT NULL DEFAULT '0' AFTER `bo_subject_len` ", false);
}

if (!isset($board['bo_mobile_page_rows'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_page_rows` INT(11) NOT NULL DEFAULT '0' AFTER `bo_page_rows` ", false);
}

if (!isset($board['bo_mobile_content_head'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_content_head` TEXT NOT NULL AFTER `bo_content_head`, ADD `bo_mobile_content_tail` TEXT NOT NULL AFTER `bo_content_tail`", false);
}

if (!isset($board['bo_use_cert'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_use_cert` ENUM('','cert','adult') NOT NULL DEFAULT '' AFTER `bo_use_email` ", false);
}

if (!isset($board['bo_use_sns'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_use_sns` TINYINT NOT NULL DEFAULT '0' AFTER `bo_use_cert` ", false);

    $result = sql_query(" select bo_table from `{$g5['board_table']}` ");
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        sql_query(" ALTER TABLE `{$g5['write_prefix']}{$row['bo_table']}`
                    ADD `wr_facebook_user` VARCHAR(255) NOT NULL DEFAULT '' AFTER `wr_ip`,
                    ADD `wr_twitter_user` VARCHAR(255) NOT NULL DEFAULT '' AFTER `wr_facebook_user` ", false);
    }
}

$sql = " SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_use_cert' ";
$row = sql_fetch($sql);
if(strpos($row['Type'], 'hp-') === false) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` CHANGE `bo_use_cert` `bo_use_cert` ENUM('','cert','adult','hp-cert','hp-adult') NOT NULL DEFAULT '' ", false);
}

if (!isset($board['bo_use_list_file'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_use_list_file` TINYINT NOT NULL DEFAULT '0' AFTER `bo_use_list_view` ", false);

    $result = sql_query(" select bo_table from `{$g5['board_table']}` ");
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        sql_query(" ALTER TABLE `{$g5['write_prefix']}{$row['bo_table']}`
                    ADD `wr_file` TINYINT NOT NULL DEFAULT '0' AFTER `wr_datetime` ", false);
    }
}

if (!isset($board['bo_mobile_subject'])) {
    sql_query(" ALTER TABLE `{$g5['board_table']}` ADD `bo_mobile_subject` VARCHAR(255) NOT NULL DEFAULT '' AFTER `bo_subject` ", false);
}

$required = "";
$readonly = "";
if ($w == '') {

    $html_title .= '创建';

    $required = 'required';
    $required_valid = 'alnum_';
    $sound_only = '<strong class="sound_only">必选项</strong>';

    $board['bo_count_delete'] = 1;
    $board['bo_count_modify'] = 1;
    $board['bo_read_point'] = $config['cf_read_point'];
    $board['bo_write_point'] = $config['cf_write_point'];
    $board['bo_comment_point'] = $config['cf_comment_point'];
    $board['bo_download_point'] = $config['cf_download_point'];

    $board['bo_gallery_cols'] = 4;
    $board['bo_gallery_width'] = 174;
    $board['bo_gallery_height'] = 124;
    $board['bo_mobile_gallery_width'] = 125;
    $board['bo_mobile_gallery_height'] = 100;
    $board['bo_table_width'] = 100;
    $board['bo_page_rows'] = $config['cf_page_rows'];
    $board['bo_mobile_page_rows'] = $config['cf_page_rows'];
    $board['bo_subject_len'] = 60;
    $board['bo_mobile_subject_len'] = 30;
    $board['bo_new'] = 24;
    $board['bo_hot'] = 100;
    $board['bo_image_width'] = 600;
    $board['bo_upload_count'] = 2;
    $board['bo_upload_size'] = 1048576;
    $board['bo_reply_order'] = 1;
    $board['bo_use_search'] = 1;
    $board['bo_skin'] = 'basic';
    $board['bo_mobile_skin'] = 'basic';
    $board['gr_id'] = $gr_id;
    $board['bo_use_secret'] = 0;
    $board['bo_include_head'] = '_head.php';
    $board['bo_include_tail'] = '_tail.php';

} else if ($w == 'u') {

    $html_title .= '编辑';

    if (!$board['bo_table'])
        alert('不存在的论坛');

    if ($is_admin == 'group') {
        if ($member['mb_id'] != $group['gr_admin'])
            alert('群组设置错误');
    }

    $readonly = 'readonly';

}

if ($is_admin != 'super') {
    $group = get_group($board['gr_id']);
    $is_admin = is_admin($member['mb_id']);
}

$g5['title'] = $html_title;
include_once ('./admin.head.php');

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_bo_basic">基本设置</a></li>
    <li><a href="#anc_bo_auth">权限 设置</a></li>
    <li><a href="#anc_bo_function">功能设置</a></li>
    <li><a href="#anc_bo_design">设计样式</a></li>
    <li><a href="#anc_bo_point">积分设置</a></li>
    <li><a href="#anc_bo_extra">扩展数据</a></li>
</ul>';

$frm_submit = '<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="确定" class="btn_submit" accesskey="s">
    <a href="./board_list.php?'.$qstr.'">目录</a>'.PHP_EOL;
if ($w == 'u') $frm_submit .= '    <a href="./board_copy.php?bo_table='.$bo_table.'" id="board_copy" target="win_board_copy">论坛复制</a>
    <a href="'.G5_BBS_URL.'/board.php?bo_table='.$board['bo_table'].'" class="btn_frmline">访问论坛版块</a>
    <a href="./board_thumbnail_delete.php?bo_table='.$board['bo_table'].'&amp;'.$qstr.'" onclick="return delete_confirm2(\'您确定要删除论坛缩略图文件吗？\');">缩略图删除</a>
    '.PHP_EOL;
$frm_submit .= '</div>';
?>

<form name="fboardform" id="fboardform" action="./board_form_update.php" onsubmit="return fboardform_submit(this)" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">

<section id="anc_bo_basic">
    <h2 class="h2_frm">论坛基本设置</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>论坛基本设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="bo_table">TABLE<?php echo $sound_only ?></label></th>
            <td colspan="2">
                <input type="text" name="bo_table" value="<?php echo $board['bo_table'] ?>" id="bo_table" <?php echo $required ?> <?php echo $readonly ?> class="frm_input <?php echo $reaonly ?> <?php echo $required ?> <?php echo $required_valid ?>" maxlength="20">
                <?php if ($w == '') { ?>
                    英文、数字及下划线（20字以内，禁止使用空格）
                <?php } else { ?>
                    <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $board['bo_table'] ?>" class="btn_frmline">访问论坛版块</a>
                    <a href="./board_list.php" class="btn_frmline">返回目录</a>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gr_id">群组<strong class="sound_only">必选项</strong></label></th>
            <td colspan="2">
                <?php echo get_group_select('gr_id', $board['gr_id'], 'required'); ?>
                <?php if ($w=='u') { ?><a href="javascript:document.location.href='./board_list.php?sfl=a.gr_id&stx='+document.fboardform.gr_id.value;" class="btn_frmline">相同群组论坛列表</a><?php } ?></td>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_subject">论坛版块名称<strong class="sound_only">必选项</strong></label></th>
            <td colspan="2">
                <input type="text" name="bo_subject" value="<?php echo get_text($board['bo_subject']) ?>" id="bo_subject" required class="required frm_input" size="80" maxlength="120">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_mobile_subject">触屏板 论坛版块名称</label></th>
            <td colspan="2">
                <?php echo help("请设置触屏版显示的论坛版块名称，如果不设置将会显示与pc版本相同的名称") ?>
                <input type="text" name="bo_mobile_subject" value="<?php echo get_text($board['bo_mobile_subject']) ?>" id="bo_mobile_subject" class="frm_input" size="80" maxlength="120">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_device">访问设备</label></th>
            <td>
                <?php echo help("区分PC版本或触屏版本") ?>
                <select id="bo_device" name="bo_device">
                    <option value="both"<?php echo get_selected($board['bo_device'], 'both'); ?>>全部允许</option>
                    <option value="pc"<?php echo get_selected($board['bo_device'], 'pc'); ?>>仅限PC</option>
                    <option value="mobile"<?php echo get_selected($board['bo_device'], 'mobile'); ?>>仅限触屏版</option>
                </select>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_device" value="1" id="chk_grp_device">
                <label for="chk_grp_device">应用到群组</label>
                <input type="checkbox" name="chk_all_device" value="1" id="chk_all_device">
                <label for="chk_all_device">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_category_list">分类</label></th>
            <td>
                <?php echo help('多个分类之间请使用(|)进行分割。(例:提问|回答)，请勿在分类名称前加入特殊符号') ?>
                <input type="text" name="bo_category_list" value="<?php echo get_text($board['bo_category_list']) ?>" id="bo_category_list" class="frm_input" size="70">
                <input type="checkbox" name="bo_use_category" value="1" id="bo_use_category" <?php echo $board['bo_use_category']?'checked':''; ?>>
                <label for="bo_use_category">使用</label>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_category_list" value="1" id="chk_grp_category_list">
                <label for="chk_grp_category_list">应用到群组</label>
                <input type="checkbox" name="chk_all_category_list" value="1" id="chk_all_category_list">
                <label for="chk_all_category_list">应用到全部</label>
            </td>
        </tr>
        <?php if ($w == 'u') { ?>
        <tr>
            <th scope="row"><label for="proc_count">计数器刷新</label></th>
            <td colspan="2">
                <?php echo help('当前主题 : '.number_format($board['bo_count_write']).', 当前回帖 : '.number_format($board['bo_count_comment'])."\n".'当论坛统计数据出现误差时请使用此功能') ?>
                <input type="checkbox" name="proc_count" value="1" id="proc_count">
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_bo_auth">
    <h2 class="h2_frm">论坛 权限 设置</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>论坛 权限 设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="bo_admin">论坛 管理员</label></th>
            <td>
                <input type="text" name="bo_admin" value="<?php echo $board['bo_admin'] ?>" id="bo_admin" class="frm_input" maxlength="20">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_admin" value="1" id="chk_grp_admin">
                <label for="chk_grp_admin">应用到群组</label>
                <input type="checkbox" name="chk_all_admin" value="1" id="chk_all_admin">
                <label for="chk_all_admin">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_list_level">查看目录 权限</label></th>
            <td>
                <?php echo help('权限1为游客，2以上为注册会员，权限10为最高管理员') ?>
                <?php echo get_member_level_select('bo_list_level', 1, 10, $board['bo_list_level']) ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_list_level" value="1" id="chk_grp_list_level">
                <label for="chk_grp_list_level">应用到群组</label>
                <input type="checkbox" name="chk_all_list_level" value="1" id="chk_all_list_level">
                <label for="chk_all_list_level">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_read_level">浏览权限</label></th>
            <td>
                <?php echo get_member_level_select('bo_read_level', 1, 10, $board['bo_read_level']) ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_read_level" value="1" id="chk_grp_read_level">
                <label for="chk_grp_read_level">应用到群组</label>
                <input type="checkbox" name="chk_all_read_level" value="1" id="chk_all_read_level">
                <label for="chk_all_read_level">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_write_level">发表主题 权限</label></th>
            <td>
                <?php echo get_member_level_select('bo_write_level', 1, 10, $board['bo_write_level']) ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_write_level" value="1" id="chk_grp_write_level">
                <label for="chk_grp_write_level">应用到群组</label>
                <input type="checkbox" name="chk_all_write_level" value="1" id="chk_all_write_level">
                <label for="chk_all_write_level">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_reply_level">回帖权限</label></th>
            <td>
                <?php echo get_member_level_select('bo_reply_level', 1, 10, $board['bo_reply_level']) ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_reply_level" value="1" id="chk_grp_reply_level">
                <label for="chk_grp_reply_level">应用到群组</label>
                <input type="checkbox" name="chk_all_reply_level" value="1" id="chk_all_reply_level">
                <label for="chk_all_reply_level">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_comment_level">发表评论 权限</label></th>
            <td>
                <?php echo get_member_level_select('bo_comment_level', 1, 10, $board['bo_comment_level']) ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_comment_level" value="1" id="chk_grp_comment_level">
                <label for="chk_grp_comment_level">应用到群组</label>
                <input type="checkbox" name="chk_all_comment_level" value="1" id="chk_all_comment_level">
                <label for="chk_all_comment_level">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_link_level">链接 权限</label></th>
            <td>
                <?php echo get_member_level_select('bo_link_level', 1, 10, $board['bo_link_level']) ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_link_level" value="1" id="chk_grp_link_level">
                <label for="chk_grp_link_level">应用到群组</label>
                <input type="checkbox" name="chk_all_link_level" value="1" id="chk_all_link_level">
                <label for="chk_all_link_level">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_upload_level">上传权限</label></th>
            <td>
                <?php echo get_member_level_select('bo_upload_level', 1, 10, $board['bo_upload_level']) ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_upload_level" value="1" id="chk_grp_upload_level">
                <label for="chk_grp_upload_level">应用到群组</label>
                <input type="checkbox" name="chk_all_upload_level" value="1" id="chk_all_upload_level">
                <label for="chk_all_upload_level">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_download_level">下载权限</label></th>
            <td>
                <?php echo get_member_level_select('bo_download_level', 1, 10, $board['bo_download_level']) ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_download_level" value="1" id="chk_grp_download_level">
                <label for="chk_grp_download_level">应用到群组</label>
                <input type="checkbox" name="chk_all_download_level" value="1" id="chk_all_download_level">
                <label for="chk_all_download_level">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_html_level">HTML 编辑 权限</label></th>
            <td>
                <?php echo get_member_level_select('bo_html_level', 1, 10, $board['bo_html_level']) ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_html_level" value="1" id="chk_grp_html_level">
                <label for="chk_grp_html_level">应用到群组</label>
                <input type="checkbox" name="chk_all_html_level" value="1" id="chk_all_html_level">
                <label for="chk_all_html_level">应用到全部</label>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_bo_function">
    <h2 class="h2_frm">论坛功能设置</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>论坛功能设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="bo_count_modify">禁止修改原文<strong class="sound_only">必选项</strong></label></th>
            <td>
                 <?php echo help('当评论/回帖数量达到设定数量时禁止修改原文内容，设置为0时不启用此功能'); ?>
                评论 <input type="text" name="bo_count_modify" value="<?php echo $board['bo_count_modify'] ?>" id="bo_count_modify" required class="required numeric frm_input" size="3">条以上时禁止修改
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_count_modify" value="1" id="chk_grp_count_modify">
                <label for="chk_grp_count_modify">应用到群组</label>
                <input type="checkbox" name="chk_all_count_modify" value="1" id="chk_all_count_modify">
                <label for="chk_all_count_modify">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_count_delete">禁止删除原文<strong class="sound_only">必选项</strong></label></th>
            <td>
                评论 <input type="text" name="bo_count_delete" value="<?php echo $board['bo_count_delete'] ?>" id="bo_count_delete" required class="required numeric frm_input" size="3">条以上是禁止删除
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_count_delete" value="1" id="chk_grp_count_delete">
                <label for="chk_grp_count_delete">应用到群组</label>
                <input type="checkbox" name="chk_all_count_delete" value="1" id="chk_all_count_delete">
                <label for="chk_all_count_delete">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_sideview">作者信息</label></th>
            <td>
                <input type="checkbox" name="bo_use_sideview" value="1" id="bo_use_sideview" <?php echo $board['bo_use_sideview']?'checked':''; ?>>
                使用 (点击用户名时显示的会员信息栏)
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_sideview" value="1" id="chk_grp_use_sideview">
                <label for="chk_grp_use_sideview">应用到群组</label>
                <input type="checkbox" name="chk_all_use_sideview" value="1" id="chk_all_use_sideview">
                <label for="chk_all_use_sideview">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_secret">主题加密功能</label></th>
            <td>
                <?php echo help('设置为"选项"时允许作者进行是否加密的选择，设置为"强制加密"时所发布的内容将会强制转为加密主题内容(管理员始终显示加密选项) 根据不同皮肤实际应用也会有所不同') ?>
                <select id="bo_use_secret" name="bo_use_secret">
                    <?php echo option_selected(0, $board['bo_use_secret'], "不使用"); ?>
                    <?php echo option_selected(1, $board['bo_use_secret'], "选项"); ?>
                    <?php echo option_selected(2, $board['bo_use_secret'], "强制"); ?>
                </select>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_secret" value="1" id="chk_grp_use_secret">
                <label for="chk_grp_use_secret">应用到群组</label>
                <input type="checkbox" name="chk_all_use_secret" value="1" id="chk_all_use_secret">
                <label for="chk_all_use_secret">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_dhtml_editor">开启富文本编辑器</label></th>
            <td>
                <?php echo help('是否允许作者使用富文本编辑器制作内容。（根据皮肤选项有所区别）') ?>
                <input type="checkbox" name="bo_use_dhtml_editor" value="1" <?php echo $board['bo_use_dhtml_editor']?'checked':''; ?> id="bo_use_dhtml_editor">
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_dhtml_editor" value="1" id="chk_grp_use_dhtml_editor">
                <label for="chk_grp_use_dhtml_editor">应用到群组</label>
                <input type="checkbox" name="chk_all_use_dhtml_editor" value="1" id="chk_all_use_dhtml_editor">
                <label for="chk_all_use_dhtml_editor">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_rss_view">RSS浏览器</label></th>
            <td>
                <?php echo help('允许游客可浏览时才能启用RSS浏览器选项') ?>
                <input type="checkbox" name="bo_use_rss_view" value="1" <?php echo $board['bo_use_rss_view']?'checked':''; ?> id="bo_use_rss_view">
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_rss_view" value="1" id="chk_grp_use_rss_view">
                <label for="chk_grp_use_rss_view">应用到群组</label>
                <input type="checkbox" name="chk_all_use_rss_view" value="1" id="chk_all_use_rss_view">
                <label for="chk_all_use_rss_view">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_good">推荐功能</label></th>
            <td>
                <input type="checkbox" name="bo_use_good" value="1" <?php echo $board['bo_use_good']?'checked':''; ?> id="bo_use_good">
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_good" value="1" id="chk_grp_use_good">
                <label for="chk_grp_use_good">应用到群组</label>
                <input type="checkbox" name="chk_all_use_good" value="1" id="chk_all_use_good">
                <label for="chk_all_use_good">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_nogood">反对功能</label></th>
            <td>
                <input type="checkbox" name="bo_use_nogood" value="1" id="bo_use_nogood" <?php echo $board['bo_use_nogood']?'checked':''; ?>>
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_nogood" value="1" id="chk_grp_use_nogood">
                <label for="chk_grp_use_nogood">应用到群组</label>
                <input type="checkbox" name="chk_all_use_nogood" value="1" id="chk_all_use_nogood">
                <label for="chk_all_use_nogood">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_name">实名显示</label></th>
            <td>
                <input type="checkbox" name="bo_use_name" value="1" id="bo_use_name" <?php echo $board['bo_use_name']?'checked':''; ?>>
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_name" value="1" id="chk_grp_use_name">
                <label for="chk_grp_use_name">应用到群组</label>

                <input type="checkbox" name="chk_all_use_name" value="1" id="chk_all_use_name">
                <label for="chk_all_use_name">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_signature">签名显示</label></th>
            <td>
                <input type="checkbox" name="bo_use_signature" value="1" id="bo_use_signature" <?php echo $board['bo_use_signature']?'checked':''; ?>>
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_signature" value="1" id="chk_grp_use_signature">
                <label for="chk_grp_use_signature">应用到群组</label>
                <input type="checkbox" name="chk_all_use_signature" value="1" id="chk_all_use_signature">
                <label for="chk_all_use_signature">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_ip_view">ip地址显示</label></th>
            <td>
                <input type="checkbox" name="bo_use_ip_view" value="1" id="bo_use_ip_view" <?php echo $board['bo_use_ip_view']?'checked':''; ?>>
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_ip_view" value="1" id="chk_grp_use_ip_view">
                <label for="chk_grp_use_ip_view">应用到群组</label>
                <input type="checkbox" name="chk_all_use_ip_view" value="1" id="chk_all_use_ip_view">
                <label for="chk_all_use_ip_view">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_list_content">目录中预览内容</label></th>
            <td>
                <?php echo help("目录中需要直接读取主题内容时使用，默认不开启"); ?>
                <input type="checkbox" name="bo_use_list_content" value="1" id="bo_use_list_content" <?php echo $board['bo_use_list_content']?'checked':''; ?>>
                使用 (开启后会导致访问速度下降)
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_list_content" value="1" id="chk_grp_use_list_content">
                <label for="chk_grp_use_list_content">应用到群组</label>
                <input type="checkbox" name="chk_all_use_list_content" value="1" id="chk_all_use_list_content">
                <label for="chk_all_use_list_content">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_list_file">目录中显示附件</label></th>
            <td>
                <?php echo help("目录中需要直接显示附件并允许下载时使用，默认不开启"); ?>
                <input type="checkbox" name="bo_use_list_file" value="1" id="bo_use_list_file" <?php echo $board['bo_use_list_file']?'checked':''; ?>>
                使用 (开启后会导致访问速度下降)
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_list_file" value="1" id="chk_grp_use_list_file">
                <label for="chk_grp_use_list_file">应用到群组</label>
                <input type="checkbox" name="chk_all_use_list_file" value="1" id="chk_all_use_list_file">
                <label for="chk_all_use_list_file">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_list_view">显示全部目录</label></th>
            <td>
                <input type="checkbox" name="bo_use_list_view" value="1" id="bo_use_list_view" <?php echo $board['bo_use_list_view']?'checked':''; ?>>
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_list_view" value="1" id="chk_grp_use_list_view">
                <label for="chk_grp_use_list_view">应用到群组</label>
                <input type="checkbox" name="chk_all_use_list_view" value="1" id="chk_all_use_list_view">
                <label for="chk_all_use_list_view">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_email">邮件通知功能</label></th>
            <td>
                <input type="checkbox" name="bo_use_email" value="1" id="bo_use_email" <?php echo $board['bo_use_email']?'checked':''; ?>>
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_email" value="1" id="chk_grp_use_email">
                <label for="chk_grp_use_email">应用到群组</label>
                <input type="checkbox" name="chk_all_use_email" value="1" id="chk_all_use_email">
                <label for="chk_all_use_email">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_cert">实名认证</label></th>
            <td>
                <?php echo help("可以根据会员认证状态设置访问条件"); ?>
                <select id="bo_use_cert" name="bo_use_cert">
                    <?php
                    echo option_selected("",  $board['bo_use_cert'], "不使用");
                    if ($config['cf_cert_use']) {
                        echo option_selected("cert",  $board['bo_use_cert'], "已认证实名会员");
                        echo option_selected("adult", $board['bo_use_cert'], "认证实名的成人会员");
                        echo option_selected("hp-cert",  $board['bo_use_cert'], "已认证手机号码的会员");
                        echo option_selected("hp-adult", $board['bo_use_cert'], "已认证手机号码的成人会员");
                    }
                    ?>
                </select>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_cert" value="1" id="chk_grp_use_cert">
                <label for="chk_grp_use_cert">应用到群组</label>
                <input type="checkbox" name="chk_all_use_cert" value="1" id="chk_all_use_cert">
                <label for="chk_all_use_cert">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_upload_count">附件数量<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('请设置每个主题允许上传附件的数量(设置为0时不限制数量)') ?>
                <input type="text" name="bo_upload_count" value="<?php echo $board['bo_upload_count'] ?>" id="bo_upload_count" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_upload_count" value="1" id="chk_grp_upload_count">
                <label for="chk_grp_upload_count">应用到群组</label>
                <input type="checkbox" name="chk_all_upload_count" value="1" id="chk_all_upload_count">
                <label for="chk_all_upload_count">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_upload_size">附件上传限制<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('当前服务器支持最大值为'.ini_get("upload_max_filesize").' , 1 MB = 1,048,576 bytes') ?>
                限制每个上传附件<input type="text" name="bo_upload_size" value="<?php echo $board['bo_upload_size'] ?>" id="bo_upload_size" required class="required numeric frm_input"  size="10"> bytes 以内
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_upload_size" value="1" id="chk_grp_upload_size">
                <label for="chk_grp_upload_size">应用到群组</label>
                <input type="checkbox" name="chk_all_upload_size" value="1" id="chk_all_upload_size">
                <label for="chk_all_upload_size">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_file_content">附件说明功能</label></th>
            <td>
                <input type="checkbox" name="bo_use_file_content" value="1" id="bo_use_file_content" <?php echo $board['bo_use_file_content']?'checked':''; ?>>使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_file_content" value="1" id="chk_grp_use_file_content">
                <label for="chk_grp_use_file_content">应用到群组</label>
                <input type="checkbox" name="chk_all_use_file_content" value="1" id="chk_all_use_file_content">
                <label for="chk_all_use_file_content">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_write_min">最少长度限制</label></th>
            <td>
                <?php echo help('可以通过该选项设置发表文章时内容文字长度限制，设置0或则管理员、dhtml编辑器开启时将会跳过此限制') ?>
                <input type="text" name="bo_write_min" value="<?php echo $board['bo_write_min'] ?>" id="bo_write_min" class="numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_write_min" value="1" id="chk_grp_write_min">
                <label for="chk_grp_write_min">应用到群组</label>
                <input type="checkbox" name="chk_all_write_min" value="1" id="chk_all_write_min">
                <label for="chk_all_write_min">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_write_max">最多内容限制</label></th>
            <td>
                <?php echo help('可以通过该选项设置发表文章时内容文字长度限制，设置0或则管理员、dhtml编辑器开启时将会跳过此限制') ?>
                <input type="text" name="bo_write_max" value="<?php echo $board['bo_write_max'] ?>" id="bo_write_max" class="numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_write_max" value="1" id="chk_grp_write_max">
                <label for="chk_grp_write_max">应用到群组</label>
                <input type="checkbox" name="chk_all_write_max" value="1" id="chk_all_write_max">
                <label for="chk_all_write_max">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_comment_min">最少评论限制</label></th>
            <td>
                <?php echo help('发布评论时最少内容长度限制，设置为0时关闭验证') ?>
                <input type="text" name="bo_comment_min" value="<?php echo $board['bo_comment_min'] ?>" id="bo_comment_min" class="numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_comment_min" value="1" id="chk_grp_comment_min">
                <label for="chk_grp_comment_min">应用到群组</label>
                <input type="checkbox" name="chk_all_comment_min" value="1" id="chk_all_comment_min">
                <label for="chk_all_comment_min">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_comment_max">最长评论限制</label></th>
            <td>
                <?php echo help('发布评论时最长内容长度限制，设置为0时关闭验证') ?>
                <input type="text" name="bo_comment_max" value="<?php echo $board['bo_comment_max'] ?>" id="bo_comment_max" class="numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_comment_max" value="1" id="chk_grp_comment_max">
                <label for="chk_grp_comment_max">应用到群组</label>
                <input type="checkbox" name="chk_all_comment_max" value="1" id="chk_all_comment_max">
                <label for="chk_all_comment_max">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_sns">sns分享功能</label></th>
            <td>
                <?php echo help("可以推荐主题内容至其他sns平台或则可以设置评论同时进行分享。<br>网站设置中开启了sns分享功能才能使用此功能") ?>
                <input type="checkbox" name="bo_use_sns" value="1" id="bo_use_sns" <?php echo $board['bo_use_sns']?'checked':''; ?>>
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_sns" value="1" id="chk_grp_use_sns">
                <label for="chk_grp_use_sns">应用到群组</label>
                <input type="checkbox" name="chk_all_use_sns" value="1" id="chk_all_use_sns">
                <label for="chk_all_use_sns">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_use_search">开启搜索功能</label></th>
            <td>
                <input type="checkbox" name="bo_use_search" value="1" id="bo_use_search" <?php echo $board['bo_use_search']?'checked':''; ?>>
                使用
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_use_search" value="1" id="chk_grp_use_search">
                <label for="chk_grp_use_search">应用到群组</label>
                <input type="checkbox" name="chk_all_use_search" value="1" id="chk_all_use_search">
                <label for="chk_all_use_search">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_order">显示顺序</label></th>
            <td>
                <?php echo help('参数小的论坛将优先显示') ?>
                <input type="text" name="bo_order" value="<?php echo $board['bo_order'] ?>" id="bo_order" class="frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_order" value="1" id="chk_grp_order">
                <label for="chk_grp_order">应用到群组</label>
                <input type="checkbox" name="chk_all_order" value="1" id="chk_all_order">
                <label for="chk_all_order">应用到全部</label>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_bo_design">
    <h2 class="h2_frm">论坛外观样式</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>论坛外观样式</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
            <tr>
            <th scope="row"><label for="bo_skin">皮肤目录<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo get_skin_select('board', 'bo_skin', 'bo_skin', $board['bo_skin'], 'required'); ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_skin" value="1" id="chk_grp_skin">
                <label for="chk_grp_skin">应用到群组</label>
                <input type="checkbox" name="chk_all_skin" value="1" id="chk_all_skin">
                <label for="chk_all_skin">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_mobile_skin">触屏版<br>皮肤目录<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo get_mobile_skin_select('board', 'bo_mobile_skin', 'bo_mobile_skin', $board['bo_mobile_skin'], 'required'); ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_mobile_skin" value="1" id="chk_grp_mobile_skin">
                <label for="chk_grp_mobile_skin">应用到群组</label>
                <input type="checkbox" name="chk_all_mobile_skin" value="1" id="chk_all_mobile_skin">
                <label for="chk_all_mobile_skin">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_include_head">顶部文件路径</label></th>
            <td>
                <input type="text" name="bo_include_head" value="<?php echo $board['bo_include_head'] ?>" id="bo_include_head" class="frm_input" size="50">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_include_head" value="1" id="chk_grp_include_head">
                <label for="chk_grp_include_head">应用到群组</label>
                <input type="checkbox" name="chk_all_include_head" value="1" id="chk_all_include_head">
                <label for="chk_all_include_head">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_include_tail">底部文件路径</label></th>
            <td>
                <input type="text" name="bo_include_tail" value="<?php echo $board['bo_include_tail'] ?>" id="bo_include_tail" class="frm_input" size="50">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_include_tail" value="1" id="chk_grp_include_tail">
                <label for="chk_grp_include_tail">应用到群组</label>
                <input type="checkbox" name="chk_all_include_tail" value="1" id="chk_all_include_tail">
                <label for="chk_all_include_tail">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_content_head">顶部内容</label></th>
            <td>
                <?php echo editor_html("bo_content_head", get_text($board['bo_content_head'], 0)); ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_content_head" value="1" id="chk_grp_content_head">
                <label for="chk_grp_content_head">应用到群组</label>
                <input type="checkbox" name="chk_all_content_head" value="1" id="chk_all_content_head">
                <label for="chk_all_content_head">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_content_tail">底部内容</label></th>
            <td>
                <?php echo editor_html("bo_content_tail", get_text($board['bo_content_tail'], 0)); ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_content_tail" value="1" id="chk_grp_content_tail">
                <label for="chk_grp_content_tail">应用到群组</label>
                <input type="checkbox" name="chk_all_content_tail" value="1" id="chk_all_content_tail">
                <label for="chk_all_content_tail">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_mobile_content_head">触屏版顶部内容</label></th>
            <td>
                <?php echo editor_html("bo_mobile_content_head", get_text($board['bo_mobile_content_head'], 0)); ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_mobile_content_head" value="1" id="chk_grp_mobile_content_head">
                <label for="chk_grp_mobile_content_head">应用到群组</label>
                <input type="checkbox" name="chk_all_mobile_content_head" value="1" id="chk_all_mobile_content_head">
                <label for="chk_all_mobile_content_head">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_mobile_content_tail">触屏版底部内容</label></th>
            <td>
                <?php echo editor_html("bo_mobile_content_tail", get_text($board['bo_mobile_content_tail'], 0)); ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_mobile_content_tail" value="1" id="chk_grp_mobile_content_tail">
                <label for="chk_grp_mobile_content_tail">应用到群组</label>
                <input type="checkbox" name="chk_all_mobile_content_tail" value="1" id="chk_all_mobile_content_tail">
                <label for="chk_all_mobile_content_tail">应用到全部</label>
            </td>
        </tr>
         <tr>
            <th scope="row"><label for="bo_insert_content">发布主题默认内容</label></th>
            <td>
                <textarea id="bo_insert_content" name="bo_insert_content" rows="5"><?php echo $board['bo_insert_content'] ?></textarea>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_insert_content" value="1" id="chk_grp_insert_content">
                <label for="chk_grp_insert_content">应用到群组</label>
                <input type="checkbox" name="chk_all_insert_content" value="1" id="chk_all_insert_content">
                <label for="chk_all_insert_content">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_subject_len">标题长度<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('超过标题长度限制文字将会被(...)代替显示') ?>
                <input type="text" name="bo_subject_len" value="<?php echo $board['bo_subject_len'] ?>" id="bo_subject_len" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_subject_len" value="1" id="chk_grp_subject_len">
                <label for="chk_grp_subject_len">应用到群组</label>
                <input type="checkbox" name="chk_all_subject_len" value="1" id="chk_all_subject_len">
                <label for="chk_all_subject_len">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_mobile_subject_len">触屏版标题长度<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('超过标题长度限制文字将会被(...)代替显示') ?>
                <input type="text" name="bo_mobile_subject_len" value="<?php echo $board['bo_mobile_subject_len'] ?>" id="bo_mobile_subject_len" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_mobile_subject_len" value="1" id="chk_grp_mobile_subject_len">
                <label for="chk_grp_mobile_subject_len">应用到群组</label>
                <input type="checkbox" name="chk_all_mobile_subject_len" value="1" id="chk_all_mobile_subject_len">
                <label for="chk_all_mobile_subject_len">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_page_rows">每页显示内容数量<strong class="sound_only">必选项</strong></label></th>
            <td>
                <input type="text" name="bo_page_rows" value="<?php echo $board['bo_page_rows'] ?>" id="bo_page_rows" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_page_rows" value="1" id="chk_grp_page_rows">
                <label for="chk_grp_page_rows">应用到群组</label>
                <input type="checkbox" name="chk_all_page_rows" value="1" id="chk_all_page_rows">
                <label for="chk_all_page_rows">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_mobile_page_rows">触屏版每页显示内容数量<strong class="sound_only">必选项</strong></label></th>
            <td>
                <input type="text" name="bo_mobile_page_rows" value="<?php echo $board['bo_mobile_page_rows'] ?>" id="bo_mobile_page_rows" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_mobile_page_rows" value="1" id="chk_grp_mobile_page_rows">
                <label for="chk_grp_mobile_page_rows">应用到群组</label>
                <input type="checkbox" name="chk_all_mobile_page_rows" value="1" id="chk_all_mobile_page_rows">
                <label for="chk_all_mobile_page_rows">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_gallery_cols">图片数量<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('用于图片皮肤论坛每行显示图片数量') ?>
                <input type="text" name="bo_gallery_cols" value="<?php echo $board['bo_gallery_cols'] ?>" id="bo_gallery_cols" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_gallery_cols" value="1" id="chk_grp_gallery_cols">
                <label for="chk_grp_gallery_cols">应用到群组</label>
                <input type="checkbox" name="chk_all_gallery_cols" value="1" id="chk_all_gallery_cols">
                <label for="chk_all_gallery_cols">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_gallery_width">图片宽度<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('用于图片皮肤论坛在目录中显示的缩略图宽度') ?>
                <input type="text" name="bo_gallery_width" value="<?php echo $board['bo_gallery_width'] ?>" id="bo_gallery_width" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_gallery_width" value="1" id="chk_grp_gallery_width">
                <label for="chk_grp_gallery_width">应用到群组</label>
                <input type="checkbox" name="chk_all_gallery_width" value="1" id="chk_all_gallery_width">
                <label for="chk_all_gallery_width">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_gallery_height">图片高度<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('用于图片皮肤论坛在目录中显示的缩略图高度') ?>
                <input type="text" name="bo_gallery_height" value="<?php echo $board['bo_gallery_height'] ?>" id="bo_gallery_height" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_gallery_height" value="1" id="chk_grp_gallery_height">
                <label for="chk_grp_gallery_height">应用到群组</label>
                <input type="checkbox" name="chk_all_gallery_height" value="1" id="chk_all_gallery_height">
                <label for="chk_all_gallery_height">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_mobile_gallery_width">触屏版<br>图片宽度<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('用于触屏版图片皮肤论坛在目录中显示的缩略图宽度') ?>
                <input type="text" name="bo_mobile_gallery_width" value="<?php echo $board['bo_mobile_gallery_width'] ?>" id="bo_mobile_gallery_width" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_mobile_gallery_width" value="1" id="chk_grp_mobile_gallery_width">
                <label for="chk_grp_mobile_gallery_width">应用到群组</label>
                <input type="checkbox" name="chk_all_mobile_gallery_width" value="1" id="chk_all_mobile_gallery_width">
                <label for="chk_all_mobile_gallery_width">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_mobile_gallery_height">触屏版<br>图片高度<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('用于触屏版图片皮肤论坛在目录中显示的缩略图高度') ?>
                <input type="text" name="bo_mobile_gallery_height" value="<?php echo $board['bo_mobile_gallery_height'] ?>" id="bo_mobile_gallery_height" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_mobile_gallery_height" value="1" id="chk_grp_mobile_gallery_height">
                <label for="chk_grp_mobile_gallery_height">应用到群组</label>
                <input type="checkbox" name="chk_all_mobile_gallery_height" value="1" id="chk_all_mobile_gallery_height">
                <label for="chk_all_mobile_gallery_height">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_table_width">论坛页面宽度<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('请输入数字，100以上为像素，100以内参数为百分比') ?>
                <input type="text" name="bo_table_width" value="<?php echo $board['bo_table_width'] ?>" id="bo_table_width" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_table_width" value="1" id="chk_grp_table_width">
                <label for="chk_grp_table_width">应用到群组</label>
                <input type="checkbox" name="chk_all_table_width" value="1" id="chk_all_table_width">
                <label for="chk_all_table_width">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_image_width">图片宽度<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('论坛中显示图片的最大宽度限制') ?>
                <input type="text" name="bo_image_width" value="<?php echo $board['bo_image_width'] ?>" id="bo_image_width" required class="required numeric frm_input" size="4"> 像素
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_image_width" value="1" id="chk_grp_image_width">
                <label for="chk_grp_image_width">应用到群组</label>
                <input type="checkbox" name="chk_all_image_width" value="1" id="chk_all_image_width">
                <label for="chk_all_image_width">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_new">新主题图标<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('当有新内容发布时在标题显示的new图标显示时间，设置为0不显示') ?>
                <input type="text" name="bo_new" value="<?php echo $board['bo_new'] ?>" id="bo_new" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_new" value="1" id="chk_grp_new">
                <label for="chk_grp_new">应用到群组</label>
                <input type="checkbox" name="chk_all_new" value="1" id="chk_all_new">
                <label for="chk_all_new">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_hot">热门主题图标<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php echo help('当主题点击数达到设定值时显示hot图标，设置为0不显示') ?>
                <input type="text" name="bo_hot" value="<?php echo $board['bo_hot'] ?>" id="bo_hot" required class="required numeric frm_input" size="4">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_hot" value="1" id="chk_grp_hot">
                <label for="chk_grp_hot">应用到群组</label>
                <input type="checkbox" name="chk_all_hot" value="1" id="chk_all_hot">
                <label for="chk_all_hot">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_reply_order">评论显示</label></th>
            <td>
                <select id="bo_reply_order" name="bo_reply_order">
                    <option value="1"<?php echo get_selected($board['bo_reply_order'], 1, true); ?>>最新评论在尾页显示（默认盖楼模式）
                    <option value="0"<?php echo get_selected($board['bo_reply_order'], 0); ?>>最新评论在前页显示
                </select>
            </td>
            <td class="td_grpset">
                <input type="checkbox" id="chk_grp_reply_order" name="chk_grp_reply_order" value="1">
                <label for="chk_grp_reply_order">应用到群组</label>
                <input type="checkbox" id="chk_all_reply_order" name="chk_all_reply_order" value="1">
                <label for="chk_all_reply_order">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_sort_field">目录排列方式</label></th>
            <td>
                <?php echo help('目录中显示主题排序方式设置，“默认”方式是速度最佳模式，其他模式将降低页面访问速度。') ?>
                <select id="bo_sort_field" name="bo_sort_field">
                    <option value="" <?php echo get_selected($board['bo_sort_field'], ""); ?>>wr_num, wr_reply : 默认</option>
                    <option value="wr_datetime asc" <?php echo get_selected($board['bo_sort_field'], "wr_datetime asc"); ?>>wr_datetime asc : 较早日期的开始显示</option>
                    <option value="wr_datetime desc" <?php echo get_selected($board['bo_sort_field'], "wr_datetime desc"); ?>>wr_datetime desc : 最近日期的开始显示</option>
                    <option value="wr_hit asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_hit asc, wr_num, wr_reply"); ?>>wr_hit asc : 点击数少的开始显示</option>
                    <option value="wr_hit desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_hit desc, wr_num, wr_reply"); ?>>wr_hit desc : 点击数多的开始显示</option>
                    <option value="wr_last asc" <?php echo get_selected($board['bo_sort_field'], "wr_last asc"); ?>>wr_last asc : 评论较早的开始显示</option>
                    <option value="wr_last desc" <?php echo get_selected($board['bo_sort_field'], "wr_last desc"); ?>>wr_last desc : 评论较新的开始显示</option>
                    <option value="wr_comment asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_comment asc, wr_num, wr_reply"); ?>>wr_comment asc : 评论数少的开始显示</option>
                    <option value="wr_comment desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_comment desc, wr_num, wr_reply"); ?>>wr_comment desc : 评论数多的开始显示</option>
                    <option value="wr_good asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_good asc, wr_num, wr_reply"); ?>>wr_good asc : 推荐数少的开始显示</option>
                    <option value="wr_good desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_good desc, wr_num, wr_reply"); ?>>wr_good desc : 推荐书多的开始显示</option>
                    <option value="wr_nogood asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_nogood asc, wr_num, wr_reply"); ?>>wr_nogood asc : 反对数少的开始显示</option>
                    <option value="wr_nogood desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_nogood desc, wr_num, wr_reply"); ?>>wr_nogood desc : 反对数多的开始显示</option>
                    <option value="wr_subject asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_subject asc, wr_num, wr_reply"); ?>>wr_subject asc : 标题降序排列</option>
                    <option value="wr_subject desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_subject desc, wr_num, wr_reply"); ?>>wr_subject desc : 标题升序排列</option>
                    <option value="wr_name asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_name asc, wr_num, wr_reply"); ?>>wr_name asc : 作者降序排列</option>
                    <option value="wr_name desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_name desc, wr_num, wr_reply"); ?>>wr_name desc : 作者升序排列</option>
                    <option value="ca_name asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "ca_name asc, wr_num, wr_reply"); ?>>ca_name asc : 分类名称降序排列</option>
                    <option value="ca_name desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "ca_name desc, wr_num, wr_reply"); ?>>ca_name desc : 分类名称升序排列</option>
                </select>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_sort_field" value="1" id="chk_grp_sort_field">
                <label for="chk_grp_sort_field">应用到群组</label>
                <input type="checkbox" name="chk_all_sort_field" value="1" id="chk_all_sort_field">
                <label for="chk_all_sort_field">应用到全部</label>
            </td>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_bo_point">
    <h2 class="h2_frm">论坛积分设置</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>论坛积分设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="chk_grp_point">设置为默认值</label></th>
            <td colspan="2">
                <?php echo help('使用网站设置中的全局设定') ?>
                <input type="checkbox" name="chk_grp_point" id="chk_grp_point" onclick="set_point(this.form)">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_read_point">浏览积分<strong class="sound_only">必选项</strong></label></th>
            <td>
                <input type="text" name="bo_read_point" value="<?php echo $board['bo_read_point'] ?>" id="bo_read_point" required class="required frm_input" size="5">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_read_point" value="1" id="chk_grp_read_point">
                <label for="chk_grp_read_point">应用到群组</label>
                <input type="checkbox" name="chk_all_read_point" value="1" id="chk_all_read_point">
                <label for="chk_all_read_point">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_write_point">发表积分<strong class="sound_only">必选项</strong></label></th>
            <td>
                <input type="text" name="bo_write_point" value="<?php echo $board['bo_write_point'] ?>" id="bo_write_point" required class="required frm_input" size="5">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_write_point" value="1" id="chk_grp_write_point">
                <label for="chk_grp_write_point">应用到群组</label>
                <input type="checkbox" name="chk_all_write_point" value="1" id="chk_all_write_point">
                <label for="chk_all_write_point">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_comment_point">评论积分<strong class="sound_only">必选项</strong></label></th>
            <td>
                <input type="text" name="bo_comment_point" value="<?php echo $board['bo_comment_point'] ?>" id="bo_comment_point" required class="required frm_input" size="5">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_comment_point" value="1" id="chk_grp_comment_point">
                <label for="chk_grp_comment_point">应用到群组</label>
                <input type="checkbox" name="chk_all_comment_point" value="1" id="chk_all_comment_point">
                <label for="chk_all_comment_point">应用到全部</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bo_download_point">下载积分<strong class="sound_only">必选项</strong></label></th>
            <td>
                <input type="text" name="bo_download_point" value="<?php echo $board['bo_download_point'] ?>" id="bo_download_point" required class="required frm_input" size="5">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_download_point" value="1" id="chk_grp_download_point">
                <label for="chk_grp_download_point">应用到群组</label>
                <input type="checkbox" name="chk_all_download_point" value="1" id="chk_all_download_point">
                <label for="chk_all_download_point">应用到全部</label>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_bo_extra">
    <h2 class="h2_frm">论坛 扩展数据 设置</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>论坛 扩展数据 设置</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <?php for ($i=1; $i<=10; $i++) { ?>
        <tr>
            <th scope="row">扩展数据<?php echo $i ?></th>
            <td class="td_extra">
                <label for="bo_<?php echo $i ?>_subj">扩展数据 <?php echo $i ?> 主题</label>
                <input type="text" name="bo_<?php echo $i ?>_subj" id="bo_<?php echo $i ?>_subj" value="<?php echo get_text($board['bo_'.$i.'_subj']) ?>" class="frm_input">
                <label for="bo_<?php echo $i ?>">扩展数据 <?php echo $i ?>参数</label>
                <input type="text" name="bo_<?php echo $i ?>" value="<?php echo get_text($board['bo_'.$i]) ?>" id="bo_<?php echo $i ?>" class="frm_input">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_grp_<?php echo $i ?>" value="1" id="chk_grp_<?php echo $i ?>">
                <label for="chk_grp_<?php echo $i ?>">应用到群组</label>
                <input type="checkbox" name="chk_all_<?php echo $i ?>" value="1" id="chk_all_<?php echo $i ?>">
                <label for="chk_all_<?php echo $i ?>">应用到全部</label>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

</form>

<script>
$(function(){
    $("#board_copy").click(function(){
        window.open(this.href, "win_board_copy", "left=10,top=10,width=500,height=400");
        return false;
    });
});

function board_copy(bo_table) {
    window.open("./board_copy.php?bo_table="+bo_table, "BoardCopy", "left=10,top=10,width=500,height=200");
}

function set_point(f) {
    if (f.chk_grp_point.checked) {
        f.bo_read_point.value = "<?php echo $config['cf_read_point'] ?>";
        f.bo_write_point.value = "<?php echo $config['cf_write_point'] ?>";
        f.bo_comment_point.value = "<?php echo $config['cf_comment_point'] ?>";
        f.bo_download_point.value = "<?php echo $config['cf_download_point'] ?>";
    } else {
        f.bo_read_point.value     = f.bo_read_point.defaultValue;
        f.bo_write_point.value    = f.bo_write_point.defaultValue;
        f.bo_comment_point.value  = f.bo_comment_point.defaultValue;
        f.bo_download_point.value = f.bo_download_point.defaultValue;
    }
}

function fboardform_submit(f)
{
    <?php echo get_editor_js("bo_content_head"); ?>
    <?php echo get_editor_js("bo_content_tail"); ?>
    <?php echo get_editor_js("bo_mobile_content_head"); ?>
    <?php echo get_editor_js("bo_mobile_content_tail"); ?>

    if (parseInt(f.bo_count_modify.value) < 0) {
        alert("原文禁止修改条件请输入0或以上数字");
        f.bo_count_modify.focus();
        return false;
    }

    if (parseInt(f.bo_count_delete.value) < 1) {
        alert("原文禁止删除条件请输入0或以上数字");
        f.bo_count_delete.focus();
        return false;
    }

    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
