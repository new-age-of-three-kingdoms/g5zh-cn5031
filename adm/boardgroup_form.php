<?php
$sub_menu = "300200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('您没有访问权限');

$html_title = '论坛群组';
$gr_id_attr = '';
$sound_only = '';
if ($w == '') {
    $gr_id_attr = 'required';
    $sound_only = '<strong class="sound_only"> 必选项</strong>';
    $gr['gr_use_access'] = 0;
    $html_title .= '创建';
} else if ($w == 'u') {
    $gr_id_attr = 'readonly';
    $gr = sql_fetch(" select * from {$g5['group_table']} where gr_id = '$gr_id' ");
    $html_title .= '编辑';
}
else
    alert('传递参数错误！');

if (!isset($group['gr_device'])) {
    sql_query(" ALTER TABLE `{$g5['group_table']}` ADD `gr_device` ENUM('both','pc','mobile') NOT NULL DEFAULT 'both' AFTER `gr_subject` ", false);
}


$g5['title'] = $html_title;
include_once('./admin.head.php');
?>

<form name="fboardgroup" id="fboardgroup" action="./boardgroup_form_update.php" onsubmit="return fboardgroup_check(this);" method="post" autocomplete="off">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="gr_id">群组 ID<?php echo $sound_only ?></label></th>
        <td><input type="text" name="gr_id" value="<?php echo $group['gr_id'] ?>" id="gr_id" <?php echo $gr_id_attr; ?> class="<?php echo $gr_id_attr; ?> alnum_ frm_input" maxlength="10">
            <?php
            if ($w=='')
                echo '英文、数字及下划线（不含空格）';
            else
                echo '<a href="'.G5_BBS_URL.'/group.php?gr_id='.$group['gr_id'].'" class="btn_frmline">访问群组</a>';
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="gr_subject">群组 主题<strong class="sound_only"> 必选项</strong></label></th>
        <td>
            <input type="text" name="gr_subject" value="<?php echo get_text($group['gr_subject']) ?>" id="gr_subject" required class="required frm_input" size="80">
            <?php
            if ($w == 'u')
                echo '<a href="./board_form.php?gr_id='.$gr_id.'" class="btn_frmline">论坛创建</a>';
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="gr_device">访问设备</label></th>
        <td>
            <?php echo help("区分PC版本或触屏版本") ?>
            <select id="gr_device" name="gr_device">
                <option value="both"<?php echo get_selected($group['gr_device'], 'both', true); ?>>全部允许</option>
                <option value="pc"<?php echo get_selected($group['gr_device'], 'pc'); ?>>仅限PC</option>
                <option value="mobile"<?php echo get_selected($group['gr_device'], 'mobile'); ?>>仅限触屏版</option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php if ($is_admin == 'super') { ?><label for="gr_admin"><?php } ?>群组管理员<?php if ($is_admin == 'super') { ?></label><?php } ?></th>
        <td>
            <?php
            if ($is_admin == 'super')
                echo '<input type="text" id="gr_admin" name="gr_admin" class="frm_input" value="'.$gr['gr_admin'].'" maxlength="20">';
            else
                echo '<input type="hidden" id="gr_admin" name="gr_admin" value="'.$gr['gr_admin'].'">'.$gr['gr_admin'];
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="gr_use_access">开启群组访问权限</label></th>
        <td>
            <?php echo help("开启此功能后只有拥有访问权限的会员才能访问当前群组内的论坛版块") ?>
            <input type="checkbox" name="gr_use_access" value="1" id="gr_use_access" <?php echo $gr['gr_use_access']?'checked':''; ?>>
            使用
        </td>
    </tr>
    <tr>
        <th scope="row">授权会员</th>
        <td>
            <?php
            // 授权会员
            $sql1 = " select count(*) as cnt from {$g5['group_member_table']} where gr_id = '{$gr_id}' ";
            $row1 = sql_fetch($sql1);
            echo '<a href="./boardgroupmember_list.php?gr_id='.$gr_id.'">'.$row1['cnt'].'</a>';
            ?>
        </td>
    </tr>
    <?php for ($i=1;$i<=10;$i++) { ?>
    <tr>
        <th scope="row">扩展数据<?php echo $i ?></th>
        <td class="td_extra">
            <label for="gr_<?php echo $i ?>_subj">扩展数据 <?php echo $i ?> 主题</label>
            <input type="text" name="gr_<?php echo $i ?>_subj" value="<?php echo get_text($group['gr_'.$i.'_subj']) ?>" id="gr_<?php echo $i ?>_subj" class="frm_input">
            <label for="gr_<?php echo $i ?>">扩展数据 <?php echo $i ?> 内容</label>
            <input type="text" name="gr_<?php echo $i ?>" value="<?php echo $gr['gr_'.$i] ?>" id="gr_<?php echo $i ?>" class="frm_input">
        </td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" class="btn_submit" accesskey="s" value="确定">
    <a href="./boardgroup_list.php?<?php echo $qstr ?>">目录</a>
</div>

</form>

<div class="local_desc01 local_desc">
    <p>
        创建论坛前至少需要建立一个群组<br>
        您可以通过群组功能更加方便的管理所属论坛版块
    </p>
</div>

<script>
function fboardgroup_check(f)
{
    f.action = './boardgroup_form_update.php';
    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
