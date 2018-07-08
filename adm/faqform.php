<?php
$sub_menu = '300700';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$sql = " select * from {$g5['faq_master_table']} where fm_id = '$fm_id' ";
$fm = sql_fetch($sql);

$html_title = 'FAQ '.$fm['fm_subject'];;

if ($w == "u")
{
    $html_title .= "编辑";
    $readonly = " readonly";

    $sql = " select * from {$g5['faq_table']} where fa_id = '$fa_id' ";
    $fa = sql_fetch($sql);
    if (!$fa['fa_id']) alert("未找到已设置项目");
}
else
    $html_title .= ' 项目输入';

$g5['title'] = $html_title.' 管理';

include_once (G5_ADMIN_PATH.'/admin.head.php');
?>

<form name="frmfaqform" action="./faqformupdate.php" onsubmit="return frmfaqform_check(this);" method="post">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="fm_id" value="<?php echo $fm_id; ?>">
<input type="hidden" name="fa_id" value="<?php echo $fa_id; ?>">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="fa_order">显示顺序</label></th>
        <td>
            <?php echo help('数值越小将在常见问题列表中优先显示'); ?>
            <input type="text" name="fa_order" value="<?php echo $fa['fa_order']; ?>" id="fa_order" class="frm_input" maxlength="10" size="10">
            <?php if ($w == 'u') { ?><a href="<?php echo G5_BBS_URL; ?>/faq.php?fm_id=<?php echo $fm_id; ?>" class="btn_frmline">内容查看</a><?php } ?>
        </td>
    </tr>
    <tr>
        <th scope="row">提问</th>
        <td><?php echo editor_html('fa_subject', get_text($fa['fa_subject'], 0)); ?></td>
    </tr>
    <tr>
        <th scope="row">回复</th>
        <td><?php echo editor_html('fa_content', get_text($fa['fa_content'], 0)); ?></td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="确定" class="btn_submit" accesskey="s">
    <a href="./faqlist.php?fm_id=<?php echo $fm_id; ?>">目录</a>
</div>

</form>

<script>
function frmfaqform_check(f)
{
    errmsg = "";
    errfld = "";

    //check_field(f.fa_subject, "请输入标题");
    //check_field(f.fa_content, "请输入内容");

    if (errmsg != "")
    {
        alert(errmsg);
        errfld.focus();
        return false;
    }

    <?php echo get_editor_js('fa_subject'); ?>
    <?php echo get_editor_js('fa_content'); ?>

    return true;
}

// document.getElementById('fa_order').focus(); 解除焦点
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
