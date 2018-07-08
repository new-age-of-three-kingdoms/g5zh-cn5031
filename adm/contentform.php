<?php
$sub_menu = '300600';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

// 顶部、底部文件路径字段添加
if(!sql_query(" select co_include_head from {$g5['content_table']} limit 1 ", false)) {
    $sql = " ALTER TABLE `{$g5['content_table']}`  ADD `co_include_head` VARCHAR( 255 ) NOT NULL ,
                                                    ADD `co_include_tail` VARCHAR( 255 ) NOT NULL ";
    sql_query($sql, false);
}

// html purifier 使用选项字段
if(!sql_query(" select co_tag_filter_use from {$g5['content_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE `{$g5['content_table']}`
                    ADD `co_tag_filter_use` tinyint(4) NOT NULL DEFAULT '0' AFTER `co_content` ", true);
    sql_query(" update {$g5['content_table']} set co_tag_filter_use = '1' ");
}

// 触屏板 内容 添加
if(!sql_query(" select co_mobile_content from {$g5['content_table']} limit 1", false)) {
    sql_query(" ALTER TABLE `{$g5['content_table']}`
                    ADD `co_mobile_content` longtext NOT NULL AFTER `co_content` ", true);
}

// 皮肤 设置 添加
if(!sql_query(" select co_skin from {$g5['content_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE `{$g5['content_table']}`
                    ADD `co_skin` varchar(255) NOT NULL DEFAULT '' AFTER `co_mobile_content`,
                    ADD `co_mobile_skin` varchar(255) NOT NULL DEFAULT '' AFTER `co_skin` ", true);
    sql_query(" update {$g5['content_table']} set co_skin = 'basic', co_mobile_skin = 'basic' ");
}

$html_title = "内容";
$g5['title'] = $html_title.' 管理';

if ($w == "u")
{
    $html_title .= "编辑";
    $readonly = " readonly";

    $sql = " select * from {$g5['content_table']} where co_id = '$co_id' ";
    $co = sql_fetch($sql);
    if (!$co['co_id'])
        alert('未找到已设置项目');
}
else
{
    $html_title .= '新建';
    $co['co_html'] = 2;
    $co['co_skin'] = 'basic';
    $co['co_mobile_skin'] = 'basic';
}

include_once (G5_ADMIN_PATH.'/admin.head.php');
?>

<form name="frmcontentform" action="./contentformupdate.php" onsubmit="return frmcontentform_check(this);" method="post" enctype="MULTIPART/FORM-DATA" >
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="co_html" value="1">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="co_id">ID</label></th>
        <td>
            <?php echo help('只能使用20字以内的英文、数字及下划线'); ?>
            <input type="text" value="<?php echo $co['co_id']; ?>" name="co_id" id ="co_id" required <?php echo $readonly; ?> class="required <?php echo $readonly; ?> frm_input" size="20" maxlength="20">
            <?php if ($w == 'u') { ?><a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=<?php echo $co_id; ?>" class="btn_frmline">内容确定</a><?php } ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="co_subject">主题</label></th>
        <td><input type="text" name="co_subject" value="<?php echo htmlspecialchars2($co['co_subject']); ?>" id="co_subject" required class="frm_input required" size="90"></td>
    </tr>
    <tr>
        <th scope="row">内容</th>
        <td><?php echo editor_html('co_content', get_text($co['co_content'], 0)); ?></td>
    </tr>
    <tr>
        <th scope="row">触屏板 内容</th>
        <td><?php echo editor_html('co_mobile_content', get_text($co['co_mobile_content'], 0)); ?></td>
    </tr>
    <tr>
        <th scope="row"><label for="co_skin">皮肤目录<strong class="sound_only">必选项</strong></label></th>
        <td>
            <?php echo get_skin_select('content', 'co_skin', 'co_skin', $co['co_skin'], 'required'); ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="co_mobile_skin">触屏板皮肤目录<strong class="sound_only">必选项</strong></label></th>
        <td>
            <?php echo get_mobile_skin_select('content', 'co_mobile_skin', 'co_mobile_skin', $co['co_mobile_skin'], 'required'); ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="co_tag_filter_use">代码检测</label></th>
        <td>
            <?php echo help("请设置内容中禁止使用的代码，如iframe等，如果您需要使用代码功能，请关闭此功能。"); ?>
            <select name="co_tag_filter_use" id="co_tag_filter_use">
                <option value="1"<?php echo get_selected(1, $co['co_tag_filter_use']); ?>>使用</option>
                <option value="0"<?php echo get_selected(0, $co['co_tag_filter_use']); ?>>不使用</option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="co_include_head">顶部文件路径</label></th>
        <td>
            <?php echo help("不设置时将使用默认设置"); ?>
            <input type="text" name="co_include_head" value="<?php echo $co['co_include_head']; ?>" id="co_include_head" class="frm_input" size="60">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="co_include_tail">底部文件路径</label></th>
        <td>
            <?php echo help("不设置时将使用默认设置"); ?>
            <input type="text" name="co_include_tail" value="<?php echo $co['co_include_tail']; ?>" id="co_include_tail" class="frm_input" size="60">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="co_himg">顶部图片</label></th>
        <td>
            <input type="file" name="co_himg" id="co_himg">
            <?php
            $himg = G5_DATA_PATH.'/content/'.$co['co_id'].'_h';
            if (file_exists($himg)) {
                $size = @getimagesize($himg);
                if($size[0] && $size[0] > 750)
                    $width = 750;
                else
                    $width = $size[0];

                echo '<input type="checkbox" name="co_himg_del" value="1" id="co_himg_del"> <label for="co_himg_del">删除</label>';
                $himg_str = '<img src="'.G5_DATA_URL.'/content/'.$co['co_id'].'_h" width="'.$width.'" alt="">';
            }
            if ($himg_str) {
                echo '<div class="banner_or_img">';
                echo $himg_str;
                echo '</div>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="co_timg">底部图片</label></th>
        <td>
            <input type="file" name="co_timg" id="co_timg">
            <?php
            $timg = G5_DATA_PATH.'/content/'.$co['co_id'].'_t';
            if (file_exists($timg)) {
                $size = @getimagesize($timg);
                if($size[0] && $size[0] > 750)
                    $width = 750;
                else
                    $width = $size[0];

                echo '<input type="checkbox" name="co_timg_del" value="1" id="co_timg_del"> <label for="co_timg_del">删除</label>';
                $timg_str = '<img src="'.G5_DATA_URL.'/content/'.$co['co_id'].'_t" width="'.$width.'" alt="">';
            }
            if ($timg_str) {
                echo '<div class="banner_or_img">';
                echo $timg_str;
                echo '</div>';
            }
            ?>
        </td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="确定" class="btn_submit" accesskey="s">
    <a href="./contentlist.php">目录</a>
</div>

</form>

<script>
function frmcontentform_check(f)
{
    errmsg = "";
    errfld = "";

    <?php echo get_editor_js('co_content'); ?>
    <?php echo chk_editor_js('co_content'); ?>
    <?php echo get_editor_js('co_mobile_content'); ?>

    check_field(f.co_id, "请输入ID");
    check_field(f.co_subject, "请输入标题");
    check_field(f.co_content, "请输入内容");

    if (errmsg != "") {
        alert(errmsg);
        errfld.focus();
        return false;
    }
    return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
